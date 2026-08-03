<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Galeri extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'galeri';
    protected $primaryKey = 'id_galeri';

    protected $fillable = [
        'file',
        'keterangan',
        'keterangan_en',
        'tipe',
    ];

    /**
     * Keterangan lokal sesuai locale situs (EN fallback ke Indonesia).
     */
    public function getKeteranganAttribute()
    {
        if (app()->getLocale() === 'en' && !empty($this->attributes['keterangan_en'])) {
            return $this->attributes['keterangan_en'];
        }
        return $this->attributes['keterangan'] ?? null;
    }

    public function scopeWisata($query)
    {
        return $query->where('tipe', 'wisata');
    }

    public function scopeRestoran($query)
    {
        return $query->where('tipe', 'restoran');
    }
}
