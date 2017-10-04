<?php
// Captcha code for registration - really didn't work alot :(

session_start();
$en = 100;
$boy = 25;
$sayi = mt_rand(0, 9999999);
$_SESSION['captcha'] = $sayi;
$tuval = imagecreatetruecolor($en, $boy);
$b = imagecolorallocate($tuval, 175, 238, 238);
$s = imagecolorallocate($tuval, 0, 0, 0);
imagefill($tuval, 0, 0, $s);
imageline($tuval, 20, 50, $en, $boy, $b);
imagestring($tuval, 3, 27, 7, $sayi, $b);
header("content-type:image/gif");
imagegif($tuval);
imagedestroy($tuval);
