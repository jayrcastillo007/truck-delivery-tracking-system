<?php

namespace App\Http\Controllers;

use App\Mail\DriverAssignedMail;
use App\Models\Drivers;
use App\Models\Transactions;
use App\Models\Vehicles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class TransactionController extends Controller
{
    public function index(){    

        if (Auth::user()->role !== 'admin') {
            abort(403); // Forbidden
        }

        return view('transaction.index');
    }

    public function create(Request $request){

        // dd(
        //     $request->pickup_location,
        //     $request->pickup_lat,
        //     $request->pickup_long,
        //     $request->dropoff_long
        // );

        $validated = $request->validate([
            'customer_name'    => ['required', 'string', 'min:2', 'max:30'],

            'pickup_location'  => ['required', 'string', 'min:10'],
            'pickup_lat'       => ['required', 'numeric', 'between:-90,90'],
            'pickup_long'      => ['required', 'numeric', 'between:-180,180'],

            'dropoff_location' => ['required', 'string', 'min:10'],
            'dropoff_lat'      => ['required', 'numeric', 'between:-90,90'],
            'dropoff_long'     => ['required', 'numeric', 'between:-180,180'],

            'cargo_details'    => ['required', 'string', 'min:4', 'max:100'],
        ],[
            'pickup_lat.required' => 'Please select a complete pickup location.',
            'pickup_long.required' => 'Pickup location is invalid.',
            'dropoff_lat.required' => 'Please select a complete drop-off location.',
        ]);

        $validated['transaction_code'] = 'TC-' . now()->format('Ymd-His');
        $validated['created_by'] = Auth::id();
       

        Transactions::create($validated);

        return redirect('/transaction')
            ->with('message', 'Booking created successfully!');
    }

    public function schedule(Request $request){
      
        $validated = $request->validate([
            'transaction_id' => 'required|exists:transactions,id',
            'driver_id'      => 'required|exists:drivers,id',
            'vehicle_id'     => 'required|exists:vehicles,id',
        ]);

        $transaction = Transactions::findOrFail($validated['transaction_id']);
        
        DB::transaction(function () use ($transaction, $validated) {

            // update transaction first
            $transaction->update([
                'driver_id'       => $validated['driver_id'],
                'vehicle_id'      => $validated['vehicle_id'],
                'status'          => 'scheduled',
                'scheduled_date'  => now()->toDateString(), // if column is DATE
            ]);

            $transaction->vehicle->update([
                'status' => 'assigned',
            ]);

            $transaction->driver->update([
                'status' => 'assigned',
            ]);
        });

        $transaction->load(['driver', 'vehicle']);

        $email = DB::table('users')
            ->where('id', $transaction->driver->user_id)
            ->value('email'); // returns string or null
        // dd($email);
        if ($email) {
            Mail::to($email)->send(new DriverAssignedMail($transaction));
        }

        return response()->json(['message' => 'Scheduled successfully']);
     
        // return redirect('/transaction')->with('message', 'Scheduled successfully!');
    }

    public function start(Request $request){
        // dd($request['id']);


        $transaction = Transactions::findOrFail($request['id']);
        // dd($transaction);

        if (is_null($transaction->driver_id) || is_null($transaction->vehicle_id)) {
           
            return redirect('/transaction')->with('message', 'Transaction must have a driver and vehicle before starting.');
        }
        DB::transaction(function () use ($transaction) {

            $transaction->update([
                'status'     => 'in_transit',
                'started_at' => now(),
            ]);

            $transaction->vehicle->update([
                'status' => 'in_use',
            ]);

            $transaction->driver->update([
                'status' => 'on_delivery',
            ]);
        });

     
        return redirect('/driver/trip/'.$transaction->id)->with('message', 'Transaction started successfully.');
    }

    public function done(Request $request){
        // dd($request['id']);


        $transaction = Transactions::findOrFail($request['id']);
        // dd($transaction);

        if (is_null($transaction->driver_id) || is_null($transaction->vehicle_id)) {
           
            return redirect('/transaction')->with('message', 'Transaction must have a driver and vehicle before starting.');
        }
        DB::transaction(function () use ($transaction) {

            $transaction->update([
                'status'     => 'completed',
                'completed_at' => now(),
            ]);

            $transaction->vehicle->update([
                'status' => 'available',
            ]);

            $transaction->driver->update([
                'status' => 'available',
            ]);
        });

     
        return redirect('/transaction')->with('message', 'Transaction completed successfully.');
    }

    public function show($id){
        
        $data = Transactions::with(['driver', 'vehicle'])->findOrFail($id);
        // dd($data['vehicle']);
        return view('transaction.information', ['transaction' => $data]);
    }

    public function data(Request $request){
        $search = $request->query('search');
        $perPage = (int) $request->query('per_page', 10);

        $q = Transactions::query()->with(['driver', 'vehicle', 'user']);

        if ($search) {
            $q->where(function ($x) use ($search) {
                $x->where('transaction_code','like',"%{$search}%")
                ->orWhere('customer_name','like',"%{$search}%")
                ->orWhere('pickup_location','like',"%{$search}%")
                ->orWhere('dropoff_location','like',"%{$search}%")
                ->orWhere('cargo_details','like',"%{$search}%")
                ->orWhere('status','like',"%{$search}%");
            });
        }

        $page = $q->latest()->paginate($perPage);

        return response()->json([
            'data' => $page->items(),
            'meta' => [
                'current_page' => $page->currentPage(),
                'last_page'    => $page->lastPage(),
                'per_page'     => $page->perPage(),
                'total'        => $page->total(),
            ],
        ]);
    }

}
