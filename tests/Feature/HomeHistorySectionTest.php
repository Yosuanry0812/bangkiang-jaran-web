<?php

namespace Tests\Feature;

use App\Models\Galeri;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class HomeHistorySectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_beranda_menampilkan_section_sejarah_dengan_tiga_babak(): void
    {
        $html = $this->get('/')->assertOk()
            ->assertSee('id="sejarah"', false)
            ->assertSee('bj-rv-history', false)
            ->assertSee(__('sejarah.origin_title'))
            ->getContent();

        $this->assertSame(3, substr_count($html, 'data-rv-chapter='));
        $this->assertStringContainsString('href="#sejarah"', $html);
    }

    public function test_section_sejarah_tampil_dalam_bahasa_inggris(): void
    {
        $this->withSession(['locale' => 'en'])->get('/')->assertOk()
            ->assertSee('Named after the shape of its cliffs')
            ->assertSee('Where soldiers rested');
    }

    public function test_foto_sejarah_hanya_di_section_sejarah_bukan_galeri_wisata(): void
    {
        Galeri::query()->forceDelete();
        Galeri::create(['file' => 'galeri/sejarah-xx.jpg', 'keterangan' => 'Foto sejarah uji', 'tipe' => 'sejarah']);
        Galeri::create(['file' => 'galeri/wisata-xx.jpg', 'keterangan' => 'Foto wisata uji', 'tipe' => 'wisata']);

        $this->assertNotContains('galeri/sejarah-xx.jpg', Galeri::wisata()->pluck('file')->all());
        $this->assertContains('galeri/sejarah-xx.jpg', Galeri::sejarah()->pluck('file')->all());

        $this->get('/')->assertOk()->assertSee('sejarah-xx.jpg', false);
    }

    public function test_pengelola_bisa_mengunggah_foto_bertipe_sejarah(): void
    {
        Storage::fake('local');
        Storage::fake('public');

        Galeri::query()->forceDelete();
        $this->actingAs(User::factory()->create(['role' => 'pengelola']))
            ->post(route('pengelola.galeri.store'), [
                'tipe' => 'sejarah',
                'file' => [UploadedFile::fake()->image('a.jpg', 40, 40)],
            ])->assertSessionHasNoErrors();

        $this->assertSame(1, Galeri::sejarah()->count());
    }
}
