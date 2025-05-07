
@extends('layouts.admin')

@section('content')
    <div class="card radius-15">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 col-lg-6 border-right">
                    <div class="d-md-flex align-items-center">
                        <div class="ms-md-6 flex-grow-1">
                            <h5 class="mb-2">Google Ads Campaign</h5>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-content mt-3" id="page-1">
                <div class="tab-pane fade show active" id="Edit-Profile">
                    <div class="card shadow-none border mb-0 radius-15">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="low_bls-template-table" class="table table-striped table-bordered" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase ps-2 text-secondary text-xxs font-weight-bolder opacity-7">
                                                Name</th>
                                            <th class="text-uppercase ps-2 text-secondary text-xxs font-weight-bolder opacity-7">
                                                Status</th>
                                            <th class="text-uppercase ps-2 text-secondary text-xxs font-weight-bolder opacity-7">
                                                Start Date</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                End Date</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="campaign-table-body">
                                        @foreach ($campaigns as $campaign)
                                            <tr>
                                                <td>{{ $campaign['campaign']['name'] }}</td>
                                                <td>
                                                    <span class="badge {{ $campaign['campaign']['status'] === 'ENABLED' ? 'bg-info text-dark' : 'bg-secondary text-white' }}">
                                                        {{ $campaign['campaign']['status'] }}
                                                    </span>
                                                </td>
                                                <td>{{ $campaign['campaign']['startDate'] }}</td>
                                                <td>{{ $campaign['campaign']['endDate'] }}</td>
                                                <td>
                                                    <div class="table-actions d-flex align-items-center gap-3 fs-6">
                                                        <a href="{{ route('admin.sub_account.client-management.google_ads_campaign.edit', ['sub_account_id' => $sub_account_id]) }}?google_account_id={{ $campaign['google_account_id'] }}&customer_id={{ $campaign['customer_id'] }}&campaign_resource_name={{ $campaign['campaign']['resourceName'] }}" class="text-warning" data-bs-toggle="tooltip" data-bs-placement="bottom" aria-label="Edit"><i class="bi bi-pencil-fill"></i></a>
                                                    </div>
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
        </div>
    </div>
@endsection
