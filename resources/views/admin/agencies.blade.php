@extends('layouts.admin')
@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="border p-4 rounded">
                        <div class="card-title d-flex align-items-center">
                            <h5 class="mb-0">{{isset($edit) ? "Edit Agency" : 'Add Agency'}}</h5>
                        </div>
                        <hr/>
                        <form method="POST" action="{{ route('admin.agency.save') }}"
                            class="row g-3 ajaxForm">
                            @csrf
                            <div class="col-md-6">
                                <label for="">Name<span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" value="{{ $edit->name ?? '' }}" placeholder="Enter Agency Name" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label for="">Status<span class="text-danger">*</span></label>
                                <select name="status" class="form-control" required>
                                    <option value="1" {{ isset($edit) && $edit->status == 1 ? 'selected' : '' }}>active</option>
                                    <option value="0" {{ isset($edit) && $edit->status == 0 ? 'selected' : '' }}>de-active</option>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label for="">Address<span class="text-danger">*</span></label>
                                <input type="text" name="address" id="address" value="{{ $edit->address ?? '' }}" placeholder="Enter Agency Address" class="form-control" required>
                            </div>
                            <div class="form-group mb-3 text-right">
                                <input type="hidden" name="id" value="{{ $edit->hashid ?? null }}">
                                <button type="submit" class="btn btn-primary px-5 form-submit-btn">Save</button>
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
                        <h5 class="">{{$title}}</h5>
                    </div>
                        <hr>
                    <div class="table-responsive">
                        <table id="example" class="table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th class="text-uppercase ps-2 text-secondary text-xxs font-weight-bolder opacity-7">
                                        S NO:</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Name</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Address</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Status</th>
                                    <th class="text-uppercase ps-2 text-secondary text-xxs font-weight-bolder opacity-7">
                                        Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($agencies as $key=>$val)
                                <tr>
                                    <td>
                                        <p class="text-sm font-weight-bold mb-0">{{ $key+1}}</p>
                                    </td>
                                    <td>
                                        <p class="text-sm font-weight-bold mb-0">{{ $val->name}}</p>
                                    </td>
                                    <td>
                                        <a href="javascript:void(0);" class="text-sm text-dark font-weight-bold mb-0" data-bs-toggle="tooltip" data-bs-placement="bottom" title="{{ $val->address }}">{{ Str::limit($val->address, 30, "...") ?? "No Agency Address Found"}}</a>
                                        {{-- <p class="text-sm font-weight-bold mb-0">{{ Str::limit($val->address, 30, "...") ?? ""}}</p> --}}
                                    </td>
                                    <td>
                                        <p class="text-sm font-weight-bold mb-0">
                                          <span class="badge bg-{{ $val->status == 1 ? 'success' : 'warning' }}">{{ $val->status == 1 ? 'Active' : 'Deactive' }}</span>
                                        </p>
                                    </td>
                                    <td>
                                    <div class="table-actions d-flex align-items-center gap-3 fs-6">

                                        <a href="{{route('admin.agency.edit',$val->hashid)}}" class="text-warning" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Edit"><i class="bi bi-pencil-fill"></i></a>
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
            $('#example').DataTable();
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
