<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

/**
 * Menyajikan foto galeri dalam ukuran yang pas untuk layar (WebP), dibuat sekali lalu disimpan.
 * Foto asli tetap utuh dan dipakai untuk tampilan perbesar.
 */
class MediaVariantController extends Controller
{
    private const WIDTHS = [480, 800, 1280, 1920, 2560];

    public function __invoke(int $width, string $path): Response
    {
        abort_unless(in_array($width, self::WIDTHS, true), 404);
        abort_if(str_contains($path, '..') || ! preg_match('#^galeri/[A-Za-z0-9._-]+\.(jpe?g|png|webp)$#i', $path), 404);

        $disk = Storage::disk('public');
        abort_unless($disk->exists($path), 404);

        $cache = "_variants/{$width}/{$path}.webp";
        if (! $disk->exists($cache) || $disk->lastModified($cache) < $disk->lastModified($path)) {
            if (! $this->generate($disk->path($path), $disk->path($cache), $width)) {
                return redirect(asset('storage/'.$path));
            }
        }

        return response()->file($disk->path($cache), [
            'Content-Type' => 'image/webp',
            'Cache-Control' => 'public, max-age=31536000, immutable',
        ]);
    }

    private function generate(string $source, string $target, int $width): bool
    {
        if (! function_exists('imagewebp')) {
            return false;
        }

        $info = @getimagesize($source);
        if (! $info || $info[0] * $info[1] > 60_000_000) {
            return false;
        }

        @ini_set('memory_limit', '512M');

        $image = match ($info[2]) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($source),
            IMAGETYPE_PNG => @imagecreatefrompng($source),
            IMAGETYPE_WEBP => @imagecreatefromwebp($source),
            default => false,
        };
        if (! $image) {
            return false;
        }

        if ($info[2] === IMAGETYPE_JPEG && function_exists('exif_read_data')) {
            $orientation = @exif_read_data($source)['Orientation'] ?? 1;
            $rotation = [3 => 180, 6 => -90, 8 => 90][$orientation] ?? 0;
            if ($rotation !== 0 && ($rotated = imagerotate($image, $rotation, 0))) {
                $image = $rotated;
            }
        }

        if (imagesx($image) > $width && ($scaled = imagescale($image, $width, -1, IMG_BICUBIC))) {
            $image = $scaled;
        }

        imagepalettetotruecolor($image);
        imagealphablending($image, true);
        imagesavealpha($image, true);

        if (! is_dir(dirname($target))) {
            @mkdir(dirname($target), 0775, true);
        }
        $temporary = $target.'.tmp';
        $saved = @imagewebp($image, $temporary, 82);
        if (! $saved) {
            @unlink($temporary);

            return false;
        }

        return rename($temporary, $target);
    }
}
