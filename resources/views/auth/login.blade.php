@extends('layouts.user_auth')
@section('title', 'Login')
@php
    $title = 'login';
@endphp
@section('page-css')
<style>
    @media only screen and (max-width: 1100px) {
  .desktop-image {
    display: none;
  }
  .mobile-image {
    display: block;
  }
  .coverr{
    width: 95% !important;
    margin: auto;
    margin-top: 40px;
}
  .card{
    height: 500px !important;
  }
  .sdsd{
    background-image: url('{{asset('front')}}/assets/images/login-mobile-logo.png');
    background-position: 100% 100%;
    background-size: contain;
    background-repeat: no-repeat;
  }
  .main-logo{
    display: none !important;
  }
}
</style>
@endsection
@section('content')
    <!-- <div class="container-fluid">
        <div class="authentication-card login-card"> -->
        <div class=" sdsd h-100 w-100">
            <img  src="{{asset('front')}}/assets/images/login-logo-hd.png" class="main-logo position-absolute w-100 h-100" alt="">
            <div class="coverr d-flex justify-content-center align-items-center w-75 h-75">    
                <div class="card shadow rounded-0 overflow-hidden" style="width: 700px;">
                    <div class="row g-0">
                        <div class="col-lg-12">
                            <div class="card-body p-4 p-sm-4">
                                <div class="row">
                                <div class="col-sm-7">
                                <h5 class="card-title">Sign In</h5>
                                <p class="card-text mb-4">Recognized Google Partner.</p>
                                </div>
                                <div class="col-sm-5">
                                <img src="{{ asset('front') }}/assets/images/google-login.jpg" alt="" style=" width: 250px; ">
                                </div>
                                </div>
                                <form class="form-body" method="POST" action="{{ route('auth.login.submit') }}">
                                    @csrf
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label for="inputEmailAddress" class="form-label">Email Address</label>
                                            <div class="ms-auto position-relative">
                                                <div class="position-absolute top-50 translate-middle-y search-icon px-3"><i
                                                        class="bi bi-envelope-fill"></i></div>
                                                <input type="email" class="form-control radius-30 ps-5" name="email"
                                                    autocomplete="email" value="{{ old('email') }}" id="inputEmailAddress"
                                                    placeholder="Email">
                                            </div>
                                            @error('email')
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
                                                <span class="text-danger fw-bold">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="col-6">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" name="remember" type="checkbox"
                                                    id="flexSwitchCheckChecked">
                                                <label class="form-check-label sm-txt-log-reg" for="flexSwitchCheckChecked">Remember Me</label>
                                            </div>
                                        </div>
                                        <div class="col-6 text-end sm-txt-log-reg"><a href="{{ route('password.request') }}">Forgot Password ?</a>
                                        </div>
                                        <div class="col-6">
                                            <div class="d-grid">
                                                <p class="mb-0 f sm-txt-log-reg">Don't have an account yet? <a href="{{ route('register') }}">Sign
                                                    up here</a></p>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <button type="submit" class="btn btn-primary float-end btn-md radius-30">Sign In</button>

                                        </div>
                                    </div>
                                </form>
                            </div>
                            
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>    
        <!-- </div>
    </div> -->



@endsection
@section('page-script')
    <script>
        $(document).ready(function() {
            var email = localStorage.getItem('rememberedEmail');
            if (email) {
                $('#inputEmailAddress').val(email);
                $('#flexSwitchCheckChecked').prop('checked', true);
            }else{
                $('#flexSwitchCheckChecked').prop('checked', false);
            }
            if ($('#flexSwitchCheckChecked').prop('checked') && localStorage.getItem('rememberedEmail')) {
                $('#inputEmailAddress').val(localStorage.getItem('rememberedEmail'));
            }

            // Update rememberedEmail on "Remember Me" checkbox change
            $('#flexSwitchCheckChecked').change(function() {
                if ($(this).is(':checked')) {
                    localStorage.setItem('rememberedEmail', $('#inputEmailAddress').val());
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
