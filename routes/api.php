<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\API\AuthSellerController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\ForgotPasswordController;
use Illuminate\Http\Exceptions\HttpResponseException;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::fallback(function () {
    throw new HttpResponseException(response()->json([
        'success' => false,
        'message' => 'Route tidak ditemukan. Harap periksa kembali endpoint Anda.',
    ], 404));
});

// fallback if user not authenticated
Route::get('/login', function () {
    return response()->json([
        'success' => false,
        'message' => 'Silakan login untuk melanjutkan.',
    ], 401);
})->name('login');

//USER
Route::post('/register', [AuthenticationController::class, 'register']);
Route::post('/check-otp-register', [AuthenticationController::class, 'verifyOtp']);
Route::post('/verify-register', [AuthenticationController::class, 'verifyRegister']);
Route::post('/resend-otp', [AuthenticationController::class, 'resendOtp']);

Route::post('/login', [AuthenticationController::class, 'login']);

Route::get('/slider', [HomeController::class, 'getSlider']);
Route::get('/category', [HomeController::class, 'getCategory']);

Route::prefix('forgot-password')->group(function(){
    Route::post('/request', [ForgotPasswordController::class, 'request']);
    Route::post('/resend-otp', [ForgotPasswordController::class, 'resendOtp']);
    Route::post('/check-otp', [ForgotPasswordController::class, 'verifyOtp']);
    Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword']);
});

Route::middleware('auth:sanctum')->group(function(){
    Route::get('profile', [ProfileController::class, 'getProfile']);
    Route::patch('profile', [ProfileController::class, 'updateProfile']);
    
    Route::apiResource('address', AddressController::class);
    Route::post('address/{uuid}/set-default', [AddressController::class, 'setDefault']);

    Route::get('province', [AddressController::class, 'getProvince']);
    Route::get('city', [AddressController::class, 'getCity']);
});

// group route for seller
Route::group(['prefix' => 'seller'], function () {
    Route::post('/register', [AuthSellerController::class, 'registration_seller'])->name('seller.register');
    Route::post('/verify-otp', [AuthSellerController::class, 'verify_otp'])->name('seller.verify_otp');
    Route::post('/resend-otp', [AuthSellerController::class, 'resend_otp'])->name('seller.resend_otp');
    Route::post('/register-step-2', [AuthSellerController::class, 'register_step_2'])->name('seller.register_step_2');
    Route::post('/login', [AuthSellerController::class, 'login'])->name('seller.login');

    Route::middleware(['auth:sanctum', 'seller.active', 'seller.session.valid'])->group(function () {
        Route::post('/logout', [AuthSellerController::class, 'logout']);
        Route::get('/get-seller/{id}', [AuthSellerController::class, 'get_seller']);
    });
});