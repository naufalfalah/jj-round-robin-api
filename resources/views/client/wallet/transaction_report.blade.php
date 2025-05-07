@extends('layouts.front')
@section('page-css')
<style>
    #success-message {
        transition: opacity 0.5s ease-out;
        /* Smooth fade-out effect */
    }


    .card-custom {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: calc(100% - 10px);
        /* 5px margin on each side */
        margin: 5px;
        padding: 10px;
    }

    .amount {
        font-size: 1.2rem;
    }

    .credit-card-image {
        width: 50px;
        height: auto;
    }

    .button-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        width: calc(100% - 10px);
        /* 5px margin on each side */
        margin: 5px;
        padding: 10px;
    }

    .digit-display {
        display: flex;
        align-items: center;
        font-size: 2rem;
        margin-bottom: 10px;
    }

    .digit-display button {
        width: 40px;
        height: 40px;
        font-size: 1.5rem;
    }

    .digit-value {
        margin: 0 10px;
    }

    .button-group {
        display: flex;
        justify-content: space-around;
        width: 100%;
        margin-bottom: 10px;
    }

    .button-group button {
        width: 40px;
        height: 40px;
        font-size: 1.2rem;
    }

    .slider {
        width: 100%;
    }

    @media screen and (max-width: 767px) {
        button#\34 00_btn {
            margin-top: 9px !important;
        }

        div.dataTables_wrapper div.row div.col-sm-12.col-md-6:nth-child(2) {
            margin: 0px !important;
            padding: 0px !important;
            margin-top: 7px !important;
            padding-top: 10px !important;
        }

        div.dataTables_length select.form-select {
            width: 100% !important;
        }
    }

    button#\34 00_btn {
        margin-top: auto;
    }

    /* div.dataTables_length select.form-select {
        width: 100% !important;
    } */
</style>



<link href="{{ asset('front') }}/assets/plugins/datatable/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
@endsection

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/intro.js/introjs.css">
    <style>
        .introjs-tooltip-header {
            display: none !important;
        }
    </style>
@endpush

@push('scripts-head')
    <script src="https://unpkg.com/intro.js/intro.js"></script>
@endpush

@section('content')



@include('components.client_nav_tabs')


<div class="row mt-5">
    <div class="col-12 col-xl-12">
        <div class="card shadow radius-10 w-100">
            <div class="card-body">
                <div class="row align-items-center g-3">
                    <div class="col-12 col-lg-6">
                        <h5 class="mb-0">Status definitions</h5>
                    </div>
                </div>
                <hr>
                <div class="table-responsive mt-2">
                    <table class="table align-middle mb-0" id="step-1">
                        <tbody>
                            <tr>
                                <td><span class="badge bg-secondary">Processing</span> &nbsp;&nbsp;
                                    We have received confirmation of your withdrawal request, and will be
                                    processing your request shortly.
                                </td>

                            </tr>


                            <tr>
                                <td><span class="badge bg-success">Completed</span>&nbsp;&nbsp;
                                    Your withdrawal request has been successfully processed and your funds are on the way to your designated account.
                                </td>

                            </tr>

                            <tr>
                                <td><span class="badge bg-dark">Canceled</span>&nbsp;&nbsp;
                                    Your withdrawal has been canceled as per your request.

                                </td>

                            </tr>

                            <tr>
                                <td><span class="badge bg-danger">Declined</span> &nbsp;&nbsp;
                                   <small> Your withdrawal request was not able to be processed. An email has been sent to you with more details. If you require any further clarifications please contact
                                    <br>
                                   <a href="mailto:support@icmarkets.com" style="margin-left: 80px; color:green;" target="_blank">support@jomejourney.com.</a>
                                   </small>
                                </td>

                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row" id="success_msg" style="display:none">
    <div class="col-xl-12 mx-auto">
       <div id="success-message" class="alert alert-success">
           
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12 col-xl-12">
        <div class="card shadow radius-10 w-100" id="deposit-card">
            <div class="card-body">
                <div class="row align-items-center g-3">
                    <div class="col-12 col-lg-6">
                        <h5 class="mb-0">Deposit Funds</h5>
                    </div>
                </div>
                <hr>
                <div class="table-responsive mt-2">
                    <table class="table align-middle mb-0" id="step-2">
                        <thead>
                            <tr>
                                <th>Funds transferred</th>
                                <th>Deposited wallet</th>
                                <th>Transaction ID</th>
                                <th>Status </th>
                                <th>Date & Time</th>
                                <th>Fees</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(empty($wallet_transactions))
                            <tr class="odd">
                                <td valign="top" colspan="5" class="dataTables_empty" style=" text-align: center; ">No
                                    data available in table</td>
                            </tr>
                            @endif
                            @foreach($wallet_transactions as $wallet_transaction) 
                            @php
                            $adsTItle = $wallet_transaction->get_ads?->adds_title ?? '';
                            if(isset($wallet_transaction->get_ads)){
                                $wallet_name = "Sub Wallet ($adsTItle)";
                            }else{
                                $wallet_name = 'Main Wallet';
                            }
                            @endphp
                            <tr>
                                @php
                                if($wallet_transaction->topup_type == 'stripe'){
                                $fee = '3.9 % + 0.60  SGD';
                                $icon = 'bi bi-credit-card-2-back-fill';
                                }else{
                                $fee = 'Free';
                                $icon = 'lni lni-money-protection';
                                }
                                @endphp
                                <td><i class="{{$icon }}"></i> Fund Now by {{ucfirst($wallet_transaction->topup_type)}}
                                    ({{get_price($wallet_transaction->amount_in)}})</td>
                                <td>{{$wallet_name}}</td>    
                                <td title="{{$wallet_transaction->transaction_id}}" onclick="copyToClipboard('{{ $wallet_transaction->transaction_id }}')"  style="cursor: pointer;">{{\Str::limit($wallet_transaction->transaction_id, 20, '...')}}</td>    
                                <td> <span class="badge bg-{{pay_topup_badge($wallet_transaction->status)}}">{{ucfirst($wallet_transaction->status)}}</span></td>
                                <td>{{get_fulltime($wallet_transaction->created_at)}}</td>
                                <td>{{ $fee }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- <div class="row">
    <div class="col-12 col-xl-12">
        <div class="card shadow radius-10 w-100">
            <div class="card-body">
                <div class="row align-items-center g-3">
                    <div class="col-12 col-lg-6">
                        <h5 class="mb-0">Pay Requests</h5>
                    </div>
                </div>
                <hr>
                <div class="table-responsive mt-2">
                    <table class="table align-middle mb-0" id="step-3">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Proof Image</th>
                                <th>Transaction ID</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($paynow_requests->count() == 0)
                            <tr class="odd">
                                <td valign="top" colspan="5" class="dataTables_empty" style=" text-align: center; ">No
                                    data available in table</td>
                            </tr>
                            @endif
                            @foreach($paynow_requests as $key => $paynow_request)
                            <tr>
                                <td>{{++$key}}</td>
                                <td>{{get_price($paynow_request->topup_amount)}}</td>
                                <td>
                                    <p class="text-sm font-weight-bold mb-0">
                                        <span
                                            class="badge bg-{{ $paynow_request->status == 'pending' ? 'warning' : ($paynow_request->status == 'approve' ? 'success' : 'danger') }}">
                                            {{ $paynow_request->status == 'pending' ? 'Pending' :
                                            ($paynow_request->status == 'approve' ? 'Approved' : 'Rejected') }}
                                        </span>
                                    </p>
                                </td>

                                <td>
                                    @foreach(explode(',', $paynow_request->proof) as $key => $imagePath)
                                    @if (isset($imagePath) && !empty($imagePath))

                                    <a href="{{ asset($imagePath) }}" target="_blank" class="text-primary"
                                        onmouseover="this.style.textDecoration='underline'"
                                        onmouseout="this.style.textDecoration='none'" data-bs-toggle="tooltip"
                                        data-bs-placement="bottom" title="View Deposit Slip {{ $key + 1 }}"
                                        aria-label="View Deposit Slip {{ $key + 1 }}">
                                        Proof Image {{ $key + 1 }}
                                    </a><br>

                                    @else
                                <td>-</td>
                                @endif
                                @endforeach
                                </td>
                                <td>{{strtotime($paynow_request->created_at)}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div> -->



<div class="row">
    <div class="col-12 col-xl-12">
        <div class="card shadow radius-10 w-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <h5 class="mb-0">Transactions Details</h5>
                </div>
                <div class="form-group col-md-6 my-2">
                <label>All Wallets <span class="text-danger">*</span></label>
                <input type="hidden" value="{{ request()->input('ads_id') }}" id="ads_id">
                <select name="wallet_id" id="wallet_id" class="form-control single-select" required>
                    <option value="all">All Wallets</option>
                    <option value="main">Main Wallet</option>
                    @foreach($subwallets as $subwallet)
                    <option value="{{$subwallet->id}}">{{$subwallet->adds_title}}</option>
                    @endforeach
                </select>
            </div>
                <hr>
                <div class="table-responsive mt-2" id="step-3">
                    <table class="table align-middle mb-0" id="transactions_template_table">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Wallet Name</th>
                                <th>Amount In</th>
                                <th>Amount Out</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Date & Time</th>
                            </tr>
                        </thead>
                    </table>
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
                        <h5 class="">Google Invoice</h5>
                    </div>
                    <hr>
                    <div class="table-responsive" id="step-4">
                        <table id="report-table" class="table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th
                                        class="">
                                        Invoice ID</th>
                                    <th class="">
                                        Invoice Date</th>
                                    <th class="">
                                        Total Amount</th>
                                    <th class="">
                                        Billing ID</th>
                                    <th class="">
                                        Customer ID</th>
                                    <th class="">
                                        Action</th>
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

    $('#amt').on('input', function () {
            var value = $(this).val();
            if (value.length > 6) {
                $(this).val(value.slice(0, 6)); // Sirf 6 digits rakhne ke liye
            }
        });
</script>
<script>
    $(document).ready(function() {
        getTopUps();
        wallet = 'all';
        getTransactions(wallet);
        getReport();
    });

    function getTopUps() {
        if ($.fn.DataTable.isDataTable('#wallet-template-table')) {
            $('#wallet-template-table').DataTable().destroy();
        }
        $('#wallet-template-table').DataTable({
            processing: true,
            serverSide: true,
            "order": [
                [0, "desc"]
            ],
            "pageLength": 10,
            "lengthMenu": [10, 50, 100, 150, 500],
            ajax: {
                url: "{{ route('user.wallet.add') }}",
                data: function(d) {
                    d.search = $('input[type="search"]').val();
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
                    data: 'amount',
                    name: 'amount',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'topup_type',
                    name: 'topup_type',
                    orderable: true,
                    searchable: false,
                    render: function(data, type, full, meta) {
                        return data.charAt(0).toUpperCase() + data.slice(1);
                    }
                },

                {
                    data: 'created_at',
                    name: 'created_at',
                    orderable: false,
                    searchable: false
                },

            ],
        });
    }

</script>
<script>
    $('#wallet_id').change(function() {
        var selectedValue = $(this).val();
        getTransactions(selectedValue);
    })
    function getTransactions(wallet) {
        if ($.fn.DataTable.isDataTable('#transactions_template_table')) {
            $('#transactions_template_table').DataTable().destroy();
        }
        $('#transactions_template_table').DataTable({
            processing: true,
            serverSide: true,
            "order": [
                [0, "desc"]
            ],
            "pageLength": 10,
            "lengthMenu": [10, 50, 100, 150, 500],
            ajax: {
                url: "{{ route('user.wallet.transaction_table') }}",
                data: function(d) {
                    d.search = $('input[type="search"]').val();
                    d.wallet = wallet;
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
                    data: 'wallet',
                    name: 'wallet',
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
                    data: 'status',
                    name: 'status',
                    orderable: true,
                    searchable: false
                },
                {
                    data: 'created_at',
                    name: 'created_at',
                    orderable: false,
                    searchable: false
                },

            ],
        });
    }
</script>
<script>
    function getReport() {
        if ($.fn.DataTable.isDataTable('#report-table')) {
            $('#report-table').DataTable().destroy();
        }
        $('#report-table').DataTable({
            processing: true,
            serverSide: true,
            "order": [
                [0, "desc"]
            ],
            "pageLength": 10,
            "lengthMenu": [10, 50, 100, 150, 500],
            ajax: {
                url: "{{ route('user.report.view') }}",
                data: function(d) {
                    d.search = $('input[type="search"]').val();
                },
            },
            columns: [
                {
                    data: 'invoice_id',
                    name: 'invoice_id',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'invoice_date',
                    name: 'invoice_date',
                    orderable: false,
                    searchable: true
                },
                {
                    data: 'total_amount',
                    name: 'total_amount',
                    orderable: true,
                    searchable: false
                },
                {
                    data: 'billing_id',
                    name: 'billing_id',
                    orderable: true,
                    searchable: false
                },
                {
                    data: 'customer_id',
                    name: 'customer_id',
                    orderable: true,
                    searchable: false
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: true,
                    searchable: false
                },
            ],
        });
    }
</script>





<script>
    function updateDigit(num) {
      document.getElementById('digit-display').innerText = num;
    }

    function changeDigit(change) {
      const digitDisplay = document.getElementById('digit-display');
      let currentDigit = parseInt(digitDisplay.innerText);
      currentDigit += change;
      digitDisplay.innerText = currentDigit;
    }
</script>
@endsection

@push('scripts')
    <script>
        const tour = '{{ $tour ?? null }}'
        const tourId = '{{ $tour->id ?? null }}';
        const tourCode = '{{ $tour->code ?? null }}';
        const clientTour = '{{ $client_tour ?? null }}';
        const clientId = `{{ auth('web')->user()->id }}`;
        const intro = introJs();

        function goToNextStep() {
            if (intro) {
                intro.nextStep();
            }
        }

        function startTour() {
            const steps = [];
            let step1 = null;

            let device = checkDevice();

            if (tourCode == 'AFTER_TOPUP') {
                step1 = document.querySelector('#deposit-card');
            }
            
            if (step1) {
                steps.push({ 
                    element: step1,
                    intro: "Inform your account manager and get this payment approve!",
                    position: 'top',
                });
            }

            intro.setOptions({
                steps,
                tooltipClass: 'customTooltipClass',
                disableInteraction: false,
                exitOnOverlayClick: false,
                exitOnEsc: false,
                showStepNumbers: false,
                hidePrev: true,
                hideNext: false,
                doneLabel: 'Next',
                overlayOpacity: 0.3,
                autoPosition: false,
                keyboardNavigation: false,
                showBullets: false,
            });

            intro.start();

            intro.oncomplete(function() {
                sendAjaxRequest('completed');
                if (tourCode == 'AFTER_TOPUP') {
                    setTimeout(function() {
                        window.location.href = `{{ route('user.wallet.transfer_funds') }}`;
                    }, 1000);
                }
            });
        }

        function sendAjaxRequest(action) {
            $.ajax({
                url: `{{ route('tour.store') }}`,
                method: 'POST',
                data: {
                    client_id: clientId,
                    tour_id: tourId,
                    action: action,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    console.log('AJAX request successful:', response);
                },
                error: function(xhr, status, error) {
                    console.error('AJAX request failed:', status, error);
                }
            });
        }

        function checkDevice() {
            if ($(window).width() < 768) {
                // Mobile
                console.log('Mobile device detected');
                return 'mobile';
            } else {
                // Desktop
                console.log('Desktop device detected');
                return 'desktop';
            }
        }

        $(window).resize(function() {
            checkDevice();
        });
        
        $(document).ready(function() {
            if (tour && !clientTour) {
                startTour();

                $('.introjs-skipbutton').hide();
                $('.introjs-backbutton').hide();
            }
        });
    </script>
@endpush