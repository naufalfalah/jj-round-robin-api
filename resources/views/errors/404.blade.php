
@extends('errors::minimal')
@section('title', 'Error 404')
<div class="error-404 d-flex align-items-center justify-content-center mt-5">
    <div class="container mt-5">
      <div class="card py-5 radius-15">
        <div class="row g-0">
          <div class="col col-xl-5">
            <div class="card-body p-4">
              <h1 class="display-1"><span class="text-danger">4</span><span class="text-primary">0</span><span class="text-success">4</span></h1>
              <h2 class="font-weight-bold display-4">Lost in Space</h2>
              <p>You have reached the edge of the universe.
                <br>The page you requested could not be found.
                <br>Dont'worry and return to the previous page.</p>
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
            <img src="{{ asset('front') }}/assets/images/error/404-error.png" class="img-fluid" alt="">
          </div>
        </div>
        <!--end row-->
      </div>
    </div>
</div>
