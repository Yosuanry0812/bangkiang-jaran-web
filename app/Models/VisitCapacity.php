<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitCapacity extends Model
{
    protected $primaryKey = 'visit_date';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['visit_date', 'capacity'];

    protected $casts = ['capacity' => 'integer'];
}
