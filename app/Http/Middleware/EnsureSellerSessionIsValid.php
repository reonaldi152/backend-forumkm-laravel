<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class EnsureSellerSessionIsValid
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token tidak ditemukan atau tidak valid.',
            ], 401);
        }

        $accessToken = PersonalAccessToken::findToken($token);

        if (!$accessToken) {
            return response()->json([
                'success' => false,
                'message' => 'Token tidak valid atau telah kadaluarsa.',
            ], 401);
        }

        // Periksa apakah token sudah expired berdasarkan expires_at
        if ($accessToken->expires_at && now()->greaterThan($accessToken->expires_at)) {
            $accessToken->delete(); // Hapus token jika expired

            return response()->json([
                'success' => false,
                'message' => 'Sesi Anda telah kadaluarsa. Silakan login kembali.',
            ], 401);
        }

        // Perbarui expires_at jika user aktif
        $expirationMinutes = (int) env('SELLER_SESSION_EXPIRED', 1440); // Nilai dari ENV
        $accessToken->forceFill([
            'expires_at' => now()->addMinutes($expirationMinutes), // Perpanjang masa aktif
            'last_used_at' => now(), // Perbarui last_used_at
        ])->save();

        return $next($request);
    }
}