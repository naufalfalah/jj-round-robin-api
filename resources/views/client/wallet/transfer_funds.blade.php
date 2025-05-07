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
    .error {
        color: red;
    }
</style>



<link href="{{ asset('front') }}/assets/plugins/datatable/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
@endsection

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/intro.js/introjs.css">
    <style>
        .introjs-tooltip {
            max-width: 600px;
        }
        .introjs-tooltip-header,
        .introjs-tooltipbuttons .introjs-skipbutton,
        .introjs-tooltipbuttons .introjs-prevbutton {
            display: none !important;
        }
        .introjs-tooltipReferenceLayer {
            z-index: 25 !important;
        }
        .introjs-showElement {
            z-index: 23 !important;
        }
        .introjs-overlay {
            z-index: 22 !important;
        }
        .introjs-helperLayer {
            z-index: 21 !important;
        }
    </style>
@endpush

@push('scripts-head')
    <script src="https://unpkg.com/intro.js/intro.js"></script>
@endpush

@section('content')



@include('components.client_nav_tabs')


<div class="col-12 col-xl-12 mt-4">
    <div class="card shadow radius-10 w-100">
        <div class="card-body">
            <div class="table-responsive mt-2">
                <h6> Manage Your Funds Efficiently with Datapoco </h6>

                <p style="font-size: 15px;">

                    Easily transfer funds between your main account and individual sub-wallets to manage your ad budgets
                    effectively. Whether you're reallocating funds for different <br> campaigns or adjusting your
                    budget, our system allows for quick and seamless transfers. <br> <br>

                    Simply choose the main account to transfer funds from, select the sub-wallet for the transfer, and
                    click the 'Transfer Funds Now' button. <br> <br>

                    Please note that while transfers are usually processed quickly, there may be instances where
                    additional processing time is required. To avoid any disruption to your <br>
                    campaigns, we recommend evaluating your budget needs and ensuring that sufficient funds are
                    transferred in a timely manner. Datapoco is not responsible for any <br>
                    consequences arising from delayed transfers, including the potential interruption of your
                    advertising campaigns.
                </p>
            </div>
        </div>
    </div>
</div>


<div class="row mt-3">
    <form method="post" action="{{ route('user.wallet.funds_save') }}" class="row pb-5 pb-lg-0 g-3 ajaxForm" id="section-1">
        <div class="col-md-3" id="step-1">
            <label for="">From:</span></label>
            <select name="form_wallet" class="form-control" id="" required>
                <option value="">Select...</option>
                <option value="main_wallet">Main Wallet | SGD {{$total_balance}}</option>
                @foreach($from_sub_wallets as $from_sub_wallet)
                <option value="{{$from_sub_wallet->id}}">{{$from_sub_wallet->adds_title}} |
                SGD {{$from_sub_wallet->spend_amount}}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3" id="step-2">
            <label for="">Amount</span></label>
            <input type="number" name="spend_amount" id="spend" value="" placeholder="0" min="1" maxlength="6" class="form-control"
                required="">
        </div>
        <div class="col-md-3" id="step-3">
            <label for="">To:</span></label>
            <select name="to_wallet" class="form-control" id="" required>
                <option value="">Select...</option>
                <option value="main_wallet">Main Wallet | SGD {{$total_balance}}</option>
                @foreach($to_sub_wallets as $to_sub_wallet)
                <option value="{{$to_sub_wallet->id}}">{{$to_sub_wallet->adds_title}} |
                    SGD {{$to_sub_wallet->spend_amount}}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3" id="step-4">
            <button type="submit" class="btn btn-primary px-5 form-submit-btn mt-4">Send Request</button>
        </div>
    </form>
    <div class="col-12 col-xl-12 mt-4">
        <div class="card shadow radius-10 w-100" id="step-5">
            <div class="card-body">
                <div class="row align-items-center g-3">
                    <div class="col-12 col-lg-6">
                        <h5 class="mb-0">Your Requests</h5>
                    </div>
                </div>
                <hr>
                <div class="table-responsive mt-2">
                    <table class="table align-middle mb-0" id="wallet-template-table">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>From Wallet</th>
                                <th>To Wallet</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                    </table>
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
    $(document).ready(function() {
        getTopUps();

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
                if (res.success) {
                    intro.nextStep();
                }
            }, true);
        })
    });
</script>
<script>
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
                url: "{{ route('user.wallet.view_fund_transections') }}",
                data: function(d) {
                    d.search = $('input[type="search"]').val();
                },
            },
            columns: [
                {
                    data: 'date',
                    name: 'date',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'from_wallet_id',
                    name: 'from_wallet_id',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'to_wallet_id',
                    name: 'to_wallet_id',
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
                    data: 'status',
                    name: 'status',
                    orderable: false,
                    searchable: false
                }

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
        const tour = '{{ $tour ?? null }}';
        const tourId = '{{ $tour->id ?? null }}';
        const tourCode = '{{ $tour->code ?? null }}';
        const clientTour = '{{ $client_tour ?? null }}';
        const clientId = `{{ auth('web')->user()->id }}`;
        const wallet = `{{ $wallet ?? null }}`;
        const mainWalletBalance = `{{ $total_balance }}`;
        const intro = introJs();

        function goToNextStep() {
            if (intro) {
                intro.nextStep();
            }
        }

        function startTour() {
            const steps = [];
            const step1 = document.querySelector('#section-1');

            if (step1) {
                steps.push({
                    element: step1,
                    intro: `<strong>Final Step<br><br>
                        You can conveniently top up your main wallet at any time, ensuring you have the flexibility to allocate funds across your projects. You may move funds between your main wallet and project sub-wallets, or between sub-wallets to optimize funding.</strong>`,
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
                if (tourCode == 'FINISH_1') {
                    setTimeout(function() {
                        window.location.href = `{{ route('user.ads.all') }}`;
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

        $(document).ready(function() {
            if (tour && !clientTour) {
                startTour();
                $('.introjs-skipbutton').hide();
                $('.introjs-backbutton').hide();

                // Step 0
                if (intro._currentStep === 0) {
                    if (mainWalletBalance > 0) {
                        $('.introjs-nextbutton').hide();
                    }
                }
            }
        });
    </script>
@endpush
