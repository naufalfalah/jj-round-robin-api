<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>@yield('title') - {{ env('APP_NAME') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!--favicon-->
	<link rel="icon" href="{{asset('front')}}/assets/images/favicon.png" type="image/png" />
  <!-- Bootstrap CSS -->
  <link href="{{asset('front')}}/assets/css/bootstrap.min.css" rel="stylesheet" />
  <link href="{{asset('front')}}/assets/css/bootstrap-extended.css" rel="stylesheet" />
  <link href="{{asset('front')}}/assets/css/style.css" rel="stylesheet" />
  <link href="{{asset('front')}}/assets/css/icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">

  <link href="{{ asset('front') }}/assets/plugins/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css" />

  <!-- loader-->
	<link href="{{asset('front')}}/assets/css/pace.min.css" rel="stylesheet" />

    <style>
            select:invalid {
                color: gray;
            }
            option {
                color: black;
            }
            @media only screen and (min-width: 1024px) {
            .wrapper{
                margin-right: 190px;
            }
        }
        .desktop-image {
            display: block;
    }

.mobile-image {
  display: none;
}

@media only screen and (width >= 1100px) {

    .main-logo{

    }


}


    </style>
    @yield('page-css')
</head>

<body style="background-image: url('{{asset('front')}}/assets/images/background.png'); background-position: center; background-size: cover;  height: 100vh; width: 100vw;">

    <!-- wrapper -->
	<!-- <div class="wrapper">
        <main class="authentication-content"> -->
        @yield('content')
        <!-- </main>
	</div> -->

	<!-- end wrapper -->
	<!-- JavaScript -->
	<!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <!--plugins-->
    <script src="{{ asset('front') }}/assets/plugins/sweetalert2/sweetalert2.min.js"></script>
    <script src="{{asset('front')}}/assets/js/jquery.min.js"></script>
    <script src="{{asset('front')}}/assets/js/pace.min.js"></script>
    <script src="{{ asset('front') }}/assets/js/danidev.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>
	<!--Password show & hide js -->
    @yield('page-script')
</body>

</html>
