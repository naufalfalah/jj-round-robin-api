@extends('layouts.admin_auth')
@section('title', 'Forgot Password')
@php
    $title = 'Forgot Password';
@endphp
@section('content')
    <div class="container-fluid">
        <div class="authentication-card login-card">
            <div class="card shadow rounded-0 overflow-hidden">
                <div class="row g-0">
                    <div class="col-lg-12">
                        <div class="card-body p-4 p-sm-5">
                            <h5 class="card-title">Forgot Password?</h5>
                            <p class="card-text mb-5">Enter your registered email Address to reset the password</p>
                            @if (session('success'))
                                <div class="alert alert-success">
                                    {{ session('success') }}
                                </div>
                            @endif
                            @if (session('error'))
                                <div class="alert alert-danger">
                                    {{ session('error') }}
                                </div>
                            @endif
                            <form class="form-body" action="{{ route('admin.send_email') }}" method="post">
                                @csrf
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label for="inputEmailid" class="form-label">Email</label>
                                        <input type="email" class="form-control form-control-lg radius-30" name="email"
                                            value="{{ old('email') }}" id="inputEmailid" placeholder="Email Address"
                                            required>
                                        @error('email')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-12">
                                        <div class="d-grid gap-3">
                                            <button type="submit" class="btn btn-lg btn-primary radius-30">Send</button>
                                            <a href="{{ route('admin.login') }}" class="btn btn-lg btn-light radius-30">Back
                                                to Login</a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="login-image-div-monkey">
        <img src="{{ asset('front') }}/assets/css/images/login_mbl_image2.png" class="monkey_image" alt="">
    </div>

    <div class="login-image-div">
        <img src="{{ asset('front') }}/assets/css/images/login_mbl_image1.png" class="footer_image" alt="">
    </div>
@endsection

{{-- <div class="form">
    @if (session('status'))
    <div class="alert alert-success" role="alert">
        {{ session('status') }}
    </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="form-group">
            <label for="email">{{ __('E-Mail Address') }}</label>

            <div class="email">
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
        </div>

        <div class="form-group mb-0">
            <button type="submit" class="btn btn-primary btn-block">
                {{ __('Send Password Reset Link') }}
            </button>
        </div>
    </form>
</div> --}}
