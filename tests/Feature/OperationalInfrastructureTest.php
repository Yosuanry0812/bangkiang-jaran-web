<?php

namespace Tests\Feature;

use App\Mail\PemesananBerhasil;
use App\Mail\TransactionalMail;
use App\Models\Pemesanan;
use App\Models\Tiket;
use App\Models\User;
use App\Services\DatabaseBackup;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\TestCase;

class OperationalInfrastructureTest extends TestCase
{
    use DatabaseMigrations;

    private ?string $fixtureDirectory = null;

    protected function tearDown(): void
    {
        foreach (['backup_source', 'backup_target'] as $name) {
            DB::purge($name);
        }
        if ($this->fixtureDirectory && is_dir($this->fixtureDirectory)) {
            $resolved = realpath($this->fixtureDirectory);
            $allowed = realpath(storage_path('framework/testing')).DIRECTORY_SEPARATOR;
            if ($resolved && str_starts_with(strtolower($resolved), strtolower($allowed))) {
                File::deleteDirectory($resolved);
            }
        }
        parent::tearDown();
    }

    private function prepareBackup(): DatabaseBackup
    {
        $this->fixtureDirectory = storage_path('framework/testing/backup-'.Str::uuid());
        File::ensureDirectoryExists($this->fixtureDirectory.'/receipts');
        file_put_contents($this->fixtureDirectory.'/receipts/test.txt', 'receipt fixture');
        foreach (['backup_source', 'backup_target'] as $name) {
            $path = $this->fixtureDirectory.'/'.$name.'.sqlite';
            touch($path);
            config(['database.connections.'.$name => ['driver' => 'sqlite', 'database' => $path, 'prefix' => '', 'foreign_key_constraints' => true]]);
        }
        config(['backup.directory' => $this->fixtureDirectory.'/archives', 'backup.uploads' => ['private/bukti' => $this->fixtureDirectory.'/receipts']]);
        DB::connection('backup_source')->statement('CREATE TABLE fixture_values (id INTEGER PRIMARY KEY, note TEXT)');
        DB::connection('backup_source')->table('fixture_values')->insert(['id' => 1, 'note' => 'original']);

        return app(DatabaseBackup::class);
    }

    public function test_backup_restores_database_and_receipt_bytes_into_empty_isolated_target(): void
    {
        $backup = $this->prepareBackup();
        $archive = $backup->create('backup_source');
        $manifest = $backup->verify($archive);
        $this->assertSame('sqlite', $manifest['engine']);
        $this->assertArrayHasKey('uploads/private/bukti/test.txt', $manifest['files']);
        DB::connection('backup_source')->table('fixture_values')->where('id', 1)->update(['note' => 'changed after backup']);
        $backup->restoreToEmpty($archive, 'backup_target', $this->fixtureDirectory.'/restored');
        $this->assertSame('original', DB::connection('backup_target')->table('fixture_values')->value('note'));
        $this->assertSame('receipt fixture', file_get_contents($this->fixtureDirectory.'/restored/private/bukti/test.txt'));
        $this->artisan('tourism:backup', ['--verify' => $archive])->assertSuccessful();
    }

    public function test_restore_refuses_nonempty_target_and_does_not_change_existing_rows(): void
    {
        $backup = $this->prepareBackup();
        $archive = $backup->create('backup_source');
        DB::connection('backup_target')->statement('CREATE TABLE existing_values (note TEXT)');
        DB::connection('backup_target')->table('existing_values')->insert(['note' => 'retain']);
        try {
            $backup->restoreToEmpty($archive, 'backup_target', $this->fixtureDirectory.'/restored');
            $this->fail('Nonempty target must be rejected.');
        } catch (\RuntimeException $error) {
            $this->assertStringContainsString('kosong', $error->getMessage());
        }
        $this->assertSame('retain', DB::connection('backup_target')->table('existing_values')->value('note'));
        $this->artisan('tourism:restore', ['archive' => $archive])->assertFailed();
    }

    public function test_backup_checksum_detects_corruption_before_restore(): void
    {
        $backup = $this->prepareBackup();
        $archive = $backup->create('backup_source');
        $zip = new \ZipArchive;
        $zip->open($archive);
        $zip->addFromString('database.sqlite', 'corrupted');
        $zip->close();
        $this->expectException(\RuntimeException::class);
        $backup->verify($archive);
    }

    private function booking(): Pemesanan
    {
        $user = User::factory()->create(['role' => 'wisatawan']);
        $ticket = Tiket::create(['nama_tiket' => 'Masuk', 'harga' => 15000, 'status' => 'aktif', 'kategori' => 'perorangan']);

        return Pemesanan::create(['id_user' => $user->id, 'id_tiket' => $ticket->id_tiket, 'tgl_kunjungan' => now()->toDateString(),
            'jumlah' => 1, 'total_harga' => 15000, 'status' => 'pending', 'kode_booking' => 'BJ-QUEUE']);
    }

    public function test_transactional_email_is_persisted_and_delivered_by_the_worker_without_smtp(): void
    {
        config(['mail.default' => 'array', 'queue.transactional_connection' => 'database']);
        $booking = $this->booking();
        Mail::to($booking->user->email)->send(new PemesananBerhasil($booking));
        $this->assertSame(1, DB::table('jobs')->where('queue', 'mail')->count());
        $this->artisan('queue:work', ['connection' => 'database', '--queue' => 'mail', '--once' => true, '--sleep' => 0])->assertSuccessful();
        $this->assertSame(0, DB::table('jobs')->count());
        $this->assertCount(1, Mail::mailer('array')->getSymfonyTransport()->messages());
    }

    public function test_failed_background_email_is_retained_for_retry(): void
    {
        config(['mail.default' => 'array', 'queue.transactional_connection' => 'database']);
        $booking = $this->booking();
        Mail::to($booking->user->email)->send(new FailingOperationalMail($booking));
        $this->artisan('queue:work', ['connection' => 'database', '--queue' => 'mail', '--once' => true, '--sleep' => 0])->assertSuccessful();
        $job = DB::table('jobs')->first();
        $this->assertNotNull($job);
        $this->assertSame(1, (int) $job->attempts);
        $this->assertGreaterThan(now()->timestamp, $job->available_at);
        $this->assertSame(0, DB::table('failed_jobs')->count());
    }

    public function test_scheduler_registers_expiry_and_checkout_on_active_bootstrap(): void
    {
        $this->artisan('schedule:list')->expectsOutputToContain('pemesanan:expire')->expectsOutputToContain('tiket:auto-checkout')->assertSuccessful();
    }
}

class FailingOperationalMail extends TransactionalMail
{
    public function build()
    {
        throw new \RuntimeException('Deliberate isolated mail failure.');
    }
}
