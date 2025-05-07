
@extends('layouts.admin')

@push('scripts')
    <script>
        let googleAccountId = null;
        let customerId = null;

        function capitalize(str) {
            return str.charAt(0).toUpperCase() + str.slice(1);
        }

        function getAds() {
            $('#ad_id').empty();

            if (googleAccountId) {
                $.ajax({
                    url: "{{ route('google_ads.ad_group_ad') }}",
                    method: 'GET',
                    data: {
                        google_account_id: googleAccountId,
                        customer_id: customerId,
                    },
                    success: function(data) {
                        $('#ad_id').append(`<option value="" selected>Select an google ads ad</option>`);
                        if (data.results) {
                            for (const item of data.results) {
                                $('#ad_id').append(`
                                    <option value="${item.adGroupAd.resourceName}"">
                                        ${item.campaign.name} - ${item.adGroupAd.resourceName}
                                    </option>
                                `);
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
            }
        }

        $(document).ready(function() {
            $('#ad_request_id').on('change', function() {
                const selectedOption = $(this).find(':selected');
                googleAccountId = selectedOption.data('google-account-id');
                customerId = selectedOption.data('customer-id');
                getAds();
            });
        });
    </script>
@endpush

@section('content')
    <div class="card radius-15">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12 border-right">
                    <div class="d-md-flex align-items-center">
                        <div class="flex-grow-1">
                            <h5 class="mb-2">Google Ads Ad Group Ad</h5>
                        </div>
                        <div>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#syncAdModal">Sync Ad</button>
                        </div>
                    </div>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success my-2" role="alert">
                    {{ session('success') }}
                </div>
            @elseif (session('error'))
                <div class="alert alert-danger my-2" role="alert">
                    {{ session('error') }}
                </div>
            @endif
            
            <div class="tab-content mt-3" id="page-1">
                <div class="tab-pane fade show active" id="Edit-Profile">
                    <div class="card shadow-none border mb-0 radius-15">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="low_bls-template-table" class="table table-striped table-bordered" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase ps-2 text-secondary text-xxs font-weight-bolder opacity-7">
                                                Ad Request Title</th>
                                            <th class="text-uppercase ps-2 text-secondary text-xxs font-weight-bolder opacity-7">
                                                Resource Name</th>
                                            <th class="text-uppercase ps-2 text-secondary text-xxs font-weight-bolder opacity-7">
                                                Campaign</th>
                                            <th class="text-uppercase ps-2 text-secondary text-xxs font-weight-bolder opacity-7">
                                                Ad Group</th>
                                            <th class="text-uppercase ps-2 text-secondary text-xxs font-weight-bolder opacity-7">
                                                Status</th>
                                            <th class="text-uppercase ps-2 text-secondary text-xxs font-weight-bolder opacity-7">
                                                Google Account</th>
                                            <th class="text-uppercase ps-2 text-secondary text-xxs font-weight-bolder opacity-7">
                                                Customer ID</th>
                                        </tr>
                                    </thead>
                                    <tbody id="ad_group_ad-table-body">
                                        @foreach ($ad_group_ads as $ad_group_ad)
                                            <tr>
                                                <td>{{ $ad_group_ad['ad_request'] }}</td>
                                                <td>{{ $ad_group_ad['adGroupAd']['ad']['resourceName'] ?? '-' }}</td>
                                                <td>{{ $ad_group_ad['campaign']['name'] }}</td>
                                                <td>{{ $ad_group_ad['adGroup']['name'] }}</td>
                                                <td>
                                                    <span class="badge {{ $ad_group_ad['adGroupAd']['status'] === 'ENABLED' ? 'bg-info text-dark' : 'bg-secondary text-white' }}">
                                                        {{ $ad_group_ad['adGroupAd']['status'] }}
                                                    </span>
                                                </td>
                                                <td>{{ $ad_group_ad['google_account'] }}</td>
                                                <td>{{ $ad_group_ad['customer_id'] }}</td>
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

     <!-- Modal Sync Ad -->
     <div class="modal fade" id="syncAdModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Sync Google Ads Ad</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.sub_account.client-management.google_ads.sync',  ['sub_account_id' => $sub_account_id]) }}" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group mt-2">
                            <label for="ad_request_id">Ads Request</label>
                            <select class="form-select" name="ad_request_id" id="ad_request_id" required>
                                <option value="" selected>Select an ad request</option>
                                @foreach ($ad_requests as $ad_request)
                                    <option value="{{ $ad_request->id }}" 
                                        data-google-account-id="{{ $ad_request->google_account_id }}" 
                                        data-customer-id="{{ $ad_request->customer_id }}"
                                        {{ old('ad_request_id') == $ad_request->id ? 'selected' : '' }}>
                                        {{ $ad_request->adds_title }} - {{ $ad_request->customer_id }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mt-2">
                            <label for="ad_id">Ads Request</label>
                            <select class="form-select" name="ad_id" id="ad_id" required>
                                <option value="" selected>Select an google ads ad</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Sync</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection