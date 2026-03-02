<?php

namespace App\Http\Controllers;

use App\Models\Drivers;
use App\Models\Transactions;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class DriverController extends Controller
{
    public function index(){    

        if (Auth::user()->role !== 'admin') {
            abort(403); // Forbidden
        }

        // $data = array("drivers" => DB::table('drivers')->orderBy('created_at','desc')->simplePaginate(10)) ;
        // return view('driver.index', $data);
        //  return view('driver.index');

        $totalDrivers = Drivers::get()->count();

        $onDelivery = Drivers::where('status', 'on_delivery')->count();

        $driverLeave = Drivers::where('status', 'restday')->count();
       

        return view('driver.index', [
            'totalDrivers' => $totalDrivers,
            'onDelivery' => $onDelivery,
            'driverLeave' => $driverLeave,
        ]);
    }

    public function store(Request $request){
        $validated = $request->validate([
            "email" => ['required', 'email', Rule::unique('users', 'email')],
            "password" => 'required|confirmed|min:6',
            "first_name" => ['required','string', 'max:30', 'min:2'],
            "last_name" => ['required','string', 'max:30', 'min:2'],
            "address" => ['required','max:55', 'min:4'],    
            "license_number" => ['required', 'min:6', Rule::unique('drivers', 'license_number')],
            "phone" => ['required', 'max:20', 'min:6']  
        ]);
        DB::transaction(function () use ($validated) {

            // 1) create login account
            $user = User::create([
                'name' => $validated['first_name'] . ' ' . $validated['last_name'], // optional
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'driver',
            ]);

            // 2) create driver profile
            Drivers::create([
                'user_id' => $user->id,
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'address' => $validated['address'],
                'license_number' => $validated['license_number'],
                'phone' => $validated['phone'],
                'status' => 'available', // optional default
            ]);
        });

        return redirect('/driver')->with('message', 'Driver added successfully!');
        

        // Drivers::create($validated);
        // return redirect('/driver')->with('message', 'Driver added successfully!');

    }

    public function show($id){

        if (Auth::user()->role !== 'admin') {
            abort(403); // Forbidden
        }

        $data = Drivers::findOrFail($id);
        // dd($data);
        return view('driver.edit', ['drivers' => $data]);
    }

    public function update(Request $request, Drivers $drivers){
        // dd($vehicles);

        $validated = $request->validate([
            "first_name" => ['required','string', 'max:30', 'min:2'],
            "last_name" => ['required','string', 'max:30', 'min:2'],
            "address" => ['required','max:55', 'min:4'],    
            "license_number" => ['required', 'min:6', Rule::unique('drivers', 'license_number')->ignore($drivers->id)],
            "phone" => ['required', 'max:20', 'min:6']    
           
        ]);
        
        $drivers->update($validated);
        // dd($drivers);
        return redirect('/driver')->with('message', 'Driver update successfully!');
    }

    public function dashboard(){

        if (Auth::user()->role !== 'driver') {
            abort(403); // Forbidden
        }

        $driver = Auth::user()->driver;
        // dd($driver);
        $transaction = null;

        if ($driver) {
            $transaction = Transactions::with(['vehicle','driver'])
                ->where('driver_id', $driver->id)
                ->whereIn('status', ['scheduled', 'in_transit'])
                ->latest()
                ->first();
        }
        // dd($transaction);
        return view('driver.dashboard', compact('transaction'));
    }

    public function trip($id){

        if (Auth::user()->role !== 'driver') {
            abort(403); // Forbidden
        }
        
        $data = Transactions::with(['driver', 'vehicle'])->findOrFail($id);
        // dd($data);
        return view('driver.trip', ['transaction' => $data]);
    }

    public function data(Request $request){
        $search = $request->query('search');
        $perPage = (int) $request->query('per_page', 10);

        $q = Drivers::query()->where('status','!=','inactive');

        if ($search) {
            $q->where(function ($x) use ($search) {
                $x->where('first_name','like',"%{$search}%")
                ->orWhere('last_name','like',"%{$search}%")
                ->orWhere('address','like',"%{$search}%")
                ->orWhere('license_number','like',"%{$search}%")
                ->orWhere('phone','like',"%{$search}%")
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
