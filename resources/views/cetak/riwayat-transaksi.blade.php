<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Cetak</title>
    <style type="text/css">
        @page {
            size: A4 portrait;
            margin: 15mm;
        }

        body {
            margin: 0;
            padding: 0;
        }

        #print {
            margin: auto;
            text-align: center;
            font-family: "Calibri", Courier, monospace;
            width: 100%;
            max-width: 210mm;
            font-size: 11px;
        }

        #print .title {
            margin: 10px 0;
            text-align: right;
            font-family: "Calibri", Courier, monospace;
            font-size: 10px;
        }

        #print span {
            text-align: center;
            font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
            font-size: 14px;
        }

        #print table {
            border-collapse: collapse;
            width: 100%;
            margin: 5px 0;
        }

        #print .table1 {
            border-collapse: collapse;
            width: 100%;
            text-align: center;
            margin: 5px 0;
        }

        #print table hr {
            border: 3px double #000;
        }

        #print .ttd {
            float: right;
            width: 200px;
            background-position: center;
            background-size: contain;
        }

        #print table th {
            color: #000;
            font-family: Verdana, Geneva, sans-serif;
            font-size: 10px;
            padding: 8px 4px;
            background-color: #f0f0f0;
        }

        #print table td {
            padding: 6px 4px;
            font-size: 10px;
        }

        #logo {
            width: 80px;
            height: 70px;
            padding-top: 5px;
        }

        h2 {
            margin: 2px 0;
            font-size: 16px;
        }

        h3 {
            margin: 0;
        }

        .table-data {
            width: 100%;
        }

        .table-data th,
        .table-data td {
            border: 1px solid #000;
            text-align: center;
        }

        /* Pengaturan width kolom yang proporsional */
        .col-no {
            width: 5%;
        }

        .col-perubahan {
            width: 12%;
        }

        .col-stok {
            width: 12%;
        }

        .col-tanggal {
            width: 11%;
        }

        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>

<body>
    <div id="print">
        <table class='table1'>
            <tr>
                <td style="width: 100px;"><img src='logoq.png' height="80" width="80"></td>
                <td>
                    <h2>Laporan Riwayat Transaksi</h2>
                    <h2>BBB</h2>
                    <p style="font-size:12px; margin: 5px 0;"><i>Jl. BBB</i></p>
                </td>
            </tr>
        </table>
        <table class='table'>
            <tr>
                <td>
                    <hr />
                </td>
            </tr>
        </table>
        <table class='table table-data'>
            <thead>
                <tr>
                    <th class="col-no">No.</th>
                    <th class="col-tipe">Tipe<br>Transaksi</th>
                    <th class="col-tanggal">Tanggal</th>
                    <th class="col-perubahan">Perubahan<br>Isi</th>
                    <th class="col-perubahan">Perubahan<br>Kosong</th>
                    <th class="col-stok">Tabung Isi<br>Awal</th>
                    <th class="col-stok">Tabung Kosong<br>Awal</th>
                    <th class="col-stok">Tabung Isi<br>Setelah</th>
                    <th class="col-stok">Tabung Kosong<br>Setelah</th>
                </tr>
            </thead>
            <tbody>
                @forelse($riwayat as $i => $item)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $item['tipe_transaksi'] ?? 0 }}</td>
                        <td>{{ \Carbon\Carbon::parse($item['tanggal_transaksi'])->format('d/m/Y') }}</td>
                        <td>{{ $item['perubahan_isi'] ?? 0 }}</td>
                        <td>{{ $item['perubahan_kosong'] ?? 0 }}</td>
                        <td>{{ $item['stok_awal_isi'] ?? 0 }}</td>
                        <td>{{ $item['stok_awal_kosong'] ?? 0 }}</td>
                        <td>{{ $item['stok_isi_setelah'] ?? 0 }}</td>
                        <td>{{ $item['stok_kosong_setelah'] ?? 0 }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="padding: 15px;">Tidak ada data riwayat transaksi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <script type="text/javascript">
        var accessToken = "{{ session('access_token') }}";
        if (!accessToken) {
            window.location.href = "{{ route('login') }}";
        }
        window.print();
    </script>
</body>

</html>
