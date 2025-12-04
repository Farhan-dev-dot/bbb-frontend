<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ValidateApiToken
{
    /**
     * Handle an incoming request.
     * Middleware ini akan memvalidasi token dengan API backend
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Ambil token dari session
        $token = $request->session()->get('access_token');

        // Jika tidak ada token, redirect ke login
        if (!$token) {
            Log::info('No access token found, redirecting to login');

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Please login first.',
                    'redirect' => route('login')
                ], 401);
            }

            return redirect()->route('login')->with('error', 'Anda harus login terlebih dahulu.');
        }

        // Validasi token dengan API backend
        $apiUrl = env('APIURL');
        if ($apiUrl) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json'
                ])->get($apiUrl . '/api/auth/me'); // Endpoint untuk validasi token

                // Jika token tidak valid
                if (!$response->successful()) {
                    Log::info('Token validation failed, clearing session');

                    // Hapus session data
                    $request->session()->forget(['access_token', 'token_type', 'expires_in', 'login_time']);
                    $request->session()->regenerate();

                    if ($request->expectsJson() || $request->ajax()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Token expired or invalid. Please login again.',
                            'redirect' => route('login')
                        ], 401);
                    }

                    return redirect()->route('login')->with('error', 'Session expired. Silakan login kembali.');
                }
            } catch (\Exception $e) {
                Log::error('Token validation error: ' . $e->getMessage());
                // Jika terjadi error saat validasi, tetap lanjutkan (fallback ke check token saja)
            }
        }

        return $next($request);
    }
}
