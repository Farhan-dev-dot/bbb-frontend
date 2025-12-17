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
        var modeDraft = 0; // 0 = update, 1 = tambah baru
        var dataTransaksi = [];
        var daftarSemuaBarang = [];

        // ============================================
        // INISIALISASI SAAT HALAMAN DIMUAT
        // ============================================
        $(document).ready(function() {
            // Event: Tambah data baru
            $('#btn-tambah').on('click', function() {
                tambahDataBaru();
            });

            // Event: Simpan data
            $(document).on('click', '#btn-save', function(e) {
                e.preventDefault();
                simpanData();
            });

            // Event: Pilih barang
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

            // Panggil fungsi ambil data saat halaman pertama kali dimuat
            ambilDataTransaksi();
        });
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

        // Event perubahan barang
        $('#id_barang').on('change', function() {
            updateKodeBarang();
        });

        // Event untuk input angka saja pada telepon
        $('#no_telfon').on('input', function() {
            this.value = this.value.replace(/[^0-9\-\+\(\)\s]/g, '');
        });

        // Event untuk validasi email realtime (disabled)
        // $('#email').on('blur', function() {
        //     validateEmail($(this).val());
        // });

        // Auto update summary saat input berubah
        $('#jumlah_isi, #jumlah_kosong').on('input', function() {
            updateSummary();
        });
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

        // ==================== FUNGSI TAMBAH DATA ====================
        function bukaFormTambahData() {
            // Reset form dan variabel
            $('#form-data').trigger('reset');
            itemsTransaksi = [];
            resetTabelItem();

            // Aktifkan semua input
            $('#form-data input, #form-data select, #btnTambahItem, #btn-save').prop('disabled', false);

            $('#id_masuk').val('');
            modeDraft = 1;
            $('#Modal-title').html('Tambah Data Transaksi Masuk');

            // Set tanggal hari ini
            const today = new Date().toISOString().split('T')[0];
            $('#tanggal_masuk').val(today);

            // Reset nilai default
            $('#jumlah_isi, #jumlah_kosong').val(0);

            // Update summary
            updateSummary();

            $('#Modalbody').modal('show');
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
                tbody.append('<tr><td colspan="8" class="text-center">Tidak ada data ditemukan</td></tr>');
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
                    '<td>' + (item.customer.nama_customer || '-') + '</td>' +
                    '<td>' + (item.barang.nama_barang || '-') + '</td>' +
                    '<td class="text-center">' + (item.jumlah_isi || 0) + '</td>' +
                    '<td class="text-center">' + (item.jumlah_kosong || 0) + '</td>' +
                    '<td class="text-center">' + formatTanggal(item.tanggal_masuk) + '</td>' +
                    '</tr>';
                tbody.append(baris);
            });
        }

        // ==================== FUNGSI PAGINASI ====================
        function tampilkanPaginasi(data) {
            var htmlPaginasi = '<ul class="pagination">';
            var halamanSekarang = data.currentPage || data.current_page || 1;
            var totalHalaman = data.totalPage || data.total_page || 1;

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

        // ==================== FUNGSI UPDATE DROPDOWN ====================
        function updateKodeBarang() {
            var idTerpilih = $('#id_barang').val();

            if (idTerpilih && daftarBarang.length > 0) {
                var barangTerpilih = daftarBarang.find(item => item.id_barang == idTerpilih);

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
                    resetTabelItem();
                    itemsTransaksi = [];

                    // Set ID tersembunyi
                    $('#id_masuk').val(data.id_masuk || '');
                    $('#id_customer').val(data.id_customer || '');

                    // Set data supplier
                    $('#nama_customer').val(data.customer.nama_customer || '');
                    $('#email').val(data.customer.email || '');
                    $('#no_telfon').val(data.customer.telepon || '');
                    $('#alamat').val(data.customer.alamat || '');

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
                    } else if (data.id_barang) {
                        $('#id_barang').val(data.id_barang);
                    }

                    // Set Tanggal
                    if (data.tanggal_masuk) {
                        $('#tanggal_masuk').val(formatTanggalDatabase(data.tanggal_masuk));
                    }

                    // Set Informasi Tabung
                    $('#jumlah_isi').val(data.jumlah_isi || 0);
                    $('#jumlah_kosong').val(data.jumlah_kosong || 0);
                    $('#keterangan').val(data.keterangan || '');

                    // Tambah ke array items
                    itemsTransaksi.push({
                        id_barang: data.id_barang || data.barang?.id_barang,
                        nama_barang: data.nama_barang || data.barang?.nama_barang,
                        nama_customer: data.customer.nama_customer,
                        email: data.customer.email,
                        telepon: data.customer.telepon,
                        alamat: data.customer.alamat,
                        jumlah_isi: data.jumlah_isi || 0,
                        jumlah_kosong: data.jumlah_kosong || 0,
                        keterangan: data.keterangan,
                        tanggal_masuk: data.tanggal_masuk
                    });

                    // Render tabel dan update summary
                    renderTabelItem();
                    updateSummary();
                },
                error: function(xhr) {
                    console.error('Error fetching data by ID:', xhr);
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
            if (!validateForm()) {
                return;
            }

            const formData = getFormData();

            // Cek duplikasi berdasarkan barang dan supplier
            const existing = itemsTransaksi.find(item =>
                item.id_barang === formData.id_barang &&
                item.nama_customer === formData.nama_customer
            );

            if (existing) {
                Swal.fire({
                    title: 'Item Sudah Ada',
                    text: 'Item dengan barang dan supplier yang sama sudah ditambahkan. Apakah ingin menggabungkan?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Gabungkan',
                    cancelButtonText: 'Batal',
                    target: document.getElementById('Modalbody')
                }).then((result) => {
                    if (result.isConfirmed) {
                        existing.jumlah_isi += formData.jumlah_isi;
                        existing.jumlah_kosong += formData.jumlah_kosong;
                        existing.keterangan = `${existing.keterangan} | ${formData.keterangan}`;
                        renderTabelItem();
                        clearItemForm();
                        updateSummary();
                    }
                });
            } else {
                itemsTransaksi.push(formData);
                renderTabelItem();
                clearItemForm();
                updateSummary();

                Swal.fire({
                    icon: 'success',
                    title: 'Item Ditambahkan',
                    text: 'Item berhasil ditambahkan ke daftar transaksi',
                    timer: 1500,
                    showConfirmButton: false,
                    target: document.getElementById('Modalbody')
                });
            }
        }

        function getFormData() {
            const namaBarang = $('#id_barang option:selected').text();

            return {
                id_barang: parseInt($('#id_barang').val()),
                nama_barang: namaBarang,
                nama_customer: $('#nama_customer').val().trim(),
                alamat: $('#alamat').val().trim(),
                email: $('#email').val().trim(),
                no_telfon: $('#no_telfon').val().trim(),
                jumlah_isi: parseInt($('#jumlah_isi').val()) || 0,
                jumlah_kosong: parseInt($('#jumlah_kosong').val()) || 0,
                keterangan: $('#keterangan').val().trim(),
                tanggal_masuk: $('#tanggal_masuk').val()
            };
        }

        function clearItemForm() {
            $('#id_barang').val('');
            $('#jumlah_isi').val(0);
            $('#jumlah_kosong').val(0);
            $('#keterangan').val('');
        }

        function renderTabelItem() {
            const tbody = $('#tableItemList');

            if (itemsTransaksi.length === 0) {
                resetTabelItem();
                return;
            }

            let html = '';
            itemsTransaksi.forEach((item, index) => {
                console.log(item);
                html += `
                    <tr>
                        <td>${item.nama_barang}</td>
                        <td>${item.nama_customer}</td>
                        <td>${item.email}</td>
                        <td>${item.telepon}</td>
                        <td class="text-center">${item.jumlah_isi}</td>
                        <td class="text-center">${item.jumlah_kosong}</td>
                        <td class="text-center">${formatTanggal(item.tanggal_masuk)}</td>
                        <td class="text-center">
                            <button type="button" class="btn btn-danger btn-sm" onclick="hapusItem(${index})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
            tbody.html(html);
        }

        // ==================== FUNGSI HAPUS ITEM ====================
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
                    updateSummary();

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

        function resetTabelItem() {
            $('#tableItemList').html(
                '<tr><td colspan="8" class="text-center">-- Belum ada item ditambahkan --</td></tr>'
            );
        }

        // ==================== FUNGSI UPDATE SUMMARY ====================
        function updateSummary() {
            const totalIsi = itemsTransaksi.reduce((sum, item) => sum + (item.jumlah_isi || 0), 0);
            const totalKosong = itemsTransaksi.reduce((sum, item) => sum + (item.jumlah_kosong || 0), 0);
            const totalItem = itemsTransaksi.length;

            $('#totalTabungIsi').text(totalIsi);
            $('#totalTabungKosong').text(totalKosong);
            $('#totalItem').text(totalItem);
        }

        // ==================== FUNGSI SIMPAN DATA ====================
        function simpanData() {
            if (itemsTransaksi.length === 0) {
                Swal.fire({
                    title: 'Peringatan',
                    text: 'Belum ada item yang ditambahkan',
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

        function prosesSimapanData() {
            const idMasuk = $('#id_masuk').val();
            let urlAjax = '';
            let tipeAjax = '';
            let pesanSukses = '';
            let dataKirim = {};

            // Disable tombol save
            $('#btn-save').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');

            if (idMasuk) {
                // Mode UPDATE
                urlAjax = '/transaksi-masuk/update-data/' + idMasuk;
                tipeAjax = 'PATCH';
                pesanSukses = 'Data transaksi masuk berhasil diupdate.';
                dataKirim = itemsTransaksi[0]; // Untuk update, ambil item pertama
            } else {
                // Mode INSERT
                urlAjax = '/transaksi-masuk/insert-data';
                tipeAjax = 'POST';
                pesanSukses = 'Data transaksi masuk berhasil disimpan.';
                dataKirim = {
                    data: itemsTransaksi
                };
            }

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
                    }).then(() => {
                        $('#Modalbody').modal('hide');
                        $('#form-data').trigger('reset');
                        itemsTransaksi = [];
                        resetTabelItem();
                        updateSummary();
                        ambilDataTransaksi();
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error saving data:', xhr);

                    var pesanError = 'Terjadi kesalahan';

                    if (xhr.status === 422 && xhr.responseJSON?.errors) {
                        var errors = xhr.responseJSON.errors;
                        var errorPertama = Object.values(errors)[0];
                        pesanError = Array.isArray(errorPertama) ? errorPertama[0] : errorPertama;
                    } else if (xhr.responseJSON?.message) {
                        pesanError = xhr.responseJSON.message;
                    } else if (error) {
                        pesanError = error;
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
                    $('#btn-save').prop('disabled', false).html(
                        '<i class="fas fa-save me-1"></i>Simpan Transaksi');
                }
            });
        }
    </script>
@endpush
