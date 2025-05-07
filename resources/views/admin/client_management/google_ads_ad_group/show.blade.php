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

            @if (!auth()->user()->google_access_token)
                <div class="alert border-0 border-danger border-start border-4 bg-light-danger alert-dismissible fade show py-2 my-2" id="act_expire_alert">
                    <div class="d-flex align-items-center">
                        <div class="fs-3 text-danger">
                            <i class="bi bi-x-circle-fill"></i>
                        </div>
                        <div class="ms-3">
                            <div class="text-danger">Google Ads account not connected.</div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="tab-content mt-3" id="page-1">
                <div class="tab-pane fade show active" id="Edit-Profile">
                    <div class="card shadow-none border mb-0 radius-15">
                        <div class="card-body">
                            <div class="form-body">
                                <div class="row mb-2">
                                    <div class="col-xl-6">
                                        <div class="form-group mb-2">
                                            <label for="ad_group" class="form-label">Ad Group Name</label>
                                            <input type="text" name="ad_group" id="ad_group"
                                                class="form-control" value="{{ $ad_group['adGroup']['name'] }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-xl-6">
                                        <div class="form-group mb-2">
                                            <label for="ad_group" class="form-label">Status</label>
                                            <select class="form-select" name="ad_group" readonly="readonly" style="pointer-events: none;">
                                                <option value="ENABLED" {{ $ad_group['adGroup']['status'] }}>Enabled</option>
                                                <option value="PAUSED" {{ $ad_group['adGroup']['status'] }}>Paused</option>
                                                <option value="REMOVED" {{ $ad_group['adGroup']['status'] }}>Removed</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xl-6">
                                        <div class="form-group mb-2">
                                            <label for="campaign_name" class="form-label">Campaign Name</label>
                                            <input type="text" name="campaign_name" id="campaign_name" placeholder="Campaign Name"
                                                class="form-control" value="{{ $ad_group['campaign']['name'] }}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection