@extends('layouts.master')

@section('title', 'Transaksi Keluar')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('/') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Transaksi Keluar</li>
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
                                <label for="nama_pengirim_filter" class="form-label mb-1">Nama Pengirim</label>
                                <input type="text" class="form-control" id="nama_pengirim_filter"
                                    placeholder="Nama Pengirim" />
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
                <div class="card-header">
                    <button type="button" id="tombol-tambah" class="btn btn-md" data-bs-toggle="modal"
                        data-bs-target="#Modalbody" style="background: #F07124; color: white;">
                        <i class="fas fa-exchange-alt"></i>
                        Tambah Data</button>
                </div>
                <div class="card-body">
                    <table class="table table-bordered" id="tabel-data">
                        <thead>
                            <tr>
                                <th>Aksi</th>
                                <th>Nama Pengirim</th>
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
                <div class="card-footer clearfix">
                    <div id="containerPagination" class="float-end"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@include('modal.transaksikeluar')

@push('scripts')
    <script>
        // ============================================
        // VARIABEL GLOBAL
        // ============================================
        var modeDraft = 0; // 0 = tidak draft, 1 = draft baru
        var daftarTransaksi = []; // Menyimpan semua data transaksi
        var daftarSemuaBarang = []; // Menyimpan semua data barang

        // ============================================
        // INISIALISASI SAAT HALAMAN DIMUAT
        // ============================================
        $(document).ready(function() {
            // Event: Simpan data
            $(document).on('click', '#btn-save', function(e) {
                e.preventDefault();
                simpanData();
            });

            // Event: Ketika memilih barang dari dropdown
            $('#id_barang').on('change', function() {
                var idBarangTerpilih = $(this).val();
                if (idBarangTerpilih && daftarSemuaBarang.length > 0) {
                    var barangTerpilih = daftarSemuaBarang.find(item => item.id_barang == idBarangTerpilih);
                    if (barangTerpilih) {
                        $('#harga_satuan').val(formatRupiah(barangTerpilih.harga_jual || 0));
                    }
                } else {
                    $('#harga_satuan').val('Rp 0');
                }
            });

            // Event: Tambah data baru
            $('#tombol-tambah').on('click', function() {
                tambahDataBaru();
            });

            // Event: Tambah item ke tabel
            $('#btnTambahItem').on('click', function() {
                tambahItemKeTable();
            });

            // Event: Filter data
            $('#tombol-filter').on('click', function() {
                ambilDataTransaksi(1);
            });

            // Event: Bersihkan filter
            $('#tombol-bersihkan').on('click', function() {
                $('#tanggal_dari').val('');
                $('#tanggal_sampai').val('');
                $('#nama_pengirim_filter').val('');
                ambilDataTransaksi(1);
            });

            // Panggil fungsi ambil data saat halaman pertama kali dimuat
            ambilDataTransaksi();
        });

        // ============================================
        // FUNGSI: AMBIL DATA TRANSAKSI DENGAN FILTER
        // ============================================
        function ambilDataTransaksi(halaman = 1) {
            $.ajax({
                url: '/transaksi-keluar/getdata',
                type: 'GET',
                data: {
                    page: halaman,
                    tanggal_from: $('#tanggal_dari').val(),
                    tanggal_to: $('#tanggal_sampai').val(),
                    pengirim: $('#nama_pengirim_filter').val(),
                    keyword: $('#keyword').val(),
                },
                success: function(respons) {
                    var dataTransaksiKeluar = respons.pengiriman || [];
                    daftarTransaksi = dataTransaksiKeluar;

                    tampilkanDataKeTable(dataTransaksiKeluar);
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
        // FUNGSI: TAMPILKAN DATA KE TABLE
        // ============================================
        function tampilkanDataKeTable(dataTransaksiKeluar) {
            var isiTabel = $('#tabel-data tbody');
            isiTabel.empty();

            if (!dataTransaksiKeluar || dataTransaksiKeluar.length === 0) {
                isiTabel.append('<tr><td colspan="6" class="text-center">Tidak ada data ditemukan</td></tr>');
                return;
            }

            $.each(dataTransaksiKeluar, function(indeks, itemTransaksi) {
                var barisTabel =
                    '<tr>' +
                    '<td class="text-center">' +
                    '<div class="btn-group btn-group-sm" role="group">' +
                    '<button class="btn btn-info" onclick="lihatDetailData(\'' + itemTransaksi.id_keluar +
                    '\'); event.stopPropagation();" title="Lihat Detail">' +
                    '<i class="fas fa-magnifying-glass"></i>' +
                    '</button>' +
                    '<button class="btn btn-warning" onclick="editDataTransaksi(\'' + itemTransaksi.id_keluar +
                    '\'); event.stopPropagation();" title="Edit">' +
                    '<i class="fas fa-pencil"></i>' +
                    '</button>' +
                    '<button class="btn btn-danger btn-sm" onclick="hapusData(\'' + itemTransaksi.id_keluar +
                    '\'); event.stopPropagation();" title="Hapus"><i class="fas fa-solid fa-trash"></i></button>' +
                    // Diperbaiki dari 'item' ke 'itemTransaksi'
                    '</div>' +
                    '</td>' +
                    '<td>' + (itemTransaksi.nama_pengirim || '-') + '</td>' +
                    '<td class="text-center">' + (itemTransaksi.jumlah_isi || 0) + '</td>' +
                    '<td class="text-center">' + (itemTransaksi.jumlah_kosong || 0) + '</td>' +
                    '<td class="text-center">' + (itemTransaksi.pinjam_tabung || 0) + '</td>' +
                    '<td class="text-center">' + formatTanggal(itemTransaksi.tanggal_keluar) + '</td>' +
                    '</tr>';
                isiTabel.append(barisTabel);
            });
        }

        // ==================== FUNGSI HAPUS DATA - DIPERBAIKI ====================
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
                        url: '/transaksi-keluar/delete-data/' + id,
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: response.message || 'Data berhasil dihapus',
                                icon: 'success'
                            }).then(function() {
                                ambilDataTransaksi
                                    (); // Diperbaiki dari 'ambilDataBarang()' ke 'ambilDataTransaksi()'
                            });
                        },
                        error: function(xhr) {
                            console.error('Error deleting data:', xhr.responseJSON); // Debug log
                            Swal.fire({
                                title: 'Kesalahan',
                                text: xhr.responseJSON?.message ||
                                    'Terjadi kesalahan saat menghapus data',
                                icon: 'error'
                            });
                        }
                    });
                }
            });
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
                                    nama_barang: itemBarang.nama_barang,
                                    harga_jual: itemBarang.harga_jual,
                                    stok_tabung_isi: itemBarang.stok_tabung_isi || 0,
                                    stok_tabung_kosong: itemBarang.stok_tabung_kosong || 0
                                });
                                selectBarang.append('<option value="' + itemBarang.id_barang + '">' +
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
        // FUNGSI: TAMBAH DATA BARU
        // ============================================
        function tambahDataBaru() {
            // Aktifkan semua input dan tombol
            $('#form-data input, #form-data select, #btnTambahItem, #btn-save').prop('disabled', false);

            if (modeDraft == 0) {
                $('#form-data').trigger('reset');
                $('#tableItemList').html(
                    '<tr><td colspan="10" class="text-center">-- Belum ada item ditambahkan --</td></tr>');
                $('#grandTotal').text('Rp 0');
            }
            $('#id_keluar').val('');
            modeDraft = 1;
            $('#Modal-title').html('Tambah Data Transaksi Keluar');
            $('#Modalbody').modal('show');

            // Set tanggal hari ini
            $('#tanggal_keluar').val(formatTanggal(new Date()));

            // Reset nilai default
            $('#jumlah_isi, #jumlah_kosong, #pinjam_tabung').val(0);

            // PENTING: Reload data barang untuk memastikan stok terbaru
            isiDropdownBarang();
        }

        // ============================================
        // FUNGSI: TAMBAH ITEM KE TABLE
        // ============================================
        function tambahItemKeTable() {
            // Validasi form
            if (!$('#id_barang').val() || !$('#nama_customer').val() || !$('#nama_pengirim').val()) {
                Swal.fire({
                    title: 'Peringatan',
                    text: 'Mohon lengkapi data barang, customer, dan nama pengirim',
                    icon: 'warning'
                });
                return;
            }

            // Ambil data dari form
            var namaBarangDipilih = $('#id_barang option:selected').text();
            var namaCustomerInput = $('#nama_customer').val();
            var namaPengirimInput = $('#nama_pengirim').val();
            var jumlahTabungIsi = parseInt($('#jumlah_isi').val()) || 0;
            var jumlahTabungKosong = parseInt($('#jumlah_kosong').val()) || 0;
            var jumlahPinjamTabung = parseInt($('#pinjam_tabung').val()) || 0;
            var hargaSatuanBarang = parseInt($('#harga_satuan').val().replace(/[^0-9]/g, '')) || 0;
            var totalHargaItem = jumlahTabungIsi * hargaSatuanBarang;
            var keteranganItem = $('#keterangan').val() || '-';

            // Hapus placeholder row jika ada
            if ($('#tableItemList tr td').attr('colspan') == '10') {
                $('#tableItemList').html('');
            }

            // Tambahkan baris ke tabel
            var barisBaruItem = `
        <tr>
            <td>${namaBarangDipilih}</td>
            <td>${namaCustomerInput}</td>
            <td>${namaPengirimInput}</td>
            <td class="text-center">${jumlahTabungIsi}</td>
            <td class="text-center">${jumlahTabungKosong}</td>
            <td class="text-center">${jumlahPinjamTabung}</td>
            <td class="text-end">${formatRupiah(hargaSatuanBarang)}</td>
            <td class="text-end">${formatRupiah(totalHargaItem)}</td>
            <td>${keteranganItem}</td>
            <td class="text-center">
                <button type="button" class="btn btn-danger btn-sm" onclick="hapusItemDariTable(this)">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    `;
            $('#tableItemList').append(barisBaruItem);

            // Update Grand Total
            perbaruiTotalKeseluruhan();

            // Reset form setelah menambah item
            resetFormInput();
        }

        // ============================================
        // FUNGSI: RESET FORM INPUT (TIDAK SEMUA)
        // ============================================
        function resetFormInput() {
            $('#id_barang').val('');
            $('#harga_satuan').val('Rp 0');
            $('#jumlah_isi, #jumlah_kosong, #pinjam_tabung').val(0);
            $('#keterangan').val('');
        }

        // ============================================
        // FUNGSI: LIHAT DETAIL DATA
        // ============================================
        function lihatDetailData(idTransaksi) {
            modeDraft = 0;
            $('#Modal-title').html('Detail Data Transaksi Keluar');
            $('#Modalbody').modal('show');
            ambilDataBerdasarkanId(idTransaksi);

            // Disable semua input untuk mode view
            $('#form-data input, #form-data select, #btnTambahItem, #btn-save').prop('disabled', true);
        }

        // ============================================
        // FUNGSI: EDIT DATA TRANSAKSI
        // ============================================
        function editDataTransaksi(idTransaksi) {
            modeDraft = 0;
            $('#Modal-title').html('Edit Data Transaksi Keluar');
            $('#Modalbody').modal('show');
            ambilDataBerdasarkanId(idTransaksi);

            // Enable semua input untuk mode edit
            $('#form-data input:not([readonly]), #form-data select, #btn-save').prop('disabled', false);
            $('#btnTambahItem').prop('disabled', true);
        }

        // ============================================
        // FUNGSI: SIMPAN DATA (INSERT/UPDATE) - DIPERBAIKI
        // ============================================
        function simpanData() {
            var idTransaksiKeluar = $('#id_keluar').val();
            var idBarangDipilih = $('#id_barang').val();
            var tanggalTransaksi = $('#tanggal_keluar').val();
            var jenisTransaksi = 'penjualan'; // Diperbaiki dari 'keluar' ke 'penjualan'
            var metodePembayaran = $('#metode_pembayaran').val();
            var statusPembayaran = 'lunas';
            var namaPengirimInput = $('#nama_pengirim').val();
            var namaCustomerInput = $('#nama_customer').val();
            var teleponInput = $('#telepon').val();
            var emailInput = $('#email').val();
            var alamatInput = $('#alamat').val();
            var keteranganTransaksi = $('#keterangan').val();
            var jumlahTabungIsi = parseInt($('#jumlah_isi').val()) || 0;
            var jumlahTabungKosong = parseInt($('#jumlah_kosong').val()) || 0;
            var jumlahPinjamTabung = parseInt($('#pinjam_tabung').val()) || 0;
            var hargaSatuanBarang = parseInt($('#harga_satuan').val().replace(/[^0-9]/g, '')) || 0;

            // ========== MODE UPDATE ==========
            if (idTransaksiKeluar) {
                if (!idBarangDipilih || !tanggalTransaksi || !namaPengirimInput || !namaCustomerInput) {
                    Swal.fire({
                        title: 'Peringatan',
                        text: 'Mohon lengkapi data yang diperlukan',
                        icon: 'warning'
                    });
                    return;
                }

                // Konversi format tanggal dari DD/MM/YYYY ke YYYY-MM-DD
                if (tanggalTransaksi && tanggalTransaksi.includes('/')) {
                    var bagianTanggal = tanggalTransaksi.split('/');
                    tanggalTransaksi = bagianTanggal[2] + '-' + bagianTanggal[1] + '-' + bagianTanggal[0];
                }

                var dataUpdate = {
                    id_barang: parseInt(idBarangDipilih),
                    nama_customer: namaCustomerInput,
                    alamat: alamatInput,
                    email: emailInput,
                    telepon: teleponInput,
                    jumlah_isi: jumlahTabungIsi,
                    jumlah_kosong: jumlahTabungKosong,
                    pinjam_tabung: jumlahPinjamTabung,
                    harga_satuan: hargaSatuanBarang,
                    keterangan: keteranganTransaksi || '',
                    tanggal_transaksi: tanggalTransaksi,
                    nama_pengirim: namaPengirimInput,
                    metode_pembayaran: metodePembayaran,
                    alamat_pengiriman: alamatInput
                };

                $.ajax({
                    url: '/transaksi-keluar/update-data/' + idTransaksiKeluar,
                    type: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    contentType: 'application/json',
                    data: JSON.stringify(dataUpdate),
                    success: function(respons) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: respons.message || 'Data transaksi berhasil diupdate.',
                            icon: 'success'
                        });
                        $('#Modalbody').modal('hide');
                        resetFormulir();
                        ambilDataTransaksi();
                    },
                    error: function(xhr) {
                        tampilkanPesanKesalahan(xhr);
                    }
                });
                return;
            }

            // ========== MODE INSERT ==========
            // Validasi field wajib untuk mode INSERT
            if (!namaCustomerInput || !alamatInput || !emailInput || !teleponInput || !tanggalTransaksi) {
                Swal.fire({
                    title: 'Peringatan',
                    text: 'Mohon lengkapi semua data yang diperlukan (Nama Customer, Alamat, Email, Telepon, Tanggal Transaksi)',
                    icon: 'warning'
                });
                return;
            }

            var daftarItemTransaksi = [];
            $('#tableItemList tr').each(function() {
                var kolomTabel = $(this).find('td');
                if (kolomTabel.length >= 10 && kolomTabel.eq(0).text() !== '-- Belum ada item ditambahkan --') {
                    var idBarang = cariIdBarangDariNama(kolomTabel.eq(0).text());
                    var barangData = daftarSemuaBarang.find(b => b.id_barang == idBarang);

                    console.log('Processing item:', {
                        id_barang: idBarang,
                        nama_barang: kolomTabel.eq(0).text(),
                        barangData: barangData,
                        daftarSemuaBarang: daftarSemuaBarang
                    });

                    if (!barangData) {
                        console.error('Barang tidak ditemukan di cache untuk ID:', idBarang);
                        Swal.fire({
                            title: 'Error',
                            text: 'Data barang "' + kolomTabel.eq(0).text() +
                                '" tidak ditemukan. Silakan refresh halaman dan coba lagi.',
                            icon: 'error'
                        });
                        return false; // Stop execution
                    }

                    daftarItemTransaksi.push({
                        id_barang: idBarang,
                        jumlah_isi: parseInt(kolomTabel.eq(3).text()) || 0,
                        jumlah_kosong: parseInt(kolomTabel.eq(4).text()) || 0,
                        jumlah_pinjam_tabung: parseInt(kolomTabel.eq(5).text()) || 0,
                        harga_satuan: parseInt(kolomTabel.eq(6).text().replace(/[^0-9]/g, '')) || 0,
                        diskon: 0,
                        keterangan: kolomTabel.eq(8).text() || '',
                        stok_awal_isi: barangData.stok_tabung_isi,
                        stok_awal_kosong: barangData.stok_tabung_kosong
                    });
                }
            });

            if (daftarItemTransaksi.length === 0) {
                Swal.fire({
                    title: 'Peringatan',
                    text: 'Tambahkan minimal satu item transaksi!',
                    icon: 'warning'
                });
                return;
            }

            // Konversi format tanggal dari DD/MM/YYYY ke YYYY-MM-DD
            if (tanggalTransaksi && tanggalTransaksi.includes('/')) {
                var bagianTanggal = tanggalTransaksi.split('/');
                tanggalTransaksi = bagianTanggal[2] + '-' + bagianTanggal[1] + '-' + bagianTanggal[0];
            }

            var dataInsert = {
                nama_customer: namaCustomerInput,
                alamat: alamatInput,
                email: emailInput, // Diperbaiki dari 'email' ke 'email'
                telepon: teleponInput, // Diperbaiki dari 'telepon' ke 'telepon'
                tanggal_transaksi: tanggalTransaksi, // Diperbaiki dari 'tanggal_keluar'
                jenis_transaksi: jenisTransaksi, // Diperbaiki ke 'penjualan'
                metode_pembayaran: metodePembayaran,
                status_pembayaran: statusPembayaran,
                nama_pengirim: namaPengirimInput,
                alamat_pengiriman: alamatInput, // Tambahkan alamat_pengiriman
                biaya_pengiriman: 0, // Set default atau ambil dari form jika ada
                keterangan: keteranganTransaksi || '',
                items: daftarItemTransaksi
            };

            console.log('Data yang akan dikirim:', dataInsert); // Debug log

            $.ajax({
                url: '/transaksi-keluar/insert-data',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                contentType: 'application/json',
                data: JSON.stringify(dataInsert),
                success: function(respons) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: respons.message || 'Data transaksi berhasil disimpan.',
                        icon: 'success'
                    });
                    $('#Modalbody').modal('hide');
                    resetFormulir();
                    ambilDataTransaksi();
                },
                error: function(xhr) {
                    console.error('Error response:', xhr.responseJSON); // Debug log
                    tampilkanPesanKesalahan(xhr);
                }
            });
        }

        // ============================================
        // FUNGSI: TAMBAH DATA BARU - DIPERBAIKI
        // ============================================
        function tambahDataBaru() {
            // Aktifkan semua input dan tombol
            $('#form-data input, #form-data select, #btnTambahItem, #btn-save').prop('disabled', false);

            if (modeDraft == 0) {
                $('#form-data').trigger('reset');
                $('#tableItemList').html(
                    '<tr><td colspan="10" class="text-center">-- Belum ada item ditambahkan --</td></tr>');
                $('#grandTotal').text('Rp 0');
            }
            $('#id_keluar').val('');
            modeDraft = 1;
            $('#Modal-title').html('Tambah Data Transaksi Keluar');
            $('#Modalbody').modal('show');

            // Set tanggal hari ini dalam format YYYY-MM-DD untuk input date
            var today = new Date();
            var tanggalHariIni = today.getFullYear() + '-' +
                String(today.getMonth() + 1).padStart(2, '0') + '-' +
                String(today.getDate()).padStart(2, '0');

            // Jika input tanggal_keluar adalah type="date", gunakan format YYYY-MM-DD
            $('#tanggal_keluar').val(tanggalHariIni);
            // Jika input tanggal_keluar adalah type="text", gunakan format DD/MM/YYYY
            // $('#tanggal_keluar').val(formatTanggal(new Date()));

            // Reset nilai default
            $('#jumlah_isi, #jumlah_kosong, #pinjam_tabung').val(0);

            // Panggil dropdown barang
            isiDropdownBarang();
        }

        // ============================================
        // FUNGSI: CARI ID BARANG DARI NAMA
        // ============================================
        function cariIdBarangDariNama(namaBarang) {
            var barangDitemukan = daftarSemuaBarang.find(b => b.nama_barang === namaBarang);
            return barangDitemukan ? barangDitemukan.id_barang : null;
        }

        // ============================================
        // FUNGSI: AMBIL DATA BERDASARKAN ID - DIPERBAIKI
        // ============================================
        function ambilDataBerdasarkanId(idTransaksi) {
            $.ajax({
                url: '/transaksi-keluar/get-data-by-id/' + idTransaksi,
                type: 'GET',
                success: function(respons) {
                    var dataTransaksi = respons.data;

                    // Clear table
                    $('#tableItemList').html('');

                    // Set hidden field
                    $('#id_keluar').val(dataTransaksi.id_keluar || '');

                    // Set Customer Data dari object customer
                    if (dataTransaksi.customer) {
                        $('#nama_customer').val(dataTransaksi.customer.nama_customer || '');
                        $('#telepon').val(dataTransaksi.customer.telepon || '');
                        $('#email').val(dataTransaksi.customer.email || '');
                        $('#alamat').val(dataTransaksi.customer.alamat || '');
                    }

                    // Set Barang
                    if (dataTransaksi.barang) {

                        // Tambahkan option barang jika belum ada
                        if ($('#id_barang option[value="' + dataTransaksi.barang.id_barang + '"]').length ===
                            0) {
                            $('#id_barang').append(new Option(dataTransaksi.barang.nama_barang, dataTransaksi
                                .barang.id_barang, true, true));
                        }
                        $('#id_barang').val(dataTransaksi.barang.id_barang).trigger('change');

                        $('#harga_satuan').val(formatRupiah(dataTransaksi.harga_satuan || 0));
                    }

                    // Detail Transaksi dari object transaksi
                    $('#nama_pengirim').val(dataTransaksi.nama_pengirim || '');
                    // console.log('Data Transaksi:', dataTransaksi); // Debug log
                    if (dataTransaksi.transaksi) {
                        $('#metode_pembayaran').val(dataTransaksi.transaksi.metode_pembayaran || '').trigger(
                            'change');
                    } else {
                        // Fallback ke field langsung
                        $('#metode_pembayaran').val(dataTransaksi.metode_pembayaran || '').trigger('change');
                    }
                    $('#keterangan').val(dataTransaksi.keterangan || '');

                    // Tanggal Keluar - konversi format untuk display
                    if (dataTransaksi.tanggal_keluar) {
                        // Jika input adalah type="date", gunakan format YYYY-MM-DD
                        var tanggalInput = new Date(dataTransaksi.tanggal_keluar);
                        var formatTanggalInput = tanggalInput.getFullYear() + '-' +
                            String(tanggalInput.getMonth() + 1).padStart(2, '0') + '-' +
                            String(tanggalInput.getDate()).padStart(2, '0');
                        $('#tanggal_keluar').val(formatTanggalInput);
                    }

                    // Informasi Tabung
                    $('#jumlah_isi').val(dataTransaksi.jumlah_isi || 0);
                    $('#jumlah_kosong').val(dataTransaksi.jumlah_kosong || 0);
                    $('#pinjam_tabung').val(dataTransaksi.pinjam_tabung || 0);

                    // Total Harga
                    var totalHargaTransaksi = dataTransaksi.total_harga || 0;

                    // Tambahkan ke tabel
                    tambahkanKeTableItem({
                        namaBarang: dataTransaksi.barang ? dataTransaksi.barang.nama_barang : '-',
                        namaCustomer: dataTransaksi.customer ? dataTransaksi.customer.nama_customer :
                            '-',
                        namaPengirim: dataTransaksi.nama_pengirim || '-',
                        jumlahIsi: dataTransaksi.jumlah_isi || 0,
                        jumlahKosong: dataTransaksi.jumlah_kosong || 0,
                        pinjamTabung: dataTransaksi.pinjam_tabung || 0,
                        hargaSatuan: dataTransaksi.harga_satuan || 0,
                        totalHarga: totalHargaTransaksi,
                        keterangan: dataTransaksi.keterangan || '-',
                        mode: 'view'
                    });

                    $('#grandTotal').text(formatRupiah(totalHargaTransaksi));
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
        // FUNGSI: TAMBAHKAN KE TABLE ITEM
        // ============================================
        function tambahkanKeTableItem(itemData) {
            // Hapus placeholder jika ada
            if ($('#tableItemList tr td').attr('colspan') == '10') {
                $('#tableItemList').html('');
            }

            var tombolAksi = itemData.mode === 'view' ? '' :
                `<button type="button" class="btn btn-danger btn-sm" onclick="hapusItemDariTable(this)">
            <i class="fas fa-trash"></i>
        </button>`;

            var barisBaruItem = `
        <tr>
            <td>${itemData.namaBarang}</td>
            <td>${itemData.namaCustomer}</td>
            <td>${itemData.namaPengirim}</td>
            <td class="text-center">${itemData.jumlahIsi}</td>
            <td class="text-center">${itemData.jumlahKosong}</td>
            <td class="text-center">${itemData.pinjamTabung}</td>
            <td class="text-end">${formatRupiah(itemData.hargaSatuan)}</td>
            <td class="text-end">${formatRupiah(itemData.totalHarga)}</td>
            <td>${itemData.keterangan}</td>
            <td class="text-center">${tombolAksi}</td>
        </tr>
    `;

            $('#tableItemList').append(barisBaruItem);
        }

        // ============================================
        // FUNGSI: HAPUS ITEM DARI TABLE
        // ============================================
        function hapusItemDariTable(tombolHapus) {
            $(tombolHapus).closest('tr').remove();

            // Jika tidak ada item, tampilkan placeholder
            if ($('#tableItemList tr').length === 0) {
                $('#tableItemList').html(
                    '<tr><td colspan="10" class="text-center">-- Belum ada item ditambahkan --</td></tr>');
            }

            perbaruiTotalKeseluruhan();
        }

        // ============================================
        // FUNGSI: PERBARUI TOTAL KESELURUHAN
        // ============================================
        function perbaruiTotalKeseluruhan() {
            var totalKeseluruhan = 0;
            $('#tableItemList tr').each(function() {
                var teksHarga = $(this).find('td').eq(7).text().replace(/[^0-9]/g, '');
                var nilaiHarga = parseInt(teksHarga) || 0;
                totalKeseluruhan += nilaiHarga;
            });
            $('#grandTotal').text(formatRupiah(totalKeseluruhan));
        }

        // ============================================
        // FUNGSI: RESET FORMULIR
        // ============================================
        function resetFormulir() {
            $('#form-data').trigger('reset');
            $('#tableItemList').html(
                '<tr><td colspan="10" class="text-center">-- Belum ada item ditambahkan --</td></tr>'
            );
            $('#grandTotal').text('Rp 0');
        }

        // ============================================
        // FUNGSI: TAMPILKAN PESAN KESALAHAN
        // ============================================
        function tampilkanPesanKesalahan(xhr) {
            let pesanError = xhr.responseJSON?.message || 'Terjadi kesalahan saat menyimpan data.';
            if (xhr.responseJSON?.errors) {
                pesanError += '\n' + Object.values(xhr.responseJSON.errors).map(e => e.join(', ')).join('\n');
            }
            Swal.fire({
                title: 'Kesalahan',
                text: pesanError,
                icon: 'error'
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
        // FUNGSI HELPER: FORMAT RUPIAH
        // ============================================
        function formatRupiah(angka) {
            if (!angka || angka == 0) return 'Rp 0';

            // Pastikan angka adalah number, bukan string
            var nilaiAngka = typeof angka === 'number' ? angka : parseInt(angka);

            // Jika parsing gagal atau hasil tidak valid
            if (isNaN(nilaiAngka)) return 'Rp 0';

            // Gunakan toLocaleString untuk format yang lebih akurat
            var formatted = nilaiAngka.toLocaleString('id-ID');

            return 'Rp ' + formatted;
        }

        // ============================================
        // FUNGSI: TAMPILKAN PAGINATION
        // ============================================
        function tampilkanPagination(dataRespons) {
            var htmlPagination = '<ul class="pagination">';
            var halamanSekarang = dataRespons.currentPage || dataRespons.current_page;
            var totalHalaman = dataRespons.totalPage || dataRespons.total_page;

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
