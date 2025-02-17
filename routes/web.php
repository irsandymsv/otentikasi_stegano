<?php
use Jenssegers\Agent\Agent;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'OtentikasiController@index')->name('index');
Route::post('/store_user', 'OtentikasiController@store_user')->name('store_user');
Route::get('/dashboard', 'OtentikasiController@dashboard')->name('dashboard')->middleware('auth');
Route::get('/download_cover', 'OtentikasiController@download_cover')->name('download_cover')->middleware('auth');
Route::get('/login', 'OtentikasiController@login')->name('login');
Route::post('/login', 'OtentikasiController@check_login')->name('check_login');
Route::get('/logout', 'OtentikasiController@logout')->name('logout');
Route::get('/pemulihan_gambar', 'OtentikasiController@pemulihan_gambar')->name('pemulihan_gambar');
Route::post('/kirim_email_pemulihan', 'OtentikasiController@kirim_email_pemulihan')->name('kirim_email_pemulihan');
Route::get('/pemulihan_gambar/reset/{code}', 'OtentikasiController@reset_cover')->name('reset_cover');
Route::post('/update_cover', 'OtentikasiController@update_cover')->name('update_cover');

Route::get('/uji_kualitas', 'TestController@index')->name('uji_kualitas');
Route::post('/test', 'TestController@test')->name('test');

Route::get('/uji_enkripsi', 'OtentikasiController@uji_enkripsi')->name('uji_enkripsi');
Route::post('/tes_enkripsi', 'OtentikasiController@tes_enkripsi')->name('tes_enkripsi');

//Deteksi device yang digunakan
Route::get('/device_detect', function()
{
	$agent = new Agent();
	$device = $agent->device();
	echo "device = ".$device."<br>";

	$platform = $agent->platform();
	$platform_ver = $agent->version($platform);
	echo "platform = ".$platform." ".$platform_ver."<br>";

	$browser = $agent->browser();
	$browser_ver = $agent->version($browser);
	echo "browser = ".$browser." ".$browser_ver."<br>";

	echo "<br>";
	if ($agent->isDesktop()) {
		echo "you are using DESKTOP device <br>";
	}
	elseif ($agent->isMobile()) {
		echo "you are using MOBILE device <br>";
	}
});