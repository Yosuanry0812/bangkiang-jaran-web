<?php

namespace App\Models;

use App\Models\Concerns\TranslatesFields;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Konten extends Model
{
    use HasFactory, SoftDeletes, TranslatesFields;

    protected array $autoTranslate = ['judul' => 'judul_en', 'isi' => 'isi_en'];

    protected $table = 'konten';
    protected $primaryKey = 'id_konten';

    protected $fillable = [
        'judul',
        'judul_en',
        'isi',
        'isi_en',
        'jenis',
    ];

    /** Judul sesuai locale (EN fallback ke Indonesia). */
    public function getJudulAttribute($value)
    {
        return app()->getLocale() === 'en' && ! empty($this->attributes['judul_en']) ? $this->attributes['judul_en'] : $value;
    }

    /** Isi sesuai locale (EN fallback ke Indonesia). */
    public function getIsiAttribute($value)
    {
        return app()->getLocale() === 'en' && ! empty($this->attributes['isi_en']) ? $this->attributes['isi_en'] : $value;
    }
}
