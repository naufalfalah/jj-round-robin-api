@php
    $round_robin = App\Models\SubAccount::WhereNotNull('sub_account_url')->get();
    $sub_account_id = session()->get('sub_account_id') ?? "";
    if($round_robin->isEmpty()) {
        Session()->forget('sub_account_id');
        $sub_account_id = "";
    }
@endphp

<!--start sidebar -->
<aside class="sidebar-wrapper" data-simplebar="true">
    <div class="sidebar-header">
        <div>
            <a href="{{ url('/') }}">
                <img src="{{ asset('front') }}/assets/images/logo.png" class="logo-icon" alt="logo icon">
            </a>
        </div>
    </div>
    <!--navigation-->
    <ul class="metismenu" id="menu">
        @if ($round_robin->count() > 0)

            <li>
                <a href="javascript:void();" href="{{ route('admin.home') }}" class="has-arrow">
                    <div class="parent-icon"><i class="fa-solid fa-users"></i></i>
                    </div>
                    <div class="menu-title">Sub Account</div>
                </a>
                <ul>
                    @foreach ($round_robin as $item)
                        <li> <a href="{{ route('admin.sub_account.advertisements.running_ads', ['sub_account_id' => $item->hashid ]) }}" class="d-flex align-items-start"><i class="fa-solid fa-circle mt-1"></i>{{$item->sub_account_name }}</a></li>
                    @endforeach
                </ul>

            </li>
        @else
            <li class="{{ request()->routeIs('admin.home') ? 'mm-active' : '' }}">
                <a href="{{ route('admin.home') }}">
                    <div class="parent-icon"><i class="fa-solid fa-users"></i></i>
                    </div>
                    <div class="menu-title">Sub Account</div>
                </a>
            </li>
        @endif

        @if ($auth_user->can('lead-frequency-read'))

            @if (isset($sub_account_id) && !empty($sub_account_id))
                <!-- <li
                    class="{{ request()->routeIs('admin.sub_account.advertisements.running_ads',['sub_account_id' => $sub_account_id ]) ? 'mm-active' : '' }}">
                    <a href="{{ route('admin.sub_account.advertisements.running_ads',['sub_account_id' => $sub_account_id ]) }}">
                        <div class="parent-icon"><i class="fa-solid fa-people-group"></i></i>
                        </div>
                        <div class="menu-title">Lead Frequency</div>
                    </a>
                </li> -->
            @endif

        @endif




        @if ($auth_user->user_type == 'admin' && $auth_user->role_name == 'super_admin')

            @if (isset($sub_account_id) && !empty($sub_account_id))
                <li>
                    <a href="javascript:voud(0);" class="has-arrow">
                        <div class="parent-icon"><i class="fa-solid fa-users"></i>
                        </div>
                        <div class="menu-title">Client Management</div>
                    </a>
                    <ul>
                        {{-- <li>
                            <a href="{{ route('admin.sub_account.client-management.add', ['sub_account_id' => $sub_account_id ]) }}"><i class="fa-solid fa-circle"></i>
                                Add Client</a>
                        </li> --}}
                        {{-- <li>
                            <a href="{{ route('admin.sub_account.client-management.all', ['sub_account_id' => $sub_account_id ]) }}"><i class="fa-solid fa-circle"></i>
                                All Client</a>
                        </li> --}}
                        {{-- <li> 
                            <a href="{{ route('admin.sub_account.client-management.all_clients', ['sub_account_id' => $sub_account_id ]) }}"><i class="fa-solid fa-circle"></i>
                                Import Client</a>
                        </li> --}}
                        {{-- <li>
                            <a href="{{ route('admin.sub_account.client-management.top_up', ['sub_account_id' => $sub_account_id ]) }}"><i
                                    class="fa-solid fa-circle"></i>TopUp</a>
                        </li> --}}
                        <li> 
                            <a href="{{ route('admin.sub_account.client-management.edit', ['sub_account_id' => $sub_account_id ]) }}">
                                <i class="fa-solid fa-circle"></i>Edit Client
                            </a>
                        </li>
                        <li> 
                            <a href="{{ route('admin.sub_account.client-management.google_ads_campaign', ['sub_account_id' => $sub_account_id ]) }}">
                                <i class="fa-solid fa-circle"></i>All Campaigns
                            </a>
                        </li>
                        <li> 
                            <a href="{{ route('admin.sub_account.client-management.google_ads_ad_group', ['sub_account_id' => $sub_account_id ]) }}">
                                <i class="fa-solid fa-circle"></i>All Ad Groups
                            </a>
                        </li>
                        <li> 
                            <a href="{{ route('admin.sub_account.client-management.google_ads_ad_group_ad', ['sub_account_id' => $sub_account_id ]) }}">
                                <i class="fa-solid fa-circle"></i>All Ads
                            </a>
                        </li>
                        <li> 
                            <a href="{{ route('admin.sub_account.client-management.google_ads.create', ['sub_account_id' => $sub_account_id ]) }}">
                                <i class="fa-solid fa-circle"></i>Create Google Ads
                            </a>
                        </li>
                        <li> 
                            <a href="{{ route('admin.sub_account.client-management.google_ads_conversion_action', ['sub_account_id' => $sub_account_id ]) }}">
                                <i class="fa-solid fa-circle"></i>All Conversion Goals
                            </a>
                        </li>
                        <li> 
                            <a href="{{ route('admin.sub_account.client-management.google_ads_conversion_action.create', ['sub_account_id' => $sub_account_id ]) }}">
                                <i class="fa-solid fa-circle"></i>Create Conversion Goal
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('admin.sub_account.google-ads-report.index', ['sub_account_id' => $sub_account_id ]) }}">
                                <i class="fa-brands fa-google"></i>Google Ads Report
                            </a>
                        </li>
                    </ul>
                </li>
            @endif

        @endif

        {{-- @if ($auth_user->can('user-management-read'))
            <li>
                <a href="javascript:;" class="has-arrow">
                    <div class="parent-icon"><i class="fa-solid fa-user"></i>
                    </div>
                    <div class="menu-title">User Management</div>
                </a>
                <ul>
                    @if ($auth_user->can('user-management-write'))
                        <li> <a href="{{ route('admin.user-management.add-user') }}"><i
                                    class="fa-solid fa-circle"></i>Add Users</a></li>
                    @endif
                    <li> <a href="{{ route('admin.user-management.view') }}"><i class="fa-solid fa-circle"></i>All
                            Users</a></li>
                </ul>
            </li>
        @endif

        @if ($auth_user->user_type == 'admin' && $auth_user->role_name == 'super_admin')
            <li class="{{ request()->routeIs(['admin.role.all', 'admin.role.add', 'admin.role.edit']) ? 'mm-active' : '' }}">
                <a href="{{ route('admin.role.all') }}">
                    <div class="parent-icon"><i class="fa-solid fa-drum-steelpan"></i></div>
                    <div class="menu-title">Role Management</div>
                </a>
            </li>
            <li>
                <a href="javascript:;" class="has-arrow">
                    <div class="parent-icon"><i class="fa-solid fa-drum-steelpan"></i>
                    </div>
                    <div class="menu-title">Role Management</div>
                </a>
                <ul>
                    <li> <a href="{{ route('admin.permission_type.permission_type') }}"><i
                                class="fa-solid fa-circle"></i>Permission Type</a></li>
                    <li> <a href="{{ route('admin.permission.permission') }}"><i
                                class="fa-solid fa-circle"></i>Permission</a></li>
                    <li> <a href="{{ route('admin.role.all') }}"><i class="fa-solid fa-circle"></i>Role</a></li>
                </ul>
            </li>
        @endif --}}

        @if ($auth_user->user_type == 'admin' && $auth_user->role_name == 'super_admin')
            <li>
                <a href="javascript:voud(0);" class="has-arrow">
                    <div class="parent-icon"><i class="fa-solid fa-gears"></i>
                    </div>
                    <div class="menu-title">Settings</div>
                </a>
                <ul>
                    <li> <a href="{{ route('admin.setting.google_account') }}"><i class="fa-solid fa-circle"></i>Google
                            Account</a></li>
                    <li> <a href="{{ route('admin.setting.taxes') }}"><i class="fa-solid fa-circle"></i>Taxes & Vat
                            Charges / Topup Setting</a></li>
                    <li> <a href="{{ route('admin.setting.whatsapp_temp') }}"><i class="fa-solid fa-circle"></i>Whatsapp Message Template</a></li>
                </ul>
            </li>
        @endif

        @if ($auth_user->can('agencies-read'))
            <li>
                <a href="{{ route('admin.agency.all') }}">
                    <div class="parent-icon"><i class="fa-solid fa-building"></i></div>
                    <div class="menu-title">Agencies</div>
                </a>
            </li>
        @endif

    </ul>
    <!--end navigation-->
</aside>
<!--end sidebar -->
