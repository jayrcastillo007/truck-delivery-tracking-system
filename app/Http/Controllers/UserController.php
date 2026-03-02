<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(){

        if (Auth::user()->role !== 'admin') {
            abort(403); // Forbidden
        }
            //Eloquent query
        // $data = Students::where('id', '<=', 10)
        //                 ->where('first_name', '!=', 'Penelope')
        //                 ->get();
            //Laravel query
        // $data = DB::table('students')
        //         ->select(DB::raw('count(*) as gender_count, gender'))
        //         ->groupBy('gender')->get();

        // $data = Students::selectRaw('count(*) as gender_count, gender')
        //        ->groupBy('gender')
        //        ->get();

        // $data = array("users" => DB::table('users')->orderBy('created_at','desc')->simplePaginate(10)) ;
        // return view('students.index', $data);
        // return view('user.list', $data);
        return view('user.list');
    }
        //Login page
    public function login(){
        // if(View::exists('user.login')){
        //     return view('user.login');
        // }else{
        //     return abort(404);
        // }
        return view('user.login');
    }


    public function show($id){

        if (Auth::user()->role !== 'admin') {
            abort(403); // Forbidden
        }

        $data = User::findOrFail($id);
        // dd($data);
        return view('user.edit', ['users' => $data]);
    }

    public function update(Request $request, User $user){
        // dd($vehicles);

        $validated = $request->validate([
            "name" => ['required', 'min:4', 'max:20'],
            "email" => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)]
           
        ]);
        
        $user->update($validated);

        return redirect('/users')->with('message', 'User update successfully!');
    }

        //Login process
    public function process(Request $request){
        
        $validated = $request->validate([
            "email" => ['required', 'email'],
            "password" => 'required'
        ]);

        if (Auth::attempt($validated)) {
            $request->session()->regenerate();

            // check role
            if (Auth::user()->role == 'driver') {
                return redirect('/driver/dashboard');
            }

            // admin
            return redirect('/transaction')->with('message', 'Welcome back!');
        }

        return back()->withErrors(['email' => 'Login failed'])->onlyInput('email');
    }

        //Logout process
    public function logout(Request $request){
        Auth::logout();
        // auth()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('message', 'Logout successful');
    }

        //Register page
    public function register(){
        // if(View::exists('user.login')){
        //     return view('user.login');
        // }else{
        //     return abort(404);
        // }
        return view('user.register');
    }

        //Add user
    public function store(Request $request){
        $validated = $request->validate([
            "name" => ['required', 'min:4'],
            "email" => ['required', 'email', Rule::unique('users', 'email')],
            "password" => 'required|confirmed|min:6'       
        ]);

        $validated['password'] = bcrypt($validated['password']);

        $user = User::create($validated);
        // dd($user);
        // auth()->login($user);
        // Auth::login($user); //use this if u want direct login after create 

        return redirect('/login')->with('message', 'Account created successfully!');

    }

    public function data(Request $request){
        $search = $request->query('search');
        $perPage = (int) $request->query('per_page', 10);

        $q = User::query();

        if ($search) {
            $q->where(function ($x) use ($search) {
                $x->where('name','like',"%{$search}%")
                ->orWhere('email','like',"%{$search}%")
                ->orWhere('role','like',"%{$search}%");
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
