@extends('layouts.master')

@section('title', 'Riwayat Transaksi')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('/') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Riwayat Transaksi</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-12 mb-2">
            <div class="card">
                <div class="card-header" style="background-color:#F07124">
                    <button class="btn btn-link text-white p-0" type="button" data-bs-toggle="collapse"
                        data-bs-target="#filterCollapse" aria-expanded="false" aria-controls="filterCollapse">
                        <i class="fa-solid fa-arrow-down-wide-short"></i> Filter
                    </button>
                </div>
                <div id="filterCollapse" class="collapse">
                    <div class="card-body p-3">
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label for="tanggal_dari" class="form-label mb-1">Dari</label>
                                <input type="date" class="form-control" id="tanggal_dari" />
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="tanggal_sampai" class="form-label mb-1">Sampai</label>
                                <input type="date" class="form-control" id="tanggal_sampai" />
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="tipe_transaksi" class="form-label mb-1">Tipe Transaksi</label>
                                <input type="text" class="form-control" id="tipe_transaksi"
                                    placeholder="Tipe Transaksi" />
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="id_barang" class="form-label">
                                    Nama Barang <span class="text-danger">*</span>
                                </label>
                                <select name="id_barang" class="form-select" id="id_barang" required>
                                    <option value="">-- Pilih Nama Barang --</option>
                                </select>
                            </div>
                        </div>
                        <div class="d-grid gap-2 d-md-block mt-3">
                            <button class="btn btn-sm" id="tombol-filter" type="button"
                                style="background: #060771 ;color: white;"><i class="fas fa-filter"></i> Filter</button>
                            <button class="btn btn-sm" id="tombol-bersihkan" type="button"
                                style="background: #F07124; color:white;"><i class="fa-solid fa-recycle"></i> Bersihkan
                                Filter</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header g-2">
                    <button type="button" id="tombol-print" class="btn btn-sm" style="background: #F07124; color: white;">
                        <i class="fa-solid fa-print"></i></button>
                    <button type="button" id="tombol-excel" class="btn btn-sm" style="background: #4CAF50; color: white;">
                        <i class="fa-solid fa-file-excel"></i>
                    </button>
                </div>
                <div class="card-body">
                    <table class="table table-bordered" id="tabel-data">
                        <thead>
                            <tr>
                                <th>Nama Barang</th>
                                <th>Tipe Transaksi</th>
                                <th>Tanggal</th>
                                <th>Perubahan Isi</th>
                                <th>Perubahan Kosong</th>
                                <th>Tabung Isi Awal</th>
                                <th>Tabung Kosong Awal</th>
                                <th>Tabung Isi Setelah</th>
                                <th>Tabung Kosong Setelah</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
                <div class="card-footer clearfix">
                    <div id="containerPagination" class="float-end"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // ============================================
        // VARIABEL GLOBAL
        // ============================================
        var modeDraft = 0; // 0 = tidak draft, 1 = draft baru
        var datas = [];

        $(document).ready(function() {

            // Event: Filter data
            $('#tombol-filter').on('click', function() {
                ambilDataTransaksi(1);
            });

            // Event: Bersihkan filter
            $('#tombol-bersihkan').on('click', function() {
                $('#tanggal_dari').val('');
                $('#tanggal_sampai').val('');
                $('#tipe_transaksi').val('');
                $('#id_barang').val('');
                ambilDataTransaksi(1);
            });


            // Event: Ketika memilih barang dari dropdown
            $('#id_barang').on('change', function() {
                var idBarangTerpilih = $(this).val();
                if (idBarangTerpilih && daftarSemuaBarang.length > 0) {
                    var barangTerpilih = daftarSemuaBarang.find(item => item.id_barang == idBarangTerpilih);
                    if (barangTerpilih) {
                        // Jika harga_jual kosong, tetap pakai harga sebelumnya
                        var hargaSebelumnya = $('#harga_satuan').val();
                        $('#kode_barang').val(barangTerpilih.kode_barang || '');
                        $('#harga_satuan').val(barangTerpilih.harga_jual ? barangTerpilih.harga_jual :
                            hargaSebelumnya);
                    }
                } else {
                    $('#kode_barang').val('');
                    $('#harga_satuan').val('');
                }

            });

            $('#tombol-excel').on('click', function() {
                $.ajax({
                    url: "/riwayat-transaksi/export",
                    method: "POST",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        data: JSON.stringify(datas)
                    },
                    xhrFields: {
                        responseType: 'blob'
                    },
                    success: function(blob) {
                        let url = URL.createObjectURL(blob);
                        let a = document.createElement('a');
                        a.href = url;
                        a.download = "riwayat-transaksi.xlsx";
                        a.click();
                    }
                });
            });


            $('#tombol-print').on('click', function() {
                var tanggal_dari = $('#tanggal_dari').val();
                var tanggal_sampai = $('#tanggal_sampai').val();
                var tipe_transaksi = $('#tipe_transaksi').val();
                var id_barang = $('#id_barang').val();

                var url = '/riwayat-transaksi/cetak';
                var params = [];

                if (tanggal_dari) params.push('tanggal_dari=' + encodeURIComponent(tanggal_dari));
                if (tanggal_sampai) params.push('tanggal_sampai=' + encodeURIComponent(tanggal_sampai));
                if (tipe_transaksi) params.push('tipe_transaksi=' + encodeURIComponent(tipe_transaksi));
                if (id_barang) params.push('id_barang=' + encodeURIComponent(id_barang));

                if (params.length > 0) {
                    url += '?' + params.join('&');
                }

                window.open(url, '_blank');
            });
            // Panggil fungsi ambil data saat halaman pertama kali dimuat
            ambilDataTransaksi();
        });

        // ============================================
        // FUNGSI: AMBIL DATA TRANSAKSI DENGAN FILTER
        // ============================================
        function ambilDataTransaksi(halaman = 1) {
            $.ajax({
                url: '/riwayat-transaksi/getdata',
                type: 'GET',
                data: {
                    page: halaman,
                    tanggal_dari: $('#tanggal_dari').val(),
                    tanggal_sampai: $('#tanggal_sampai').val(),
                    tipe_transaksi: $('#tipe_transaksi').val(),
                    id_barang: $('#id_barang').val(),
                    keyword: $('#keyword').val()
                },
                success: function(respons) {
                    var data = respons.riwayat_transaksi;
                    datas = data;
                    // console.log('dasdasda', datas);
                    tampilkanDataKeTable(datas);
                    tampilkanPagination(respons);
                    // exportToExcel(datas);

                    // Isi dropdown barang 
                    isiDropdownBarang();

                },
                error: function(xhr) {
                    Swal.fire({
                        title: 'Kesalahan',
                        text: 'Terjadi kesalahan: ' + (xhr.responseJSON?.message ||
                            'Unknown error'),
                        icon: 'error'
                    });
                }
            });
        }





        // FUNGSI: TAMPILKAN DATA KE TABLE
        // ============================================
        function tampilkanDataKeTable(datas) {
            var isiTabel = $('#tabel-data tbody');
            isiTabel.empty();

            if (!datas || datas.length === 0) {
                isiTabel.append('<tr><td colspan="9" class="text-center">Tidak ada data ditemukan</td></tr>');
                return;
            }

            $.each(datas, function(indeks, items) {
                var barisTabel =
                    '<tr>' +
                    '<td class="text-center">' + (items.barang.nama_barang ?? '') + '</td>' +
                    '<td class="text-center">' + (items.tipe_transaksi ?? '') + '</td>' +
                    '<td class="text-center">' + formatTanggal(items.tanggal_transaksi) + '</td>' +
                    '<td class="text-center" >' + (items.perubahan_isi ?? 0) + '</td>' +
                    '<td class="text-center" >' + (items.perubahan_kosong ?? 0) + '</td>' +
                    '<td class="text-center" >' + (items.stok_awal_isi ?? 0) + '</td>' +
                    '<td class="text-center" >' + (items.stok_awal_kosong ?? 0) + '</td>' +
                    '<td class="text-center" >' + (items.stok_isi_setelah ?? 0) + '</td>' +
                    '<td class="text-center" >' + (items.stok_kosong_setelah ?? 0) + '</td>' +
                    '</tr>';
                isiTabel.append(barisTabel);
            });
        }

        // ============================================
        // FUNGSI HELPER: FORMAT TANGGAL
        // ============================================
        function formatTanggal(tanggal) {
            if (!tanggal) return '';
            const objekTanggal = new Date(tanggal);
            const hari = String(objekTanggal.getDate()).padStart(2, '0');
            const bulan = String(objekTanggal.getMonth() + 1).padStart(2, '0');
            const tahun = objekTanggal.getFullYear();
            return `${hari}/${bulan}/${tahun}`;
        }


        // ============================================
        // FUNGSI: ISI DROPDOWN BARANG
        // ============================================
        function isiDropdownBarang() {
            var selectBarang = $('#id_barang');
            var nilaiSaatIni = selectBarang.val();

            selectBarang.empty();
            selectBarang.append('<option value="">-- Pilih Nama Barang --</option>');
            daftarSemuaBarang = [];

            $.ajax({
                url: '/transaksi-keluar/barang-list',
                type: 'GET',
                success: function(respons) {
                    var dataBarang = Array.isArray(respons) ? respons : respons.data || [];
                    if (dataBarang.length > 0) {
                        var barangUnik = {};
                        dataBarang.forEach(function(itemBarang) {
                            if (!barangUnik[itemBarang.id_barang]) {
                                barangUnik[itemBarang.id_barang] = true;
                                daftarSemuaBarang.push({
                                    id_barang: itemBarang.id_barang,
                                    kode_barang: itemBarang.kode_barang,
                                    nama_barang: itemBarang.nama_barang,
                                    harga_jual: itemBarang.harga_jual
                                });
                                selectBarang.append('<option value="' + itemBarang.id_barang +
                                    '">' +
                                    itemBarang.nama_barang + '</option>');
                            }
                        });
                    }
                    if (selectBarang.children('option').length === 1) {
                        selectBarang.append('<option value="" disabled>Barang tidak tersedia</option>');
                    }
                    if (nilaiSaatIni) {
                        selectBarang.val(nilaiSaatIni);
                    }
                },
                error: function() {
                    selectBarang.append('<option value="" disabled>Gagal mengambil data barang</option>');
                }
            });
        }


        // ============================================
        // FUNGSI: TAMPILKAN PAGINATION
        // ============================================
        function tampilkanPagination(datas) {
            var htmlPagination = '<ul class="pagination">';
            var halamanSekarang = datas.currentPage || datas.current_page;
            var totalHalaman = datas.totalPage || datas.total_page;

            // Tombol First dan Previous
            if (halamanSekarang > 1) {
                htmlPagination +=
                    '<li class="page-item"><a class="page-link" href="#" onclick="ambilDataTransaksi(1)">First</a></li>';
                htmlPagination += '<li class="page-item"><a class="page-link" href="#" onclick="ambilDataTransaksi(' + (
                    halamanSekarang - 1) + ')">Previous</a></li>';
            }

            // Nomor halaman
            for (var nomorHalaman = 1; nomorHalaman <= totalHalaman; nomorHalaman++) {
                htmlPagination += '<li class="page-item ' + (nomorHalaman == halamanSekarang ? 'active' : '') + '">';
                htmlPagination += '<a class="page-link" href="#" onclick="ambilDataTransaksi(' + nomorHalaman + ')">' +
                    nomorHalaman + '</a>';
                htmlPagination += '</li>';
            }

            // Tombol Next dan Last
            if (halamanSekarang < totalHalaman) {
                htmlPagination += '<li class="page-item"><a class="page-link" href="#" onclick="ambilDataTransaksi(' + (
                    halamanSekarang + 1) + ')">Next</a></li>';
                htmlPagination += '<li class="page-item"><a class="page-link" href="#" onclick="ambilDataTransaksi(' +
                    totalHalaman + ')">Last</a></li>';
            }

            htmlPagination += '</ul>';
            $('#containerPagination').html(htmlPagination);
        }
    </script>
@endpush
