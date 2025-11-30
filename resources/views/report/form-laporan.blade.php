@extends('layouts.master')

@section('title', 'Laporan Transaksi')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('/') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Laporan Transaksi</li>
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
                        </div>
                        <div class="d-grid gap-2 d-md-block mt-3 text-end">
                            <button class="btn btn-sm" id="tombol-excel" type="button"
                                style="background: #060771 ;color: white;"><i class="fas fa-filter"></i> Export</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('scripts')
    <script>
        var datas = [];
        $(document).ready(function() {
            $('#tombol-excel').on('click', function() {
                // Ambil data yang sudah difilter, misal dari variabel global 'datas'
                var tanggal_dari = $('#tanggal_dari').val();
                var tanggal_sampai = $('#tanggal_sampai').val();

                if (!tanggal_dari || !tanggal_sampai) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Tanggal Belum Lengkap',
                        text: 'Silakan tentukan tanggal dari dan tanggal sampai terlebih dahulu.'
                    });
                    return;
                }

                $.ajax({
                    url: "/laporan-transaksi/export",
                    method: "POST",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        data: JSON.stringify(datas) // datas harus berisi data yang sudah terfilter!
                    },
                    xhrFields: {
                        responseType: 'blob'
                    },
                    success: function(blob) {
                        let url = URL.createObjectURL(blob);
                        let a = document.createElement('a');
                        a.href = url;
                        a.download = "Laporan Transaksi.xlsx";
                        a.click();
                    }
                });
            });


            $('#tanggal_dari, #tanggal_sampai').on('change', function() {
                getData();
            });
        });

        function getData() {
            $.ajax({
                url: '/laporan-transaksi/getdata',
                type: 'GET',
                data: {
                    tanggal_dari: $('#tanggal_dari').val(),
                    tanggal_sampai: $('#tanggal_sampai').val(),
                },
                success: function(respons) {
                    var data = respons.laporan;
                    datas = data;
                    console.log(datas);

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
    </script>
@endpush
