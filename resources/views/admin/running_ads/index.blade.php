@extends('layouts.admin')

@section('page-css')
<link href="{{ asset('front/assets/plugins/fileUpload/fileUpload.css') }}" rel="stylesheet" />
<link href="{{ asset('front') }}/assets/plugins/datatable/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.0/fullcalendar.min.css" />
@endsection

<style>
    #adsDetailModal .modal-body p {
        word-wrap: break-word;
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
</style>

@section('content')
<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-3 row-cols-xxl-3">
    <div class="col">
        <div class="card radius-10 border-0 border-start border-success border-3">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="">
                        <p class="mb-1">Total Main Wallet Balance</p>
                        <h4 class="mb-0 text-success">{{ get_price($main_wallet_bls) }}</h4>
                    </div>
                    <div class="ms-auto widget-icon bg-success text-white">
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
                        <p class="mb-1">Monthly CPL</p>
                        <h4 class="mb-0 text-primary">{{ $monthly_leads ? get_price($monthly_ads_spents /
                            $monthly_leads) : 0 }}</h4>
                    </div>
                    <div class="ms-auto widget-icon bg-primary text-white">
                        <i class="bi bi-currency-dollar"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="card radius-10 border-0 border-start border-danger border-3">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="">
                        <p class="mb-1">Monthly Leads</p>
                        <p class="mb-1" style="font-size: 9px; color:black;">({{ $start_of_month }} - {{ $end_of_month
                            }})</p>
                        <h4 class="mb-0 text-danger">{{ $monthly_leads }}</h4>
                    </div>
                    <div class="ms-auto widget-icon bg-danger text-white">
                        <i class="bi bi-currency-dollar"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- sub wallet code -->

<div><h4>Sub Wallets</h4></div>
@if(!count($sub_wallet))

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
<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-3 row-cols-xxl-3">
    @foreach($sub_wallet as $sub_account)
    <a
        href="{{ route('admin.sub_account.advertisements.get_sub_wallet_transactions', ['sub_account_id' => $sub_account_id, 'client_id' => hashids_encode($client_id), 'ads_id' => $sub_account->hashid]) }}">
        <div class="col">
            <div class="card radius-10 border-0 border-start border-primary border-3">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="overflow-hidden">
                            <p class="mb-1 text-truncate">{{$sub_account->adds_title}}</p>
                            <h4 class="mb-0 text-primary">{{get_price($sub_account->spend_amount)}} <span
                                    style=" font-size: 12px; ">(Spend Amount)</span></h4>
                        </div>
                        <div class="widget-icon bg-primary text-white" style="min-width:48;">
                            <i class="bi bi-currency-dollar"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </a>
    @endforeach
</div>
<!-- sub wallet code -->

<x-running_ads.daily_ads_spent_table :ads="$ads" />

<div class="row">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-center">
                        <h5 class="">TopUp Requests</h5>
                    </div>
                    <hr>
                    <div class="table-responsive">
                        <table id="wallet-template-table" class="table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>
                                        S NO:</th>
                                    <th>Transaction ID</th>
                                    <th >
                                        Client Name</th>
                                    <th >
                                        TopUp Amount</th>
                                    <th >
                                        Method</th>
                                    <th >
                                        Status</th>
                                    <th >
                                        Proof Image</th>
                                    <th>
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

<x-running_ads.ads_request_table />

<div class="row">
    <div class="col-xl-12 mx-auto">
        <div class="card">
            <div class="card-body">
                <div class="card-title d-flex align-items-center">
                    <h5 class="">Main Wallet Transactions</h5>
                </div>
                <hr>
                <div class="table-responsive">
                    <table id="main-wallet-template-table" class="table table-striped table-bordered"
                        style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Amount IN</th>
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

<div class="row">
    <div class="col-xl-12 mx-auto">
        <div class="card">
            <div class="card-body">
                <div class="card-title d-flex align-items-center">
                    <h5 class="">Google Ads</h5>
                </div>
                <hr>
                <div class="table-responsive">
                    <table id="google_ads-template-table" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Client</th>
                                <th>Ads Request</th>
                                <th></th>
                                <th>Advertisement</th>
                                <th>Format</th>
                                <th>Ads Group</th>
                                <th>Spent</th>
                                <th>Impressions</th>
                                <th>Clicks</th>
                                <th>Conversions</th>
                                <th>CPM</th>
                                <th>CPC</th>
                                <th>CTR</th>
                                <th>Conversion Rate</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($google_ads as $google_ad)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $google_ad['client'] }}</td>
                                <td>{{ $google_ad['ad_request'] }}</td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch"
                                            id="flexSwitchCheckChecked" @if ($google_ad['adGroupAd']['status'])
                                            @checked($google_ad['adGroupAd']['status']==='ENABLED' ) @endif disabled>
                                    </div>
                                </td>
                                <td>{{ $google_ad['adGroupAd']['name'] ?? ''}}</td>
                                <td>{{ $google_ad['campaign']['advertisingChannelType'] ?? ''}}</td>
                                <td>{{ $google_ad['adGroup']['name'] ?? '' }}</td>
                                <td>{{ $google_ad['metrics']['costMicros'] ? round($google_ad['metrics']['costMicros']  / 1000000, 2) : 0 }} SGD</td>
                                <td>{{ $google_ad['metrics']['impressions'] ?? 0 }}</td>
                                <td>{{ $google_ad['metrics']['clicks'] ?? 0 }}</td>
                                <td>{{ $google_ad['metrics']['conversions'] }}</td>
                                <td>{{ isset($google_ad['metrics']['averageCpm']) ? round($google_ad['metrics']['averageCpm']  / 1000000, 2) : 0 }}</td>
                                <td>{{ isset($google_ad['metrics']['averageCpc']) ? round($google_ad['metrics']['averageCpc']  / 1000000, 2) : 0}}</td>
                                <td>{{ isset($google_ad['metrics']['ctr']) ? round($google_ad['metrics']['ctr'] * 100, 2) : 0}}%</td>
                                <td>
                                    @if ($google_ad['metrics']['clicks'] === '0')
                                    0%
                                    @else
                                    {{ ($google_ad['metrics']['conversions'] / $google_ad['metrics']['clicks']) * 100;
                                    }}%
                                    @endif
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

<div class="row">
    <div class="col-xl-12 mx-auto">
        <div class="card">
            <div class="card-body">
                <div class="card-title d-flex align-items-center">
                    <h5 class="">Blocked Ads Due to Low Balance</h5>
                </div>
                <hr>
                <div class="table-responsive">
                    <table id="low_bls-template-table" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Client Name</th>
                                <th>Ads Title</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Action</th>
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


<div class="modal fade" id="changeTopUpStatus" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change Top Up Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form
                action="{{ route('admin.sub_account.advertisements.change-status', ['sub_account_id' => $sub_account_id]) }}"
                method="post" id="ajaxForm">
                @csrf
                <input type="hidden" id="wallet_id" name="wallet_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="topup_status">Status</label>
                        <select name="topup_status" id="topup_status" class="form-control" required>
                            <option value="">Select Status</option>
                            <option value="pending">Processing</option>
                            <option value="approve">Completed</option>
                            <option value="canceled">Canceled</option>
                            <option value="rejected">Declined</option>
                        </select>
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

<div class="row">
    <div class="col-xl-12 mx-auto">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <h5 class="mb-0">All Leads</h5>
                </div>
                <hr>
                <div class="table-responsive">
                    <table id="all_leads-template-table" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Ads Name</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone Number</th>
                                <th>Qualifying Questions</th>
                                <th>Status</th>
                                <th>Action</th>
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

<div class="row">
    <div class="col-xl-12 mx-auto">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <h5 class="mb-0">Latest Leads</h5>
                </div>
                <hr>
                <div class="row">
                    <div class="col-12 col-lg-6 col-xl-4">
                        <div class="form-group my-2">
                            <label for="lead_ads_filter">Filter by Ads Title:</label>
                            <select id="lead_ads_filter" class="form-select">
                                <option value="">All</option>
                                @foreach ($ads as $ad)
                                    <option value="{{ $ad->id }}">{{ $ad->adds_title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="ppc_leads-template-table" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Ads Name</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone Number</th>
                                <th>Qualifying Questions</th>
                                <th>Status</th>
                                <th>Action</th>
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

<div class="modal fade" id="changeLeadStatus" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change Lead Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form
                action="{{ route('admin.sub_account.advertisements.lead_status', ['sub_account_id' => $sub_account_id]) }}"
                method="post" id="ajaxForm">
                @csrf
                <input type="hidden" id="lead_id" name="lead_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="lead_status">Status</label>
                        <select name="lead_status" id="lead_status" class="form-control" required>
                            <option value="">Select Status</option>
                            <option value="contacted">Contacted</option>
                            <option value="appointment_set">Appointment Set</option>
                            <option value="burst">Burst</option>
                            <option value="follow_up">Follow Up</option>
                            <option value="call_back">Call Back</option>
                        </select>
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
{{-- Ads Detail model --}}
<div class="modal fade" id="adsDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ads Detail</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="">Title:</label>
                        <p id="title"></p>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="">Spend Amount:</label>
                        <p id="amount"></p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="type">Status:</label>
                        <p id="status"></p>

                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="">Discord Link:</label>
                        <p id="discordLink"></p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <label for="">Domain Is :</label>
                        <p id="domain_is"></p>

                    </div>
                    <div class="col-md-6">
                        <label for="">Domain name :</label>
                        <p id="domain_name"></p>

                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label for="">Hosting Is:</label>
                        <p id="hosting_is"></p>

                    </div>
                    <div class="col-md-6">
                        <label for="">Hosting details :</label>
                        <p id="hosting_details"></p>

                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="">Website URL :</label>
                        <p id="web_url"></p>

                    </div>
                </div>
                <!-- <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="type">Type:</label>
                        <p id="type"></p>
                    </div>
                </div> -->

                {{-- <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="type">Domain:</label>
                        <p id="domain"></p>
                    </div>
                </div> --}}


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
{{-- Ads Detail model --}}


{{-- Change Ads status model --}}
<div class="modal fade" id="changeAdsStatus" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Ads Detail</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{route('admin.sub_account.advertisements.edit_add', ['sub_account_id' => $sub_account_id])}}"
                method="POST" id="ajaxForm">
                @csrf
                <input type="hidden" id="adsId" name="ads_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="ads_status">Status:</label>
                            <select name="ads_status" id="ads_status" class="form-select" required>
                                <option value="pending">Pending Creation</option>
                                <option value="complete">Complete</option>
                                <option value="running">Live</option>
                                <option value="reject">Stopped</option>
                                <option value="inactive">Invactive</option>
                                <option value="test">Test</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="discord_link">Discord Link:</label>
                            <input type="url" id="discord_link" name="discord_link" class="form-control" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="name_domain">Domain Name:</label>
                            <input type="text" name="name_domain" id="name_domain" placeholder="Enter domain name"
                                class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label for="name_domain">Website Url:</label>
                            <input type="text" name="website_url" id="website_url" placeholder="" class="form-control"
                                required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="e_wallet">E Wallet:</label>
                            <select name="e_wallet" id="e_wallet" class="form-select" required>
                                <option value="normal">Normal</option>
                                <option value="deduct_balance_real_time">Deduct Balance Real Time</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-6">
                            <label for="inputName" class="form-label">Google Account <span></label>
                            <select name="google_account_id" id="google_account_id" class="form-control">
                                <option value="">Select google account</option>
                                @foreach ($google_accounts as $google_account)
                                    <option value="{{ $google_account->id }}">
                                        {{ $google_account->email }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-xl-6">
                            <label for="inputName" class="form-label">Customer ID <span></label>
                            <select name="customer_id" id="customer_id" class="form-control">
                                <option value="">Select customer ID</option>
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


{{-- Change ads_running status model --}}
<div class="modal fade" id="ads_running" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change Ads Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form
                action="{{ route('admin.sub_account.advertisements.change-ads_running-status', ['sub_account_id' => $sub_account_id]) }}"
                method="post" id="ajaxFormAds">
                @csrf
                <input type="hidden" id="ads_id2" name="ads_id">
                <div class="modal-body">
                    <!-- <div class="form-group">
                        <label for="user_bls">User Balance</label>
                        <input type="text" name="user_bls" id="user_bls" class="form-control" readonly>
                    </div> -->
                    <div class="form-group">
                        <label for="ads_status">Status</label>
                        <select name="ads_status" id="ads_status2" class="form-select" required>
                            <option value="">Select Status</option>
                            <!-- <option value="pending">Pending</option> -->
                            <option value="running">Live</option>
                            <option value="reject">Stopped</option>
                            <option value="complete">Complete</option>
                        </select>
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
{{-- Change ads_running status model --}}


{{-- ads amount refund model --}}
<div class="modal fade" id="ads_amt_refund" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ads Reremaining Balance Refund</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form
                action="{{ route('admin.sub_account.advertisements.ads-remaining-balance-refund', ['sub_account_id' => $sub_account_id]) }}"
                method="post" id="ajaxFormAds">
                @csrf
                <input type="hidden" id="refun_ads_id" name="ads_id">
                <input type="hidden" id="refun_client_id" name="client_id">
                <input type="hidden" id="refund_amt" name="refund_amt">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="user_bls">Reremaining Balance</label>
                        <input type="text" name="user_bls" id="user_bls" class="form-control" readonly>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary form-submit-btn">Refund</button>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- ads amount refund model --}}

{{-- view lead details  --}}
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
{{-- view lead details  --}}


{{-- view lead details info  --}}
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
{{-- view lead details info  --}}

@endsection

@section('page-scripts')
<script src="{{ asset('front') }}/assets/plugins/datatable/js/jquery.dataTables.min.js"></script>
<script src="{{ asset('front') }}/assets/plugins/datatable/js/dataTables.bootstrap5.min.js"></script>
<script src="{{ asset('front') }}/assets/js/table-datatable.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.0/fullcalendar.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.8/clipboard.min.js"></script>

<script>
    let googleAccountId;
    let customerId;

    var sub_account_id = "{{ $sub_account_id }}";
        $('.single-select').select2({
            theme: 'bootstrap4',
            width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
            placeholder: $(this).data('placeholder'),
            allowClear: Boolean($(this).data('allow-clear')),
        });

        $(document).on('click', ".view_lead_detail_id", function() {
            let data = $(this).data('data');
            //  alert(data.name);
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

        // agent_specific_action

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

        // end

    function getCustomerId() {
        $('.customer-id-option').remove();
        if (googleAccountId) {
            $.ajax({
                url: "{{ route('google_ads.customer_id') }}",
                method: 'GET',
                data: {
                    google_account_id: googleAccountId,
                },
                success: function(result) {
                    for (const customer of result) {
                        $('#customer_id').append(`
                            <option value="${customer['customer']['id']}"
                                class="customer-id-option"
                                ${customer['customer']['manager'] ? ' disabled' : ''}
                                ${customer['customer']['id'] == customerId ? ' selected' : ''}>
                                ${customer['customer']['id']} ${customer['customer']['descriptiveName'] ? ' - ' + customer['customer']['descriptiveName'] : ''}
                            </option>
                        `);
                    }
    
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        }
    }
    
    $(document).ready(function() {
        var today = new Date().toISOString().split('T')[0];
        var yesterday = new Date();
        yesterday.setDate(yesterday.getDate() - 3);
        var yesterdayFormatted = yesterday.toISOString().split('T')[0];

        $('#datePicker').attr('min', yesterdayFormatted);
        $('#datePicker').attr('max', today);
        $('#datePicker').val(yesterdayFormatted);

        validations = $("#addEventForm").validate();
        $('#ajaxForm, #ajaxFormAds').submit(function(e) {
            e.preventDefault();
            validations = $("#ajaxForm").validate();
            if (validations.errorList.length != 0) {
                return false;
            }
            var url = $(this).attr('action');
            var param = new FormData(this);
            my_ajax(url, param, 'post', function(res) {}, true);
        });

        $('#google_account_id').change(function() {
            $('#customer_id').val('');
            $('.customer-id-option').remove();
            
            googleAccountId = $('select[name="google_account_id"]').val();

            if (googleAccountId) {
                getCustomerId();
            }
        });
    });

    $(document).on('click', '.change-ads-status', function() {
        let data = $(this).data('data');
        let discord_link = data.discord_link;
        let type = data.type;
        let status = data.status;
        let id = data.id;
        let domain_name = data.domain_name;
        let web_url = data.website_url;
        let description = data.description;
        googleAccountId = data.google_account_id;
        if (customerId != data.customer_id) {
            customerId = data.customer_id;
            getCustomerId();
        }

        $('#adsId').val(id);
        $('#website_url').val(web_url);
        $('#discord_link').val(discord_link);
        $("#status").val(status);
        $("#name_domain").val(domain_name);
        $("#ads_status").val(status);
        $("#google_account_id").val(googleAccountId);
        $('#changeAdsStatus').modal('show');
    });

        $('#addDailyAdSpentFormAjax').submit(function(e) {
            e.preventDefault();
            let validations = $("#addDailyAdSpentFormAjax").validate();
            if (validations.errorList.length != 0) {
                return false;
            }
            var url = $(this).attr('action');
            var param = new FormData(this);
            my_ajax(url, param, 'post', function(res) {
                if (res.success) {
                    // edit-daily-ads-spent
                    $('#addDailyAdSpentFormAjax')[0].reset();

                    var today = new Date().toISOString().split('T')[0];
                    var yesterday = new Date();
                    yesterday.setDate(yesterday.getDate() - 3);
                    var yesterdayFormatted = yesterday.toISOString().split('T')[0];

                    // Set minimum and maximum date for the date picker
                    $('#datePicker').attr('min', yesterdayFormatted);
                    $('#datePicker').attr('max', today);
                    $('#datePicker').val(yesterdayFormatted);
                    getDailyAdsSpent();
                }
            }, true);
        });

        getMainWallet();
        getTopUps();
        getTransactions();
        getPpcLeads();
        getAllLowBlsAds();

        function getTransactions() {
            var client = $('#client').val();
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
                    url: "{{ route('admin.sub_account.advertisements.transactions', ['sub_account_id' => '"+sub_account_id "']) }}",
                    data: function(d) {
                        d.search = $('#transaction-table').DataTable().search();
                        d.client = client
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
                        data: 'client_name',
                        name: 'client_name',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'date',
                        name: 'date',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'trans_type',
                        name: 'trans_type',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'debit',
                        name: 'debit',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'credit',
                        name: 'credit',
                        orderable: true,
                        searchable: false
                    },
                    // {
                    //     data: 'vat_charges',
                    //     name: 'vat_charges',
                    //     orderable: true,
                    //     searchable: false
                    // },
                    // {
                    //     data: 'available_balance',
                    //     name: 'available_balance',
                    //     orderable: true,
                    //     searchable: false
                    // },
                ],
            });
        }

        function getTopUps() {
            var client = $('#client').val();
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
                    url: "{{ route('admin.sub_account.advertisements.get_topups', ['sub_account_id' => '"+sub_account_id "']) }}",
                    data: function(d) {
                        d.search = $('#wallet-template-table').DataTable().search();
                        d.client = client
                    }
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
                        data: 'transaction_id',
                        name: 'transaction_id',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'client_name',
                        name: 'client_name',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'topup_amount',
                        name: 'topup_amount',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'method',
                        name: 'method',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'slip',
                        name: 'slip',
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


        function getMainWallet() {
            var client = {{$client_id}};
            if ($.fn.DataTable.isDataTable('#main-wallet-template-table')) {
                $('#main-wallet-template-table').DataTable().destroy();
            }
            $('#main-wallet-template-table').DataTable({
                processing: true,
                serverSide: true,
                "order": [
                    [0, "desc"]
                ],
                "pageLength": 10,
                "lengthMenu": [10, 50, 100, 150, 500],
                ajax: {
                    url: "{{ route('admin.sub_account.advertisements.get_main_wallet', ['sub_account_id' => '"+sub_account_id "']) }}",
                    data: function(d) {
                        d.search = d.search = $('#main-wallet-template-table').DataTable().search();
                        d.client = client
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
                        orderable: true,
                        searchable: false
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        orderable: true,
                        searchable: false
                    }
                ],
            });
        }

        function getAllLowBlsAds() {
            var client = $('#client').val();
            if ($.fn.DataTable.isDataTable('#low_bls-template-table')) {
                $('#low_bls-template-table').DataTable().destroy();
            }
            $('#low_bls-template-table').DataTable({
                processing: true,
                serverSide: true,
                "order": [
                    [0, "desc"]
                ],
                "pageLength": 10,
                "lengthMenu": [10, 50, 100, 150, 500],
                ajax: {
                    url: "{{ route('admin.sub_account.advertisements.get_low_bls_ads', ['sub_account_id' => '"+sub_account_id "']) }}",
                    data: function(d) {
                        d.search = d.search = $('#low_bls-template-table').DataTable().search();
                        d.client = client
                    },
                },
                columns: [{
                        data: null,
                        render: function(data, type, row, meta) {
                            return meta.row + 1;
                        },
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'client_name',
                        name: 'client_name',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'adds_title',
                        name: 'adds_title',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'type',
                        name: 'type',
                        orderable: true,
                        searchable: false
                    },
                    {
                        data: 'status',
                        name: 'status',
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

        function getAllLeads() {
            if ($.fn.DataTable.isDataTaall('#all_leads-template-table')) {
                $('#all_leads-template-table').DataTable().destroy();
            }
            $('#all_leads-template-table').DataTable({
                processing: true,
                serverSide: true,
                "order": [
                    [0, "desc"]
                ],
                "pageLength": 10,
                "lengthMenu": [10, 50, 100, 150, 500],
                ajax: {
                    type: 'POST',
                    url: "{{ route('admin.sub_account.advertisements.get_all_leads', ['sub_account_id' => '"+sub_account_id "']) }}",
                    data: function(d) {
                        d.search = $('#all_leads-template-table').DataTable().search();
                        d.ppc = ppc;
                        d.client = client;
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
                        data: 'client_name',
                        name: 'client_name',
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

        function getPpcLeads() {
            var ppc = $('#ppc_leads').val();
            var client = $('#client').val();
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
                    url: "{{ route('admin.sub_account.advertisements.get_ppc_leads', ['sub_account_id' => '"+sub_account_id "']) }}",
                    data: function(d) {
                        d.ads_id = $('#lead_ads_filter').val();
                        d.search = $('#ppc_leads-template-table').DataTable().search();
                        d.ppc = ppc;
                        d.client = client;
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
                        data: 'client_name',
                        name: 'client_name',
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

        $('#lead_ads_filter').on('change', function() {
            getPpcLeads();
        });


        $(document).on('click', '.change-topup-status', function() {
            let clientId = $('#client_id').val();
            let status = $(this).data('status');
            let id = $(this).data('id');
            $('#topup_status').val(status);
            $('#wallet_id').val(id);
            $('#changeTopUpStatus').modal('show');

        });


        $(document).on('click', '.ads-running', function() {
            let status = $(this).data('status');
            let id = $(this).data('clientid');
            let adsid = $(this).data('id');

            var data = {
                '_token': "{{ csrf_token() }}",
                clientid: id,
            };

            getAjaxRequests(
                "{{ route('admin.sub_account.advertisements.get_user_bls', ['sub_account_id' => '"+sub_account_id "']) }}",
                data, 'POST', true,
                function(res) {
                    if (res.success) {
                        $('#ads_status2').val(status);
                        $('#ads_id2').val(adsid);
                        $('#user_bls').val(res.bls);
                        $('#ads_running').modal('show');
                    }
                });

        });

        $(document).on('click', '.ads_amt_refund', function() {
            let status = $(this).data('status');
            let id = $(this).data('clientid');
            let adsid = $(this).data('id');

            var data = {
                '_token': "{{ csrf_token() }}",
                clientid: id,
                adsid:adsid
            };

            getAjaxRequests(
                "{{ route('admin.sub_account.advertisements.get_user_bls', ['sub_account_id' => '"+sub_account_id "']) }}",
                data, 'POST', true,
                function(res) {
                    if (res.success) {
                        $('#ads_status2').val(status);
                        $('#refun_ads_id').val(adsid);
                        $('#refun_client_id').val(id);
                        $('#user_bls').val(res.bls);
                        $('#refund_amt').val(res.amt);
                        $('#ads_amt_refund').modal('show');
                    }
                });

        });

        $(document).on('change', '#client', function() {
            $('.ajaxFormClient').submit();
        });

        $(document).on('click', '.view_detail', function() {
            let data = $(this).data('data');
            let title = data.adds_title;
            let email = data.email;
            let amount = data.spend_amount;
            let discord_link = data.discord_link;
            let type = data.type;
            let status = data.status;
            let spend = data.spend_amount;
            let domain_name = data.domain_name;
            let web_url = data.website_url;
            let description = data.description;
            let domain = data.domain_is;
            let hosting_is = data.hosting_is;
            let hosting_details = data.hosting_details;
            let formattedDomain = domain.replace(/_/g, " ");
            formattedDomain = formattedDomain.charAt(0).toUpperCase() + formattedDomain.slice(1);

            let formattedHosting = hosting_is.replace(/_/g, " ");
            formattedHosting = formattedHosting.charAt(0).toUpperCase() + formattedHosting.slice(1);

            if (data.spend_type === 'daily') {
                let day = 30;
                if (data.status === 'running') {
                    let days = Math.floor(data.spend_amount / data.daily_budget);
                    day = Math.min(days, 30);
                }

                if (data.spend_amount !== 0 && data.status === 'running') {
                    budget = data.daily_budget * day;
                } else {
                    budget = data.daily_budget * 30;
                }
            } else {
                budget = data.daily_budget;
            }

            $('#title').html(title);
            $('#amount').html('SGD '+budget);
            $('#discordLink').html(discord_link);
            // $("#type").html(ads_type_text(type));
            $("#status").html(ads_status_text(status));
            $("#spend").html(spend);
            $("#domain_name").html(domain_name);
            $('#web_url').html(web_url);
            $('#domain_is').html(formattedDomain);
            $('#hosting_is').html(formattedHosting);
            $('#hosting_details').html(hosting_details);

            $('#adsDetailModal').modal({
                backdrop: 'static',
                keyboard: false
            });
            $('#adsDetailModal').modal('show');
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
                "{{ route('admin.sub_account.advertisements.lead_admin_status', ['sub_account_id' => '"+sub_account_id "']) }}",
                data, 'POST', true,
                function(res) {
                    if (res.success) {
                        toast(res.success, "Success!", 'success', 3000);
                    }
                });
        });

        function ads_status_text(status) {
            let text = '';
            if (status == "pending") {
                text = 'Pending';
            } else if (status == "running") {
                text = 'Running';
            } else if (status == "complete") {
                text = 'Complete';
            } else {
                text = 'Rejected';
            }
            return text;
        }

        function ads_type_text(type) {
            let texts = [];

            type.split(",").forEach(function(item) {
                switch (item.trim()) {
                    case "3in1_valuation":
                        texts.push('3 in 1 Valuation');
                        break;
                    case "hbd_valuation":
                        texts.push('HBD Valuation');
                        break;
                    case "condo_valuation":
                        texts.push('Condo Valuation');
                        break;
                    case "landed_valuation":
                        texts.push('Landed Valuation');
                        break;
                    case "rental_valuation":
                        texts.push('Rental Valuation');
                        break;
                    case "post_launch_generic":
                        texts.push('Post Launch Generic');
                        break;
                    case "executive_launch_generic":
                        texts.push('Executive Launch Generic');
                        break;
                }
            });

            return texts;
        }



        // status for client lead

</script>
@endsection
