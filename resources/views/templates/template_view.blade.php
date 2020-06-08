<!DOCTYPE html>
<html lang="en">
<head>
	<title>Fasilkom Hosting | @yield("page_title")</title>
	<meta charset="UTF-8">
	<meta name="description" content="Fasilkom UNEJ Hosting">
	<meta name="keywords" content="cloud, hosting, creative, html">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<!-- Favicon -->
	<link href="{{asset('/cloud83/img/favicon.ico')}}" rel="shortcut icon"/>

	<!-- Google Font -->
	<link href="https://fonts.googleapis.com/css?family=Poppins:400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

	<!-- Stylesheets -->
	<link rel="stylesheet" href="{{asset('/cloud83/css/bootstrap.min.css')}}"/>
	<link rel="stylesheet" href="{{asset('/cloud83/css/style.css')}}"/>
	<link rel="stylesheet" href="{{asset('/cloud83/css/animate.css')}}"/>

	<!--[if lt IE 9]>
	  <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
	  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->

	{{-- CSS Form Registration --}}
	<!-- LINEARICONS -->
	<link rel="stylesheet" href="{{asset('/regform-25/fonts/linearicons/style.css')}}">

	<!-- DATE-PICKER -->
	<link rel="stylesheet" href="{{asset('/regform-25/vendor/date-picker/css/datepicker.min.css')}}">
	
	<!-- STYLE CSS -->
	<link rel="stylesheet" href="{{asset('/regform-25/css/style.css')}}">

	{{-- custom CSS --}}
	
	<style type="text/css">
		.top-index-bg{
			background-image: url('{{asset("/cloud83/img/bg.jpg")}}'); 
			padding-top: 90px;
			padding-right: 30px;
			padding-left: 30px;
			padding-bottom: 20px;
		}	

		.invalid-feedback{
			display: block;
			font-size: 14px;
		}

		.main-menu li a:hover{
			color: #25ae88;
		}
	</style>
	
	@yield("custom_css")
</head>
<body>
	@yield("page_loader")

	<!-- Header section -->
	<header class="header-section">
		<div class="container">
			<a href="{{ route('index') }}" class="site-logo">
				<img src="{{asset('/image/fasilkom host logo 2.png')}}" alt="logo">
			</a>
			
			<!-- Switch button -->
			<div class="nav-switch">
				<div class="ns-bar"></div>
			</div>
			<div class="header-right">
				<ul class="main-menu">
					<li><a href="{{ route('index') }}">Home</a></li>
					<li><a href="{{ route('pemulihan_gambar') }}">Pemulihan Gambar</a></li>
					<li><a href="{{ route('uji_kualitas') }}">Uji Kualitas</a></li>
					{{-- <li><a href="blog.html">News</a></li>
					<li><a href="contact.html">Contact</a></li> --}}
				</ul>
				<div class="header-btns">
					<a href="{{ route('login') }}" class="site-btn sb-c2">Log In</a>
					{{-- <a href="#" class="site-btn sb-c3">Register</a> --}}
				</div>
			</div>
		</div>
	</header>
	<!-- Header section end -->

	@yield("content")
	
	<!-- Footer section -->
	<footer class="footer-section">
		<div class="container">
			<div class="footer-nav">
				<ul>
					<li><a href="{{ route('index') }}">Home</a></li>
					{{-- <li><a href="about.html">About us</a></li>
					<li><a href="service.html">Services</a></li>
					<li><a href="blog.html">News</a></li>
					<li><a href="contact.html">Contact</a></li> --}}
				</ul>
			</div>
			<div class="copyright">
				<p><!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
					Copyright &copy;<script>document.write(new Date().getFullYear());</script> All rights reserved | This template is made with <i class="fa fa-heart-o" aria-hidden="true"></i> by <a href="https://colorlib.com" target="_blank">Colorlib</a>
				<!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
				</p>
			</div>
		</div>
	</footer>
	<!-- Footer section end -->


	<!--====== Javascripts & Jquery ======-->
	<script src="{{asset('/js/jquery-3.4.1.min.js')}}"></script>
	{{-- <script src="{{asset('/cloud83/js/jquery-3.2.1.min.js')}}"></script> --}}
	<script src="{{asset('/cloud83/js/bootstrap.min.js')}}"></script>
	<script src="{{asset('/cloud83/js/main.js')}}"></script>

	@yield("script")
</body>
</html>
