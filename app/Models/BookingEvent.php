<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingEvent extends Model
{
    public $timestamps = false;

    protected $fillable = ['id_pemesanan', 'actor_id', 'event', 'description', 'created_at'];

    protected $casts = ['created_at' => 'datetime'];

    public function label(): string
    {
        return __('operations.events.'.$this->event);
    }
}
