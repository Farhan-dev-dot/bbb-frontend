@extends('layouts.master')

@section('title', 'Stok Opname')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('/') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Stok Opname</li>
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
                <div class="card-header g-2 justify-content-between">
                    <button type="button" id="tombol-stokopname" class="btn btn-sm"
                        style="background:  #060771; color: white;" data-bs-toggle="modal" data-bs-target="#Modalbody">
                        <i class="fa-solid fa-plus"></i></button>
                    {{-- <button type="button" id="tombol-excel" class="btn btn-sm" style="background: #4CAF50; color: white;">
                        <i class="fa-solid fa-file-excel"></i>
                    </button> --}}
                </div>
                <div class="card-body">
                    <table class="table table-bordered" id="tabel-data">
                        <thead>
                            <tr>
                                <th style="width: 8%;">Aksi</th>
                                <th>Nama Barang</th>
                                <th>Kapasitas</th>
                                <th>Isi Sistem</th>
                                <th>Kosong Sistem</th>
                                <th>Isi Fisik</th>
                                <th>Kosong Fisik</th>
                                <th>Selisih Isi</th>
                                <th>Selisih Kosong</th>
                                <th>Total Selisih</th>
                                <th>Tanggal</th>
                                <th>Keterangan</th>
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

@include('modal.stokopname')

@push('scripts')
    <script>
        // ============================================
        // VARIABEL GLOBAL
        // ============================================
        var modeDraft = 0;
        var datas = [];
        var daftarSemuaBarang = [];

        $(document).ready(function() {

            // Event: Buka modal stok opname
            $('#tombol-stokopname').on('click', function() {
                bukaModalStokOpname();
            });

            // Event: Simpan koreksi stok
            $('#btn-save').on('click', function() {
                simpanKoreksiStok();
            });

            // Event: Pilih barang di modal
            $('#id_barang').on('change', function() {
                var idBarang = $(this).val();
                if (idBarang) {
                    muatDataBarang(idBarang);
                }
            });

            // Event: Hitung selisih real-time saat input stok fisik
            $('#stok_isi_fisik, #stok_kosong_fisik').on('input', function() {
                hitungSelisihRealtime();
            });

            // Event: Filter data
            $('#tombol-filter').on('click', function() {
                ambilDataTransaksi(1);
            });

            // Event: Bersihkan filter
            $('#tombol-bersihkan').on('click', function() {
                $('#tanggal_dari').val('');
                $('#tanggal_sampai').val('');
                $('#tipe_transaksi').val('');
                $('#id_barang_filter').val('');
                ambilDataTransaksi(1);
            });

            $('#tombol-excel').on('click', function() {
                $.ajax({
                    url: "/stok-opname/export",
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
                        a.download = "stok-opname.xlsx";
                        a.click();
                    }
                });
            });


            // $('#tombol-print').on('click', function() {
            //     var tanggal_dari = $('#tanggal_dari').val();
            //     var tanggal_sampai = $('#tanggal_sampai').val();
            //     var tipe_transaksi = $('#tipe_transaksi').val();
            //     var id_barang = $('#id_barang').val();

            //     var url = '/stok-opname/cetak';
            //     var params = [];

            //     if (tanggal_dari) params.push('tanggal_dari=' + encodeURIComponent(tanggal_dari));
            //     if (tanggal_sampai) params.push('tanggal_sampai=' + encodeURIComponent(tanggal_sampai));
            //     if (tipe_transaksi) params.push('tipe_transaksi=' + encodeURIComponent(tipe_transaksi));
            //     if (id_barang) params.push('id_barang=' + encodeURIComponent(id_barang));

            //     if (params.length > 0) {
            //         url += '?' + params.join('&');
            //     }

            //     window.open(url, '_blank');
            // });

            // Isi dropdown barang SEKALI saat halaman load
            isiDropdownBarang();

            // TIDAK panggil ambilDataTransaksi() otomatis
            // Data tabel hanya ditampilkan setelah user klik tombol Filter
            ambilDataTransaksi();
        });

        // ============================================
        // FUNGSI: AMBIL DATA TRANSAKSI DENGAN FILTER
        // ============================================
        function ambilDataTransaksi(halaman = 1) {
            $.ajax({
                url: '/stok-opname/getdata',
                type: 'GET',
                data: {
                    page: halaman,
                    tanggal_dari: $('#tanggal_dari').val(),
                    tanggal_sampai: $('#tanggal_sampai').val(),
                    tipe_transaksi: $('#tipe_transaksi').val(),
                    id_barang: $('#id_barang_filter').val(),
                    keyword: $('#keyword').val()
                },
                success: function(respons) {

                    // Data ada di respons.stok_opname
                    var data = respons.stok_opname || [];
                    datas = data;


                    tampilkanDataKeTable(datas);
                    tampilkanPagination(respons);
                },
                error: function(xhr) {
                    console.error('❌ Error:', xhr);
                    console.error('Status:', xhr.status);
                    console.error('Response:', xhr.responseJSON);

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
        function tampilkanDataKeTable(dataBarang) {
            var isiTabel = $('#tabel-data tbody');
            isiTabel.empty();

            if (!dataBarang || dataBarang.length === 0) {
                isiTabel.append('<tr><td colspan="12" class="text-center">Tidak ada data ditemukan</td></tr>');
                return;
            }

            $.each(dataBarang, function(indeks, items) {
                // Struktur API baru
                var stokSistem = items.stok_sistem || {};
                var stokFisik = items.stok_fisik || {};
                var selisih = items.selisih || {};

                // Hitung total selisih
                var totalSelisih = (selisih.isi || 0) + (selisih.kosong || 0);

                var barisTabel =
                    '<tr>' +
                    '<td class="text-center">' +
                    '<div class="btn-group btn-group-sm" role="group">' +
                    '<button class="btn btn-danger" onclick="hapusRiwayat(' + items.id_opname +
                    ')" title="Hapus">' +
                    '<i class="fas fa-trash"></i>' +
                    '</button>' +
                    '</div>' +
                    '</td>' +
                    '<td>' +
                    (items.nama_barang || '-') +
                    '<div class="text-muted small">#' + (items.id_opname || '-') + '</div>' +
                    '</td>' +
                    '<td class="text-center">' + (items.kapasitas || '-') + '</td>' +
                    '<td class="text-center">' + (stokSistem.isi || 0) + '</td>' +
                    '<td class="text-center">' + (stokSistem.kosong || 0) + '</td>' +
                    '<td class="text-center">' + (stokFisik.isi !== null ? stokFisik.isi : '-') + '</td>' +
                    '<td class="text-center">' + (stokFisik.kosong !== null ? stokFisik.kosong : '-') + '</td>' +
                    '<td class="text-center ' + getSelisihClass(selisih.isi) + '">' + (selisih.isi !== null ?
                        selisih.isi : '-') + '</td>' +
                    '<td class="text-center ' + getSelisihClass(selisih.kosong) + '">' + (selisih.kosong !== null ?
                        selisih.kosong : '-') + '</td>' +
                    '<td class="text-center ' + getSelisihClass(totalSelisih) + '">' + (totalSelisih !== 0 ?
                        totalSelisih : '-') + '</td>' +
                    '<td class="text-center">' + (items.tanggal_opname ? formatTanggal(items.tanggal_opname) :
                        '-') + '</td>' +
                    '<td>' + (items.keterangan || '-') + '</td>' +
                    '</tr>';
                isiTabel.append(barisTabel);
            });
        }

        function getSelisihClass(nilai) {
            if (nilai === null || nilai === undefined) return '';
            if (nilai > 0) return 'text-success fw-bold';
            if (nilai < 0) return 'text-danger fw-bold';
            return 'text-muted';
        }


        // ============================================
        // FUNGSI: HAPUS RIWAYAT
        // ============================================
        function hapusRiwayat(idRiwayat) {
            Swal.fire({
                title: 'Hapus Riwayat?',
                text: 'Apakah Anda yakin ingin menghapus data riwayat ini? Tindakan ini tidak dapat dibatalkan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    prosesHapusRiwayat(idRiwayat);
                }
            });
        }

        function prosesHapusRiwayat(idRiwayat) {
            $.ajax({
                url: '/stok-opname/delete-data/' + idRiwayat,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Content-Type': 'application/json'
                },
                success: function(response) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: 'Data riwayat berhasil dihapus.',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        ambilDataTransaksi(1);
                    });
                },
                error: function(xhr) {
                    let errorMessage = 'Gagal menghapus data riwayat.';
                    if (xhr.responseJSON?.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.statusText) {
                        errorMessage = xhr.statusText;
                    }

                    Swal.fire({
                        title: 'Kesalahan!',
                        text: errorMessage,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });

                    console.error('Error deleting riwayat:', xhr);
                }
            });
        }

        // ============================================
        // FUNGSI: LIHAT HISTORY LENGKAP PER BARANG
        // ============================================
        function lihatHistory(idBarang, namaBarang) {
            Swal.fire({
                title: '<i class="fas fa-spinner fa-spin"></i> Memuat History...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                showConfirmButton: false
            });

            $.ajax({
                url: '/stok-opname/history/' + idBarang,
                type: 'GET',
                success: function(response) {
                    console.log('📜 History opname:', response);

                    if (response.success && response.history && response.history.length > 0) {
                        tampilkanModalHistory(response, namaBarang);
                    } else {
                        Swal.fire({
                            title: 'Tidak Ada History',
                            text: 'Barang ini belum pernah dilakukan stok opname.',
                            icon: 'info'
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        title: 'Kesalahan',
                        text: 'Gagal mengambil history: ' + (xhr.responseJSON?.message ||
                            'Unknown error'),
                        icon: 'error'
                    });
                }
            });
        }

        function tampilkanModalHistory(response, namaBarang) {
            var barangInfo = response.barang || {};
            var history = response.history || [];
            var totalHistory = response.total_history || 0;

            var htmlTable = `
                <div style="text-align: left;">
                    <p><strong>Barang:</strong> ${namaBarang}</p>
                    <p><strong>Total Koreksi:</strong> ${totalHistory} kali</p>
                    <p><strong>Stok Saat Ini:</strong> 
                        Isi = ${barangInfo.stok_saat_ini?.isi || 0}, 
                        Kosong = ${barangInfo.stok_saat_ini?.kosong || 0}
                    </p>
                    <hr>
                    <div style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-sm table-bordered" style="font-size: 12px;">
                            <thead style="position: sticky; top: 0; background: white;">
                                <tr>
                                    <th>#</th>
                                    <th>Tanggal</th>
                                    <th>Stok Sistem</th>
                                    <th>Stok Fisik</th>
                                    <th>Selisih</th>
                                    <th>By</th>
                                </tr>
                            </thead>
                            <tbody>
            `;

            $.each(history, function(index, item) {
                var tanggal = new Date(item.tanggal_opname).toLocaleString('id-ID', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });

                var selisihIsi = item.selisih?.isi || 0;
                var selisihKosong = item.selisih?.kosong || 0;
                var selisihClass = (selisihIsi < 0 || selisihKosong < 0) ? 'text-danger' :
                    (selisihIsi > 0 || selisihKosong > 0) ? 'text-success' : 'text-muted';

                htmlTable += `
                    <tr>
                        <td>${index + 1}</td>
                        <td style="white-space: nowrap;">${tanggal}</td>
                        <td>Isi: ${item.stok_sistem?.isi || 0}<br>Kosong: ${item.stok_sistem?.kosong || 0}</td>
                        <td>Isi: ${item.stok_fisik?.isi || 0}<br>Kosong: ${item.stok_fisik?.kosong || 0}</td>
                        <td class="${selisihClass}">
                            <strong>Isi: ${selisihIsi > 0 ? '+' : ''}${selisihIsi}</strong><br>
                            <strong>Kosong: ${selisihKosong > 0 ? '+' : ''}${selisihKosong}</strong>
                        </td>
                        <td style="font-size: 10px;">${item.created_by || '-'}</td>
                    </tr>
                    ${item.keterangan ? `<tr><td colspan="6" class="text-muted" style="font-size: 11px;"><i>💬 ${item.keterangan}</i></td></tr>` : ''}
                `;
            });

            htmlTable += `
                            </tbody>
                        </table>
                    </div>
                </div>
            `;

            Swal.fire({
                title: '📜 History Stok Opname',
                html: htmlTable,
                width: '800px',
                confirmButtonText: 'Tutup',
                customClass: {
                    popup: 'swal-wide'
                }
            });
        }

        // ============================================
        // FUNGSI: BUKA MODAL STOK OPNAME
        // ============================================
        function bukaModalStokOpname() {
            // Reset form
            $('#form-data')[0].reset();
            $('#id_barang').val('');
            $('#nama_barang').val('');
            $('#kapasitas').val('');
            $('#stok_sistem_isi').val('');
            $('#stok_sistem_kosong').val('');
            $('#stok_isi_fisik').val('');
            $('#stok_kosong_fisik').val('');
            $('#keterangan').val('');
            $('#preview-selisih-isi').html('<span class="text-muted">-</span>');
            $('#preview-selisih-kosong').html('<span class="text-muted">-</span>');

            // Set tanggal hari ini
            var today = new Date().toISOString().split('T')[0];
            $('#tanggal_opname').val(today);
        }

        // ============================================
        // FUNGSI: HITUNG SELISIH REAL-TIME
        // ============================================
        function hitungSelisihRealtime() {
            var stokSistemIsi = parseInt($('#stok_sistem_isi').val()) || 0;
            var stokSistemKosong = parseInt($('#stok_sistem_kosong').val()) || 0;
            var stokFisikIsi = parseInt($('#stok_isi_fisik').val());
            var stokFisikKosong = parseInt($('#stok_kosong_fisik').val());

            // Hitung selisih jika input valid
            if (!isNaN(stokFisikIsi)) {
                var selisihIsi = stokFisikIsi - stokSistemIsi;
                var selisihClass = selisihIsi > 0 ? 'text-success' : selisihIsi < 0 ? 'text-danger' : 'text-muted';
                $('#preview-selisih-isi').html('<span class="' + selisihClass + ' fw-bold">' +
                    (selisihIsi > 0 ? '+' : '') + selisihIsi + '</span>');
            } else {
                $('#preview-selisih-isi').html('<span class="text-muted">-</span>');
            }

            if (!isNaN(stokFisikKosong)) {
                var selisihKosong = stokFisikKosong - stokSistemKosong;
                var selisihClass = selisihKosong > 0 ? 'text-success' : selisihKosong < 0 ? 'text-danger' : 'text-muted';
                $('#preview-selisih-kosong').html('<span class="' + selisihClass + ' fw-bold">' +
                    (selisihKosong > 0 ? '+' : '') + selisihKosong + '</span>');
            } else {
                $('#preview-selisih-kosong').html('<span class="text-muted">-</span>');
            }
        }

        // ============================================
        // FUNGSI: MUAT DATA BARANG (REAL-TIME)
        // ============================================
        function muatDataBarang(idBarang) {
            if (!idBarang) return;

            // Reset field stok fisik setiap kali pilih barang
            $('#stok_isi_fisik').val('');
            $('#stok_kosong_fisik').val('');
            $('#keterangan').val('');
            $('#preview-selisih-isi').html('<span class="text-muted">-</span>');
            $('#preview-selisih-kosong').html('<span class="text-muted">-</span>');

            // Tampilkan loading
            $('#nama_barang').val('Memuat...');
            $('#kapasitas').val('Memuat...');
            $('#stok_sistem_isi').val('...');
            $('#stok_sistem_kosong').val('...');

            // ⚠️ PENTING: SELALU ambil data REAL-TIME dari API
            // JANGAN gunakan data dari laporan (data lama/snapshot)
            $.ajax({
                url: '/stok-opname/current-stok',
                type: 'GET',
                data: {
                    id_barang: idBarang
                },
                success: function(response) {
                    if (response.success && response.data && response.data.length > 0) {
                        // Filter data berdasarkan id_barang yang dipilih
                        var barangData = response.data.find(function(item) {
                            return item.id_barang == idBarang;
                        });

                        if (barangData) {
                            $('#nama_barang').val(barangData.nama_barang || '');
                            $('#kapasitas').val(barangData.kapasitas || '');
                            $('#stok_sistem_isi').val(barangData.stok_tabung_isi || 0);
                            $('#stok_sistem_kosong').val(barangData.stok_tabung_kosong || 0);
                        } else {
                            Swal.fire({
                                title: 'Data Tidak Ditemukan',
                                text: 'Barang dengan ID tersebut tidak ditemukan.',
                                icon: 'warning',
                                target: document.getElementById('Modalbody')
                            });
                            resetFormFields();
                        }
                    } else {
                        Swal.fire({
                            title: 'Data Tidak Ditemukan',
                            text: 'Data barang tidak tersedia. Silakan coba lagi.',
                            icon: 'warning',
                            target: document.getElementById('Modalbody')
                        });
                        resetFormFields();
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        title: 'Kesalahan',
                        text: 'Gagal mengambil data stok real-time. Silakan coba lagi.',
                        icon: 'error',
                        target: document.getElementById('Modalbody')
                    });

                    resetFormFields();
                }
            });
        }

        // Helper function untuk reset form fields
        function resetFormFields() {
            $('#nama_barang').val('');
            $('#kapasitas').val('');
            $('#stok_sistem_isi').val('');
            $('#stok_sistem_kosong').val('');
            $('#stok_isi_fisik').val('');
            $('#stok_kosong_fisik').val('');
        }

        // ============================================
        // FUNGSI: SIMPAN KOREKSI STOK
        // ============================================
        function simpanKoreksiStok() {
            var idBarang = $('#id_barang').val();
            var namaBarang = $('#nama_barang').val();
            var stokSistemIsi = parseInt($('#stok_sistem_isi').val()) || 0;
            var stokSistemKosong = parseInt($('#stok_sistem_kosong').val()) || 0;
            var stokIsiFisik = parseInt($('#stok_isi_fisik').val());
            var stokKosongFisik = parseInt($('#stok_kosong_fisik').val());
            var keterangan = $('#keterangan').val() || '';
            var tanggalOpname = $('#tanggal_opname').val();

            // Validasi
            if (!idBarang) {
                Swal.fire({
                    title: 'Peringatan',
                    text: 'Silakan pilih barang terlebih dahulu',
                    icon: 'warning',
                    target: document.getElementById('Modalbody')
                });
                return;
            }

            if (!tanggalOpname) {
                Swal.fire({
                    title: 'Peringatan',
                    text: 'Tanggal opname harus diisi',
                    icon: 'warning',
                    target: document.getElementById('Modalbody')
                });
                return;
            }

            // Validasi stok fisik wajib diisi
            if (isNaN(stokIsiFisik) || stokIsiFisik < 0) {
                Swal.fire({
                    title: 'Peringatan',
                    text: 'Stok Isi Fisik harus diisi dengan angka yang valid',
                    icon: 'warning',
                    target: document.getElementById('Modalbody')
                });
                return;
            }

            if (isNaN(stokKosongFisik) || stokKosongFisik < 0) {
                Swal.fire({
                    title: 'Peringatan',
                    text: 'Stok Kosong Fisik harus diisi dengan angka yang valid',
                    icon: 'warning',
                    target: document.getElementById('Modalbody')
                });
                return;
            }

            // Hitung selisih
            var selisihIsi = stokIsiFisik - stokSistemIsi;
            var selisihKosong = stokKosongFisik - stokSistemKosong;

            // Validasi: Jangan simpan jika tidak ada perubahan
            if (selisihIsi === 0 && selisihKosong === 0) {
                Swal.fire({
                    title: 'Tidak Ada Perubahan',
                    html: 'Stok fisik sama dengan stok sistem.<br>Tidak ada yang perlu dikoreksi.',
                    icon: 'info',
                    confirmButtonText: 'OK',
                    target: document.getElementById('Modalbody')
                });
                return;
            }

            // Buat pesan konfirmasi dengan detail selisih
            var selisihIsiText = selisihIsi > 0 ? `<span style="color: green;">+${selisihIsi}</span>` :
                selisihIsi < 0 ? `<span style="color: red;">${selisihIsi}</span>` : '0';
            var selisihKosongText = selisihKosong > 0 ? `<span style="color: green;">+${selisihKosong}</span>` :
                selisihKosong < 0 ? `<span style="color: red;">${selisihKosong}</span>` : '0';

            // Konfirmasi dengan informasi detail
            Swal.fire({
                title: 'Konfirmasi Koreksi Stok',
                html: `
                    <div style="text-align: left;">
                        <p><strong>Barang:</strong> ${namaBarang}</p>
                        <hr>
                        <table style="width: 100%; font-size: 14px;">
                            <tr>
                                <td><strong>Stok Sistem Isi:</strong></td>
                                <td>${stokSistemIsi}</td>
                            </tr>
                            <tr>
                                <td><strong>Stok Fisik Isi:</strong></td>
                                <td>${stokIsiFisik}</td>
                            </tr>
                            <tr>
                                <td><strong>Selisih Isi:</strong></td>
                                <td>${selisihIsiText}</td>
                            </tr>
                            <tr><td colspan="2">&nbsp;</td></tr>
                            <tr>
                                <td><strong>Stok Sistem Kosong:</strong></td>
                                <td>${stokSistemKosong}</td>
                            </tr>
                            <tr>
                                <td><strong>Stok Fisik Kosong:</strong></td>
                                <td>${stokKosongFisik}</td>
                            </tr>
                            <tr>
                                <td><strong>Selisih Kosong:</strong></td>
                                <td>${selisihKosongText}</td>
                            </tr>
                        </table>
                        <hr>
                        <p style="font-size: 12px; color: #666;">
                            <i class="fas fa-info-circle"></i> 
                            Setiap koreksi akan tersimpan sebagai record baru untuk audit trail.
                        </p>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Simpan Koreksi!',
                cancelButtonText: 'Batal',
                target: document.getElementById('Modalbody'),
                customClass: {
                    popup: 'swal-wide'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    prosesKoreksiStok(idBarang, stokIsiFisik, stokKosongFisik, keterangan, tanggalOpname);
                }
            });
        }

        // ============================================
        // FUNGSI: PROSES KOREKSI STOK
        // ============================================
        function prosesKoreksiStok(idBarang, stokIsiFisik, stokKosongFisik, keterangan, tanggalOpname) {
            // Disable tombol save
            $('#btn-save').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');

            var payload = {
                corrections: [{
                    id_barang: parseInt(idBarang),
                    stok_isi_fisik: stokIsiFisik,
                    stok_kosong_fisik: stokKosongFisik,
                    keterangan: keterangan
                }],
                tanggal_opname: tanggalOpname
            };

            $.ajax({
                url: '/stok-opname/koreksi-stok',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Content-Type': 'application/json'
                },
                data: JSON.stringify(payload),
                success: function(response) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: response.message || 'Koreksi stok berhasil disimpan',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        $('#Modalbody').modal('hide');
                        $('#form-data')[0].reset();
                        $('#id_barang').val('').trigger('change');
                        resetFormFields();
                        setTimeout(function() {
                            ambilDataTransaksi(1);
                        }, 500);
                    });
                },
                error: function(xhr) {
                    var errorMessage = 'Gagal menyimpan koreksi stok';

                    if (xhr.status === 422 && xhr.responseJSON?.errors) {
                        var errors = xhr.responseJSON.errors;
                        var firstError = Object.values(errors)[0];
                        errorMessage = Array.isArray(firstError) ? firstError[0] : firstError;
                    } else if (xhr.responseJSON?.message) {
                        errorMessage = xhr.responseJSON.message;
                    }

                    Swal.fire({
                        title: 'Kesalahan',
                        text: errorMessage,
                        icon: 'error',
                        confirmButtonText: 'OK',
                        target: document.getElementById('Modalbody')
                    });

                    console.error('Error saving koreksi stok:', xhr);
                },
                complete: function() {
                    $('#btn-save').prop('disabled', false).html(
                        '<i class="fas fa-save me-1"></i>Simpan Koreksi Stok');
                }
            });
        }

        // ============================================
        // FUNGSI HELPER: GET ACCESS TOKEN
        // ============================================
        function getAccessToken() {
            // Ambil token dari meta tag atau session storage
            return document.querySelector('meta[name="access-token"]')?.getAttribute('content') || '';
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
            var selectBarangFilter = $('#id_barang_filter');
            var selectBarangModal = $('#id_barang');

            selectBarangFilter.empty().append('<option value="">-- Pilih Nama Barang --</option>');
            selectBarangModal.empty().append('<option value="">-- Pilih Barang --</option>');
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
                                    kapasitas: itemBarang.kapasitas,
                                    harga_jual: itemBarang.harga_jual,
                                    stok_tabung_isi: itemBarang.stok_tabung_isi,
                                    stok_tabung_kosong: itemBarang.stok_tabung_kosong
                                });

                                var optionHtml = '<option value="' + itemBarang.id_barang + '">' +
                                    itemBarang.nama_barang + '</option>';
                                selectBarangFilter.append(optionHtml);
                                selectBarangModal.append(optionHtml);
                            }
                        });

                    }
                    if (daftarSemuaBarang.length === 0) {
                        selectBarangFilter.append('<option value="" disabled>Barang tidak tersedia</option>');
                        selectBarangModal.append('<option value="" disabled>Barang tidak tersedia</option>');
                    }
                },
                error: function() {
                    selectBarangFilter.append('<option value="" disabled>Gagal mengambil data barang</option>');
                    selectBarangModal.append('<option value="" disabled>Gagal mengambil data barang</option>');
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

    <style>
        /* Custom width untuk SweetAlert konfirmasi koreksi stok */
        .swal-wide {
            width: 600px !important;
        }

        /* Styling untuk tabel dalam SweetAlert */
        .swal2-html-container table {
            border-collapse: collapse;
        }

        .swal2-html-container table td {
            padding: 5px 10px;
        }

        .swal2-html-container table tr:nth-child(odd) {
            background-color: #f9f9f9;
        }
    </style>
@endpush
