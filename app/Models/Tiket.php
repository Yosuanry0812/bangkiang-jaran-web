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
        'harga',
        'status',
    ];

    public function pemesanan()
    {
        return $this->hasMany(Pemesanan::class, 'id_tiket');
    }

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }
}
