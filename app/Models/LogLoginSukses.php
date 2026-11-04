<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogLoginSukses extends Model
{
    use HasFactory;

    protected $table = 'log_login_sukses';

    protected $fillable = [
        'id_user',
        'nama',
        'email',
        'role',
        'metode',
        'ip_address',
        'user_agent',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
