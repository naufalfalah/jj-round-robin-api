@extends('errors::minimal')
@section('title', 'Error 500')
<div class="error-404 d-flex align-items-center justify-content-center">
    <div class="container mt-5">
      <div class="card py-5">
        <div class="row g-0">
          <div class="col-xl-5">
            <div class="card-body p-4">
              <h1 class="display-1"><span class="text-warning">5</span><span class="text-danger">0</span><span class="text-primary">0</span></h1>
              <h2 class="font-weight-bold display-4">Sorry, unexpected error</h2>
              <p>Looks like you are lost!
                <br>May be you are not connected to the internet!</p>
              <div class="mt-5">
                @if (auth('admin')->check())
                    <a href="{{ route('admin.home') }}" class="btn btn-primary btn-lg px-md-5 radius-30">Go Dashboard</a>
                @else
                    <a href="{{ route('user.dashboard') }}" class="btn btn-primary btn-lg px-md-5 radius-30">Go Dashboard</a>
                @endif
              </div>
            </div>
          </div>
          <div class="col-xl-7">
            <img src="{{ asset('front') }}/assets/imag  es/error/505-error.png" class="img-fluid" alt="">
          </div>
        </div>
        <!--end row-->
      </div>
    </div>
</div>
