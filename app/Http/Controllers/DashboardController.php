<?php

namespace App\Http\Controllers;

use App\Models\Transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(){

        $transactions = Transactions::with(['driver', 'vehicle'])
                        ->orderBy('created_at', 'desc')
                        ->get();
        $totalActive = DB::table('transactions')
                        ->where('status','in_transit')
                        ->count();
        $totalComplete = DB::table('transactions')
                        ->where('status','completed')
                        ->count();
        $totalPending = DB::table('transactions')
                        ->where('status','pending')
                        ->count();
        $totalCancelled = DB::table('transactions')
                        ->where('status','cancel')
                        ->count();

        $drivers = DB::table('drivers')->get();

        $availableDrivers = DB::table('drivers')
                        ->where('status', 'available')
                        ->count();

        $topVehicles = DB::table('transactions')
                        ->join('vehicles', 'transactions.vehicle_id', '=', 'vehicles.id')
                        ->select('vehicles.id', 'vehicles.plate_number', DB::raw('COUNT(transactions.id) as trips'))
                        ->whereNotNull('transactions.vehicle_id')
                        ->groupBy('vehicles.id', 'vehicles.plate_number')
                        ->orderByDesc('trips')
                        ->limit(3)
                        ->get();

        $topDrivers = DB::table('transactions')
                        ->join('drivers', 'transactions.driver_id', '=', 'drivers.id')
                        ->select(
                            'drivers.id',
                            'drivers.first_name',
                            'drivers.last_name',
                            DB::raw('COUNT(transactions.id) as trips')
                        )
                        ->where('transactions.status', 'completed')
                        ->whereNotNull('transactions.driver_id')
                        ->groupBy('drivers.id', 'drivers.first_name', 'drivers.last_name')
                        ->orderByDesc('trips')
                        ->limit(3)
                        ->get();
        // dd($transactions);
        return view('admin.dashboard',[
            'transactions' => $transactions,
            'totalActive' => $totalActive,
            'totalComplete' => $totalComplete,
            'totalPending' => $totalPending,
            'totalCancelled' => $totalCancelled,
            'drivers'    => $drivers,
            'availableDrivers' => $availableDrivers,
            'topVehicles' => $topVehicles,
            'topDrivers'  => $topDrivers,
        ]);

        
    }
}
