<?php

namespace App\Http\Controllers;

use App\Models\Vehicles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class VehicleController extends Controller
{
    public function index(){    

        if (Auth::user()->role !== 'admin') {
            abort(403); // Forbidden
        }

        // $data = array("vehicles" => DB::table('vehicles')->orderBy('created_at','desc')->simplePaginate(10)) ;
        // return view('vehicle.index', $data);

        // $vehicles = DB::table('vehicles')
        // ->orderBy('created_at','desc')
        // ->simplePaginate(10);
        // ->get();
        // return view('vehicle.index', compact('vehicles'));

        $totalVehicles = Vehicles::where('status', '!=', 'inactive')->count();

        $activeVehicles = Vehicles::where('status', '!=', 'inactive')->count();

        $availableVehicles = Vehicles::where('status', 'available')->count();

        $inUseVehicles = Vehicles::where('status', 'in_use')->count();
       

        return view('vehicle.index', [
            'totalVehicles' => $totalVehicles,
            'activeVehicles' => $activeVehicles,
            'availableVehicles' => $availableVehicles,
            'inUseVehicles' => $inUseVehicles,
        ]);

        // $vehicles = Vehicles::latest()->get();
        // return view('vehicle.index', compact('vehicles'));
    }

    public function store(Request $request){
        $validated = $request->validate([
            "vehicle_type" => ['required', 'min:4', 'max:20'],
            "plate_number" => ['required', 'min:6', 'max:10'],
            "capacity" => ['required']       
        ]);

        Vehicles::create($validated);
        return redirect('/vehicle')->with('message', 'Vehicle added successfully!');

    }

    public function show($id){

        if (Auth::user()->role !== 'admin') {
            abort(403); // Forbidden
        }

        $data = Vehicles::findOrFail($id);
        // dd($data);
        return view('vehicle.edit', ['vehicles' => $data]);
    }

    public function update(Request $request, Vehicles $vehicles){
        // dd($vehicles);

        $validated = $request->validate([
            "vehicle_type" => ['required', 'min:4', 'max:20'],
            "plate_number" => ['required', 'min:4', 'max:10'],
            "capacity" => ['required'],
            "status" => ['required']
           
        ]);
        
        $vehicles->update($validated);

        return redirect('/vehicle')->with('message', 'Vehicle update successfully!');
    }

    public function data(Request $request){
        $search = $request->query('search');
        $perPage = (int) $request->query('per_page', 10);

        $q = Vehicles::query()->where('status','!=','inactive');

        if ($search) {
            $q->where(function ($x) use ($search) {
                $x->where('vehicle_type','like',"%{$search}%")
                ->orWhere('plate_number','like',"%{$search}%")
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

    public function archive($id){

        $vehicle = Vehicles::findOrFail($id);
        $vehicle->status = 'in_active';
        $vehicle->save();

        return redirect('/vehicle')->with('message', 'Vehicle archived successfully!');
    }
}
