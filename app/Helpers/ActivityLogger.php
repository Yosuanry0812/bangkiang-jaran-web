<?php

namespace App\Helpers;

use App\Models\ActivityLog;
use App\Models\LogLoginGagal;
use App\Models\LogLoginSukses;
use App\Models\User;
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

            // Sinkronisasi otomatis ke tabel terpisah jika aktivitas login
            if ($aktivitas === 'Login gagal') {
                $input = trim(str_replace('Percobaan login gagal untuk:', '', is_string($detail) ? $detail : ''));
                LogLoginGagal::create([
                    'input_login' => $input ?: '-',
                    'alasan'      => 'Password salah atau akun tidak ditemukan',
                    'ip_address'  => Request::ip(),
                    'user_agent'  => Request::userAgent(),
                ]);
            } elseif ($aktivitas === 'Login sukses') {
                $user = null;
                if ($id_user) {
                    $user = User::find($id_user);
                }
                $user = $user ?? auth()->user();

                $cleanDetail = is_string($detail) ? trim(str_replace('Login sebagai', '', $detail)) : '';
                LogLoginSukses::create([
                    'id_user'    => $user?->id ?? $id_user,
                    'nama'       => $user?->name ?? ($cleanDetail ?: 'Pengguna'),
                    'email'      => $user?->email ?? ($cleanDetail ?: '-'),
                    'role'       => $user?->role ?? 'wisatawan',
                    'metode'     => $user?->last_login_method ?? 'manual',
                    'ip_address' => Request::ip(),
                    'user_agent' => Request::userAgent(),
                ]);
            }
        } catch (\Exception $e) {
            \Log::warning('Gagal log aktivitas: ' . $e->getMessage());
        }
    }

    public static function logLoginSukses(User $user, string $metode = 'manual'): void
    {
        self::log('Login sukses', 'Login sebagai ' . $user->email, $user->id);
    }

    public static function logLoginGagal(string $inputLogin, string $alasan = 'Password salah atau akun tidak ditemukan'): void
    {
        self::log('Login gagal', 'Percobaan login gagal untuk: ' . $inputLogin);
    }
}
