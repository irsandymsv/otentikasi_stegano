@extends("templates.template_view")
@section("page_title")
	Ubah Password
@endsection

@section("custom_css")
<style type="text/css">
	.top-index-bg{
		min-height: 600px;
	}

	.inner{
		width: 60%;
		margin: auto;
		margin-top: 70px;
		margin-bottom: 110px;
	}	

	.inner h3{
		text-align: center;
	}

	button {
	  width: 100px;
	  height: 35px;
	  margin: unset;
	  /*margin-top: 10px;*/
	}

	@media only screen and (max-width: 991px) {
	  .inner {
	   	width: 85%;
	 	} 
	}
	
	@media only screen and (max-width: 767px) {
	  .inner {
	    width: 100%;
	  }
	}
</style>
@endsection

@section("content")
<section class="hero-section">
	<div class="top-index-bg">
		<div class="inner">

			@if (Session('error_dekripsi'))
				<form>
					<h3>Error</h3>
					<br><br>
					<p>
						{{ Session('error_dekripsi') }}
					</p>
				</form>
				@php
					Session::forget('error_dekripsi');
				@endphp		
			@else
				@if ($timeout)
					<form>
						<h3>Timeout</h3>
						<br><br>
						<p>
							Mohon maaf, batas waktu link pemulihan ini telah terlewati. Silahkan buat ulang permintaan pemulihan gambar melalui <a href="{{ route('pemulihan_gambar') }}">Link berikut</a>
						</p>
					</form>
				@else
				<form action="{{ route('update_cover') }}" method="post" enctype="multipart/form-data">
					@csrf
					
					<h3>Ubah Password</h3>
					<p>
						gambar cover baru akan dibuat ulang setelah anda memasukkan password dan pilih gambar baru. 
					</p>
					<div class="form-wrapper">
						<label for="password">Password</label>
						<input type="password" name="password" class="form-control">

						@error('password')
							<span class="invalid-feedback" role="alert" style="color: red;">
								<strong>{{ $message }}</strong>
							</span>
						@enderror
					</div>
					<br>

					<div class="form-wrapper">
						<label for="cover_photo">Pilih Gambar Cover</label>
						<input type="file" class="form-control" name="cover_photo" accept="image/jpeg,image/png">
						<small>Gambar ini akan digunakan sebagai media Log In</small>

						@error('cover_photo')
							<span class="invalid-feedback" role="alert" style="color: red;">
								<strong>{{ $message }}</strong>
							</span>
						@enderror

						@if (Session('error_found'))
							<span class="invalid-feedback" role="alert" style="color: red;">
								<strong>{{ Session('error_found') }}</strong>
							</span>
						@endif

						<input type="hidden" name="code" value="{{ $code }}">
					</div>
					<br><br>

					<button type="submit">Simpan</button>
				</form>
				@endif
			@endif
			
		</div>
	</div>
</section>
@endsection

@section("script")
<script src="{{asset('/regform-25/js/jquery-3.3.1.min.js')}}"></script>
@endsection