<?php

namespace App\Services;

use BaconQrCode\Common\ErrorCorrectionLevel;
use BaconQrCode\Encoder\Encoder;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class TicketQr
{
    public function url(string $code): string
    {
        return rtrim(config('app.url'), '/').'/tiket/verifikasi/'.rawurlencode($code);
    }

    public function dataUri(string $code): string
    {
        $writer = new Writer(new ImageRenderer(new RendererStyle(180, 4), new SvgImageBackEnd));

        return 'data:image/svg+xml;base64,'.base64_encode($writer->writeString($this->url($code)));
    }

    /**
     * QR dalam format PNG untuk dilampirkan inline di email (klien email
     * seperti Gmail tidak menampilkan SVG maupun gambar data: URI).
     * Mengembalikan null bila ekstensi GD tidak tersedia.
     */
    public function png(string $code, int $scale = 6, int $quiet = 4): ?string
    {
        if (! function_exists('imagecreate')) {
            return null;
        }

        $matrix = Encoder::encode($this->url($code), ErrorCorrectionLevel::M(), Encoder::DEFAULT_BYTE_MODE_ECODING)->getMatrix();
        $modules = $matrix->getWidth();
        $size = ($modules + $quiet * 2) * $scale;

        $img = imagecreate($size, $size);
        $white = imagecolorallocate($img, 255, 255, 255);
        $black = imagecolorallocate($img, 0, 0, 0);
        imagefill($img, 0, 0, $white);

        for ($y = 0; $y < $modules; $y++) {
            for ($x = 0; $x < $modules; $x++) {
                if ($matrix->get($x, $y) === 1) {
                    $px = ($x + $quiet) * $scale;
                    $py = ($y + $quiet) * $scale;
                    imagefilledrectangle($img, $px, $py, $px + $scale - 1, $py + $scale - 1, $black);
                }
            }
        }

        ob_start();
        imagepng($img);
        $png = ob_get_clean();
        imagedestroy($img);

        return $png === false ? null : $png;
    }
}
