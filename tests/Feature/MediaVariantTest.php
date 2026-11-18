<?php

namespace Tests\Feature;

use App\Models\Galeri;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaVariantTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    private function putJpeg(string $path, int $width, int $height): void
    {
        $image = imagecreatetruecolor($width, $height);
        imagefilledrectangle($image, 0, 0, $width, $height, imagecolorallocate($image, 30, 120, 70));
        ob_start();
        imagejpeg($image, null, 90);
        Storage::disk('public')->put($path, ob_get_clean());
        imagedestroy($image);
    }

    public function test_gallery_photo_is_served_as_resized_webp(): void
    {
        $this->putJpeg('galeri/foto-besar.jpg', 2400, 1600);

        $response = $this->get('/img/800/galeri/foto-besar.jpg');

        $response->assertOk();
        $this->assertSame('image/webp', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('immutable', $response->headers->get('Cache-Control'));
        $size = getimagesize($response->getFile()->getPathname());
        $this->assertSame(800, $size[0]);
        $this->assertSame(533, $size[1]);
    }

    public function test_small_photo_is_not_upscaled(): void
    {
        $this->putJpeg('galeri/foto-kecil.jpg', 600, 400);

        $response = $this->get('/img/1280/galeri/foto-kecil.jpg')->assertOk();

        $this->assertSame(600, getimagesize($response->getFile()->getPathname())[0]);
    }

    public function test_unknown_width_other_folder_and_traversal_are_rejected(): void
    {
        $this->putJpeg('galeri/foto.jpg', 900, 600);
        Storage::disk('public')->put('bukti/rahasia.jpg', 'x');

        $this->get('/img/777/galeri/foto.jpg')->assertNotFound();
        $this->get('/img/800/bukti/rahasia.jpg')->assertNotFound();
        $this->get('/img/800/galeri/..%2Fbukti%2Frahasia.jpg')->assertNotFound();
        $this->get('/img/800/galeri/tidak-ada.jpg')->assertNotFound();
    }

    public function test_homepage_offers_responsive_sizes_for_uploaded_photos(): void
    {
        Galeri::create(['file' => 'galeri/hd.jpg', 'keterangan' => 'Air terjun', 'tipe' => 'wisata']);

        $this->get('/')->assertOk()
            ->assertSee('img/480/galeri/hd.jpg 480w', false)
            ->assertSee('img/1920/galeri/hd.jpg 1920w', false)
            ->assertSee('storage/galeri/hd.jpg', false);
    }
}
