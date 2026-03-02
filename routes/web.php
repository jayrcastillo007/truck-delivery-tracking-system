<?php

use App\Http\Controllers\AdminTrackingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\DriverTripController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VehicleController;
use App\Models\Drivers;
use App\Models\Vehicles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

//Common routes naming
//index - show all data or students
//show - show a single data or student
//create - show a form to a new user
//store - store a data
//edit - show form to data
//update - update data
//destroy - delete data


    //USER CONTROLLER
Route::controller(UserController::class)->group(function(){
    Route::get('/login','login')->name('login')->middleware('guest');
    
    // Route::get('/login','login');
    // Route::post('/login/process','process');
    Route::post('/login/process','process');
    Route::get('/register','register');
    Route::post('/logout','logout');
    Route::post('/store','store');
    Route::get('/user/data','data');

    Route::get('/user/edit/{id}','show');
    Route::put('/user/update/{user}','update');

    Route::get('/users','index')->middleware('auth');
    
});

    //VEHICLE CONTROLLER
Route::controller(VehicleController::class)->group(function(){
    Route::get('/vehicle','index')->middleware('auth');
    Route::post('/add_vehicle','store');

    Route::get('/vehicle/edit/{id}','show');
    Route::put('/update_vehicle/{vehicles}','update');

    Route::get('/vehicle/archive/{id}','archive');
    
        //VEHICLES LIST
    Route::get('/vehicles_list', function(){
        return Vehicles::select('id', 'vehicle_type', 'status')->get();
    });

    Route::get('/vehicle/data','data');
});

    //DRIVER CONTROLLER
Route::controller(DriverController::class)->group(function(){
    Route::get('/driver','index')->middleware('auth');
    Route::post('/add_driver','store');

    Route::get('/driver/edit/{id}','show')->name('driver');
    Route::put('/update_driver/{drivers}','update');

    Route::get('/driver/data','data');

        //DRIVERS LIST
    Route::get('/drivers_list', function(){
        return Drivers::select('id', 'first_name', 'last_name', 'status')->get();
    });

    Route::get('/driver/dashboard', 'dashboard')->middleware('auth');
    Route::get('/driver/trip/{id}','trip');
});

    //TRANSACTION CONTROLLER
Route::controller(TransactionController::class)->group(function(){
    Route::get('/transaction','index')->middleware('auth');
    Route::post('/create_transaction','create');
    Route::post('/schedule_transaction','schedule');
    Route::post('/start-trip/{id}','start');
    Route::put('/done-trip/{id}','done');

    Route::get('/transaction/data','data');

    Route::get('/transaction/information/{id}','show');
   
});

// Route::get('/admin/dashboard',function () {
//     return view('admin.dashboard');
// });

Route::get('/admin/dashboard', [DashboardController::class, 'index'])->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::post('/driver/trips/{transaction}/start', [DriverTripController::class, 'start']);
    Route::post('/driver/trips/{transaction}/location', [DriverTripController::class, 'storeLocation']);
});

Route::get('/admin/transactions/{transaction}/latest-location', [AdminTrackingController::class, 'latestLocation'])->middleware('auth');
Route::post('/done-trip/{transaction}', [DriverTripController::class, 'doneTrip']);


/////////////////// RESET PASSWORD SEND EMAIL///////////////////////////
Route::post('/forgot-password', function (Request $request) {
    $request->validate([
        'email' => ['required', 'email'],
    ]);

    $status = Password::sendResetLink($request->only('email'));

    return $status === Password::RESET_LINK_SENT
        ? back()->with('status', __($status))
        : back()->withErrors(['email' => __($status)]);
})->middleware('guest')->name('password.email');

Route::get('/reset-password/{token}', function (string $token) {
    return view('auth.reset-password', ['token' => $token]);
})->middleware('guest')->name('password.reset');

Route::post('/reset-password', function (Request $request) {
    $request->validate([
        'token' => ['required'],
        'email' => ['required', 'email'],
        'password' => ['required', 'confirmed', 'min:8'],
    ]);

    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function ($user) use ($request) {
            $user->forceFill([
                'password' => Hash::make($request->password),
                'remember_token' => Str::random(60),
            ])->save();
        }
    );

    return $status === Password::PASSWORD_RESET
        ? redirect('/login')->with('message', 'Password reset successfully. Please login.')
        : back()->withErrors(['email' => __($status)]);
})->middleware('guest')->name('password.update');
