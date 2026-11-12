<?php

namespace Tests\Feature;

use App\Models\Galeri;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RestaurantHeroTest extends TestCase
{
    use RefreshDatabase;

    private function heroPhotos(string $html): array
    {
        $dom = new \DOMDocument;
        @$dom->loadHTML('<?xml encoding="UTF-8">'.$html);
        $xpath = new \DOMXPath($dom);
        $film = $xpath->query('//*[@data-resto-film]')->item(0);
        $this->assertNotNull($film);
        $this->assertSame(2, $xpath->query('.//img[@data-resto-frame]', $film)->length);
        $this->assertSame(0, $xpath->query('.//figcaption|.//button|.//*[@data-resto-controls]', $film)->length);
        $this->assertSame(1, $xpath->query('//*[@id="hero-section"][@data-hero-intro]')->length);
        $this->assertSame(1, $xpath->query('//*[@id="resto-hero-title"][@data-hero-typewriter]')->length);

        return json_decode($film->getAttribute('data-resto-photos'), true, flags: JSON_THROW_ON_ERROR);
    }

    public function test_restaurant_film_only_uses_unique_real_restaurant_uploads_and_safe_localized_captions(): void
    {
        Galeri::create(['file' => 'wisata/waterfall.webp', 'keterangan' => 'Wisata only', 'tipe' => 'wisata']);
        Galeri::create(['file' => 'restoran/placeholder2.jpg', 'keterangan' => 'Demo only', 'tipe' => 'restoran']);
        Galeri::create(['file' => 'restoran/food.webp', 'keterangan' => 'Sajian', 'keterangan_en' => 'Local food', 'tipe' => 'restoran']);
        Galeri::create(['file' => 'restoran/table.webp', 'keterangan' => 'Meja "bersama" <img src=x onerror=alert(1)>', 'keterangan_en' => 'Shared "table" & garden', 'tipe' => 'restoran']);
        Galeri::create(['file' => 'restoran/table.webp', 'keterangan' => 'Meja "bersama" <img src=x onerror=alert(1)>', 'keterangan_en' => 'Shared "table" & garden', 'tipe' => 'restoran']);

        foreach (['id', 'en'] as $locale) {
            $response = $this->withSession(['locale' => $locale])->get('/restoran')->assertOk()
                ->assertSee('restaurant-film.js', false)->assertSee('restaurant-film.css', false)
                ->assertDontSee('<img src=x onerror=alert(1)>', false);
            $photos = $this->heroPhotos($response->getContent());
            $this->assertCount(2, $photos);
            $this->assertEqualsCanonicalizing([asset('storage/restoran/food.webp'), asset('storage/restoran/table.webp')], array_column($photos, 'url'));
            $table = collect($photos)->firstWhere('url', asset('storage/restoran/table.webp'));
            $this->assertSame($locale === 'en' ? 'Shared "table" & garden' : 'Meja "bersama" <img src=x onerror=alert(1)>', $table['caption']);
        }
    }

    public function test_empty_restaurant_film_keeps_a_truthfully_labelled_landscape_fallback(): void
    {
        $response = $this->get('/restoran')->assertOk()->assertSee('Dokumentasi restoran belum tersedia.');
        $photos = $this->heroPhotos($response->getContent());
        $this->assertSame([['url' => asset('images/sejarah-bangkiang-waterfall.webp'), 'caption' => 'Lanskap Air Terjun Bangkiang Jaran']], $photos);
    }

    public function test_one_upload_is_not_duplicated_to_invent_a_slideshow(): void
    {
        Galeri::create(['file' => 'restoran/only-photo.webp', 'keterangan' => 'Ruang bersantap', 'tipe' => 'restoran']);
        $photos = $this->heroPhotos($this->get('/restoran')->assertOk()->getContent());
        $this->assertCount(1, $photos);
        $this->assertSame(asset('storage/restoran/only-photo.webp'), $photos[0]['url']);
        $this->get('/')->assertDontSee('restaurant-film.js', false)->assertDontSee('restaurant-film.css', false);
    }
}
