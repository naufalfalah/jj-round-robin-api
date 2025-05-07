@extends('layouts.admin')

@section('content')

<div class="row">
    <div class="col-xl-12 mx-auto">
        <div class="card">
            <div class="card-body">
                <div class="border p-4 rounded">
                    <div class="card-title d-flex align-items-center">
                        <h5 class="mb-0">{{isset($edit) ? $title : 'Add Permission Types'}}</h5>
                    </div>
                    <hr/>
                    <form method="POST" action="{{ route('admin.permission_type.save') }}" class="row g-3 ajaxForm">
                        @csrf
                        <div class="row">
                            <div class="form-group mb-3 mt-3 col-md-6">
                                <label for="parent_category">Permission Type<span class="text-danger">*</span></label>
                                <input type="text" name="permission" id="permission" placeholder="Enter permission Name" value="{{$edit->permission_type ?? ''}}" class="form-control" required>
                            </div>

                            <div class="form-group mb-3 mt-3 col-md-6">
                                <label for="parent_category">Description<span class="text-danger">*</span></label>
                                <textarea name="description" id="description" cols="30" rows="1" class="form-control" placeholder="Enter Description">{{$edit->description ?? ''}}</textarea>
                            </div>
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
<h4></h4>
<div class="row">
    <div class="col-xl-12 mx-auto">
        <div class="card">
            <div class="card-body">
                <div class="card-title d-flex align-items-center">
                    <h5 class="">All Permission Types</h5>
                </div>
                    <hr>
                <div class="table-responsive">
                    <table id="example" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th class="text-uppercase ps-2 text-secondary text-xxs font-weight-bolder opacity-7">S NO:</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">permission</th>
                                <th class="text-uppercase ps-2 text-secondary text-xxs font-weight-bolder opacity-7">Description</th>
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
                                        <p class="text-sm font-weight-bold mb-0">{{$val->permission_type}}</p>
                                    </td>
                                    <td>{{Str::limit($val->description, 20, $end='...')}}</td>
                                    <td>
                                        <div class="table-actions d-flex align-items-center gap-3 fs-6">
                                            <a href="{{route('admin.permission_type.edit',$val->hashid)}}" class="text-warning" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Edit"><i class="bi bi-pencil-fill"></i></a>
                                            <a href="javascript:void(0)" class="text-danger" onclick="ajaxRequest(this)" data-url="{{route('admin.permission_type.delete',$val->id)}}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"><i class="bi bi-trash-fill"></i></a>
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
