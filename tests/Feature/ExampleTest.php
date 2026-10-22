<?php

namespace Tests\Feature;

use App\Models\Galeri;
use App\Models\Tiket;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
        $this->get('/restoran')->assertOk();
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

        $this->get('/')
            ->assertOk()
            ->assertSee('data-bj-hero-next', false)
            ->assertSee('Aliran air')
            ->assertSee('Jalur masuk');
    }

    public function test_public_layout_loads_the_typography_and_motion_system(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('typography-motion.css', false)
            ->assertSee('swiper@14.3.0', false)
            ->assertSee('lenis@1.3.27', false)
            ->assertSee('data-bj-hero-swiper', false)
            ->assertSee('Playfair+Display', false)
            ->assertSee('DM+Sans', false)
            ->assertSee('Cormorant+Garamond', false);
    }

    public function test_restaurant_page_uses_uploaded_photos_in_editorial_layout(): void
    {
        Galeri::create(['file' => 'restoran/ruang-makan.webp', 'keterangan' => 'Ruang makan', 'tipe' => 'restoran']);
        Galeri::create(['file' => 'restoran/hidangan.webp', 'keterangan' => 'Hidangan lokal', 'tipe' => 'restoran']);

        $this->get('/restoran')
            ->assertOk()
            ->assertSee('bj-restaurant-hero', false)
            ->assertSee('data-gallery-open', false)
            ->assertSee('data-bj-marquee', false)
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
            ->assertSee('Tiket Dewasa')
            ->assertSee('Tiket Anak')
            ->assertSee('Tiket Wisatawan Mancanegara')
            ->assertSee('data-ticket-estimator', false)
            ->assertSee('Tabel perbandingan tiket');
    }
}
