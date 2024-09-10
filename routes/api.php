<?php


use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PatientController;
use Illuminate\Support\Facades\Route;




Route::prefix('auth')->group(function () {

    Route::post('/login',[AuthController::class,'login'])->name('login');
    Route::post('/send-otp',[AuthController::class,'sendOtp'])->name('send.otp');
    Route::post('/forgot-password',[AuthController::class,'forgotPassword'])->name('forgot.password');
    Route::post('/sign',[AuthController::class,'sign']);
    Route::middleware('auth:sanctum')->get('/logout/{role}',[AuthController::class,'logout']);

});
Route::middleware('auth:sanctum')->get('/patients/filters',[PatientController::class,'filters']);
Route::middleware('auth:sanctum')->Resource('patients', PatientController::class);

Route::middleware('auth:sanctum')->post('/patients/exist',[PatientController::class,'exist_patient'])->name('patients.exist');
Route::middleware('auth:sanctum')->Resource('appointments', AppointmentController::class);


