<?php

use App\Http\Controllers\AuthController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/auth/google/redirect', function(){
    return Socialite::driver('google')->redirect();
});

Route::get('/auth/google/callback', function(){
    $googleUser = Socialite::driver('google')->user();
    $user = User::updateOrCreate([
        'google_id' => $googleUser -> id
    ],[
        'name' => $googleUser -> name,
        'email' => $googleUser -> email,
        'google_token' => $googleUser -> token,
        'google_refresh_token' => $googleUser -> refreshToken
    ]);



    Auth::login($user);
    return redirect()->route('api.dashboard');
    
});



