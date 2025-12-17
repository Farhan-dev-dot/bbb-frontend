<?php

namespace App\Http\Controllers\transaksi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class riwayatstokController extends Controller
{
    public function index()
    {
        if (!session()->has('access_token')) {
            return redirect()->route('login');
        }
        return view('transaksi.form-riwayat-stok');
    }

    public function getData(Request $request)
    {
        try {
            $apiUrl = env('APIURL');
            $token = 'Bearer ' . session('access_token');

            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->get($apiUrl . '/api/riwayat-pembelian', [
                'page' => $request->input('page', 1),
                'tanggal_dari' => $request->input('tanggal_dari'),
                'tanggal_sampai' => $request->input('tanggal_sampai'),
                'id_barang' => $request->input('id_barang'),
                'tipe_transaksi' => $request->input('tipe_transaksi'),
                'per_page' => $request->input('per_page', 10)
            ]);

            if ($response->successful()) {
                $responseData = $response->json();

                // Struktur API response
                $dataArray = $responseData['data'] ?? [];

                // Pagination info
                $currentPage = $responseData['current_page'] ?? 1;
                $perPage = $responseData['per_page'] ?? 10;
                $total = $responseData['total'] ?? 0;
                $totalPage = $responseData['total_page'] ?? 1;
                $from = $responseData['from'] ?? null;
                $to = $responseData['to'] ?? null;
                $hasNextPage = $responseData['has_next_page'] ?? false;
                $hasPrevPage = $responseData['has_prev_page'] ?? false;

                return response()->json([
                    'riwayat_stok' => $dataArray,
                    'current_page' => $currentPage,
                    'per_page' => $perPage,
                    'total' => $total,
                    'total_page' => $totalPage,
                    'has_next_page' => $hasNextPage,
                    'has_prev_page' => $hasPrevPage,
                    'from' => $from,
                    'to' => $to
                ]);
            } else {
                $error = $response->json();
                return response()->json([
                    'riwayat_stok' => [],
                    'message' => 'Gagal mengambil data: ' . ($error['message'] ?? 'Unknown error')
                ], 200);
            }
        } catch (\Exception $e) {
            Log::error('Error getData riwayat stok', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'riwayat_stok' => [],
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 200);
        }
    }
}
