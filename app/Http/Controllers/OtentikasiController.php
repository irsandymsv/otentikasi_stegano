<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Encryption\DecryptException;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\User;
use App\recovery_image;
use App\Mail\recoveryImage;

class OtentikasiController extends Controller
{
   public function index()
   {
   	return view('otentikasi.index');
   }

   public function store_user(Request $request)
   {
	   $this->validate($request, [
         "cover_photo" => "required|mimetypes:image/jpeg,image/png",
         "nama" => "required|string",
         "email" => "required|email|string|max:190|unique:users",
         "no_hp" => "required|string|unique:users",
         "tgl_lahir" => "required",
         "gender" => "required",
         "password" => "required|string|max:12|min:6",
      ]);

      $cover_photo = $request->file('cover_photo');
      $ekstensi = $cover_photo->getClientOriginalExtension();
      $image = '';
      if ($ekstensi == "jpeg" || $ekstensi == "jpg") {
         $image = imagecreatefromjpeg($cover_photo->path());
      }
      elseif ($ekstensi == "png") {
         $image = imagecreatefrompng($cover_photo->path()); 
      }

      //Buat histogram dari $image
      $histogram = $this->makeHistogram($image);

      //Tentukan Peak dan Zero
      $max_point = max($histogram); //Jumlah piksel terbanyak
      $peak = array_search($max_point, $histogram);

      $min_point = min($histogram); //Jumlah piksel tersedikit
      $zero = array_search($min_point, $histogram);

      //Return back jika peak == zero
      if ($peak == $zero) {
         return redirect()->back()->with('error_found', 'Gambar tidak dapat digunakan, harap pilih gambar lain')->withInput();
      }

      $password = $request->input('password');
      $message = $request->input('email')." ".$password;
      $message_encrypt = encrypt($message); //Enkripsi kredensial (email dan password)
      $msg_secret = $message_encrypt." ";
      $bin_message = $this->stringToBin($msg_secret);
      $bin_msg_len = strlen($bin_message);

      //tentukan kapasitas image
      $overhead_len = 0; //jml pixel zero(jika ada) + pixel di sampingnya
      if ($min_point > 0) {
         $overhead_len = $min_point;

         if ($peak > $zero) {
            $overhead_len += $histogram[$zero + 1];
         }
         else {
            $overhead_len += $histogram[$zero - 1];
         }
      }

      $unused_key_pixel = 0; //jmlh pixel peak yg tidak dapat digunakan utk penyisipan karena digunakan utk menyimpan binary key (peak n zero)
      $yAxis=0;
      for ($x=0; $x < 16; $x++) { 
         $rgb = imagecolorat($image, $x, $yAxis);
         $r = ($rgb >> 16) & 0xFF;
         if ($r == $peak) {
            $unused_key_pixel++;
         }
      }

      $pure_payload = $max_point - ($overhead_len + $unused_key_pixel);
      if ($bin_msg_len > $pure_payload) {
         return redirect()->back()->with('error_found', 'Gambar tidak cukup untuk menampung data, harap pilih gambar lain')->withInput();
      }

      $tgl_parse = Carbon::parse($request->input('tgl_lahir'));
      $hash_pass = Hash::make($password);
      $new_user = User::create([
         "nama" => $request->input('nama'),
         "email" => $request->input('email'),
         "no_hp" => $request->input('no_hp'),
         "gender" => $request->input('gender'),
         "tgl_lahir" => $tgl_parse,
         "password" => $hash_pass
      ]);

      $this->penyisipan(
         $image, 
         $peak, 
         $zero, 
         $bin_message, 
         $new_user->id
      );

      Auth::login($new_user);
      $executionTime = microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'];
      Session(["executionTime_register" => $executionTime]);
      return redirect()->route('dashboard');
   }

   public function dashboard()
   {
      $user = Auth::user();
      $is_exist = Storage::exists('user_cover/cover_photo-'.$user->id.'.png');
      $cover_exist = false;
      if ($is_exist) {
         $cover_exist = true;
      }

      return view('dashboard', ['cover_exist' => $cover_exist]);
   }

   public function download_cover()
   {
      $user = Auth::user();
      $is_exist = Storage::exists('user_cover/cover_photo-'.$user->id.'.png');
      if ($is_exist) {
         ob_end_clean();
         $headers = array(
            'Content-Type: image/png',
         );
         return response()->download(storage_path('app/public/user_cover/cover_photo-'.$user->id.'.png'), 'user_cover_image.png', $headers)->deleteFileAfterSend();
      }
      else{
         return redirect()->back()->with('error_found', 'Gambar cover tidak ditemukan atau sudah didownload sebelumnya.');
      }
   }

	public function login()
   {
      if (Auth::check()) {
         return redirect()->route('dashboard');
      }
   	return view('otentikasi.login');
   }

   public function check_login(Request $request)
   {
   	$this->validate($request, [
         "cover_photo" => "required|mimetypes:image/jpeg,image/png",
      ]);

      $cover_photo = $request->file('cover_photo');
      $ekstensi = $cover_photo->getClientOriginalExtension();
      $image = '';
      if ($ekstensi == "jpeg" || $ekstensi == "jpg") {
         $image = imagecreatefromjpeg($cover_photo->path());
      }
      elseif ($ekstensi == "png") {
         $image = imagecreatefrompng($cover_photo->path());
      }

      $user_info = $this->ekstraksi($image);

      if ($user_info == "error_cover") {
         return redirect()->back()->with('error_found', "Gambar tidak dapat digunakan. Pastikan gambar yang digunakan adalah gambar cover yang didapat ketika registrasi. Jika tetap gagal, gunakan fitur pemulihan gambar cover.");
      }

      //Dekrip kredensial (email dan password) 
      try {
         $dekrip_pesan = decrypt($user_info);
      } catch (\Exception $e) {
         return redirect()->back()->with('error_found', "Dekripsi kredensial gagal. Harap pastikan gambar cover yang digunakan benar dan tidak rusak.");
      }
      $kredensial = explode(" ", $dekrip_pesan);

      if (Auth::attempt(['email' => $kredensial[0], 'password' => $kredensial[1]])) {
         $executionTime = microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'];
         Session(["executionTime_login" => $executionTime]);

         return redirect()->route('dashboard');
      }
      else{
         return redirect()->back()->with('user_not_found', "Akun tidak ditemukan. Harap gunakan gambar cover yang anda dapat ketika registrasi.");
      }
   }

   public function logout(Request $request)
   {
   	Auth::logout();
      $request->session()->flush();
      return redirect()->route('login');
   }

   public function pemulihan_gambar()
   {
   	return view('otentikasi.pemulihan_gambar');
   }

   public function kirim_email_pemulihan(Request $request)
   {
   	$this->validate($request, [
         'email' => 'required|email',
         'tgl_lahir' => 'required'
      ]);

      $tgl_lahir = Carbon::parse($request->input('tgl_lahir'));
      $user = User::where([
         ['email', $request->input('email')],
         ['tgl_lahir', $tgl_lahir]
      ])->first();

      if (is_null($user)) {
         return redirect()->back()->with('user_not_found', 'Akun tidak ditemukan. Harap periksa kembali email dan tanggal lahir yang anda masukkan')->withInput();
      }
      else{
         $waktu_email = time()." ".$user->email;
         $code = encrypt($waktu_email);
         MAil::to($request->input('email'))->send(new recoveryImage($code, $user->nama));

         return redirect()->back()->with('email_send', 'Email pemulihan telah dikirimkan ke alamat email anda. Silahkan periksa kotak masuk email anda.')->withInput();
      }
   }

   public function reset_cover($code)
   {
   	$timeout = false;
      try {
         $waktu_email = decrypt($code);
      } catch (\Exception $e) {
         $timeout = true;
         Session(['error_dekripsi' => "Terjadi error, pastikan link yang anda gunakan benar."]);
         return view('otentikasi.reset_cover', ['timeout' => $timeout]);
      }

      $waktu_email = explode(" ", $waktu_email);
      $selisih_waktu = (time() - $waktu_email[0]) / 60;
      
      if (ceil($selisih_waktu) > 30) {
         $timeout = true;
      }
      return view('otentikasi.reset_cover', [
         'timeout' => $timeout,
         'code' => $code
      ]);
   }

   public function update_cover(Request $request)
   {
   	$this->validate($request, [
         "cover_photo" => "required|mimetypes:image/jpeg,image/png",
         "password" => "required|string|max:12|min:6",
      ]);

      $code = '';
      if (is_null($request->input('code'))) {
         return redirect()->back()->with('error_found', 
            'Terjadi kesalahan. Harap buat permintaan pemulihan gambar lagi.');
      }
      else{
         $code = $request->input('code');
      }

      //dapatkan email pengguna dari $code
      try {
         $waktu_email = decrypt($code); 
      } catch (\Exception $e) {
         return redirect()->back()->with('error_found', 'pastikan link yang anda gunakan benar. Jika tetap mengalami error, harap buat ulang permintaan pemulihan gambar cover.')->withInput();
      }

      $waktu_email = explode(" ", $waktu_email);
      $user = User::where('email', $waktu_email[1])->first();

      $cover_photo = $request->file('cover_photo');
      $ekstensi = $cover_photo->getClientOriginalExtension();
      $image = '';
      if ($ekstensi == "jpeg" || $ekstensi == "jpg") {
         $image = imagecreatefromjpeg($cover_photo->path());
      }
      elseif ($ekstensi == "png") {
         $image = imagecreatefrompng($cover_photo->path()); 
      }

      //Buat histogram dari $image
      $histogram = $this->makeHistogram($image);

      //Tentukan Peak dan Zero
      $max_point = max($histogram);
      $peak = array_search($max_point, $histogram);

      $min_point = min($histogram);
      $zero = array_search($min_point, $histogram); 

      //Return back jika peak == zero
      if ($peak == $zero) {
         return redirect()->back()->with('error_found', 'Gambar tidak dapat digunakan, harap pilih gambar lain');
      }

      $password = $request->input('password');
      $message = $user->email." ".$password;
      $message_encrypt = encrypt($message); //Enkripsi kredensial (email dan password)
      $msg_secret = $message_encrypt." ";
      $bin_message = $this->stringToBin($msg_secret);
      $bin_msg_len = strlen($bin_message);

      //tentukan kapasitas image
      $overhead_len = 0; //jml pixel zero(jika ada) + pixel di sampingnya
      if ($min_point > 0) {
         $overhead_len = $min_point;

         if ($peak > $zero) {
            $overhead_len += $histogram[$zero + 1];
         }
         else {
            $overhead_len += $histogram[$zero - 1];
         }
      }

      $unused_key_pixel = 0; //jmlh pixel peak yg tidak dapat digunakan utk embedding karena digunakan utk menyimpan binary key (peak n zero)
      $yAxis=0;
      for ($x=0; $x < 16; $x++) { 
         $rgb = imagecolorat($image, $x, $yAxis);
         $r = ($rgb >> 16) & 0xFF;
         if ($r == $peak) {
            $unused_key_pixel++;
         }
      }

      $pure_payload = $max_point - ($overhead_len + $unused_key_pixel);
      if ($bin_msg_len > $pure_payload) {
         return redirect()->back()->with('error_found', 'Gambar tidak cukup untuk menampung data. Harap pilih gambar lain')->withInput();
      }

      $hash_pass = Hash::make($password);
      $user->password = $hash_pass;
      $user->save();

      $this->penyisipan(
         $image, 
         $peak, 
         $zero, 
         $bin_message, 
         $user->id
      );

      Auth::login($user);
      Session(['pemulihan_sukses' => 'Password dan gambar cover anda berhasil diperbarui.']);
      
      return redirect()->route('dashboard');
   }



   //<======= Penyisipan dan Ekstraksi Steganografi =======>

   private function penyisipan($image, $peak, $zero, $bin_message, $user_id)
   {
      $width = imagesx($image);
      $height = imagesy($image);

      //simpan nilai binary dari peak dan zero
      $bin_key = ''; //nilai binary peak dan zero
      $bin_peak = $this->integerToBin($peak);
      $bin_zero = $this->integerToBin($zero);
      $bin_key = $bin_peak.$bin_zero;

      //Shifting
      if ($peak < $zero) {
         //ditambah (shift to right)
         for ($y=0; $y < $height; $y++) { 
            for ($x=0; $x < $width; $x++) { 
               $rgb = imagecolorat($image, $x, $y);
               $r = ($rgb >> 16) & 0xFF;
               $g = ($rgb >> 8) & 0xFF;
               $b = $rgb & 0xFF;

               //overhead info (ketika min_point > 0)
               if ($r == $zero) {
                  $bin_message .= "0";
               }
               elseif($r == ($zero - 1)){
                  $bin_message .= "1";
               }

               //SHIFT
               if ($r > $peak && $r < $zero) {
                  $newR = $r+1;
                  $newColor = imagecolorallocate($image, $newR, $g, $b);
                  imagesetpixel($image, $x, $y, $newColor);
               }
            }
         }
      }
      else{
         //dikurangi (shift to left)
         for ($y=0; $y < $height; $y++) { 
            for ($x=0; $x < $width; $x++) { 
               $rgb = imagecolorat($image, $x, $y);
               $r = ($rgb >> 16) & 0xFF;
               $g = ($rgb >> 8) & 0xFF;
               $b = $rgb & 0xFF;

               //overhead info (ketika min_point > 0)
               if ($r == $zero) {
                  $bin_message .= "0";
               }
               elseif($r == ($zero + 1)){
                  $bin_message .= "1";
               }

               if ($r < $peak && $r > $zero) {
                  $newR = $r-1;
                  $newColor = imagecolorallocate($image, $newR, $g, $b);
                  imagesetpixel($image, $x, $y, $newColor);
               }
            }
         }
      }

      /*Simpan LSB 16 pixel pertama ke bin_message. Ganti dg bin_key
      */

      for ($y=0; $y < 1; $y++) { 
         for ($x=0; $x < 16; $x++) { 
            $rgb = imagecolorat($image, $x, $y);
            $r = ($rgb >> 16) & 0xFF;
            $g = ($rgb >> 8) & 0xFF;
            $b = $rgb & 0xFF;

            $newR = $this->integerToBin($r);

            // simpan LSB asli di bin_message
            $bin_message .= $newR[strlen($newR) - 1];
            // ganti LSB dg bit dr bin_key 
            $newR[strlen($newR) - 1] = $bin_key[$x]; 
            $newR = bindec($newR);

            $newColor = imagecolorallocate($image, $newR, $g, $b);
            imagesetpixel($image, $x, $y, $newColor);
         }
      }

      //penyisipan
      $bin_msg_len = strlen($bin_message);
      $count = 0;
      for ($y=0; $y < $height; $y++) { 
         for ($x=0; $x < $width; $x++) { 
            if ($y == 0) { 
               if ($x >= 0 && $x < 16) {
                  //cek apakah pixel termasuk 16 pertama, jika ya lewati
                  continue;
               }
            }

            if ($count == $bin_msg_len) {
               break 2;
            }
            $rgb = imagecolorat($image, $x, $y);
            $r = ($rgb >> 16) & 0xFF;
            $g = ($rgb >> 8) & 0xFF;
            $b = $rgb & 0xFF;

            if ($r == $peak) {
               if ($peak < $zero) {
                  //r ditambah 1
                  if ($bin_message[$count] == 1) {
                     $newR = $r+1;
                  }
                  else{
                     $newR = $r;
                  }
               }
               else{
                  //r dikurangi 1
                  if ($bin_message[$count] == 1) {
                     $newR = $r-1;
                  }
                  else{
                     $newR = $r;
                  }
               }
               
               $newColor = imagecolorallocate($image, $newR, $g, $b);
               imagesetpixel($image, $x, $y, $newColor);

               $count+=1;
            }
         }
      }

      $path = storage_path("app/public/user_cover/cover_photo-".$user_id.".png");
      imagepng($image, $path);
      imagedestroy($image);
   }

   //EKSTRAKSI
   private function ekstraksi($cover_photo)
   {
      $width = imagesx($cover_photo);
      $height = imagesy($cover_photo);
      $bin_message = '';
      $user_info = '';
      // $min_point = 0;

      try {
         /*Ekstrak peak dan zero point dari 16 piksel pertama
         */
         $bin_key = '';
         $bin_peak = '';
         $bin_zero = '';
         for ($y=0; $y < 1; $y++) { 
            for ($x=0; $x < 16; $x++) { 
               $rgb = imagecolorat($cover_photo, $x, $y);
               $r = ($rgb >> 16) & 0xFF;
               $bin_r = $this->integerToBin($r);
               $bin_key .= $bin_r[strlen($bin_r) - 1];
            }
         }

         $bin_peak = substr($bin_key, 0, 8);
         $bin_zero = substr($bin_key, 8, 8);
         $peak = bindec($bin_peak);
         $zero = bindec($bin_zero);

         //Ekstrak pesan yg disisipkan
         if ($peak < $zero) { //jika peak di sebelah kiri zero
            for ($y=0; $y < $height; $y++) { 
               for ($x=0; $x < $width; $x++) { 
                  if ($y == 0) { 
                     if ($x >= 0 && $x < 16) {
                        //cek apakah pixel termasuk 16 pertama, jika ya lewati
                        continue;
                     }
                  }

                  $rgb = imagecolorat($cover_photo, $x, $y);
                  $r = ($rgb >> 16) & 0xFF;

                  if ($r == $peak) {
                     $bin_message .= "0";
                  }
                  elseif ($r == ($peak + 1)) {
                     $bin_message .= "1";
                  }
                  // elseif ($r == $zero) {
                  //    $min_point++;
                  // }
               }
            }
         }
         elseif ($peak > $zero){ //jika peak di sebelah kanan zero
            for ($y=0; $y < $height; $y++) { 
               for ($x=0; $x < $width; $x++) { 
                  if ($y == 0) { 
                     if ($x >= 0 && $x < 16) {
                        //cek apakah pixel termasuk 16 pertama, jika ya lewati
                        continue;
                     }
                  }

                  $rgb = imagecolorat($cover_photo, $x, $y);
                  $r = ($rgb >> 16) & 0xFF;

                  if ($r == $peak) {
                     $bin_message .= "0";
                  }
                  elseif ($r == ($peak - 1)) {
                     $bin_message .= "1";
                  }
                  // elseif ($r == $zero) {
                  //    $min_point++;
                  // }
               }
            }
         }
         else{
            return "error_cover";
         }

         //Ambil pesan asli dan overhead info (jika ada)
         $message_len = strlen($bin_message);
         $pesan_asli = '';
         // $overhead_info = '';
         // $key_LSB_asli = '';
         
         for ($i=0; ($i + 7) < $message_len; $i += 8) { 
            $bin_part = substr($bin_message, $i, 8);
            $char = pack('H*', dechex(bindec($bin_part)));

            if ($char == " ") {
               /* setelah space adalah overhead info atau 
               LSB 16 piksel pertama*/
               break;

               /*[Tahap mendapatkan overhead info dan LSB]*/
            }

            $pesan_asli .= $char;
         }

         /*[Tahap pengembalian citra asli]*/

         imagedestroy($cover_photo);
         return $pesan_asli;
      } 
      catch (\Exception $e) {
         return "error_cover";
      }
   }


   /* NOTE fungsi ekstraksi: 
   format $bin_smessage = EMAIL+[space]+PASSWORD+[sapce]+OVERHEAD_INFO/LSB+0000000...(karna message < dr max_point jadinya 0000...)
   */


   //Tahap Mendapatkan Overhead info dan LSB 16 Piksel Pertama
   // <===============================Start===========================================>
   /*
   if ($min_point > 0) { //cek apakah ada Overhead Info
      $mulai = $i + 8;
      $overhead_info = substr($bin_message, $mulai, $min_point);
      $mulai = $mulai + $min_point;
      $key_LSB_asli = substr($bin_message, $mulai, 16);

      break;
   }
   else{ 
      $mulai = $i + 8;
      $key_LSB_asli = substr($bin_message, $mulai, 16);
      break;
   }
   */
   // <===============================End===========================================>



   //Tahap Pengembalian Citra Asli pada Ekstraksi Metode Histogram Shifting
   // <===============================Start===========================================>
   /*
   // set LSB asli ke 16 pixel pertama
   for ($y=0; $y < 1; $y++) { 
      for ($x=0; $x < 16; $x++) { 
         $rgb = imagecolorat($cover_photo, $x, $y);
         $r = ($rgb >> 16) & 0xFF;
         $g = ($rgb >> 8) & 0xFF;
         $b = $rgb & 0xFF;

         $newR = $this->integerToBin($r);
         // ganti LSB 16 pixel pertama dg yg LSB asli
         $newR[strlen($newR) - 1] = $key_LSB_asli[$x]; 
         $newR = bindec($newR);

         $newColor = imagecolorallocate($cover_photo, $newR, $g, $b);
         imagesetpixel($cover_photo, $x, $y, $newColor);
      }
   }

   //Shift Back 
   $index = 0;
   if ($peak < $zero) { //jika max index lbh kecil, geser ke kiri
      for ($y=0; $y < $height; $y++) { 
         for ($x=0; $x < $width; $x++) { 
            $rgb = imagecolorat($cover_photo, $x, $y);
            $r = ($rgb >> 16) & 0xFF;
            $g = ($rgb >> 8) & 0xFF;
            $b = $rgb & 0xFF;

            if ($r > $peak && $r < $zero) {
               $newR = $r - 1;
               $newColor = imagecolorallocate($cover_photo, $newR, $g, $b);
               imagesetpixel($cover_photo, $x, $y, $newColor);
            }
            elseif ($r == $zero) {
               if ((int)$overhead_info[$index] == 1) {
                  $newR = $r - 1;
                  $newColor = imagecolorallocate($cover_photo, $newR, $g, $b);
                  imagesetpixel($cover_photo, $x, $y, $newColor);    
               }
               $index++;
            }
         }
      }
   }
   else {
      for ($y=0; $y < $height; $y++) { //jika max index lbh besar, geser ke kanan
         for ($x=0; $x < $width; $x++) { 
            $rgb = imagecolorat($cover_photo, $x, $y);
            $r = ($rgb >> 16) & 0xFF;
            $g = ($rgb >> 8) & 0xFF;
            $b = $rgb & 0xFF;

            if ($r < $peak && $r > $zero) {
               $newR = $r + 1;
               $newColor = imagecolorallocate($cover_photo, $newR, $g, $b);
               imagesetpixel($cover_photo, $x, $y, $newColor);
            }
            elseif ($r == $zero) {
               if ((int)$overhead_info[$index] == 1) {
                  $newR = $r + 1;
                  $newColor = imagecolorallocate($cover_photo, $newR, $g, $b);
                  imagesetpixel($cover_photo, $x, $y, $newColor);    
               }
               $index++;
            }
         }
      }
   }
   */
   // <===============================End===========================================>


   // $histogram = $this->makeHistogram($cover_photo);
   // for ($i=0; $i < 256; $i++) { 
   //    echo $histogram[$i]." ";
   // }
   // echo "<br>";
   // die();
}
