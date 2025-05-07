@extends('layouts.front')

@section('content')
<div class="user-profile-page">
    <div class="card radius-15">
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-lg-12 border-right">
                    <div class="d-md-flex align-items-center">
                        <div class="mb-md-0 mb-3">
                            <img src="{{check_file($edit->image,'user')}}" class="rounded-circle shadow" width="130" height="130" alt="" />
                        </div>
                        <div class="ms-md-4 flex-grow-1">
                            <div class="d-flex align-items-center mb-1">
                                <h4 class="mb-0">{{$edit->client_name}}</h4>
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
                                        <form method="POST" action="{{route('user.profile.update')}}" class="row g-3 ajaxForm" enctype="multipart/form-data">
                                            @csrf
                                            <div class="col-md-6 col-12">
                                                <label class="form-label">Client Name <span class="text-danger fw-bold">*</span></label>
                                                <input type="text" name="client_name" value="{{$edit->client_name}}" class="form-control" required>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <label class="form-label">Mobile <span class="text-danger fw-bold">*</span></label>
                                                <input type="text" name="phone_number" value="{{$edit->phone_number}}" oninput="this.value = this.value.replace(/[^0-9]/g, '');" class="form-control" required>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <label class="form-label">Agency <span class="text-danger fw-bold">*</span></label>
                                                <select name="agency" id="agency" class="form-control" required>
                                                    <option value="">Select Agency</option>
                                                    @if($user_agencies->count() != 0 )
                                                    <option value="{{$user_agencies->id}}" selected>{{$user_agencies->name}}</option>
                                                    @endif
                                                    @foreach ($agencies as $val)
                                                    <option value="{{ $val->id }}" {{ (isset($edit) && $edit->agency_id == $val->id) ? 'selected' : '' }}>{{ $val->name }}</option>
                                                    @endforeach
                                                    <option value="other">Other</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <label class="form-label">Package <span class="text-danger fw-bold">*</span></label>
                                                <select name="package" id="" class="form-select mb-3" required>
                                                    <option value="">Select Package</option>
                                                    <option value="6_months" {{ (isset($edit) && $edit->package == '6_months') ? 'selected' : '' }}>6 Months subscription</option>
                                                    <option value="12_months" {{ (isset($edit) && $edit->package == 'package_2') ? 'selected' : '' }}>12 Months subscription</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <label class="form-label">Email <span class="text-danger fw-bold">*</span></label>
                                                <input type="text" value="{{$edit->email}}" readonly class="form-control">
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <label class="form-label">Address <span class="text-danger fw-bold">*</span></label>
                                                <input type="text" value="{{$edit->address}}" name="address" class="form-control" required>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <label class="form-label">Profile Image <span class="text-danger">(File Type: png,jpeg,jpg,)</span></label>
                                                <input type="file" id="profile_image" name="profile_image" class="form-control">
                                            </div>

                                            <div class="col-md-6 col-12">
                                                <label class="form-label">Industry <span class="text-danger fw-bold">*</span></label>
                                                <select name="industry" id="industry" class="form-select mb-3" required>
                                                    <option value="">Select Industry</option>
                                                    @foreach ($industries as $industry)
                                                    <option value="{{ $industry->id }}" {{ (isset($edit) && $edit->industry_id == $industry->id) ? 'selected' : '' }}>{{ $industry->industries }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <input type="hidden" name="id" value="{{$edit->id}}">
                                            <div class="col-12">
                                                <button type="submit" class="btn btn-primary form-submit-btn">Submit</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="tab-content mt-3">
    <div class="tab-pane fade show active" id="Edit-Profile">
        <div class="card shadow-none border mb-0 radius-15">
            <div class="card-body">
                <h4>Change Password</h4>
                <div class="form-body">
                    <form action="{{route('user.profile.update_password')}}" id="ajaxPasswordForm" method="post">
                    @csrf
                    <div class="row">
                        <div class="col-12 mt-2">
                            <label class="form-label">Current Password <span class="text-danger fw-bold">*</span></label>
                            <div class="ms-auto position-relative">
                                <input type="password" name="current_password" id="currentPassword" class="form-control pe-5" placeholder="Enter Current Password" required>

                                <button type="button" class="btn position-absolute top-50 end-0 translate-middle-y me-3" style="border: none;" id="toggleCurrentPassword">
                                    <i class="bi bi-eye-slash" id="eyeIcon"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6 col-12 mt-2">
                            <label class="form-label">New Password <span class="text-danger fw-bold">*</span></label>
                            <div class="ms-auto position-relative">
                                <input type="password" name="password" id="newPassword" class="form-control pe-5" placeholder="Enter New Password" required>

                                <button type="button" class="btn position-absolute top-50 end-0 translate-middle-y me-3" style="border: none;" id="toggleNewPassword">
                                    <i class="bi bi-eye-slash" id="eyeIcon"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6 col-12 mt-2">
                            <label class="form-label">Confirm Password <span class="text-danger fw-bold">*</span></label>
                            <div class="ms-auto position-relative">
                                <input type="password" name="password_confirmation" id="confirmPassword" class="form-control pe-5" placeholder="Confirm New Password" required>

                                <button type="button" class="btn position-absolute top-50 end-0 translate-middle-y me-3" style="border: none;" id="toggleConfirmPassword">
                                    <i class="bi bi-eye-slash" id="eyeIcon"></i>
                                </button>
                            </div>
                        </div>

                        <input type="hidden" name="user_id" value="{{$edit->hashid ?? ''}}">
                        <div class="col-12 mt-3">
                            <button type="submit" class="btn btn-primary btn-loader">Submit</button>
                        </div>
                    </div>
                </form>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection
@section('page-scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>

<script>
    $(document).ready(function() {
        // Toggle password visibility
        $('#toggleCurrentPassword').on('click', function() {
            const passwordField = $('#currentPassword');
            const type = passwordField.attr('type') === 'password' ? 'text' : 'password';
            passwordField.attr('type', type);

            // Toggle icon
            $(this).find('i').toggleClass('bi-eye').toggleClass('bi-eye-slash');
        });

        $('#toggleNewPassword').on('click', function() {
            const passwordField = $('#newPassword');
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
        
        validations = $(".ajaxForm").validate();
        $('.ajaxForm').submit(function(e) {
            e.preventDefault();
            validations = $(".ajaxForm").validate();
            if (validations.errorList.length != 0) {
                return false;
            }
            var url = $(this).attr('action');
            var param = new FormData(this);
            var files = $('#profile_image')[0].files[0] ?? '';
            param.append('profile_image', files);
            my_ajax(url, param, 'post', function(res) {

            },true);
        });

        validations = $(".ajaxPasswordForm").validate();
        $('#ajaxPasswordForm').submit(function(e) {
            e.preventDefault();
            validations = $("#ajaxPasswordForm").validate();
            if (validations.errorList.length != 0) {
                return false;
            }
            passwordLoader('show');
            var url = $(this).attr('action');
            var param = new FormData(this);
            $.ajax({
                url: url,
                method: 'POST',
                data: param,
                contentType: false,
                processData: false,
                dataType: "json",
                before: function() {
                    passwordLoader('show');
                },
                complete: function () {
                    passwordLoader('hide');
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    passwordLoader('hide');
                    ajaxErrorHandling(jqXHR, errorThrown);
                },
                success: function (data) {
                    var timer = 3000;

                    if (data['reload'] !== undefined) {
                        toast(data['success'], "Success!", 'success', timer);
                        setTimeout(function () {
                            window.location.reload(true);
                        }, 600);
                        return false;
                    }

                    if (data['error'] !== undefined) {
                        toast(data['error'], "Error!", 'error');
                        return false;
                    }

                    if (data['errors'] !== undefined) {
                        multiple_errors_ajax_handling(data['errors']);
                    }

                    if (data['success'] !== undefined) {
                        toast(data['success'], "Success!", 'success', timer);
                    }
                }
            });
            })
    });

    function passwordLoader(status){
    var submitBtn = $('.btn-loader');

    if (status == 'hide') {
        submitBtn.prop('disabled',false);
        submitBtn.html('Submit');
        return;
    }

    submitBtn.prop('disabled',true);
    submitBtn.html('<span class="d-flex align-items-center"><div class="spinner-border text-dark" role="status"><span class="sr-only">Loading...</span> </div> Saving...</span>');
    return
}
</script>
@endsection
