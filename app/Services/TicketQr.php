<?php

namespace App\Services;

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class TicketQr
{
    public function dataUri(string $code): string
    {
        $writer = new Writer(new ImageRenderer(new RendererStyle(180, 4), new SvgImageBackEnd));
        $url = rtrim(config('app.url'), '/').'/tiket/verifikasi/'.rawurlencode($code);

        return 'data:image/svg+xml;base64,'.base64_encode($writer->writeString($url));
    }
}
