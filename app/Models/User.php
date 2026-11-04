<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'phone',
        'password',
        'role',
        'google_id',
        'last_login_at',
        'last_login_method',
        'login_count',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function pemesanan()
    {
        return $this->hasMany(Pemesanan::class, 'id_user');
    }

    public function isPengelola()
    {
        return $this->role === 'pengelola';
    }

    public function isWisatawan()
    {
        return $this->role === 'wisatawan';
    }

    public function registrationMethodLabel(): string
    {
        return $this->registration_method === 'google' ? 'Google' : 'Email';
    }

    public function emailVerificationLabel(): string
    {
        if (!$this->hasVerifiedEmail()) {
            return 'Belum diverifikasi';
        }

        return match ($this->email_verification_method) {
            'otp' => 'OTP email',
            'google' => 'Google',
            default => 'Terverifikasi (akun lama)',
        };
    }
}
