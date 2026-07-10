<?php

namespace App\Helpers;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Request;

class ActivityLogger
{
    public static function log($aktivitas, $detail = null, $id_user = null)
    {
        try {
            ActivityLog::create([
                'id_user'    => $id_user ?? auth()->id(),
                'aktivitas'  => $aktivitas,
                'detail'     => is_array($detail) ? json_encode($detail) : $detail,
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
            ]);
        } catch (\Exception $e) {
            \Log::warning('Gagal log aktivitas: ' . $e->getMessage());
        }
    }
}
