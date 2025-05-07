@extends('errors::minimal')
<div class="wrapper">
    <div class="error-404 d-flex align-items-center justify-content-center mt-5">
        <div class="container mt-5">
            <div class="card radius-15 shadow-none">
                <div class="row g-0">
                    <div class="col-lg-6">
                        <div class="card-body p-5">
                            <h1 class="display-1"><strong>403</strong></h1>
                            <h2 class="font-weight-bold display-4"><strong>Forbidden</strong></h2>
                        <p>you don’t have permission to access this resource.
                            <div class="mt-5">
                                @if (auth('admin')->check())
                                    <a href="{{ route('admin.home') }}" class="btn btn-primary btn-lg px-md-5 radius-30">Go Dashboard</a>
                                @else
                                    <a href="{{ route('user.dashboard') }}" class="btn btn-primary btn-lg px-md-5 radius-30">Go Dashboard</a>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <img src="{{asset('front')}}/assets/images/error/auth-img-7.png" class="card-img" alt="">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
