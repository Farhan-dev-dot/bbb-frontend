<?php

namespace App\Http\Controllers\transaksi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class riwayatContoller extends Controller
{

    public function index()
    {
        if (!session()->has('access_token')) {
            return redirect()->route('login');
        }
        return view('transaksi.form-riwayat-transaksi');
    }

    public function getData(Request $request)
    {
        $apiUrl = env('APIURL'); // Pastikan .env memiliki APIURL
        $token = 'Bearer ' . session('access_token');

        $response = Http::withHeaders([
            'Authorization' => $token,
        ])->get($apiUrl . '/api/riwayat-pembelian', [
            'page' => $request->input('page', 1),
            'tanggal_dari' => $request->input('tanggal_dari'),
            'tanggal_sampai' => $request->input('tanggal_sampai'),
            'tipe_transaksi' => $request->input('tipe_transaksi'),
            'id_barang' => $request->input('id_barang'),
            'per_page' => $request->input('per_page', 10)
        ]);

        if ($response->successful()) {
            $data = $response->json();

            $riwayat_transaksi = $data['data'] ?? [];
            $currentPage = $data['current_page'] ?? 1;
            $perPage = $data['per_page'] ?? 10;
            $total = $data['total'] ?? 0;
            $totalPage = $data['total_page'] ?? 0; // Catatan: Laravel standar menggunakan 'last_page', bukan 'total_page'
            $hasNextPage = $data['has_next_page'] ?? false;
            $hasPrevPage = $data['has_prev_page'] ?? false;
            $from = $data['from'] ?? null;
            $to = $data['to'] ?? null;

            return compact('riwayat_transaksi', 'currentPage', 'perPage', 'total', 'totalPage', 'hasNextPage', 'hasPrevPage', 'from', 'to');
        } else {
            $error = $response->json();
            // Return error (misalnya, redirect atau JSON)
            return back()->withErrors(['error' => 'Gagal mengambil data customer: ' . ($error['message'] ?? 'Unknown error')]);
        }
    }

    public function Cetakprint(Request $request)
    {
        $apiUrl = env('APIURL');
        $token = 'Bearer ' . session('access_token');

        $response = Http::withHeaders([
            'Authorization' => $token,
        ])->get($apiUrl . '/api/riwayat-pembelian', [
            'tanggal_dari' => $request->input('tanggal_dari'),
            'tanggal_sampai' => $request->input('tanggal_sampai'),
            'tipe_transaksi' => $request->input('tipe_transaksi'),
            'id_barang' => $request->input('id_barang'),
            'per_page' => 1000
        ]);

        $data = $response->json();
        $riwayat = $data['data'] ?? [];

        return view('cetak.riwayat-transaksi', compact('riwayat'));
    }


    public function exportExcel(Request $request)
    {
        $data = json_decode($request->data, true);

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Tipe Transaksi');
        $sheet->setCellValue('C1', 'Tanggal');
        $sheet->setCellValue('D1', 'Perubahan Isi');
        $sheet->setCellValue('E1', 'Perubahan Kosong');
        $sheet->setCellValue('F1', 'Stok Awal Isi');
        $sheet->setCellValue('G1', 'Stok Awal Kosong');
        $sheet->setCellValue('H1', 'Stok Isi Setelah');
        $sheet->setCellValue('I1', 'Stok Kosong Setelah');

        // Isi data
        $row = 2;
        foreach ($data as $i => $item) {
            $sheet->setCellValue("A{$row}", $i + 1);
            $sheet->setCellValue("B{$row}", $item['tipe_transaksi'] ?? 0);

            // format tanggal
            $tgl = \Carbon\Carbon::parse($item['tanggal_transaksi'])->format('d/m/Y');
            $sheet->setCellValue("C{$row}", $tgl);

            $sheet->setCellValue("D{$row}", $item['perubahan_isi'] ?? 0);
            $sheet->setCellValue("E{$row}", $item['perubahan_kosong'] ?? 0);
            $sheet->setCellValue("F{$row}", $item['stok_awal_isi'] ?? 0);
            $sheet->setCellValue("G{$row}", $item['stok_awal_kosong'] ?? 0);
            $sheet->setCellValue("H{$row}", $item['stok_isi_setelah'] ?? 0);
            $sheet->setCellValue("I{$row}", $item['stok_kosong_setelah'] ?? 0);

            $row++;
        }

        // Hindari output lain merusak Excel
        if (ob_get_length()) {
            ob_end_clean();
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        return response()->stream(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="riwayat-transaksi.xlsx"',
            'Cache-Control' => 'max-age=0, no-cache, must-revalidate, proxy-revalidate',
        ]);
    }
}
