<?php

namespace Tests\Feature;

use App\Models\Galeri;
use App\Models\Tiket;
use App\Services\AutoTranslator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AutoTranslateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['translation.enabled' => true, 'translation.driver' => 'mymemory']);
        Cache::flush();
    }

    private function fakeApi(string $text = 'Adult Admission'): void
    {
        Http::fake(['api.mymemory.translated.net/*' => Http::response([
            'responseStatus' => 200, 'responseData' => ['translatedText' => $text],
        ])]);
    }

    public function test_empty_english_is_filled_from_indonesian_on_save(): void
    {
        $this->fakeApi('Adult Admission');

        $tiket = Tiket::create(['nama_tiket' => 'Tiket Dewasa', 'harga' => 20000, 'status' => 'aktif']);

        $this->assertSame('Adult Admission', $tiket->getRawOriginal('nama_tiket_en'));
    }

    public function test_empty_indonesian_is_filled_from_english(): void
    {
        $this->fakeApi('Foto air terjun');

        $galeri = Galeri::create(['file' => 'galeri/a.jpg', 'keterangan_en' => 'Waterfall photo', 'tipe' => 'wisata']);

        $this->assertSame('Foto air terjun', $galeri->getRawOriginal('keterangan'));
    }

    public function test_manual_translation_is_never_overwritten(): void
    {
        Http::fake();

        $tiket = Tiket::create(['nama_tiket' => 'Tiket Dewasa', 'nama_tiket_en' => 'Adult Pass', 'harga' => 1, 'status' => 'aktif']);

        Http::assertNothingSent();
        $this->assertSame('Adult Pass', $tiket->getRawOriginal('nama_tiket_en'));
    }

    public function test_save_still_succeeds_when_service_fails_or_quota_is_exhausted(): void
    {
        Http::fake(['*' => Http::response(['responseStatus' => 200, 'responseData' => ['translatedText' => 'MYMEMORY WARNING: YOU USED ALL AVAILABLE FREE TRANSLATIONS FOR TODAY']])]);
        $tiket = Tiket::create(['nama_tiket' => 'Tiket Anak', 'harga' => 1, 'status' => 'aktif']);
        $this->assertNull($tiket->getRawOriginal('nama_tiket_en'));

        Http::fake(['*' => Http::response('error', 500)]);
        $tiket2 = Tiket::create(['nama_tiket' => 'Tiket Rombongan', 'harga' => 1, 'status' => 'aktif']);
        $this->assertNotNull($tiket2->id_tiket);
    }

    public function test_placeholders_are_preserved(): void
    {
        $this->fakeApi('Hello ZXQ0QXZ, welcome');

        $this->assertSame('Hello :name, welcome', app(AutoTranslator::class)->translate('Halo :name, selamat datang', 'id', 'en'));
    }

    public function test_result_that_loses_a_placeholder_is_rejected(): void
    {
        $this->fakeApi('Hello, welcome');

        $this->assertNull(app(AutoTranslator::class)->translate('Halo :name, selamat datang', 'id', 'en'));
    }
    public function test_libretranslate_driver_is_used_when_configured(): void
    {
        config(['translation.driver' => 'libretranslate', 'translation.libretranslate.url' => 'http://libre.test']);
        Http::fake(['libre.test/translate' => Http::response(['translatedText' => 'Hello'])]);

        $this->assertSame('Hello', app(AutoTranslator::class)->translate('Halo', 'id', 'en'));
    }

    public function test_result_is_cached_so_the_api_is_called_once(): void
    {
        $this->fakeApi('Hello');
        $service = app(AutoTranslator::class);
        $service->translate('Halo', 'id', 'en');
        $service->translate('Halo', 'id', 'en');

        Http::assertSentCount(1);
    }

    public function test_missing_lang_key_is_translated_at_runtime_with_placeholders(): void
    {
        $file = lang_path('id/zz_autotest.php');
        file_put_contents($file, "<?php\nreturn ['halo' => 'Selamat datang, :name'];\n");
        try {
            $this->fakeApi('Welcome, ZXQ0QXZ');
            app()->setLocale('en');

            $this->assertSame('Welcome, Budi', __('zz_autotest.halo', ['name' => 'Budi']));
        } finally {
            @unlink($file);
        }
    }

    public function test_missing_lang_key_falls_back_to_source_text_when_service_fails(): void
    {
        $file = lang_path('id/zz_autotest2.php');
        file_put_contents($file, "<?php\nreturn ['halo' => 'Selamat datang'];\n");
        try {
            Http::fake(['*' => Http::response('error', 500)]);
            app()->setLocale('en');

            $this->assertSame('Selamat datang', __('zz_autotest2.halo'));
        } finally {
            @unlink($file);
        }
    }
}
