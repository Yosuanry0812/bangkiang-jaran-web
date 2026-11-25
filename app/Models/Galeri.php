<?php

namespace App\Models;

use App\Models\Concerns\TranslatesFields;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Galeri extends Model
{
    use HasFactory, SoftDeletes, TranslatesFields;

    protected array $autoTranslate = ['keterangan' => 'keterangan_en'];

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

    /** URL foto dalam ukuran ringan (WebP); foto asli tetap utuh di storage. */
    public static function variantUrl(?string $file, int $width): string
    {
        $file = ltrim((string) $file, '/');

        return str_starts_with($file, 'galeri/') ? url("img/{$width}/{$file}") : asset('storage/'.$file);
    }

    public function scopeWisata($query)
    {
        return $query->where('tipe', 'wisata');
    }

    public function scopeRestoran($query)
    {
        return $query->where('tipe', 'restoran');
    }

    public function scopeSejarah($query)
    {
        return $query->where('tipe', 'sejarah');
    }
}
