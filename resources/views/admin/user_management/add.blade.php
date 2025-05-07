@extends('layouts.admin')

@section('content')


<div class="row">
    <div class="col-xl-12 mx-auto">
        <div class="card">
            <div class="card-body">  
                <div class="border p-4 rounded">
                    <div class="card-title d-flex align-items-center">
                        <h5 class="mb-0">{{$title}}</h5>
                    </div>
                    <hr/>
                    <form method="POST" action="{{route('admin.user-management.save')}}" class="row g-3 ajaxForm" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="inputName" class="col-form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="inputName" value="{{ $edit->name ?? '' }}" name="name" placeholder="Enter Full Name" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="email" class="col-form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" value="{{ $edit->email ?? '' }}" name="email" placeholder="Enter Email" required>
                            </div>

                            <div class="col-12 mt-2">
                                <label class="form-label">Profile Image <span class="text-danger">(jpeg, jpg, png. Image will be resized into 400x400px)</span></label>
                                <input type="file" id="profile_image" name="profile_image" class="form-control">
                            </div>

                        </div>

                        <div class="row">
                            <label class="col-sm-12 col-form-label"></label>
                            <input type="hidden" name="id" value="{{ $edit->id ?? null }}">
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-12 mx-auto">
        <div class="card">
            <div class="card-body">
                <div class="border p-4 rounded">
                    <div class="card-title d-flex align-items-center">
                        <h5 class="mb-0">Login Detail</h5>
                    </div>
                    <hr/>
                    <div class="form-body">
                        <div class="row">
                            <div class="col-md-6 mt-2">
                                <label class="form-label">Username <span class="text-danger">*</span></label>
                                <input type="text" value="{{$edit->username ?? ''}}" name="username"
                                    class="form-control" placeholder="Enter User Name" required>
                            </div>

                            <div class="col-md-6 mt-2" id="user_types">
                                <label class="form-label">User Type <span class="text-danger">*</span></label>
                                <select class="form-control" data-parsley-required name="user_type" id="user_type" required onchange="changed_user_type(this.value)">
                                    <option value="">Select User Type</option>
                                    <option {{isset($edit) && $edit->user_type == 'admin' ? 'selected' : ''}} value="admin">Admin</option>
                                    <option {{isset($edit) && $edit->user_type == 'team_lead' ? 'selected' : ''}} value="team_lead">Team Lead</option>
                                    <option {{isset($edit) && $edit->user_type == 'normal' ? 'selected' : ''}} value="normal">Normal</option>
                                </select>
                            </div>

                            <div class="col-12 mt-2" id="permissions_wrap" style="display: {{ (isset($edit) && ($edit->user_type == 'normal' || $edit->user_type == 'team_lead')) ? 'block' : 'none' }}">
                                <label class="form-label">User Role <span class="text-danger">*</span></label>
                                <select class="form-control" name="role" id="role">
                                    <option value="">Select Role </option>
                                    @foreach ($roles as $role)
                                    <option value="{{$role->id}}" {{isset($edit) && $role->id == $edit->role_id ? 'selected' : ''}}>{{$role->name}}</option>
                                    @endforeach
                                </select>
                            </div>

                            @if (!isset($edit) && empty($edit))
                            <div class="col-12 mt-2">
                                <label class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" value="" name="password" class="form-control" placeholder="Enter password" required>
                            </div>
                            @endif

                            <input type="hidden" name="id" value="{{$edit->id ?? ''}}">
                            <div class="col-sm-9 mt-3">
                                <button type="submit" class="btn btn-primary px-5">Submit</button>
                            </div>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


@if (isset($edit))
<div class="tab-content mt-3">
    <div class="tab-pane fade show active" id="Edit-Profile">
        <div class="card shadow-none border mb-0 radius-15">
            <div class="card-body">
                <h4>Change Password</h4>
                <div class="form-body">
                    <form action="{{route('admin.user-management.update-password')}}" id="ajaxForm" method="post">
                        @csrf
                    <div class="row">
                        <div class="col-md-6 mt-2">
                            <label class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" name="password"
                                class="form-control">
                        </div>
                        <div class="col-md-6 mt-2">
                            <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" class="form-control"
                                required>
                        </div>

                        <input type="hidden" name="user_id" value="{{$edit->hashid ?? ''}}">
                        <div class="col-12 mt-3">
                            <button type="submit" class="btn btn-primary px-5">Submit</button>
                        </div>
                    </div>
                </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@endsection
@section('page-scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>

<script>

    function changed_user_type(type) {
        if (type != '') {
            if (type != 'admin') {
                $("#permissions_wrap").show();
                $("#role").attr('required', 'required');

            } else {
                $("#role").removeAttr('required');
                $("#permissions_wrap").hide();
            }
        }else{
            $("#role").removeAttr('required');
            $("#permissions_wrap").hide();
        }

    }

    $(document).ready(function() {
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


        validations2 = $("#ajaxForm").validate();
        $('#ajaxForm').submit(function(e) {
            e.preventDefault();
            validations2 = $("#ajaxForm").validate();
            if (validations2.errorList.length != 0) {
                return false;
            }
            var url = $(this).attr('action');
            var param = new FormData(this);
            my_ajax(url, param, 'post', function(res) {

            },true);
        })
    });
</script>
@endsection
