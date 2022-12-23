<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('guest')->except('logout');
    }

    public function login(Request $request)
    {
        $input = $request->all();
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required'
        ]);
        $loggin_data = array('email' => $input['email'], 'password' => $input['password']);
        // Get the value from the form
        $payload['email'] = $input['email'];
        // Must  exist in the `email` column of `users` table
        $rules = array('email' => 'unique:users,email');
        $validator = Validator::make($payload, $rules);
        $user = User::where('email', $input['email'])->first();
        if (is_null($user)) {
            return Redirect::back()->with('error', 'Invalid Email id');
        }
        if (!Hash::check($input['password'], $user->password)) {
            return Redirect::back()->with('error', 'Incorrect password.');
        }
        if (!$validator->fails()) {
            return Redirect::back()->with('error', 'Invalid Email id');
        }
        if ($request->get('remember') === null) {
            if (isset($_COOKIE['login_email'])) {
                unset($_COOKIE['login_email']); 
                setcookie('login_email', null, -1, '/'); 
            }
            if (isset($_COOKIE['login_pwd'])) {
                unset($_COOKIE['login_pwd']); 
                setcookie('login_pwd', null, -1, '/'); 
            }
        }else{
           setcookie('login_email',$input['email'],time()+60*60*24*30);
           setcookie('login_pwd',$input['password'],time()+60*60*24*30);
        }
        if (auth()->attempt($loggin_data)) {
            //type  1 = su admin ,2 = admin ,3 = user
            $user = Auth::user();
            //return view('agent.dashboard');
            if ($user->type == 1) {
                return redirect()->route('superAdmin.dashboard');
                // return view('superAdmin.dashboard');
            }
            if ($user->type == 2) {
                return redirect()->route('agent.dashboard');
            }
            if ($user->type == 3) {
                return redirect()->route('properties_search');
            }
        } else {
            return Redirect::back()->with('error', 'Login failed please try again.');
        }
    }
}
