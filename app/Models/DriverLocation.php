<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverLocation extends Model
{
    protected $fillable = [
        'transaction_id',
        'driver_id',
        'lat',
        'lng',
        'accuracy',
        'speed',
        'tracked_at'
    ];
}
