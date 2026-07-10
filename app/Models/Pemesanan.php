<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pemesanan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pemesanan';
    protected $primaryKey = 'id_pemesanan';

    protected $fillable = [
        'id_user',
        'id_tiket',
        'tgl_kunjungan',
        'jumlah',
        'total_harga',
        'status',
        'kode_booking',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function tiket()
    {
        return $this->belongsTo(Tiket::class, 'id_tiket');
    }

    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class, 'id_pemesanan');
    }
}
