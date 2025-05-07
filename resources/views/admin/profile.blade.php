@extends('layouts.admin')

@section('content')
<div class="user-profile-page">
    <div class="card radius-15">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 col-lg-6 border-right">
                    <div class="d-md-flex align-items-center">
                        <div class="mb-md-0 mb-3">
                            <img src="{{check_file($edit->image,'user')}}" class="rounded-circle shadow" width="130"
                                height="130" alt="" />
                        </div>
                        <div class="ms-md-6 flex-grow-1">
                            <div class="mb-1" style=" margin-left: 10px; ">
                                <h4 class="mb-0">{{$edit->name}}</h4>
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
                                        <form method="POST" action="{{route('admin.profile.update')}}"
                                            class="row g-3 ajaxForm" enctype="multipart/form-data">
                                            @csrf
                                            <div class="col-md-6">
                                                <label class="form-label">Name</label>
                                                <input type="text" name="name" value="{{$edit->name}}"
                                                    class="form-control">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Username</label>
                                                <input type="text" value="{{$edit->username}}" readonly
                                                    class="form-control">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Email</label>
                                                <input type="text" value="{{$edit->email}}" name="email"
                                                    class="form-control" readonly>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Password</label>
                                                <div class="ms-auto position-relative">
                                                    <input type="password" value="" name="password" class="form-control pe-5" id="password">
                                                    <button type="button" class="btn position-absolute top-50 end-0 translate-middle-y me-3" style="border: none;" id="togglePassword">
                                                        <i class="bi bi-eye-slash" id="eyeIcon"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label">Profile Image <span class="text-danger">(jpeg, jpg, png)</span></label>
                                                <input type="file" id="profile_image" name="profile_image"
                                                    class="form-control">
                                            </div>



                                            <input type="hidden" name="id" value="{{$edit->id}}">
                                            <div class="col-12">
                                                <button type="submit"
                                                    class="btn btn-info form-submit-btn">Submit</button>
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


@endsection
@section('page-scripts')

<!-- The core Firebase JS SDK is always required and must be listed first -->
{{-- <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script> --}}


<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>
<script>
    $(document).ready(function() {
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
        })

        // Toggle password visibility
        $('#togglePassword').on('click', function() {
            const passwordField = $('#password');
            const type = passwordField.attr('type') === 'password' ? 'text' : 'password';
            passwordField.attr('type', type);

            // Toggle icon
            $(this).find('i').toggleClass('bi-eye').toggleClass('bi-eye-slash');
        });
    });
</script>

@endsection
