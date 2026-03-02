<?php

namespace App\Http\Controllers;

use App\Models\DriverLocation;
use App\Models\Transactions;
use Illuminate\Http\Request;

class AdminTrackingController extends Controller
{
    public function latestLocation(Transactions $transaction)
    {
        $latest = DriverLocation::where('transaction_id', $transaction->id)
            ->orderByDesc('tracked_at')
            ->first();

        if (!$latest) {
            return response()->json(['found' => false]);
        }

        return response()->json([
            'found' => true,
            'lat' => (float) $latest->lat,
            'lng' => (float) $latest->lng,
            'tracked_at' => $latest->tracked_at,
        ]);
    }
}
