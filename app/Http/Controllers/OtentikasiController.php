<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OtentikasiController extends Controller
{
   public function index()
   {
   	return view('otentikasi.index');
   }

   public function store_user(Request $request)
   {
   	# code...
   }

	public function login()
   {
   	return view('otentikasi.login');
   }

   public function check_login(Request $request)
   {
   	# code...
   }

   public function logout()
   {
   	# code...
   }

   public function pemulihan_gambar()
   {
   	return view('otentikasi.pemulihan_gambar');
   }

   public function kirim_email_pemulihan(Request $request)
   {
   	# code...
   }

   public function reset_cover($code)
   {
   	# code...
   }

   public function update_cover(Request $request)
   {
   	# code...
   }

   public function dashboard()
   {
   	# code...
   }

   public function download_cover()
   {
   	# code...
   }
}
