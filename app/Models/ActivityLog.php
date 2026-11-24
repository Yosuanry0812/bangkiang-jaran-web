<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $table = 'activity_logs';

    protected $primaryKey = 'id_log';

    protected $fillable = [
        'id_user',
        'aktivitas',
        'detail',
        'ip_address',
        'user_agent',
    ];

    private const ACTIVITIES = [
        'Login sukses' => 'operations.activity_login_success',
        'Login gagal' => 'operations.activity_login_failed',
        'Registrasi' => 'operations.activity_register',
        'Hapus galeri' => 'operations.activity_delete_gallery',
        'Hapus tiket' => 'operations.activity_delete_ticket',
    ];

    private const DETAIL_PREFIXES = [
        'Percobaan login gagal untuk: ' => 'operations.detail_failed_login',
        'File: ' => 'operations.detail_file',
        'Nama: ' => 'operations.detail_name',
    ];

    /** Aktivitas disimpan dalam bahasa Indonesia; ditampilkan sesuai locale. */
    public function getLabelAktivitasAttribute(): string
    {
        $raw = (string) ($this->attributes['aktivitas'] ?? '');

        return isset(self::ACTIVITIES[$raw]) ? __(self::ACTIVITIES[$raw]) : $raw;
    }

    public function getLabelDetailAttribute(): string
    {
        $raw = (string) ($this->attributes['detail'] ?? '');
        foreach (self::DETAIL_PREFIXES as $prefix => $key) {
            if (str_starts_with($raw, $prefix)) {
                return __($key, ['name' => substr($raw, strlen($prefix))]);
            }
        }

        return $raw;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user')->withTrashed();
    }
}
