@extends('layouts.admin')

@section('content')
    <div class="user-profile-page">
        <div class="card radius-15">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 col-lg-6 border-right">
                        <div class="d-md-flex align-items-center">

                            <div class="mb-md-0 mb-3">
                                @if (isset($edit->image))
                                    <img src="{{ check_file($edit->image, 'public/uploads/profile_images/2024/') }}"
                                        class="rounded-circle shadow" width="130" height="130" alt="" />
                                @endif
                            </div>

                            <div class="mb-md-0 mb-3">
                                @if (isset($clone_client->image))
                                    <img src="{{ check_file($clone_client->image,) }}"
                                        class="rounded-circle shadow" width="130" height="130" alt="" />
                                @endif
                            </div>

                            <div class="ms-md-6 flex-grow-1">
                                <div class="mb-1" style=" margin-left: 10px; ">

                                    <h5 class="mb-0">{{ isset($edit) ? 'Edit Client' : 'Add Client' }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end row-->

                
                
                <div class="tab-content mt-3">
                    <div class="tab-pane fade show active" id="Edit-Profile">
                        <div class="card shadow-none border mb-0 radius-15">
                            <div class="card-body">
                                <div class="form-body">
                                    <div class="row">
                                        <div class="col-12 col-lg-12 border-right">
                                            <form method="POST" action="{{ route('admin.sub_account.client-management.save', ['sub_account_id' => $sub_account_id ]) }}"
                                                class="row g-3 ajaxForm" enctype="multipart/form-data" id="first-form">
                                                @csrf
                                                <input type="hidden" value="1" name="sub_account_id">

                                                <div class="col-md-6">
                                                    <label for="inputName" class="form-label">Client Name <span
                                                            class="text-danger fw-bold">*</span></label>
                                                    <input type="text" name="client_name" id="client_name"
                                                        value="{{ $edit->client_name ?? '' }}" placeholder="Enter Name"
                                                        class="form-control" required
                                                        oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '');">
                                                </div>


                                                <div class="col-md-6">
                                                    <label for="inputName" class="form-label">Mobile <span
                                                            class="text-danger fw-bold">*</span></label>
                                                    <input type="text" name="phone_number" id="inputPhone"
                                                        value="{{ $edit->phone_number ?? '' }}" placeholder="Enter Number"
                                                        class="form-control" maxlength="8" minlength="8" pattern="\d*" required>
                                                </div>

                                                <div class="col-md-6">
                                                    <label for="inputName" class="form-label">Agency <span
                                                            class="text-danger fw-bold">*</span></label>
                                                    <select class="form-control" name="agency_id" id="agency_address" required>
                                                        <option value="" disabled selected>Select Agency</option>
                                                        @foreach ($agencies as $agency)
                                                            <option value="{{ $agency->id }}"
                                                                @if (isset($edit) && $edit->agency_id == $agency->id) selected @endif>
                                                                {{ $agency->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-md-6">
                                                    <label for="inputName" class="form-label">Package <span
                                                            class="text-danger fw-bold">*</span></label>
                                                    <select name="package" class="form-control">
                                                        <option value="Package 1"
                                                            @if (isset($edit) && $edit->package == 1) selected @endif>
                                                            Package 1
                                                        </option>
                                                        <option value="Package 2"
                                                            @if (isset($edit) && $edit->package == 2) selected @endif>
                                                            Package 2
                                                        </option>
                                                        <option value="Package 3"
                                                            @if (isset($edit) && $edit->package == 3) selected @endif>
                                                            Package 3
                                                        </option>
                                                    </select>
                                                </div>

                                                <div class="col-md-6">
                                                    <label for="inputName" class="form-label">Email <span
                                                            class="text-danger fw-bold">*</span></label>
                                                    <input type="email" name="email" id="email"
                                                        value="{{ $edit->email ?? '' }}" placeholder="Enter email"
                                                        class="form-control"
                                                        @if (isset($edit)) readonly @endif required>
                                                </div>


                                                <div class="col-md-6">
                                                    <label for="inputName" class="form-label">Address <span
                                                            class="text-danger fw-bold">*</span></label>
                                                    <input type="text" name="address" id="address"
                                                        value="{{ $edit->address ?? '' }}" placeholder="Enter address"
                                                        class="form-control" required>
                                                </div>

                                                <div class="col-12 col-lg-6">
                                                    <label for="inputName" class="form-label">Industry <span
                                                            class="text-danger fw-bold">*</span></label>
                                                    <div class="ms-auto position-relative">

                                                        <select name="industry_id" id="industry" class="form-control"
                                                            required>
                                                            <option value="">Select Industry</option>
                                                            @foreach ($industries as $industry)
                                                                <option value="{{ $industry->id }}"
                                                                    @if (isset($edit) && $edit->industry_id == $industry->id) selected @endif>
                                                                    {{ $industry->industries }}
                                                                </option>
                                                            @endforeach

                                                        </select>
                                                    </div>
                                                    @error('agency')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>

                                                <div class="col-xl-6">
                                                    <label for="inputName" class="form-label">Professional Image <span
                                                            class="text-danger fw-bold">* </span><span class="text-danger">(Image Type jpg,jpeg,png)</span></label>
                                                    @if (isset($edit) && !empty($edit->image))
                                                    @endif
                                                    <input type="file" id="profile_image" name="image"
                                                        class="form-control" accept=".jpg, .png, .jpeg"
                                                        @if (!isset($edit)) required @endif>
                                                </div>



                                                @if (!isset($edit) && empty($edit))
                                                    <div class="col-md-6">
                                                        <label for="inputName" class="form-label">Password<span
                                                                class="text-danger fw-bold"> *</span></label>
                                                        <input type="password" value="" name="password"
                                                            class="form-control" required>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label">Confirm Password</label>
                                                        <label for="inputName" class="form-label">Client Name<span
                                                                class="text-danger fw-bold"> *</span></label>
                                                        <input type="password" value="" name="confirm_password"
                                                            class="form-control" required>
                                                    </div>
                                                @endif

                                                <div class="form-group mb-3 text-right">
                                                    <input type="hidden" name="id"
                                                        value="{{ $edit->hashid ?? '' }}">
                                                        <button type="submit" class="btn btn-primary px-5 form-submit-btn">Submit</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <br>

                {{-- another for pass update  --}}
                @if (isset($edit) && !empty($edit))
                    <div class="tab-content mt-3">
                        <div class="tab-pane fade show active" id="Edit-Client">
                            <div class="card shadow-none border mb-0 radius-15">
                                <div class="card-body">
                                    <h4>Change Password</h4>
                                    <div class="form-body">
                                        <form action="{{ route('admin.sub_account.client-management.update-password', ['sub_account_id' => $sub_account_id ]) }}" id="second_form" method="post" class="second">
                                            @csrf
                                            <div class="row">
                                                <div class="col-md-6 mt-2">
                                                    <label class="form-label">Password <span class="text-danger">*</span></label>
                                                    <div class="ms-auto position-relative">
                                                        <input type="password" name="password" id="password" class="form-control">
                                                        <button type="button" class="btn position-absolute top-50 end-0 translate-middle-y me-3" style="border: none;" id="togglePassword">
                                                            <i class="bi bi-eye-slash" id="eyeIcon"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-6 mt-2">
                                                    <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                                    <div class="ms-auto position-relative">
                                                        <input type="password" name="password_confirmation"
                                                            class="form-control" id="confirmPassword" required>
                                                        <button type="button" class="btn position-absolute top-50 end-0 translate-middle-y me-3" style="border: none;" id="toggleConfirmPassword">
                                                            <i class="bi bi-eye-slash" id="eyeIcon"></i>
                                                        </button>
                                                    </div>
                                                </div>

                                                <div id="message"></div>
                                                <input type="hidden" name="client_id"
                                                    value="{{ $edit->hashid ?? '' }}">
                                                <div class="col-12 mt-3">
                                                    <button type="submit"
                                                        class="btn btn-primary px-5 sub-button">Submit</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                
            </div>
        </div>
    </div>
@endsection

@section('page-scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>
    @if (isset($clone_client->email))
        <script>
            var id = {{ $clone_client->id }};
            var email = $("#email").val();
            var atIndex = email.indexOf('@');
            var newId = id + 1;
            var modifiedEmail = email.substring(0, atIndex) + newId + email.substring(atIndex);
            $("#email").val(modifiedEmail);
        </script>
    @endif
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
            $('.ajaxForm').submit(function(e) {
                e.preventDefault();
                // validations = $(".ajaxForm").validate();
                // if (validations.errorList.length != 0) {
                //     return false;
                // }
                var url = $(this).attr('action');
                var param = new FormData(this);
                var files = $('#profile_image')[0].files[0] ?? '';
                param.append('profile_image', files);

                my_ajax(url, param, 'post', function(res) {

                }, true);
            }); 
            
            //change password function
            $('#second_form').submit(function(e) {
                e.preventDefault();
               
                var password = $('#password').val();
                var confirmPassword = $('#confirmPassword').val();

                // Check if password and confirm password match
                if (password != confirmPassword) {
                $('#message').html('Passwords do not match!').css('color', 'red'); 
                event.preventDefault(); // Prevent form submission
                } else if (password.length < 6) {
                $('#message').html('Password should be at least 6 characters long.').css('color', 'red'); 
                event.preventDefault(); // Prevent form submission
                } else {
                    validations = $("#second_form").validate();
                    if (validations.errorList.length != 0) {
                        submitBtn2.prop('disabled',false);
                        submitBtn2.html('Submit');
                        return false;
                    }
                    var url = $(this).attr('action');
                    var param = new FormData(this);
                    var files = $('#profile_image')[0].files[0] ?? '';
                    param.append('profile_image', files);
                    my_ajax(url, param, 'post','',false, function(res) {

                    }, true);
                }    
            })

            // Toggle password visibility
            $('#togglePassword').on('click', function() {
                const passwordField = $('#password');
                const type = passwordField.attr('type') === 'password' ? 'text' : 'password';
                passwordField.attr('type', type);

                // Toggle icon
                $(this).find('i').toggleClass('bi-eye').toggleClass('bi-eye-slash');
            });

            $('#toggleConfirmPassword').on('click', function() {
                const passwordField = $('#confirmPassword');
                const type = passwordField.attr('type') === 'password' ? 'text' : 'password';
                passwordField.attr('type', type);

                // Toggle icon
                $(this).find('i').toggleClass('bi-eye').toggleClass('bi-eye-slash');
            });
        });

        $(document).on('change', '#agency_address', function () {

            var url = "{{ route('admin.sub_account.client-management.get_agency_address', ['sub_account_id' => $sub_account_id ]) }}"
            var agency_id = $(this).val();
            
            $.ajax({
                url: url,
                method: "post",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    agency_id : agency_id,
                },
                dataType: "json",
                success: function (response) {
                    if (response['success'] !== undefined && response['address'] !== undefined) {
                        $('#address').val("");
                        $('#address').val(response.address);
                    }
                    
                },
                error: function(xhr, status, error) {
                    $('#address').val("");
                    console.error(xhr.responseText);
                }
            });
        });
    </script>



@endsection
