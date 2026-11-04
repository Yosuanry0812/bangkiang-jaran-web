<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogLoginGagal extends Model
{
    use HasFactory;

    protected $table = 'log_login_gagal';

    protected $fillable = [
        'input_login',
        'alasan',
        'ip_address',
        'user_agent',
    ];
}
