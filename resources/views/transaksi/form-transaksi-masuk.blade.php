@extends('layouts.master')

@section('title', 'Transaksi Masuk')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('/') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Transaksi Masuk</li>
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
                                <label for="tanggal_from" class="form-label mb-1">Form</label>
                                <input type="date" class="form-control" id="tanggal_from" />
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="tanggal_to" class="form-label mb-1">To</label>
                                <input type="date" class="form-control" id="tanggal_to" />
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
    </div>


    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header">
                    <button type="button" id="btn-tambah" class="btn btn-md" style="background: #F07124; color: white;">
                        <i class="fas fa-plus"></i> Tambah Data</button>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table class="table table-bordered" id="data-table">
                        <thead>
                            <tr>
                                <th>Aksi</th>
                                <th>Nama Pelanggan</th>
                                <th>Nama Barang</th>
                                <th>Tabung Isi</th>
                                <th>Tabung Kosong</th>
                                <th>Tanggal</th>
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

@include('modal.transaksimasuk')


@push('scripts')
    <script>
        // ============================================
        // VARIABEL GLOBAL
        // ============================================
        var modeDraft = 0; // 0 = view/update, 1 = tambah baru
        var dataTransaksi = [];
        var daftarSemuaBarang = [];
        var itemsTransaksi = []; // Array untuk menyimpan item yang akan disimpan

        // ============================================
        // INISIALISASI SAAT HALAMAN DIMUAT
        // ============================================
        $(document).ready(function() {
            // Event: Tambah data baru
            $('#btn-tambah').on('click', function() {
                tambahDataBaru();
            });

            // Event: Simpan data
            $('#btn-save').on('click', function(e) {
                e.preventDefault();
                simpanData();
            });

            // Event: Pilih barang - update kode barang
            $('#id_barang').on('change', function() {
                var idBarang = $(this).val();
                if (idBarang && daftarSemuaBarang.length > 0) {
                    var barang = daftarSemuaBarang.find(item => item.id_barang == idBarang);

                }
            });

            // Event: Filter data
            $('#filter-data').on('click', function() {
                ambilDataTransaksi(1);
            });

            // Event: Bersihkan filter
            $('#btn-bersihkan').on('click', function() {
                $('#tanggal_from').val('');
                $('#tanggal_to').val('');
                ambilDataTransaksi(1);
            });

            // Event: Tambah item ke tabel
            $('#btnTambahItem').on('click', function() {
                tambahItemKeTable();
            });

            // Panggil fungsi ambil data saat halaman pertama kali dimuat
            ambilDataTransaksi();
        });

        // ============================================
        // FUNGSI: TAMBAH DATA BARU
        // ============================================
        function tambahDataBaru() {
            modeDraft = 1;
            $('#Modal-title').html('Tambah Data Transaksi Masuk');

            // Reset form dan array items
            $('#form-data')[0].reset();
            $('#id_masuk').val('');
            $('#id_customer').val('');
            itemsTransaksi = [];

            // Reset tabel item
            resetTabelItem();

            // Set tanggal hari ini
            var today = new Date().toISOString().split('T')[0];
            $('#tanggal_masuk').val(today);

            // Set nilai default
            $('#jumlah_isi').val(0);
            $('#jumlah_kosong').val(0);

            // Aktifkan semua input
            $('#form-data input, #form-data select, #form-data textarea').prop('disabled', false);

            // Tampilkan tombol "Tambah Item" untuk mode tambah
            $('#btnTambahItem').show();

            // Buka modal
            $('#Modalbody').modal('show');
        }

        // ============================================
        // FUNGSI: AMBIL DATA TRANSAKSI DENGAN FILTER
        // ============================================
        function ambilDataTransaksi(halaman = 1) {
            $.ajax({
                url: '/transaksi-masuk/getdata',
                type: 'GET',
                data: {
                    page: halaman,
                    tanggal_from: $('#tanggal_from').val(),
                    tanggal_to: $('#tanggal_to').val()
                },
                success: function(respons) {
                    var dataTransaksiMasuk = respons.penerimaan || [];
                    dataTransaksi = dataTransaksiMasuk;

                    tampilkanDataKeTable(dataTransaksiMasuk);
                    tampilkanPagination(respons);

                    // Isi dropdown barang
                    isiDropdownBarang();
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

        // ============================================
        // FUNGSI: TAMPILKAN DATA KE TABEL
        // ============================================
        function tampilkanDataKeTable(dataTransaksi) {
            var tbody = $('#data-table tbody');
            tbody.empty();

            if (!dataTransaksi || dataTransaksi.length === 0) {
                tbody.append('<tr><td colspan="6" class="text-center">Tidak ada data ditemukan</td></tr>');
                return;
            }

            $.each(dataTransaksi, function(indeks, item) {
                var baris = `
                    <tr>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm" role="group">
                                <button class="btn btn-info btn-lihat-detail" data-id="${item.id_masuk}" title="Lihat Detail">
                                    <i class="fas fa-magnifying-glass"></i>
                                </button>
                                <button class="btn btn-warning btn-edit-data" data-id="${item.id_masuk}" title="Edit">
                                    <i class="fas fa-pencil"></i>
                                </button>
                                <button class="btn btn-danger btn-hapus-data" data-id="${item.id_masuk}" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                        <td>${item.customer.nama_customer || '-'}</td>
                        <td>${item.barang.nama_barang || '-'}</td>
                        <td class="text-center">${item.jumlah_isi || 0}</td>
                        <td class="text-center">${item.jumlah_kosong || 0}</td>
                        <td class="text-center">${formatTanggal(item.tanggal_masuk)}</td>
                    </tr>
                `;
                tbody.append(baris);
            });

            // Event listener untuk tombol lihat detail
            $('.btn-lihat-detail').on('click', function() {
                var id = $(this).data('id');
                lihatDetailData(id);
            });

            // Event listener untuk tombol edit
            $('.btn-edit-data').on('click', function() {
                var id = $(this).data('id');
                updateData(id);
            });

            // Event listener untuk tombol hapus
            $('.btn-hapus-data').on('click', function() {
                var id = $(this).data('id');
                hapusData(id);
            });
        }

        // ============================================
        // FUNGSI: LIHAT DETAIL DATA
        // ============================================
        function lihatDetailData(id) {
            modeDraft = 0;
            $('#Modal-title').html('Detail Data Transaksi Masuk');

            // Ambil data
            ambilDataById(id);

            // Nonaktifkan semua input
            $('#form-data input, #form-data select, #form-data textarea, #btn-save').prop('disabled', true);

            // Sembunyikan tombol "Tambah Item" karena mode view only
            $('#btnTambahItem').hide();

            // Buka modal
            $('#Modalbody').modal('show');
        }

        // ============================================
        // FUNGSI: UPDATE DATA (EDIT)
        // ============================================
        function updateData(id) {
            modeDraft = 0;
            $('#Modal-title').html('Edit Data Transaksi Masuk');

            // ISI DROPDOWN BARANG DULU, BARU AMBIL DATA
            isiDropdownBarang(function() {
                // Callback setelah dropdown terisi
                ambilDataById(id);

                // Aktifkan input untuk edit
                $('#form-data input:not([readonly]), #form-data select, #form-data textarea').prop('disabled',
                    false);
                $('#btn-save').prop('disabled', false);

                // Tampilkan tombol "Tambah Item"
                $('#btnTambahItem').show();

                // Buka modal
                $('#Modalbody').modal('show');
            });
        }

        // ============================================
        // FUNGSI: AMBIL DATA BY ID
        // ============================================
        function ambilDataById(id) {
            $.ajax({
                url: '/transaksi-masuk/get-data-by-id/' + id,
                type: 'GET',
                success: function(respons) {
                    var data = Array.isArray(respons.data) ? respons.data[0] : respons.data;

                    // Reset array items
                    itemsTransaksi = [];

                    // Set ID tersembunyi
                    $('#id_masuk').val(data.id_masuk || '');
                    $('#id_customer').val(data.id_customer || '');

                    // Set data customer
                    $('#nama_customer').val(data.customer.nama_customer || '');
                    $('#email').val(data.customer.email || '');
                    $('#no_telfon').val(data.customer.telepon || '');
                    $('#alamat').val(data.customer.alamat || '');

                    // Set barang
                    if (data.barang && data.barang.id_barang) {
                        // Cek apakah option sudah ada
                        if ($('#id_barang option[value="' + data.barang.id_barang + '"]').length === 0) {
                            $('#id_barang').append(new Option(
                                data.barang.nama_barang,
                                data.barang.id_barang,
                                false,
                                false
                            ));
                        }
                        $('#id_barang').val(data.barang.id_barang).trigger('change');
                    }

                    // Set tanggal
                    if (data.tanggal_masuk) {
                        $('#tanggal_masuk').val(formatTanggalDatabase(data.tanggal_masuk));
                    }

                    // Set informasi tabung
                    $('#jumlah_isi').val(data.jumlah_isi || 0);
                    $('#jumlah_kosong').val(data.jumlah_kosong || 0);
                    $('#keterangan').val(data.keterangan || '');

                    // Tambahkan ke array items untuk mode edit
                    itemsTransaksi.push({
                        id_barang: data.barang.id_barang,
                        nama_barang: data.barang.nama_barang,
                        nama_customer: data.customer.nama_customer,
                        email: data.customer.email,
                        no_telfon: data.customer.telepon,
                        alamat: data.customer.alamat,
                        jumlah_isi: data.jumlah_isi || 0,
                        jumlah_kosong: data.jumlah_kosong || 0,
                        keterangan: data.keterangan || '',
                        tanggal_masuk: data.tanggal_masuk
                    });

                    // Render tabel item
                    renderTabelItem();
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

        // ============================================
        // FUNGSI: TAMBAH ITEM KE TABEL
        // ============================================
        function tambahItemKeTable() {
            // Validasi form
            if (!validasiFormItem()) {
                return;
            }

            // Ambil data dari form
            var itemData = {
                id_barang: $('#id_barang').val(),
                nama_barang: $('#id_barang option:selected').text(),
                nama_customer: $('#nama_customer').val(),
                email: $('#email').val(),
                no_telfon: $('#no_telfon').val(),
                alamat: $('#alamat').val(),
                jumlah_isi: parseInt($('#jumlah_isi').val()) || 0,
                jumlah_kosong: parseInt($('#jumlah_kosong').val()) || 0,
                keterangan: $('#keterangan').val(),
                tanggal_masuk: $('#tanggal_masuk').val()
            };

            // Tambahkan ke array
            itemsTransaksi.push(itemData);

            // Render ulang tabel
            renderTabelItem();

            // Clear form barang (tapi pertahankan data customer)
            $('#id_barang').val('');
            $('#jumlah_isi').val(0);
            $('#jumlah_kosong').val(0);
            $('#keterangan').val('');

            // Tampilkan notifikasi
            Swal.fire({
                icon: 'success',
                title: 'Item Ditambahkan',
                text: 'Item berhasil ditambahkan ke daftar transaksi',
                timer: 1500,
                showConfirmButton: false,
                target: document.getElementById('Modalbody')
            });
        }

        // ============================================
        // FUNGSI: RENDER TABEL ITEM
        // ============================================
        function renderTabelItem() {
            var tbody = $('#tableItemList');
            tbody.empty();

            if (itemsTransaksi.length === 0) {
                tbody.append('<tr><td colspan="8" class="text-center">-- Belum ada item ditambahkan --</td></tr>');
                return;
            }

            itemsTransaksi.forEach(function(item, index) {
                var row = `
                    <tr>
                        <td>${item.nama_barang}</td>
                        <td>${item.nama_customer}</td>
                        <td>${item.email}</td>
                        <td>${item.no_telfon}</td>
                        <td class="text-center">${item.jumlah_isi}</td>
                        <td class="text-center">${item.jumlah_kosong}</td>
                        <td class="text-center">${formatTanggal(item.tanggal_masuk)}</td>
                        <td class="text-center">
                            <button type="button" class="btn btn-danger btn-sm btn-hapus-item" data-index="${index}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
                tbody.append(row);
            });

            // Event listener untuk button hapus
            $('.btn-hapus-item').on('click', function() {
                var index = $(this).data('index');
                hapusItem(index);
            });
        }

        // ============================================
        // FUNGSI: HAPUS ITEM DARI TABEL
        // ============================================
        function hapusItem(index) {
            Swal.fire({
                title: 'Hapus Item',
                text: 'Apakah Anda yakin ingin menghapus item ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
                target: document.getElementById('Modalbody')
            }).then((result) => {
                if (result.isConfirmed) {
                    itemsTransaksi.splice(index, 1);
                    renderTabelItem();

                    Swal.fire({
                        icon: 'success',
                        title: 'Item Dihapus',
                        timer: 1000,
                        showConfirmButton: false,
                        target: document.getElementById('Modalbody')
                    });
                }
            });
        }

        // ============================================
        // FUNGSI: RESET TABEL ITEM
        // ============================================
        function resetTabelItem() {
            $('#tableItemList').html(
                '<tr><td colspan="8" class="text-center">-- Belum ada item ditambahkan --</td></tr>'
            );
        }

        // ============================================
        // FUNGSI: HAPUS DATA
        // ============================================
        function hapusData(id) {
            Swal.fire({
                title: 'Hapus Data',
                text: 'Apakah Anda yakin ingin menghapus data transaksi masuk ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    prosesHapusData(id);
                }
            });
        }

        // ============================================
        // FUNGSI: PROSES HAPUS DATA
        // ============================================
        function prosesHapusData(id) {
            $.ajax({
                url: '/transaksi-masuk/delete-data/' + id,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(respons) {
                    Swal.fire({
                        title: 'Berhasil',
                        text: 'Data transaksi masuk berhasil dihapus',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        ambilDataTransaksi();
                    });
                },
                error: function(xhr) {
                    var pesanError = 'Terjadi kesalahan saat menghapus data';

                    if (xhr.responseJSON?.message) {
                        pesanError = xhr.responseJSON.message;
                    }

                    Swal.fire({
                        title: 'Kesalahan',
                        text: pesanError,
                        icon: 'error'
                    });
                }
            });
        }

        // ============================================
        // FUNGSI: SIMPAN DATA
        // ============================================
        function simpanData() {
            // Validasi ada item atau tidak
            if (itemsTransaksi.length === 0) {
                Swal.fire({
                    title: 'Peringatan',
                    text: 'Belum ada item yang ditambahkan. Silakan tambah item terlebih dahulu.',
                    icon: 'warning',
                    target: document.getElementById('Modalbody')
                });
                return;
            }

            // Konfirmasi penyimpanan
            Swal.fire({
                title: 'Simpan Transaksi',
                text: `Apakah Anda yakin ingin menyimpan ${itemsTransaksi.length} item transaksi masuk?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Batal',
                target: document.getElementById('Modalbody')
            }).then((result) => {
                if (result.isConfirmed) {
                    prosesSimapanData();
                }
            });
        }

        // ============================================
        // FUNGSI: PROSES SIMPAN DATA
        // ============================================
        function prosesSimapanData() {
            var idMasuk = $('#id_masuk').val();
            var urlAjax = '';
            var tipeAjax = '';
            var pesanSukses = '';
            var dataSimpan = {};

            // Disable tombol save
            $('#btn-save').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');

            // Tentukan mode (tambah atau update)
            if (idMasuk) {
                // Mode UPDATE - ambil data terbaru dari form
                urlAjax = '/transaksi-masuk/update-data/' + idMasuk;
                tipeAjax = 'PATCH';
                pesanSukses = 'Data transaksi masuk berhasil diupdate.';

                // Ambil data terbaru dari form, bukan dari array
                dataSimpan = {
                    id_barang: $('#id_barang').val(),
                    nama_customer: $('#nama_customer').val(),
                    alamat: $('#alamat').val(),
                    email: $('#email').val(),
                    no_telfon: $('#no_telfon').val(),
                    jumlah_isi: parseInt($('#jumlah_isi').val()) || 0,
                    jumlah_kosong: parseInt($('#jumlah_kosong').val()) || 0,
                    keterangan: $('#keterangan').val(),
                    tanggal_masuk: $('#tanggal_masuk').val()
                };
            } else {
                // Mode INSERT - kirim array items sesuai backend API
                urlAjax = '/transaksi-masuk/insert-data';
                tipeAjax = 'POST';
                pesanSukses = 'Data transaksi masuk berhasil disimpan.';
                dataSimpan = {
                    data: itemsTransaksi // Kirim array items
                };
            }

            $.ajax({
                url: urlAjax,
                type: tipeAjax,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                contentType: 'application/json',
                data: JSON.stringify(dataSimpan),
                success: function(respons) {
                    Swal.fire({
                        title: 'Sukses',
                        text: pesanSukses,
                        icon: 'success'
                    }).then(() => {
                        $('#Modalbody').modal('hide');
                        $('#form-data')[0].reset();
                        itemsTransaksi = [];
                        resetTabelItem();
                        ambilDataTransaksi();
                    });
                },
                error: function(xhr) {
                    var pesanError = 'Terjadi kesalahan saat menyimpan data';

                    if (xhr.status === 422 && xhr.responseJSON?.errors) {
                        var errors = xhr.responseJSON.errors;
                        var errorPertama = Object.values(errors)[0];
                        pesanError = Array.isArray(errorPertama) ? errorPertama[0] : errorPertama;
                    } else if (xhr.responseJSON?.message) {
                        pesanError = xhr.responseJSON.message;
                    }

                    Swal.fire({
                        target: document.getElementById('Modalbody'),
                        title: 'Kesalahan',
                        text: pesanError,
                        icon: 'error'
                    });
                },
                complete: function() {
                    $('#btn-save').prop('disabled', false).html('<i class="fas fa-save me-1"></i>Simpan');
                }
            });
        }

        // ============================================
        // FUNGSI: VALIDASI FORM ITEM
        // ============================================
        function validasiFormItem() {
            var namaCustomer = $('#nama_customer').val();
            var email = $('#email').val();
            var noTelfon = $('#no_telfon').val();
            var alamat = $('#alamat').val();
            var idBarang = $('#id_barang').val();
            var jumlahIsi = parseInt($('#jumlah_isi').val()) || 0;
            var jumlahKosong = parseInt($('#jumlah_kosong').val()) || 0;

            if (!namaCustomer || namaCustomer.trim() === '') {
                Swal.fire({
                    title: 'Data Tidak Lengkap',
                    text: 'Nama supplier harus diisi',
                    icon: 'warning',
                    target: document.getElementById('Modalbody')
                });
                return false;
            }

            if (!email || email.trim() === '') {
                Swal.fire({
                    title: 'Data Tidak Lengkap',
                    text: 'Email supplier harus diisi',
                    icon: 'warning',
                    target: document.getElementById('Modalbody')
                });
                return false;
            }

            if (!noTelfon || noTelfon.trim() === '') {
                Swal.fire({
                    title: 'Data Tidak Lengkap',
                    text: 'No. telepon supplier harus diisi',
                    icon: 'warning',
                    target: document.getElementById('Modalbody')
                });
                return false;
            }

            if (!alamat || alamat.trim() === '') {
                Swal.fire({
                    title: 'Data Tidak Lengkap',
                    text: 'Alamat supplier harus diisi',
                    icon: 'warning',
                    target: document.getElementById('Modalbody')
                });
                return false;
            }

            if (!idBarang) {
                Swal.fire({
                    title: 'Data Tidak Lengkap',
                    text: 'Barang harus dipilih',
                    icon: 'warning',
                    target: document.getElementById('Modalbody')
                });
                return false;
            }

            if (jumlahIsi <= 0 && jumlahKosong <= 0) {
                Swal.fire({
                    title: 'Data Tidak Valid',
                    text: 'Jumlah tabung isi atau kosong harus lebih dari 0',
                    icon: 'warning',
                    target: document.getElementById('Modalbody')
                });
                return false;
            }

            return true;
        }

        // ============================================
        // FUNGSI: ISI DROPDOWN BARANG
        // ============================================
        function isiDropdownBarang(callback) {
            var dropdown = $('#id_barang');
            var nilaiSebelumnya = dropdown.val();

            dropdown.empty();
            dropdown.append('<option value="">-- Pilih Nama Barang --</option>');
            daftarSemuaBarang = [];

            $.ajax({
                url: '/transaksi-keluar/barang-list',
                type: 'GET',
                success: function(respons) {
                    var dataBarang = Array.isArray(respons) ? respons : respons.data || [];

                    if (dataBarang.length > 0) {
                        var barangUnik = {};

                        dataBarang.forEach(function(item) {
                            if (!barangUnik[item.id_barang]) {
                                barangUnik[item.id_barang] = true;

                                daftarSemuaBarang.push({
                                    id_barang: item.id_barang,
                                    nama_barang: item.nama_barang,
                                    harga_jual: item.harga_jual
                                });

                                dropdown.append('<option value="' + item.id_barang + '">' + item
                                    .nama_barang + '</option>');
                            }
                        });
                    }

                    if (dropdown.children('option').length === 1) {
                        dropdown.append('<option value="" disabled>Barang tidak tersedia</option>');
                    }

                    if (nilaiSebelumnya) {
                        dropdown.val(nilaiSebelumnya);
                    }

                    // Panggil callback setelah dropdown terisi
                    if (typeof callback === 'function') {
                        callback();
                    }
                },
                error: function() {
                    dropdown.append('<option value="" disabled>Gagal mengambil data barang</option>');

                    // Tetap panggil callback meskipun error
                    if (typeof callback === 'function') {
                        callback();
                    }
                }
            });
        }

        // ============================================
        // FUNGSI: TAMPILKAN PAGINATION
        // ============================================
        function tampilkanPagination(data) {
            var htmlPaginasi = '<ul class="pagination">';
            var halamanSekarang = data.currentPage || data.current_page || 1;
            var totalHalaman = data.totalPage || data.total_page || 1;

            // Tombol First & Previous
            if (halamanSekarang > 1) {
                htmlPaginasi +=
                    '<li class="page-item"><a class="page-link" href="javascript:void(0)" onclick="ambilDataTransaksi(1)">First</a></li>';
                htmlPaginasi +=
                    '<li class="page-item"><a class="page-link" href="javascript:void(0)" onclick="ambilDataTransaksi(' + (
                        halamanSekarang - 1) + ')">Previous</a></li>';
            }

            // Nomor halaman
            for (var i = 1; i <= totalHalaman; i++) {
                htmlPaginasi += '<li class="page-item ' + (i == halamanSekarang ? 'active' : '') + '">';
                htmlPaginasi += '<a class="page-link" href="javascript:void(0)" onclick="ambilDataTransaksi(' + i + ')">' +
                    i +
                    '</a>';
                htmlPaginasi += '</li>';
            }

            // Tombol Next & Last
            if (halamanSekarang < totalHalaman) {
                htmlPaginasi +=
                    '<li class="page-item"><a class="page-link" href="javascript:void(0)" onclick="ambilDataTransaksi(' + (
                        halamanSekarang + 1) + ')">Next</a></li>';
                htmlPaginasi +=
                    '<li class="page-item"><a class="page-link" href="javascript:void(0)" onclick="ambilDataTransaksi(' +
                    totalHalaman + ')">Last</a></li>';
            }

            htmlPaginasi += '</ul>';
            $('#divPagination').html(htmlPaginasi);
        }

        // ============================================
        // FUNGSI HELPER: FORMAT TANGGAL
        // ============================================
        function formatTanggal(tanggal) {
            if (!tanggal) return '';
            const tanggalObj = new Date(tanggal);
            const hari = String(tanggalObj.getDate()).padStart(2, '0');
            const bulan = String(tanggalObj.getMonth() + 1).padStart(2, '0');
            const tahun = tanggalObj.getFullYear();
            return `${hari}/${bulan}/${tahun}`;
        }

        function formatTanggalDatabase(tanggalStr) {
            if (!tanggalStr) return new Date().toISOString().split('T')[0];

            // Jika objek Date
            if (tanggalStr instanceof Date) {
                const tahun = tanggalStr.getFullYear();
                const bulan = String(tanggalStr.getMonth() + 1).padStart(2, '0');
                const hari = String(tanggalStr.getDate()).padStart(2, '0');
                return `${tahun}-${bulan}-${hari}`;
            }

            tanggalStr = String(tanggalStr);

            // Jika format dd/mm/yyyy
            if (tanggalStr.includes('/')) {
                const bagian = tanggalStr.split('/');
                if (bagian.length === 3) {
                    return `${bagian[2]}-${bagian[1]}-${bagian[0]}`;
                }
            }

            // Jika format ISO
            if (tanggalStr.includes('T')) {
                return tanggalStr.split('T')[0];
            }

            return tanggalStr;
        }
    </script>
@endpush
