@extends('layouts.master')

@section('title', 'Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('/') }}">Dashboard</a></li>
    <li class="breadcrumb-item active"></li>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <!--begin::Col-->
            <div class="col-lg-3 col-6">
                <!--begin::Small Box Widget 1-->
                <div class="small-box" style="background:#060771 ; color: white;">
                    <div class="inner">
                        <h3 id="total-customer">-</h3>

                        <p>Pelanggan</p>
                    </div>
                    <svg class="small-box-icon" fill="white" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"
                        aria-hidden="true">
                        <path
                            d="M12 12c2.485 0 4.5-2.015 4.5-4.5S14.485 3 12 3 7.5 5.015 7.5 7.5 9.515 12 12 12zm0 1.5c-2.7 0-8.25 1.357-8.25 4.125V21h16.5v-3.375c0-2.768-5.55-4.125-8.25-4.125z" />
                    </svg>


                    <a href="#"
                        class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                        info Lengkap <i class="bi bi-link-45deg"></i>
                    </a>
                </div>
                <!--end::Small Box Widget 1-->
            </div>
            <!--begin::Col-->
            <div class="col-lg-3 col-6">
                <!--begin::Small Box Widget 1-->
                <div class="small-box" style="background:#FF6C0C ; color: white;">
                    <div class="inner">
                        <h3 id="total-barang">-</h3>

                        <p>Barang</p>
                    </div>
                    <svg class="small-box-icon" fill="white" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"
                        aria-hidden="true">
                        <path
                            d="M21 7.5l-9-4.5-9 4.5v9l9 4.5 9-4.5v-9zM12 4.118l6.494 3.247L12 10.612 5.506 7.365 12 4.118zM4.5 9.118l6.75 3.375v7.007L4.5 16.125v-7.007zm15 7.007l-6.75 3.375v-7.007l6.75-3.375v7.007z" />
                    </svg>



                    <a href="#"
                        class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                        info Lengkap <i class="bi bi-link-45deg"></i>
                    </a>
                </div>
                <!--end::Small Box Widget 2-->
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Wait untuk window load supaya semua script sudah dimuat
        window.addEventListener('load', function() {
            waitForJQuery();
        });

        function waitForJQuery() {
            if (typeof jQuery !== 'undefined' && typeof $ !== 'undefined') {
                console.log('=== jQuery READY ===');
                console.log('jQuery version:', jQuery.fn.jquery);
                console.log('==================');

                // Initialize dashboard setelah jQuery ready
                initializeDashboard();
            } else {
                console.log('Waiting for jQuery...');
                setTimeout(waitForJQuery, 50); // Check setiap 50ms
            }
        }

        function initializeDashboard() {
            $(document).ready(function() {
                console.log('=== DASHBOARD INITIALIZING ===');

                // Check session on page load
                checkSession();

                // Refresh data button handler
                $(document).on('click', '#refresh-data', function() {
                    console.log('Refresh button clicked');
                    refreshCustomerData();
                });

                // Logout button handler
                $(document).on('click', '#logout-btn', function() {
                    console.log('Logout button clicked');
                    logout();
                });

                // Auto refresh data every 5 minutes
                setInterval(function() {
                    console.log('Auto refresh triggered');
                    loadCustomerData();
                }, 300000); // 5 minutes
            });
        }

        function loadCustomerData() {
            console.log('=== LOADING CUSTOMER DATA ===');

            // Show loading spinner in card
            $('#total-customer').html('<i class="fas fa-spinner fa-spin"></i>');
            $('#last-updated').text('Loading...');

            $.ajax({
                url: '{{ route('fresh-data-customer') }}',
                type: 'GET',
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    console.log('=== CUSTOMER DATA SUCCESS ===');
                    console.log('Full Response:', response);
                    console.log('Success:', response.success);
                    console.log('Data:', response.data);
                    console.log('============================');

                    if (response.success) {
                        // Update customer count - handle nested data structure
                        const totalCustomer = response.data.total_customer ||
                            response.data.data?.total_customer ||
                            response.data.total ||
                            response.data.count || 0;
                        $('#total-customer').text(totalCustomer);

                        // Update last updated time
                        updateLastUpdated();

                        console.log('Customer count updated to:', totalCustomer);
                    } else {
                        $('#total-customer').html('<span class="text-warning">Error</span>');
                        $('#last-updated').text('Error loading data');
                        console.error('Failed to load customer data:', response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.log('=== CUSTOMER DATA ERROR ===');
                    console.log('Status:', xhr.status);
                    console.log('Error:', error);
                    console.log('Response Text:', xhr.responseText);
                    console.log('==========================');

                    $('#total-customer').html('<span class="text-warning">Error</span>');
                    $('#last-updated').text('Error');

                    if (xhr.status === 401) {
                        redirectToLogin('Session expired while loading data.');
                    } else {
                        console.error('Customer data load failed:', error);
                    }
                }
            });
        }

        function refreshCustomerData() {
            console.log('=== REFRESHING DATA ===');

            // Add spinning animation to refresh button
            $('#refresh-icon').addClass('fa-spin');

            // Reload customer data
            loadCustomerData();

            // Remove spinning animation after 2 seconds
            setTimeout(function() {
                $('#refresh-icon').removeClass('fa-spin');
            }, 2000);
        }
    </script>
@endpush
