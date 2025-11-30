<?php

namespace App\Http\Controllers\system;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function index()
    {
        return view('authentication.form-login');
    }

    public function postLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $apiurl = env('APIURL');

        try {
            $response = Http::post($apiurl . '/api/auth/login', [
                'email' => $request->email,
                'password' => $request->password,
            ]);

            if ($response->successful()) {
                $responseData = $response->json();

                if (isset($responseData['access_token'])) {
                    // Simpan token ke session satu per satu
                    $request->session()->put('access_token', $responseData['access_token']);
                    $request->session()->put('token_type', $responseData['token_type'] ?? 'bearer');
                    $request->session()->put('expires_in', $responseData['expires_in'] ?? 3600);
                    $request->session()->save();
                    $request->session()->regenerate();


                    return redirect()->route('/')->with('success', 'Login successful!')
                        ->with('swal', true);
                } else {
                    return back()->with('error', 'Token not found in response')
                        ->with('swal', true);
                }
            } else {
                if ($response->status() == 401) {
                    return back()->with('error', 'Invalid credentials. Please try again.')
                        ->with('swal', true);
                }

                return redirect("login")
                    ->with('error', 'Invalid userid or password')
                    ->with('swal', true);
            }
        } catch (\Exception $e) {
            return redirect("login")
                ->with('error', 'An error occurred while trying to connect to the API.')
                ->with('swal', true);
        }
    }
}
