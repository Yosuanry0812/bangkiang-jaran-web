<?php

namespace App\Services;

class CsvExport
{
    public function download(string $name, array $headers, iterable $rows)
    {
        return response()->streamDownload(function () use ($headers, $rows) {
            $stream = fopen('php://output', 'wb');
            fwrite($stream, "\xEF\xBB\xBF");
            fputcsv($stream, $headers, ';', '"', '');
            foreach ($rows as $row) {
                $row = array_map(static function ($cell) {
                    $value = (string) ($cell ?? '');

                    return preg_match('/^[\x00-\x20]*[=+@-]/u', $value) ? "'".$value : $value;
                }, $row);
                fputcsv($stream, $row, ';', '"', '');
            }
            fclose($stream);
        }, $name, ['Content-Type' => 'text/csv; charset=UTF-8', 'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff']);
    }
}
