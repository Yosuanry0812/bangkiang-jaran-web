<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;

    protected $table = 'pembayaran';

    protected $primaryKey = 'id_bayar';

    protected $fillable = [
        'id_pemesanan',
        'total',
        'metode',
        'bukti_bayar',
        'status',
        'tgl_bayar',
        'rejection_reason', 'proof_disk', 'verified_at',
    ];

    protected $casts = ['verified_at' => 'datetime'];

    public function pemesanan()
    {
        return $this->belongsTo(Pemesanan::class, 'id_pemesanan');
    }
}
