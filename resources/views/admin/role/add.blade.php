@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">

    <div class="row">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="border p-4 rounded">
                        <div class="card-title d-flex align-items-center">
                            <h5 class="mb-0">{{$title}}</h5>
                        </div>
                        <hr/>
                    <form action="{{ route('admin.role.save') }}" class="ajaxForm" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="col-lg-12">
                            <div class="mb-3">
                                <label for="userRoleModal_name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="userRoleModal_name" placeholder="Enter Role Name" value="{{$edit->name ?? ''}}" name="name" required>
                                {{-- <select class="form-control" id="userRoleModal_name" name="name" required>
                                    <option value="">Choose Role</option>
                                    <option value="super_admin" {{ isset($edit) && $edit->name == 'super_admin' ? 'selected' : null}}>Super Admin</option>
                                    <option value="admin" {{ isset($edit) && $edit->name == 'admin' ? 'selected' : null}}>Admin</option>
                                    <option value="user" {{ isset($edit) && $edit->name == 'user' ? 'selected' : null}}>User</option>
                                </select> --}}
                            </div>
                        </div>

                        <div class="col-12">
                            <fieldset>
                                <h5 class="mb-0">Permission</h5>
                                {{-- <legend><strong>Permission</strong></legend> --}}
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Title</th>
                                                <th>Read</th>
                                                <th>Write</th>
                                                <th>Update</th>
                                                <th>Delete</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $i = 1;
                                            @endphp
                                            @foreach ($permission_types as $item)
                                            <tr>
                                                <td>
                                                    <h6>{{ $item->permission_type }}</h6>
                                                </td>


                                                @foreach ($item->permissions as $val)
                                                <td>
                                                    <div class="col-md-4">
                                                        <input type="checkbox" id="permission_{{$i}}" value="{{ $val->slug }}" title="{{ $val->name }}" name="permission[]" @if (isset($edit)) @foreach ($user_permissions as $per) {{ $val->slug  == $per->name ? 'checked' : null }} @endforeach @endif>
                                                    </div>
                                                </td>
                                                @if ($item->permissions()->count() < 3)
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                @endif
                                                @php
                                                    $i++;
                                                @endphp
                                                @endforeach

                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </fieldset>
                        </div>
                        @if (isset($edit))
                        <input type="hidden" name="role_id" value="{{ $edit->id }}">
                        @endif
                        <div class="form-group mb-3 text-right">
                            <button type="submit" class="btn btn-primary px-5 form-submit-btn">Submit</button>
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
        validations = $(".ajaxForm").validate();
        $('.ajaxForm').submit(function(e) {
            e.preventDefault();
            var url = $(this).attr('action');
            validations = $(".ajaxForm").validate();
            if (validations.errorList.length != 0) {
                return false;
            }
            var formData = new FormData(this);
            my_ajax(url, formData, 'post', function(res) {

            },true);
        })
    });
</script>
@endsection
