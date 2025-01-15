<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
   

});

Route::prefix('users')->group(function () {
    Route::controller(AuthController::class)->group(function(){
        Route::post('register','register')->name('User - Register');
    });
});