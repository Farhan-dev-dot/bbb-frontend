@extends('layouts.master')

@section('title', ' Barang')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('/') }}">Dashboard</a></li>
    <li class="breadcrumb-item active"> Barang</li>
@endsection

@section('content')
    <div class="col-12 mb-2">
        <div class="card">
            <div class="card-header" style="background-color:#F07124">
                <button class="btn btn-link text-white p-0" type="button" data-bs-toggle="collapse"
                    data-bs-target="#filterCollapse" aria-expanded="false" aria-controls="filterCollapse">
                    <i class="fa-solid fa-arrow-down-wide-short"></i> Filter
                </button>
            </div>
            <div id="filterCollapse" class="collapse">
                <div class="card-body p-3"> <!-- konsisten padding -->
                    <div class="row g-3"> <!-- gap antar kolom -->
                        <div class="col-12 col-md-6">
                            <label for="namakeyword" class="form-label mb-1">Nama Barang</label>
                            <input type="text" class="form-control" id="namakeyword" placeholder="Nama Barang" />
                        </div>
                    </div>
                    <div class="d-grid gap-2 d-md-block mt-3">
                        <button class="btn  btn-sm" id="filter-data" type="button"
                            style="background: #060771 ;color: white;"><i class="fas fa-filter"></i> Filter</button>
                        <button class="btn  btn-sm" id="btn-bersihkan" type="button"
                            style="background: #F07124; color:white;"><i class="fa-solid fa-recycle"></i> Bersihkan
                            Filter</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header">
                    <button type="button" id="btn-tambah" class="btn btn-md" data-bs-toggle="modal"
                        data-bs-target="#ModalBarang" style="background: #F07124; color: white;">
                        <i class="fa-solid fa-warehouse"></i> Tambah Data</button>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table class="table table-bordered" id="data-table">
                        <thead>
                            <tr>
                                <th>Aksi</th>
                                <th>Nama Barang</th>
                                <th>Kapasitas</th>
                                <th>Harga</th>
                                <th>Tabung Isi</th>
                                <th>Tabung Kosong</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
                <!-- /.card-body -->
                <div class="card-footer clearfix">
                    <div id="divPagination" class="float-end"></div>
                </div>
            </div>
        </div>
    </div>
    @include('modal.master-barang')
    <!-- /.card -->
@endsection

@push('scripts')
    <script>
        // ==================== VARIABEL GLOBAL ====================
        var modeDraft = 0;
        var dataBarang;
        var penghitungKode = 1;

        // ==================== INISIALISASI DOKUMEN ====================
        $(document).ready(function() {
            inisialisasiEventListener();
            ambilDataBarang();
        });

        // ==================== EVENT LISTENER ====================
        function inisialisasiEventListener() {
            // Bersihkan filter
            $('#btn-bersihkan').on('click', function() {
                $('#kodekeyword').val('');
                $('#namakeyword').val('');
                ambilDataBarang();
            });

            // Tambah data barang
            $('#btn-tambah').on('click', function() {
                bukaFormTambahData();
                $('#limit').val('50,000,000.00');
            });

            // Simpan data
            $(document).on('click', '#btn-save', function(e) {
                e.preventDefault();
                simpanData();
            });

            // Filter barang
            $('#filter-data').on('click', function() {
                ambilDataBarang(1);
            });
        }

        // ==================== FUNGSI AMBIL DATA ====================
        function ambilDataBarang(halaman = 1) {
            $.ajax({
                url: '/master-barang/getdata',
                type: 'GET',
                data: {
                    page: halaman,
                    kodekeyword: $('#kodekeyword').val(),
                    namakeyword: $('#namakeyword').val()
                },
                success: function(response) {
                    dataBarang = response.barang;


                    tampilkanDataKeTable(dataBarang);
                    tampilkanPaginasi(response);

                },
                error: function(xhr, status, error) {
                    // Redirect ke login jika unauthorized atau server error
                    if (xhr.status === 401 || xhr.status === 500) {
                        window.location.href = "/login";
                    }
                },
            });
        }

        // ==================== FUNGSI TAMPILKAN DATA ====================
        function tampilkanDataKeTable(dataBarang) {
            var tbody = $('#data-table tbody');
            tbody.empty();

            if (dataBarang.length === 0) {
                tbody.append('<tr><td colspan="7" class="text-center">Tidak ada data ditemukan</td></tr>');
                return;
            }

            $.each(dataBarang, function(indeks, item) {
                var baris =
                    '<tr>' +
                    '<td>' +
                    '<button class="btn btn-warning btn-sm" onclick="editData(\'' + item.id_barang +
                    '\'); event.stopPropagation();"><i class="fas fa-solid fa-pencil me-2"></i></button> ' +
                    '<button class="btn btn-danger btn-sm" onclick="hapusData(\'' + item.id_barang +
                    '\'); event.stopPropagation();"><i class="fas fa-solid fa-trash"></i></button>' +
                    '</td>' +
                    '<td>' + item.nama_barang + '</td>' +
                    '<td>' + item.kapasitas + '</td>' +
                    '<td>' + item.harga_jual + '</td>' +
                    '<td>' + item.stok_tabung_isi + '</td>' +
                    '<td>' + item.stok_tabung_kosong + '</td>' +
                    '</tr>';
                tbody.append(baris);
            });
        }

        // ==================== FUNGSI PAGINASI ====================
        function tampilkanPaginasi(data) {
            var htmlPaginasi = '<ul class="pagination">';
            var halamanSekarang = data.currentPage || data.current_page;
            var totalHalaman = data.totalPage || data.total_page;

            // Tombol First & Previous
            if (halamanSekarang > 1) {
                htmlPaginasi +=
                    '<li class="page-item"><a class="page-link" href="#" onclick="ambilDataBarang(1)">First</a></li>';
                htmlPaginasi += '<li class="page-item"><a class="page-link" href="#" onclick="ambilDataBarang(' + (
                    halamanSekarang - 1) + ')">Previous</a></li>';
            }

            // Nomor halaman
            for (var i = 1; i <= totalHalaman; i++) {
                htmlPaginasi += '<li class="page-item ' + (i == halamanSekarang ? 'active' : '') + '">';
                htmlPaginasi += '<a class="page-link" href="#" onclick="ambilDataBarang(' + i + ')">' + i + '</a>';
                htmlPaginasi += '</li>';
            }

            // Tombol Next & Last
            if (halamanSekarang < totalHalaman) {
                htmlPaginasi += '<li class="page-item"><a class="page-link" href="#" onclick="ambilDataBarang(' + (
                    halamanSekarang + 1) + ')">Next</a></li>';
                htmlPaginasi += '<li class="page-item"><a class="page-link" href="#" onclick="ambilDataBarang(' +
                    totalHalaman + ')">Last</a></li>';
            }

            htmlPaginasi += '</ul>';
            $('#divPagination').html(htmlPaginasi);
        }

        // ==================== FUNGSI TAMBAH DATA ====================
        function bukaFormTambahData() {
            if (modeDraft == 0) {
                $('#form-data').trigger('reset');
            }
            $('#id_barang').val('');
            modeDraft = 1;
            $('#ModalTitleBarang').html('Tambah Data Barang');
            $('#ModalBarang').modal('show');
        }

        // function buatKodeOtomatis() {
        //     let kode = 'BRG' + String(penghitungKode).padStart(3, '0');
        //     penghitungKode++;
        //     return kode;
        // }

        // ==================== FUNGSI EDIT DATA ====================
        function editData(id) {
            modeDraft = 0;
            $('#ModalTitleBarang').html('Edit Master Barang');
            ambilDataById(id);
            $('#ModalBarang').modal('show');
        }

        // ==================== FUNGSI HAPUS DATA ====================
        function hapusData(id) {
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Anda yakin ingin menghapus data ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal'
            }).then(function(result) {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/master-barang/delete-data/' + id,
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            Swal.fire({
                                title: response.message,
                                icon: 'success'
                            }).then(function() {
                                ambilDataBarang();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                title: 'Kesalahan',
                                text: xhr.responseJSON?.message || 'Terjadi kesalahan',
                                icon: 'error'
                            });
                        }
                    });
                }
            });
        }

        // ==================== FUNGSI SIMPAN DATA ====================
        function simpanData() {
            var idBarang = $("#id_barang").val();
            var namaBarang = $("#nama_barang").val();
            var kapasitas = $("#kapasitas").val();
            var hargaJual = $("#harga_jual").val();
            var stokTabungIsi = $("#stok_tabung_isi").val();
            var stokTabungKosong = $("#stok_tabung_kosong").val();

            var urlAjax = '';
            var tipeAjax = '';
            var pesanSukses = '';

            // Validasi data wajib diisi
            if (!namaBarang) {
                Swal.fire({
                    title: 'Peringatan',
                    text: ' dan nama barang wajib diisi!',
                    icon: 'warning'
                });
                return;
            }

            if (modeDraft == 0) {
                // Mode UPDATE
                urlAjax = '/master-barang/update-data/' + idBarang;
                tipeAjax = 'PATCH';
                pesanSukses = 'Data berhasil diupdate!';
            } else {
                // Mode INSERT
                urlAjax = '/master-barang/insert-data';
                tipeAjax = 'POST';
                pesanSukses = 'Data berhasil ditambahkan!';
            }

            // Data yang akan dikirim
            var dataKirim = {
                id_barang: idBarang,
                nama_barang: namaBarang,
                kapasitas: kapasitas || '',
                harga_jual: hargaJual || 0,
                stok_tabung_isi: stokTabungIsi || 0,
                stok_tabung_kosong: stokTabungKosong || 0
            };

            $.ajax({
                url: urlAjax,
                type: tipeAjax,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Content-Type': 'application/json'
                },
                data: JSON.stringify(dataKirim),
                success: function(response) {
                    Swal.fire({
                        title: response.message || pesanSukses,
                        icon: "success",
                    }).then(function() {
                        modeDraft = 0;
                        $('#ModalBarang').modal('hide');
                        $('#form-data').trigger('reset');
                        ambilDataBarang();
                    });
                },
                error: function(xhr) {
                    var pesanError = 'Error terjadi';

                    if (xhr.status === 422 && xhr.responseJSON?.errors) {
                        var errors = xhr.responseJSON.errors;
                        var errorPertama = Object.values(errors)[0];
                        pesanError = Array.isArray(errorPertama) ? errorPertama[0] : errorPertama;
                    } else if (xhr.responseJSON?.message) {
                        pesanError = xhr.responseJSON.message;
                    }

                    Swal.fire({
                        title: 'Kesalahan',
                        text: pesanError,
                        icon: 'error'
                    });
                },
                complete: function() {
                    $('#btn-save').prop('disabled', false).html('Simpan');
                }
            });
        }

        // ==================== FUNGSI AMBIL DATA BY ID ====================
        function ambilDataById(id) {
            $.ajax({
                url: '/master-barang/get-data-by-id/' + id,
                type: 'GET',
                success: function(response) {
                    var data = Array.isArray(response.data) ? response.data[0] : response.data;
                    console.log(data);
                    if (data) {
                        $('#id_barang').val(data.id_barang);
                        $('#nama_barang').val(data.nama_barang);
                        $('#kapasitas').val(data.kapasitas);
                        $('#harga_jual').val(data.harga_jual);
                        $('#stok_tabung_isi').val(data.stok_tabung_isi);
                        $('#stok_tabung_kosong').val(data.stok_tabung_kosong);
                    }
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
    </script>
@endpush
