<?php

namespace App\Http\Controllers;

use App\Models\DriverLocation;
use App\Models\Transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DriverTripController extends Controller
{   
        //Update the status of transaction and started date
    public function start(Transactions $transaction){
        // Optional: ensure this transaction belongs to the logged-in driver
        // Example if drivers table has user_id:
        abort_unless($transaction->driver && $transaction->driver->user_id === Auth::id(), 403);

        $transaction->update([
            'status' => 'in_transit',
            'started_at' => now(), // if you have this column
        ]);

        return response()->json(['message' => 'Trip started']);
    }

    public function storeLocation(Request $request, Transactions $transaction){
        // Optional ownership check again
        abort_unless($transaction->driver && $transaction->driver->user_id === Auth::id(), 403);

        $data = $request->validate([
            'lat' => ['required', 'numeric'],
            'lng' => ['required', 'numeric'],
            'accuracy' => ['nullable', 'numeric'],
            'speed' => ['nullable', 'numeric'],
        ]);

        DriverLocation::create([
            'transaction_id' => $transaction->id,
            'driver_id' => $transaction->driver_id,
            'lat' => $data['lat'],
            'lng' => $data['lng'],
            'accuracy' => $data['accuracy'] ?? null,
            'speed' => $data['speed'] ?? null,
            'tracked_at' => isset($data['tracked_at']) ? $data['tracked_at'] : now(),
        ]);

        return response()->json(['ok' => true]);
    }

    public function doneTrip(Request $request, $id){

        $transaction = Transactions::findOrFail($id);

        $data = $request->validate([
            'receiver_name' => ['required', 'string', 'max:100'],
            'signature' => ['required', 'string'],
        ]);

        // Convert base64 signature to image file
        $signatureImage = $data['signature'];
        $signatureImage = str_replace('data:image/png;base64,', '', $signatureImage);
        $signatureImage = str_replace(' ', '+', $signatureImage);

        $fileName = 'signatures/' . uniqid() . '.png';
        Storage::disk('public')->put($fileName, base64_decode($signatureImage));

        $transaction->update([
            'status' => 'delivered',
            'receiver_name' => $data['receiver_name'],
            'signature_path' => $fileName,
            'completed_at' => now(),
        ]);

        return redirect('/driver/dashboard')->with('message', 'Trip completed successfully!');
    }
}
