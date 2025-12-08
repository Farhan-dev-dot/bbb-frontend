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
        ])->get($apiUrl . '/api/stok-opname/laporanstok', [
            'page' => $request->input('page', 1),
            'tanggal_dari' => $request->input('tanggal_dari'),
            'tanggal_sampai' => $request->input('tanggal_sampai'),
            'tipe_transaksi' => $request->input('tipe_transaksi'),
            'id_barang' => $request->input('id_barang'),
            'per_page' => $request->input('per_page', 10)
        ]);

        if ($response->successful()) {
            $responseData = $response->json();

            $dataArray = $responseData['data'] ?? [];
            $riwayat_transaksi = $dataArray['data'] ?? [];
            $summary = $responseData['summary'] ?? [];

            // Pagination info
            $currentPage = $dataArray['current_page'] ?? 1;
            $perPage = $dataArray['per_page'] ?? 10;
            $total = $dataArray['total'] ?? 0;
            $totalPage = $dataArray['last_page'] ?? 1;
            $from = $dataArray['from'] ?? null;
            $to = $dataArray['to'] ?? null;
            $hasNextPage = !empty($dataArray['next_page_url']);
            $hasPrevPage = !empty($dataArray['prev_page_url']);

            return compact('riwayat_transaksi', 'summary', 'currentPage', 'perPage', 'total', 'totalPage', 'hasNextPage', 'hasPrevPage', 'from', 'to');
        } else {
            $error = $response->json();
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data: ' . ($error['message'] ?? 'Unknown error')
            ], 500);
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

        // Set judul dan styling
        $sheet->mergeCells('A1:L1');
        $sheet->setCellValue('A1', 'LAPORAN PERBANDINGAN STOK SISTEM VS FISIK');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Header kolom
        $sheet->setCellValue('A3', 'No');
        $sheet->setCellValue('B3', 'Kode Barang');
        $sheet->setCellValue('C3', 'Nama Barang');
        $sheet->setCellValue('D3', 'Kapasitas');
        $sheet->setCellValue('E3', 'Stok Sistem Isi');
        $sheet->setCellValue('F3', 'Stok Sistem Kosong');
        $sheet->setCellValue('G3', 'Stok Fisik Isi');
        $sheet->setCellValue('H3', 'Stok Fisik Kosong');
        $sheet->setCellValue('I3', 'Selisih Isi');
        $sheet->setCellValue('J3', 'Selisih Kosong');
        $sheet->setCellValue('K3', 'Total Selisih');
        $sheet->setCellValue('L3', 'Tanggal Opname');
        $sheet->setCellValue('M3', 'Keterangan');

        // Style header
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F07124']
            ],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                ]
            ]
        ];
        $sheet->getStyle('A3:M3')->applyFromArray($headerStyle);

        // Auto size columns
        foreach (range('A', 'M') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Isi data
        $row = 4;
        foreach ($data as $i => $item) {
            $stokSistem = $item['stok_sistem'] ?? [];
            $stokFisik = $item['stok_fisik_terakhir'] ?? [];
            $selisih = $item['selisih'] ?? [];

            $sheet->setCellValue("A{$row}", $i + 1);
            $sheet->setCellValue("B{$row}", $item['kode_barang'] ?? '-');
            $sheet->setCellValue("C{$row}", $item['nama_barang'] ?? '-');
            $sheet->setCellValue("D{$row}", $item['kapasitas'] ?? '-');
            $sheet->setCellValue("E{$row}", $stokSistem['tabung_isi'] ?? 0);
            $sheet->setCellValue("F{$row}", $stokSistem['tabung_kosong'] ?? 0);
            $sheet->setCellValue("G{$row}", $stokFisik['tabung_isi'] ?? '-');
            $sheet->setCellValue("H{$row}", $stokFisik['tabung_kosong'] ?? '-');
            $sheet->setCellValue("I{$row}", $selisih['tabung_isi'] ?? '-');
            $sheet->setCellValue("J{$row}", $selisih['tabung_kosong'] ?? '-');
            $sheet->setCellValue("K{$row}", $selisih['total'] ?? '-');

            // Format tanggal opname
            if (isset($stokFisik['tanggal_opname']) && $stokFisik['tanggal_opname']) {
                $tgl = \Carbon\Carbon::parse($stokFisik['tanggal_opname'])->format('d/m/Y');
                $sheet->setCellValue("L{$row}", $tgl);
            } else {
                $sheet->setCellValue("L{$row}", '-');
            }

            $sheet->setCellValue("M{$row}", $stokFisik['keterangan'] ?? '-');

            // Style untuk selisih dengan warna
            if (isset($selisih['tabung_isi']) && $selisih['tabung_isi'] !== null) {
                $selisihIsi = $selisih['tabung_isi'];
                if ($selisihIsi > 0) {
                    $sheet->getStyle("I{$row}")->getFont()->getColor()->setRGB('008000'); // Hijau
                } elseif ($selisihIsi < 0) {
                    $sheet->getStyle("I{$row}")->getFont()->getColor()->setRGB('FF0000'); // Merah
                }
            }

            if (isset($selisih['tabung_kosong']) && $selisih['tabung_kosong'] !== null) {
                $selisihKosong = $selisih['tabung_kosong'];
                if ($selisihKosong > 0) {
                    $sheet->getStyle("J{$row}")->getFont()->getColor()->setRGB('008000');
                } elseif ($selisihKosong < 0) {
                    $sheet->getStyle("J{$row}")->getFont()->getColor()->setRGB('FF0000');
                }
            }

            if (isset($selisih['total']) && $selisih['total'] !== null) {
                $totalSelisih = $selisih['total'];
                if ($totalSelisih > 0) {
                    $sheet->getStyle("K{$row}")->getFont()->getColor()->setRGB('008000');
                    $sheet->getStyle("K{$row}")->getFont()->setBold(true);
                } elseif ($totalSelisih < 0) {
                    $sheet->getStyle("K{$row}")->getFont()->getColor()->setRGB('FF0000');
                    $sheet->getStyle("K{$row}")->getFont()->setBold(true);
                }
            }

            // Border untuk setiap baris data
            $sheet->getStyle("A{$row}:M{$row}")->getBorders()->getAllBorders()
                ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

            // Center alignment untuk kolom angka
            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("E{$row}:K{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

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
            'Content-Disposition' => 'attachment; filename="laporan-perbandingan-stok-' . date('Y-m-d') . '.xlsx"',
            'Cache-Control' => 'max-age=0, no-cache, must-revalidate, proxy-revalidate',
        ]);
    }

    public function deleteRiwayat($id)
    {
        if (!session()->has('access_token')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $apiUrl = env('APIURL');
        $token = 'Bearer ' . session('access_token');

        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
                'Accept' => 'application/json'
            ])->delete($apiUrl . '/api/stok-opname/hapus/' . $id);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => $response->json()['message'] ?? 'Data berhasil dihapus'
                ]);
            } else {
                $error = $response->json();
                return response()->json([
                    'success' => false,
                    'message' => $error['message'] ?? 'Gagal menghapus data'
                ], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
