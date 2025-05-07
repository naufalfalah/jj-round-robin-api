@extends('layouts.admin')
@section('content')
    <div class="container-fluid py-4">

    <div class="row">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">

                    <a href="{{ route('admin.sub_account.client-management.add', ['sub_account_id' => $sub_account_id ]) }}" class="btn btn-primary float-end">Add New
                        Client</a>
                    <div class="card-title d-flex align-items-center">
                        <h5 class="card-title">{{ $title }}</h5>
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
                                            Client Image</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Client Name</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Mobile</th>
                                        <th
                                            class="text-uppercase ps-2 text-secondary text-xxs font-weight-bolder opacity-7">
                                            Agency</th>
                                       
                                        <th
                                            class="text-uppercase ps-2 text-secondary text-xxs font-weight-bolder opacity-7">
                                            Package</th>
                                        <th
                                            class="text-uppercase ps-2 text-secondary text-xxs font-weight-bolder opacity-7">
                                            Email</th>
                                        <th
                                            class="text-uppercase ps-2 text-secondary text-xxs font-weight-bolder opacity-7">
                                            Address</th>
                                        <th
                                            class="text-uppercase ps-2 text-secondary text-xxs font-weight-bolder opacity-7">
                                            Customer ID</th>
                                        <th
                                            class="text-uppercase ps-2 text-secondary text-xxs font-weight-bolder opacity-7">
                                            Action</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($all_users as $key => $val)
                                        <tr>
                                            <td class="col-6">
                                                <p class="text-sm font-weight-bold mb-0">{{ $key + 1 }}</p>
                                            </td>

                           
                                            <td>
                                                <img src="{{ check_file($val->image, 'public/uploads/profile_images/2024/') }}"
                                                    alt="" width="60" height="60">
                                            </td>

                                            <td>
                                                <p class="text-sm font-weight-bold mb-0">{{ $val->client_name }}</p>
                                            </td>

                                            <td>
                                                <p class="text-sm font-weight-bold mb-0">{{ $val->phone_number }}</p>
                                            </td>

                                            <td>
                                                <p class="text-sm font-weight-bold mb-0">{{ $val->Agencices->name }}</p>
                                            </td>
                                           
                                            <td>
                                                <p class="text-sm font-weight-bold mb-0">{{ $val->package }}</p>
                                            </td>

                                        <td>
                                            <p class="text-sm font-weight-bold mb-0">{{ $val->email }}</p>
                                        </td>


                                            <td>
                                                <p class="text-sm font-weight-bold mb-0" data-bs-toggle="tooltip"
                                                data-bs-placement="bottom" title="{{$val->address}}"
                                                aria-label="{{$val->address}}">{{ Str::limit($val->address, 12) }}</p>
                                            </td> 
                                            

                                            <td>
                                                <p class="text-sm font-weight-bold mb-0">{{ $val->customer_id ?? '' }}</p>
                                            </td>
                                            <td>
                                                <div class="table-actions d-flex align-items-center gap-3 fs-6">
                                                    <a href="{{ route('admin.sub_account.client-management.edit', ['sub_account_id' => $sub_account_id, 'id' => $val->hashid]) }}"
                                                        class="text-warning" data-bs-toggle="tooltip"
                                                        data-bs-placement="bottom" title="Edit"><i
                                                            class="bi bi-pencil-fill"></i></a>
                                                    <a href="javascript:void(0)" class="text-danger"
                                                        onclick="ajaxRequest(this)"
                                                        data-url="{{ route('admin.sub_account.client-management.delete', ['sub_account_id' => $sub_account_id, 'id' => $val->hashid]) }}"
                                                        data-toggle="tooltip" data-placement="top" title=""
                                                        data-original-title="Delete"><i class="bi bi-trash-fill"></i></a>
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

    @endsection

    @section('page-scripts')
    <script>
        $(function() {
            $('[data-bs-toggle="tooltip"]').tooltip();
        });
    </script>  
    @endsection



