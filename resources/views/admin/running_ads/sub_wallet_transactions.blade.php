@extends('layouts.admin')
@section('page-css')
<link href="{{ asset('front') }}/assets/plugins/datatable/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
@endsection
@section('content')
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-2 row-cols-xl-2 row-cols-xxl-2">
        <!-- <div class="col">
            <div class="card radius-10 border-0 border-start border-primary border-3">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="">
                            <p class="mb-1">Main wallet Balance</p>
                            <h4 class="mb-0 text-primary">{{get_price($main_wallet_bls)}}</h4>
                        </div>
                        <div class="ms-auto widget-icon bg-primary text-white">
                            <i class="bi bi-currency-dollar"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card radius-10 border-0 border-start border-primary border-3">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="">
                            <p class="mb-1">Sub wallet Budget</p>
                            <h4 class="mb-0 text-primary">{{get_price($sub_wallet_budget->spend_amount)}}</h4>
                        </div>
                        <div class="ms-auto widget-icon bg-primary text-white">
                            <i class="bi bi-currency-dollar"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card radius-10 border-0 border-start border-primary border-3">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="">
                            <p class="mb-1">Remaining Balance</p>
                            <h4 class="mb-0 text-primary">{{get_price($sub_wallet_remaining)}}</h4>
                        </div>
                        <div class="ms-auto widget-icon bg-primary text-white">
                            <i class="bi bi-currency-dollar"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->

        <div class="col">
            <div class="card radius-10 border-0 border-start border-primary border-3">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="">
                            <p class="mb-1">Total Topup</p>
                            <h4 class="mb-0 text-primary">{{get_price($total_topup)}}</h4>
                        </div>
                        <div class="ms-auto widget-icon bg-primary text-white">
                            <i class="bi bi-currency-dollar"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card radius-10 border-0 border-start border-primary border-3">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="">
                            <p class="mb-1">Total Spend</p>
                            <h4 class="mb-0 text-primary">{{get_price($total_spend)}}</h4>
                        </div>
                        <div class="ms-auto widget-icon bg-primary text-white">
                            <i class="bi bi-currency-dollar"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card radius-10 border-0 border-start border-primary border-3">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="">
                            <p class="mb-1">Total Deducted</p>
                            <h4 class="mb-0 text-primary">{{get_price($total_deducted)}}</h4>
                        </div>
                        <div class="ms-auto widget-icon bg-primary text-white">
                            <i class="bi bi-currency-dollar"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card radius-10 border-0 border-start border-primary border-3">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="">
                            <p class="mb-1">Remaining Balance</p>
                            <h4 class="mb-0 text-primary">{{get_price($sub_wallet_remaining)}}</h4>
                        </div>
                        <div class="ms-auto widget-icon bg-primary text-white">
                            <i class="bi bi-currency-dollar"></i>
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
            <div class="row align-items-center g-3">
                <div class="col-12 col-lg-6">
                    <h5 class="mb-0">Transactions Detail</h5>
                </div>
                <div class="col-12 col-lg-6 text-md-end">
                    <button type="button" class="btn btn-primary updatebls" id="updatebls">Balance Adjusting </button>
                </div>
            </div>
                <div class="table-responsive mt-3">
                    <table id="transaction-table" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Amount In</th>
                                <th>Amount Out</th>
                                <th>Description</th>
                                <th>Date & Time</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


{{-- Change Ads status model --}}
<div class="modal fade" id="changeAdsStatus" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Balance Adjusting </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('admin.sub_account.advertisements.sub_wallets_bls_update', ['sub_account_id' => session()->get('sub_account_id')]) }}" method="POST" id="ajaxForm">
                @csrf
                <input type="hidden" id="adsId" name="ads_id" value="{{$ads_id}}">
                <input type="hidden" name="client_id" value={{$client_id}}>
                <input type="hidden" name="sub_wallet_remaining" value="{{$sub_wallet_remaining}}">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="type">Type:</label>
                            <select name="type" id="bls_type" class="form-select" required>
                                <option value="">Select...</option>
                                <option value="add_from_main_wallet">Add From Main Wallet</option>
                                <option value="back_to_main_wallet">Back To Main Wallet</option>
                                <option value="transfer_to_subwallet">Transfer To Subwallet</option>
                                <option value="transfer_from_subwallet">Transfer From Subwallet</option>
                                <option value="google_spent">Google Spent</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="amt">Amount:</label>
                            <input type="number" id="amt" name="amt" class="form-control" required>
                        </div>
                    </div>
                    <div class="row" id="sub_wallets" style="display:none;">
                        <div class="col-md-12 mb-3">
                            <label for="sub_wallet">Subwallet:</label>
                            <select name="sub_wallet" id="sub_wallet" class="form-select">
                                <option value="">Select...</option>
                                @foreach($subwallets as $sub_wallet)
                                    <option value="{{$sub_wallet->id}}">{{$sub_wallet->adds_title}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary form-submit-btn">Submit</button>
                </div>

            </form>

        </div>
    </div>
</div>
{{-- Change Ads status model --}}

@endsection
@section('page-scripts')
<script src="{{ asset('front') }}/assets/plugins/datatable/js/jquery.dataTables.min.js"></script>
<script src="{{ asset('front') }}/assets/plugins/datatable/js/dataTables.bootstrap5.min.js"></script>
<script src="{{ asset('front') }}/assets/js/table-datatable.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>
<script>
    $("#updatebls").click(function() {
        $('#changeAdsStatus').modal('show');
    });
    $('#bls_type').change(function() {
        bls_type = $('#bls_type').val();
        if(bls_type == 'transfer_to_subwallet' || bls_type == 'transfer_from_subwallet'){
            $("#sub_wallets").show();
            $('#sub_wallet').prop('required', true);
            return false;
        }
        $('#sub_wallet').prop('required', false);
        $("#sub_wallets").hide();
    })
    $('#ajaxForm').submit(function(e) {
        e.preventDefault();
        validations = $("#ajaxForm").validate();
        if (validations.errorList.length != 0) {
            return false;
        }
        var url = $(this).attr('action');
        var param = new FormData(this);
        my_ajax(url, param, 'post', function(res) {}, true);
    });
    $(document).ready(function() {
        getTransactions();
    });


    function getTransactions() {
            if ($.fn.DataTable.isDataTable('#transaction-table')) {
                $('#transaction-table').DataTable().destroy();
            }

            $('#transaction-table').DataTable({
                processing: true,
                serverSide: true,
                "order": [
                    [0, "desc"]
                ],
                "pageLength": 10,
                "lengthMenu": [10, 50, 100, 150, 500],

                ajax: {
                    type: 'POST',
                    url: "{{ route('admin.sub_account.advertisements.sub_wallets_transactions', ['sub_account_id' => session()->get('sub_account_id')]) }}",
                    data: function(d) {
                        d.search = $('#transaction-table').DataTable().search();
                        d.ads_id = '{{$sub_wallet_budget->id}}';
                        d._token = "{{ csrf_token() }}";
                        d.client_id = "{{$client_id}}";
                    },
                },

                columns: [
                    {
                        data: null,
                        render: function (data, type, row, meta) {
                        return meta.row + 1;
                        },
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'amount_in',
                        name: 'amount_in',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'amount_out',
                        name: 'amount_out',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'description',
                        name: 'description',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        orderable: true,
                        searchable: false
                    },

                ],
            });



        }
        
</script>
@endsection