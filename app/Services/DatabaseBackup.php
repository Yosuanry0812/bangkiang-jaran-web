<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;
use ZipArchive;

class DatabaseBackup
{
    public function create(?string $connection = null): string
    {
        $db = DB::connection($connection);
        $driver = $db->getDriverName();
        if (! in_array($driver, ['sqlite', 'mysql'], true)) {
            throw new \RuntimeException('Driver backup tidak didukung.');
        }
        $directory = config('backup.directory');
        File::ensureDirectoryExists($directory, 0750);
        $base = $directory.'/backup-'.now()->format('Ymd-His').'-'.Str::uuid();
        $snapshot = $base.($driver === 'sqlite' ? '.sqlite' : '.sql');
        $archive = $base.'.zip';
        try {
            if ($driver === 'sqlite') {
                if ($db->transactionLevel() > 0) {
                    throw new \RuntimeException('Backup SQLite harus di luar transaksi.');
                }
                $db->getPdo()->exec("VACUUM INTO '".str_replace("'", "''", $snapshot)."'");
            } else {
                $this->mysqlProcess($db->getConfig(), true, $snapshot);
            }
            $zip = new ZipArchive;
            if ($zip->open($archive, ZipArchive::CREATE | ZipArchive::EXCL) !== true) {
                throw new \RuntimeException('Arsip tidak dapat dibuat.');
            }
            $entry = $driver === 'sqlite' ? 'database.sqlite' : 'database.sql';
            $zip->addFile($snapshot, $entry);
            $manifest = ['version' => 1, 'engine' => $driver, 'created_at' => now()->toIso8601String(),
                'files' => [$entry => hash_file('sha256', $snapshot)]];
            foreach (config('backup.uploads') as $folder => $root) {
                if (! is_dir($root)) {
                    continue;
                }
                foreach (File::allFiles($root) as $file) {
                    $relative = str_replace('\\', '/', $file->getRelativePathname());
                    $name = 'uploads/'.$folder.'/'.$relative;
                    $zip->addFile($file->getPathname(), $name);
                    $manifest['files'][$name] = hash_file('sha256', $file->getPathname());
                }
            }
            $zip->addFromString('manifest.json', json_encode($manifest, JSON_THROW_ON_ERROR));
            $zip->close();
            $this->verify($archive);

            return $archive;
        } catch (\Throwable $e) {
            if (is_file($archive)) {
                unlink($archive);
            }
            throw $e;
        } finally {
            if (is_file($snapshot)) {
                unlink($snapshot);
            }
        }
    }

    public function verify(string $archive): array
    {
        $zip = new ZipArchive;
        if ($zip->open($archive) !== true) {
            throw new \RuntimeException('Arsip tidak dapat dibaca.');
        }
        try {
            $manifest = json_decode($zip->getFromName('manifest.json') ?: '', true, flags: JSON_THROW_ON_ERROR);
            if (($manifest['version'] ?? null) !== 1 || ! in_array($manifest['engine'] ?? null, ['mysql', 'sqlite'], true)
                || ! is_array($manifest['files'] ?? null) || $zip->numFiles !== count($manifest['files']) + 1) {
                throw new \RuntimeException('Manifest backup tidak valid.');
            }
            $database = $manifest['engine'] === 'sqlite' ? 'database.sqlite' : 'database.sql';
            if (! isset($manifest['files'][$database])) {
                throw new \RuntimeException('Snapshot database tidak ditemukan.');
            }
            foreach ($manifest['files'] as $name => $checksum) {
                if ($name !== $database && (! str_starts_with($name, 'uploads/private/bukti/') && ! str_starts_with($name, 'uploads/public/bukti/') && ! str_starts_with($name, 'uploads/public/galeri/'))) {
                    throw new \RuntimeException('Path berkas tidak diizinkan.');
                }
                if (str_contains($name, '..') || str_contains($name, '\\')) {
                    throw new \RuntimeException('Path berkas tidak aman.');
                }
                $stream = $zip->getStream($name);
                if (! $stream) {
                    throw new \RuntimeException('Berkas backup tidak ditemukan.');
                }
                $hash = hash_init('sha256');
                hash_update_stream($hash, $stream);
                fclose($stream);
                if (! hash_equals($checksum, hash_final($hash))) {
                    throw new \RuntimeException('Checksum backup tidak sesuai.');
                }
            }

            return $manifest;
        } finally {
            $zip->close();
        }
    }

    public function restoreToEmpty(string $archive, string $connection, string $filesDirectory): void
    {
        $manifest = $this->verify($archive);
        $db = DB::connection($connection);
        if ($db->getDriverName() !== $manifest['engine'] || $db->getSchemaBuilder()->getTables()) {
            throw new \RuntimeException('Restore hanya diizinkan pada database kosong dengan driver yang sama.');
        }
        if (is_dir($filesDirectory) && File::allFiles($filesDirectory)) {
            throw new \RuntimeException('Folder tujuan berkas harus kosong.');
        }
        $zip = new ZipArchive;
        $zip->open($archive);
        try {
            if ($manifest['engine'] === 'sqlite') {
                $temp = tempnam(sys_get_temp_dir(), 'bj-restore-');
                try {
                    $stream = $zip->getStream('database.sqlite');
                    $out = fopen($temp, 'wb');
                    stream_copy_to_stream($stream, $out);
                    fclose($stream);
                    fclose($out);
                    $source = new \PDO('sqlite:'.$temp);
                    if ($source->query('PRAGMA integrity_check')->fetchColumn() !== 'ok') {
                        throw new \RuntimeException('Snapshot SQLite rusak.');
                    }
                    $target = $db->getDatabaseName();
                    if ($target === ':memory:') {
                        throw new \RuntimeException('Restore memerlukan tujuan SQLite berkas.');
                    }
                    $source = null;
                    DB::purge($connection);
                    if (! copy($temp, $target)) {
                        throw new \RuntimeException('Snapshot gagal dipulihkan.');
                    }
                } finally {
                    if (is_file($temp)) {
                        unlink($temp);
                    }
                }
            } else {
                $temp = tempnam(sys_get_temp_dir(), 'bj-restore-');
                try {
                    $stream = $zip->getStream('database.sql');
                    $out = fopen($temp, 'wb');
                    stream_copy_to_stream($stream, $out);
                    fclose($stream);
                    fclose($out);
                    $this->mysqlProcess($db->getConfig(), false, $temp);
                } finally {
                    if (is_file($temp)) {
                        unlink($temp);
                    }
                }
            }
            foreach ($manifest['files'] as $name => $checksum) {
                if (! str_starts_with($name, 'uploads/')) {
                    continue;
                }
                $target = rtrim($filesDirectory, '/\\').'/'.substr($name, 8);
                File::ensureDirectoryExists(dirname($target), 0750);
                $in = $zip->getStream($name);
                $out = fopen($target, 'wb');
                stream_copy_to_stream($in, $out);
                fclose($in);
                fclose($out);
            }
        } finally {
            $zip->close();
        }
    }

    private function mysqlProcess(array $config, bool $dump, string $file): void
    {
        $args = [config($dump ? 'backup.mysqldump' : 'backup.mysql'), '--host='.$config['host'],
            '--port='.($config['port'] ?? 3306), '--user='.$config['username'], '--default-character-set=utf8mb4'];
        if ($dump) {
            array_push($args, '--single-transaction', '--no-tablespaces', '--hex-blob');
        }
        $args[] = $config['database'];
        $process = new Process($args, null, ['MYSQL_PWD' => $config['password'] ?? ''], null, 300);
        $handle = fopen($file, $dump ? 'wb' : 'rb');
        try {
            if (! $dump) {
                $process->setInput($handle);
            }
            $process->run(function ($type, $data) use ($dump, $handle, $process) {
                if ($dump && $type === Process::OUT) {
                    fwrite($handle, $data);
                }
                $process->clearOutput();
                $process->clearErrorOutput();
            });
            if (! $process->isSuccessful()) {
                throw new \RuntimeException('Proses database gagal. Periksa koneksi, izin dan binary; detail kredensial tidak ditampilkan.');
            }
        } finally {
            fclose($handle);
        }
    }
}
