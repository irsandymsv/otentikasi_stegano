<!DOCTYPE html>
<html>
<head>
	<title>Uji Enkripsi</title>

	<style type="text/css">
		body{
			padding: 10px;
		}
	</style>
</head>
<body>
	<h2>Pengujian Enkripsi</h2>
	<p>Masukkan kredensial dan citra. Kredensial akan disisipkan ke dalam citra tanpa enkripsi</p>
	<form action="{{ route('tes_enkripsi') }}" method="post" enctype="multipart/form-data">
		@csrf
		<p>
			<label for="email">Email : </label>
			<input type="email" name="email" >
		</p>
		<p>
			<label for="password">Password : </label>
			<input type="password" name="password" accept="image/jpeg,image/png">
		</p>
		<p>
			<label for="citra">Masukkan Citra : </label>
			<input type="file" name="cover_photo" accept="image/jpeg,image/png">
		</p>

		<input type="submit" name="submit" value="Kirim">
	</form>
</body>
</html>