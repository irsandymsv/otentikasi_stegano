@extends("templates.template_view")
@section("page_title")
	Home
@endsection

@section("custom_css")
<style type="text/css">
	.top-wrap .col-lg-5{
		margin-top: 60px;
	}

	.top-wrap h2{
		color: white;
	}

	.top-wrap p{
		color: white;
		font-size: 17px;
	}

	.form-group label{
		display: block;
		font-family: "Muli-SemiBold";
		font-weight: bold;
		font-size: 16px;
		color: #4c4c4c;
	}

	#sign_now{
		font-size: 22px;
	}
</style>
@endsection

@section("page_loader")
<!-- Page Preloder -->
<div id="preloder">
	<div class="loader"></div>
</div>
@endsection

@section("content")
<!-- Hero section -->
<section class="hero-section">
	<div class="top-index-bg">
		<div class="row top-wrap">
			<div class="col-lg-5">
				<h2>Fasilkom Hosting</h2>
				<br>
				<p>Web hosting tetrbaru dari Fakultas Ilmu Komputer Universitas Jember</p>
				<p id="sign_now">Daftar Sekarang!</p>
				<br><br>
			</div>

			<div class="col-lg-7">
				<div class="inner">
					<form action="{{ route('store_user') }}" method="post" enctype="multipart/form-data">
						@csrf
						<h3 style="text-align: center;">Registrasi</h3>
						
						<div class="form-group">
							<label for="nama">Nama</label>
							<input type="text" name="nama" class="form-control" value="{{ old('nama') }}">

							@error('nama')
								<span class="invalid-feedback" role="alert" style="color: red;">
									<strong>{{ $message }}</strong>
								</span>
							@enderror
						</div>

						<div class="form-group">
							<div class="row">
								<div class="col-sm-6">
									<label for="email">Email</label>
									<input type="email" name="email" class="form-control" value="{{ old('email') }}">

									@error('email')
										<span class="invalid-feedback" role="alert" style="color: red;">
											<strong>{{ $message }}</strong>
										</span>
									@enderror
								</div>

								<div class="col-sm-6">
									<label for="password">Password</label>
									<input type="password" name="password" class="form-control">

									@error('password')
										<span class="invalid-feedback" role="alert" style="color: red;">
											<strong>{{ $message }}</strong>
										</span>
									@enderror
								</div>
							</div>
						</div>

						<div class="form-group">
							<div class="row">
								<div class="col-sm-6">
									<label for="no_hp">No. HP</label>
									<input type="text" class="form-control" name="no_hp" value="{{ old('no_hp') }}">

									@error('no_hp')
										<span class="invalid-feedback" role="alert" style="color: red;">
											<strong>{{ $message }}</strong>
										</span>
									@enderror
								</div>

								<div class="col-sm-6">
									<label for="tgl_lahir">Tanggal Lahir</label>
									<span class="lnr lnr-calendar-full"></span>
									<input type="text" name="tgl_lahir" class="form-control datepicker-here" data-language='id' data-date-format="dd-mm-yyyy" id="dp1" autocomplete="off">

									@error('tgl_lahir')
										<span class="invalid-feedback" role="alert" style="color: red;">
											<strong>{{ $message }}</strong>
										</span>
									@enderror
								</div>
							</div>
						</div>

						<div class="form-group">
							<div class="row">
								<div class="col-sm-6">
									<label for="gender">Jenis Kelamin</label>
									<label><input type="radio" name="gender" value="Laki-laki" {{ (old('gender') == "Laki-laki"? "checked":"") }}> Laki-laki </label>
									<label><input type="radio" name="gender" value="Perempuan" {{ (old('gender') == "Perempuan"? "checked":"") }}> Perempuan </label>

									@error('gender')
										<span class="invalid-feedback" role="alert" style="color: red;">
											<strong>{{ $message }}</strong>
										</span>
									@enderror
								</div>

								<div class="col-sm-6">
									<label for="cover_photo">Cover Photo</label>
									<input type="file" name="cover_photo" class="form-control" accept="image/jpeg,image/png">
									<small>Gambar ini akan digunakan sebagai media Log In</small><br>

									@error('cover_photo')
										<span style="color: red;">
											<strong>{{ $message }}</strong>
										</span>
									@enderror

									@if (Session('error_found'))
										<span class="invalid-feedback" role="alert" style="color: red;">
											<strong>{{ Session('error_found') }}</strong>
										</span>
									@endif

									{{-- @if (Session('gambar_tdk_cukup'))
										<span class="invalid-feedback" role="alert" style="color: red;">
											<strong>{{ Session('gambar_tdk_cukup') }}</strong>
										</span>
									@endif --}}
								</div>
							</div>
						</div>
						
						<button type="submit">Daftar</button>
					</form>
				</div>
			</div>
		</div>
	</div>
</section>
<!-- Hero section end -->
@endsection

@section("script")
{{-- <script src="{{asset('/regform-25/js/jquery-3.3.1.min.js')}}"></script> --}}
<script src="{{asset('/cloud83/js/circle-progress.min.js')}}"></script>
<!-- DATE-PICKER -->
<script src="{{asset('/regform-25/vendor/date-picker/js/datepicker.js')}}"></script>
<script src="{{asset('/regform-25/vendor/date-picker/js/datepicker.en.js')}}"></script>
{{-- <script src="{{asset('/regform-25/js/main.js')}}"></script> --}}
<script type="text/javascript">
	$(function() {
		var dp1 = $('#dp1').datepicker().data('datepicker');
		// dp1.selectDate(new Date();

		var old_tgl = @json(old('tgl_lahir'));
		if (old_tgl != null){
			var tgl = old_tgl.split("-");
			dp1.selectDate(new Date(tgl[2] +"-"+ tgl[1] +"-"+ tgl[0]));
		}
	});
</script>
@endsection