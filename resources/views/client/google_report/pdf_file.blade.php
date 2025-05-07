<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="assets/images/favicon-32x32.png" type="image/png" />
    <!--plugins-->
    <link href="{{ asset('front') }}/assets/plugins/simplebar/css/simplebar.css" rel="stylesheet" />
    <link href="{{ asset('front') }}/assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css" rel="stylesheet" />
    <link href="{{ asset('front') }}/assets/plugins/metismenu/css/metisMenu.min.css" rel="stylesheet" />
    <!-- Bootstrap CSS -->
    <link href="{{ asset('front') }}/assets/css/bootstrap.min.css" rel="stylesheet" />
    <link href="{{ asset('front') }}/assets/css/bootstrap-extended.css" rel="stylesheet" />
    <link href="{{ asset('front') }}/assets/css/style.css" rel="stylesheet" />
    <link href="{{ asset('front') }}/assets/css/icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link href="{{ asset('front') }}/assets/plugins/highcharts/css/dark-unica.css" rel="stylesheet" />
    
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Product+Sans:wght@100;200;300;400;500;600;700&display=swap" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <link rel="stylesheet" href="{{ asset('front') }}/assets/css/google-report.css" />
    <title>Google Ads Report</title>
    <style>
        body {
            font-size: 10px !important;
            margin: 0;
            padding: 0;
        }

        .container {
            text-align: center;
            margin-top: 100px;
        }

        .heading {
            margin-top: 20px;
            /* font-size: 24px; */
        }

        .apexcharts-menu-icon {
            display: none;
        }

        #ctr-chart {
            background-color: white !important;
        }

    </style>
</head>

<body>
    <div class="container">

        <img src="{{ asset('front') }}/assets/images/logo.png" width="500px" height="300px" class="logo"
            alt="logo icon">

        <br><br><br><br>

        <div class="heading">
            <h1 class="text-dark">Google Ads Report</h1>
            <h6 class="text-dark"> {{ @$ad_account_name }}</h6>
        </div>

        <div class="generated-at">
            Generated on: {{ @$generated_on }}
        </div>
    </div>

    <div style="page-break-before:always">&nbsp;</div>

    <div class="d-flex align-items-center m-3">
        <div class="">
            <h3 class="text-dark">Google Ads - Summary</h3>
        </div>
        <div class="ms-auto">
            <h6 class="text-dark">{{ (new DateTime($start_date))->format('M d Y') }} - {{ (new DateTime($end_date))->format('M d Y')  }}</h6>
        </div>
    </div>
    <hr>

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
                        <div class="col-3">
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
                        <div class="col-3">
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
                        <div class="col-3">
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
                        <div class="col-3">
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
                        <div class="col-4">
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
                        <div class="col-4">
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
                        <div class="col-4">
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
                        <div class="col-4">
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
                        <div class="col-4">
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
                        <div class="col-4">
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
                                            if (isset($item['final_url'])){
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
                                            <td>{{ $metrics['impressions'] ?? '...'}}</td>
                                            <td>{{ $metrics['clicks'] ?? '...' }}</td>
                                            <td>{{ round($ctr, 2) ?? '0.00' }}%</td>
                                            <td>{{ $cost ?? '0.00' }}</td>
                                            <td>{{ $metrics['conversions'].'.00' ?? '0.00' }}</td>
                                            <td>{{ $metrics['conversions'].'.00' ?? '0.00' }}</td>
                                            <td>{{ $metrics['roas'] ?? '0.00' }}</td>
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
                                            <td>{{ $metrics['impressions'] ?? '...'}}</td>
                                            <td>{{ $metrics['clicks'] ?? '...' }}</td>
                                            <td>{{ round($ctr, 2) ?? '0.00' }}%</td>
                                            <td>{{ $cost ?? '0.00' }}</td>
                                            <td>{{ $metrics['conversions'].'.00' ?? '0.00' }}</td>
                                            <td>{{ $metrics['conversions'].'.00' ?? '0.00' }}</td>
                                            <td>{{ $metrics['roas'] ?? '0.00' }}</td>
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
                                            <td>{{ $metrics['impressions'] ?? '...' }}</td>
                                            <td>{{ $metrics['clicks'] ?? '...' }}</td>
                                            <td>
                                                @if ($ctr != 0)
                                                    {{ round($ctr, 2) ?? '0.00' }}%
                                                @else
                                                    0.00%
                                                @endif
                                            </td>
                                            <td>
                                                @if ($cost != 0)
                                                    {{ $cost ?? '0.00' }}
                                                @else
                                                    0.00
                                                @endif
                                            </td>
                                            <td>{{ $metrics['conversions'].'.00' ?? '0.00' }}</td>
                                            <td>{{ $metrics['conversions'].'.00' ?? '0.00' }}</td>
                                            <td>{{ $metrics['roas'] ?? '0.00' }}</td>
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
                                                    </small>
                                                    <br>
                                                    <a href="Javascript:void(0);">Between Newton & Novena Area...</a>
                                                </h6>
                                                <p>The nearest MRT station to Former...</p>
                                            </td>
                                            <td>{{ $adGroupAd['ad']['type'] }}</td>
                                            <td>{{ $metrics['impressions'] ?? '...' }}</td>
                                            <td>{{ $metrics['clicks'] ?? '...' }}</td>
                                            <td>{{ round($ctr, 2) ?? '0.00' }}%</td>
                                            <td>{{ $cost ?? '0.00' }}</td>
                                            <td>{{ $metrics['conversions'].'.00' ?? '0.00' }}</td>
                                            <td>{{ $metrics['conversions'].'.00' ?? '0.00' }}</td>
                                            <td>{{ $metrics['roas'] ?? '0.00' }}</td>
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
                                    <td>{{ $unspecified_imp ?? '...' }}</td>
                                    <td>{{ $unspecified_click ?? '...' }}</td>
                                    <td>{{ $unspecified_imp > 0 ? round($unspecified_click / $unspecified_imp * 100, 2).'%' : '0.00%' }}</td>
                                    <td>{{ $unspecified_cost ?? '0.00' }}</td>
                                    <td>{{ $unspecified_conversation.'.00' ?? '0.00' }}</td>
                                    <td>{{ $unspecified_conversation.'.00' ?? '0.00' }}</td>
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
                                    <td>{{ $performance_device['MOBILE']['cost'] ?? '0.00' }}</td>
                                    <td>{{ isset($performance_device['MOBILE']) && is_array($performance_device['MOBILE']) ? $performance_device['MOBILE']['conversions'].'.00' : '0.00' }}</td>
                                    <td>{{ isset($performance_device['MOBILE']) && is_array($performance_device['MOBILE']) ? $performance_device['MOBILE']['conversions'].'.00' : '0.00' }}</td>
                                    <td>0.00</td>
                                </tr>
                                <tr>
                                    <td>DESKTOP</td>
                                    <td>{{ $performance_device['DESKTOP']['impressions'] ?? '...'}}</td>
                                    <td>{{ $performance_device['DESKTOP']['clicks'] ?? '...'}}</td>
                                    <td>{{ isset($performance_device['DESKTOP']['impressions']) && $performance_device['DESKTOP']['impressions'] > 0 ? round($performance_device['DESKTOP']['clicks'] / $performance_device['DESKTOP']['impressions'] * 100, 2).'%' : '0.00%' }}</td>
                                    <td>{{ $performance_device['DESKTOP']['cost'] ?? '0.00' }}</td>
                                    <td>{{ isset($performance_device['DESKTOP']) && is_array($performance_device['DESKTOP']) ? $performance_device['DESKTOP']['conversions'].'.00' : '0.00' }}</td>
                                    <td>{{ isset($performance_device['DESKTOP']) && is_array($performance_device['DESKTOP']) ? $performance_device['DESKTOP']['conversions'].'.00' : '0.00' }}</td>
                                    <td>0.00</td>
                                </tr>
                                <tr>
                                    <td>TABLET</td>
                                    <td>{{ $performance_device['TABLET']['impressions'] ?? '...'}}</td>
                                    <td>{{ $performance_device['TABLET']['clicks'] ?? '...'}}</td>
                                    <td>{{ isset($performance_device['TABLET']['impressions']) && $performance_device['TABLET']['impressions'] > 0 ? round($performance_device['TABLET']['clicks'] / $performance_device['TABLET']['impressions'] * 100, 2).'%' : '0.00%' }}</td>
                                    <td>{{ $performance_device['TABLET']['cost'] ?? '0.00' }}</td>
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
                                    <td>{{ $unspecified_imp ?? '...' }}</td>
                                    <td>{{ $unspecified_click ?? '...' }}</td>
                                    <td>{{ $unspecified_imp > 0 ? round($unspecified_click / $unspecified_imp * 100, 2).'%' : '0.00%' }}</td>
                                    <td>{{ $unspecified_cost ?? '0.00' }}</td>
                                    <td>{{ $unspecified_conversation.'.00' ?? '0.00' }}</td>
                                    <td>{{ $unspecified_conversation.'.00' ?? '0.00' }}</td>
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
                                    <td>{{ $unspecified_imp ?? '...' }}</td>
                                    <td>{{ $unspecified_click ?? '...' }}</td>
                                    <td>{{ $unspecified_imp > 0 ? round($unspecified_click / $unspecified_imp * 100, 2).'%' : '0.00%' }}</td>
                                    <td>{{ $unspecified_cost ?? '0.00' }}</td>
                                    <td>{{ $unspecified_conversation.'.00' ?? '0.00' }}</td>
                                    <td>{{ $unspecified_conversation.'.00' ?? '0.00' }}</td>
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
                <form action="{{ route('user.google-ads-report.save_google_report') }}" method="POST" class="date_range_ajaxForm">
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

                    <form action="{{ route('user.google-ads-report.campaign_note_save') }}" method="POST"
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
                                            data-url="{{ route('user.google-ads-report.campaign_note_delete', $note->hashid) }}"
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

    <script src="{{ asset('front') }}/assets/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('front') }}/assets/js/jquery.min.js"></script>
    <script src="{{ asset('front') }}/assets/plugins/simplebar/js/simplebar.min.js"></script>
    <script src="{{ asset('front') }}/assets/plugins/metismenu/js/metisMenu.min.js"></script>
    <script src="{{ asset('front') }}/assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js"></script>
    <script src="{{ asset('front') }}/assets/js/pace.min.js"></script>
    <script src="{{ asset('front') }}/assets/plugins/apexcharts-bundle/js/apexcharts.min.js"></script>
    <script src="{{ asset('front') }}/assets/plugins/highcharts/js/highcharts.js"></script>
    <script src="{{ asset('front') }}/assets/js/app.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"
        integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
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
                // width: 5
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
                },
                labels: {
                    style: {
                        fontSize: '12px'
                    }
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
                width: 3,
                dashArray: [5, 5]
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
</body>

</html>
