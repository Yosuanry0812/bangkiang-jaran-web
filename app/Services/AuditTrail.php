<?php

namespace App\Services;

use App\Models\ActivityLog;

class AuditTrail
{
    public static function record(string $action, string $detail): void
    {
        ActivityLog::create(['id_user' => auth()->id(), 'aktivitas' => $action, 'detail' => $detail,
            'ip_address' => request()->ip(), 'user_agent' => mb_substr((string) request()->userAgent(), 0, 1000)]);
    }
}
