<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Seller;

class EnsureSellerIsActive
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || !auth()->user() instanceof Seller) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk mengakses sumber daya ini.',
            ], 403);
        }

        $seller = auth()->user();

        if ($seller->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Akun Anda belum aktif. Silakan verifikasi akun terlebih dahulu.',
            ], 403);
        }

        return $next($request);
    }
}