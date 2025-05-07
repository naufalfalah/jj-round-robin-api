@extends('layouts.admin_auth')
@section('title', 'Login')
@php
    $title = 'login';
@endphp
@section('content')
    <main class="authentication-content">
        <div class="container-fluid">
            <div class="authentication-card login-card">
                <div class="card shadow mt-3 rounded-3 overflow-hidden">
                    <div class="row">
                        <div class="col-lg-12 shadow">
                            <div class="card-body">
                                <h3 class="card-title text-center">Sign In</h3>
                                <p class="card-text text-center">See your growth and get consulting support!</p>
                                <form method="POST" action="{{ route('admin.login.submit') }}" novalidate class="form-body">
                                    @csrf
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label for="inputUserName" class="form-label">Enter Username Or Email</label>
                                            <div class="ms-auto position-relative">
                                                <div class="position-absolute top-50 translate-middle-y search-icon px-3"><i
                                                        class="bi bi-person-circle"></i></div>
                                                <input type="text" name="username" class="form-control radius-30 ps-5"
                                                    value="{{ old('username') }}" id="inputUserName"
                                                    placeholder="Username Or Email" required>
                                            </div>
                                            @error('username')
                                                <span class="text-danger fw-bold">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="col-12">
                                            <label for="inputChoosePassword" class="form-label">Enter Password</label>
                                            <div class="ms-auto position-relative">
                                                <div class="position-absolute top-50 translate-middle-y search-icon px-3">
                                                    <i class="bi bi-lock-fill"></i>
                                                </div>
                                                <input type="password" class="form-control radius-30 ps-5 pe-5" name="password" id="inputChoosePassword" placeholder="Password">
                                        
                                                <button type="button" class="btn position-absolute top-50 end-0 translate-middle-y me-3" style="border: none;" id="togglePassword">
                                                    <i class="bi bi-eye-slash" id="eyeIcon"></i>
                                                </button>
                                            </div>
                                            @error('password')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6 mt-2">
                                            <input class="form-check-input" name="remember" type="checkbox"
                                                id="flexSwitchCheckChecked">
                                            <label class="form-check-label" for="flexSwitchCheckChecked">Remember Me</label>
                                        </div>
                                        <div class="col-6 mt-2">
                                            <a href="{{ route('admin.change_password') }}" class="float-end">Forgot Password
                                                ?</a>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="d-grid">
                                            <button type="submit" class="btn btn-primary radius-30 mt-3">Sign In</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <div class="login-image-div-monkey">
        <img src="{{ asset('front') }}/assets/css/images/login_mbl_image2.png" class="monkey_image" alt="">
    </div>

    <div class="login-image-div">
        <img src="{{ asset('front') }}/assets/css/images/login_mbl_image1.png" class="footer_image" alt="">
    </div>
@endsection
@section('page-script')
    <script>
        $(document).ready(function() {
            var email = localStorage.getItem('rememberedEmail');
            if (email) {
                $('#inputUserName').val(email);
                $('#flexSwitchCheckChecked').prop('checked', true);
            }else{
                $('#flexSwitchCheckChecked').prop('checked', false);
            }
            if ($('#flexSwitchCheckChecked').prop('checked') && localStorage.getItem('rememberedEmail')) {
                $('#inputUserName').val(localStorage.getItem('rememberedEmail'));
            }

            // Update rememberedEmail on "Remember Me" checkbox change
            $('#flexSwitchCheckChecked').change(function() {
                if ($(this).is(':checked')) {
                    localStorage.setItem('rememberedEmail', $('#inputUserName').val());
                } else {
                    localStorage.removeItem('rememberedEmail');
                }
            });

            // Toggle password visibility
            $('#togglePassword').on('click', function() {
                const passwordField = $('#inputChoosePassword');
                const type = passwordField.attr('type') === 'password' ? 'text' : 'password';
                passwordField.attr('type', type);

                // Toggle icon
                $(this).find('i').toggleClass('bi-eye').toggleClass('bi-eye-slash');
            });
        });
    </script>
@endsection
