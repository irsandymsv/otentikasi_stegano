@extends("templates.template_view")
@section("page_title")
	Pemulihan Gambar Cover
@endsection

@section("custom_css")
<style type="text/css">
	.top-index-bg{
		min-height: 600px;
	}

	.inner{
		width: 60%;
		margin: auto;
		margin-top: 60px;
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

	.form-wrapper span.lnr-calendar-full{
		top: 46px;
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

	@if (Session('email_send'))
	#form_recovery{
		display: none;
	}
	@endif
</style>
@endsection

@section("content")
<section class="hero-section">
	<div class="top-index-bg">
		<div class="inner">
			@if (Session('email_send'))
			<form>
				<h3>Berhasil</h3>
				<br><br>
				<p>
					{{ Session('email_send') }} <br>
					<span> Tidak menerima email? <a href="#" id="resend_link">Klik untuk Kirim ulang</a></span>
				</p>
			</form>
			@endif

			<form id="form_recovery" action="{{ route('kirim_email_pemulihan') }}" method="post">
				@csrf
				
				<h3>Pemulihan Gambar</h3>
				<p>Masukkan email dan tanggal lahir yang anda gunakan ketika mendaftarkan akun. Email berisi link pemulihan gambar akan dikirim ke alamat email anda</p>
				<div class="form-wrapper">
					<label for="cover_photo">Email</label>
					<input type="email" class="form-control" name="email" value="{{ old('email') }}">

					@error('email')
						<span class="invalid-feedback" role="alert" style="color: red;">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
				</div>

				<div class="form-wrapper">
					<label for="tgl_lahir">Tanggal Lahir</label>
					<span class="lnr lnr-calendar-full"></span>
					<input type="text" name="tgl_lahir" class="form-control datepicker-here" data-language='id' data-date-format="dd-mm-yyyy" id="dp1" autocomplete="off">

					@error('tgl_lahir')
						<span class="invalid-feedback" role="alert" style="color: red;">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
				</div>

				@if (Session('user_not_found'))
					<span class="invalid-feedback" role="alert" style="color: red;">
						<strong>{{ Session('user_not_found') }}</strong>
					</span>
				@endif
				<br><br>

				<button type="submit">Kirim</button>
			</form>
			
		</div>
	</div>
</section>
@endsection

@section("script")
{{-- <script src="{{asset('/regform-25/js/jquery-3.3.1.min.js')}}"></script> --}}
<!-- DATE-PICKER -->
<script src="{{asset('/regform-25/vendor/date-picker/js/datepicker.js')}}"></script>
<script src="{{asset('/regform-25/vendor/date-picker/js/datepicker.en.js')}}"></script>
{{-- <script src="{{asset('/regform-25/js/main.js')}}"></script> --}}
<script type="text/javascript">
	$(function() {
		var dp1 = $('#dp1').datepicker().data('datepicker');

		var old_tgl = @json(old('tgl_lahir'));
		if (old_tgl != null) {
			var tgl = old_tgl.split("-");
			dp1.selectDate(new Date(tgl[2] +"-"+ tgl[1] +"-"+ tgl[0]));
		}

		@if (Session('email_send'))
		$("#resend_link").click(function(event) {
			$("#form_recovery").submit();
		});
		@endif
	});
</script>
@endsection