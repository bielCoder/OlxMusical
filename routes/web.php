<?php

use App\Http\Controllers\AuthController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/auth/google/redirect', [AuthController::class,'redirectOAuth']);

Route::get('auth/google/callback', [AuthController::class,'oAuth']);

