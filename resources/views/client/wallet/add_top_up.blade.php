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




    *,
    *:before,
    *:after {
        box-sizing: border-box;
    }

    :root {
        --c-grey-100: #f4f6f8;
        --c-grey-200: #e3e3e3;
        --c-grey-300: #b2b2b2;
        --c-grey-400: #7b7b7b;
        --c-grey-500: #3d3d3d;

        --c-blue-500: #688afd;
    }

    /* Some basic CSS overrides */
    body {
        line-height: 1.5;
        min-height: 100vh;
        font-family: "Outfit", sans-serif;
        /* padding-bottom: 20vh; */
    }

    button,
    input,
    select,
    textarea {
        font: inherit;
    }

    a {
        color: inherit;
    }

    img {
        /* display: block; */
        max-width: 100%;
    }

    /* End basic CSS override */

    .timeline {
        width: 85%;
        max-width: 700px;
        margin-left: auto;
        /* margin-right: auto; */
        display: flex;
        flex-direction: column;
        padding: 32px 0 32px 32px;
        border-left: 2px solid var(--c-grey-200);
        font-size: 1.125rem;
    }

    .timeline-item {
        display: flex;
        gap: 24px;

        &+* {
            margin-top: 24px;
        }

        &+.extra-space {
            margin-top: 48px;
        }
    }

    .new-comment {
        width: 100%;

        input {
            border: 1px solid var(--c-grey-200);
            border-radius: 6px;
            height: 48px;
            padding: 0 16px;
            width: 100%;

            &::placeholder {
                color: var(--c-grey-300);
            }

            &:focus {
                border-color: var(--c-grey-300);
                outline: 0; // Don't actually do this
                box-shadow: 0 0 0 4px var(--c-grey-100);
            }
        }
    }

    .timeline-item-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        margin-left: -52px;
        flex-shrink: 0;
        overflow: hidden;
        box-shadow: 0 0 0 6px #fff;

        svg {
            width: 20px;
            height: 20px;
        }

        &.faded-icon {
            background-color: var(--c-grey-100);
            color: var(--c-grey-400);
        }

        &.filled-icon {
            background-color: var(--c-blue-500);
            color: #fff;
        }
    }

    .timeline-item-description {
        display: flex;
        padding-top: 6px;
        gap: 8px;
        color: var(--c-grey-400);

        img {
            flex-shrink: 0;
        }

        a {
            color: var(--c-grey-500);
            font-weight: 500;
            text-decoration: none;

            &:hover,
            &:focus {
                outline: 0; // Don't actually do this
                color: var(--c-blue-500);
            }
        }
    }

    .avatar {
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        overflow: hidden;
        aspect-ratio: 1 / 1;
        flex-shrink: 0;
        width: 40px;
        height: 40px;

        &.small {
            width: 28px;
            height: 28px;
        }

        img {
            object-fit: cover;
        }
    }

    .comment {
        margin-top: 12px;
        color: var(--c-grey-500);
        border: 1px solid var(--c-grey-200);
        box-shadow: 0 4px 4px 0 var(--c-grey-100);
        border-radius: 6px;
        padding: 16px;
        font-size: 1rem;
    }

    .button {
        border: 0;
        padding: 0;
        display: inline-flex;
        vertical-align: middle;
        margin-right: 4px;
        margin-top: 12px;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        height: 32px;
        padding: 0 8px;
        background-color: var(--c-grey-100);
        flex-shrink: 0;
        cursor: pointer;
        border-radius: 99em;

        &:hover {
            background-color: var(--c-grey-200);
        }

        &.square {
            border-radius: 50%;
            color: var(--c-grey-400);
            width: 32px;
            height: 32px;
            padding: 0;

            svg {
                width: 24px;
                height: 24px;
            }

            &:hover {
                background-color: var(--c-grey-200);
                color: var(--c-grey-500);
            }
        }
    }

    .show-replies {
        color: var(--c-grey-300);
        background-color: transparent;
        border: 0;
        padding: 0;
        margin-top: 16px;
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 1rem;
        cursor: pointer;

        svg {
            flex-shrink: 0;
            width: 24px;
            height: 24px;
        }

        &:hover,
        &:focus {
            color: var(--c-grey-500);
        }
    }

    .avatar-list {
        display: flex;
        align-items: center;

        &>* {
            position: relative;
            box-shadow: 0 0 0 2px #fff;
            margin-right: -8px;
        }
    }

    @media only screen and (min-width: 769px) {

        /* CSS rules for laptop screens */
        .paynow_text {
            width: 660px !important;
            padding-left: 93px !important;
        }
    }

    .error {
        color: red;
    }


    .payment_modal {
        max-width: 90% !important;
    }

    @media (max-width: 1275px) {
        #main-wallet-container {
            display: block !important;
            width: 100% !important;
        }

        #main-wallet-container>div {
            width: 100% !important;
            margin-top: 20px;
        }

        .payment_modal {
            max-width: 97% !important;
            width: 97% !important;
        }

    }

    @media (max-width: 1200px) {
        .amount-btn-container {
            width: 90% !important;
        }
    }

    .selected_card {
        background-color: transparent !important;
        max-height: 135px;
        width: 100%;
        min-height: 135px;
        max-width: 234px;
        min-width: 234px;
        mix-blend-mode: normal;

    }

    .subwallet_card {
        background-color: transparent !important;
        max-height: 135px;
        width: 100%;
        min-height: 135px;
        max-width: 234px;
        min-width: 234px;
        mix-blend-mode: luminosity;


    }
    #swal2-title{
        font-size: 26px;
    }
</style>



<link href="{{ asset('front') }}/assets/plugins/datatable/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
@endsection


@push('styles')
<link rel="stylesheet" href="https://unpkg.com/intro.js/introjs.css">
<style>
    .introjs-tooltip {
        max-width: 600px;
        width: 600px;
    }

    /* .introjs-tooltip-header, */
    /* .introjs-tooltipbuttons .introjs-skipbutton, */
    .introjs-tooltipbuttons .introjs-prevbutton {
        display: none !important;
    }

    .introjs-tooltipReferenceLayer {
        z-index: 2004 !important;
    }

    .introjs-showElement {
        z-index: 2003 !important;
    }

    .introjs-overlay {
        z-index: 2002 !important;
    }

    .introjs-helperLayer {
        z-index: 2001 !important;
    }

    #paynowqrmodal {
        z-index: 3001 !important;
    }

    .swal2-container {
        z-index: 4001 !important;
    }

    @media (max-width: 768px) {
        .introjs-tooltip {
            max-width: 90vw;
            width: 90vw;
        }

        .introjs-tooltiptext {
            font-size: 12px;
        }
        .sub_walllet_inner_container {
             flex-direction: column-reverse;
        }
    }
   
</style>
@endpush

@push('scripts-head')
<script src="https://unpkg.com/intro.js/intro.js"></script>
@endpush

@section('content')



@include('components.client_nav_tabs')
@if (session('success'))
<div class="row mt-5" id="success_msg">
    <div class="col-xl-12 mx-auto">
        <div id="success-message  mt-5" class="alert alert-success">
            {{ session('success') }}
        </div>
    </div>
</div>
@elseif (session('error'))
    <div class="alert alert-danger mt-5" role="alert">
        {{ session('error') }}
    </div>
@endif


<div class="col-12 col-xl-12 mt-5">
    <div class="card shadow radius-10 w-100">
        <div class="card-body">
            <div class="row align-items-center g-3">
                <div class="col-12 col-lg-6">
                    <h5 class="mb-0">Main Wallet</h5>
                </div>
                <div class="col-12 col-lg-6 text-md-end">
                    <button type="button" class="btn btn-primary topUpModal" id="step-1">Add TopUp</button>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-lg-4 col-12 col-md-6">
                    {{-- Main Wallet Card --}}
                    <div class="card radius-10 bg-info" style="background-color: #39548a !important; height: 240px;"
                        id="step-2">
                        <div class="card-body p-lg-4 d-flex flex-column justify-content-between"
                            style="min-height: 190px;">
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
                                        <p class="mb-1 text-white"
                                            style="font-size: 14px; font-weight: bold; line-height: 17.6px;">Account
                                            Balance</p>
                                        <h4 class="mb-0 text-white"
                                            style="font-weight: 900; font-size: 20px; line-height: 25.14px;">
                                            {{get_price($total_balance)}}</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end align-items-center gap-2">
                                <div class="d-flex align-items-center flex-column"
                                    style="font-family: Inter, sans-serif;">
                                    <p class="mb-0 w-100 text-white text-end pe-1"
                                        style="font-weight: bold; font-size: 12px;">Last Updated:</p>
                                    <p class="text-white"
                                        style="font-size: 12px; margin-bottom: 0px; font-weight: bold;">
                                        {{get_date(@$last_transaction_date)}} {{get_time(@$last_transaction_date)}}</p>
                                </div>
                                <div class="d-flex justify-content-center align-items-center" style="width: 38px;
                                    background-color: white;
                                    height: 38px;
                                    border-radius: 50%;
                                    padding: 10px;">
                                    <img src="{{ asset('front') }}/assets/images/calendar.png"
                                        style="display: block !important;" alt="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <div class="col-12 col-xl-12 mt-5">
        <div class="card shadow radius-10 w-100">
            <div class="card-body">
                <div class="row align-items-center g-3">
                    <div class="col-12 col-lg-6">
                        <h5 class="mb-0">Sub Wallets</h5>
                    </div>
                    @if($sub_wallets->count() != 0)
                    <div class="col-12 col-lg-6 text-md-end">
                        <a href="{{ route('user.wallet.transfer_funds') }}" class="btn btn-primary" id="step-3">Fund
                            Now</a>
                    </div>
                    @endif
                </div>
                <hr>


                <div class="d-flex flex-lg-row flex-md-row flex-column flex-wrap justify-content-lg-start justify-content-md-start justify-content-center " style="gap: 20px">
                    @forelse ($sub_wallets as $walt)
                    <div class="" style="min-width: 300px">
                        <div class="card radius-10 bg-info"
                            style="background-color: #39548a !important; min-height: 390px;" id="step-4">
                            <div class="card-body p-lg-4 d-flex flex-column justify-content-between"
                                style="min-height: 190px; {{$walt->status == 'close' ? 'opacity: 0.5; cursor: no-drop;' : '' }}">
                                <div>
                                    <div class="d-flex justify-content-between">
                                        <a href="{{ route('user.wallet.transactions',['ads_id'  => $walt->hashid]) }}">
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
                                                    Str::limit(ucfirst($walt->adds_title), 30, "...") }}</p>
                                            </div>
                                        </div>
                                        </a>
                                        <div class="d-flex align-items-center">
                                            @php
                                            $bdg_color = '#FCC82D';
                                            $text_color = 'black';
                                            if($walt->status == 'running' || $walt->status == 'complete'){
                                            $bdg_color = '#10bd23';
                                            }if($walt->status == 'reject' || $walt->status == 'close'){
                                            $bdg_color = '#e70e12';
                                            $text_color = '#fff';
                                            }
                                            $monthly_budget = 0;
                                            $daily_budget = 0;
                                            $day = 0;
                                            if($walt->spend_type == 'daily'){
                                            $daily_budget = $walt->daily_budget;
                                            $monthly_budget = $walt->daily_budget*30;
                                            }else{
                                            $daily_budget = $walt->daily_budget/30;
                                            $monthly_budget = $walt->daily_budget;
                                            }
                                            @endphp
                                            <span class="text-center text-truncate" style="
                                        font-size: 9.84px;
                                        font-family: Inter, sans-serif;
                                        background-color: {{$bdg_color}};
                                        padding: 3px 7px;
                                        white-space:nowrap;
                                        border-radius: 100px;
                                        
                                        font-weight: 600; margin-right: 3px; color:{{$text_color}}
                                        ">
                                                {{ ads_status_text($walt->status) }}
                                            </span>
                                        @if($walt->status != 'close' && $walt->status == 'running')    
                                            <span class="close_wallet text-center text-truncate" style="
                                                font-size: 9.84px;
                                                font-family: Inter, sans-serif;
                                                background-color: red;
                                                padding: 3px 7px;
                                                white-space:nowrap;
                                                border-radius: 100px;
                                                font-weight: 600; color: #fff; cursor: pointer;"
                                                data-id="{{$walt->id}}">
                                                Close <i class="fa fa-close"></i>
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="mt-3 d-flex justify-content-between sub_walllet_inner_container">
                                        <div>
                                            <p class="mb-1 text-white"
                                                style="font-size: 14px; font-weight: bold; line-height: 17.6px;">
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
                                        <div class="my-3 mt-lg-0">
                                        @if($walt->status != 'close')
                                            <p class="mb-1 text-white"
                                                style="font-size: 14px; font-weight: bold; line-height: 17.6px;">
                                                Account Recharge</p>
                                                <h4 class="mb-0 text-white" style="font-weight: 900;font-size: 16px;line-height: 25.14px;background: #21345c;padding: 10px;border-radius: 10px;"> 
                                                @if($walt->spend_amount != 0)        
                                                    <span style="color: #12bf24;padding-right: 4px; {{($monthly_budget-$walt->spend_amount) != 0 && $walt->spend_amount < $monthly_budget ? 'border-right: 1px solid gray;' : '' }}">
                                                    SGD {{ $walt->spend_amount && $walt->spend_amount > 0 ? number_format($walt->spend_amount, 2) : 0 }}</span>
                                                @endif    
                                                @if(($monthly_budget-$walt->spend_amount) != 0 && $walt->spend_amount < $monthly_budget)  
                                                    <span style="color: #e72e2e;">SGD {{ number_format(($monthly_budget - $walt->spend_amount), 2) }}</span>
                                                @endif
                                                </h4>
                                        @endif        
                                        </div>
                                    </div>

                                    @if($walt->spend_type == 'daily' || $walt->spend_type == 'monthly')
                                    @php



                                    $days = 0;
                                    $max_days = 30;
                                    if ($daily_budget > 0) {
                                    $days = floor($walt->spend_amount / $daily_budget);
                                    $days = min($days, $max_days);
                                    }


                                    @endphp
                                    @if($walt->status != 'close')
                                    <div style="margin-top: 15px;">
                                        <p style="font-family: Outfit;
                                    font-size: 14px;
                                    color: black;
                                    font-weight: 500;
                                    line-height: 17.64px;
                                    text-align: left; background-color: #FCC82D; border-radius: 3px; padding: 3px 7px; max-width: 315px;
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
                                    <div class="d-flex align-items-center flex-column"
                                        style="font-family: Inter, sans-serif;">
                                        <p class="mb-0 w-100 text-end pe-1"
                                            style="font-weight: bold; font-size: 12px;">Last Updated:</p>
                                        <p style="font-size: 12px; margin-bottom: 0px; font-weight: bold;">
                                            {{get_date(@$walt->updated_at)}} {{get_time(@$walt->updated_at)}}</p>
                                    </div>
                                    <div class="d-flex justify-content-center align-items-center" style="width: 38px;
                                    background-color: white;
                                    height: 38px;
                                    border-radius: 50%;
                                    padding: 10px;">
                                        <img src="{{ asset('front') }}/assets/images/calendar.png"
                                            style="display: block !important;" alt="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                </div>

                <div class="box-body pt-30 pb-30 text-center">
                    <i class="fadeIn animated bx bx-x-circle" style=" font-size: 50px; "></i>
                    <h4 id="sub_wallet_empty">No Sub Wallets Found</h4>
                </div>

                @endforelse
            </div>
        </div>
    </div>

</div>

<!-- Modal -->
<div class="modal fade" id="topUpModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog payment_modal" id="topupModalCardContainer">
        <div class="modal-content" id="topupModalCard" style="border-radius: 20px !important;">
            <div class="modal-header">
                <h5 class="modal-title">Add TopUp</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pb-5"
                style="box-shadow: inset 6px 6px 10px 0 rgba(0, 0, 0, 0.2), inset -6px -6px 10px 0 rgba(255, 255, 255, 0.5);background-color: #f2f5fa;border-bottom-left-radius: 20px; border-bottom-right-radius: 20px;">
                <div class="w-100 h-100 d-flex justify-content-center align-items-center">
                    <div style="width: 95%; height: 90%;">
                        <div id="main-wallet-container" class="h-100 w-100 d-flex " style="gap: 10px;">
                            <div class="border border-1 w-75 bg-white p-4">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="">Amount</label>
                                        <div class="input-group ">
                                            <span class="input-group-text">SGD</span>
                                            <input type="number" class="form-control"
                                                aria-label="Amount (to the nearest dollar)" type="number" id="amt"
                                                name="amt" min="1" maxlength="6" value="{{@$top_setting->default_topup ?? ''}}"
                                                {{@$top_setting->is_fillable == 0 ? 'readonly' : ''}} required>
                                            <span class="input-group-text">.00</span>
                                        </div>
                                        <span class="mb-3">Make partial payment of existing campaigns or pay by full and
                                            allocate your funds.</span>
                                        <input type="hidden" id="min_amt" value="{{@$top_setting->min_topup ?? ''}}">
                                        <input type="hidden" id="ad_id" value="0">
                                        <input type="hidden" id="type" value="">
                                        <input type="hidden" id="daily_budget" value="">
                                        <input type="hidden" id="partial_payment" value="0">
                                        <input type="hidden" id="partial_payment_text">
                                        <input type="hidden" id="ad_amt">
                                        <input type="hidden" id="spend_amount">
                                        <input type="hidden" id="hosting_amount" value="0">
                                        <input type="hidden" id="domain_amount" value="0">
                                        <input type="hidden" id="total_amt" value="1500">
                                    </div>
                                </div>
                                <div class="amount-btn-container d-flex justify-content-center flex-wrap"
                                    style="width: 100%; max-width: 700px; margin: auto; gap:10px" >
                                    <div class="d-flex justify-content-center align-items-center">
                                        <button type="button" class="btn btn-primary  my-2"
                                            style="background-color:#39548a !important; width: 100px;"
                                            onclick="addamount(1500,1);">SGD1500</button>
                                    </div>
                                    <div class="d-flex justify-content-center align-items-center">
                                        <button type="button" class="btn btn-primary my-2"
                                            style="background-color:#39548a !important; width: 100px;"
                                            onclick="addamount(2000,1);">SGD2000</button>
                                    </div>
                                    <div class="d-flex justify-content-center align-items-center">
                                        <button type="button" class="btn btn-primary my-2"
                                            style="background-color:#39548a !important; width: 100px;"
                                            onclick="addamount(3000,1);">SGD3000</button>
                                    </div>
                                    <div class="d-flex justify-content-center align-items-center">
                                        <button type="button" class="btn btn-primary  my-2" id="400_btn"
                                            style="background-color:#39548a !important; width: 100px;"
                                            onclick="addamount(4000,1);">SGD4000</button>
                                    </div>
                                    <div class="d-flex justify-content-center align-items-center">
                                        <button type="button" class="btn btn-primary  my-2"
                                            style="background-color:#39548a !important; width: 100px;"
                                            onclick="addamount(5000,1);">SGD5000</button>
                                    </div>
                                    <div class="d-flex justify-content-center align-items-center">
                                        <button type="button" class="btn btn-primary  my-2"
                                            style="background-color:#39548a !important; width: 100px;"
                                            onclick="addamount(6000,1);">SGD6000</button>
                                    </div>
                                    <div class="d-flex justify-content-center align-items-center">
                                        <button type="button" class="btn btn-primary  my-2"
                                            style="background-color:#39548a !important; width: 100px;"
                                            onclick="addamount(7000,1);">SGD7000</button>
                                    </div>
                                    <div class="d-flex justify-content-center align-items-center">
                                        <button type="button" class="btn btn-primary  my-2"
                                            style="background-color:#39548a !important; width: 100px;"
                                            onclick="addamount(8000,1);">SGD8000</button>
                                    </div>

                                </div>
                                @if(count($ads) != 0)
                                <div class="row mt-2">
                                    <hr>
                                    <h6 class="mb-4">Make Your Recent Ad Payment Campaign</h6>
                                    @foreach($ads as $ad_val)
                                    @php
                                    $day = 30;
                                    if($ad_val->status == 'running'){
                                    $days = floor($ad_val->spend_amount / $ad_val->daily_budget);
                                    $day = min($days, 30);
                                    }
                                    $budget = 0;
                                    if($ad_val->spend_amount != 0 && $ad_val->status == 'running'){
                                    $budget = $ad_val->daily_budget*$day;
                                    }else{
                                    $budget = $ad_val->daily_budget*30;
                                    }
                                    $amt = $ad_val->daily_budget;
                                    if($ad_val->spend_type == 'daily'){
                                    $amt = $budget;
                                    }
                                    @endphp
                                    @if(($amt-$ad_val->spend_amount) != 0 && $ad_val->spend_amount < $amt)  <div
                                    style="min-width: 104px;" class="col-12 col-md-6 col-lg-4 d-flex justify-content-center">
                                    <div  onclick="addamountsubwallet({{$amt-$ad_val->spend_amount}},{{$amt}},'{{$ad_val->spend_type}}',{{$ad_val->daily_budget}},{{$day}},{{$ad_val->id}},'{{\Str::limit($ad_val->adds_title, 10, '...')}}','{{$ad_val->adds_title}}', {{$ad_val->spend_amount ?? 0}} , '{{$ad_val->domain_is}}', '{{$ad_val->hosting_is}}', {{$ad_val->is_domain_pay}}, {{$ad_val->is_hosting_pay}})"
                                        class="card radius-10 shadow-none bg-info subwallets subwallet_card subwallet_card_div{{$ad_val->id}}"
                                        id="step-2" style="cursor: pointer; background-image: url('{{asset('front')}}/assets/images/card-bg.png');background-repeat: no-repeat;background-position: 100% 100%; background-size: cover;">
                                            <div style="width: 65%; height: 100%;padding: 23px 0px 10px 10px;display: flex;flex-direction: column;align-items: center;">
                                                <div style="width:76%; margin-bottom: 0px;">
                                                    <p title="SGD {{ $ad_val->spend_type == 'daily' ? $budget : $ad_val->daily_budget }}" style="font-family: Inter;font-size: 16px;font-weight: 700;color: #39548a; line-height: 27.84px;" class="mb-0">SGD {{ $ad_val->spend_type == 'daily' ? ($budget - $ad_val->spend_amount) : ($ad_val->daily_budget-$ad_val->spend_amount) }}</p>
                                                </div>
                                                <div style="width: 76%;margin-bottom: 5px;font-weight: 400;font-size: 12px;color: #1E1E1E91;">
                                                    {{ucfirst($ad_val->spend_type)}} BUDGET
                                                </div>
                                                <div style="width:76%">
                                                    <button   style="background-color: #39548a;color: white;border: none;border-radius: 18px; font-size: 12px;width: 87px;height: 24px; ">
                                                    <img src="{{asset('front')}}/assets/images/icons/add.png" width="16px" alt="add funds">    
                                                       Add Funds
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="position-absolute" style="padding: 0px 0px 0px 9px; z-index: 1;right: 0px;background-color:#39548a ; width: 34%; display: flex; align-items: center;  border-radius: 18px;  height: 23px;">
                                                <img src="{{ asset('front') }}/assets/images/icons/wallet.png" style="width: 14px"  alt="wallet">
                                                <p title="{{$ad_val->ads_title}}" class="mb-0 text-white ms-1" style="font-weight: 400; font-size: 12px">
                                                    {{\Str::limit($ad_val->adds_title, 7, '...')}}
                                                </p>
                                            </div>
                                            <div class="position-absolute" style="width: 78px; bottom: 5px; right: 14px">
                                                <img src="{{asset('front')}}/assets/images/icons/topup.png" style="opacity: 0.1" alt="topup">
                                            </div>
                                    </div>
                                    
                            </div>
                           
                                    
                                @endif
                                @endforeach
                            </div>
                            @endif
                        </div>
                        <div class="pt-4 border border-1 w-25 bg-white px-4" style="min-height: 507px; min-width: 310px;">
                            <p style="font-weight: bold;">Billing summary</p>
                            <div class="d-flex justify-content-between">
                                <span>Amount:</span>
                                <span class="summray_amt">
                                    SGD 1500
                                </span>
                            </div>
                            <div id="hosting_summary" style="display:none !important">
                            <div class="mt-3 d-flex justify-content-between" >
                                <span>Hosting Charges:</span>
                                <span>
                                SGD 15
                                </span>
                            </div>
                            </div>
                            <div id="domain_summary" style="display:none !important">
                            <div class="mt-3 d-flex justify-content-between">
                                <span>Domain Charges:</span>
                                <span>
                                SGD 20
                                </span>
                            </div>
                            </div>
                            <hr>

                            <div class="mt-3 d-flex justify-content-between">
                                <span>Amount to be paid:</span>
                                <span class="summray_total_amt">
                                    SGD 1500
                                </span>
                            </div>
                            <div class="mt-3 d-flex justify-content-between">
                                <b>Funding to:</b>
                                <span class="funding_wallet" style=" font-size: 14px; ">
                                    <b>Main Wallet</b>
                                </span>
                            </div>
                            <div class="p-2 mt-3 campaing_summary" style="background-color: #e5eeff; display:none">

                                <div class="mt-1 d-flex justify-content-between px-2">
                                    <span style="font-size: 13px; font-weight: bolder !important;">Campaign
                                        Budget:</span>
                                    <span style="font-size: 13px; font-weight: bolder !important;" id="s_camp_budget">

                                    </span>
                                </div>
                                <div style="font-weight: bolder; font-size: 12px; color: #39548a;"
                                    class="mt-1 d-flex justify-content-between px-2">
                                    <span id="s_camp_text"></span>
                                    <!-- <span >
                                        To be paid now
                                    </span> -->
                                </div>
                            </div>
                            <p class="mt-4">Select Payment Method</p>
                            <div class="modal-footer justify-content-center" id="payment-container">
                                <button type="button" class="btn btn-outline-primary" id="add_topup_btn_paynow"
                                    style="min-width: 200px; min-height:50px; border: 2px solid;font-weight:600">
                                    Pay by <img src="{{ asset('front') }}/assets/images/paynow-logo.png" alt=""
                                        style=" width: 85px; "></button>
                                <button type="button" id="add_topup_btn" class="btn btn-outline-primary"
                                    style="min-width: 200px; min-height:50px; border: 2px solid;font-weight:600">
                                    Pay by <img src="{{ asset('front') }}/assets/images/visa-card.png" alt=""
                                        style=" width: 85px; "></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<!-- paynow qr modal code -->
<div class="modal fade" id="paynowqrmodal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Paynow QR Code</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="add_topup_qr">
                    <div style=" text-align: center;">
                        <img src="{{ asset('front') }}/assets/images/paynowqr.jpg" alt="" style=" width: 220px;">
                        <div class="row paynow_text">
                            <p style=" color: red; font-weight: 600; ">Please note down this Transaction ID (<span
                                    id="paynow_transaction_id_text">{{ session('paynow_transaction_id'); }}</span>) and
                                include it when making the payment through PayNow.</p>
                            <p>Payments made will be processed under the name "Jome Journey Pte Ltd." We support a
                                variety of payment methods including PayNow, which is compatible with major banks and
                                the following app.
                            </p>
                        </div>
                    </div>
                    <div style=" text-align: center;">
                        <img class="bank_logo" src="{{ asset('front') }}/assets/images/bank/dbs_logo.png" alt=""
                            style="    width: 146px; filter: grayscale(100%) hue-rotate(180deg) brightness(150%); ">
                        <img class="bank_logo" src="{{ asset('front') }}/assets/images/bank/key_bank.png" alt=""
                            style="    width: 146px; filter: grayscale(100%) hue-rotate(180deg) brightness(150%);">
                        <img class="bank_logo" src="{{ asset('front') }}/assets/images/bank/ocbs.png" alt=""
                            style="    width: 146px; filter: grayscale(100%) hue-rotate(180deg) brightness(150%);">
                        <img class="bank_logo" src="{{ asset('front') }}/assets/images/bank/uob.png" alt=""
                            style="    width: 146px; filter: grayscale(100%) hue-rotate(180deg) brightness(150%);">
                    </div>
                    <ol class="timeline">
                        <li class="timeline-item">
                            <span class="timeline-item-icon | faded-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                                    <path fill="none" d="M0 0h24v24H0z" />
                                    <path fill="currentColor" d="M12 13H4v-2h8V4l8 8-8 8z" />
                                </svg>
                            </span>
                            <div class="timeline-item-description">
                                <span>Save this QR code to your photos or take a screenshot</span>
                            </div>
                        </li>
                        <li class="timeline-item">
                            <span class="timeline-item-icon | faded-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                                    <path fill="none" d="M0 0h24v24H0z" />
                                    <path fill="currentColor" d="M12 13H4v-2h8V4l8 8-8 8z" />
                                </svg>
                            </span>
                            <div class="timeline-item-description">
                                <span>Open your bank app or payment app</span>
                            </div>
                        </li>
                        <li class="timeline-item">
                            <span class="timeline-item-icon | faded-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                                    <path fill="none" d="M0 0h24v24H0z" />
                                    <path fill="currentColor" d="M12 13H4v-2h8V4l8 8-8 8z" />
                                </svg>
                            </span>
                            <div class="timeline-item-description">
                                <span>Select the option to scan a QR code</span>
                            </div>
                        </li>
                        <li class="timeline-item">
                            <span class="timeline-item-icon | faded-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                                    <path fill="none" d="M0 0h24v24H0z" />
                                    <path fill="currentColor" d="M12 13H4v-2h8V4l8 8-8 8z" />
                                </svg>
                            </span>
                            <div class="timeline-item-description">
                                <span>Upload the QR code you saved earlier</span>
                            </div>
                        </li>
                        <li class="timeline-item">
                            <span class="timeline-item-icon | faded-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                                    <path fill="none" d="M0 0h24v24H0z" />
                                    <path fill="currentColor" d="M12 13H4v-2h8V4l8 8-8 8z" />
                                </svg>
                            </span>
                            <div class="timeline-item-description">
                                <span>Attached your receipt in a form of screenshot</span>
                            </div>
                        </li>
                        <li class="timeline-item">
                            <span class="timeline-item-icon | faded-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                                    <path fill="none" d="M0 0h24v24H0z" />
                                    <path fill="currentColor" d="M12 13H4v-2h8V4l8 8-8 8z" />
                                </svg>
                            </span>
                            <div class="timeline-item-description">
                                <span>We will process the payment in 1- 2 working days</span>
                            </div>
                        </li>
                        <li class="timeline-item">
                            <span class="timeline-item-icon | faded-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                                    <path fill="none" d="M0 0h24v24H0z" />
                                    <path fill="currentColor" d="M12 13H4v-2h8V4l8 8-8 8z" />
                                </svg>
                            </span>
                            <div class="timeline-item-description">
                                <span>End your transaction by clicking Complete </span>
                            </div>
                        </li>
                    </ol>

                </div>
                <form method="POST" action="{{route('user.wallet.save')}}" class="row g-3 ajaxForm" id="add_topup_form"
                    novalidate="novalidate" style="display:none">
                    @csrf
                    <input type="hidden" value="{{ session('paynow_transaction_id'); }}" id="paynow_transaction_id">
                    <input type="hidden" name="ad_id" id="paynow_adId">
                    <div class="col-md-12">
                        <label for="">Amount<span class="text-danger">*</span></label>
                        <input type="text" oninput="this.value = this.value.replace(/[^0-9]/g, '');" name="amount"
                            id="amount" value="{{@$top_setting->default_topup ?? ''}}" placeholder="Enter TopUp Amount"
                            class="form-control" required="" readonly style="background-color: #e9ecef;">
                            <span>If you need to change this amount, you'll have to go back and make the update in the 'Add Top-up' section.</span>
                    </div>
                    <div class="col-md-12">
                        <label for="">Proof<span class="text-danger">* (Upload Deposit Slip Image, Type: jpeg, jpg,
                                png)</span></label>
                        <input type="file" name="deposit_slip[]" id="deposit_slip" value="" class="form-control"
                            accept=".jpeg, .jpg, .png" required="">
                    </div>
                    <div class="form-group mb-3 text-right">
                        <button type="submit" class="btn btn-primary px-5 form-submit-btn">Submit</button>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="show_qr_code" style="display:none">Show QR
                    Code</button>
                <button type="button" class="btn btn-primary" id="add_topup_details">Complete </button>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->

@endsection
@section('page-scripts')
<script src="{{ asset('front') }}/assets/plugins/datatable/js/jquery.dataTables.min.js"></script>
<script src="{{ asset('front') }}/assets/plugins/datatable/js/dataTables.bootstrap5.min.js"></script>
<script src="{{ asset('front') }}/assets/js/table-datatable.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>
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
    $('#amt').on('input', function () {
        var value = $(this).val();
        if (value.length > 6) {
            $(this).val(value.slice(0, 6)); // Sirf 6 digits rakhne ke liye
        }
    });

    let url = "{{route('user.wallet.add_paynow_transaction_id')}}";

    function getTransactionId() {
        $.ajax({
            url: url,
            type: 'GET',
            success: function(response) {
                $('#paynow_transaction_id').val(response);
                $('#paynow_transaction_id_text').html(response);
                // console.log('Transaction ID saved successfully');
            },
            error: function(xhr, status, error) {
                // console.error('Error saving Transaction ID:', error);
            }
        });
    }

    $('.topUpModal').click(function() {
        $('#add_topup_form').hide();
        $('#add_topup_qr').show();
        $('#show_qr_code').hide();
        $('#add_topup_details').show();
        paynow_transaction_id = $('#paynow_transaction_id').val();
        if(paynow_transaction_id == ''){
            getTransactionId();
        }

        $('#topUpModal').modal('show');
    });
    $("#show_qr_code").click(function() {
        $('#add_topup_form').hide();
        $('#add_topup_qr').show();
        $('#show_qr_code').hide();
        $('#add_topup_details').show();
    })
    $('#add_topup_details').click(function() {
        $('#add_topup_form').show();
        $('#add_topup_qr').hide();
        $('#show_qr_code').show();
        $('#add_topup_details').hide();
    })
</script>
<script>
    function addamountsubwallet(amt,campaign_budget,type,daily_budget,days,ad_id,adds_title,full_title,spend_amount,domain_is,hosting_is,is_domain_pay,is_hosting_pay) {
        $('#hosting_summary').hide();
        $('#domain_summary').hide();
        $("#domain_amount").val(0);
        $("#hosting_amount").val(0) 
        if(is_domain_pay == 0){
            if(domain_is == 'request_to_purchase'){
                $("#domain_amount").val(20)
                $('#domain_summary').show()
            }else{
                $("#domain_amount").val(0)
                $('#domain_summary').hide()
            }
        }
        if(is_hosting_pay == 0){  
            if(hosting_is == 'request_to_purchase_hosting'){
                $("#hosting_amount").val(15)
                $('#hosting_summary').show()
            }else{
                $("#hosting_amount").val(0) 
                $('#hosting_summary').hide()
            }
        }
        
        addamount(amt);
        $('#partial_payment').val(0);
        $('.funding_wallet').html(`<b title="${full_title}">Sub Wallet (${adds_title})</b>`);
        $('.subwallets').removeClass('selected_card');
        $('.subwallets').addClass('subwallet_card');
        $('.subwallet_card_div'+ad_id).removeClass('subwallet_card');
        $('.subwallet_card_div'+ad_id).addClass('selected_card');
        $('#ad_id').val(ad_id);
        $('#type').val(type);
        $('#daily_budget').val(daily_budget);
        $('#paynow_adId').val(ad_id);
        $('#spend_amount').val(spend_amount);
        $('#ad_amt').val(amt);

        $('#s_camp_budget').html('SGD '+campaign_budget+'.00');
        $('#s_camp_text').html(`Your Ads are expected to end in: ${days} days`);
        $('.campaing_summary').show();
    }
    $("#amount").keyup(function() {
        type = $('#type').val();
        daily_budget = $('#daily_budget').val();
        ad_amt = $('#ad_amt').val();
        current_amt = $(this).val();
        ad_id = $('#ad_id').val();
        input_amt = $('#amt').val(current_amt);
        spend_amount = $('#spend_amount').val();

        if(ad_amt > 0){
            if (type === 'daily') {
                result = (parseFloat(current_amt)+parseFloat(spend_amount)) / daily_budget;
                campaign_budget = daily_budget*30;

            }else if (type === 'monthly') {
                let monthly_budget = daily_budget; // Get monthly budget
                daily_budget = monthly_budget / 30; // Convert monthly to daily
                result = (parseFloat(current_amt)+parseFloat(spend_amount)) / daily_budget;
                campaign_budget = monthly_budget;
            }
            if((parseFloat(spend_amount)+parseFloat(current_amt)) < parseInt(campaign_budget)){
                endDate = new Date();
                endDate.setDate(endDate.getDate() + Math.ceil(result)); // Add the result to the current date

                // Format the date as 12/01/2024
                let formattedDate = ('0' + endDate.getDate()).slice(-2) + '/' +
                        ('0' + (endDate.getMonth() + 1)).slice(-2) + '/' +
                        endDate.getFullYear();
                $('#s_camp_text').html(`<img src="{{ asset("front/assets/images/warning.svg") }}" style=" width: 17px; "> Please note that with this payment, you are expected to complete by ${formattedDate} <br> Would you like to proceed?`);
                $('.summray_amt').html('SGD '+current_amt);
                // if(parseInt(current_amt) <  parseInt(ad_amt)){
                    $('#partial_payment').val(1);
                    $('#partial_payment_text').val(`Please note that with this payment, you are expected to complete by ${formattedDate}. Would you like to proceed?`);
                // }else{
                //     $('#partial_payment').val(0);
                //     $('#partial_payment_text').val('');
                // }
            }else{
                $('#partial_payment').val(0);
                $('#partial_payment_text').val('');
                $('#s_camp_text').html('Your Ads are expected to end in: 30 days');
            }
        }else{
            ad_id = $('#ad_id').val(0);
            $('.summray_amt').html('SGD '+current_amt);
        }
    })
    $("#amt").keyup(function() {
        ad_id = $('#ad_id').val();
        daily_budget = $('#daily_budget').val();
        input_amt = $('#amt').val();
        type = $('#type').val();
        ad_amt = $('#ad_amt').val();
        current_amt = $(this).val();
        spend_amount = $('#spend_amount').val();
        
        
        if(input_amt == ''){ input_amt = 0;} console.log(input_amt);
        if(ad_id > 0){
            if (type === 'daily') {
                result = (parseFloat(input_amt)+parseFloat(spend_amount)) / daily_budget;
                campaign_budget = daily_budget*30;
            }else if (type === 'monthly') {
                let monthly_budget = daily_budget; // Get monthly budget
                daily_budget = monthly_budget / 30; // Convert monthly to daily
                result = (parseFloat(input_amt)+parseFloat(spend_amount)) / daily_budget;
                campaign_budget = monthly_budget;
            }

            if((parseFloat(spend_amount)+parseFloat(current_amt)) < parseInt(campaign_budget)){
                endDate = new Date();
                endDate.setDate(endDate.getDate() + Math.ceil(result)); // Add the result to the current date
                // Format the date as 12/01/2024
                let formattedDate = ('0' + endDate.getDate()).slice(-2) + '/' +
                        ('0' + (endDate.getMonth() + 1)).slice(-2) + '/' +
                        endDate.getFullYear();
                $('#s_camp_text').html(`<img src="{{ asset("front/assets/images/warning.svg") }}" style=" width: 17px; "> Please note that with this payment, you are expected to complete by ${formattedDate} <br> Would you like to proceed?`);
                $('.summray_amt').html('SGD '+input_amt);


                // if(parseInt(current_amt) < parseInt(ad_amt)){
                    $('#partial_payment').val(1);
                    $('#partial_payment_text').val(`Please note that with this payment, you are expected to complete by ${formattedDate}. Would you like to proceed?`);
                // }else{
                //     $('#partial_payment').val(0);
                //     $('#partial_payment_text').val('');
                // }
            }else{
                $('#partial_payment').val(0);
                $('#partial_payment_text').val('');
                $('#s_camp_text').html('Your Ads are expected to end in: 30 days');
            }

        }else{
            ad_id = $('#ad_id').val(0);
            $('.summray_amt').html('SGD '+input_amt);
        }
    })
    function addamount(amt,main_wallet = 0) {
        if(amt == ''){amt = 0; } 
        var is_fillable = "{{@$top_setting->is_fillable}}"
        if(is_fillable == 1){
            $('.campaing_summary').hide();
            $('.funding_wallet').html(`<b>Main Wallet</b>`);
            $('.subwallets').removeClass('selected_card');
            $('.subwallets').addClass('subwallet_card');
            $('#ad_id').val(0);
            if(main_wallet == 1){
                $('#hosting_amount').val(0);
                $('#domain_amount').val(0);
                $('#hosting_summary').hide();
                $('#domain_summary').hide();
            }
            hosting_amount = $('#hosting_amount').val();
            domain_amount = $('#domain_amount').val();
            total_amt = parseFloat(amt)+parseFloat(hosting_amount)+parseFloat(domain_amount);

            $('#type').val('');
            $('#daily_budget').val('');
            $('#paynow_adId').val('');
            $('#partial_payment').val(0);
            $('#amt').val(amt);
            $('#amount').val(total_amt);
            $('.summray_amt').html('SGD '+amt);
            $('.summray_total_amt').html('SGD '+total_amt);
            $('#total_amt').val(total_amt);
        }
    }
    $('#amt').keyup(function() {
        amt = $('#amt').val();
        if(amt == ''){ amt = 0}
        hosting_amount = $('#hosting_amount').val();
        domain_amount = $('#domain_amount').val();
        total_amt = parseFloat(amt)+parseFloat(hosting_amount)+parseFloat(domain_amount);
        $('.summray_total_amt').html('SGD '+total_amt);
        $('#total_amt').val(total_amt);
        $('#amount').val(total_amt);
    })
    $(document).ready(function() {
        getTopUps();
        getTransactions();
        validations = $(".ajaxForm").validate();


        $('.ajaxForm').submit(function(e) {
            e.preventDefault();

            ad_id = $('#ad_id').val();
            partial_payment = $('#partial_payment').val();
            partial_payment_text = $('#partial_payment_text').val();

            var url = $(this).attr('action');
            validations = $(".ajaxForm").validate();
            if (validations.errorList.length != 0) {
                return false;
            }
            var formData = new FormData(this);
            if(ad_id > 0 && partial_payment == 1){
                Swal.fire({
                    title: "Are you sure?",
                    text: partial_payment_text,
                    type: "warning",
                    showCancelButton: !0,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes",
                    cancelButtonText: "No"
                }).then(function (t) {
                    if (t.value){
                        my_ajax(url, formData, 'post', function(res) {
                            if (res.success) {
                                intro.nextStep();
                            }
                        }, true);
                    }
                })
            }else{
                my_ajax(url, formData, 'post', function(res) {
                    if (res.success) {
                        intro.nextStep();
                    }
                }, true);
            }

        })
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
    function getTransactions() {
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
                    orderable: false,
                    searchable: false
                },

            ],
        });
    }
</script>
<script>
    $(document).ready(function() {
        $('#add_topup_btn').click(function(event) {
            var price = parseFloat($("#total_amt").val());
            var min_amt = parseFloat($("#min_amt").val());
            var ad_id = $("#ad_id").val();
            var partial_payment_text = $("#partial_payment_text").val();
            var partial_payment = $("#partial_payment").val();
            var product = 'topup';

            if (isNaN(price) || price < min_amt) {
                alert(`Please enter a minimum amount of ${min_amt}`);
                event.preventDefault();
            } else {
                if(ad_id > 0 && partial_payment == 1){
                    Swal.fire({
                        title: "Are you sure?",
                        text: partial_payment_text,
                        type: "warning",
                        showCancelButton: !0,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Yes",
                        cancelButtonText: "No"
                    }).then(function (t) {
                        if (t.value){
                            var url = "{{ route('user.stripe.checkout', ['price' => 'PLACEHOLDER_PRICE','product' => 'PLACEHOLDER_PRODUCT','ad_id' => 'ADID']) }}";
                            // Replace placeholders with actual values
                            url = url.replace('PLACEHOLDER_PRICE', encodeURIComponent(price))
                                    .replace('PLACEHOLDER_PRODUCT', encodeURIComponent(product))
                                    .replace('ADID', encodeURIComponent(ad_id));
                            window.location.replace(url);
                        }
                    })
                }else{
                    var url = "{{ route('user.stripe.checkout', ['price' => 'PLACEHOLDER_PRICE','product' => 'PLACEHOLDER_PRODUCT','ad_id' => 'ADID']) }}";
                    // Replace placeholders with actual values
                    url = url.replace('PLACEHOLDER_PRICE', encodeURIComponent(price))
                            .replace('PLACEHOLDER_PRODUCT', encodeURIComponent(product))
                            .replace('ADID', encodeURIComponent(ad_id));
                    window.location.replace(url);
                }

            }
        });
    });

    $(document).ready(function() {
        $('#add_topup_btn_paynow').click(function(event) {
            $('#paynowqrmodal').modal('show');
            return false;
            var price = parseFloat($("#amt").val());
            var min_amt = parseFloat($("#min_amt").val());
            var product = 'topup';

            if (isNaN(price) || price < min_amt) {
                alert(`Please enter a minimum amount of ${min_amt}`);
                event.preventDefault();
            } else {
                var url = "{{ route('user.paynow.checkout', ['price' => 'PLACEHOLDER_PRICE', 'product' => 'PLACEHOLDER_PRODUCT']) }}";
                url = url.replace('PLACEHOLDER_PRICE', price).replace('PLACEHOLDER_PRODUCT', product);
                window.location.replace(url);
            }
        });
    });

document.addEventListener('DOMContentLoaded', function() {
        var successMessage = document.getElementById('success-message');
        if (successMessage) {
            setTimeout(function() {
                successMessage.style.opacity = 0;
                setTimeout(function() {
                    successMessage.style.display = 'none';
                }, 500);
            }, 5000);
        }
    });
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

    document.addEventListener('DOMContentLoaded', function() {
        var successMessage = document.getElementById('success-message');
        if (successMessage) {
            setTimeout(function() {
                successMessage.style.opacity = 0;
                setTimeout(function() {
                    successMessage.style.display = 'none';
                }, 500);
            }, 5000);
        }
    });
</script>
<script>
    let successMessage = document.getElementById('success_msg');
    if (successMessage) {
        setTimeout(function() {
            successMessage.style.display = 'none';
        }, 5000);
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
        const firstWallet = `{{ $first_wallet->adds_title ?? null }}`;
        const intro = introJs();

        function goToNextStep() {
            if (intro) {
                intro.nextStep();
            }
        }

        function startTour() {
            const steps = [];
            let step1 = null;
            let step2 = null;
            let step3 = null;
            let step4 = null;

            let device = checkDevice();

            if (tourCode == 'START_1') {
                step1 = document.querySelector('.page-content');
                step2 = document.querySelector('#ads-request-submenu');
            }
            if (tourCode == 'START_3') {
                step3 = device == 'mobile' ? document.querySelector('#topupModalCard') : document.querySelector('#payment-container');
            }

            if (step1) {
                steps.push({
                    intro: `<h3 style="text-align:center">Welcome to Your Ad Journey!</h3><br><br>
                        You're about to kickstart your ads! Follow our step-by-step guide to top up your funds and get your campaign rolling.<br><br>
                        <strong>Here’s what you’ll experience:</strong><br><br>
                            1. <strong>Request Your Google Ads</strong>
                            <br><span style="padding-left: 20px;">Start by creating a campaign request. It’s quick and easy</span><br><br>
                            2. <strong>Create Your Wallet</strong>
                            <br><span style="padding-left: 20px;">Set up your wallet to manage your funds seamlessly</span><br><br>
                            3. <strong>Launch Your Ads</strong>
                            <br><span style="padding-left: 20px;">Once your wallet is funded, watch your ads go live in no time</span><br><br>
                        Get ready for a smooth and exciting advertising experience with our portal!`
                });
            }

            if (step2) {
                steps.push({
                    element: step2,
                    intro: `<strong>Click “Ads Request” to Start!</strong><br><br>
                        Submit your ad request here whenever you have a new campaign. Share your daily and monthly budget, and we’ll create a sub-wallet for easy top-ups.`,
                    position: 'right',
                });
            }

            if (step3) {
                steps.push({
                    element: step3,
                    intro: `<strong>Almost done!</strong><br><br>
                        Now that you've requested a sub-wallet for your recent project <strong>${firstWallet ?? ''}</strong>, it's time to top up your main wallet. You can then create multiple sub-wallets to diversify your ad spend across different campaigns.`,
                    position: device == 'mobile' ? 'top' : 'bottom',
                });
            }

            if (tourCode == 'AFTER_TOPUP') {
                steps.push({
                    intro: `<strong>Almost done!</strong><br><br>
                        Now that you've requested a sub-wallet for your recent project <strong>${firstWallet ?? ''}</strong>, it's time to top up your main wallet. You can then create multiple sub-wallets to diversify your ad spend across different campaigns.`,
                });
            }

            let options = {
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
                scrollToElement: false,
            }

            if (tourCode == 'START_3') {
                options.overlayOpacity = 0;
                options.disableInteraction = false;
            }

            intro.setOptions(options);

            intro.start();

            intro.onafterchange(function(targetElement) {
                if (tourCode == 'START_1') {
                    if (intro._currentStep === 1) {
                        let device = checkDevice();
                        if (device == 'mobile') {
                            $('.wrapper').addClass('toggled');
                            setTimeout(function() {
                                startTour();
                            }, 4000);
                            setTimeout(function() {
                                $('.introjs-helperLayer').css('left', '0px');
                                $('.introjs-tooltipReferenceLayer').css('left', '0px')
                                $('.customTooltipClass').css('left', '5px');
                                $('.customTooltipClass').css('top', '50px');
                            }, 500);
                        }
                    }
                }
            });

            intro.oncomplete(function() {
                sendAjaxRequest('completed');
                if (tourCode == 'START_1') {
                    setTimeout(function() {
                        window.location.href = `{{ route('user.ads.add') }}`;
                    }, 1000);
                }
                if (tourCode == 'START_3') {
                    setTimeout(function() {
                        window.location.href = `{{ route('user.wallet.transaction_report') }}`;
                    }, 1000);
                }
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
            if ($(window).width() <= 768) {
                // Mobile
                console.log('Mobile device detected');
                return 'mobile';
            } else if ($(window).width() > 768 && $(window).width() < 1024) {
                // Tablet
                console.log('Tablet device detected');
                return 'tablet';
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
            $('#wallet-sidebar-toggle').next('.mm-collapse').slideToggle(300);
            $(this).attr('aria-expanded', true);
            let device = checkDevice();

            if (tour && !clientTour) {
                if (tourCode == 'START_1') {
                    startTour();
                    $('.introjs-skipbutton').hide();
                    $('.introjs-backbutton').hide();

                    // Step 0
                    if (intro._currentStep === 0) {
                        $('.introjs-tooltip-header').hide();
                        $('.introjs-skipbutton').hide();
                        $('.introjs-nextbutton ').html('Get Started!');
                    }
                }

                if (tourCode == 'START_3') {
                    $('#topUpModal').modal({
                        backdrop: 'static',
                        keyboard: false
                    });
                    $('#topUpModal').modal('show');
                    getTransactionId();
                    if (device == 'mobile') {
                        $('#topupModalCard').css('margin-top', '225px');
                    }
                    $('.btn-close').hide();
                    setTimeout(function() {
                        startTour();
                        $('.introjs-skipbutton').hide();
                        $('.introjs-backbutton').hide();

                        // Step 0
                        if (intro._currentStep === 0) {
                            $('.introjs-nextbutton').hide();
                            $('.introjs-overlay').remove();
                            $('.introjs-helperLayer').remove();
                            $('.introjs-tooltip-title').remove();
                            $('.introjs-tooltip-header').append(`
                                <a role="button" tabindex="0" class="hide-button" style>x</a>
                            `);
                            $('.introjs-tooltipbuttons').append(`
                                <button role="button" tabindex="0" class="introjs-button hide-button">Hide</button>
                            `);
    
                            $('.hide-button').on('click', function () {
                                $('.customTooltipClass').hide();
                            })
                        }
                    }, 1000);
                }

                if (tourCode == 'AFTER_TOPUP') {
                    startTour();
                    $('.introjs-skipbutton').hide();
                    $('.introjs-backbutton').hide();
                }
            }
        });
</script>
@endpush
