@extends('templates.template_view')
@section('page_title')
Pengujian Kualitas Gambar
@endsection

@section('custom_css')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style type="text/css">
	.top-index-bg{
		min-height: 600px;
		padding-right: 10px;
		padding-left: 10px;
	}

	.base_card{
		background: white;
		padding: 25px 15px;
		min-height: 480px;
	}

	.section-title{
		margin-bottom: 0;
	}

	#input_form{
		margin-top: 30px;
	}

	.img_container{
		display: none;
		width: 100%;
		height: auto;
		margin: auto;
	}

	.img_container img{
		width: 100%;
		height: auto;
		object-fit: cover;
	}

	#error_msg{
		display: none;
		font-size: 17px;
		color : red;
		font-weight: bold;
		text-align: center;
		margin-top: 15px;
	}

	#btn_wrapper{
		text-align: center;
		margin-top: 25px;
	}

	#loading_gif{
		display: none;
		text-align: center;
	}

	#card_hasil{
		display: none;
		width: 90%;
		text-align: center;
		margin: auto;
		margin-bottom: 25px;
		padding: 25px 100px;
		border: 3px solid #25ae88;
	}

	#card_hasil p{
		font-size: 18px;
	}

	#hasil_mse p{
		font-size: 15px;
	}

	#hasil_mse h4{
		font-size: 18px;
	}

	.chart_canvas{
		height:250px;
	}

	#histogram_btn{
		display: none;
		margin: auto;
	}

	#hasil_histogram{
		display: none;
		padding: 30px;
		text-align: center;
	}

	@media only screen and (max-width: 991px){
		.col-lg-6{
			margin-top: 20px;
		}
	}
</style>
@endsection

@section('content')
<section class="hero-section">
	<div class="top-index-bg">
		<div class="base_card">

			<div class="section-title">
				{{-- <p>The only ones</p> --}}
				<h2>Pengujian Kualitas Citra</h2>
			</div>

			<form method="post" enctype="multipart/form-data" id="input_form">
				<div class="row">
					<div class="col-lg-6">
						<div class="form-wrapper">
							<label for="gambar1">Pilih Gambar Asli (SEBELUM penyisipan)</label>
							<input type="file" name="gambar1" id="gambar1" accept="image/jpeg,image/png">
							<br><br>
							@error('gambar1')
								<span class="invalid-feedback" role="alert" style="color: red;">
									<strong>{{ $message }}</strong>
								</span>
							@enderror

							<div class="img_container" id="img_container1">
								<img src="#" id="preview_img1">
							</div>
						</div>
					</div>

					<div class="col-lg-6">
						<div class="form-wrapper">
							<label for="gambar2">Pilih Gambar Stego (SETELAH penyisipan)</label>
							<input type="file" name="gambar2" id="gambar2" accept="image/jpeg,image/png">
							<br><br>
							@error('gambar2')
								<span class="invalid-feedback" role="alert" style="color: red;">
									<strong>{{ $message }}</strong>
								</span>
							@enderror

							<div class="img_container" id="img_container2">
								<img src="#" id="preview_img2">
							</div>
						</div>
					</div>
				</div>

				<div id="btn_wrapper">
					<button class="btn btn-primary" type="button" id="tes_submit">Tes Gambar</button>
				</div>

			</form>

			<div id="loading_gif">
				<img src="{{ asset('/image/loading.gif') }}">
			</div>

			<div id="error_msg">
				<br>
				
			</div>

			<div class="row" id="card_hasil">
				<div class="col-lg-12" id="hasil_pengukuran">
					<h4>Hasil Pengukuran</h4>

					<div id="hasil_psnr">
						<br>
						<p><b>PSNR</b></p>
						<h2 id="nilai_psnr">0</h2>
					</div>
					<br>
					<div id="hasil_mse">
						<p><b>MSE</b></p>
						<h4 id="nilai_mse">0</h4>
					</div>
				</div>
			</div>	

			<br>
			<button class="btn btn-default" id="histogram_btn"  type="button" state="close">Histogram</button>
			<div id="hasil_histogram">
				<h4>Histogram Gambar Asli</h4>
				<canvas id="barChart" class="chart_canvas"></canvas>
				<br><br>
				<h4>Histogram Gambar stego</h4>
				<canvas id="barChart2" class="chart_canvas"></canvas>
			</div>

		</div>
	</div>
</section>
@endsection

@section('script')
<script src="{{asset('/chart.js/Chart.bundle.min.js')}}"></script>
<script type="text/javascript">
	function readURL(input, img_container) {
	 	if (input.files && input.files[0]) {
	   	var reader = new FileReader();

	   	reader.onload = function(e) {
	      	$(img_container).show();
	      	$(img_container+' img').attr('src', e.target.result);
	    	}

	   	reader.readAsDataURL(input.files[0]);
	  	}
	}

	$("#gambar1").change(function() {
	 	readURL(this, '#img_container1');
	});

	$("#gambar2").change(function() {
	 	readURL(this, '#img_container2');
	});

	function open_histogram() {
		$("#histogram_btn").click(function(event) {
			var state = $(this).attr('state');
			if (state == "close") {
				$("#hasil_histogram").show();
				$(this).attr('state', 'open');
			}
			else{
				$("#hasil_histogram").hide();
				$(this).attr('state', 'close');	
			}
		});
	}

	$('#tes_submit').click(function(event) {
		event.preventDefault();
		$("#card_hasil").hide();
		$("#histogram_btn").hide();
		$("#histogram_btn").attr('state', 'close');
		$("#hasil_histogram").hide();
		$("#loading_gif").show();
		// var gambar1 = $('input[name="gambar1"]').val();
		// var gambar2 = $('input[name="gambar2"]').val();
		var form = $("#input_form")[0];
		var input = new FormData(form);

		$("#error_msg").hide();
		$.ajaxSetup({
      headers: {
      	'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
   	});

		$.ajax({
			url: '{{ route('test') }}',
			type: 'post',
			enctype: 'multipart/form-data',
			dataType: 'json',
			data: input,
			processData: false,
			contentType: false,
		})
		.done(function(hasil) {
			console.log("success");
			// console.log(hasil);
			$("#loading_gif").hide();
			if (hasil['status'] == 'sukses') {
				$("#nilai_psnr").text(hasil['psnr']);
				$("#nilai_mse").text(hasil['mse']);

				var label_histogram = [];
				for (var i = 0; i < 256; i++) {
					label_histogram[i] = i;
				}
				// console.log('label : '+label_histogram);

				//chart Histogram gambar asli
				var ctx = $('#barChart').get(0).getContext('2d');
				var chart1 = new Chart(ctx, {
					type: 'bar',
					data: {
						labels : label_histogram,
						datasets: [{
							label: 'Jumlah Piksel',
				        	maxBarThickness: 3,
				        	data: hasil['histogram'][0],
				        	backgroundColor: "rgba(54, 162, 235, 1)",
				        	borderWidth : 1
				    	}]
					},
					options: {
						scales : {
							xAxes : [{
								ticks: {
									autoSkip : true,
									maxRotation: 10
								}
							}]
						}
					}
				});

				//cahrt histogram gambar stego
				var ctx = $('#barChart2').get(0).getContext('2d');
				var chart2 = new Chart(ctx, {
					type: 'bar',
					data: {
						labels : label_histogram,
						datasets: [{
							label: 'Jumlah Piksel',
				        	maxBarThickness: 3,
				        	data: hasil['histogram'][1],
				        	backgroundColor: "rgba(54, 162, 235, 1)",
				        	borderWidth : 1
				    	}]
					},
					options: {
						scales : {
							xAxes : [{
								ticks: {
									autoSkip : true,
									maxRotation: 10
								}
							}]
						}
					}
				});

				$("#card_hasil").show();

				$("#histogram_btn").show();
				open_histogram();
			}
			else if (hasil['status'] == 'resolusi_berbeda') {
				$("#error_msg").text(hasil['pesan']);
				$("#error_msg").show();
			}
			else if(hasil['status'] == 'mse_0'){
				$("#error_msg").text(hasil['pesan']);
				$("#error_msg").show();	
			}
			
		})
		.fail(function() {
			console.log("error");
			$("#loading_gif").hide();
			$("#error_msg").append("Terjadi Kesalahan. Harap coba kembali");
			$("#error_msg").show();
		});
		
	});
</script>
@endsection