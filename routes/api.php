<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('users')->group(function () {
        Route::controller(UserController::class)->group(function(){
            Route::get('','index') -> name('api.users');
        });
    });
});

Route::prefix('users')->group(function () {
    Route::controller(AuthController::class)->group(function(){
        Route::post('register','register')->name('User - Register');
        Route::post('login','login')->name('User - Login');
        Route::middleware(['web'])->group(function () {
            Route::get('auth/google/redirect','redirectOAuth')->name('User - OAuth - Redirect');
            Route::get('auth/google/callback', 'oAuth')->name('User - OAuth');
        });
        Route::post('logout','logout')->name('User - Logout');
    });
});


// Route::prefix('dashboard')->group(function () {
//     Route::get('', function () {
//         return response()->json([
//             "status" => "logged"
//         ]);
//     })->name('api.dashboard'); // Certifique-se de usar o prefixo 'api' aqui
// });
