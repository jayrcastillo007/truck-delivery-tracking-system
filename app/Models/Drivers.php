<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Drivers extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'address',
        'license_number',
        'phone',
        'status',
        'vehicle_id',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
