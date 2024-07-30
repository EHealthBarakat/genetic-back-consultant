<?php


use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;




Route::prefix('auth')->group(function () {

    Route::post('/login',[AuthController::class,'login'])->name('login');
    Route::post('/send-otp',[AuthController::class,'sendOtp'])->name('send.otp');
    Route::post('/forgot-password',[AuthController::class,'forgotPassword'])->name('forgot.password');
    Route::post('/sign',[AuthController::class,'sign']);
    Route::middleware('auth:sanctum')->get('/logout/{role}',[AuthController::class,'logout']);

});
