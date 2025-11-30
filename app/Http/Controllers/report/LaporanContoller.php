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
        // dd($data);

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Tanggal');
        $sheet->setCellValue('C1', 'Tempat Pengiriman');
        $sheet->setCellValue('D1', 'Pengiriman');
        $sheet->setCellValue('E1', 'Penerima');
        $sheet->setCellValue('F1', 'Jenis Tabung');
        $sheet->setCellValue('G1', 'Tabung Isi');
        $sheet->setCellValue('H1', 'Tabung Kosong');
        $sheet->setCellValue('I1', 'Pinjam Tabung');
        $sheet->setCellValue('J1', 'Harga Satuan');
        $sheet->setCellValue('K1', 'Total Harga');
        $sheet->setCellValue('L1', 'Keterangan');


        // Isi data
        $row = 2;
        foreach ($data as $i => $item) {
            $tanggal = '';
            if (!empty($item['tanggal_transaksi'])) {
                $tanggal = \Carbon\Carbon::parse($item['tanggal_transaksi'])->format('d/m/Y');
            }

            $sheet->setCellValue("A{$row}", $i + 1);
            $sheet->setCellValue("B{$row}", $tanggal);
            $sheet->setCellValue("C{$row}", $item['transaksi']['customer']['alamat'] ?? '');
            $sheet->setCellValue("D{$row}", $item['transaksi']['nama_pengirim'] ?? '');
            $sheet->setCellValue("E{$row}", $item['transaksi']['customer']['nama_customer']  ?? '');
            $sheet->setCellValue("F{$row}", $item['barang']['nama_barang'] ?? '');
            $sheet->setCellValue("G{$row}", $item['transaksi']['jumlah_tabung_isi'] ?? '');
            $sheet->setCellValue("H{$row}", $item['transaksi']['jumlah_tabung_kosong'] ?? '');
            $sheet->setCellValue("I{$row}", $item['transaksi']['jumlah_pinjam_tabung'] ?? '');
            $sheet->setCellValue("J{$row}", $item['transaksi']['harga_satuan'] ?? '');
            $sheet->setCellValue("K{$row}", $item['transaksi']['total_harga'] ?? '');
            $sheet->setCellValue("L{$row}", $item['transaksi']['metode_pembayaran'] ?? '');


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
