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
                    <button type="button" id="btn-tambah" class="btn btn-md" data-bs-toggle="modal"
                        data-bs-target="#Modalbody" style="background: #F07124; color: white;">
                        <i class="fas fa-exchange-alt"></i> Tambah Data</button>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table class="table table-bordered" id="data-table">
                        <thead>
                            <tr>
                                <th>Aksi</th>
                                <th>Nama Pelanggan</th>
                                <th>Tabung Isi</th>
                                <th>Tabung Kosong</th>
                                <th>Pinjam Tabung</th>
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
        // ==================== VARIABEL GLOBAL ====================
        var modeDraft = 0;
        var dataTransaksi = [];
        var daftarBarang = [];
        var daftarCustomer = [];

        // ==================== INISIALISASI DOKUMEN ====================
        $(document).ready(function() {
            inisialisasiEventListener();
            ambilDataTransaksi();
        });

        // ==================== EVENT LISTENER ====================
        function inisialisasiEventListener() {
            // Tombol simpan
            $(document).on('click', '#btn-save', function(e) {
                e.preventDefault();
                simpanData();
            });

            // Filter data
            $('#filter-data').on('click', function() {
                ambilDataTransaksi(1);
            });

            // Bersihkan filter
            $('#btn-bersihkan').on('click', function() {
                $('#tanggal_from').val('');
                $('#tanggal_to').val('');
                ambilDataTransaksi(1);
            });

            // Tambah item ke tabel
            $('#btnTambahItem').on('click', function() {
                tambahItemKeTable();
            });

            // Tombol tambah data baru
            $('#btn-tambah').on('click', function() {
                bukaFormTambahData();
            });

            // Event perubahan customer
            $('#id_customer').on('change', function() {
                updateKodeCustomer();
            });

            // Event perubahan barang
            $('#id_barang').on('change', function() {
                updateKodeBarang();
            });
        }

        // ==================== FUNGSI AMBIL DATA ====================
        function ambilDataTransaksi(halaman = 1) {
            $.ajax({
                url: '/transaksi-masuk/getdata',
                type: 'GET',
                data: {
                    page: halaman,
                    tanggal_from: $('#tanggal_from').val(),
                    tanggal_to: $('#tanggal_to').val(),
                    keyword: $('#keyword').val(),
                },
                success: function(response) {
                    dataTransaksi = response.penerimaan;

                    // Isi dropdown terlebih dahulu
                    isiDropdownBarang();
                    isiDropdownCustomer();

                    // Tampilkan data ke tabel
                    tampilkanDataKeTable(dataTransaksi);
                    tampilkanPaginasi(response);
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        title: 'Kesalahan!',
                        text: 'Gagal mengambil data transaksi masuk.',
                        icon: 'error',
                    });
                }
            });
        }

        // ==================== FUNGSI HELPER ====================
        function formatTanggal(tanggal) {
            if (!tanggal) return '';
            const tanggalObj = new Date(tanggal);
            const hari = String(tanggalObj.getDate()).padStart(2, '0');
            const bulan = String(tanggalObj.getMonth() + 1).padStart(2, '0');
            const tahun = tanggalObj.getFullYear();
            return `${hari}/${bulan}/${tahun}`;
        }

        function formatTanggalDatabase(tanggalStr) {
            if (!tanggalStr) return '';

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

        // ==================== FUNGSI TAMBAH DATA ====================
        function bukaFormTambahData() {
            // Aktifkan semua input
            $('#form-data input, #form-data select, #btnTambahItem, #btn-save').prop('disabled', false);

            if (modeDraft == 0) {
                $('#form-data').trigger('reset');
                resetTabelItem();
            }

            $('#id_masuk').val('');
            modeDraft = 1;
            $('#Modal-title').html('Tambah Data Transaksi Masuk');
            $('#Modalbody').modal('show');

            // Set tanggal hari ini
            $('#tanggal_masuk').val(formatTanggal(new Date()));

            // Reset nilai default
            $('#jumlah_isi, #jumlah_kosong, #pinjam_tabung').val(0);
        }

        // ==================== FUNGSI LIHAT DETAIL ====================
        function viewData(id) {
            modeDraft = 0;
            $('#Modal-title').html('Detail Data Transaksi Masuk');
            $('#Modalbody').modal('show');
            ambilDataById(id);

            // Nonaktifkan semua input
            $('#form-data input, #form-data select, #btnTambahItem, #btn-save').prop('disabled', true);
        }

        // ==================== FUNGSI EDIT DATA ====================
        function editData(id) {
            modeDraft = 0;
            $('#Modal-title').html('Edit Data Transaksi Masuk');
            $('#Modalbody').modal('show');
            ambilDataById(id);

            // Aktifkan input untuk edit
            $('#form-data input:not([readonly]), #form-data select, #btn-save').prop('disabled', false);
            $('#btnTambahItem').prop('disabled', true);
        }

        // ==================== FUNGSI TAMPILKAN DATA ====================
        function tampilkanDataKeTable(dataTransaksi) {
            var tbody = $('#data-table tbody');
            tbody.empty();

            if (!dataTransaksi || dataTransaksi.length === 0) {
                tbody.append('<tr><td colspan="6" class="text-center">Tidak ada data ditemukan</td></tr>');
                return;
            }

            $.each(dataTransaksi, function(indeks, item) {
                var baris =
                    '<tr>' +
                    '<td class="text-center">' +
                    '<div class="btn-group btn-group-sm" role="group">' +
                    '<button class="btn btn-info" onclick="viewData(\'' + item.id_masuk +
                    '\'); event.stopPropagation();" title="Lihat Detail">' +
                    '<i class="fas fa-magnifying-glass"></i>' +
                    '</button>' +
                    '<button class="btn btn-warning" onclick="editData(\'' + item.id_masuk +
                    '\'); event.stopPropagation();" title="Edit">' +
                    '<i class="fas fa-pencil"></i>' +
                    '</button>' +
                    '</div>' +
                    '</td>' +
                    '<td>' + (item.customer ? item.customer.nama_customer : '-') + '</td>' +
                    '<td class="text-center">' + (item.jumlah_isi || 0) + '</td>' +
                    '<td class="text-center">' + (item.jumlah_kosong || 0) + '</td>' +
                    '<td class="text-center">' + (item.pinjam_tabung || 0) + '</td>' +
                    '<td class="text-center">' + formatTanggal(item.tanggal_masuk) + '</td>' +
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
                    '<li class="page-item"><a class="page-link" href="#" onclick="ambilDataTransaksi(1)">First</a></li>';
                htmlPaginasi += '<li class="page-item"><a class="page-link" href="#" onclick="ambilDataTransaksi(' + (
                    halamanSekarang - 1) + ')">Previous</a></li>';
            }

            // Nomor halaman
            for (var i = 1; i <= totalHalaman; i++) {
                htmlPaginasi += '<li class="page-item ' + (i == halamanSekarang ? 'active' : '') + '">';
                htmlPaginasi += '<a class="page-link" href="#" onclick="ambilDataTransaksi(' + i + ')">' + i + '</a>';
                htmlPaginasi += '</li>';
            }

            // Tombol Next & Last
            if (halamanSekarang < totalHalaman) {
                htmlPaginasi += '<li class="page-item"><a class="page-link" href="#" onclick="ambilDataTransaksi(' + (
                    halamanSekarang + 1) + ')">Next</a></li>';
                htmlPaginasi += '<li class="page-item"><a class="page-link" href="#" onclick="ambilDataTransaksi(' +
                    totalHalaman + ')">Last</a></li>';
            }

            htmlPaginasi += '</ul>';
            $('#divPagination').html(htmlPaginasi);
        }

        // ==================== FUNGSI ISI DROPDOWN ====================
        function isiDropdownBarang() {
            var dropdown = $('#id_barang');
            var nilaiSebelumnya = dropdown.val();

            dropdown.empty();
            dropdown.append('<option value="">-- Pilih Nama Barang --</option>');
            daftarBarang = [];

            $.ajax({
                url: '/transaksi-keluar/barang-list',
                type: 'GET',
                success: function(response) {
                    var dataBarang = Array.isArray(response) ? response : response.data || [];

                    if (dataBarang.length > 0) {
                        var barangUnik = {};

                        dataBarang.forEach(function(item) {
                            if (!barangUnik[item.id_barang]) {
                                barangUnik[item.id_barang] = true;

                                daftarBarang.push({
                                    id_barang: item.id_barang,
                                    kode_barang: item.kode_barang,
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
                },
                error: function() {
                    dropdown.append('<option value="" disabled>Gagal mengambil data barang</option>');
                }
            });
        }

        function isiDropdownCustomer() {
            var dropdown = $('#id_customer');
            var nilaiSebelumnya = dropdown.val();

            dropdown.empty();
            dropdown.append('<option value="">-- Pilih Customer --</option>');
            daftarCustomer = [];

            $.ajax({
                url: '/transaksi-keluar/customer-list',
                type: 'GET',
                success: function(response) {
                    var dataCustomer = Array.isArray(response) ? response : response.data || [];

                    if (dataCustomer.length > 0) {
                        var customerUnik = {};

                        dataCustomer.forEach(function(item) {
                            if (!customerUnik[item.id_customer]) {
                                customerUnik[item.id_customer] = true;

                                daftarCustomer.push({
                                    id_customer: item.id_customer,
                                    kode_customer: item.kode_customer,
                                    nama_customer: item.nama_customer
                                });

                                dropdown.append('<option value="' + item.id_customer + '">' + item
                                    .nama_customer + '</option>');
                            }
                        });
                    }

                    if (dropdown.children('option').length === 1) {
                        dropdown.append('<option value="" disabled>Customer tidak tersedia</option>');
                    }

                    if (nilaiSebelumnya) {
                        dropdown.val(nilaiSebelumnya);
                    }
                },
                error: function() {
                    dropdown.append('<option value="" disabled>Gagal mengambil data customer</option>');
                }
            });
        }

        // ==================== FUNGSI UPDATE DROPDOWN ====================
        function updateKodeCustomer() {
            var idTerpilih = $('#id_customer').val();

            if (idTerpilih && daftarCustomer.length > 0) {
                var customerTerpilih = daftarCustomer.find(item => item.id_customer == idTerpilih);

                if (customerTerpilih) {
                    $('#kode_customer').val(customerTerpilih.kode_customer || '');
                }
            } else {
                $('#kode_customer').val('');
            }
        }

        function updateKodeBarang() {
            var idTerpilih = $('#id_barang').val();

            if (idTerpilih && daftarBarang.length > 0) {
                var barangTerpilih = daftarBarang.find(item => item.id_barang == idTerpilih);

                if (barangTerpilih) {
                    $('#kode_barang').val(barangTerpilih.kode_barang || '');
                    $('#harga_satuan').val(barangTerpilih.harga_jual || '');
                }
            } else {
                $('#kode_barang').val('');
                $('#harga_satuan').val('');
            }
        }

        // ==================== FUNGSI AMBIL DATA BY ID ====================
        function ambilDataById(id) {
            $.ajax({
                url: '/transaksi-masuk/get-data-by-id/' + id,
                type: 'GET',
                success: function(response) {
                    var data = Array.isArray(response.data) ? response.data[0] : response.data;

                    // Bersihkan tabel
                    $('#tableItemList').html('');

                    // Set ID tersembunyi
                    $('#id_masuk').val(data.id_masuk || '');

                    // Set Customer
                    if (data.customer && data.customer.id_customer) {
                        if ($('#id_customer option[value="' + data.customer.id_customer + '"]').length === 0) {
                            $('#id_customer').append(new Option(
                                data.customer.nama_customer,
                                data.customer.id_customer,
                                false,
                                false
                            ));
                        }
                        $('#id_customer').val(data.customer.id_customer);
                        $('#kode_customer').val(data.customer.kode_customer || '');
                    }

                    // Set Barang
                    if (data.barang && data.barang.id_barang) {
                        if ($('#id_barang option[value="' + data.barang.id_barang + '"]').length === 0) {
                            $('#id_barang').append(new Option(
                                data.barang.nama_barang,
                                data.barang.id_barang,
                                false,
                                false
                            ));
                        }
                        $('#id_barang').val(data.barang.id_barang);
                        $('#kode_barang').val(data.barang.kode_barang || '');
                        $('#harga_satuan').val(data.barang.harga_jual || '');
                    }

                    // Set Tanggal
                    if (data.tanggal_masuk) {
                        $('#tanggal_masuk').val(formatTanggal(data.tanggal_masuk));
                    }

                    // Set Informasi Tabung
                    $('#jumlah_isi').val(data.jumlah_isi || 0);
                    $('#jumlah_kosong').val(data.jumlah_kosong || 0);
                    $('#pinjam_tabung').val(data.pinjam_tabung || 0);
                    $('#keterangan').val(data.keterangan || '');

                    // Tambah ke tabel
                    tambahBarisKeTable({
                        namabarang: data.barang ? data.barang.nama_barang : '-',
                        namacustomer: data.customer ? data.customer.nama_customer : '-',
                        jumlahisi: data.jumlah_isi || 0,
                        jumlahkosong: data.jumlah_kosong || 0,
                        pinjamtabung: data.pinjam_tabung || 0,
                        keterangan: data.keterangan || '-',
                        mode: 'view'
                    });
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

        // ==================== FUNGSI TAMBAH ITEM ====================
        function tambahItemKeTable() {
            var namaBarang = $('#id_barang option:selected').text();
            var namaCustomer = $('#id_customer option:selected').text();
            var jumlahIsi = parseInt($('#jumlah_isi').val()) || 0;
            var jumlahKosong = parseInt($('#jumlah_kosong').val()) || 0;
            var pinjamTabung = parseInt($('#pinjam_tabung').val()) || 0;
            var keterangan = $('#keterangan').val() || '-';

            // Validasi
            if (!$('#id_barang').val() || !$('#id_customer').val() || namaCustomer === '-- Pilih Customer --') {
                Swal.fire({
                    title: 'Peringatan',
                    text: 'Mohon pilih barang dan customer yang valid',
                    icon: 'warning'
                });
                return;
            }

            // Hapus placeholder jika ada
            if ($('#tableItemList tr td').attr('colspan') == '7') {
                $('#tableItemList').html('');
            }

            // Tambah baris baru
            tambahBarisKeTable({
                namabarang: namaBarang,
                namacustomer: namaCustomer,
                jumlahisi: jumlahIsi,
                jumlahkosong: jumlahKosong,
                pinjamtabung: pinjamTabung,
                keterangan: keterangan,
                mode: 'edit'
            });
        }

        function tambahBarisKeTable(item) {
            // Hapus placeholder jika ada
            if ($('#tableItemList tr td').attr('colspan') == '7') {
                $('#tableItemList').html('');
            }

            var tombolAksi = item.mode === 'view' ? '' :
                `<button type="button" class="btn btn-danger btn-sm" onclick="hapusItem(this)">
                    <i class="fas fa-trash"></i>
                </button>`;

            var barisBaru = `
                <tr>
                    <td>${item.namabarang}</td>
                    <td>${item.namacustomer}</td>
                    <td class="text-center">${item.jumlahisi}</td>
                    <td class="text-center">${item.jumlahkosong}</td>
                    <td class="text-center">${item.pinjamtabung}</td>
                    <td>${item.keterangan}</td>
                    <td class="text-center">${tombolAksi}</td>
                </tr>
            `;

            $('#tableItemList').append(barisBaru);
        }

        // ==================== FUNGSI HAPUS ITEM ====================
        function hapusItem(tombol) {
            $(tombol).closest('tr').remove();

            // Jika tidak ada item, tampilkan placeholder
            if ($('#tableItemList tr').length === 0) {
                resetTabelItem();
            }
        }

        function resetTabelItem() {
            $('#tableItemList').html(
                '<tr><td colspan="7" class="text-center">-- Belum ada item ditambahkan --</td></tr>'
            );
        }

        // ==================== FUNGSI SIMPAN DATA ====================
        function simpanData() {
            var idMasuk = $('#id_masuk').val();
            var urlAjax = '';
            var tipeAjax = '';
            var pesanSukses = '';

            // Validasi minimal 1 item
            // if ($('#tableItemList tr td[colspan]').length > 0 || $('#tableItemList tr').length === 0) {
            //     Swal.fire({
            //         target: document.getElementById('Modalbody'),
            //         title: 'Peringatan',
            //         text: 'Mohon tambahkan minimal 1 item transaksi',
            //         icon: 'warning'
            //     });
            //     return;
            // }

            // Kumpulkan semua item dari tabel
            var daftarItem = [];
            $('#tableItemList tr').each(function() {
                var kolom = $(this).find('td');

                if (kolom.length >= 6 && !kolom.eq(0).attr('colspan')) {
                    var idBarang = cariIdBarangDariNama(kolom.eq(0).text());
                    var idCustomer = cariIdCustomerDariNama(kolom.eq(1).text());

                    if (!idBarang || !idCustomer) {
                        return true; // lanjut ke iterasi berikutnya
                    }

                    daftarItem.push({
                        id_barang: parseInt(idBarang),
                        id_customer: idCustomer,
                        jumlah_isi: parseInt(kolom.eq(2).text()) || 0,
                        jumlah_kosong: parseInt(kolom.eq(3).text()) || 0,
                        pinjam_tabung: parseInt(kolom.eq(4).text()) || 0,
                        keterangan: kolom.eq(5).text() || '',
                        tanggal_masuk: formatTanggalDatabase($('#tanggal_masuk').val() || new Date())
                    });
                }
            });

            // Jika tidak ada item dari tabel, ambil dari form
            if (daftarItem.length === 0) {
                var idBarang = parseInt($('#id_barang').val());
                var idCustomer = $('#id_customer').val();

                if (!idBarang || !idCustomer) {
                    Swal.fire({
                        target: document.getElementById('Modalbody'),
                        title: 'Peringatan',
                        text: 'Mohon pilih barang dan customer',
                        icon: 'warning'
                    });
                    return;
                }

                daftarItem.push({
                    id_barang: idBarang,
                    id_customer: idCustomer,
                    jumlah_isi: parseInt($('#jumlah_isi').val()) || 0,
                    jumlah_kosong: parseInt($('#jumlah_kosong').val()) || 0,
                    pinjam_tabung: parseInt($('#pinjam_tabung').val()) || 0,
                    keterangan: $('#keterangan').val() || '',
                    tanggal_masuk: formatTanggalDatabase($('#tanggal_masuk').val() || new Date())
                });
            }

            // Setup AJAX
            var dataKirim;
            if (idMasuk) {
                // Mode UPDATE
                urlAjax = '/transaksi-masuk/update-data/' + idMasuk;
                tipeAjax = 'PATCH';
                pesanSukses = 'Data transaksi masuk berhasil diupdate.';
                dataKirim = {
                    id_barang: parseInt($('#id_barang').val()),
                    id_customer: $('#id_customer').val(),
                    jumlah_isi: parseInt($('#jumlah_isi').val()) || 0,
                    jumlah_kosong: parseInt($('#jumlah_kosong').val()) || 0,
                    pinjam_tabung: parseInt($('#pinjam_tabung').val()) || 0,
                    keterangan: $('#keterangan').val() || '',
                    tanggal_masuk: formatTanggalDatabase($('#tanggal_masuk').val() || new Date())
                };
            } else {
                // Mode INSERT
                urlAjax = '/transaksi-masuk/insert-data';
                tipeAjax = 'POST';
                pesanSukses = 'Data transaksi masuk berhasil disimpan.';
                dataKirim = {
                    data: daftarItem
                };
            }

            // Kirim data
            $.ajax({
                url: urlAjax,
                type: tipeAjax,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                contentType: 'application/json',
                data: JSON.stringify(dataKirim),
                success: function(response) {
                    Swal.fire({
                        title: 'Sukses',
                        text: pesanSukses,
                        icon: 'success'
                    });
                    $('#Modalbody').modal('hide');
                    $('#form-data').trigger('reset');
                    resetTabelItem();
                    ambilDataTransaksi();
                },
                error: function(xhr, status, error) {
                    var pesanError = 'Terjadi kesalahan';

                    if (xhr.status === 422 && xhr.responseJSON?.errors) {
                        var errors = xhr.responseJSON.errors;
                        var errorPertama = Object.values(errors)[0];
                        pesanError = Array.isArray(errorPertama) ? errorPertama[0] : errorPertama;
                    } else if (xhr.responseJSON?.message) {
                        pesanError = xhr.responseJSON.message;
                    } else {
                        pesanError = error || 'Unknown error';
                    }

                    Swal.fire({
                        target: document.getElementById('Modalbody'),
                        title: 'Kesalahan',
                        text: pesanError,
                        icon: 'error',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    });
                },
                complete: function() {
                    $('#btn-save').prop('disabled', false).html('Simpan');
                }
            });
        }

        // ==================== FUNGSI HELPER PENCARIAN ====================
        function cariIdBarangDariNama(namaBarang) {
            var barang = daftarBarang.find(item => item.nama_barang === namaBarang.trim());
            return barang ? barang.id_barang : null;
        }

        function cariIdCustomerDariNama(namaCustomer) {
            var customer = daftarCustomer.find(item => item.nama_customer === namaCustomer.trim());
            return customer ? customer.id_customer : null;
        }
    </script>
@endpush
