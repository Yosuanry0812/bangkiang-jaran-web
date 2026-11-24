<?php

namespace App\Console\Commands;

use App\Models\Galeri;
use App\Models\Konten;
use App\Models\Tiket;
use App\Services\AutoTranslator;
use Illuminate\Console\Command;

class TranslateFill extends Command
{
    protected $signature = 'translate:fill
        {--lang : Isi juga kunci yang belum ada di lang/ (ID<->EN)}
        {--dry-run : Tampilkan saja tanpa menyimpan}';

    protected $description = 'Terjemahkan otomatis isian ID/EN yang masih kosong (tiket, galeri, konten, dan opsional file lang).';

    public function handle(AutoTranslator $translator): int
    {
        if (! $translator->enabled()) {
            $this->error('TRANSLATE_ENABLED=false. Aktifkan dulu di .env.');

            return self::FAILURE;
        }

        $dry = (bool) $this->option('dry-run');
        $done = 0;
        $failed = 0;

        foreach ([Tiket::class, Galeri::class, Konten::class] as $class) {
            $pairs = (new $class)->autoTranslateMap();
            foreach ($class::query()->get() as $row) {
                $dirty = false;
                foreach ($pairs as $id => $en) {
                    $idv = trim((string) $row->getRawOriginal($id));
                    $env = trim((string) $row->getRawOriginal($en));
                    [$from, $to, $src, $dst] = $idv !== '' && $env === '' ? ['id', 'en', $idv, $en]
                        : ($env !== '' && $idv === '' ? ['en', 'id', $env, $id] : [null, null, null, null]);
                    if (! $from) {
                        continue;
                    }
                    $text = $translator->translate($src, $from, $to);
                    if (! $text) {
                        $failed++;
                        $this->warn(class_basename($class)."#{$row->getKey()} {$dst}: gagal diterjemahkan.");

                        continue;
                    }
                    $this->line(class_basename($class)."#{$row->getKey()} {$dst}: ".mb_strimwidth($text, 0, 60, '…'));
                    $row->setRawAttributes(array_merge($row->getAttributes(), [$dst => $text]));
                    $dirty = true;
                    $done++;
                }
                if ($dirty && ! $dry) {
                    $row->saveQuietly();
                }
            }
        }

        if ($this->option('lang')) {
            [$d, $f] = $this->fillLang($translator, $dry);
            $done += $d;
            $failed += $f;
        }

        $this->info("Selesai: {$done} diterjemahkan, {$failed} gagal".($dry ? ' (dry-run, tidak disimpan)' : '').'.');

        return $failed ? self::FAILURE : self::SUCCESS;
    }

    /** Tambahkan kunci tingkat-atas yang ada di satu bahasa tetapi belum di bahasa lain. */
    private function fillLang(AutoTranslator $translator, bool $dry): array
    {
        $done = $failed = 0;
        foreach (glob(lang_path('id/*.php')) as $idFile) {
            $name = basename($idFile, '.php');
            $enFile = lang_path("en/{$name}.php");
            if (in_array($name, ['validation', 'pagination', 'passwords', 'auth'], true) || ! is_file($enFile)) {
                continue;
            }
            foreach ([[$idFile, $enFile, 'id', 'en'], [$enFile, $idFile, 'en', 'id']] as [$srcFile, $dstFile, $from, $to]) {
                $src = require $srcFile;
                $dst = require $dstFile;
                $add = '';
                foreach ($src as $key => $value) {
                    if (array_key_exists($key, $dst) || ! is_string($value)) {
                        continue;
                    }
                    $text = $translator->translate($value, $from, $to);
                    if (! $text) {
                        $failed++;
                        $this->warn("{$name}.{$key} ({$to}): gagal.");

                        continue;
                    }
                    $this->line("{$name}.{$key} ({$to}): ".mb_strimwidth($text, 0, 60, '…'));
                    $add .= '    '.var_export($key, true).' => '.var_export($text, true).",\n";
                    $done++;
                }
                if ($add !== '' && ! $dry) {
                    $content = file_get_contents($dstFile);
                    $pos = strrpos($content, '];');
                    file_put_contents($dstFile, substr($content, 0, $pos).$add.substr($content, $pos));
                }
            }
        }

        return [$done, $failed];
    }
}
