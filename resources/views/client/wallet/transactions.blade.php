@extends('layouts.front')
@section('page-css')
<link href="{{ asset('front') }}/assets/plugins/datatable/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
@endsection
@section('content')

<div class="row" id="success_msg" style="display:none">
    <div class="col-xl-12 mx-auto">
       <div id="success-message" class="alert alert-success">
           
        </div>
    </div>
</div>

    <div class="row">
        <div class="col-12 col-xl-12">
            <div class="card shadow radius-10 w-100">
                <div class="card-body">
                <div class="row align-items-center g-3">
                        <div class="col-12 col-lg-6">
                            <h5 class="mb-0">Transactions Detail</h5>
                        </div>
                        <!-- <div class="col-12 col-lg-6 text-md-end">
                            <button type="button" class="btn btn-primary topUpModal">Add Balance</button>
                        </div> -->
                    </div>
                    <hr>  
                    <div class="table-responsive mt-2">
                        <table class="table align-middle mb-0" id="transaction-table">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Transaction ID</th>
                                    <th>Amount In</th>
                                    <th>Amount Out</th>
                                    <th>Description</th>
                                    <th>Date & Time</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                        </table>    
                    </div>
                </div>
            </div>
        </div>
    </div>

{{-- </div> --}}


<div class="modal fade" id="topUpModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Balance</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="">Wallet Amount</label>
                        <div class="input-group mb-3"> <span class="input-group-text">$</span>
                            <input type="number" class="form-control" aria-label="Amount (to the nearest dollar)"
                                type="number" id="main_wallet_amt" name="main_wallet_amt" value="{{$main_wallet_bls}}" readonly> <span
                                class="input-group-text">.00</span>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="">Enter Amount</label>
                        <div class="input-group mb-3"> <span class="input-group-text">$</span>
                            <input type="number" class="form-control" aria-label="Amount (to the nearest dollar)"
                                type="number" id="topup" name="topup" value="0" min="1" required> <span
                                class="input-group-text">.00</span>
                        </div>
                        <input type="hidden" id="min_amt" value="{{@$top_setting->min_topup ?? ''}}">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" id="add_topup_btn" class="btn btn-primary">Press & Add Balance</button>
            </div>
        </div>
    </div>
</div>
@endsection
@section('page-scripts')
<script src="{{ asset('front') }}/assets/plugins/datatable/js/jquery.dataTables.min.js"></script>
<script src="{{ asset('front') }}/assets/plugins/datatable/js/dataTables.bootstrap5.min.js"></script>
<script src="{{ asset('front') }}/assets/js/table-datatable.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>
<script>
    function copyToClipboard(transactionId) {
        // Create a temporary input element
        var tempInput = document.createElement("input");
        // Set its value to the transaction ID
        tempInput.value = transactionId;
        // Append it to the document body
        document.body.appendChild(tempInput);
        // Select the text in the input element
        tempInput.select();
        // Copy the selected text to clipboard
        document.execCommand("copy");
        // Remove the temporary input element
        document.body.removeChild(tempInput);
        // Optionally, alert the user that the text has been copied
        $('#success-message').html("Transaction ID copied: " + transactionId);
        $('#success_msg').show();
            setTimeout(function() {
                $('#success_msg').hide();
            }, 5000);
    }
    $('.topUpModal').click(function() {
        $('#topUpModal').modal('show');
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
                    url: "{{ route('user.wallet.sub_wallets_transactions') }}",
                    data: function(d) {
                        d.search = $('#transaction-table').DataTable().search();
                        d.ads_id = '{{$ads_id}}';
                        d._token = "{{ csrf_token() }}";
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
                        data: 'transaction_id',
                        name: 'transaction_id',
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
                    {
                        data: 'status',
                        name: 'status',
                        orderable: true,
                        searchable: false
                    },
                ],
            });



        }
        
</script>

<script>
    $('#add_topup_btn').click(function() { 
        var main_wallet_amt = $('#main_wallet_amt').val();
        var topup = $('#topup').val();
        if(topup == 0 || topup == ''){
            alert('Please enter correct value');
            return false;
        }
        if(topup > main_wallet_amt){
            alert('You do not have enough balance to make a transactions');
        }else{
            var data = {
                '_token': "{{ csrf_token() }}",
                ads_id: "{{$ads_id}}",
                topup: topup,
                main_wallet_amt: main_wallet_amt
            };
            getAjaxRequests(
                "{{ route('user.wallet.add_topup_subwallet') }}",
                data, 'POST', true,
                function(res) {
                    if (res.success) {
                        toast(res.success, "Success!", 'success', 3000);
                    }
            });
        }
    })
</script>
@endsection
