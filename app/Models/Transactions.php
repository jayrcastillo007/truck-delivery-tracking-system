<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Drivers;
use App\Models\Vehicles;
use App\Models\User;

class Transactions extends Model
{
    protected $fillable = [
        'transaction_code',
        'customer_name',
        'pickup_location',
        'pickup_lat',
        'pickup_long',
        'dropoff_location',
        'dropoff_lat',
        'dropoff_long',
        'cargo_details',
        'status',
        'created_by',
        'driver_id',
        'vehicle_id',
        'scheduled_date',
        'started_at',
        'receiver_name',
        'signature_path',
        'completed_at',
    
    ];

    public function driver()
    {
        return $this->belongsTo(Drivers::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicles::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
