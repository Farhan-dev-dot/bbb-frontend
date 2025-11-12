<!--begin::Login Box-->
<div class="login-box">
    <!--begin::Login Logo-->
    <div class="login-logo">
        <a href="">
            <img src="{{ asset('images/AdminLTELogo.png') }}" alt="AdminLTE Logo" class="brand-image opacity-75 shadow"
                style="width: 60px;">
            <span class="brand-text font-weight-light"><b>Admin</b>LTE</span>
        </a>
    </div>
    <!--end::Login Logo-->

    <!--begin::Login Card-->
    <div class="card card-outline card-primary">
        <div class="card-header text-center">
            <a href="" class="h1"><b>Admin</b>LTE</a>
        </div>
        <div class="card-body">
            @yield('auth-content')
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
</div>
<!--end::Login Box-->
