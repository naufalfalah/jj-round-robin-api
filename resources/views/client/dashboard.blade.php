@extends('layouts.front')

@section('page-css')
<link href="{{ asset('front') }}/assets/plugins/datatable/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
<style>
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

        #sub_wallet_empty {
            font-size: 19px;
        }
    }

    .credit-card-image {
        width: 50px;
        height: auto;
    }

    img {
        /* display: block; */
        max-width: 100%;
    }
    #swal2-title{
        font-size: 26px;
    }
</style>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/intro.js/introjs.css">
@endpush

@push('scripts-head')
<script src="https://unpkg.com/intro.js/intro.js"></script>
@endpush


@section('content')
<div>
    <h4>Main Wallet</h4>
</div>
<hr>
<div class="row">
    <div class="col-lg-4 col-12 col-md-6">
        {{-- Main Wallet Card --}}
        <div class="card radius-10 bg-info" style="background-color: #39548a !important; height: 240px;" id="step-1">
            <div class="card-body p-lg-4 d-flex flex-column justify-content-between" style="min-height: 190px;">
                <div>
                    <div class="d-flex justify-content-between">
                        <div class="fs-2 d-flex gap-3 text-white">
                            <span style="
                                    width: 45px;
                                    background-color: white;
                                    height: 45px;
                                    border-radius: 11px;
                                    padding: 7px;">
                                <img src="{{ asset('front') }}/assets/images/wallet.png"
                                    style="display: block !important;" alt="">
                            </span>
                            <div style="font-size: 12px; font-family:Outfit">

                            </div>
                        </div>
                    </div>
                    <div class="mt-3 d-flex justify-content-between">
                        <div>
                            <p class="mb-1 text-white" style="font-size: 14px; font-weight: bold; line-height: 17.6px;">
                                Account Balance</p>
                            <h4 class="mb-0 text-white"
                                style="font-weight: 900; font-size: 20px; line-height: 25.14px;">
                                {{get_price($main_wallet_bls)}}</h4>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end align-items-center gap-2">
                    <div class="d-flex align-items-center flex-column" style="font-family: Inter, sans-serif;">
                        <p class="mb-0 w-100 text-white text-end pe-1" style="font-weight: bold; font-size: 12px;">Last
                            Updated:</p>
                        <p class="text-white" style="font-size: 12px; margin-bottom: 0px; font-weight: bold;">
                            {{get_date(@$last_transaction_date)}} {{get_time(@$last_transaction_date)}}</p>
                    </div>
                    <div class="d-flex justify-content-center align-items-center" style="width: 38px;
                            background-color: white;
                            height: 38px;
                            border-radius: 50%;
                            padding: 10px;">
                        <img src="{{ asset('front') }}/assets/images/calendar.png" style="display: block !important;"
                            alt="">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div>
    <h4>Sub Wallets</h4>
</div>
<hr>
@if($sub_accounts->count() == 0)
<div class="row">
    <div class="col-12 col-xl-12">
        <div class="card shadow radius-10 w-100">
            <div class="card-body">
                <div class="col-12 col-md-12 col-sm-12 mb-25">
                    <div class="box">
                        <div class="box-body pt-30 pb-30 text-center">
                            <i class="fadeIn animated bx bx-x-circle" style=" font-size: 50px; "></i>
                            <h4 id="sub_wallet_empty">No Sub Wallets Found</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endif

<div class="row" @if ($sub_accounts->count()) id="step-2" @endif>
    @foreach($sub_accounts as $sub_account)
    <div class="col-lg-4 col-12 col-md-6">

        <div class="card radius-10 bg-info" style="background-color: #39548a !important; min-height: 390px;"
            id="step-4">
            <div class="card-body p-lg-4 d-flex flex-column justify-content-between" style="min-height: 190px; {{$sub_account->status == 'close' ? 'opacity: 0.5; cursor: no-drop;' : '' }}">
                <div>
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('user.wallet.transactions',['ads_id'  => $sub_account->hashid]) }}">
                            <div class="fs-2 d-flex gap-3 text-white">
                                <span style="
                                            width: 45px;
                                            background-color: white;
                                            height: 45px;
                                            border-radius: 11px;
                                            padding: 7px;">
                                    <img src="{{ asset('front') }}/assets/images/wallet.png"
                                        style="display: block !important;" alt="">
                                </span>
                                <div style="font-size: 12px; font-family:Outfit">
                                    <p class="m-0" style="font-weight: 900;">Sub Wallet:</p>
                                    <p class="m-0" style="font-weight: 700;">{{
                                        Str::limit(ucfirst($sub_account->adds_title), 30, "...") }}</p>
                                </div>
                            </div>
                        </a>
                        <div class="d-flex align-items-center">
                            @php
                            $bdg_color = '#FCC82D';
                            $text_color = 'black';
                            if($sub_account->status == 'running' || $sub_account->status == 'complete'){
                            $bdg_color = '#10bd23';
                            }if($sub_account->status == 'reject' || $sub_account->status == 'close'){
                            $bdg_color = '#e70e12';
                            $text_color = '#fff';
                            }
                            $monthly_budget = 0;
                            $daily_budget = 0;
                            $day = 0;
                            if($sub_account->spend_type == 'daily'){
                            $daily_budget = $sub_account->daily_budget;
                            $monthly_budget = $sub_account->daily_budget*30;
                            }else{
                            $daily_budget = $sub_account->daily_budget/30;
                            $monthly_budget = $sub_account->daily_budget;
                            }
                            @endphp
                            <span class="text-center text-truncate" style="
                                        font-size: 9.84px;
                                        font-family: Inter, sans-serif;
                                        background-color: {{$bdg_color}};
                                        padding: 3px 7px;
                                        border-radius: 100px;
                                        font-weight: 600; margin-right: 3px; color: {{$text_color}};
                                        ">
                                {{ ads_status_text($sub_account->status) }}
                            </span>
                            @if($sub_account->status != 'close' && $sub_account->status == 'running')   
                            <span class="text-center text-truncate" style="
                                        font-size: 9.84px;
                                        font-family: Inter, sans-serif;
                                        background-color: red;
                                        padding: 3px 7px;
                                        border-radius: 100px;
                                        font-weight: 600; color: white; cursor: pointer;
                                        " class="close_wallet" data-id="{{$sub_account->id}}">
                                Close <i class="fa fa-close"></i>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="mt-3 d-lg-flex justify-content-between">
                        <div>
                            <p class="mb-1 text-white" style="font-size: 14px; font-weight: bold; line-height: 17.6px;">
                                Budget allocation for ads</p>
                            <h4 class="mb-0 text-white"
                                style="font-weight: 900; font-size: 20px; line-height: 25.14px;">
                                SGD {{$monthly_budget}}</h4>
                            <div class="mt-3">
                                <p class="mb-1 text-white"
                                    style="font-size: 14px; font-weight: bold; line-height: 17.6px;">
                                    Estimated Daily Spending</p>
                                <h4 class="mb-0 text-white"
                                    style="font-weight: 900; font-size: 20px; line-height: 25.14px;">
                                    SGD @if(fmod($daily_budget, 1) == 0)
                                            {{ number_format($daily_budget, 0) }}
                                        @else
                                            {{ number_format($daily_budget, 2) }}
                                        @endif</h4>
                            </div>
                            <div class="mt-3">
                                <p class="mb-1 text-white"
                                    style="font-size: 14px; font-weight: bold; line-height: 17.6px;">
                                    Estimated Monthly Spending</p>
                                <h4 class="mb-0 text-white"
                                    style="font-weight: 900; font-size: 20px; line-height: 25.14px;">
                                    SGD {{$monthly_budget}}</h4>
                            </div>
                        </div>
                        <div class="mt-3 mt-lg-0">
                        @if($sub_account->status != 'close')
                            <p class="mb-1 text-white" style="font-size: 14px; font-weight: bold; line-height: 17.6px;">
                                Account Recharge</p>
                                <h4 class="mb-0 text-white" style="font-weight: 900;font-size: 16px;line-height: 25.14px;background: #21345c;padding: 10px;border-radius: 10px;"> 
                                @if($sub_account->spend_amount != 0)        
                                    <span style="color: #12bf24;padding-right: 4px; {{( $monthly_budget-$sub_account->spend_amount) != 0 && $sub_account->spend_amount < $monthly_budget ? 'border-right: 1px solid gray;' : '' }}">
                                    SGD {{ $sub_account->spend_amount && $sub_account->spend_amount > 0 ? number_format($sub_account->spend_amount, 2) : 0}}</span>
                                @endif    
                                @if(($monthly_budget-$sub_account->spend_amount) != 0 && $sub_account->spend_amount < $monthly_budget)    
                                    <span style="color: #e72e2e;">SGD {{ number_format(($monthly_budget - $sub_account->spend_amount), 2) }}</span>
                                @endif
                                </h4>
                        @endif
                        </div>
                    </div>

                    @if($sub_account->spend_type == 'daily' || $sub_account->spend_type == 'monthly')
                    @php



                    $days = 0;
                    $max_days = 30;
                    if ($daily_budget > 0) {
                    $days = floor($sub_account->spend_amount / $daily_budget);
                    $days = min($days, $max_days);
                    }


                    @endphp
                    @if($sub_account->status != 'close')
                    <div style="margin-top: 15px;">
                        <p style="font-family: Outfit;
                                    font-size: 14px;
                                    color: black;
                                    font-weight: 500;
                                    line-height: 17.64px;
                                    text-align: left; background-color: #FCC82D; border-radius: 3px; padding: 3px 7px; width: 315px;
                                    ">Your campaign is projected to end in {{$days}} days</p>
                        @if($days <= 7) <p style="font-family: Outfit;
                                    font-size: 14px;
                                    color: black;
                                    font-weight: 500;
                                    line-height: 17.64px;
                                    text-align: left; background-color: #FCC82D; border-radius: 3px; padding: 3px 7px; width: 108px;
                                    ">Please Top Up</p>
                            @endif
                    </div>
                    @endif
                    @endif
                </div>
                <div class="d-flex justify-content-end align-items-center gap-2 text-white">
                    <div class="d-flex align-items-center flex-column" style="font-family: Inter, sans-serif;">
                        <p class="mb-0 w-100 text-end pe-1" style="font-weight: bold; font-size: 12px;">Last Updated:
                        </p>
                        <p style="font-size: 12px; margin-bottom: 0px; font-weight: bold;">
                            {{get_date(@$sub_account->updated_at)}} {{get_time(@$sub_account->updated_at)}}</p>
                    </div>
                    <div class="d-flex justify-content-center align-items-center" style="width: 38px;
                                    background-color: white;
                                    height: 38px;
                                    border-radius: 50%;
                                    padding: 10px;">
                        <img src="{{ asset('front') }}/assets/images/calendar.png" style="display: block !important;"
                            alt="">
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>



<form method="get" action="" class="row g-3 ajaxFormClient">
    <div class="row" id="step-3">
        <div class="form-group col-md-6 my-2">
            <label>All Ads <span class="text-danger">*</span></label>
            <input type="hidden" value="{{ request()->input('ads_id') }}" id="ads_id">
            <select name="ads_id" id="ads_id" class="form-control single-select" required>
                <option value="">All Ads</option>
                @foreach ($sub_accounts as $val)
                <option value="{{ $val->hashid }}" {{ request()->input('ads_id') == $val->hashid ? 'selected' : '' }}>
                    {{$val->adds_title}}
                </option>
                @endforeach
            </select>
        </div>
    </div>
</form>
<div class="row">
    <div class="col-12 col-xl-12">
        <div class="card shadow radius-10 w-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <h6 class="mb-0">Latest Leads</h6>
                    <div class="fs-6 ms-auto dropdown">
                        <a href="{{ route('user.leads-management.leads') }}">View All Leads</a>
                    </div>
                </div>
                <hr>
                <div class="table-responsive mt-2" id="step-4">
                    <table class="table align-middle mb-0" id="ppc_leads-template-table">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Ads Name</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>phone Number</th>
                                <th>Qualifying Questions</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>



{{-- view lead details --}}
<div class="modal fade" id="view_lead_details" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Lead Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-1">
                    <div class="col-6 col-md-6 col-lg-6">
                        <h5>Name</h5>
                        <p id="name">Name</p>
                    </div>
                    <div class="col-6 col-md-6 col-lg-6">
                        <h5>Email</h5>
                        <p id="email"></p>
                    </div>
                    <div class="col-6 col-md-6 col-lg-6">
                        <h5>Phone Number</h5>
                        <p id="mobile_number"></p>
                    </div>
                    <div class="col-6 col-md-6 col-lg-6">
                        <h5>Status</h5>
                        <p id="admin_status"></p>
                    </div>
                </div>
                <h5>Qualifying Questions</h5>

                <div class="row">
                    <div class="col-6 col-md-12 col-lg-12">
                        <div id="leadData_body"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
            </form>
        </div>
    </div>
</div>
{{-- view lead details --}}


{{-- view lead details info --}}
<div class="modal fade" id="view_lead_details_info" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Agent Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-1">
                    <div class="col-6 col-md-6 col-lg-6">
                        <h5>Salesperson Name</h5>
                        <p id="salesperson_name">Name</p>
                    </div>
                    <div class="col-6 col-md-6 col-lg-6">
                        <h5>Registration No</h5>
                        <p id="registration_no"></p>
                    </div>
                    <div class="col-6 col-md-6 col-lg-6">
                        <h5>Registration Start Date</h5>
                        <p id="registration_start_date"></p>
                    </div>
                    <div class="col-6 col-md-6 col-lg-6">
                        <h5>Registration End Date</h5>
                        <p id="registration_end_date"></p>
                    </div>
                    <div class="col-6 col-md-6 col-lg-6">
                        <h5>Estate Agent Name</h5>
                        <p id="estate_agent_name"></p>
                    </div>
                    <div class="col-6 col-md-6 col-lg-6">
                        <h5>Estate Agent License No</h5>
                        <p id="estate_agent_license_no"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
            </form>
        </div>
    </div>
</div>
{{-- view lead details info --}}
@endsection

@section('page-scripts')
<script src="{{ asset('front') }}/assets/plugins/datatable/js/jquery.dataTables.min.js"></script>
<script src="{{ asset('front') }}/assets/plugins/datatable/js/dataTables.bootstrap5.min.js"></script>
<script src="{{ asset('front') }}/assets/js/table-datatable.js"></script>
<script>
    $('.close_wallet').click(function() {
        id = $(this).data('id');
        Swal.fire({
            title: "Are you sure you want to close this ad?",
            text: "Once closed, the ad will stop running, and any remaining balance from this wallet will be transferred to your main wallet.",
            type: "warning",
            showCancelButton: !0,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes",
            cancelButtonText: "No"
        }).then(function (t) {
            if (t.value){
                formData = new FormData();
                formData.append('id', id);
                formData.append('_token', "{{ csrf_token() }}");
                my_ajax("{{route('user.wallet.wallet_close')}}", formData, 'post', function(res) {
                    if (res.success) {
                       
                    }
                }, true);
            }
        })
    })
</script>
<script>
    var leadDataCount = 0;
        $(document).ready(function() {
            get_ppc_leads()
        });
        function get_ppc_leads() {
            var ppc = 'ppc';
            var ads_id = $('#ads_id').val();
            if ($.fn.DataTable.isDataTable('#ppc_leads-template-table')) {
                $('#ppc_leads-template-table').DataTable().destroy();
            }

            $('#ppc_leads-template-table').DataTable({
                processing: true,
                serverSide: true,
                "order": [
                    [0, "desc"]
                ],
                "pageLength": 10,
                "lengthMenu": [10, 50, 100, 150, 500],

                ajax: {
                    type: 'POST',
                    url: "{{ route('user.get_latest_leads_dashboard') }}",
                    data: function(d) {
                        d.search = $('#ppc_leads-template-table').DataTable().search();
                        d.ppc = ppc;
                        d.ads_id = ads_id;
                        d._token = "{{ csrf_token() }}";
                    },
                },

                columns: [
                    {
                        data: null,
                        render: function(data, type, row, meta) {
                            return meta.row + 1;
                        },
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'ads_name',
                        name: 'ads_name',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'email',
                        name: 'email',
                        orderable: true,
                        searchable: false
                    },
                    {
                        data: 'mobile_number',
                        name: 'mobile_number',
                        orderable: true,
                        searchable: false
                    },
                    {
                        data: 'lead_data',
                        name: 'lead_data',
                        orderable: true,
                        searchable: false
                    },

                    {
                        data: 'admin_status',
                        name: 'admin_status',
                        orderable: true,
                        searchable: false
                    },

                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: true,
                        searchable: false
                    },

                ],
            });
        }

        $(document).on('click', ".view_lead_detail_id", function() {
            let data = $(this).data('data');
            // alert(data.name);
            $("#name").html(data.name);
            $("#email").html(data.email);
            $("#mobile_number").html(data.mobile_number);
            if (data.admin_status != null) {
                $("#admin_status").html(data.admin_status.toUpperCase());
            }
            $("#leadData_body").html('');
            leadDataCount = data.lead_data ? data.lead_data.length : 0;
            for (let index = 0; index < leadDataCount; index++) {
                let leadData = data.lead_data[index];
                let _html = `
                                <p>${leadData.key}: ${leadData.value}</p>`;
                $("#leadData_body").append(_html);
            }
            $('#view_lead_details').modal('show');
        });

        $(document).on('click', ".agent_specific_action", function() {

            let data = $(this).data('data');
            // alert(data.name);
            $("#salesperson_name").html(data.salesperson_name);
            $("#registration_no").html(data.registration_no);
            $("#registration_start_date").html(data.registration_start_date);
            $("#registration_end_date").html(data.registration_end_date);
            $("#estate_agent_name").html(data.estate_agent_name);
            $("#estate_agent_license_no").html(data.estate_agent_license_no);

            $('#view_lead_details_info').modal('show');
        });


        $(document).on('change', 'select[name="admin_status"]', function() {
            let id = $(this).data('id');
            let status = $(this).val();
            var data = {
                '_token': "{{ csrf_token() }}",
                lead_id: id,
                admin_status: status
            };

            getAjaxRequests(
                "{{ route('user.ads.lead_admin_status') }}",
                data, 'POST', true,
                function(res) {
                    if (res.success) {
                        toast(res.success, "Success!", 'success', 3000);
                    }
                });
        });


        $(document).on('change', '#ads_id', function() {
            $('.ajaxFormClient').submit();
        });
</script>
@endsection

@push('scripts')
<script>
    const tour = '{{ $tour ?? null }}'
        const tourId = '{{ $tour->id ?? null }}';
        const clientTour = '{{ $client_tour ?? null }}';
        const clientId = `{{ auth('web')->user()->id }}`;

        $(document).ready(function() {
            if (tour && !clientTour) {
                const intro = introJs();

                const steps = [];
                const step1 = document.querySelector('#step-1');
                const step2 = document.querySelector('#step-2');
                const step3 = document.querySelector('#step-3');
                const step4 = document.querySelector('#step-4');

                if (step1) {
                    steps.push({
                        element: step1,
                        intro: "This section shows an overview of your main wallet, including balance and last updated."
                    });
                }

                if (step2) {
                    steps.push({
                        element: step2,
                        intro: "Here, you can view and manage all your sub-wallets and their respective balances."
                    });
                }

                if (step3) {
                    steps.push({
                        element: step3,
                        intro: "Select this option to view a list of all ads that have been created."
                    });
                }

                if (step4) {
                    steps.push({
                        element: step4,
                        intro: "This table displays the most recent leads, providing details and status updates."
                    });
                }

                // Add a final step if there are any valid steps
                if (steps.length > 0) {
                    steps.push({
                        intro: "This concludes the tour of the Dashboard page. You’re now ready to navigate and utilize the dashboard effectively."
                    });
                }

                intro.setOptions({
                    steps,
                    showStepNumbers: true,
                    tooltipClass: 'customTooltipClass'
                });

                intro.start();

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

                intro.oncomplete(function() {
                    sendAjaxRequest('completed');
                });

                intro.onexit(function() {
                    sendAjaxRequest('skipped');
                });
            }
        });
</script>
@endpush
