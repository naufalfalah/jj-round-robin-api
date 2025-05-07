@extends('layouts.admin')

@push('styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link href="{{ asset('front') }}/assets/plugins/datatable/css/dataTables.bootstrap5.min.css" rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('front') }}/assets/plugins/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Product+Sans:wght@100;200;300;400;500;600;700&display=swap" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <link rel="stylesheet" href="{{ asset('front') }}/assets/css/google-report.css" />
    
    <style>
        .loader-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 999;
            opacity: 0.7;
            transition: opacity 0.3s ease-in-out;
        }

        .loader {
            border: 8px solid #f1f1f1;
            border-top: 10px solid #39548A;
            border-radius: 50%;
            width: 80px;
            height: 80px;
            animation: spin 2s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .apexcharts-menu-icon {
            display: none;
        }

        #ctr-chart {
            background-color: white !important;
        }
    </style>
@endpush

@section('content')
    <div class="loader-container" id="loader-container" style="display: none;">
        <div class="loader"></div>
    </div>

    <div class="alert border-0 border-danger border-start border-4 bg-light-danger alert-dismissible fade show py-2" id="act_expire_alert" style="display: none;">
        <div class="d-flex align-items-center">
            <div class="fs-3 text-danger">
                <i class="bi bi-x-circle-fill"></i>
            </div>
            <div class="ms-3">
                <div class="text-danger">Google Ad account has expired.</div>
            </div>
        </div>
    </div>

    <x-google_report.filter :googleAds="$google_ads" />

    <div class="row mb-3">
        <div class="col-xl-4">
            <a href="javascript:void(0);" class="text-dark" data-bs-toggle="tooltip" data-bs-placement="top"
                title="Last Updated: {{ $last_updated }} ({{ $last_updated_date }})">
                <i class="fa-regular fa-clock"></i> {{ $last_updated }}
            </a>
        </div>
        <div class="col-8">
            @if (isset(auth()->user()->provider_id) && !empty(auth()->user()->provider_id) && !empty(auth('admin')->user()->google_access_token))
                <a href="javascript:void(0);" class="btn btn-primary float-end mb-2"
                    id="generate_pdf_btn" data-bs-toggle="tooltip" data-bs-placement="top" title="Generate PDF File">
                    <i class="fa-solid fa-file-pdf"></i>
                </a>
                <a href="javascript:void(0);" id="refresh_google_report" class="btn btn-primary float-end me-1 mb-2"
                    data-bs-toggle="tooltip" data-bs-placement="top"  title="Refresh Google Ads Data">
                    <i class="fa-solid fa-rotate"></i>
                </a>

                <a href="javascript:void(0);" class="btn btn-primary float-end me-1 mb-2" id="date_range_btn"
                    data-bs-toggle="tooltip" data-bs-placement="top" title="Date Range">
                    <i class="fa-regular fa-calendar-days me-1"></i>
                </a>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-xl-4">
            <div class="card radius-10">
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-xl-12">
                            <h6>Top Level Summary</h6>
                        </div>
                    </div>
                    {{-- Row 1 --}}
                    <div class="row mb-4">
                        {{-- Impressions --}}
                        <div class="col-sm-6 col-lg-3">
                            <div class="row">
                                <div class="col-12">
                                    <p class="mb-1 sub-title">Impr.&nbsp;
                                        <span class="text-dark" data-bs-toggle="tooltip" data-bs-placement="top" 
                                            title="It displays the number of times your ad is shown on a search  engine result page or related sites on the Google network.">
                                            <i class="fa-solid fa-circle-info"></i>
                                        </span>
                                    </p>
                                    <h4 class="mb-0 text-dark counter">
                                        {{ $total_impressions > 0 ? number_with_suffixes($total_impressions) : 0}}
                                    </h4>
                                </div>
                                <div class="col-12 ms-auto">
                                    <div id="chart5" class="small-chart"></div>
                                </div>
                            </div>
                        </div>
                        {{-- Cost --}}
                        <div class="col-sm-6 col-lg-3">
                            <div class="row">
                                <div class="col-12">
                                    <p class="mb-1 sub-title">Cost&nbsp;
                                        <span class="text-dark" data-bs-toggle="tooltip" data-bs-placement="top" 
                                            title="It is the sum of your cost per click (CPC) spend.">
                                            <i class="fa-solid fa-circle-info"></i>
                                        </span>
                                    </p>  
                                    <h4 class="mb-0 text-dark counter">
                                        {{ $total_cost > 0 ? number_with_suffixes($total_cost) : 0 }}&nbsp;
                                        <span class="text-dark">SGD</span>
                                    </h4>
                                </div>
                                <div class="col-12 ms-auto">
                                    <div id="chart8" class="small-chart"></div>
                                </div>
                            </div>
                        </div>
                        {{-- Clicks --}}
                        <div class="col-sm-6 col-lg-3">
                            <div class="row">
                                <div class="col-12">
                                    <p class="mb-1 sub-title">Clicks&nbsp;
                                        <span class="text-dark" data-bs-toggle="tooltip" data-bs-placement="top" 
                                            title="It indicates how many times an ad was clicked.">
                                            <i class="fa-solid fa-circle-info"></i>
                                        </span>
                                    </p>
                                    <h4 class="mb-0 text-dark counter">
                                        {{ $total_clicks > 0 ? number_with_suffixes($total_clicks) : 0 }}
                                    </h4>
                                </div>
                                <div class="col-12 ms-auto">
                                    <div id="chart6" class="small-chart"></div>
                                </div>
                            </div>
                        </div>
                        {{-- Conversions --}}
                        <div class="col-sm-6 col-lg-3">
                            <div class="row">
                                <div class="col-12">
                                    <p class="mb-1 sub-title">Conversions
                                        <span class="text-dark" data-bs-toggle="tooltip" data-bs-placement="top" 
                                            title="It is a result of someone clicking on your ad and taking an action you have defined such as purchasing or filling up a form.">
                                            <i class="fa-solid fa-circle-info"></i>
                                        </span>
                                    </p>
                                    <h4 class="mb-0 text-dark counter">{{ @$total_conversions ?? 0 }}</h4>
                                </div>
                                <div class="col-12 ms-auto">
                                    <div id="chart10" class="small-chart"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Row 2 --}}
                    <div class="row mb-4">
                        {{--  --}}
                        <div class="col-sm-12 col-md-4">
                            <div class="row">
                                <div class="col-12">
                                    <p class="mb-1 sub-title">Ctr&nbsp;
                                        <span class="text-dark" data-bs-toggle="tooltip" data-bs-placement="top" 
                                            title="It is a performance metric that displays how often people click on your ad after it has been displayed.">
                                            <i class="fa-solid fa-circle-info"></i>
                                        </span>
                                    </p>
                                    <h4 class="mb-0 text-dark counter">
                                        {{ !empty($total_clicks) && !empty($total_impressions) ? number_format($total_clicks / $total_impressions * 100, 2) : '0.00' }}%
                                    </h4>
                                </div>
                                <div class="col-12 ms-auto">
                                    <div id="chart_ctr" class="small-chart"></div>
                                </div>
                            </div>
                        </div>
                        {{--  --}}
                        <div class="col-sm-12 col-md-4">
                            <div class="row">
                                <div class="col-12 sub-title">
                                    <p class="mb-1">ROAS&nbsp;
                                        <span class="text-dark" data-bs-toggle="tooltip" data-bs-placement="top" 
                                            title="It is a roas metric that displays return on ad spend.">
                                            <i class="fa-solid fa-circle-info"></i>
                                        </span>
                                    </p>
                                    <h4 class="mb-0 text-dark counter">0%</h4>
                                </div>
                                <div class="col-12">
                                    <div id="chart7" class="small-chart"></div>
                                </div>
                            </div>
                        </div>
                        {{--  --}}
                        <div class="col-sm-12 col-md-4">
                            <div class="col-12">
                                <p class="mb-1">Average CPC&nbsp;
                                    <span class="text-dark" data-bs-toggle="tooltip" data-bs-placement="top" 
                                        title="It is calculated by dividing the total cost of your clicks by the total number of clicks."><i class="fa-solid fa-circle-info"></i></span></p>
                                <h4 class="mb-0 text-dark counter">
                                    {{ is_numeric($total_cost) && is_numeric($total_clicks) && $total_clicks != 0 ? round($total_cost / $total_clicks, 2) : 0 }}&nbsp;
                                    <span>SGD</span>
                                </h4>
                            </div>
                            <div class="col-12 ms-auto">
                                <div id="chart9" class="small-chart"></div>
                            </div>
                        </div>
                    </div>
                    {{-- Row 3 --}}
                    <div class="row">
                        {{--  --}}
                        <div class="col-sm-12 col-md-4">
                            <div class="row">
                                <div class="col-12">
                                    <p class="mb-1 sub-title">Conversion Action&nbsp;
                                        <span class="text-dark" data-bs-toggle="tooltip" data-bs-placement="top" 
                                            title="It is a result of someone clicking on your ad and taking an action you have defined such as purchasing or filling up a form.">
                                            <i class="fa-solid fa-circle-info"></i>
                                        </span>
                                    </p>
                                    <h4 class="mb-0 text-dark counter">{{ @$total_conversions ?? 0 }}</h4>
                                </div>
                                <div class="col-12 ms-auto">
                                    <div id="chart_conversations_action" class="small-chart"></div>
                                </div>
                            </div>
                        </div>
                        {{--  --}}
                        <div class="col-sm-12 col-md-4">
                            <div class="row">
                                <div class="col-12">
                                    <p class="mb-1 sub-title">Conversion Rate&nbsp;
                                        <span class="text-dark" data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="It is calculated by simply taking the number of conversions dividing it by the number of total clicks during the same time period.">
                                            <i class="fa-solid fa-circle-info"></i>
                                        </span>
                                    </p>
                                    <h4 class="mb-0 text-dark counter">
                                        {{ $total_clicks != 0 ? round(@$total_conversions / $total_clicks * 100, 2) : 0 }}%
                                    </h4>
                                </div>
                                <div class="col-12 ms-auto">
                                    <div id="chart11" class="small-chart"></div>
                                </div>
                            </div>
                        </div>
                        {{--  --}}
                        <div class="col-sm-12 col-md-4">
                            <div class="row">
                                <div class="col-12">
                                    <p class="mb-1 sub-title">Cost Per Conversion&nbsp;
                                        <span class="text-dark" data-bs-toggle="tooltip" data-bs-placement="top" 
                                            title="It is the amount you have paid per conversion in ads.">
                                            <i class="fa-solid fa-circle-info"></i>
                                        </span>
                                    </p>
                                    <h4 class="mb-0 text-dark counter">
                                        {{ is_numeric($total_cost) && is_numeric($total_conversions) && $total_conversions != 0 ? round($total_cost / $total_conversions, 1) : 0 }}&nbsp;
                                        <span>SGD</span>
                                    </h4>
                                </div>
                                <div class="col-12 ms-auto">
                                    <div id="chart12" class="small-chart"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-xl-12">
                            <h6 class="title">Summary&nbsp;
                                <span class="text-dark" data-bs-toggle="tooltip" data-bs-placement="top" 
                                    title="Take a look at the progress of the PPC campaign over a period of time.">
                                    <i class="fa-solid fa-circle-info"></i>
                                </span>
                            </h6>
                        </div>
                    </div>
                    <div id="summaryChart"></div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-xl-12">
                            <h6 class="title">Performance&nbsp;
                                <span class="text-dark" data-bs-toggle="tooltip" data-bs-placement="top"
                                    title="It shows the real-time performance data of your PPC campaign over a period of time.">
                                    <i class="fa-solid fa-circle-info"></i>
                                </span>
                            </h6>
                        </div>
                    </div>
                    <div id="performanceChart"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- <div class="row">
        <div class="col-xl-4">
            <div class="card radius-10">
                <div class="card-header">
                    <b>Campaign Performace Report</b>
                </div>
                <div class="card-body">
                    <div class="row">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card radius-10">
                <div class="card-header">
                    <b>Conversion Over Time</b>
                </div>
                <div class="card-body">
                    <div class="row">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card radius-10">
                <div class="card-header">
                    <b>Top Search Terms</b>
                </div>
                <div class="card-body">
                    <div class="row">
                    </div>
                </div>
            </div>
        </div>
    </div> --}}

    <div class="row row-cols-1 row-cols-md-3 row-cols-lg-3 row-cols-xxl-4">
        {{-- <div class="col">
            <div class="card radius-10">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            @if ($total_impressions > 0)
                            <h4 class="mb-0 text-primary">{{ number_with_suffixes($total_impressions) }}</h4>
                            @else
                            <h4 class="mb-0 text-primary">0</h4>
                            @endif
                            <p class="mb-1">Impressions <span style="font-size: 13px;" class="text-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="It displays the number of times your ad is shown on a search  engine result page or related sites on the Google network."><i class="fa-solid fa-circle-info"></i></span></p>
                        </div>
                        <div class="col-12 ms-auto">
                            <div id="chart5"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}

        {{-- <div class="col">
            <div class="card radius-10">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            @if ($total_clicks > 0)
                                <h4 class="mb-0 text-primary">{{ number_with_suffixes($total_clicks) }}</h4>
                            @else
                                <h4 class="mb-0 text-primary">{{ $total_clicks }}</h4>
                            @endif
                            
                            <p class="mb-1">Clicks <span style="font-size: 13px;" class="text-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="It indicates how many times an ad was clicked."><i class="fa-solid fa-circle-info"></i></span></p>
                        </div>
                        <div class="col-12 ms-auto">
                            <div id="chart6"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}

        {{-- <div class="col">
            <div class="card radius-10">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            @if (!empty($total_clicks) && !empty($total_impressions))
                                <h4 class="mb-0 text-primary">{{ number_format($total_clicks / $total_impressions * 100, 2) ?? 0 }}%</h4>
                            @else
                                <h4 class="mb-0 text-primary">0.00%</h4>
                            @endif
                            <p class="mb-1">Ctr <span style="font-size: 13px;" class="text-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="It is a performance metric that displays how often people click on your ad after it has been displayed."><i class="fa-solid fa-circle-info"></i></span></p>
                        </div>
                        <div class="col-12 ms-auto">
                            <div id="chart_ctr"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}

        {{-- <div class="col">
            <div class="card radius-10">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <h4 class="mb-0 text-primary">0%</h4>
                            <p class="mb-1">ROAS <span style="font-size: 13px;" class="text-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="It is a roas metric that displays return on ad spend."><i class="fa-solid fa-circle-info"></i></span></p>
                        </div>
                        <div class="col-12 ms-auto">
                            <div id="chart7"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}

        {{-- <div class="col">
            <div class="card radius-10">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            @if ($total_cost > 0)
                            <h4 class="mb-0 text-primary">{{ number_with_suffixes($total_cost) }} <span style="font-size: 13px;" class="text-dark">SGD</span></h4>
                            @else
                            <h4 class="mb-0 text-primary">0 <span style="font-size: 13px;" class="text-dark">SGD</span></h4>
                            @endif
                            
                            <p class="mb-1">Cost <span style="font-size: 13px;" class="text-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="It is the sum of your cost per click (CPC) spend."><i class="fa-solid fa-circle-info"></i></span></p>
                        </div>
                        <div class="col-12 ms-auto">
                            <div id="chart8"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}

        {{-- <div class="col">
            <div class="card radius-10">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <h4 class="mb-0 text-primary">{{ is_numeric($total_cost) && is_numeric($total_clicks) && $total_clicks != 0 ? round($total_cost / $total_clicks, 2) : 0 }} <span style="font-size: 13px;" class="text-dark">SGD</span></h4>
                            <p class="mb-1">Average CPC <span style="font-size: 13px;" class="text-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="It is calculated by dividing the total cost of your clicks by the total number of clicks."><i class="fa-solid fa-circle-info"></i></span></p>
                        </div>
                        <div class="col-12 ms-auto">
                            <div id="chart9"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}

        {{-- <div class="col">
            <div class="card radius-10">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <h4 class="mb-0 text-primary">{{ @$total_conversions ?? 0 }}</h4>
                            <p class="mb-1">Conversions <span style="font-size: 13px;" class="text-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="It is a result of someone clicking on your ad and taking an action you have defined such as purchasing or filling up a form."><i class="fa-solid fa-circle-info"></i></span></p>
                        </div>
                        <div class="col-12 ms-auto">
                            <div id="chart10"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}

        {{-- <div class="col">
            <div class="card radius-10">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <h4 class="mb-0 text-primary">{{ @$total_conversions ?? 0 }}</h4>
                            <p class="mb-1">Conversion Action <span style="font-size: 13px;" class="text-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="It is a result of someone clicking on your ad and taking an action you have defined such as purchasing or filling up a form."><i class="fa-solid fa-circle-info"></i></span></p>
                        </div>
                        <div class="col-12 ms-auto">
                            <div id="chart_conversations_action"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}

        {{-- <div class="col">
            <div class="card radius-10">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <h4 class="mb-0 text-primary">{{ $total_clicks != 0 ? round(@$total_conversions / $total_clicks * 100, 2) : 0 }}%</h4>
                            <p class="mb-1">Conversion Rate <span style="font-size: 13px;" class="text-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="It is calculated by simply taking the number of conversions dividing it by the number of total clicks during the same time period."><i class="fa-solid fa-circle-info"></i></span></p>
                        </div>
                        <div class="col-12 ms-auto">
                            <div id="chart11"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}

        {{-- <div class="col">
            <div class="card radius-10">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <h4 class="mb-0 text-primary">{{ is_numeric($total_cost) && is_numeric($total_conversions) && $total_conversions != 0 ? round($total_cost / $total_conversions, 1) : 0 }} <span style="font-size: 13px;" class="text-dark">SGD</span></h4>
                            <p class="mb-1">Cost Per Conversion <span style="font-size: 13px;" class="text-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="It is the amount you have paid per conversion in ads."><i class="fa-solid fa-circle-info"></i></span></p>
                        </div>
                        <div class="col-12 ms-auto">
                            <div id="chart12"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}
    </div>

    <div class="row">
        <div class="alert border-0 bg-light-success alert-dismissible fade show copy_campaign" role="alert" style="display: none;">
            URL copied to clipboard!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-4">
            <div class="card radius-10 w-100">
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-xl-12">
                            <h6 class="title">Campaign Performance Report&nbsp;
                                <span class="text-dark" data-bs-toggle="tooltip" data-bs-placement="top"
                                    title="Campaign Performance">
                                    <i class="fa-solid fa-circle-info"></i>
                                </span>
                            </h6>
                        </div>
                    </div>
                    <div class="table-responsive mt-2">
                        <table id="campaign-performance-data-table" class="table table-striped align-middle table-bordered mb-0 data-table">
                            <thead class="table-light">
                                <tr>
                                    <th>Campaign</th>
                                    <th>Start<br>Date</th>
                                    <th>Total<br>Leads</th>
                                    <th>Cost Per<br>Conversation<br><span class="text-primary">(SGD)</th>
                                    <th>Spend <span class="text-primary">(SGD)</th>
                                    <th>Campaign<br>Budget <span class="text-primary">(SGD)</span></th>
                                    <th>Campaign<br>Notes</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (isset($campaign_with_notes) && !empty($campaign_with_notes))
                                    @foreach ($campaign_with_notes as $item)
                                        @php
                                            $copy_campaign_data = "";
                                            if (isset($item['final_url'])) {
                                                $copy_campaign_data .= "Website : {$item['final_url']} \n";
                                            } else {
                                                $copy_campaign_data .= "Website : No Website URL Found \n";
                                            }
                                            
                                            $copy_campaign_data .= "Start Date : {$item['date']} \n";
                                            $copy_campaign_data .= "PPC Campaign : {$item['name']} \n";

                                            $copy_campaign_data .= "Campaign Budget - $" . number_format($item['campaign_budget'] ?? 0.00, 2) . "\n";
                                            $copy_campaign_data .= "Total leads - " . ($item['total_leads'] ?? 0) . "\n";
                                            $copy_campaign_data .= "Cost/Conversation - $" . number_format($item['cost_per_conversation'] ?? 0.00, 2) . "\n";
                                            $copy_campaign_data .= "Ads Spending - $" . ($item['spend'] ?? 0.00) . "\n\n";

                                            if (isset($item['campaign_notes']) && !empty($item['campaign_notes'])) {
                                                $copy_campaign_data .= "Advise\n";
                                                foreach ($item['campaign_notes'] as $key => $note) {
                                                    $copy_campaign_data .= "{$note}\n";
                                                }
                                            }
                                        @endphp
                                        <tr>
                                            <td>
                                                {!! wordwrap($item['name'], 15, '<br>', true) ?? '...' !!} <br>
                                                <small>
                                                    @if (isset($item['final_url']))
                                                        <a href="Javascript:void(0);" class="text-success" data-bs-toggle="tooltip" data-bs-placement="top"
                                                                title="{{ $item['final_url'] }}">{{ Str::limit($item['final_url'], 20, "...") }}</a>
                                                    @else
                                                        <a href="Javascript:void(0);" class="text-success">No Website URL Found</a>
                                                    @endif
                                                    
                                                </small>
                                            </td>
                                            <td>{{ $item['date'] ?? "..."}}</td>
                                            <td>{{ $item['total_leads'] ?? "0" }}</td>
                                            <td>${{ number_format($item['cost_per_conversation'], 2) ?? "0.00" }}</td>
                                            <td>${{ number_format($item['spend'], 2) ?? "0.00" }}</td>
                                            <td>${{ number_format($item['campaign_budget'] , 2) ?? "0.00" }}</td>
                                            <td>
                                                @if (isset($item['campaign_notes']) && !empty($item['campaign_notes']))
                                                    @foreach ($item['campaign_notes'] as $note)
                                                        <a href="javascript:void(0);" class="text-dark" data-bs-toggle="tooltip" data-bs-placement="top"
                                                            title="{{ $note }}">
                                                            {{ Str::limit($note, 15, "...") }}<br>
                                                        </a>
                                                    @endforeach
                                                @else
                                                    <a href="javascript:void(0);" class="text-dark">
                                                        No Data Found
                                                    </a>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="javascript:void(0)" class="text-success copy-button fs-5" data-clipboard-text="{{ $copy_campaign_data }}" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Copy Campaign Text">
                                                    <i class="fadeIn animated bx bx-copy"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card radius-10 w-100">
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-xl-12">
                            <h6 class="title">Campaigns&nbsp;
                                <span class="text-dark" data-bs-toggle="tooltip" data-bs-placement="top"
                                    title="It shows a list of PPC campaigns currently running under your Google Ads account.">
                                    <i class="fa-solid fa-circle-info"></i>
                                </span>
                            </h6>
                        </div>
                    </div>
                    <div class="table-responsive mt-2">
                        <table id="campaign-data-table" class="table table-striped align-middle mb-0 data-table">
                            <thead class="table-light">
                                <tr>
                                    <th>Campaign</th>
                                    <th>Impressions</th>
                                    <th>Clicks</th>
                                    <th>CTR</th>
                                    <th>Cost <span class="text-primary">(SGD)</span></th>
                                    <th>Conversions</th>
                                    <th>Conversion Action</th>
                                    <th>ROAS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (isset($campaign['results']) && !empty($campaign['results']))
                                    @foreach ($campaign['results'] as $item)
                                        @php
                                            $campaigns = $item['campaign'];
                                            $metrics = $item['metrics'];
                                            $ctr = ($metrics['impressions'] > 0) ? ($metrics['clicks'] / $metrics['impressions'] * 100) : 0;
                                            $cost = ($metrics['costMicros'] > 0) ? ($metrics['costMicros'] / 1000000) : 0;
                                        @endphp
                                        <tr>
                                            <td>{!! wordwrap($campaigns['name'], 15, '<br>', true) ?? '...' !!}</td>
                                            <td>{{ $metrics['impressions'] ?? "..."}}</td>
                                            <td>{{ $metrics['clicks'] ?? "..." }}</td>
                                            <td>{{ round($ctr, 2) ?? "0.00" }}%</td>
                                            <td>{{ $cost ?? "0.00" }}</td>
                                            <td>{{ $metrics['conversions'].'.00' ?? "0.00" }}</td>
                                            <td>{{ $metrics['conversions'].'.00' ?? "0.00" }}</td>
                                            <td>{{ $metrics['roas'] ?? "0.00" }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card radius-10 w-100">
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-xl-12">
                            <h6 class="title">Ad Groups&nbsp;
                                <span class="text-dark" data-bs-toggle="tooltip" data-bs-placement="top"
                                    title="It shows a list of Ad Groups created under the PPC campaigns.">
                                    <i class="fa-solid fa-circle-info"></i>
                                </span>
                            </h6>
                        </div>
                    </div>
                    <div class="table-responsive mt-2">
                        <table id="ad-group-data-table" class="table table-striped align-middle mb-0 data-table">
                            <thead class="table-light">
                                <tr>
                                    <th>Ad Group</th>
                                    <th>Impressions</th>
                                    <th>Clicks</th>
                                    <th>CTR</th>
                                    <th>Cost <span class="text-primary">(SGD)</span></th>
                                    <th>Conversions</th>
                                    <th>Conversion Action</th>
                                    <th>ROAS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (isset($ads_group['results']) && !empty($ads_group['results']))
                                    @foreach ($ads_group['results'] as $item)
                                        @php
                                            $adGroup = $item['adGroup'];
                                            $metrics = $item['metrics'];
                                            $ctr = ($metrics['impressions'] > 0) ? ($metrics['clicks'] / $metrics['impressions'] * 100) : 0;
                                            $cost = ($metrics['costMicros'] > 0) ? ($metrics['costMicros'] / 1000000) : 0;
                                        @endphp
                                        <tr>
                                            <td>{!! wordwrap($adGroup['name'], 15, '<br>', true) ?? '...' !!}</td>
                                            <td>{{ $metrics['impressions'] ?? "..."}}</td>
                                            <td>{{ $metrics['clicks'] ?? "..." }}</td>
                                            <td>{{ round($ctr, 2) ?? "0.00" }}%</td>
                                            <td>{{ $cost ?? "0.00" }}</td>
                                            <td>{{ $metrics['conversions'].'.00' ?? "0.00" }}</td>
                                            <td>{{ $metrics['conversions'].'.00' ?? "0.00" }}</td>
                                            <td>{{ $metrics['roas'] ?? "0.00" }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-4">
            <div class="card radius-10 w-100">
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-xl-12">
                            <h6 class="title">Keywords&nbsp;
                                <span class="text-dark" data-bs-toggle="tooltip" data-bs-placement="top"
                                    title="It shows a combined list of keywords for which your ads are being displayed.">
                                    <i class="fa-solid fa-circle-info"></i>
                                </span>
                            </h6>
                        </div>
                    </div>
                    <div class="table-responsive mt-2">
                        <table id="keywords-data-table" class="table table-striped align-middle mb-0 data-table">
                            <thead class="table-light">
                                <tr>
                                    <th>Keyword</th>
                                    <th>Impressions</th>
                                    <th>Clicks</th>
                                    <th>CTR</th>
                                    <th>Cost <span class="text-primary">(SGD)</span></th>
                                    <th>Conversions</th>
                                    <th>Conversion Action</th>
                                    <th>ROAS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (isset($keywords['results']) && !empty($keywords['results']))
                                    @foreach ($keywords['results'] as $item)
                                        @php
                                            $adGroupCriterion = $item['adGroupCriterion'];
                                            $metrics = $item['metrics'];
                                            $ctr = isset($metrics['impressions']) && $metrics['impressions'] > 0 ? ($metrics['clicks'] / $metrics['impressions'] * 100) : 0.00;
                                            $cost = isset($metrics['costMicros']) ? ($metrics['costMicros'] / 1000000) : 0.00;
                                        @endphp                
                                        <tr>
                                            <td>{!! wordwrap($adGroupCriterion['keyword']['text'], 15, '<br>', true) ?? '...' !!}</td>
                                            <td>{{ $metrics['impressions'] ?? "..." }}</td>
                                            <td>{{ $metrics['clicks'] ?? "..." }}</td>
                                            @if ($ctr != 0)
                                                <td>{{ round($ctr, 2) ?? "0.00" }}%</td>
                                            @else
                                                <td>0.00%</td>
                                            @endif
                                            @if ($cost != 0)
                                                <td>{{ $cost ?? "0.00" }}</td>
                                            @else
                                                <td>0.00</td>
                                            @endif
                                            <td>{{ $metrics['conversions'].'.00' ?? "0.00" }}</td>
                                            <td>{{ $metrics['conversions'].'.00' ?? "0.00" }}</td>
                                            <td>{{ $metrics['roas'] ?? "0.00" }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card radius-10 w-100">
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-xl-12">
                            <h6 class="title">Ads&nbsp;
                                <span class="text-dark" data-bs-toggle="tooltip" data-bs-placement="top"
                                    title="It shows a list of Ads running under the PPC campaigns.">
                                    <i class="fa-solid fa-circle-info"></i>
                                </span>
                            </h6>
                        </div>
                    </div>
                    <div class="table-responsive mt-2">
                        <table id="ads-data-table" class="table table-striped align-middle mb-0 data-table">
                            <thead class="table-light">
                                <tr>
                                    <th>Ad</th>
                                    <th>Ad Type</th>
                                    <th>Impressions</th>
                                    <th>Clicks</th>
                                    <th>CTR</th>
                                    <th>Cost <span class="text-primary">(SGD)</span></th>
                                    <th>Conversions</th>
                                    <th>Conversion Action</th>
                                    <th>ROAS</th>
                                </tr>
                            </thead>
                            <tbody id="">
                                @php
                                    $unspecified_imp = 0;
                                    $unspecified_click = 0;
                                    $unspecified_cost = 0;
                                    $unspecified_conversation = 0;
                                @endphp
                                @if (isset($ads['results']) && !empty($ads['results']))
                                    @foreach ($ads['results'] as $item)
                                        
                                        @php
                                            $adGroupAd = $item['adGroupAd'];
                                            $metrics = $item['metrics'];
                                            $ctr = isset($metrics['impressions']) && $metrics['impressions'] > 0 ? ($metrics['clicks'] / $metrics['impressions'] * 100) : 0;
                                            $cost = isset($metrics['costMicros']) ? ($metrics['costMicros'] / 1000000) : 0;
                                            $unspecified_imp += is_numeric($metrics['impressions']) ? (int)$metrics['impressions'] : 0;
                                            $unspecified_click += is_numeric($metrics['clicks']) ? (int)$metrics['clicks'] : 0;
                                            $unspecified_cost += isset($metrics['costMicros']) ? ($metrics['costMicros'] / 1000000) : 0;
                                            $unspecified_conversation += is_numeric($metrics['conversions']) ? (int)$metrics['conversions'] : 0;
                                        @endphp                        
                                        <tr>
                                            <td>
                                                <h6>
                                                    <small>
                                                        <a href="Javascript:void(0);" class="text-success">{{ (isset($adGroupAd['ad']['finalUrls'][0]) && is_string($adGroupAd['ad']['finalUrls'][0])) ? Str::limit($adGroupAd['ad']['finalUrls'][0], 30, "...") : "No Website URL Found" }}</a>
                                                    </small><br>
                                                    <a href="Javascript:void(0);">Between Newton & Novena Area...</a>
                                                </h6>
                                                <p>
                                                    The nearest MRT station to Former...
                                                </p>
                                            </td>
                                            <td>{{ $adGroupAd['ad']['type'] }}</td>
                                            <td>{{ $metrics['impressions'] ?? "..." }}</td>
                                            <td>{{ $metrics['clicks'] ?? "..." }}</td>
                                            <td>{{ round($ctr, 2) ?? "0.00" }}%</td>
                                            <td>{{ $cost ?? "0.00" }}</td>
                                            <td>{{ $metrics['conversions'].'.00' ?? "0.00" }}</td>
                                            <td>{{ $metrics['conversions'].'.00' ?? "0.00" }}</td>
                                            <td>{{ $metrics['roas'] ?? "0.00" }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card radius-10 w-100">
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-xl-12">
                            <h6 class="title">Performance Networks&nbsp;
                                <span class="text-dark" data-bs-toggle="tooltip" data-bs-placement="top"
                                    title="It shows the performance of your PPC campaign via the type of Search Network.">
                                    <i class="fa-solid fa-circle-info"></i>
                                </span>
                            </h6>
                        </div>
                    </div>
                    <div class="table-responsive mt-2">
                        <table id="performance-network-data-table" class="table table-striped align-middle mb-0 data-table">
                            <thead class="table-light">
                                <tr>
                                    <th>Publisher By Network</th>
                                    <th>Impressions</th>
                                    <th>Clicks</th>
                                    <th>CTR</th>
                                    <th>Cost <span class="text-primary">(SGD)</span></th>
                                    <th>Conversions</th>
                                    <th>Conversion Action</th>
                                    <th>ROAS</th>
                                </tr>
                            </thead>
                            <tbody id="">
                                <tr>
                                    <td>UNSPECIFIED</td>
                                    <td>{{ $unspecified_imp ?? "..."}}</td>
                                    <td>{{ $unspecified_click ?? '...' }}</td>
                                    <td>{{ $unspecified_imp > 0 ? round($unspecified_click / $unspecified_imp * 100, 2).'%' : '0.00%' }}</td>
                                    <td>{{ $unspecified_cost ?? '0.00' }}</td>
                                    <td>{{ $unspecified_conversation.'.00' ?? '0.00'}}</td>
                                    <td>{{ $unspecified_conversation.'.00' ?? '0.00'}}</td>
                                    <td>0.00</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-4">
            <div class="card radius-10 w-100">
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-xl-12">
                            <h6 class="title">Performance Devices&nbsp;
                                <span class="text-dark" data-bs-toggle="tooltip" data-bs-placement="top"
                                    title="It shows the performance of your PPC campaign via the device used.">
                                    <i class="fa-solid fa-circle-info"></i>
                                </span>
                            </h6>
                        </div>
                    </div>
                    <div class="table-responsive mt-2">
                        <table id="performance-device-data-table" class="table table-striped align-middle mb-0 data-table">
                            <thead class="table-light">
                                <tr>
                                    <th>Device</th>
                                    <th>Impressions</th>
                                    <th>Clicks</th>
                                    <th>CTR</th>
                                    <th>Cost  <span class="text-primary">(SGD)</span></th>
                                    <th>Conversions</th>
                                    <th>Conversion Action</th>
                                    <th>ROAS</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>MOBILE</td>
                                    <td>{{ $performance_device['MOBILE']['impressions'] ?? '...'}}</td>
                                    <td>{{ $performance_device['MOBILE']['clicks'] ?? '...'}}</td>
                                    <td>{{ isset($performance_device['MOBILE']['impressions']) && $performance_device['MOBILE']['impressions'] > 0 ? round($performance_device['MOBILE']['clicks'] / $performance_device['MOBILE']['impressions'] * 100, 2).'%' : '0.00%' }}</td>
                                    <td>{{ $performance_device['MOBILE']['cost'] ?? "0.00" }}</td>
                                    <td>{{ isset($performance_device['MOBILE']) && is_array($performance_device['MOBILE']) ? $performance_device['MOBILE']['conversions'].'.00' : '0.00' }}</td>
                                    <td>{{ isset($performance_device['MOBILE']) && is_array($performance_device['MOBILE']) ? $performance_device['MOBILE']['conversions'].'.00' : '0.00' }}</td>
                                    <td>0.00</td>
                                </tr>
                                <tr>
                                    <td>DESKTOP</td>
                                    <td>{{ $performance_device['DESKTOP']['impressions'] ?? '...'}}</td>
                                    <td>{{ $performance_device['DESKTOP']['clicks'] ?? '...'}}</td>
                                    <td>{{ isset($performance_device['DESKTOP']['impressions']) && $performance_device['DESKTOP']['impressions'] > 0 ? round($performance_device['DESKTOP']['clicks'] / $performance_device['DESKTOP']['impressions'] * 100, 2).'%' : '0.00%' }}</td>
                                    <td>{{ $performance_device['DESKTOP']['cost'] ?? "0.00" }}</td>
                                    <td>{{ isset($performance_device['DESKTOP']) && is_array($performance_device['DESKTOP']) ? $performance_device['DESKTOP']['conversions'].'.00' : '0.00' }}</td>
                                    <td>{{ isset($performance_device['DESKTOP']) && is_array($performance_device['DESKTOP']) ? $performance_device['DESKTOP']['conversions'].'.00' : '0.00' }}</td>
                                    <td>0.00</td>
                                </tr>
                                <tr>
                                    <td>TABLET</td>
                                    <td>{{ $performance_device['TABLET']['impressions'] ?? '...'}}</td>
                                    <td>{{ $performance_device['TABLET']['clicks'] ?? '...'}}</td>
                                    <td>{{ isset($performance_device['TABLET']['impressions']) && $performance_device['TABLET']['impressions'] > 0 ? round($performance_device['TABLET']['clicks'] / $performance_device['TABLET']['impressions'] * 100, 2).'%' : '0.00%' }}</td>
                                    <td>{{ $performance_device['TABLET']['cost'] ?? "0.00" }}</td>
                                    <td>{{ isset($performance_device['TABLET']) && is_array($performance_device['TABLET']) ? $performance_device['TABLET']['conversions'].'.00' : '0.00' }}</td>
                                    <td>{{ isset($performance_device['TABLET']) && is_array($performance_device['TABLET']) ? $performance_device['TABLET']['conversions'].'.00' : '0.00' }}</td>
                                    <td>0.00</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card radius-10 w-100">
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-xl-12">
                            <h6 class="title">Performance Click Types&nbsp;
                                <span class="text-dark" data-bs-toggle="tooltip" data-bs-placement="top"
                                title="It shows the type of actionable clicks received by your PPC campaign.">
                                    <i class="fa-solid fa-circle-info"></i>
                                </span>
                            </h6>
                        </div>
                    </div>
                    <div class="table-responsive mt-2">
                        <table id="performance-click-type-data-table" class="table table-striped align-middle title data-table">
                            <thead class="table-light">
                                <tr>
                                    <th>Click Type</th>
                                    <th>Impressions</th>
                                    <th>Clicks</th>
                                    <th>CTR</th>
                                    <th>Cost <span class="text-primary">(SGD)</span></th>
                                    <th>Conversions</th>
                                    <th>Conversion Action</th>
                                    <th>ROAS</th>
                                </tr>
                            </thead>
                            <tbody id="">
                                <tr>
                                    <td>UNSPECIFIED</td>
                                    <td>{{ $unspecified_imp ?? "..."}}</td>
                                    <td>{{ $unspecified_click ?? '...' }}</td>
                                    <td>{{ $unspecified_imp > 0 ? round($unspecified_click / $unspecified_imp * 100, 2).'%' : '0.00%' }}</td>
                                    <td>{{ $unspecified_cost ?? '0.00' }}</td>
                                    <td>{{ $unspecified_conversation.'.00' ?? '0.00'}}</td>
                                    <td>{{ $unspecified_conversation.'.00' ?? '0.00'}}</td>
                                    <td>0.00</td>
                                </tr>                     
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card radius-10 w-100">
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-xl-12">
                            <h6 class="title">Performance Ad Slots&nbsp;
                                <span class="text-dark" data-bs-toggle="tooltip" data-bs-placement="top"
                                    title="It show how your ads are performing depending on the where they are being displayed.">
                                    <i class="fa-solid fa-circle-info"></i>
                                </span>
                            </h6>
                        </div>
                    </div>
                    <div class="table-responsive mt-2">
                        <table id="performance-ad-Slot-data-table" class="table table-striped align-middle mb-0 data-table">
                            <thead class="table-light">
                                <tr>
                                    <th>Ad Slot</th>
                                    <th>Impressions</th>
                                    <th>Clicks</th>
                                    <th>CTR</th>
                                    <th>Cost <span class="text-primary">(SGD)</span></th>
                                    <th>Conversions</th>
                                    <th>Conversion Action</th>
                                    <th>ROAS</th>
                                </tr>
                            </thead>
                            <tbody id="">
                                <tr>
                                    <td>UNSPECIFIED</td>
                                    <td>{{ $unspecified_imp ?? "..."}}</td>
                                    <td>{{ $unspecified_click ?? '...' }}</td>
                                    <td>{{ $unspecified_imp > 0 ? round($unspecified_click / $unspecified_imp * 100, 2).'%' : '0.00%' }}</td>
                                    <td>{{ $unspecified_cost ?? '0.00' }}</td>
                                    <td>{{ $unspecified_conversation.'.00' ?? '0.00'}}</td>
                                    <td>{{ $unspecified_conversation.'.00' ?? '0.00'}}</td>
                                    <td>0.00</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- date range modal -->
    <div class="modal fade" id="dateRangeModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Date Range</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.sub_account.google-ads-report.save_google_report', ['sub_account_id' => session()->get('sub_account_id')]) }}" method="POST" class="date_range_ajaxForm">
                    <div class="modal-body">
                        @csrf
                        <div class="row">
                            <div class="col-md-12 col-lg-12 mb-2">
                                <label for="">Select DateRange</label>
                                <input type="text" id="reportrange" name="daterange" required class="form-control" />
                                <input type="hidden" name="date_act_id" id="date_act_id">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        @if (!empty($customer_account_id))
                            <button type="button" class="btn btn-primary form-submit-btn mb-2 ajax_date_range_btn">Save</button>
                        @endif
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- campaign notes modal -->
    <div class="modal fade" id="campaignNotesModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Campaign Notes</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">

                    <form action="{{ route('admin.sub_account.google-ads-report.campaign_note_save', ['sub_account_id' => session()->get('sub_account_id')]) }}" method="POST"
                        class="saveNoteajaxForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-12 col-lg-12 mb-2">
                                <label for="">Date</label>
                                <input type="date" class="form-control" name="note_date" id="note_date" required>
                            </div>
                            <div class="col-md-12 col-lg-12 mb-2">
                                <label for="">Campaign</label>
                                <select name="campaign" class="form-select modal-single-select" id="campaign_select">
                                    <option value="">Select Campaign</option>
                                    @if (isset($campaign['results']) )
                                        @foreach (@$campaign['results'] as $item)
                                            @php
                                                $cmpn = $item['campaign'];
                                            @endphp
                                            <option value="{{ $cmpn['name'] }}">{{ $cmpn['name'] }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 mb-2">
                                <label for="">Note</label>
                                <textarea name="notes" id="notes" class="form-control" required
                                    placeholder="Start typing your note..." cols="30" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="float-end btn btn-primary form-submit-btn mb-2">Save
                                    Note</button>
                            </div>
                        </div>
                    </form>
                    @foreach ($campaign_notes as $note)
                        <div class="card mb-3" style="border: 1px solid #ced4da;">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <h6>{{ $note->campaign_name }}</h6>
                                    <span class="">
                                        <a href="javascript:void(0)" onclick="ajaxRequest(this)"
                                            data-url="{{ route('admin.sub_account.google-ads-report.campaign_note_delete', ['sub_account_id' => session()->get('sub_account_id'), 'id' => $note->hashid]) }}"
                                            class="text-danger"><i class="fas fa-trash-alt"></i></a>
                                    </span>
                                </div>
                                <p>{{ $note->note }} <br>
                                    <small>{{ $note->note_date }}</small>
                                </p>
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-scripts')
    <script src="{{ asset('front') }}/assets/plugins/apexcharts-bundle/js/apexcharts.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <script src="{{ asset('front') }}/assets/plugins/datatable/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('front') }}/assets/plugins/datatable/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ asset('front') }}/assets/js/table-datatable.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.8/clipboard.min.js"></script>

    <!-- date picker -->
    <script src="{{ asset('front') }}/assets/plugins/datetimepicker/js/picker.date.js"></script>
    <script src="{{ asset('front') }}/assets/plugins/bootstrap-material-datetimepicker/js/moment.min.js"></script>
    <script src="{{ asset('front') }}/assets/plugins/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.min.js"></script>

    <script>

        $(function() {
            "use strict";
            $('#expiry_date').bootstrapMaterialDatePicker({
                time: false,
                // minDate: new Date()
            });
        });

        $(document).ready(function() {
            $('#google_ad_filter').on('change', function() {
                let googleAdFilter = $('#google_ad_filter').val();
                let url = "{{ route('admin.sub_account.google-ads-report.index', ['sub_account_id' => $sub_account_id]) }}";
                window.location.href = `${url}?filter=${googleAdFilter}`;
            });

            let table = $('.data-table').DataTable({
                language: {
                    info: '_START_ - _END_ of _TOTAL_',
                    infoEmpty: '0 - 0 of 0',
                    infoFiltered: '',
                    zeroRecords: 'No matching records found',
                    emptyTable: 'No data available in table'
                },
                searching: false,
                lengthChange: false,
            });

            // Modify the info text after DataTables has been initialized
            /*
            table.on('draw', function() {
                var info = table.page.info();
                var start = info.start + 1; // Start index
                var end = info.end; // End index
                var total = info.recordsTotal; // Total records

                var text = start + '-' + end + ' of ' + total;
                $('.dataTables_info').text(text);
            });
            */
            
            $('.dataTables_wrapper').css({
                'display': 'flex',
                'justify-content': 'space-between',
                'flex-wrap': 'wrap'
            });

            $('.data-table tbody tr').css('background-color', '#ffffff');
        });

        $(document).on('change', '#expiry_date', function () {
            var expiry_date = $('#expiry_date').val();
            var account_id = $('#google_account_id').val();
            var data = {
                    '_token' : "{{ csrf_token() }}",
                    expiry_date : expiry_date,
                    account_id : account_id,
                    };
            update_expiry_date("{{ route('admin.sub_account.google-ads-report.update_act_expiry_date', ['sub_account_id' => session()->get('sub_account_id')]) }}",data,'POST');
        });

        $(document).ready(function(){

            var expiry_date = $('#expiry_date').val();
            var today = new Date();
            var dd = String(today.getDate()).padStart(2, '0');
            var mm = String(today.getMonth() + 1).padStart(2, '0');
            var yyyy = today.getFullYear();

            today = yyyy + '-' + mm + '-' + dd;

            if (expiry_date && (expiry_date === today || expiry_date < today)) {
                $("#act_expire_alert").show();
            }else{
                $("#act_expire_alert").hide();
            }

            $('#campaign-data-table').DataTable();
            $('#campaign-performance-data-table').DataTable();
            $('#ad-group-data-table').DataTable();
            $('#keywords-data-table').DataTable();
            $('#ads-data-table').DataTable();
            $('#performance-network-data-table').DataTable();
            $('#performance-device-data-table').DataTable();
            $('#performance-click-type-data-table').DataTable();
            $('#performance-ad-Slot-data-table').DataTable();
            charts();

            var start = $('#act_start_date').val();
            var end = $('#act_end_date').val();
            if (start == "" && end == "") {
                var start = moment().subtract(31, 'days').format("YYYY-MM-DD");
                var end = moment().subtract(1, 'days').format("YYYY-MM-DD");
                $('#act_start_date').val(start);
                $('#act_end_date').val(end);
            }

        });

        $('.single-select').select2({
            theme: 'bootstrap4',
            width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
            placeholder: $(this).data('placeholder'),
            allowClear: Boolean($(this).data('allow-clear')),
        });

        $('.modal-single-select').select2({
            dropdownParent: $("#campaignNotesModal"),
            theme: 'bootstrap4',
            width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
            placeholder: $(this).data('placeholder'),
            allowClear: Boolean($(this).data('allow-clear')),
        });

        $(document).on('click', '#campaignNotesBtn', function() {
            var today = new Date().toISOString().split('T')[0];
            $('#note_date').attr('max', today);
            $('#note_date').val(today);
            $('#campaignNotesModal').modal({
                backdrop: 'static',
                keyboard: false
            });
            $('#campaignNotesModal').modal('show');
        });

        $('.saveNoteajaxForm').submit(function(e) {
            e.preventDefault();
            var url = $(this).attr('action');
            var formData = new FormData(this);
            my_ajax(url, formData, 'post', function(res) {

            }, true);
        });

        $(document).on('click', '.copy-button', function (){
            var clipboard = new ClipboardJS('.copy-button');
            clipboard.on('success', function (e) {
                var alertElement = $('.copy_campaign');
                alertElement.show();
                setTimeout(function () {
                    alertElement.hide();
                }, 3000);
                e.clearSelection();
            });
            clipboard.on('error', function (e) {
                console.error('Copy failed: ', e);
            });
        });

        $('#generate_pdf_btn').click(function(event) {
            var expiry_date = $('#expiry_date').val();
            var today = new Date();
            var dd = String(today.getDate()).padStart(2, '0');
            var mm = String(today.getMonth() + 1).padStart(2, '0');
            var yyyy = today.getFullYear();

            today = yyyy + '-' + mm + '-' + dd;

            if (expiry_date && (expiry_date === today || expiry_date < today)) {
                toast('Google Ad account has expired. Please recharge it to proceed with downloading the PDF.', "warning!", 'warning', 3000);
            }else{
                window.open("{{ route('admin.sub_account.google-ads-report.download_pdf', ['sub_account_id' => session()->get('sub_account_id')]) }}", '_blank') 
                $('#loader-container').show();
                setTimeout(function() {
                    $('#loader-container').hide();
                    window.location.reload();
                }, 20000);
            }
        });

        $(document).on('click', '#date_range_btn', function() {
            $(function() {
                var start = moment($('#act_start_date').val());
                var end = moment($('#act_end_date').val());

                var acct_id = $('#google_account_id').val();
                $('#date_act_id').val(acct_id);

                function cb(start, end) {
                    $('#reportrange span').html(start.format('YYYY-MM-DD') + ' - ' + end.format(
                        'YYYY-MM-DD'));
                    $('#act_start_date').val(start.format('YYYY-MM-DD'));
                    $('#act_end_date').val(end.format('YYYY-MM-DD'));
                }

                $('#reportrange').daterangepicker({
                    startDate: start,
                    endDate: end,
                    maxDate: moment(),
                    ranges: {
                        'One Month': [moment().subtract(1, 'month'), moment()],
                        'Three Months': [moment().subtract(3, 'month'), moment()],
                        'Six Months': [moment().subtract(6, 'month'), moment()],
                        'Nine Months': [moment().subtract(9, 'month'), moment()],
                        'One Year': [moment().subtract(1, 'year'), moment()]
                    }
                }, cb);

                cb(start, end);
            });

            $('#dateRangeModal').modal({ backdrop: 'static', keyboard: false});
            $('#dateRangeModal').modal('show');
        });

        $(document).on('click', '.ajax_date_range_btn', function() {
            $('#dateRangeModal').modal('hide');
            send_ajax_save_report('.date_range_ajaxForm');
        });

        $(document).on('change', '#google_account_id', function() {
            send_ajax_save_report('.ajaxFormAdAct');
        });

        $(document).on('click', '#refresh_google_report', function() {
                send_ajax_save_report('.ajaxFormAdAct');
        });

        $('#disconnect_google_account').on('click', function() {
            Swal.fire({
                title: "Are you sure?",
                text: "By this action your google account is disconnected",
                type: "warning",
                showCancelButton: !0,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, confirm it!"
            }).then(function(t) {
                if (t.value) {
                    $.ajax({
                        url: "{{ route('admin.sub_account.google-ads-report.google_act_disconnect', ['sub_account_id' => session()->get('sub_account_id')]) }}",
                        type: 'GET',
                        success: function(response) {
                            if (response.success) {
                                toast('Google Account Successfully Disconnected', "Success",
                                    'success', 3000);
                                location.reload();
                            }
                        },
                        error: function(xhr, status, error) {
                            toast('Some Thing went Wrong', "Error!", 'error');
                        }
                    });
                }
            });

        });

        function send_ajax_save_report(form_class){
            var url = $(form_class).attr('action');
            var formData = new FormData($(form_class)[0]);
            $.ajax({
                url: url,
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: formData,
                contentType: false,
                processData: false,
                dataType: "json",
                beforeSend: function() {
                    $('#loader-container').show();
                },
                complete: function() {
                    $('#loader-container').hide();
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    $('#loader-container').hide();
                },
                success: function(data) {
                    $('#loader-container').hide();
                    var timer = 3000;
                    if (data['reload'] !== undefined) {
                        toast(data['success'], "Success!", 'success', timer);
                        setTimeout(function() {
                            window.location.reload(true);
                        }, 600);
                        return false;
                    }

                    if (data['error'] !== undefined) {
                        toast(data['error'], "Error!", 'error');
                        return false;
                    }

                    if (data['success'] !== undefined) {
                        toast(data['success'], "Success!", 'success', timer);
                    }
                    callback(data);
                }
            });
        }

        function update_expiry_date(url, params, method){
            $.ajax({
                url: url,
                method: method,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: params,
                dataType: "json",
                beforeSend: function() {
                    $('#loader-container').show();
                },
                complete: function () {
                    $('#loader-container').hide();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $('#loader-container').hide();
                    ajaxErrorHandling(jqXHR, errorThrown);
                },
                success: function (data) {
                    var timer = 5000;
                    if (data['reload'] !== undefined) {
                        toast(data['success'], "Success!", 'success', timer);
                        setTimeout(function () {
                            window.location.reload(true);
                        }, 2000);
                        return false;
                    }
                    if (data['redirect'] !== undefined) {
                        toast(data['success'], "Success!", 'success', timer);
                        setTimeout(function () {
                            window.location = data['redirect'];
                        }, 2000);
                        return false;
                    }
                    if (data['error'] !== undefined) {
                        toast(data['error'], "Error!", 'error');
                        return false;
                    }

                    if (data['errors'] !== undefined) {
                        multiple_errors_ajax_handling(data['errors']);
                    }
                    callback(data);
                }
            });
        }

        // weidget charts starts
            var widget_impressions = [<?php echo $summary_graph_impressions; ?>];
            var widget_clicks = [<?php echo $summary_graph_clicks; ?>];
            var widget_conversations = [<?php echo $summary_graph_conversations; ?>];
            var widget_conversations_action = [<?php echo $summary_graph_conversations; ?>];
            var widget_ctr = [<?php echo $widget_graph_ctr; ?>];
            var widget_avg_cpc = [<?php echo $widget_graph_average_cpc; ?>];
            var widget_cost = [<?php echo $widget_graph_cost; ?>];
            var widget_conv_rate = [<?php echo $widget_graph_conversation_rate; ?>];
            var widget_cost_per_conv = [<?php echo $widget_graph_cost_per_conversion; ?>];

            function charts(){
                
                var options = {
                    series: [{
                        name: "Impressions",
                        data: widget_impressions
                    }],
                    chart: {
                        type: "line",
                        width: 250,
                        height: 50,
                        toolbar: {
                            show: !1
                        },
                        zoom: {
                            enabled: !1
                        },
                        dropShadow: {
                            enabled: 0,
                            top: 3,
                            left: 14,
                            blur: 4,
                            opacity: .12,
                            color: "#3461ff"
                        },
                        sparkline: {
                            enabled: !0
                        }
                    },
                    markers: {
                        size: 0,
                        colors: ["#3461ff"],
                        strokeColors: "#fff",
                        strokeWidth: 2,
                        hover: {
                            size: 7
                        }
                    },
                    plotOptions: {
                        bar: {
                            horizontal: !1,
                            columnWidth: "35%",
                            endingShape: "rounded"
                        }
                    },
                    dataLabels: {
                        enabled: !1
                    },
                    stroke: {
                        show: !0,
                        width: 2.5,
                        curve: "smooth"
                    },
                    colors: ["#3461ff"],
                    xaxis: {
                        categories: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"]
                    },
                    fill: {
                        opacity: 1
                    },
                    tooltip: {
                        theme: "dark",
                        fixed: {
                            enabled: !1
                        },
                        x: {
                            show: !1
                        },
                        y: {
                            title: {
                                formatter: function(e) {
                                    return ""
                                }
                            }
                        },
                        marker: {
                            show: !1
                        }
                    }
                };
                var chart = new ApexCharts(document.querySelector("#chart5"), options);
                chart.render();

                
                var options = {
                    series: [{
                        name: "Clicks",
                        data: widget_clicks
                    }],
                    chart: {
                        type: "line",
                        width: 250,
                        height: 50,
                        toolbar: {
                            show: !1
                        },
                        zoom: {
                            enabled: !1
                        },
                        dropShadow: {
                            enabled: 0,
                            top: 3,
                            left: 14,
                            blur: 4,
                            opacity: .12,
                            color: "#3461ff"
                        },
                        sparkline: {
                            enabled: !0
                        }
                    },
                    markers: {
                        size: 0,
                        colors: ["#3461ff"],
                        strokeColors: "#fff",
                        strokeWidth: 2,
                        hover: {
                            size: 7
                        }
                    },
                    plotOptions: {
                        bar: {
                            horizontal: !1,
                            columnWidth: "35%",
                            endingShape: "rounded"
                        }
                    },
                    dataLabels: {
                        enabled: !1
                    },
                    stroke: {
                        show: !0,
                        width: 2.5,
                        curve: "smooth"
                    },
                    colors: ["#3461ff"],
                    xaxis: {
                        categories: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"]
                    },
                    fill: {
                        opacity: 1
                    },
                    tooltip: {
                        theme: "dark",
                        fixed: {
                            enabled: !1
                        },
                        x: {
                            show: !1
                        },
                        y: {
                            title: {
                                formatter: function(e) {
                                    return ""
                                }
                            }
                        },
                        marker: {
                            show: !1
                        }
                    }
                };
                var chart = new ApexCharts(document.querySelector("#chart6"), options);
                chart.render();

                var options = {
                    series: [{
                        name: "CTR",
                        data: widget_ctr
                    }],
                    chart: {
                        type: "line",
                        width: 250,
                        height: 50,
                        toolbar: {
                            show: !1
                        },
                        zoom: {
                            enabled: !1
                        },
                        dropShadow: {
                            enabled: 0,
                            top: 3,
                            left: 14,
                            blur: 4,
                            opacity: .12,
                            color: "#3461ff"
                        },
                        sparkline: {
                            enabled: !0
                        }
                    },
                    markers: {
                        size: 0,
                        colors: ["#3461ff"],
                        strokeColors: "#fff",
                        strokeWidth: 2,
                        hover: {
                            size: 7
                        }
                    },
                    plotOptions: {
                        bar: {
                            horizontal: !1,
                            columnWidth: "35%",
                            endingShape: "rounded"
                        }
                    },
                    dataLabels: {
                        enabled: !1
                    },
                    stroke: {
                        show: !0,
                        width: 2.5,
                        curve: "smooth"
                    },
                    colors: ["#3461ff"],
                    xaxis: {
                        categories: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"]
                    },
                    fill: {
                        opacity: 1
                    },
                    tooltip: {
                        theme: "dark",
                        fixed: {
                            enabled: !1
                        },
                        x: {
                            show: !1
                        },
                        y: {
                            title: {
                                formatter: function(e) {
                                    return ""
                                }
                            }
                        },
                        marker: {
                            show: !1
                        }
                    }
                };
                var chart = new ApexCharts(document.querySelector("#chart_ctr"), options);
                chart.render();
                
                var options = {
                    series: [{
                        name: "ROAS",
                        data: [0, 0, 0, 0, 0, 0, 0, 0, 0]
                    }],
                    chart: {
                        type: "line",
                        width: 250,
                        height: 50,
                        toolbar: {
                            show: !1
                        },
                        zoom: {
                            enabled: !1
                        },
                        dropShadow: {
                            enabled: 0,
                            top: 3,
                            left: 14,
                            blur: 4,
                            opacity: .12,
                            color: "#3461ff"
                        },
                        sparkline: {
                            enabled: !0
                        }
                    },
                    markers: {
                        size: 0,
                        colors: ["#3461ff"],
                        strokeColors: "#fff",
                        strokeWidth: 2,
                        hover: {
                            size: 7
                        }
                    },
                    plotOptions: {
                        bar: {
                            horizontal: !1,
                            columnWidth: "35%",
                            endingShape: "rounded"
                        }
                    },
                    dataLabels: {
                        enabled: !1
                    },
                    stroke: {
                        show: !0,
                        width: 2.5,
                        curve: "smooth"
                    },
                    colors: ["#3461ff"],
                    xaxis: {
                        categories: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"]
                    },
                    fill: {
                        opacity: 1
                    },
                    tooltip: {
                        theme: "dark",
                        fixed: {
                            enabled: !1
                        },
                        x: {
                            show: !1
                        },
                        y: {
                            title: {
                                formatter: function(e) {
                                    return ""
                                }
                            }
                        },
                        marker: {
                            show: !1
                        }
                    }
                };
                var chart = new ApexCharts(document.querySelector("#chart7"), options);
                chart.render();

                
                var options = {
                    series: [{
                        name: "Cost",
                        data: widget_cost
                    }],
                    chart: {
                        type: "line",
                        width: 250,
                        height: 50,
                        toolbar: {
                            show: !1
                        },
                        zoom: {
                            enabled: !1
                        },
                        dropShadow: {
                            enabled: 0,
                            top: 3,
                            left: 14,
                            blur: 4,
                            opacity: .12,
                            color: "#3461ff"
                        },
                        sparkline: {
                            enabled: !0
                        }
                    },
                    markers: {
                        size: 0,
                        colors: ["#3461ff"],
                        strokeColors: "#fff",
                        strokeWidth: 2,
                        hover: {
                            size: 7
                        }
                    },
                    plotOptions: {
                        bar: {
                            horizontal: !1,
                            columnWidth: "35%",
                            endingShape: "rounded"
                        }
                    },
                    dataLabels: {
                        enabled: !1
                    },
                    stroke: {
                        show: !0,
                        width: 2.5,
                        curve: "smooth"
                    },
                    colors: ["#3461ff"],
                    xaxis: {
                        categories: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"]
                    },
                    fill: {
                        opacity: 1
                    },
                    tooltip: {
                        theme: "dark",
                        fixed: {
                            enabled: !1
                        },
                        x: {
                            show: !1
                        },
                        y: {
                            title: {
                                formatter: function(e) {
                                    return ""
                                }
                            }
                        },
                        marker: {
                            show: !1
                        }
                    }
                };
                var chart = new ApexCharts(document.querySelector("#chart8"), options);
                chart.render();

                
                var options = {
                    series: [{
                        name: "Average CPC",
                        data: widget_avg_cpc
                    }],
                    chart: {
                        type: "line",
                        width: 250,
                        height: 50,
                        toolbar: {
                            show: !1
                        },
                        zoom: {
                            enabled: !1
                        },
                        dropShadow: {
                            enabled: 0,
                            top: 3,
                            left: 14,
                            blur: 4,
                            opacity: .12,
                            color: "#3461ff"
                        },
                        sparkline: {
                            enabled: !0
                        }
                    },
                    markers: {
                        size: 0,
                        colors: ["#3461ff"],
                        strokeColors: "#fff",
                        strokeWidth: 2,
                        hover: {
                            size: 7
                        }
                    },
                    plotOptions: {
                        bar: {
                            horizontal: !1,
                            columnWidth: "35%",
                            endingShape: "rounded"
                        }
                    },
                    dataLabels: {
                        enabled: !1
                    },
                    stroke: {
                        show: !0,
                        width: 2.5,
                        curve: "smooth"
                    },
                    colors: ["#3461ff"],
                    xaxis: {
                        categories: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"]
                    },
                    fill: {
                        opacity: 1
                    },
                    tooltip: {
                        theme: "dark",
                        fixed: {
                            enabled: !1
                        },
                        x: {
                            show: !1
                        },
                        y: {
                            title: {
                                formatter: function(e) {
                                    return ""
                                }
                            }
                        },
                        marker: {
                            show: !1
                        }
                    }
                };
                var chart = new ApexCharts(document.querySelector("#chart9"), options);
                chart.render();

                
                var options = {
                    series: [{
                        name: "Conversions",
                        data: widget_conversations
                    }],
                    chart: {
                        type: "line",
                        width: 250,
                        height: 50,
                        toolbar: {
                            show: !1
                        },
                        zoom: {
                            enabled: !1
                        },
                        dropShadow: {
                            enabled: 0,
                            top: 3,
                            left: 14,
                            blur: 4,
                            opacity: .12,
                            color: "#3461ff"
                        },
                        sparkline: {
                            enabled: !0
                        }
                    },
                    markers: {
                        size: 0,
                        colors: ["#3461ff"],
                        strokeColors: "#fff",
                        strokeWidth: 2,
                        hover: {
                            size: 7
                        }
                    },
                    plotOptions: {
                        bar: {
                            horizontal: !1,
                            columnWidth: "35%",
                            endingShape: "rounded"
                        }
                    },
                    dataLabels: {
                        enabled: !1
                    },
                    stroke: {
                        show: !0,
                        width: 2.5,
                        curve: "smooth"
                    },
                    colors: ["#3461ff"],
                    xaxis: {
                        categories: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"]
                    },
                    fill: {
                        opacity: 1
                    },
                    tooltip: {
                        theme: "dark",
                        fixed: {
                            enabled: !1
                        },
                        x: {
                            show: !1
                        },
                        y: {
                            title: {
                                formatter: function(e) {
                                    return ""
                                }
                            }
                        },
                        marker: {
                            show: !1
                        }
                    }
                };
                var chart = new ApexCharts(document.querySelector("#chart10"), options);
                chart.render();

                var options = {
                    series: [{
                        name: "Conversions Action",
                        data: widget_conversations_action
                    }],
                    chart: {
                        type: "line",
                        width: 250,
                        height: 50,
                        toolbar: {
                            show: !1
                        },
                        zoom: {
                            enabled: !1
                        },
                        dropShadow: {
                            enabled: 0,
                            top: 3,
                            left: 14,
                            blur: 4,
                            opacity: .12,
                            color: "#3461ff"
                        },
                        sparkline: {
                            enabled: !0
                        }
                    },
                    markers: {
                        size: 0,
                        colors: ["#3461ff"],
                        strokeColors: "#fff",
                        strokeWidth: 2,
                        hover: {
                            size: 7
                        }
                    },
                    plotOptions: {
                        bar: {
                            horizontal: !1,
                            columnWidth: "35%",
                            endingShape: "rounded"
                        }
                    },
                    dataLabels: {
                        enabled: !1
                    },
                    stroke: {
                        show: !0,
                        width: 2.5,
                        curve: "smooth"
                    },
                    colors: ["#3461ff"],
                    xaxis: {
                        categories: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"]
                    },
                    fill: {
                        opacity: 1
                    },
                    tooltip: {
                        theme: "dark",
                        fixed: {
                            enabled: !1
                        },
                        x: {
                            show: !1
                        },
                        y: {
                            title: {
                                formatter: function(e) {
                                    return ""
                                }
                            }
                        },
                        marker: {
                            show: !1
                        }
                    }
                };
                var chart = new ApexCharts(document.querySelector("#chart_conversations_action"), options);
                chart.render();

                
                var options = {
                    series: [{
                        name: "Conversion Rate",
                        data: widget_conv_rate
                    }],
                    chart: {
                        type: "line",
                        width: 250,
                        height: 50,
                        toolbar: {
                            show: !1
                        },
                        zoom: {
                            enabled: !1
                        },
                        dropShadow: {
                            enabled: 0,
                            top: 3,
                            left: 14,
                            blur: 4,
                            opacity: .12,
                            color: "#3461ff"
                        },
                        sparkline: {
                            enabled: !0
                        }
                    },
                    markers: {
                        size: 0,
                        colors: ["#3461ff"],
                        strokeColors: "#fff",
                        strokeWidth: 2,
                        hover: {
                            size: 7
                        }
                    },
                    plotOptions: {
                        bar: {
                            horizontal: !1,
                            columnWidth: "35%",
                            endingShape: "rounded"
                        }
                    },
                    dataLabels: {
                        enabled: !1
                    },
                    stroke: {
                        show: !0,
                        width: 2.5,
                        curve: "smooth"
                    },
                    colors: ["#3461ff"],
                    xaxis: {
                        categories: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"]
                    },
                    fill: {
                        opacity: 1
                    },
                    tooltip: {
                        theme: "dark",
                        fixed: {
                            enabled: !1
                        },
                        x: {
                            show: !1
                        },
                        y: {
                            title: {
                                formatter: function(e) {
                                    return ""
                                }
                            }
                        },
                        marker: {
                            show: !1
                        }
                    }
                };
                var chart = new ApexCharts(document.querySelector("#chart11"), options);
                chart.render();

                
                var options = {
                    series: [{
                        name: "Cost Per Conversion",
                        data: widget_cost_per_conv
                    }],
                    chart: {
                        type: "line",
                        width: 250,
                        height: 50,
                        toolbar: {
                            show: !1
                        },
                        zoom: {
                            enabled: !1
                        },
                        dropShadow: {
                            enabled: 0,
                            top: 3,
                            left: 14,
                            blur: 4,
                            opacity: .12,
                            color: "#3461ff"
                        },
                        sparkline: {
                            enabled: !0
                        }
                    },
                    markers: {
                        size: 0,
                        colors: ["#3461ff"],
                        strokeColors: "#fff",
                        strokeWidth: 2,
                        hover: {
                            size: 7
                        }
                    },
                    plotOptions: {
                        bar: {
                            horizontal: !1,
                            columnWidth: "35%",
                            endingShape: "rounded"
                        }
                    },
                    dataLabels: {
                        enabled: !1
                    },
                    stroke: {
                        show: !0,
                        width: 2.5,
                        curve: "smooth"
                    },
                    colors: ["#3461ff"],
                    xaxis: {
                        categories: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"]
                    },
                    fill: {
                        opacity: 1
                    },
                    tooltip: {
                        theme: "dark",
                        fixed: {
                            enabled: !1
                        },
                        x: {
                            show: !1
                        },
                        y: {
                            title: {
                                formatter: function(e) {
                                    return ""
                                }
                            }
                        },
                        marker: {
                            show: !1
                        }
                    }
                };
                var chart = new ApexCharts(document.querySelector("#chart12"), options);
                chart.render();
            }
        // weidget charts ends

        // summary chart starts

            var impressions = [<?php echo $summary_graph_impressions; ?>];
            var clicks = [<?php echo $summary_graph_clicks; ?>];
            var conversations = [<?php echo $summary_graph_conversations; ?>];
            var dates = [<?php echo $summary_graph_dates; ?>];

            var optionsLine = {
                chart: {
                    foreColor: '#9ba7b2',
                    height: 360,
                    type: 'bar',
                    zoom: {
                        enabled: false
                    },
                    // dropShadow: {
                    //     enabled: true,
                    //     top: 3,
                    //     left: 2,
                    //     blur: 4,
                    //     opacity: 0.1,
                    // }
                },
                stroke: {
                    curve: 'smooth',
                    width: 5
                },
                colors: ['#4A90E2', '#7F8C8D', '#5DADE2'],
                series: [{
                    name: "Impressions",
                    data: impressions
                }, {
                    name: "Clicks",
                    data: clicks
                }, {
                    name: "Conversations",
                    data: conversations
                }],
                title: {
                    text: '',
                    align: 'left',
                    offsetY: 25,
                    offsetX: 20
                },
                subtitle: {
                    text: '',
                    offsetY: 55,
                    offsetX: 20
                },
                markers: {
                    size: 4,
                    strokeWidth: 0,
                    shape: 'square', 
                    hover: {
                        size: 7
                    }
                },
                grid: {
                    show: true,
                    padding: {
                        bottom: 0
                    }
                },
                labels: dates,
                xaxis: {
                    categories: dates,
                    labels: {
                        style: {
                            fontSize: '12px'
                        }
                    }
                },
                yaxis: [{
                    axisTicks: {
                        show: true,
                    },
                    axisBorder: {
                        show: true,
                        // color: '#FF9F41'
                    },
                    labels: {
                        style: {}
                    },
                    title: {
                        text: "Impressions",
                        style: {}
                    },
                    tooltip: {
                        enabled: true
                    }
                }, {
                    opposite: true,
                    axisTicks: {
                        show: true,
                    },
                    axisBorder: {
                        show: true,
                    },
                    labels: {
                        style: {}
                    },
                    title: {
                        text: "Clicks",
                        style: {}
                    },
                    tooltip: {
                        enabled: true
                    }
                }],
                legend: {
                    position: 'top',
                    horizontalAlign: 'center',
                }
            };
            var chartLine = new ApexCharts(document.querySelector('#summaryChart'), optionsLine);
            chartLine.render();
        // summary charts ends

        // performance chart starts

            var cost = [<?php echo $performance_graph_costs; ?>];
            var cost_per_1000_imp = [<?php echo $performance_graph_cost_per_1000_imp; ?>];
            var cost_per_click = [<?php echo $performance_graph_cost_per_click; ?>];
            var reveneu_per_click = [<?php echo $performance_graph_reveneu_per_click; ?>];
            var total_value = [<?php echo $performance_graph_total_value; ?>];
            var dates = [<?php echo $performance_graph_dates; ?>];

            var optionsLine = {
                chart: {
                    foreColor: '#9ba7b2',
                    height: 360,
                    type: 'line',
                    zoom: {
                        enabled: false
                    },
                    dropShadow: {
                        enabled: true,
                        top: 3,
                        left: 2,
                        blur: 4,
                        opacity: 0.1,
                    }
                },
                stroke: {
                    curve: 'smooth',
                    width: 5
                },
                colors: ['#4A90E2', '#7F8C8D', '#5DADE2', '#BDC3C7', '#2E86C1'],
                series: [{
                    name: "cost",
                    data: cost
                }, {
                    name: "Cost Per 1000 Impressions",
                    data: cost_per_1000_imp
                }, {
                    name: "Cost Per Click",
                    data: cost_per_click
                }, {
                    name: "Reveneu Per Click",
                    data: reveneu_per_click
                }, {
                    name: "Total Value",
                    data: total_value
                }],
                title: {
                    text: '',
                    align: 'left',
                    offsetY: 25,
                    offsetX: 20
                },
                subtitle: {
                    text: '',
                    offsetY: 55,
                    offsetX: 20
                },
                markers: {
                    size: 4,
                    strokeWidth: 0,
                    shape: 'square', 
                    hover: {
                        size: 7
                    }
                },
                grid: {
                    show: true,
                    padding: {
                        bottom: 0
                    }
                },
                labels: dates,
                yaxis: [{
                    axisTicks: {
                        show: true,
                    },
                    axisBorder: {
                        show: true
                    },
                    labels: {
                        style: {}
                    },
                    title: {
                        text: "Cost",
                        style: {}
                    },
                    tooltip: {
                        enabled: true
                    }
                }, {
                    opposite: true,
                    axisTicks: {
                        show: true,
                    },
                    axisBorder: {
                        show: true,
                    },
                    labels: {
                        style: {}
                    },
                    title: {
                        text: "Cost Per 1000 Impressions",
                        style: {}
                    },
                    tooltip: {
                        enabled: true
                    }
                }],
                legend: {
                    position: 'top',
                    horizontalAlign: 'center',
                }
            };
            var chartLine = new ApexCharts(document.querySelector('#performanceChart'), optionsLine);
            chartLine.render();
        // performance chart ends
    </script>
@endsection