<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tiket extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tiket';
    protected $primaryKey = 'id_tiket';

    protected $fillable = [
        'nama_tiket',
        'nama_tiket_en',
        'harga',
        'kategori',
        'status',
    ];

    /**
     * Nama tiket lokal sesuai locale situs (EN fallback ke Indonesia).
     */
    public function getNamaTiketAttribute()
    {
        if (app()->getLocale() === 'en' && !empty($this->attributes['nama_tiket_en'])) {
            return $this->attributes['nama_tiket_en'];
        }
        return $this->attributes['nama_tiket'] ?? null;
    }

    public function pemesanan()
    {
        return $this->hasMany(Pemesanan::class, 'id_tiket');
    }

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }
}
