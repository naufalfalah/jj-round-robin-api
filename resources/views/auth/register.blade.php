@extends('layouts.user_auth')
@section('title', 'Register')
@php
    $title = 'register';
@endphp
@section('page-css')
<style>
    @media only screen and (max-width: 768px) {
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
   width: 95% !important;
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
.error{
    color:red;
}
</style>
@endsection
@section('content')
    <!-- <div class="container-fluid">
        <div class="authentication-card"> -->
            <div style="width: 100%;justify-content: center; display: flex;">
            <div class="card shadow rounded-0 overflow-hidden" style="max-width:700px; margin-top:50px">
                <div class="row g-0">
                    <div class="col-lg-12">
                        <div class="card-body p-3 pe-sm-4 ps-sm-4">
                            <h5 class="card-title">Sign Up</h5>
                            <p class="card-text mb-3">See your growth and get consulting support!</p>
                            <form class="form-body registerForm" method="POST" novalidate="novalidate" action="{{ route('auth.register.submit') }}"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="row g-2">
                                    <div class="col-12 col-lg-6">
                                        <label for="inputName" class="form-label">Client Name<span
                                                class="text-danger fw-bold">*</span></label>
                                        <div class="ms-auto position-relative">
                                            <div class="position-absolute top-50 translate-middle-y search-icon px-3"><i
                                                    class="bi bi-person-circle"></i></div>
                                            <input type="text" class="form-control radius-30 ps-5 validate-input" name="client_name"
                                                value="{{ old('client_name') }}" id="inputName"
                                                placeholder="Enter Client Name" required>
                                        </div>
                                        <label id="inputName-error" class="error" for="inputName" style="display: none;"></label>
                                        @error('client_name')
                                            <span class="text-danger error-message">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-12 col-lg-6">
                                        <label for="inputName" class="form-label">Mobile<span
                                                class="text-danger fw-bold">*</span></label>
                                        <div class="ms-auto position-relative">
                                            <div class="position-absolute top-50 translate-middle-y search-icon px-3"><i class="bi bi-phone"></i></div>
                                                <input type="text" class="form-control radius-30 ps-5 validate-input" value="{{ old('phone_number') }}" maxlength="8" minlength="8" pattern="\d*" name="phone_number" id="inputPhone" placeholder="Enter Phone Number" required>
                                        </div>
                                        <label id="inputPhone-error" class="error" for="inputPhone" style="display: none;"></label>

                  
                                        @error('phone_number')
                                            <span class="text-danger error-message">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-lg-6">
                                        <label for="inputName" class="form-label">Agency<span
                                                class="text-danger fw-bold">*</span></label>
                                        <div class="ms-auto position-relative">
                                            <div class="position-absolute top-50 translate-middle-y search-icon px-3"><i
                                                    class="bi bi-person-circle"></i></div>
                                            <select name="agency" id="agency" class="form-control radius-30 ps-5 validate-input" required>
                                                <option value="">Select Agency</option>
                                                @foreach ($agencies as $val)
                                                    <option value="{{ $val->id }}" {{ old('agency') == $val->id ? 'selected' : '' }}>{{ $val->name }}</option>
                                                @endforeach
                                                <option value="other" {{ old('agency') == 'other' ? 'selected' : '' }}>Other</option>
                                            </select>
                                        </div>
                                        <label id="agency-error" class="error" for="agency" style="display: none;"></label>
                                        @error('agency')
                                            <span class="text-danger error-message">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-lg-6" id="other_agency_name_input" style="display:none">
                                        <label for="inputName" class="form-label">Enter Agency Name<span
                                                class="text-danger fw-bold">*</span></label>
                                        <div class="ms-auto position-relative">
                                            <div class="position-absolute top-50 translate-middle-y search-icon px-3"><i
                                                    class="bi bi-person-circle"></i></div>
                                            <input type="text" class="form-control radius-30 ps-5" id="other_agency_name" value="{{ old('other_agency_name') }}" placeholder="Enter Agency Name" name="other_agency_name" required>
                                        </div>
                                        <label id="other_agency_name-error" class="error" for="other_agency_name" style="display: none;"></label>
                                        @error('other_agency_name')
                                            <span class="text-danger error-message">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-lg-6">
                                        <label for="inputName" class="form-label">Package<span
                                                class="text-danger fw-bold">*</span></label>
                                        <div class="ms-auto position-relative">
                                            <div class="position-absolute top-50 translate-middle-y search-icon px-3"><i
                                                    class="lni lni-package"></i></div>
                                            <select name="package" id="" class="form-control radius-30 ps-5 validate-input"
                                                required>
                                                <option value="" selected>Select Package</option>
                                                <option value="6_months" {{ old('package') == '6_months' ? 'selected' : '' }}>6 Months subscription</option>
                                                <option value="12_months" {{ old('package') == '12_months' ? 'selected' : '' }}>12 Months subscription</option>
                                            </select>
                                        </div>
                                        <label id="package-error" class="error" for="package" style="display: none;"></label>
                                        @error('package')
                                            <span class="text-danger error-message">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-lg-6">
                                        <label for="inputEmailAddress" class="form-label">Email<span
                                                class="text-danger fw-bold">*</span></label>
                                        <div class="ms-auto position-relative">
                                            <div class="position-absolute top-50 translate-middle-y search-icon px-3"><i
                                                    class="bi bi-envelope-fill"></i></div>
                                            <input type="email" class="form-control radius-30 ps-5 validate-input" name="email"
                                                value="{{ old('email') }}" id="inputEmailAddress" placeholder="Email Email"
                                                required>
                                        </div>
                                        <label id="inputEmailAddress-error" class="error" for="inputEmailAddress" style="display: none;"></label>
                                        <span class="text-danger error-message" id="email_error"></span>
                                        @error('email')
                                            <span class="text-danger error-message">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-lg-6">
                                        <label for="inputName" class="form-label">Address<span
                                                class="text-danger fw-bold">*</span></label>
                                        <div class="ms-auto position-relative">
                                            <div class="position-absolute top-50 translate-middle-y search-icon px-3"><i
                                                    class="lni lni-home"></i></div>
                                            <input type="text" class="form-control radius-30 ps-5 validate-input" name="address"
                                                value="{{ old('address') }}" id="addressName" placeholder="Enter Address"
                                                required>
                                        </div>
                                        <label id="addressName-error" class="error" for="addressName" style="display: none;"></label>
                                        @error('address')
                                            <span class="text-danger error-message">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-lg-6">
                                        <label for="inputName" class="form-label">Industry<span
                                                class="text-danger fw-bold">*</span></label>
                                        <div class="ms-auto position-relative">
                                            <div class="position-absolute top-50 translate-middle-y search-icon px-3"><i
                                                    class="bi bi-person-circle"></i></div>
                                            <select name="industry" id="industry" class="form-control radius-30 ps-5 validate-input" required>
                                                <option value="">Select Industry</option>
                                                @foreach ($industries as $val)
                                                    <option value="{{ $val->id }}" {{ old('industry') == $val->id ? 'selected' : '' }}>{{ $val->industries }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <label id="industry-error" class="error" for="industry" style="display: none;"></label>
                                        @error('agency')
                                            <span class="text-danger error-message">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-lg-6">
                                        <label for="inputName" class="form-label">Professional image<span
                                                class="text-danger fw-bold">*</span></label>
                                        <div class="ms-auto position-relative">
                                            <div class="position-absolute top-50 translate-middle-y search-icon px-3"><i
                                                    class="bi bi-person-circle"></i></div>
                                            <input type="file" class="form-control radius-30 ps-5 validate-input" name="image"
                                                placeholder="{{session('temp_image')}}" id="profile_image" value="{{session('temp_image')}}" required>
                                        </div>
                                        <label id="profile_image-error" class="error" for="profile_image" style="display: none;"></label>
                                        @error('image')
                                            <span class="text-danger error-message">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-12 col-lg-12">
                                        <label for="inputNewPassword" class="form-label">
                                            Password <span class="text-danger fw-bold">*</span>
                                        </label>
                                        <div class="ms-auto position-relative">
                                            <div class="position-absolute top-50 translate-middle-y search-icon px-3">
                                                <i class="bi bi-lock-fill"></i>
                                            </div>
                                            <input type="password" class="form-control radius-30 ps-5 pe-5 validate-input" name="password"
                                                value="{{ old('password') }}" id="inputNewPassword" placeholder="Enter Password" required>
                                            <button type="button" class="btn position-absolute top-50 end-0 translate-middle-y me-3" style="border: none;" id="togglePassword">
                                                <i class="bi bi-eye-slash" id="eyeIcon"></i>
                                            </button>
                                        </div>
                                        @error('password')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-12">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" name="iagree" {{ old('iagree') == 'on' ? 'checked' : '' }}  type="checkbox" id="flexSwitchCheckChecked"
                                                required>
                                            <label class="form-check-label" for="flexSwitchCheckChecked">I Agree to the
                                                Terms & Conditions</label>
                                        </div>
                                        <label id="iagree-error" class="error" for="iagree"></label>
                                    </div>
                                    <div class="col-6">
                                        <p class="mb-0 sm-txt-log-reg">Already have an account? <a
                                                href="{{ route('login') }}">Sign in here</a></p>
                                    </div>
                                    <div class="col-6">
                                        <button type="submit" id="submitButton" class="btn btn-primary float-end radius-30 reg-form-submit-btn">Sign
                                            Up</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        <!-- </div>
    </div> -->

    <div>
        <img width="100%"  src="{{asset('front')}}/assets/images/reg-logo.png" alt="">
    </div>

@endsection
@section('page-script')
    <script>
        document.getElementById('inputPhone').addEventListener('input', function(e) {
            // Remove any non-digit characters
            this.value = this.value.replace(/\D/g, '');

            // Ensure the number starts with 9 or 8
            if (this.value.length > 0 && this.value.charAt(0) !== '9' && this.value.charAt(0) !== '8') {
                this.value = '9' + this.value.substring(1); // Default to 9 if neither 9 nor 8 is entered
            }
            
            // Limit the input to 8 digits
            if (this.value.length > 8) {
                this.value = this.value.substring(0, 8);
            }
        });

        $(document).ready(function() {
            $('#flexSwitchCheckChecked').change(function(){
                if($(this).is(':checked')) {
                    $('.terms_conditions').text("");
                }
            });

            $(document).ready(function(){
                if ("{{ old('agency') }}" == 'other') {
                    $('#other_agency_name_input').show();
                }

                $('.validate-input').on('input', function(){
                    var $this = $(this);
                    var hasValue = $this.val().length > 0;
                    var $errorSpan = $this.siblings('.error-message');

                    if (hasValue) {
                        $errorSpan.hide();
                    }
                });
            });

            function validatePassword() {
                var password = $("#inputNewPassword").val();
                var confirmPassword = $("#inputConfirmPassword").val();
                var passwordRegex = /^(?=.*[0-9])(?=.*[!@#$%^&*])[a-zA-Z0-9!@#$%^&*]{8,12}$/;
                if (password !== confirmPassword) {
                    $('.confirm_password').text("The confirm password and password must match.");
                    return false;
                } else if (!passwordRegex.test(password)) {
                    $('.confirm_password').text("");
                    $('.password_error').text("Password must be 8-12 characters long and contain at least one number and one special character.");
                    return false;
                }
                return true;
            }

            // Validate email before AJAX request
            function validateEmail() {
                var email = $('#inputEmailAddress').val();
                var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    $('#email_error').html('Invalid email format.');
                    return false;
                }
                return true;
            }

            // $("#submitButton").click(function(e) {
            //     e.preventDefault();
                
            //     var fileInput = $('#profile_image');
            //     var filePath = fileInput.val();
            //     var allowedExtensions = /(\.jpg|\.jpeg|\.png)$/i;
            //     var maxSize = 2 * 1024 * 1024; // 2MB
            //     var errorMsg = '';
            //     if (!filePath) {
            //         errorMsg = 'Please select an image file.';
            //         $('#img_error').html(errorMsg);
            //         return false;
            //     } else {
            //         $('#img_error').html('');
            //     }
            //     $.ajax({
            //         url: "{{route('check_email')}}",
            //         type: 'POST',
            //         data: {
            //             emial: $('#inputEmailAddress').val(),
            //             _token: '{{ csrf_token() }}' // Include CSRF token if required
            //         },
            //         success: function(response) {
            //            if(response){
            //                 $('#email_error').html('The email has already been taken.'); 
            //                 return false;
            //            }else{
            //                 $('#email_error').html(''); 
            //            }
            //         },
            //         error: function(xhr, status, error) {
            //             // console.error('Error saving Transaction ID:', error);
            //         }
            //     }); 
            //     if ($("#flexSwitchCheckChecked").is(":checked")) {
            //         $('.terms_conditions').text("");
            //         // $(".registerForm").submit();
            //         // if (validatePassword()) {
            //         //     $('.password_error').text("");
            //         //     $('.terms_conditions').text("");
            //         //     $('.confirm_password').text("");
            //         //     $(".registerForm").submit();
            //         // }
            //     } else {
            //         $('.terms_conditions').text("Please agree to the Terms & Conditions.");
            //     }
            // });
        });
    </script>
    <script>
        $('#agency').change(function(params) {
            agency = $(this).val(); 
            if(agency == 'other'){
                $('#other_agency_name_input').show();
            }else{
                $('#other_agency_name_input').hide();
            }
        })
        
        validations = $(".registerForm").validate();
        $('.registerForm').submit(function(e) {
            e.preventDefault();
            var submitBtn = $('.reg-form-submit-btn');
            submitBtn.prop('disabled',true);
            submitBtn.html('<span class="d-flex align-items-center">Saving...</span>');
        
            var url = $(this).attr('action');
            validations = $(".registerForm").validate();
            if (validations.errorList.length != 0) {
                submitBtn.prop('disabled',false);
                submitBtn.html('Sign Up');
                return false;
            }
            var formData = new FormData(this);
            my_ajax(url, formData, 'post', function(res) {
                submitBtn.prop('disabled',false);
                submitBtn.html('Sign Up');
            }, true);
        })
            
        document.addEventListener('DOMContentLoaded', function() {
            const togglePasswordButton = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('inputNewPassword');
            const eyeIcon = document.getElementById('eyeIcon');

            togglePasswordButton.addEventListener('click', function() {
                // Toggle password visibility
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);

                // Toggle eye icon
                if (type === 'password') {
                    eyeIcon.classList.remove('bi-eye');
                    eyeIcon.classList.add('bi-eye-slash');
                } else {
                    eyeIcon.classList.remove('bi-eye-slash');
                    eyeIcon.classList.add('bi-eye');
                }
            });
        });
    </script>

@endsection