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
                        <input type="text" id="username" class="form-control" placeholder="username" />
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
            </div>
            <!-- /.login-card-body -->
        </div>
    </div>
    <!--end::Login Box-->
@endsection
