{{-- @extends('errors::minimal')

@section('title', __('Page Expired'))
@section('code', '419')
@section('message', __('Page Expired')) --}}
@extends('errors::minimal')
<div class="wrapper">
    <div class="error-404 d-flex align-items-center justify-content-center">
        <div class="container">
            <div class="card radius-15 shadow-none">
                <div class="row g-0">
                    <div class="col-lg-12 mx-auto text-center">
                        <div class="card-body p-5">
                            <h1 class="display-1">419</h1>
                            <h2 class="font-weight-bold display-4">Page Expired!</h2>
                        {{-- <p>you don’t have permission to access this resource. --}}
                            <div class="mt-5">
                                @if (auth('admin')->check())
                                <a href="{{route('admin.home')}}" class="btn btn-lg btn-info px-md-5 radius-30">Dashboard</a>
                                @else
                                <a href="{{route('auth.login')}}" class="btn btn-lg btn-info px-md-5 radius-30">Dashboard</a>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        {{-- <img src="{{asset('admin')}}/assets/images/errors-images/403 Error Forbidden-bro.png" class="card-img" alt=""> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
