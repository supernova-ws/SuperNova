<?php
/**
* CreateBanner.php
*
* @version 1.0
* @version 1.2 by Ihor
* @copyright 2008 By e-Zobar for XNova
*/
// Function to center text in the created banner
function CenterTextBanner($z,$y,$zone) {
  $a = strlen($z);
  $b = imagefontwidth($y);
  $c = $a*$b;
  $d = $zone-$c;
  $e = $d/2;
  return $e;
}

define('INSIDE' , true);
define('INSTALL' , false);
$ugamela_root_path = '../';
include($ugamela_root_path . 'extension.inc');
include($ugamela_root_path . 'common.' . $phpEx);
includeLang('overview');

$id = intval($_GET['id']);
if (!empty($id)) {
// Parameters
header ("Content-type: image/png");
$size = getimagesize($game_config['banner_source_post']);

//$image = imagecreatefrompng($game_config['banner_source_post']);

$im = imagecreatefrompng($game_config['banner_source_post']);
$image = imagecreatetruecolor($size[0],$size[1]);
imagecopy($image,$im,0,0,0,0,$size[0],$size[1]);
imagedestroy($im);

$date = date("d/m/y");
// Querys
$Player = doquery("SELECT * FROM {{table}} WHERE `id` = '".$id."';", 'users', true);
$Stats = doquery("SELECT * FROM {{table}} WHERE `stat_type` = '1' AND `stat_code` = '1' AND `id_owner` = '".$id."';", 'statpoints', true);
$Planet = doquery("SELECT * FROM {{table}} WHERE `id_owner` = '".$id."' AND `planet_type` = '1' LIMIT 1;", 'planets', true);
// Variables
$b_univ = $game_config['game_name'];
$b_user = $Player['username'];
$b_ally = $Player['ally_name'];
$b_planet = $Planet['name'];
$b_xyz = "[".$Planet['galaxy'].":".$Planet['system'].":".$Planet['planet']."]";
$b_lvl = "".$Stats['total_rank']."/".$game_config['users_amount']."";
// Colors
$color = "FFFFFF";
$red = hexdec(substr($color,0,2));
$green = hexdec(substr($color,2,4));
$blue = hexdec(substr($color,4,6));
$select = imagecolorallocate($image,$red,$green,$blue);
$txt_shadow = imagecolorallocatealpha($image, 255, 255, 255, 255);
$txt_color = imagecolorallocatealpha($image, 255, 255, 255, 2);
$txt_shadow2 = imagecolorallocatealpha($image, 255, 255, 255, 255);
$txt_color2 = imagecolorallocatealpha($image, 255, 255, 255, 40);

// Banner 416 x 58
// Player level - right-alligned
$is = imagettfbbox(11, 0, "terminator.TTF", $b_lvl);
imagettftext($image, 11, 0, 412-$is[2], 25, $txt_shadow, "terminator.TTF", $b_lvl);
imagettftext($image, 11, 0, 410-$is[2], 23, $txt_color, "terminator.TTF", $b_lvl);

// Player name
imagettftext($image, 11, 0, 8, 26, $txt_shadow, "terminator.TTF", $b_user);
imagettftext($image, 11, 0, 6, 23, $txt_color, "terminator.TTF", $b_user);

// Ally name
$is = imagettfbbox(9, 0, "terminator.TTF", $b_ally);
imagettftext($image, 9, 0, 412-$is[2], 37, $txt_shadow, "terminator.TTF", $b_ally);
imagettftext($image, 9, 0, 410-$is[2], 35, $txt_color, "terminator.TTF", $b_ally);

//imagettftext($image, 13, 0, 165, 57, $txt_shadow, "NewZelek.ttf", $b_univ);
//imagettftext($image, 13, 0, 162, 54, $txt_color, "NewZelek.ttf", $b_univ);
// Player b_planet
imagettftext($image, 6, 0, 8, 10, $txt_shadow2, "KLMNFP2005.ttf", $b_planet." ".$b_xyz."");
imagettftext($image, 6, 0, 6, 9, $txt_color2, "KLMNFP2005.ttf", $b_planet." ".$b_xyz."");

//StatPoint
$b_points = $lang['Points'].": ".pretty_number($Stats['total_points'])."";
$is = imagettfbbox(8, 0, "terminator.TTF", $b_points);
imagettftext($image, 8, 0, 412-$is[2], 11, $txt_shadow, "terminator.TTF", $b_points);
imagettftext($image, 8, 0, 410-$is[2], 9, $txt_color, "terminator.TTF", $b_points);

//Raids Total
imagettftext($image, 6, 0, 8, 37, $txt_shadow2, "KLMNFP2005.ttf", $lang['Raids']);
imagettftext($image, 6, 0, 6, 35, $txt_color2, "KLMNFP2005.ttf", $lang['Raids']);
$b_points = ": ".pretty_number($Player['raids']);
imagettftext($image, 6, 0, 61, 37, $txt_shadow2, "KLMNFP2005.ttf", $b_points);
imagettftext($image, 6, 0, 59, 35, $txt_color2, "KLMNFP2005.ttf", $b_points);

//Raids Won
imagettftext($image, 6, 0, 8, 47, $txt_shadow2, "KLMNFP2005.ttf", $lang['RaidsWin']);
imagettftext($image, 6, 0, 6, 45, $txt_color2, "KLMNFP2005.ttf", $lang['RaidsWin']);
$b_points = ": ".pretty_number($Player['raidswin']);
imagettftext($image, 6, 0, 61, 47, $txt_shadow2, "KLMNFP2005.ttf", $b_points);
imagettftext($image, 6, 0, 59, 45, $txt_color2, "KLMNFP2005.ttf", $b_points);

//Raids Lost
imagettftext($image, 6, 0, 8, 57, $txt_shadow2, "KLMNFP2005.ttf", $lang['RaidsLoose']);
imagettftext($image, 6, 0, 6, 55, $txt_color2, "KLMNFP2005.ttf", $lang['RaidsLoose']);
$b_points = ": ".pretty_number($Player['raidsloose']);
imagettftext($image, 6, 0, 61, 57, $txt_shadow2, "KLMNFP2005.ttf", $b_points);
imagettftext($image, 6, 0, 59, 55, $txt_color2, "KLMNFP2005.ttf", $b_points);



//And now convert it back to PNG-8
$im_result = imagecreate($size[0],$size[1]);
imagecopy($im_result,$image,0,0,0,0,$size[0],$size[1]);
imagedestroy($image);
//And save it
imagepng($im_result);


// Creat and delete banner
//imagepng ($image);
//imagedestroy ($image);
}
?>
