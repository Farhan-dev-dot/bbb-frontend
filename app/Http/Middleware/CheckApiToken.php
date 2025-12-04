<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckApiToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Ambil token dari session
        $token = $request->session()->get('access_token');

        // Jika tidak ada token, redirect ke login
        if (!$token) {
            // Log untuk debugging
            Log::info('No access token found, redirecting to login');

            // Jika request adalah AJAX, return JSON response
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Please login first.',
                    'redirect' => route('login')
                ], 401);
            }

            // Jika bukan AJAX, redirect ke halaman login
            return redirect()->route('login')->with('error', 'Anda harus login terlebih dahulu.');
        }

        // Validasi apakah token masih valid (opsional)
        // Anda bisa menambahkan validasi ke API backend di sini

        return $next($request);
    }
}
