<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>@yield('title') - {{ env('APP_NAME') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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

    <!-- loader-->
	{{-- <link href="{{asset('front')}}/assets/css/pace.min.css" rel="stylesheet" /> --}}
</head>

<body class="login-body">

    <!-- wrapper -->
	<div class="wrapper">
        @yield('content')
	</div>
	<!-- end wrapper -->
	<!-- JavaScript -->
	<!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <!--plugins-->
  <script src="{{asset('front')}}/assets/js/jquery.min.js"></script>
  {{-- <script src="{{asset('front')}}/assets/js/pace.min.js"></script> --}}
  @yield('page-script')
	<!--Password show & hide js -->
	<script>
		$(document).ready(function () {
			$("#show_hide_password a").on('click', function (event) {
				event.preventDefault();
				if ($('#show_hide_password input').attr("type") == "text") {
					$('#show_hide_password input').attr('type', 'password');
					$('#show_hide_password i').addClass("bx-hide");
					$('#show_hide_password i').removeClass("bx-show");
				} else if ($('#show_hide_password input').attr("type") == "password") {
					$('#show_hide_password input').attr('type', 'text');
					$('#show_hide_password i').removeClass("bx-hide");
					$('#show_hide_password i').addClass("bx-show");
				}
			});
		});
	</script>
</body>

</html>
