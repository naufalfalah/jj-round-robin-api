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

        {{-- <li>
            <a href="{{ route('user.dashboard') }}">
                <div class="parent-icon"><i class="bi bi-house-fill"></i>
                </div>
                <div class="menu-title">Dashboard</div>
            </a>
        </li> --}}

        <!-- <li class="{{ request()->routeIs(['user.leads-management.leads']) ? 'mm-active' : '' }}">
            <a href="{{ route('user.leads-management.leads') }}">
                <div class="parent-icon"><i class="fa-solid fa-people-group"></i>
                </div>
                <div class="menu-title">Leads</div>
            </a>
        </li> -->

        {{-- <li>
            <a href="javascript:void(0);" class="has-arrow">
                <div class="parent-icon"><i class="fa-solid fa-wallet"></i></i>
                </div>
                <div class="menu-title">Wallets</div>
            </a>

            <ul>
                <li> <a href="{{ route('user.wallet.add') }}"><i class="fa-solid fa-circle"></i>Main Wallet</a></li>

                <li> <a href="{{ route('user.wallet.sub_wallets') }}"><i class="fa-solid fa-circle"></i>Sub Wallets</a></li>

            </ul>


        </li> --}}

        {{-- <li class="{{ request()->routeIs(['user.ads.all', 'user.ads.add', 'user.ads.edit']) ? 'mm-active' : '' }}">
            <a href="{{ route('user.ads.all') }}">
                <div class="parent-icon"><i class="fa-solid fa-rectangle-ad"></i>
                </div>
                <div class="menu-title">Ads Requests</div>
            </a>
        </li> --}}

<!-- <li>
            <a href="{{ route('user.report.view') }}">
                <div class="parent-icon"><i class="fa-solid fa-receipt"></i>
                </div>
                <div class="menu-title">Report</div>
            </a>
        </li>  -->

        <li>
            <a href="{{ route('user.dashboard') }}">
                <div class="parent-icon"><i class="bi bi-house-fill"></i>
                </div>
                <div class="menu-title">Dashboard</div>
            </a>
        </li>

        <li id="wallet-sidebar">
            <a href="javascript:void(0);" class="has-arrow" id="wallet-sidebar-toggle">
                <div class="parent-icon"><i class="fa-solid fa-wallet"></i></i>
                </div>
                <div class="menu-title">Wallet</div>
            </a>
            <ul>
                <li id="live-account-submenu">
                    <a href="{{ route('user.wallet.add') }}">
                        <i class="fa-solid fa-circle"></i>Live Account
                    </a>
                </li>
                <li id="ads-request-submenu">
                    <a href="{{ route('user.ads.add') }}">
                        <i class="fa-solid fa-circle"></i>Ads Request
                    </a>
                </li>
                <li>
                    <a href="{{ route('user.ads.all') }}">
                        <i class="fa-solid fa-circle"></i>Ads Status Request
                    </a>
                </li>
                <li>
                    <a href="{{ route('user.wallet.transaction_report') }}">
                        <i class="fa-solid fa-circle"></i>Transaction Reports
                    </a>
                </li>
                <li>
                    <a href="{{ route('user.wallet.transfer_funds') }}">
                        <i class="fa-solid fa-circle"></i>Transfer Funds
                    </a>
                </li>

            </ul>
        </li>


        <li>
            <a href="{{ route('user.google-ads-report.index') }}">
                <div class="parent-icon"><i class="fa-brands fa-google"></i>
                </div>
                <div class="menu-title">Google Report</div>
            </a>
        </li>

        <li >
        <img src="{{ asset('front') }}/assets/images/google.jpg" alt="" style=" width: 250px;">
        </li>    
    </ul>
   
    <!--end navigation-->
</aside>
<!--end sidebar -->
