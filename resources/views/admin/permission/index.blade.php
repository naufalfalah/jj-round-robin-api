@extends('layouts.admin')

@section('content')

<div class="row">
    <div class="col-xl-12 mx-auto">
        <div class="card">
            <div class="card-body">
                <div class="border p-4 rounded">
                    <div class="card-title d-flex align-items-center">
                        <h5 class="mb-0">{{isset($edit) ? $title : 'Add Permissions'}}</h5>
                    </div>
                    <hr/>
                    <form method="POST" action="{{ route('admin.permission.save') }}"
                        class="row g-3 ajaxForm">
                        @csrf
                        <div class="col-md-6">
                            <label for="">Permission type<span
                                class="text-danger">*</span></label>
                            <select name="permission_type" id="permission_type" class="form-control">
                            <option value="">Select Permission Type</option>
                            @foreach ($permission_type as $items)
                                <option value="{{ $items->id }}"
                                    {{ isset($edit) && $edit->permission_type_id == $items->id ? 'selected' : null }}>
                                    {{ $items->permission_type }}</option>
                            @endforeach     
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="">Name<span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" value="{{ $edit->name ?? '' }}" placeholder="Enter Name" class="form-control" required>
                        </div>
                        <div class="form-group mb-3 text-right">
                            <input type="hidden" name="id" value="{{ $edit->hashid ?? null }}">
                            <button type="submit" class="btn btn-primary px-5 form-submit-btn">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div> 

<div class="row">
    <div class="col-xl-12 mx-auto">
        <div class="card">
            <div class="card-body">
                <div class="card-title d-flex align-items-center">
                    <h5 class="">All Permissions</h5>
                </div>
                    <hr>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th class="text-uppercase ps-2 text-secondary text-xxs font-weight-bolder opacity-7">S NO:</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Permission Type</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Name</th>
                                <th class="text-uppercase ps-2 text-secondary text-xxs font-weight-bolder opacity-7">Slug</th>
                                <th class="text-uppercase ps-2 text-secondary text-xxs font-weight-bolder opacity-7">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $key=>$val)
                                <tr>
                                    <td>
                                        <p class="text-sm font-weight-bold mb-0">{{ $key+1}}</p>
                                    </td>
                                    <td>
                                        <p class="text-sm font-weight-bold mb-0">{{$val->permission_type->permission_type}}</p>
                                    </td>
                                    <td>
                                        <p class="text-sm font-weight-bold mb-0">{{ $val->name}}</p>
                                    </td>
                                    <td>
                                        <p class="text-sm font-weight-bold mb-0">{{ $val->slug}}</p>
                                    </td>
                                    <td>
                                        <div class="table-actions d-flex align-items-center gap-3 fs-6">
                                            <a href="{{route('admin.permission.edit', $val->hashid)}}" class="text-warning" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Edit"><i class="bi bi-pencil-fill"></i></a>
                                            <a href="javascript:void(0)" class="text-danger" onclick="ajaxRequest(this)" data-url="{{route('admin.permission.delete',$val->id)}}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"><i class="bi bi-trash-fill"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection
@section('page-scripts')
    <script src="{{ asset('front') }}assets/plugins/datatable/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.table').DataTable();
        });
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

                }, true);
            })
        });
    </script>
@endsection
