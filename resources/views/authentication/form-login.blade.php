@extends('layouts.auth')

@section('title', ' BBB | Login')
@section('meta-title', ' BBB | Login Page')
@section('meta-description', 'Login to BBB Dashboard - Bootstrap 5 Admin Panel')

@section('body-class', 'login-page bg-body-secondary')

@section('content')
    <!--begin::Login Box-->
    <div class="login-box">

        <!-- /.login-logo -->
        <div class="card">
            <div class="card-body login-card-body">
                <div class="login-logo">
                    <img src="{{ asset('images/logo.png') }}" class=" mx-auto d-block" alt="..."
                        style="width: 300px; height: 150px; object-fit: cover;">
                </div>

                <form id="Formlogin" method="post">
                    <div class="input-group mb-3">
                        <input type="email" id="email" class="form-control" placeholder="email" />
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" id="password" class="form-control" placeholder="Password" />
                    </div>
                    <!--begin::Row-->
                    <div class="row">
                        <div class="col-">
                            <div class="d-grid gap-2 justify-center">
                                <button type="submit" class="btn btn-primary">Sign In</button>
                            </div>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!--end::Row-->
                </form>
                {{-- <div class="social-auth-links text-center mb-3 d-grid gap-2">
                    <p>- OR -</p>
                    <a href="#" class="btn btn-primary">
                        <i class="bi bi-facebook me-2"></i> Sign in using Facebook
                    </a>
                    <a href="#" class="btn btn-danger">
                        <i class="bi bi-google me-2"></i> Sign in using Google+
                    </a>
                </div>
                <!-- /.social-auth-links -->
                <p class="mb-1"><a href="forgot-password.html">I forgot my password</a></p>
                <p class="mb-0">
                    <a href="register.html" class="text-center"> Register a new membership </a>
                </p> --}}
            </div>
            <!-- /.login-card-body -->
        </div>
    </div>
    <!--end::Login Box-->
@endsection
