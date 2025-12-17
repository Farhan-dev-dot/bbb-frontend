<?php

namespace App\Http\Controllers\transaksi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class stokopnameContoller extends Controller
{

    public function index()
    {
        if (!session()->has('access_token')) {
            return redirect()->route('login');
        }
        return view('transaksi.form-stok-opname');
    }

    public function getData(Request $request)
    {
        $apiUrl = env('APIURL'); // Pastikan .env memiliki APIURL
        $token = 'Bearer ' . session('access_token');

        $response = Http::withHeaders([
            'Authorization' => $token,
        ])->get($apiUrl . '/api/stok-opname/laporanstok', query: [
            'page' => $request->input('page', 1),
            'tanggal_dari' => $request->input('tanggal_dari'),
            'tanggal_sampai' => $request->input('tanggal_sampai'),
            'nama_barang' => $request->input('nama_barang'),
            'id_barang' => $request->input('id_barang'),
            'per_page' => $request->input('per_page', 10)
        ]);

        if ($response->successful()) {
            $responseData = $response->json();

            // Struktur API baru
            $dataArray = $responseData['data'] ?? [];
            $stok_opname = $dataArray['data'] ?? [];
            $summary = $responseData['summary'] ?? [];

            // Pagination info
            $currentPage = $dataArray['current_page'] ?? 1;
            $perPage = $dataArray['per_page'] ?? 10;
            $total = $dataArray['total'] ?? 0;
            $totalPage = $dataArray['total_page'] ?? 1;
            $from = $dataArray['from'] ?? null;
            $to = $dataArray['to'] ?? null;
            $hasNextPage = $dataArray['has_next_page'] ?? false;
            $hasPrevPage = $dataArray['has_prev_page'] ?? false;

            return response()->json([
                'stok_opname' => $stok_opname,
                'summary' => $summary,
                'currentPage' => $currentPage,
                'perPage' => $perPage,
                'total' => $total,
                'totalPage' => $totalPage,
                'hasNextPage' => $hasNextPage,
                'hasPrevPage' => $hasPrevPage,
                'from' => $from,
                'to' => $to
            ]);
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

    public function getCurrentStok(Request $request)
    {
        $apiUrl = env('APIURL');
        $token = 'Bearer ' . session('access_token');

        $response = Http::withHeaders([
            'Authorization' => $token,
            'Accept' => 'application/json'
        ])->get($apiUrl . '/api/stok-opname/current-stok', [
            'id_barang' => $request->input('id_barang')
        ]);

        if ($response->successful()) {
            $responseData = $response->json();
            return response()->json([
                'success' => true,
                'message' => $responseData['message'] ?? 'Data stok berhasil diambil',
                'data' => $responseData['data'] ?? []
            ]);
        } else {
            $error = $response->json();
            return response()->json([
                'success' => false,
                'message' => $error['message'] ?? 'Gagal mengambil data stok',
                'errors' => $error['errors'] ?? null
            ], $response->status());
        }
    }

    public function getHistory($id_barang)
    {
        $apiUrl = env('APIURL');
        $token = 'Bearer ' . session('access_token');

        $response = Http::withHeaders([
            'Authorization' => $token,
            'Accept' => 'application/json'
        ])->get($apiUrl . '/api/stok-opname/history/' . $id_barang);

        if ($response->successful()) {
            $responseData = $response->json();
            return response()->json([
                'success' => true,
                'message' => $responseData['message'] ?? 'History berhasil diambil',
                'barang' => $responseData['barang'] ?? null,
                'total_history' => $responseData['total_history'] ?? 0,
                'history' => $responseData['history'] ?? []
            ]);
        } else {
            $error = $response->json();
            return response()->json([
                'success' => false,
                'message' => $error['message'] ?? 'Gagal mengambil history',
                'errors' => $error['errors'] ?? null
            ], $response->status());
        }
    }

    public function KoreksiStok(Request $request)
    {
        $apiUrl = env('APIURL');
        $token = 'Bearer ' . session('access_token');

        // Validasi request
        $request->validate([
            'corrections' => 'required|array|min:1',
            'corrections.*.id_barang' => 'required|integer',
            'corrections.*.stok_isi_fisik' => 'required|integer',
            'corrections.*.stok_kosong_fisik' => 'required|integer',
            'corrections.*.keterangan' => 'nullable|string',
            'tanggal_opname' => 'required|date'
        ]);

        // Siapkan payload sesuai struktur API
        $payload = [
            'corrections' => $request->input('corrections'),
            'tanggal_opname' => $request->input('tanggal_opname')
        ];

        $response = Http::withHeaders([
            'Authorization' => $token,
            'Accept' => 'application/json'
        ])->post($apiUrl . '/api/stok-opname/koreksi', $payload);

        if ($response->successful()) {
            return response()->json([
                'success' => true,
                'message' => $response->json()['message'] ?? 'Koreksi stok berhasil',
                'data' => $response->json()['data'] ?? null
            ]);
        } else {
            $error = $response->json();
            return response()->json([
                'success' => false,
                'message' => $error['message'] ?? 'Gagal melakukan koreksi stok',
                'errors' => $error['errors'] ?? null
            ], $response->status());
        }
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
            // Struktur API baru
            $stokSistem = $item['stok_sistem'] ?? [];
            $stokFisik = $item['stok_fisik'] ?? [];
            $selisih = $item['selisih'] ?? [];

            // Hitung total selisih
            $totalSelisih = ($selisih['isi'] ?? 0) + ($selisih['kosong'] ?? 0);

            $sheet->setCellValue("A{$row}", $i + 1);
            $sheet->setCellValue("C{$row}", $item['nama_barang'] ?? '-');
            $sheet->setCellValue("D{$row}", $item['kapasitas'] ?? '-');
            $sheet->setCellValue("E{$row}", $stokSistem['isi'] ?? 0);
            $sheet->setCellValue("F{$row}", $stokSistem['kosong'] ?? 0);
            $sheet->setCellValue("G{$row}", $stokFisik['isi'] ?? '-');
            $sheet->setCellValue("H{$row}", $stokFisik['kosong'] ?? '-');
            $sheet->setCellValue("I{$row}", $selisih['isi'] ?? '-');
            $sheet->setCellValue("J{$row}", $selisih['kosong'] ?? '-');
            $sheet->setCellValue("K{$row}", $totalSelisih !== 0 ? $totalSelisih : '-');

            // Format tanggal opname
            if (isset($item['tanggal_opname']) && $item['tanggal_opname']) {
                $tgl = \Carbon\Carbon::parse($item['tanggal_opname'])->format('d/m/Y');
                $sheet->setCellValue("L{$row}", $tgl);
            } else {
                $sheet->setCellValue("L{$row}", '-');
            }

            $sheet->setCellValue("M{$row}", $item['keterangan'] ?? '-');

            // Style untuk selisih dengan warna
            if (isset($selisih['isi']) && $selisih['isi'] !== null) {
                $selisihIsi = $selisih['isi'];
                if ($selisihIsi > 0) {
                    $sheet->getStyle("I{$row}")->getFont()->getColor()->setRGB('008000'); // Hijau
                } elseif ($selisihIsi < 0) {
                    $sheet->getStyle("I{$row}")->getFont()->getColor()->setRGB('FF0000'); // Merah
                }
            }

            if (isset($selisih['kosong']) && $selisih['kosong'] !== null) {
                $selisihKosong = $selisih['kosong'];
                if ($selisihKosong > 0) {
                    $sheet->getStyle("J{$row}")->getFont()->getColor()->setRGB('008000');
                } elseif ($selisihKosong < 0) {
                    $sheet->getStyle("J{$row}")->getFont()->getColor()->setRGB('FF0000');
                }
            }

            if ($totalSelisih !== 0) {
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
