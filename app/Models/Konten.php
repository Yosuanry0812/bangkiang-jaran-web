<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Konten extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'konten';
    protected $primaryKey = 'id_konten';

    protected $fillable = [
        'judul',
        'isi',
        'jenis',
    ];
}
