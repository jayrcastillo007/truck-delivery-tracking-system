<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicles extends Model
{
    protected $fillable = [
        'vehicle_type',
        'plate_number',
        'capacity',
        'status'
    ];

}
