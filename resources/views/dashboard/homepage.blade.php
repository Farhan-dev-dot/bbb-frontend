@extends('layouts.master')

@section('title', 'Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('/') }}">Dashboard</a></li>
    <li class="breadcrumb-item active"></li>
@endsection

@section('content')
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-end">
            <form class="col-lg-3 col-md-4 col-12">
                <input type="date" id="tanggal" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}">
            </form>
        </div>
    </div>

    <div class="row">
        <!--begin::Col-->
        <div class="col-lg-3 col-6">
            <!--begin::Small Box Widget 1-->
            <div class="small-box" style="background:#060771 ; color: white;">
                <div class="inner">
                    <h3 id="total-customer">-</h3>
                    <p>Pelanggan</p>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" class="small-box-icon" fill="white"
                    viewBox="0 0 640 640"><!--!Font Awesome Pro v7.1.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2025 Fonticons, Inc.-->
                    <path
                        d="M320 80C377.4 80 424 126.6 424 184C424 241.4 377.4 288 320 288C262.6 288 216 241.4 216 184C216 126.6 262.6 80 320 80zM96 152C135.8 152 168 184.2 168 224C168 263.8 135.8 296 96 296C56.2 296 24 263.8 24 224C24 184.2 56.2 152 96 152zM0 480C0 409.3 57.3 352 128 352C140.8 352 153.2 353.9 164.9 357.4C132 394.2 112 442.8 112 496L112 512C112 523.4 114.4 534.2 118.7 544L32 544C14.3 544 0 529.7 0 512L0 480zM521.3 544C525.6 534.2 528 523.4 528 512L528 496C528 442.8 508 394.2 475.1 357.4C486.8 353.9 499.2 352 512 352C582.7 352 640 409.3 640 480L640 512C640 529.7 625.7 544 608 544L521.3 544zM472 224C472 184.2 504.2 152 544 152C583.8 152 616 184.2 616 224C616 263.8 583.8 296 544 296C504.2 296 472 263.8 472 224zM160 496C160 407.6 231.6 336 320 336C408.4 336 480 407.6 480 496L480 512C480 529.7 465.7 544 448 544L192 544C174.3 544 160 529.7 160 512L160 496z" />
                </svg>
                <a href="#"
                    class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                    info Lengkap <i class="bi bi-link-45deg"></i>
                </a>
            </div>
            <!--end::Small Box Widget 1-->
        </div>
        <!--end::Col-->

        <!--begin::Col-->
        <div class="col-lg-3 col-6">
            <!--begin::Small Box Widget 2-->
            <div class="small-box" style="background:#FF6C0C ; color: white;">
                <div class="inner">
                    <h3 id="total-barang">-</h3>
                    <p>Barang</p>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg"class="small-box-icon" fill="white"
                    viewBox="0 0 640 640"><!--!Font Awesome Free v7.1.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                    <path
                        d="M560.3 301.2C570.7 313 588.6 315.6 602.1 306.7C616.8 296.9 620.8 277 611 262.3L563 190.3C560.2 186.1 556.4 182.6 551.9 180.1L351.4 68.7C332.1 58 308.6 58 289.2 68.7L88.8 180C83.4 183 79.1 187.4 76.2 192.8L27.7 282.7C15.1 306.1 23.9 335.2 47.3 347.8L80.3 365.5L80.3 418.8C80.3 441.8 92.7 463.1 112.7 474.5L288.7 574.2C308.3 585.3 332.2 585.3 351.8 574.2L527.8 474.5C547.9 463.1 560.2 441.9 560.2 418.8L560.2 301.3zM320.3 291.4L170.2 208L320.3 124.6L470.4 208L320.3 291.4zM278.8 341.6L257.5 387.8L91.7 299L117.1 251.8L278.8 341.6z" />
                </svg>
                <a href="#"
                    class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                    info Lengkap <i class="bi bi-link-45deg"></i>
                </a>
            </div>
            <!--end::Small Box Widget 2-->
        </div>
        <!--end::Col-->

        <!--begin::Col-->
        <div class="col-lg-3 col-6">
            <!--begin::Small Box Widget 3-->
            <div class="small-box" style="background:#BF1A1A ; color: white;">
                <div class="inner">
                    <h3 id="total-pendapatan-hari-ini">-</h3>
                    <p>Total Pendapatan Hari ini</p>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" class="small-box-icon" fill="white"
                    viewBox="0 0 640 640"><!--!Font Awesome Free v7.1.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                    <path
                        d="M128 96C92.7 96 64 124.7 64 160L64 448C64 483.3 92.7 512 128 512L512 512C547.3 512 576 483.3 576 448L576 256C576 220.7 547.3 192 512 192L136 192C122.7 192 112 181.3 112 168C112 154.7 122.7 144 136 144L520 144C533.3 144 544 133.3 544 120C544 106.7 533.3 96 520 96L128 96zM480 320C497.7 320 512 334.3 512 352C512 369.7 497.7 384 480 384C462.3 384 448 369.7 448 352C448 334.3 462.3 320 480 320z" />
                </svg>
                <a href="#"
                    class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                    info Lengkap <i class="bi bi-link-45deg"></i>
                </a>
            </div>
            <!--end::Small Box Widget 3-->
        </div>
        <!--end::Col-->

        <!--begin::Col-->
        <div class="col-lg-3 col-6">
            <!--begin::Small Box Widget 4-->
            <div class="small-box" style="background:#777C6D ; color: white;">
                <div class="inner">
                    <h3 id="total-transaksi-hari-ini">-</h3>
                    <p>Total Transaksi </p>
                </div>

                <svg xmlns="http://www.w3.org/2000/svg" class="small-box-icon" fill="white"
                    viewBox="0 0 640 640"><!--!Font Awesome Free v7.1.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                    <path
                        d="M160 64C124.7 64 96 92.7 96 128C96 163.3 124.7 192 160 192L208 192L208 224L151 224C119.4 224 92.5 247.1 87.7 278.4L65.1 428.1C64.4 432.8 64 437.6 64 442.4L64 512C64 547.3 92.7 576 128 576L512 576C547.3 576 576 547.3 576 512L576 442.4C576 437.6 575.6 432.8 574.9 428L552.2 278.4C547.5 247.1 520.6 224 489 224L272 224L272 192L320 192C355.3 192 384 163.3 384 128C384 92.7 355.3 64 320 64L160 64zM160 112L320 112C328.8 112 336 119.2 336 128C336 136.8 328.8 144 320 144L160 144C151.2 144 144 136.8 144 128C144 119.2 151.2 112 160 112zM128 488C128 474.7 138.7 464 152 464L488 464C501.3 464 512 474.7 512 488C512 501.3 501.3 512 488 512L152 512C138.7 512 128 501.3 128 488zM176 328C162.7 328 152 317.3 152 304C152 290.7 162.7 280 176 280C189.3 280 200 290.7 200 304C200 317.3 189.3 328 176 328zM296 304C296 317.3 285.3 328 272 328C258.7 328 248 317.3 248 304C248 290.7 258.7 280 272 280C285.3 280 296 290.7 296 304zM224 408C210.7 408 200 397.3 200 384C200 370.7 210.7 360 224 360C237.3 360 248 370.7 248 384C248 397.3 237.3 408 224 408zM392 304C392 317.3 381.3 328 368 328C354.7 328 344 317.3 344 304C344 290.7 354.7 280 368 280C381.3 280 392 290.7 392 304zM320 408C306.7 408 296 397.3 296 384C296 370.7 306.7 360 320 360C333.3 360 344 370.7 344 384C344 397.3 333.3 408 320 408zM488 304C488 317.3 477.3 328 464 328C450.7 328 440 317.3 440 304C440 290.7 450.7 280 464 280C477.3 280 488 290.7 488 304zM416 408C402.7 408 392 397.3 392 384C392 370.7 402.7 360 416 360C429.3 360 440 370.7 440 384C440 397.3 429.3 408 416 408z" />
                </svg>
                <a href="#"
                    class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                    info Lengkap <i class="bi bi-link-45deg"></i>
                </a>
            </div>
            <!--end::Small Box Widget 4-->
        </div>
        <!--end::Col-->
    </div>

    <div class="row">
        <div class="col-8">
            <div class="card mb-4">
                <div class="card-header border-0">
                    <div class="d-flex justify-content-between">
                        <h3 class="card-title"> Tren Pembelian Perbulan </h3>
                        <a href="javascript:void(0);"
                            class="link-secondary link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover">View
                            Report</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="position-relative mb-4">
                        <canvas id="transaction-chart" style="height: 300px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="card mb-4">
                <div class="card-header border-0">
                    <div class="d-flex justify-content-between">
                        <h3 class="card-title"> Tren Pembelian PerTahun </h3>
                    </div>
                </div>
                <div class="card-body">
                    <div class="position-relative mb-4">
                        <canvas id="distribusi-produk-chart" style="height:300px"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Load data pertama kali
            loadAllData();

            // Event listener untuk perubahan tanggal
            $('#tanggal').on('change', function() {
                loadAllData();
            });
        });

        /* ----------------------------------------------------------
            LOAD ALL DATA - Panggil semua function load
        ---------------------------------------------------------- */
        function loadAllData() {
            const tanggal = $('#tanggal').val();

            loadCustomerData();
            loadBarangData();
            loadDatapendapatanHariIni(tanggal);
            loadDatatransaksiHariIni(tanggal);
        }

        /* ----------------------------------------------------------
            1. LOAD TOTAL CUSTOMER (Tidak ada filter tanggal)
        ---------------------------------------------------------- */
        function loadCustomerData() {
            $('#total-customer').html('<i class="fas fa-spinner fa-spin"></i>');

            $.ajax({
                url: '{{ route('fresh-data-customer') }}',
                type: 'GET',
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        const totalCustomer =
                            response.data.total_customer ||
                            response.data.data?.total_customer ||
                            response.data.total ||
                            response.data.count || 0;

                        $('#total-customer').text(totalCustomer);
                    } else {
                        showError('#total-customer');
                        console.error('Customer error:', response.message);
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 401) {
                        alert("Session expired, please login again");
                        window.location.href = "/login";
                    }
                    showError('#total-customer');
                }
            });
        }

        /* ----------------------------------------------------------
            2. LOAD TOTAL BARANG (Tidak ada filter tanggal)
        ---------------------------------------------------------- */
        function loadBarangData() {
            $('#total-barang').html('<i class="fas fa-spinner fa-spin"></i>');

            $.ajax({
                url: '{{ route('fresh-data-barang') }}',
                type: 'GET',
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        $('#total-barang').text(response.data.total_barang);
                    } else {
                        showError('#total-barang');
                        console.error('Barang error:', response.message);
                    }
                },
                error: function() {
                    showError('#total-barang');
                }
            });
        }

        /* ----------------------------------------------------------
            3. LOAD TOTAL PENDAPATAN HARI INI (DENGAN FILTER TANGGAL)
        ---------------------------------------------------------- */
        function loadDatapendapatanHariIni(tanggal) {
            $('#total-pendapatan-hari-ini').html('<i class="fas fa-spinner fa-spin"></i>');

            // Buat URL dengan parameter tanggal jika ada
            let url = '{{ route('data-pendapatan-hari-ini') }}';
            if (tanggal) {
                url += '?tanggal=' + tanggal;
            }

            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        $('#total-pendapatan-hari-ini').text(response.data.total_pendapatan_hari_ini);
                    } else {
                        showError('#total-pendapatan-hari-ini');
                        console.error('Pendapatan error:', response.message);

                    }
                },
                error: function(xhr) {
                    showError('#total-pendapatan-hari-ini');
                    console.error('Pendapatan AJAX error:', xhr.responseText); // Tambahkan ini
                }
            });
        }

        /* ----------------------------------------------------------
            4. LOAD TOTAL TRANSAKSI HARI INI (DENGAN FILTER TANGGAL)
        ---------------------------------------------------------- */
        function loadDatatransaksiHariIni(tanggal) {
            $('#total-transaksi-hari-ini').html('<i class="fas fa-spinner fa-spin"></i>');

            // Buat URL dengan parameter tanggal jika ada
            let url = '{{ route('data-transaksi-hari-ini') }}';
            if (tanggal) {
                url += '?tanggal=' + tanggal;
            }

            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        $('#total-transaksi-hari-ini').text(response.data.total_transaksi_hari_ini);
                    } else {
                        showError('#total-transaksi-hari-ini');
                        console.error('Transaksi error:', response.message);
                    }
                },
                error: function() {
                    showError('#total-transaksi-hari-ini');
                }
            });
        }

        /* ----------------------------------------------------------
            UTIL: TAMPILKAN ERROR
        ---------------------------------------------------------- */
        function showError(selector) {
            $(selector).html('<span class="text-warning">Error</span>');
        }
    </script>
@endpush
