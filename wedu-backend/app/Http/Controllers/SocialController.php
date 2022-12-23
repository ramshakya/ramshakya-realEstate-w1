<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Validator, Redirect, Response, File;
use Illuminate\Support\Facades\Auth;
use Socialite;
class SocialController extends Controller
{
    public function redirect()
    {
        $url= Socialite::driver('google')->redirect();
        return $url;
    }

    public function callback()
    {
        try {
            $user = Socialite::driver('google')->user();
            // $user = User::where('google_id', $user->id)->first();
            dd($user);

            if ($user) {
                Auth::login($user);
                return redirect('/home');
            } else {
                $newUser = User::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    'google_id' => $user->id,
                    'password' => encrypt('123456dummy')
                ]);
                Auth::login($newUser);
                return redirect('/home');
            }
        } catch (Exception $e) {
            dd($e);
        }
    }
}
