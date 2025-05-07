<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>{{ @$title }} | {{ config('app.name') }}</title>
    <link rel="icon" href="{{ asset('front') }}/assets/images/favicon.png" type="image/png" />
    {{-- <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css" rel="stylesheet"> --}}
    <!--plugins-->
    <link href="{{ asset('front') }}/assets/plugins/vectormap/jquery-jvectormap-2.0.2.css" rel="stylesheet"/>
    <link href="{{ asset('front') }}/assets/plugins/simplebar/css/simplebar.css" rel="stylesheet" />
    <link href="{{ asset('front') }}/assets/plugins/select2/css/select2.min.css" rel="stylesheet" />
	<link href="{{ asset('front') }}/assets/plugins/select2/css/select2-bootstrap4.css" rel="stylesheet" />
    <link href="{{ asset('front') }}/assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css" rel="stylesheet" />
    <link href="{{ asset('front') }}/assets/plugins/metismenu/css/metisMenu.min.css" rel="stylesheet" />
    <link href="{{ asset('front') }}/assets/plugins/datatable/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <!-- Bootstrap CSS -->
    <link href="{{ asset('front') }}/assets/css/bootstrap.min.css" rel="stylesheet" />
    <link href="{{ asset('front') }}/assets/css/bootstrap-extended.css" rel="stylesheet" />
    <link href="{{ asset('front') }}/assets/css/style.css" rel="stylesheet" />
    <link href="{{ asset('front') }}/assets/css/override.css" rel="stylesheet" />
    <link href="{{ asset('front') }}/assets/css/icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <!-- loader-->
	<link href="{{ asset('front') }}/assets/css/pace.min.css" rel="stylesheet" />
    <!--Theme Styles-->
    <link href="{{ asset('front') }}/assets/css/dark-theme.css" rel="stylesheet" />
    <link href="{{ asset('front') }}/assets/css/light-theme.css" rel="stylesheet" />
    <link href="{{ asset('front') }}/assets/css/semi-dark.css" rel="stylesheet" />
    <link href="{{ asset('front') }}/assets/css/header-colors.css" rel="stylesheet" />
    <!-- Sweet Alert -->
    <link href="{{ asset('front') }}/assets/plugins/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css" />
    
    <style>
        .footer {
            position: fixed;
            left: 260px;
            right: 0;
            bottom: 0;
            padding: 0.7rem;
            color: #484444;
            background-color: #f7f8fa;
            border-top: 1px solid #e2e3e4;
            text-align: center;
            transition: .3s all;
            z-index: 10 !important;
        }

        a.back-to-top {
            z-index: 20 !important;
        }
        @media screen and (max-width: 767px) {
            div.dataTables_wrapper div.dataTables_length, div.dataTables_wrapper div.dataTables_filter, div.dataTables_wrapper div.dataTables_info, div.dataTables_wrapper div.dataTables_paginate {
                text-align: unset !important;
            }
        }
    </style>
    @stack('scripts-head')
    @yield('page-css')
    @stack('styles')
</head>

<body>
    <div id="eq-loader">
        <div class="eq-loader-div">
            <div class="eq-loading dual-loader mx-auto mb-5"></div>
        </div>
    </div>

    <!-- wrapper -->
    <div class="wrapper">
        <!--start top header-->
            @include('components.admin_navbar')
        <!--end top header-->

        <!--start sidebar -->
            @include('components.admin_sidebar')
        <!--end sidebar -->

        <!--start content-->
        <main class="page-content">

            <!-- Start BreadCrumb -->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="breadcrumb-title pe-3">{{@$breadcrumb_main}}</div>
                <div class="ps-3">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                      <li class="breadcrumb-item"><a href="{{route('admin.home')}}"><i class="bx bx-home-alt"></i></a>
                      </li>
                      <li class="breadcrumb-item active" aria-current="page">{{@$breadcrumb}}</li>
                    </ol>
                  </nav>
                </div>
            </div>
            <!-- End BreadCrumb -->
            <!--page-content -->
                @yield('content')
            <!--end page-content -->

        </main>
        <!--end page main-->

        <!--start overlay-->
        <div class="overlay nav-toggle-icon"></div>
       <!--end overlay-->

        <!--start footer-->
       <footer class="footer">
        <div class="footer-text">
           Copyright © {{ date('Y') }}. All right reserved.
        </div>
        </footer>
        <!--end footer-->

        <!--Start Back To Top Button-->
        <a href="javaScript:;" class="back-to-top"><i class='bx bxs-up-arrow-alt'></i></a>
        <!--End Back To Top Button-->

        <!--start switcher-->
        <!--end switcher-->
    </div>
    <!-- end wrapper -->

    {{-- Start logout form --}}
    <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" class="d-none">
        @csrf
    </form>
    {{-- End logout form --}}

    <!-- Bootstrap bundle JS -->
    <script src="{{ asset('front') }}/assets/js/bootstrap.bundle.min.js"></script>
    <!--plugins-->
    <script src="{{ asset('front') }}/assets/js/jquery.min.js"></script>
    <script src="{{ asset('front') }}/assets/plugins/simplebar/js/simplebar.min.js"></script>
    <script src="{{ asset('front') }}/assets/plugins/metismenu/js/metisMenu.min.js"></script>
    <script src="{{ asset('front') }}/assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js"></script>
    {{-- <script src="{{ asset('front') }}/assets/js/pace.min.js"></script> --}}
    <script src="{{ asset('front') }}/assets/plugins/datatable/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('front') }}/assets/plugins/datatable/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ asset('front') }}/assets/js/table-datatable.js"></script>
    <script src="{{ asset('front') }}/assets/plugins/select2/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    {{-- <script src="{{ asset('front') }}/assets/js/form-select2.js"></script> --}}

    <!--Sweet Alert-->
    <script src="{{ asset('front') }}/assets/plugins/sweetalert2/sweetalert2.min.js"></script>

    <!--app-->
    <script src="{{ asset('front') }}/assets/js/app.js"></script>
    {{-- <script src="{{ asset('front') }}/assets/js/index.js"></script> --}}

    <script src="https://www.gstatic.com/firebasejs/7.14.6/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/7.14.6/firebase-messaging.js"></script>
    <script>
        const firebaseMessagingSWPath = "{{ asset('firebase-messaging-sw.js') }}";
        const firebasePublicID = "{{ config('services.firebase.public_id') }}";
        const setTokenUri = "{{ route('admin.profile.save_device_token') }}";
        const ct = "{{ csrf_token() }}";
    </script>
    <script src="{{ asset('front') }}/assets/js/firebase.js"></script>

    <script>
        $(document).on('click','#notify_ancher',function(){
            $.ajax({
                type: "get",
                url: "{{ route('admin.update_notifications') }}",
                dataType: "json",
                success: function (res) {
                    $("#notification_count_badge").addClass('d-none').html('');
                }
            });
        });
    </script>

    <script src="{{ asset('front') }}/assets/js/danidev.js"></script>
    @yield('page-scripts')
    @stack('scripts')
</body>

</html>
