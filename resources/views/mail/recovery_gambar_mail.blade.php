<!DOCTYPE html>
<html>
<head>
	<title></title>
	<style type="text/css">
		body{
			font-family: verdana;
		}

		#bg{
			background: lightgrey;
			padding: 30px;
		}

		#card{
			width: 80%;
			margin: auto;
			background: white;
			padding: 15px;
		}

		#card h3{
			text-align: center;
		}
	</style>
</head>
<body>
	<div id="gb">
		<div id="card">
			<h3>Dear, {{ $nama_user }}</h3>

			<p>
				Anda menerima email ini karena anda meminta pemulihan gambar cover baru-baru ini. 
			</p>

			<p>
				Klik pada link di bawah untuk mengubah password dan mendapatkan gambar cover baru. Link tersebut hanya aktif selama 30 menit
			</p>

			<p>
				<a href="{{ route('reset_cover', $reset_code) }}" target="_blank">Klik Link Berikut</a>
			</p>

			<br>
			<p>Terima Kasih, ttd</p>
			<br>
			{{ config('app.name') }}
		</div>
	</div>
</body>
</html>