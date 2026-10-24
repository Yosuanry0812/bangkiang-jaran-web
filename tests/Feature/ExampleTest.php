<?php

namespace Tests\Feature;

use App\Models\Galeri;
use App\Models\Tiket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_the_application_returns_a_successful_response()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_public_promotion_pages_render_without_uploaded_photos(): void
    {
        $this->get('/restoran')->assertOk()
            ->assertSee('Dokumentasi restoran belum tersedia.')
            ->assertSee('bj-photo-columns--empty', false)
            ->assertDontSee('data-photo-exhibition', false)
            ->assertDontSee('bj-resto-stitch__placeholder', false);
        $this->get('/tiket')->assertOk();
    }

    public function test_public_gallery_page_is_not_available(): void
    {
        $this->get('/galeri')->assertNotFound();
    }

    public function test_homepage_uses_uploaded_photos_for_hero(): void
    {
        Galeri::create(['file' => 'wisata/foto-satu.webp', 'keterangan' => 'Aliran air', 'tipe' => 'wisata']);
        Galeri::create(['file' => 'wisata/foto-dua.webp', 'keterangan' => 'Jalur masuk', 'tipe' => 'wisata']);
        Galeri::create(['file' => 'wisata/foto-tiga.webp', 'keterangan' => 'Pemandangan lembah', 'tipe' => 'wisata']);
        Galeri::create(['file' => 'wisata/foto-empat.webp', 'keterangan' => 'Tepian air', 'tipe' => 'wisata']);

        $this->get('/')
            ->assertOk()
            ->assertSee('data-bj-hero-next', false)
            ->assertSee('Aliran air')
            ->assertSee('Jalur masuk');

        $response = $this->get('/')->assertSee('bj-photo-collage--3', false)->assertSee('Tepian air');
        $this->assertSame(3, substr_count($response->getContent(), 'class="bj-photo-collage__photo bj-photo-collage__photo--'));
    }

    public function test_public_layout_loads_the_typography_and_motion_system(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('typography-motion.css', false)
            ->assertSee('swiper@14.3.0', false)
            ->assertSee('lenis@1.3.27', false)
            ->assertSee('data-bj-hero-swiper', false)
            ->assertSee('DM+Sans', false)
            ->assertDontSee('&family=Fraunces', false)
            ->assertDontSee('family=Cormorant+Garamond', false);

        $this->get('/')->assertSee('marketing-editorial.css', false)->assertSee('DM+Sans', false)
            ->assertSee('home-flow.css', false)->assertSee('home-flow.js', false)
            ->assertSee('data-photo-columns', false)->assertSee('data-overlap-stack', false)
            ->assertDontSee('home-dining-title', false);
        $this->get('/login')->assertDontSee('marketing-editorial.css', false)->assertDontSee('photo-exhibition.css', false);
    }

    public function test_restaurant_page_uses_uploaded_photos_in_editorial_layout(): void
    {
        Galeri::create(['file' => 'restoran/ruang-makan.webp', 'keterangan' => 'Ruang makan', 'tipe' => 'restoran']);
        Galeri::create(['file' => 'restoran/hidangan.webp', 'keterangan' => 'Hidangan lokal', 'tipe' => 'restoran']);

        $this->get('/restoran')
            ->assertOk()
            ->assertSee('bj-restaurant-hero', false)
            ->assertSee('data-photo-columns', false)
            ->assertSee('data-columns-pause', false)
            ->assertSee('Ruang makan')
            ->assertSee('Hidangan lokal');
    }

    public function test_ticket_page_presents_clear_ticket_profiles_and_estimator(): void
    {
        Tiket::create(['nama_tiket' => 'Tiket Masuk Dewasa', 'harga' => 20000, 'kategori' => 'perorangan', 'status' => 'aktif']);
        Tiket::create(['nama_tiket' => 'Tiket Masuk Anak-Anak', 'harga' => 15000, 'kategori' => 'perorangan', 'status' => 'aktif']);
        Tiket::create(['nama_tiket' => 'Tiket Masuk Domestik (WNA)', 'harga' => 30000, 'kategori' => 'perorangan', 'status' => 'aktif']);

        $this->get('/tiket')
            ->assertOk()
            ->assertSee('Tiket Masuk Dewasa')
            ->assertSee('Tiket Masuk Anak-Anak')
            ->assertSee('Tiket Masuk Domestik (WNA)')
            ->assertSee('Rp 20.000')
            ->assertSee('Rp 15.000')
            ->assertSee('Rp 30.000')
            ->assertDontSee('Usia 3–12 tahun')
            ->assertSee('data-ticket-estimator', false)
            ->assertDontSee('bj-ticket-snapshot', false)
            ->assertDontSee('bj-ticket-package', false)
            ->assertDontSee('bj-ticket-compare', false);
    }

    public function test_marketing_excludes_demo_gallery_images_and_keeps_real_uploads(): void
    {
        Storage::fake('public');
        Tiket::create(['nama_tiket' => 'Tiket Dewasa', 'harga' => 20000, 'kategori' => 'perorangan', 'status' => 'aktif']);
        Storage::disk('public')->put('wisata/air-terjun.webp', 'test-photo');
        Galeri::create(['file' => 'galeri/galeri-01.jpg', 'keterangan' => 'Demo wisata', 'tipe' => 'wisata']);
        Galeri::create(['file' => 'galeri/placeholder2.jpg', 'keterangan' => 'Demo restoran', 'tipe' => 'restoran']);
        Galeri::create(['file' => 'wisata/air-terjun.webp', 'keterangan' => 'Foto destinasi', 'tipe' => 'wisata']);

        foreach (['/', '/tiket'] as $url) {
            $this->get($url)->assertOk()->assertDontSee('galeri-01.jpg', false)->assertSee('wisata/air-terjun.webp', false);
        }
        $this->get('/restoran')->assertOk()->assertDontSee('placeholder2.jpg', false);
    }
}
