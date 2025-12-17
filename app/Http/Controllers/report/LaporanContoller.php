<?php

namespace App\Http\Controllers\report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class LaporanContoller extends Controller
{
    public function index()
    {
        if (!session()->has('access_token')) {
            return redirect()->route('login');
        }
        return view('report.form-laporan');
    }

    public function getData(Request $request)
    {
        $apiUrl = env('APIURL'); // Pastikan .env memiliki APIURL
        $token = 'Bearer ' . session('access_token');

        $response = Http::withHeaders([
            'Authorization' => $token,
        ])->get($apiUrl . '/api/laporan/laporan-transaksi', [
            'tanggal_dari' => $request->input('tanggal_dari'),
            'tanggal_sampai' => $request->input('tanggal_sampai'),
        ]);

        if ($response->status() == 200) {
            $data = $response->json();

            $laporan = $data['data'] ?? [];
            return compact('laporan');
        } else {
            return back()->withErrors(['error' => 'Gagal mengambil data laporan.']);
        }
    }


    public function exportExcel(Request $request)
    {
        $data = json_decode($request->data, true);

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Tanggal');
        $sheet->setCellValue('C1', 'Tempat Pengiriman');
        $sheet->setCellValue('D1', 'Pengiriman');
        $sheet->setCellValue('E1', 'Penerima');
        $sheet->setCellValue('F1', 'Jenis Tabung');
        $sheet->setCellValue('G1', 'Kapasitas');
        $sheet->setCellValue('H1', 'Tabung Isi');
        $sheet->setCellValue('I1', 'Tabung Kosong');
        $sheet->setCellValue('J1', 'Pinjam Tabung');
        $sheet->setCellValue('K1', 'Harga Satuan');
        $sheet->setCellValue('L1', 'Total Harga');
        $sheet->setCellValue('M1', 'Keterangan');


        // Isi data
        $row = 2;
        foreach ($data as $i => $item) {
            $transaksi = $item['transaksi'] ?? [];
            $barang = $item['barang'] ?? [];
            $customer = $item['customer'] ?? [];

            $sheet->setCellValue("A{$row}", $i + 1);
            $sheet->setCellValue("B{$row}", $transaksi['tanggal_transaksi'] ?? '');
            $sheet->setCellValue("C{$row}", $customer['alamat'] ?? '');
            $sheet->setCellValue("D{$row}", $transaksi['nama_pengirim'] ?? '');
            $sheet->setCellValue("E{$row}", $customer['nama_customer'] ?? '');
            $sheet->setCellValue("F{$row}", $barang['nama_barang'] ?? '');
            $sheet->setCellValue("G{$row}", $barang['kapasitas'] ?? '');
            $sheet->setCellValue("H{$row}", $transaksi['jumlah_tabung_isi'] ?? '');
            $sheet->setCellValue("I{$row}", $transaksi['jumlah_tabung_kosong'] ?? '');
            $sheet->setCellValue("J{$row}", $transaksi['jumlah_pinjam_tabung'] ?? '');
            $sheet->setCellValue("K{$row}", $transaksi['harga_satuan'] ?? '');
            $sheet->setCellValue("L{$row}", $transaksi['total_harga'] ?? '');
            $sheet->setCellValue("M{$row}", $transaksi['keterangan'] ?? '');
            $sheet->setCellValue("N{$row}", $transaksi['metode_pembayaran'] ?? '');
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
