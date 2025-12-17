@extends('layouts.master')

@section('title', 'Riwyat Stok')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('/') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Riwyat Stok</li>
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
                                <label for="id_barang_filter" class="form-label">
                                    Nama Barang <span class="text-danger">*</span>
                                </label>
                                <select name="  " class="form-select" id="id_barang_filter" required>
                                    <option value="">-- Pilih Nama Barang --</option>
                                </select>
                            </div>
                        </div>
                        <div class="d-grid gap-2 d-md-block mt-3 ">
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
                {{-- <div class="card-header g-2 justify-content-between">
                    <button type="button" id="tombol-stokopname" class="btn btn-sm"
                        style="background:  #060771; color: white;" data-bs-toggle="modal" data-bs-target="#Modalbody">
                        <i class="fa-solid fa-plus"></i></button>
                    <button type="button" id="tombol-excel" class="btn btn-sm" style="background: #4CAF50; color: white;">
                        <i class="fa-solid fa-file-excel"></i>
                    </button>
                </div> --}}
                <div class="card-body">
                    <table class="table table-bordered" id="tabel-data">
                        <thead>
                            <tr>
                                <th>Nama Barang</th>
                                <th>Kapasitas</th>
                                <th>Jenis Transaksi</th>
                                <th>Tabung Isi Awal</th>
                                <th>Tabung kosong Awal</th>
                                <th>Tabung isi Akhir</th>
                                <th>Tabung kosong Akhir</th>
                                <th>Perubahan Isi</th>
                                <th>Perubahan Kosong</th>
                            </tr>
                        </thead>
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
        var datas = [];
        var daftarSemuaBarang = [];

        $(document).ready(function() {
            isiDropdownBarang();

            $('#tombol-filter').click(function() {
                ambilDataTransaksi(1);
            });

            $('#tombol-bersihkan').click(function() {
                $('#tanggal_dari').val('');
                $('#tanggal_sampai').val('');
                $('#id_barang_filter').val('');
                $('#tabel-data tbody').empty();
                $('#containerPagination').empty();
                datas = [];
            });
            // Ambil data awal tanpa filter
            ambilDataTransaksi();
        });

        function isiDropdownBarang() {
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
                                    nama_barang: itemBarang.nama_barang,
                                    kapasitas: itemBarang.kapasitas
                                });

                                var optionHtml = '<option value="' + itemBarang.id_barang + '">' +
                                    itemBarang.nama_barang + '</option>';
                                $('#id_barang_filter').append(optionHtml);
                            }
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        title: 'Kesalahan',
                        text: 'Gagal mengambil data barang: ' + (xhr.responseJSON?.message ||
                            'Unknown error'),
                        icon: 'error'
                    });
                }
            });
        }

        function ambilDataTransaksi(halaman = 1) {
            $.ajax({
                url: '/riwayat-stok/getdata',
                type: 'GET',
                data: {
                    page: halaman,
                    tanggal_dari: $('#tanggal_dari').val(),
                    tanggal_sampai: $('#tanggal_sampai').val(),
                    id_barang: $('#id_barang_filter').val()
                },
                success: function(response) {

                    console.log('Response dari server:', response);
                    datas = response.riwayat_stok;
                    tampilkanDataKeTable(datas);
                    tampilkanPagination(response);
                },
                error: function(xhr) {
                    Swal.fire({
                        title: 'Kesalahan',
                        text: 'Terjadi kesalahan: ' + (xhr.responseJSON?.message || 'Unknown error'),
                        icon: 'error'
                    });
                }
            });
        }

        function tampilkanDataKeTable(arrayData) {

            var tbody = $('#tabel-data tbody');
            tbody.empty();

            if (!arrayData || arrayData.length === 0) {
                tbody.append(
                    '<tr><td colspan="9" class="text-center">Tidak ada data untuk ditampilkan</td></tr>'
                );
                return;
            }

            console.log('✨ Mulai loop data, total:', arrayData.length);

            arrayData.forEach(function(item, index) {

                var tipeTransaksi = item.tipe_transaksi || '-';
                var tipeClass = '';

                if (tipeTransaksi === 'MASUK') {
                    tipeClass = 'badge bg-success';
                } else if (tipeTransaksi === 'KELUAR') {
                    tipeClass = 'badge bg-danger';
                } else if (tipeTransaksi === 'KOREKSI') {
                    tipeClass = 'badge bg-warning';
                }

                var namaBarang = (item.barang && item.barang.nama_barang) ? item.barang.nama_barang : '-';
                var kapasitas = (item.barang && item.barang.kapasitas) ? item.barang.kapasitas : '-';

                var row = '<tr>' +
                    '<td>' + namaBarang + '</td>' +
                    '<td>' + kapasitas + '</td>' +
                    '<td><span class="' + tipeClass + '">' + tipeTransaksi + '</span></td>' +
                    '<td class="text-end">' + (item.stok_awal_isi || 0) + '</td>' +
                    '<td class="text-end">' + (item.stok_awal_kosong || 0) + '</td>' +
                    '<td class="text-end">' + (item.stok_isi_setelah || 0) + '</td>' +
                    '<td class="text-end">' + (item.stok_kosong_setelah || 0) + '</td>' +
                    '<td class="text-end ' + (item.perubahan_isi >= 0 ? 'text-success' : 'text-danger') + '">' +
                    (item.perubahan_isi > 0 ? '+' : '') + (item.perubahan_isi || 0) + '</td>' +
                    '<td class="text-end ' + (item.perubahan_kosong >= 0 ? 'text-success' : 'text-danger') + '">' +
                    (item.perubahan_kosong > 0 ? '+' : '') + (item.perubahan_kosong || 0) + '</td>' +
                    '</tr>';
                tbody.append(row);
            });
        }

        function tampilkanPagination(respons) {
            var container = $('#containerPagination');
            container.empty();

            if (!respons || respons.total <= respons.per_page) {
                return;
            }

            var pagination = '<ul class="pagination pagination-sm m-0">';

            // Previous button
            if (respons.has_prev_page) {
                pagination += '<li class="page-item"><a class="page-link" href="#" onclick="ambilDataTransaksi(' + (
                    respons.current_page - 1) + '); return false;">«</a></li>';
            } else {
                pagination += '<li class="page-item disabled"><span class="page-link">«</span></li>';
            }

            // Page numbers
            for (var i = 1; i <= respons.total_page; i++) {
                if (i === respons.current_page) {
                    pagination += '<li class="page-item active"><span class="page-link">' + i + '</span></li>';
                } else {
                    pagination += '<li class="page-item"><a class="page-link" href="#" onclick="ambilDataTransaksi(' +
                        i + '); return false;">' + i + '</a></li>';
                }
            }

            // Next button
            if (respons.has_next_page) {
                pagination += '<li class="page-item"><a class="page-link" href="#" onclick="ambilDataTransaksi(' + (
                    respons.current_page + 1) + '); return false;">»</a></li>';
            } else {
                pagination += '<li class="page-item disabled"><span class="page-link">»</span></li>';
            }

            pagination += '</ul>';
            container.html(pagination);
        }
    </script>
@endpush
