<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthenticationController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('/register', [AuthenticationController::class, 'register']);
Route::post('/check-otp-register', [AuthenticationController::class, 'verifyOtp']);
Route::post('/verify-register', [AuthenticationController::class, 'verifyRegister']);
Route::post('/resend-otp', [AuthenticationController::class, 'resendOtp']);

Route::post('/login', [AuthenticationController::class, 'login']);
