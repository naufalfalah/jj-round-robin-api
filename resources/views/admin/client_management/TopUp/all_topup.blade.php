@extends('layouts.admin')

@section('content')
    <div class="row">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="border p-4 rounded">
                        <div class="card-title d-flex align-items-center">
                            <h5 class="mb-0">{{ isset($edit) ? $title : 'Add Top Up' }}</h5>
                        </div>
                        <hr />
                        <form method="POST" action="{{ route('admin.sub_account.client-management.topup_save', ['sub_account_id' => $sub_account_id ]) }}"
                            class="row g-3 ajaxForm">
                            @csrf

                            <div class="col-md-3">
                                <label for="">Client<span class="text-danger">*</span></label>
                                <select name="client_id" id="client_id" class="form-control">
                                    <option value="">Select A Client</option>
                                    @foreach ($all_users as $user)
                                        <option value="{{ $user->id }}"
                                            @if (isset($edit) && $edit->client_id == $user->id) selected @endif>
                                            {{ $user->client_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label for="">Amount<span class="text-danger">*</span></label>
                                <input type="text" name="topup_amount" id="topup_amount"
                                    value="{{ $edit->topup_amount ?? '' }}" placeholder="Enter Amount" class="form-control"
                                    required oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                            </div>



                            {{-- old  --}}
                            {{-- <div class="col-md-6">
                                <label for="">Proof<span class="text-danger 2.5rem"> (Upload Deposit Slip Image,
                                        Type: jpeg, jpg, png)</span></label>


                                <input type="file" id="proof" name="proof" class="form-control">

                            </div> --}}

                            <div class="col-md-6">
                                <label for="">Proof<span class="text-danger 2.5rem"> (Upload Deposit Slip Images,
                                        Type: jpeg, jpg, png)</span></label>

                                <input type="file" id="proof" name="proof[]" class="form-control" multiple>
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
                        <h5 class="">All Top Ups</h5>
                    </div>
                    <hr>
                    <div style="text-align: center;" class="table-responsive">
                        <table class="table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th class="text-uppercase ps-2 text-secondary text-xxs font-weight-bolder opacity-7">S
                                        NO:</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Client
                                        Name</th>

                                    <th class="text-uppercase ps-2 text-secondary text-xxs font-weight-bolder opacity-7">
                                        TopUp Amount</th>

                                    <th class="text-uppercase ps-2 text-secondary text-xxs font-weight-bolder opacity-7">
                                        Status</th>
                                    <th class="text-uppercase ps-2 text-secondary text-xxs font-weight-bolder opacity-7">
                                        Deposit Slip</th>

                                    <th class="text-uppercase ps-2 text-secondary text-xxs font-weight-bolder opacity-7">
                                        Added_by</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($all_wallet_topup as $key => $val)
                                    <tr>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0">{{ $key + 1 }}</p>
                                        </td>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0">{{ $val->clients->client_name }}</p>
                                        </td>

                                        <td>
                                            <p class="text-sm font-weight-bold mb-0">{{ $val->topup_amount }}</p>
                                        </td>

                                        <td>
                                            <p class="text-sm font-weight-bold mb-0">
                                                <span
                                                    class="badge bg-{{ $val->status == 'pending' ? 'warning' : ($val->status == 'approve' ? 'success' : 'danger') }}">
                                                    {{ $val->status == 'pending' ? 'Pending' : ($val->status == 'approve' ? 'Approved' : 'Rejected') }}
                                                </span>
                                            </p>
                                        </td>

                                        <td>
                                            @php
                                                $proofs = $val->proof;
                                            @endphp


                                            @if (isset($val->proof))
                                                @foreach(explode(',', $proofs) as $key => $imagePath)
                                                    
                                                        <a href="{{ asset($imagePath) }}" target="_blank" class="text-primary"
                                                            onmouseover="this.style.textDecoration='underline'"
                                                            onmouseout="this.style.textDecoration='none'"
                                                            data-bs-toggle="tooltip"
                                                            data-bs-placement="bottom" title="View Deposit Slip {{ $key + 1 }}" aria-label="View Deposit Slip {{ $key + 1 }}">
                                                            Proof Image {{ $key + 1 }}
                                                        </a><br>
                                                    
                                                @endforeach
                                            @else
                                                <a href="{{ isset($proof) ? asset($proof) : '' }}" target="_blank" class="text-primary"
                                                    onmouseover="this.style.textDecoration='underline'"
                                                    onmouseout="this.style.textDecoration='none'" data-bs-toggle="tooltip"
                                                    data-bs-placement="bottom" title="empty"
                                                    aria-label="empty">
                                                    -
                                                </a><br>
                                            @endif

                                        </td>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0">{{ ucfirst($val->added_by) }}</p>
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

        $(function() {
            $('[data-bs-toggle="tooltip"]').tooltip();
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
