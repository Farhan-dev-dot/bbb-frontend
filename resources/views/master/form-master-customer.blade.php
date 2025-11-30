@extends('layouts.master')

@section('title', 'Master Pelanggan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('/') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Master Pelanggan</li>
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
                            <label for="custidkeyword" class="form-label mb-1">Kode Pelanggan</label>
                            <select name="custidkeyword" class="form-select" id="custidkeyword">
                                <option value="">Select Kode Pelanggan</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="custnamekeyword" class="form-label mb-1">Nama Pelanggan</label>
                            <input type="text" class="form-control" id="custnamekeyword" placeholder="Nama Pelanggan" />
                        </div>
                    </div>
                    <div class="d-grid gap-2 d-md-block mt-3">
                        <button class="btn  btn-sm" type="button" id="btn-filter"
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
                        data-bs-target="#ModalCustomer" style="background: #F07124; color: white;">
                        <i class="fa-regular fa-user"></i> Tambah Data</button>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table class="table table-bordered" id="data-table">
                        <thead>
                            <tr>
                                <th>Aksi</th>
                                <th>Nama Pelanggan</th>
                                <th>Alamat</th>
                                <th>Telepon</th>
                                <th>Kode Pelanggan</th>
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
@endsection

@include('modal.master-customer')


@push('scripts')
    <script>
        // ==================== VARIABEL GLOBAL ====================
        var modeDraft = 0;
        var dataCustomer;
        var penghitungKode = 1;

        // ==================== INISIALISASI DOKUMEN ====================
        $(document).ready(function() {
            inisialisasiEventListener();
            ambilDataCustomer();
        });

        // ==================== EVENT LISTENER ====================
        function inisialisasiEventListener() {
            // Bersihkan filter
            $('#btn-bersihkan').on('click', function() {
                $('#custidkeyword').val('');
                $('#custnamekeyword').val('');
                ambilDataCustomer();
            });

            // Tambah data customer
            $('#btn-tambah').on('click', function() {
                bukaFormTambahData();
                $('#limit').val('50,000,000.00');
            });

            // Filter customer
            $('#btn-filter').on('click', function() {
                ambilDataCustomer(1);
            });

            // Simpan data
            $(document).on('click', '#btn-save', function(e) {
                e.preventDefault();
                simpanData();
            });
        }

        // ==================== FUNGSI AMBIL DATA ====================
        function ambilDataCustomer(halaman = 1) {
            $.ajax({
                url: '/master-customer/getdata',
                type: 'GET',
                data: {
                    page: halaman,
                    custidkeyword: $('#custidkeyword').val(),
                    custnamekeyword: $('#custnamekeyword').val()
                },
                success: function(response) {
                    dataCustomer = response.customer;
                    tampilkanDataKeTable(dataCustomer);
                    tampilkanPaginasi(response);

                    // Isi dropdown kode customer jika belum terisi
                    if ($('#custidkeyword option').length <= 1) {
                        isiDropdownKodeCustomer(dataCustomer);
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

        // ==================== FUNGSI TAMPILKAN DATA ====================
        function tampilkanDataKeTable(dataCustomer) {
            var tbody = $('#data-table tbody');
            tbody.empty();

            if (dataCustomer.length === 0) {
                tbody.append('<tr><td colspan="5" class="text-center">Tidak ada data ditemukan</td></tr>');
                return;
            }

            $.each(dataCustomer, function(indeks, item) {
                var baris =
                    '<tr>' +
                    '<td>' +
                    '<button class="btn btn-warning btn-sm" onclick="editData(\'' + item.id_customer +
                    '\'); event.stopPropagation();"><i class="fas fa-solid fa-pencil me-2"></i></button> ' +
                    '<button class="btn btn-danger btn-sm" onclick="hapusData(\'' + item.id_customer +
                    '\'); event.stopPropagation();"><i class="fas fa-solid fa-trash"></i></button>' +
                    '</td>' +
                    '<td>' + item.nama_customer + '</td>' +
                    '<td>' + item.alamat + '</td>' +
                    '<td>' + item.telepon + '</td>' +
                    '<td>' + item.kode_customer + '</td>' +
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
                    '<li class="page-item"><a class="page-link" href="#" onclick="ambilDataCustomer(1)">First</a></li>';
                htmlPaginasi += '<li class="page-item"><a class="page-link" href="#" onclick="ambilDataCustomer(' + (
                    halamanSekarang - 1) + ')">Previous</a></li>';
            }

            // Nomor halaman
            for (var i = 1; i <= totalHalaman; i++) {
                htmlPaginasi += '<li class="page-item ' + (i == halamanSekarang ? 'active' : '') + '">';
                htmlPaginasi += '<a class="page-link" href="#" onclick="ambilDataCustomer(' + i + ')">' + i + '</a>';
                htmlPaginasi += '</li>';
            }

            // Tombol Next & Last
            if (halamanSekarang < totalHalaman) {
                htmlPaginasi += '<li class="page-item"><a class="page-link" href="#" onclick="ambilDataCustomer(' + (
                    halamanSekarang + 1) + ')">Next</a></li>';
                htmlPaginasi += '<li class="page-item"><a class="page-link" href="#" onclick="ambilDataCustomer(' +
                    totalHalaman + ')">Last</a></li>';
            }

            htmlPaginasi += '</ul>';
            $('#divPagination').html(htmlPaginasi);
        }

        // ==================== FUNGSI ISI DROPDOWN ====================
        function isiDropdownKodeCustomer(dataCustomer) {
            var dropdown = $('#custidkeyword');
            var nilaiSebelumnya = dropdown.val();

            dropdown.empty();
            dropdown.append('<option value="">Select Kode Customer</option>');

            $.each(dataCustomer, function(indeks, item) {
                dropdown.append('<option value="' + item.kode_customer + '">' + item.kode_customer + '</option>');
            });

            // Kembalikan nilai jika ada
            if (nilaiSebelumnya) {
                dropdown.val(nilaiSebelumnya);
            }
        }

        // ==================== FUNGSI TAMBAH DATA ====================
        function bukaFormTambahData() {
            if (modeDraft == 0) {
                $('#form-data').trigger('reset');
            }
            $('#id_customer').val('');
            $('#kode_customer').val(buatKodeOtomatis());
            modeDraft = 1;
            $('#ModalTitleCustomer').html('Tambah Data Customer');
            $('#ModalCustomer').modal('show');
        }

        function buatKodeOtomatis() {
            let kode = 'CUS' + String(penghitungKode).padStart(3, '0');
            penghitungKode++;
            return kode;
        }

        // ==================== FUNGSI EDIT DATA ====================
        function editData(id) {
            modeDraft = 0;
            $('#ModalTitleCustomer').html('Edit Master Customer');
            ambilDataById(id);
            $('#ModalCustomer').modal('show');
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
                        url: '/master-customer/delete-data/' + id,
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            Swal.fire({
                                title: response.message,
                                icon: 'success'
                            }).then(function() {
                                ambilDataCustomer();
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
            var idCustomer = $("#id_customer").val();
            var kodeCustomer = $('#kode_customer').val();
            var urlAjax = '';
            var tipeAjax = '';
            var pesanSukses = '';

            if (modeDraft == 0) {
                urlAjax = '/master-customer/update-data/' + idCustomer;
                tipeAjax = 'PATCH';
                pesanSukses = 'Data berhasil diupdate!';
            } else {
                urlAjax = '/master-customer/insert-data';
                tipeAjax = 'POST';
                pesanSukses = 'Data berhasil ditambahkan!';
            }

            $.ajax({
                url: urlAjax,
                type: tipeAjax,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    id_customer: idCustomer,
                    kode_customer: kodeCustomer,
                    nama_customer: $("#nama_customer").val(),
                    alamat: $("#alamat").val(),
                    telepon: $("#telepon").val()
                },
                success: function(response) {
                    Swal.fire({
                        title: response.message,
                        icon: "success",
                    });
                    modeDraft = 0;
                    $('#ModalCustomer').modal('hide');
                    ambilDataCustomer();
                },
                error: function(xhr) {
                    Swal.fire({
                        title: 'Kesalahan',
                        text: xhr.responseJSON?.message || 'Error terjadi',
                        icon: 'error'
                    });
                }
            });
        }

        // ==================== FUNGSI AMBIL DATA BY ID ====================
        function ambilDataById(id) {
            $.ajax({
                url: '/master-customer/get-data-by-id/' + id,
                type: 'GET',
                success: function(response) {
                    var data = Array.isArray(response.data) ? response.data[0] : response.data;
                    if (data) {

                        $('#kode_customer').val(data.kode_customer);
                        $('#nama_customer').val(data.nama_customer);
                        $('#alamat').val(data.alamat);
                        $('#telepon').val(data.telepon);
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
