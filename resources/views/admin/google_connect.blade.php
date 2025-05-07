@extends('layouts.admin')

@section('content')

    <div class="card radius-15">
        <div class="card-body">
            <div class="table-responsive">
                <table id="low_bls-template-table" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th class="text-uppercase ps-2 text-secondary text-xxs font-weight-bolder opacity-7">
                                Name</th>
                            <th class="text-uppercase ps-2 text-secondary text-xxs font-weight-bolder opacity-7">
                                Email</th>
                            <th class="text-uppercase ps-2 text-secondary text-xxs font-weight-bolder opacity-7">
                                Access Token</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                Add Date</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                Action</th>
                        </tr>
                    </thead>
                    <tbody id="campaign-table-body">
                        @foreach ($google_accounts as $google_account)
                            <tr>
                                <td>{{ $google_account->name }}</td>
                                <td>{{ $google_account->email }}</td>
                                <td class="text-wrap text-break">{{ json_decode($google_account->access_token, true)['access_token'] }}</td>
                                <td>{{ \Carbon\Carbon::parse($google_account->created_at)->format('Y-m-d') }}</td>
                                <td>
                                    <div class="table-actions d-flex align-items-center gap-3 fs-6">
                                        <a href={{ route('admin.setting.refresh_token', ['id' => $google_account->id]) }} class="btn btn-primary px-5">Refresh Token</a>
                                        <button type="button" class="btn btn-danger px-5" disabled>Delete Account</button>
                                    </div>    
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- @if (isset(auth('admin')->user()->provider_id) && !empty(auth('admin')->user()->provider_id))
        <div class="card radius-15">
            <div class="card-body">
                <div class="row p-5">
                    <div class="col-12 col-lg-12 border-right">
                        <div class="text-center">
                            <a class="btn btn-dark border-dark radius-30" href="{{ route('admin.setting.disconnect') }}" id="disconnectButton">
                                <span class="d-flex justify-content-center align-items-center">
                                    <img class="me-2" src="{{ asset('front') }}/assets/images/icons/search.svg" width="16"
                                        alt="">
                                    <span>Google Account Connected</span>
                                </span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else --}}
        <div class="card radius-15">
            <div class="card-body">
                <div class="row p-lg-5">
                    <div class="col-12 col-lg-12 border-right">
                        <div class="text-center">
                            <a class="btn btn-white border-dark radius-30" href="{{ route('admin.setting.connect') }}">
                                <span class="d-flex justify-content-center align-items-center">
                                    <img class="me-2" src="{{ asset('front') }}/assets/images/icons/search.svg" width="16"
                                        alt="">
                                    <span>Connect Google Account</span>
                                </span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    {{-- @endif --}}
@endsection

@section('page-scripts')
    <script>
        $(document).ready(function() {
            $('#disconnectButton').click(function(event) {
                event.preventDefault();
                if (confirm('Are you sure you want to disconnect your Google account?')) {
                    window.location.href = $(this).attr('href');
                }
            });
        });

        $('#ajaxForm').submit(function(e) {
            e.preventDefault();
            var url = $(this).attr('action');
            var param = new FormData(this);
            my_ajax(url, param, 'post', function(res) {

            },true);
        });
    </script>
@endsection
