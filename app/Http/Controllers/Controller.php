<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
	use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

	//Mengubah String ke Binary
	protected function stringToBin($str)
	{
		$str = (string)$str;
		$pjg = strlen($str);
		$res = '';
		while ($pjg--) {
			$res = str_pad(decbin(ord($str[$pjg])), 8, "0", STR_PAD_LEFT).$res;
		}
		return $res;
	}

	//Mengubah integer ke Binary
	protected function integerToBin($angka)
	{
		$bin_angka = str_pad(decbin($angka), 8, "0", STR_PAD_LEFT);
		
		return $bin_angka;
	}

	protected function makeHistogram($image)
   {
   	$histogram = [];
   	$width = imagesx($image);
    $height = imagesy($image);
    for ($i=0; $i <= 255; $i++) { 
      $histogram[$i] = 0;
    }

    for ($y=0; $y < $height; $y++) { 
      for ($x=0; $x < $width; $x++) { 
        $rgb = imagecolorat($image, $x, $y);
        $r = ($rgb >> 16) & 0xFF;
        $histogram[$r]++;
      }
    }

    return $histogram;
  }
}
