@extends('layouts.user_auth')
@section('title', 'Password SetUp')

@section('content')
    <div class="container">
        <div class="container-fluid">
            <div class="authentication-card email-screen">
                <div class="card shadow rounded-0 overflow-hidden">
                    <div class="row g-0">
                        <div class="col-lg-12">
                            <div class="card-body p-4 p-sm-5">
                                <h5 class="card-title">Set A New Password</h5>
                                <p class="card-text mb-5">Set up a password for your account.</p>
                                <form class="form-body" method="POST" action="{{ route('auth.save_new_password') }}">
                                    @csrf
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label for="inputNewPassword" class="form-label">Password</label>
                                            <div class="ms-auto position-relative">
                                                <div class="position-absolute top-50 translate-middle-y search-icon px-3"><i
                                                        class="bi bi-lock-fill"></i></div>
                                                <input type="password" class="form-control radius-30 ps-5" name="password"
                                                    value="{{ old('password') }}" id="inputNewPassword"
                                                    placeholder="Enter Password" required>
                                            </div>
                                            @error('password')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="col-12">
                                            <label for="inputConfirmPassword" class="form-label">Confirm Password</label>
                                            <div class="ms-auto position-relative">
                                                <div class="position-absolute top-50 translate-middle-y search-icon px-3"><i
                                                        class="bi bi-lock-fill"></i></div>
                                                <input type="password" class="form-control radius-30 ps-5"
                                                    name="confirm_password" value="{{ old('confirm_password') }}"
                                                    id="inputConfirmPassword" placeholder="Confirm Password">
                                                @error('confirm_password')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="d-grid gap-3">
                                                <input type="hidden" value="{{ $user_id }}" name="user_id">
                                                <button type="submit" class="btn btn-primary radius-30">Save
                                                    Password</button>
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
    </div>

    <div class="login-image-div-monkey">
        <img src="{{ asset('front') }}/assets/css/images/login_mbl_image2.png" class="monkey_image" alt="">
    </div>

    <div class="login-image-div">
        <img src="{{ asset('front') }}/assets/css/images/login_mbl_image1.png" class="footer_image" alt="">
    </div>
@endsection
