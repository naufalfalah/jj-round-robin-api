@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">

    <div class="row">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <a href="{{ route('admin.role.add') }}" class="btn btn-primary float-end">Add New Role</a>
                    <div class="card-title d-flex align-items-center">
                        <h5 class="card-title">All Roles</h5>
                    </div>
                        <hr>
                    <div class="table-responsive mt-4">
                        <table id="example" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th
                                        class="text-uppercase ps-2 text-secondary text-xxs font-weight-bolder opacity-7">
                                        S NO:</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Role name</th>
                                    <th
                                        class="text-uppercase ps-2 text-secondary text-xxs font-weight-bolder opacity-7">
                                        Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $key=>$val)
                                <tr>
                                    <td>
                                        <p class="text-sm font-weight-bold mb-0">{{ $key+1}}</p>
                                    </td>
                                    <td>
                                        <p class="text-sm font-weight-bold mb-0">{{$val->name}}</p>
                                    </td>
                                    <td>
                                        <div class="table-actions d-flex align-items-center gap-3 fs-6">
                                            <a href="{{route('admin.role.edit',$val->hashid)}}" class="text-warning" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Edit"><i class="bi bi-pencil-fill"></i></a>
                                            <a href="javascript:void(0)" class="text-danger" onclick="ajaxRequest(this)" data-url="{{route('admin.role.delete',$val->id)}}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"><i class="bi bi-trash-fill"></i></a>
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
</div>

@endsection
@section('page-scripts')

@endsection
