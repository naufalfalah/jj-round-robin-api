@extends('layouts.admin')

@push('styles')
    <style>
        .border-left-2 {
            border-left: solid black 2px;
        }

        .border-left-1 {
            border-left: solid black 1px;
        }
    </style>
@endpush

@section('content')
    <div class="card radius-15">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 col-lg-6 border-right">
                    <div class="d-md-flex align-items-center">
                        <div class="ms-md-6 flex-grow-1">
                            <div class="mb-1" style=" margin-left: 10px; ">
                                <h5 class="mb-0">Edit Google Ads Campaign</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <form action="{{ route('admin.sub_account.client-management.google_ads_campaign.update', ['sub_account_id' => $sub_account_id, 'google_account_id' => $google_account_id, 'customer_id' => $customer_id, 'campaign_resource_name' => $campaign_resource_name, 'campaign_budget_resource_name' => $campaign['campaignBudget']['resourceName']]) }}" method="post" class="">
                @csrf
                @method('put')
                <div class="tab-content mt-3" id="page-1">
                    <div class="tab-pane fade show active" id="Edit-Profile">
                        <div class="card shadow-none border mb-0 radius-15">
                            <div class="card-body">
                                @if (session('success'))
                                    <div class="alert alert-success" role="alert">
                                        {{ session('success') }}
                                    </div>
                                @elseif (session('error'))
                                    <div class="alert alert-danger" role="alert">
                                        {{ session('error') }}
                                    </div>
                                @endif

                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <div class="form-body">
                                    <h3 class="mt-4 mb-2">Configuration</h3>
                                    <div class="row mb-2">
                                        <div class="col-6">
                                            <div class="form-group mb-2">
                                                <label for="objective" class="form-label">Objective</label>
                                                <select class="form-select" name="objective" disabled>
                                                    <option value="LEADS">Leads</option>
                                                </select>
                                                @error('objective')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-6">
                                            <div class="form-group mb-2">
                                                <label for="campaign_name" class="form-label">Campaign Name
                                                    <span class="text-danger fw-bold">*</span></label>
                                                <input type="text" name="campaign_name" id="campaign_name" placeholder="Campaign Name"
                                                    class="form-control" value="{{ old('campaign_name', $campaign['campaign']['name']) }}" required>
                                                @error('campaign_name')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group mb-2">
                                                <label for="campaign_status" class="form-label">Status
                                                    <span class="text-danger fw-bold">*</span></label>
                                                <select class="form-select" name="campaign_status">
                                                    <option value="ENABLED" {{ old('campaign_status', $campaign['campaign']['status']) == 'ENABLED' ? 'selected' : '' }}>Enabled</option>
                                                    <option value="PAUSED" {{ old('campaign_status', $campaign['campaign']['status']) == 'PAUSED' ? 'selected' : '' }}>Paused</option>
                                                    <option value="REMOVED" {{ old('campaign_status', $campaign['campaign']['status']) == 'REMOVED' ? 'selected' : '' }}>Removed</option>
                                                </select>
                                                @error('campaign_status')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-6">
                                            <div class="form-group mb-2">
                                                <label for="campaign_type" class="form-label">Campaign Type
                                                    <span class="text-danger fw-bold">*</span></label>
                                                <select class="form-select" name="campaign_type" disabled>
                                                    <option value="SEARCH" {{ old('campaign_type', $campaign['campaign']['advertisingChannelType']) == 'SEARCH' ? 'selected' : '' }}>Search</option>
                                                    <option value="PERFORMANCE_MAX" {{ old('campaign_type', $campaign['campaign']['advertisingChannelType']) == 'PERFORMANCE_MAX' ? 'selected' : '' }}>Performance Max</option>
                                                </select>
                                                @error('campaign_type')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <hr>
                                    <h3 class="mt-4 mb-2">Budget</h3>
                                    <div class="row mb-2">
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="campaign_budget_amount" class="form-label">Amount
                                                    <span class="text-danger fw-bold">*</span>
                                                </label>
                                                @php
                                                    $campaignBudget = 0;
                                                    if (isset($campaign['campaignBudget']['amountMicros'])) {
                                                        $campaignBudget = (int) $campaign['campaignBudget']['amountMicros'] / 1000000;
                                                    }
                                                @endphp
                                                <input type="number" name="campaign_budget_amount" id="campaign_budget_amount" placeholder="Min 1.00$ SGD"
                                                    class="form-control" value="{{ old('campaign_budget_amount', $campaignBudget) }}" required>
                                                @error('campaign_budget_amount')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <x-client_management.google_ads.bid_cap />
                                    <div class="row mb-2">
                                        <b class="mt-4 mb-2">Campaign duration</b>
                                        <div class="col-4">
                                            <div class="form-group mb2">
                                                <div class="row custom-duration-form">
                                                    <div class="col-6">
                                                        <label for="campaign_start_date" class="form-label">Start
                                                            <span class="text-danger fw-bold">*</span>
                                                        </label>
                                                        <input type="date" name="campaign_start_date" id="campaign_start_date" 
                                                            value="{{ old('campaign_start_date', $campaign['campaign']['startDate']) }}" placeholder="" class="form-control">
                                                    </div>
                                                    @error('campaign_start_date')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                    <div class="col-6">
                                                        <label for="campaign_end_date" class="form-label">End
                                                            <span class="text-danger fw-bold">*</span>
                                                        </label>
                                                        <input type="date" name="campaign_end_date" id="campaign_end_date"
                                                            value="{{ old('campaign_end_date', $campaign['campaign']['endDate']) }}" placeholder="" class="form-control">
                                                    </div>
                                                    @error('campaign_end_date')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-4 mb-2">
                                        <div class="col-12 d-flex justify-content-end">
                                            <button type="submit" class="btn btn-dark" id="button-submit">Submit</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection