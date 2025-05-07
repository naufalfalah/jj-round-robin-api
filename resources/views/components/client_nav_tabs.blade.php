<div class="">
    <div class="card-body">
        <ul class="nav nav-tabs nav-primary" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link {{$nav_tab == 'add_wallet' ? 'active' : ''}}" href="{{ route('user.wallet.add') }}" role="tab" aria-selected="true">
                    <div class="d-flex align-items-center">
                        <div class="tab-icon">
                        </div>
                        <div class="tab-title">Live account</div>
                    </div>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link {{$nav_tab == 'ads_add' ? 'active' : ''}}" href="{{ route('user.ads.add') }}" role="tab" aria-selected="false">
                    <div class="d-flex align-items-center">
                        <div class="tab-icon">
                        </div>
                        <div class="tab-title">Ads Requests</div>
                    </div>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link {{$nav_tab == 'transaction_report' ? 'active' : ''}}" href="{{ route('user.wallet.transaction_report') }}" role="tab" aria-selected="false">
                    <div class="d-flex align-items-center">
                        <div class="tab-icon">
                        </div>
                        <div class="tab-title">Transaction Report</div>
                    </div>
                </a>
            </li>

            <li class="nav-item" role="presentation">
                <a class="nav-link {{$nav_tab == 'transfer_funds' ? 'active' : ''}}" href="{{ route('user.wallet.transfer_funds') }}" role="tab" aria-selected="false">
                    <div class="d-flex align-items-center">
                        <div class="tab-icon">
                        </div>
                        <div class="tab-title">Transfer Funds</div>
                    </div>
                </a>
            </li>
        </ul>

    </div>
</div>
