<?php

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
Route::post('store_user', 'OtentikasiController@store_user')->name('store_user');
Route::get('/login', 'OtentikasiController@login')->name('login');
Route::post('/login', 'OtentikasiController@check_login')->name('check_login');
Route::get('/logout', 'OtentikasiController@logout')->name('logout');
Route::get('/pemulihan_gambar', 'OtentikasiController@pemulihan_gambar')->name('pemulihan_gambar');
Route::post('/kirim_email_pemulihan', 'OtentikasiController@kirim_email_pemulihan')->name('kirim_email_pemulihan');
Route::get('/pemulihan_gambar/reset/{code}', 'OtentikasiController@reset_cover')->name('reset_cover');
Route::post('/update_cover', 'OtentikasiController@update_cover')->name('update_cover');

Route::get('/dashboard', 'OtentikasiController@dashboard')->name('dashboard');
Route::get('/download_cover', 'OtentikasiController@download_cover')->name('download_cover');