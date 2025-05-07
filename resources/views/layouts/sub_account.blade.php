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
    </style>
    @yield('page-css')
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
            @include('components.sub_account_sidebar')
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

    <!-- Send Notification Modal -->

    {{-- <div class="modal fade" id="notificationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="form-request-title">Send Notification</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('user.file_manager.save_file') }}" method="post" class="ajaxFolderFile" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <label class="form-label">Select Send To<span class="text-danger fw-bold">*</span></label>
                                <select name="send_to" id="notificationSendTo" class="form-select" required>
                                    <option value="">Select Notification Send To</option>
                                    <option value="all_users">All Users</option>
                                    <option value="siingle_multiple_users">Single or Mutiple Users</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <label class="form-label">Select Send To<span class="text-danger fw-bold">*</span></label>
                                <select class="multiple-select" data-placeholder="Choose anything" multiple="multiple">
                                    <option value="United States" selected>United States</option>
                                    <option value="United Kingdom" selected>United Kingdom</option>
                                    <option value="Afghanistan" selected>Afghanistan</option>
                                    <option value="Aland Islands">Aland Islands</option>
                                    <option value="Albania">Albania</option>
                                    <option value="Algeria">Algeria</option>
                                    <option value="American Samoa">American Samoa</option>
                                    <option value="Andorra">Andorra</option>
                                    <option value="Angola">Angola</option>
                                    <option value="Anguilla">Anguilla</option>
                                    <option value="Antarctica">Antarctica</option>
                                    <option value="Antigua and Barbuda">Antigua and Barbuda</option>
                                    <option value="Argentina">Argentina</option>
                                    <option value="Armenia">Armenia</option>
                                    <option value="Aruba">Aruba</option>
                                    <option value="Australia">Australia</option>
                                    <option value="Austria">Austria</option>
                                    <option value="Azerbaijan">Azerbaijan</option>
                                    <option value="Bahamas">Bahamas</option>
                                    <option value="Bahrain">Bahrain</option>
                                    <option value="Bangladesh">Bangladesh</option>
                                    <option value="Barbados">Barbados</option>
                                    <option value="Belarus">Belarus</option>
                                    <option value="Belgium">Belgium</option>
                                    <option value="Belize">Belize</option>
                                    <option value="Benin">Benin</option>
                                    <option value="Bermuda">Bermuda</option>
                                    <option value="Bhutan">Bhutan</option>
                                    <option value="Bolivia, Plurinational State of">Bolivia, Plurinational State of</option>
                                    <option value="Bonaire, Sint Eustatius and Saba">Bonaire, Sint Eustatius and Saba</option>
                                    <option value="Bosnia and Herzegovina">Bosnia and Herzegovina</option>
                                    <option value="Botswana">Botswana</option>
                                    <option value="Bouvet Island">Bouvet Island</option>
                                    <option value="Brazil">Brazil</option>
                                    <option value="British Indian Ocean Territory">British Indian Ocean Territory</option>
                                    <option value="Brunei Darussalam">Brunei Darussalam</option>
                                    <option value="Bulgaria">Bulgaria</option>
                                    <option value="Burkina Faso">Burkina Faso</option>
                                    <option value="Burundi">Burundi</option>
                                    <option value="Cambodia">Cambodia</option>
                                    <option value="Cameroon">Cameroon</option>
                                    <option value="Canada">Canada</option>
                                    <option value="Cape Verde">Cape Verde</option>
                                    <option value="Cayman Islands">Cayman Islands</option>
                                    <option value="Central African Republic">Central African Republic</option>
                                    <option value="Chad">Chad</option>
                                    <option value="Chile">Chile</option>
                                    <option value="China">China</option>
                                    <option value="Christmas Island">Christmas Island</option>
                                    <option value="Cocos (Keeling) Islands">Cocos (Keeling) Islands</option>
                                    <option value="Colombia">Colombia</option>
                                    <option value="Comoros">Comoros</option>
                                    <option value="Congo">Congo</option>
                                    <option value="Congo, The Democratic Republic of The">Congo, The Democratic Republic of The</option>
                                    <option value="Cook Islands">Cook Islands</option>
                                    <option value="Costa Rica">Costa Rica</option>
                                    <option value="Cote D&apos;ivoire">Cote D'ivoire</option>
                                    <option value="Croatia">Croatia</option>
                                    <option value="Cuba">Cuba</option>
                                    <option value="Curacao">Curacao</option>
                                </select>
                            </div>
                        </div>

                    </div>
                    <input type="hidden" name="folder_id" value="" id="folder_id">
                    <input type="hidden" name="main_folder_id" value="" id="main_folder_id">
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary form-submit-btn">Save</button>
                        <button type="button" class="btn btn-secondary" id="hideUploadMdal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div> --}}

    <!-- Send Notification Modal -->

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
</body>

</html>
