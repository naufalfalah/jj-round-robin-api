@extends('layouts.admin')
@section('content')
<div class="container-fluid py-4">

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
                                    <th
                                        class="text-uppercase ps-2 text-secondary text-xxs font-weight-bolder opacity-7">
                                        S NO:</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Name</th>
                                    <th
                                        class="text-uppercase ps-2 text-secondary text-xxs font-weight-bolder opacity-7">
                                        Username</th>
                                    <th
                                        class="text-uppercase ps-2 text-secondary text-xxs font-weight-bolder opacity-7">
                                        Email</th>
                                    <th
                                        class="text-uppercase ps-2 text-secondary text-xxs font-weight-bolder opacity-7">
                                        Image</th>
                                    <th
                                        class="text-uppercase ps-2 text-secondary text-xxs font-weight-bolder opacity-7">
                                        Role</th>
                                    <th
                                        class="text-uppercase ps-2 text-secondary text-xxs font-weight-bolder opacity-7">
                                        Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($allUsers as $key=>$val)
                                <tr>
                                    <td>
                                        <p class="text-sm font-weight-bold mb-0">{{ $key+1}}</p>
                                    </td>
                                    <td>
                                        <p class="text-sm font-weight-bold mb-0">{{ $val->name}}</p>
                                    </td>
                                    <td>
                                        <p class="text-sm font-weight-bold mb-0">{{ $val->username}}</p>
                                    </td>
                                    <td>
                                        <p class="text-sm font-weight-bold mb-0">{{ $val->email}}</p>
                                    </td>
                                    <td>
                                        <img src='{{ check_file($val->image) }}' class='product-img-2' alt='product'>
                                    </td>
                                    <td>
                                        <p class="text-sm font-weight-bold mb-0">{{ $val->role_name ?? '-'}}</p>
                                    </td>
                                    <td>
                                    <div class="table-actions d-flex align-items-center gap-3 fs-6">
                                        @if (auth('admin')->user()->can('user-update'))
                                        <a href="{{route('admin.user-management.edit',$val->hashid)}}" class="text-warning" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Edit"><i class="bi bi-pencil-fill"></i></a>
                                        @endif

                                        @if (auth('admin')->user()->can('user-delete'))
                                        <a href="javascript:void(0)" onclick="ajaxRequest(this)" data-url="{{route('admin.user-management.delete',$val->id)}}}" class="text-danger" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Delete"><i class="bi bi-trash-fill"></i></a>
                                        @endif
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
@endsection
