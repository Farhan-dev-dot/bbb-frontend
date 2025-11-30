<?php

namespace App\Http\Controllers\transaksi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class transaksikeluarController extends Controller
{
    public function index(Request $request)
    {
        if (!session()->has('access_token')) {
            return redirect()->route('login');
        }

        // $apiUrl = env('APIURL'); // Pastikan .env memiliki APIURL
        // $token = 'Bearer ' . session('access_token');

        // $response = Http::withHeaders([
        //     'Authorization' => $token,
        // ])->get($apiUrl . '/api/barang-keluar', [
        //     'page' => $request->input('page', 1),
        //     'tanggal_from' => $request->input('tanggal_from'),
        //     'tanggal_to' => $request->input('tanggal_to'),
        //     'nama_pengirim' => $request->input('nama_pengirim'),
        //     'keyword' => $request->input('keyword'),
        //     'sortby' => $request->input('sortby'),
        //     'sortorder' => $request->input('sortorder'),
        //     'per_page' => $request->input('per_page', 10)
        // ]);

        // $data = $response->json();

        // dd($data);
        return view('transaksi.form-transaksi-keluar');
    }

    public function getData(Request $request)
    {
        $apiUrl = env('APIURL'); // Pastikan .env memiliki APIURL
        $token = 'Bearer ' . session('access_token');

        $response = Http::withHeaders([
            'Authorization' => $token,
        ])->get($apiUrl . '/api/barang-keluar', [
            'page' => $request->input('page', 1),
            'tanggal_from' => $request->input('tanggal_from'),
            'tanggal_to' => $request->input('tanggal_to'),
            'pengirim' => $request->input('pengirim'),
            'keyword' => $request->input('keyword'),
            'sortby' => $request->input('sortby'),
            'sortorder' => $request->input('sortorder'),
            'per_page' => $request->input('per_page', 10)
        ]);

        if ($response->successful()) {
            $data = $response->json();

            $pengiriman = $data['data'] ?? [];
            $currentPage = $data['current_page'] ?? 1;
            $perPage = $data['per_page'] ?? 10;
            $total = $data['total'] ?? 0;
            $totalPage = $data['total_page'] ?? 0; // Catatan: Laravel standar menggunakan 'last_page', bukan 'total_page'
            $hasNextPage = $data['has_next_page'] ?? false;
            $hasPrevPage = $data['has_prev_page'] ?? false;
            $from = $data['from'] ?? null;
            $to = $data['to'] ?? null;

            return compact('pengiriman', 'currentPage', 'perPage', 'total', 'totalPage', 'hasNextPage', 'hasPrevPage', 'from', 'to');
        } else {
            $error = $response->json();
            // Return error (misalnya, redirect atau JSON)
            return back()->withErrors(['error' => 'Gagal mengambil data customer: ' . ($error['message'] ?? 'Unknown error')]);
        }
    }
    public function getDatabyid(Request $request, $id)
    {
        $apiUrl = env('APIURL');
        $token = 'Bearer ' . session('access_token');

        $response = Http::withHeaders([
            'Authorization' => $token,
        ])->get($apiUrl . '/api/barang-keluar/' . $id);

        if ($response->successful()) {
            $data = $response->json();
            return $data;
        } else {
            $error = $response->json();
            return response()->json(['error' => 'Gagal mengambil data barang keluar: ' . ($error['message'] ?? 'Unknown error')], $response->status());
        }
    }

    function InsertData(Request $request)
    {
        /* ========================================================================= */
        /* deklarasi token dan APIURL */
        /* ========================================================================= */
        $apiUrl = env('APIURL');
        $token = 'Bearer ' . session('access_token');


        /* ========================================================================= */
        /* kirim data ke back end untuk diinsert */
        /* ========================================================================= */

        $response = Http::withHeaders([
            'Authorization' => $token,
        ])->post($apiUrl . '/api/barang-keluar', $request->all());

        return response()->json($response->json(), $response->status());
    }


    public function UpdateData(Request $request,  $id)
    {
        /* ========================================================================= */
        /* deklarasi token dan APIURL */
        /* ========================================================================= */
        $apiUrl = env('APIURL');
        $token = 'Bearer ' . session('access_token');

        /* ========================================================================= */
        /* kirim data ke back end untuk diupdate */
        /* ========================================================================= */
        $response = Http::withHeaders([
            'Authorization' => $token,
        ])->patch($apiUrl . '/api/barang-keluar/' . $id, $request->all());

        return response()->json($response->json(), $response->status());
    }


    public function getBarangList()
    {
        $apiUrl = env('APIURL');
        $token = 'Bearer ' . session('access_token');

        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'Authorization' => $token,
        ])->get($apiUrl . '/api/master-barang');

        if ($response->successful()) {
            $data = $response->json();
            // Sesuaikan struktur response jika perlu
            return response()->json($data['data'] ?? $data);
        } else {
            return response()->json(['error' => 'Gagal mengambil data barang'], 500);
        }
    }


    public function getCustomerList()
    {
        $apiUrl = env('APIURL');
        $token = 'Bearer ' . session('access_token');

        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'Authorization' => $token,
        ])->get($apiUrl . '/api/master-customer');

        if ($response->successful()) {
            $data = $response->json();
            // Sesuaikan struktur response jika perlu
            return response()->json($data['data'] ?? $data);
        } else {
            return response()->json(['error' => 'Gagal mengambil data customer'], 500);
        }
    }
}
