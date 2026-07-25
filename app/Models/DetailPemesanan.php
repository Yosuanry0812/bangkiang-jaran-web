<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPemesanan extends Model
{
    protected $table = 'detail_pemesanan';
    protected $primaryKey = 'id_detail';

    protected $fillable = [
        'id_pemesanan',
        'id_tiket',
        'kode_tiket',
        'nama_tiket',
        'nama_pengunjung',
        'jenis_kelamin',
        'plat_kendaraan',
        'harga',
        'status_tiket',
        'check_in_at',
        'check_out_at',
    ];

    public function pemesanan()
    {
        return $this->belongsTo(Pemesanan::class, 'id_pemesanan', 'id_pemesanan');
    }

    public function tiket()
    {
        return $this->belongsTo(Tiket::class, 'id_tiket', 'id_tiket');
    }
}
