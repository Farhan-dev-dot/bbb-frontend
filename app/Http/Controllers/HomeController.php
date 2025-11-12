<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    public function index()
    {
        return view('dashboard.homepage');
    }


    public function checkSession()
    {
        $token = session('access_token');

        if (!$token) {
            return response()->json([
                'authenticated' => false,
                'message' => 'No session found'
            ], 401);
        }

        return response()->json([
            'authenticated' => true,
            'token' => $token,
            'token_type' => session('token_type', 'bearer'),
            'expires_in' => session('expires_in', 3600)
        ]);
    }




    public function freshdatacustomer()
    {
        $apiurl = env('APIURL');
        $token = session('access_token');

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'No authentication token'
            ], 401);
        }

        try {
            // Pastikan URL format yang benar
            $apiurl = rtrim($apiurl, '/') . '/';
            $fullUrl = $apiurl . 'api/total-customer';

            Log::info('Making API request to: ' . $fullUrl);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json'
            ])->get($fullUrl);
            if ($response->successful()) {
                $data = $response->json();

                return response()->json([
                    'success' => true,
                    'data' => [
                        'total_customer' => $data['data']['total_customer'] ?? 0,
                        'message' => $data['message'] ?? 'Data retrieved successfully'
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'API request failed',
                    'status' => $response->status(),
                    'error' => $response->body()
                ], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Request failed: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function logout(Request $request)
    {
        $request->session()->forget('access_token');
        return redirect()->route('login');
    }
}
