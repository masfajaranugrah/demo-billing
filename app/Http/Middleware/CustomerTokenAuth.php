<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class CustomerTokenAuth
{
    /**
     * Handle an incoming request.
     * Autentikasi menggunakan token dari query parameter, header, atau session
     */
    public function handle(Request $request, Closure $next)
    {
        // 1. Cek apakah sudah login via session (untuk akses web biasa)
        if (Auth::guard('customer')->check()) {
            return $next($request);
        }

        // 2. Cek token dari query parameter (?token=xxx)
        $token = $request->query('token');

        // 3. Jika tidak ada di query, cek di header Authorization
        if (!$token && $request->bearerToken()) {
            $token = $request->bearerToken();
        }

        // 4. Jika ada token, validasi dan login
        if ($token) {
            $tokenModel = PersonalAccessToken::findToken($token);

            if ($tokenModel && $tokenModel->tokenable_type === 'App\Models\Pelanggan') {
                $pelanggan = $tokenModel->tokenable;

                // Login pelanggan via guard customer untuk request ini
                Auth::guard('customer')->setUser($pelanggan);

                return $next($request);
            }
        }

        // 5. Jika tidak ada token atau token invalid, redirect ke login
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Token tidak valid atau tidak ditemukan.',
            ], 401);
        }

        return redirect()->route('login')->with('warning', 'Silakan login terlebih dahulu.');
    }
}
