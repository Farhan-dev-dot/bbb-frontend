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


    public function checkSession(Request $request)
    {


        $token = $request->session()->get('access_token');

        if (!$token) {
            return response()->json([
                'authenticated' => false,
                'message' => 'No session found',
                'session_id' => $request->session()->getId(),
                'session_data' => $request->session()->all()
            ], 401);
        }

        return response()->json([
            'authenticated' => true,
            'token' => $token,
            'token_type' => $request->session()->get('token_type', 'bearer'),
            'expires_in' => $request->session()->get('expires_in', 3600),
            'login_time' => $request->session()->get('login_time')
        ]);
    }




    public function freshdatacustomer(Request $request)
    {
        $apiurl = env('APIURL');
        $token = $request->session()->get('access_token');


        try {
            $fullUrl = rtrim($apiurl, '/') . '/api/total-customer';

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json'
            ])->get($fullUrl);


            if ($response->successful()) {
                $data = $response->json();

                return response()->json([
                    'success' => true,
                    'data' => [
                        'total_customer' => $data['data']['total_customer'] ?? $data['total_customer'] ?? 0,
                        'message' => $data['message'] ?? 'Data retrieved successfully'
                    ],
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

    public function freshdatabarang(Request $request)
    {
        $apiurl = env('APIURL');
        $token = $request->session()->get('access_token');



        try {
            $fullUrl = rtrim($apiurl, '/') . '/api/total-barang';

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json'
            ])->get($fullUrl);


            if ($response->successful()) {
                $data = $response->json();

                return response()->json([
                    'success' => true,
                    'data' => [
                        'total_barang' => $data['data']['total_barang'] ?? $data['total_barang'] ?? 0,
                        'message' => $data['message'] ?? 'Data retrieved successfully'
                    ],
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


    public function freshdatapendapatanpertahun(Request $request)
    {
        $apiurl = env('APIURL');
        $token = $request->session()->get('access_token');


        try {
            $fullUrl = rtrim($apiurl, '/') . '/api/pendapatan-per-tahun';

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json'
            ])->get($fullUrl);


            if ($response->successful()) {
                $data = $response->json();

                return response()->json([
                    'success' => true,
                    'data' => [
                        'pendapatan_per_tahun' => $data['data'],
                        'message' => $data['message'] ?? 'Data retrieved successfully'
                    ],
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

    public function freshdatapendapatanhariini(Request $request)
    {
        $apiurl = env('APIURL');
        $token = $request->session()->get('access_token');
        $tanggal = $request->query('tanggal');


        try {
            $fullUrl = rtrim($apiurl, '/') . '/api/total-pendapatan-harian';

            // Kirim parameter tanggal ke API eksternal
            $params = [];
            if ($tanggal) {
                $params['tanggal'] = $tanggal;
            }
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json'
            ])->get($fullUrl, $params);


            if ($response->successful()) {
                $data = $response->json();

                return response()->json([
                    'success' => true,
                    'data' => [
                        'total_pendapatan_hari_ini' => $data['data']['total_pendapatan_hari_ini'],
                        'message' => $data['message'] ?? 'Data retrieved successfully'
                    ],
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
    public function freshdatatransaksi(Request $request)
    {
        $apiurl = env('APIURL');
        $token = $request->session()->get('access_token');
        $tanggal = $request->query('tanggal'); // Ambil parameter tanggal dari request

        try {
            $fullUrl = rtrim($apiurl, '/') . '/api/total-transaksi-harian';

            // Kirim parameter tanggal ke API eksternal
            $params = [];
            if ($tanggal) {
                $params['tanggal'] = $tanggal;
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json'
            ])->get($fullUrl, $params);

            if ($response->successful()) {
                $data = $response->json();

                return response()->json([
                    'success' => true,
                    'data' => [
                        'total_transaksi_hari_ini' => $data['data']['total_transaksi_hari_ini'],
                        'message' => $data['message'] ?? 'Data retrieved successfully'
                    ],
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



    public function staticDataPendapatanHarian(Request $request)
    {
        try {
            $accessToken = $request->session()->get('access_token');

            if (!$accessToken) {
                return response()->json([
                    'success' => false,
                    'message' => 'No session found',
                ], 401);
            }

            $apiurl = env('APIURL');

            // Ambil parameter bulan dan tahun dari request
            $bulan = $request->query('bulan');
            $tahun = $request->query('tahun');
            $tanggal = $request->query('tanggal');

            $params = [];
            if ($bulan) $params['bulan'] = $bulan;
            if ($tahun) $params['tahun'] = $tahun;
            if ($tanggal) $params['tanggal'] = $tanggal;

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Accept' => 'application/json',
            ])->get($apiurl . '/api/pendapatan-per-tanggal', $params);

            Log::info('API Response Status: ' . $response->status());

            if ($response->successful()) {
                $data = $response->json();
                return response()->json([
                    'success' => true,
                    'data' => $data['data'] ?? $data,
                    'filter' => [
                        'bulan' => $bulan ?? null,
                        'tahun' => $tahun ?? null,
                        'tanggal' => $tanggal ?? null,
                    ],
                    'message' => $data['message'] ?? 'Data retrieved successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'API request failed',
                    'status' => $response->status()
                ], $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Static pendapatan harian error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Internal server error: ' . $e->getMessage()
            ], 500);
        }
    }

    // public function statictdatadistribusi(Request $request)
    // {
    //     // Implementasi fungsi untuk data distribusi

    //     $apiurl = env('APIURL');

    //     $token = $request->session()->get('access_token');


    //     try{
    //         $fullUrl = rtrim($apiurl, '/') . '/api/statistik-distribusi';

    //         $response = Http::withHeaders([
    //             'Authorization' => 'Bearer ' . $token,
    //             'Accept' => 'application/json'
    //         ])->get($fullUrl);

    //         if ($response->successful()) {

    //         }
    //     }
    // }


    public function analiticdatadistribusi(Request $request)
    {

        $apiurl = env('APIURL');
        $token = $request->session()->get('access_token');

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'No access token found',
            ], 401);
        }

        try {
            $fullUrl = rtrim($apiurl, '/') . '/api/distribusi-jenis-barang';

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json'
            ])->get($fullUrl);

            if ($response->successful()) {
                $data = $response->json();

                return response()->json([
                    'success' => true,
                    'data' => $data['data'] ?? [],
                    'message' => $data['message'] ?? 'Data retrieved successfully'
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
        // Hapus semua data session yang terkait autentikasi
        $request->session()->forget(['access_token', 'token_type', 'expires_in', 'login_time']);

        // Regenerate session ID untuk keamanan
        $request->session()->regenerate();

        return redirect()->route('login')->with('success', 'Anda telah berhasil logout.');
    }
}
