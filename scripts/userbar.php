<?php
/**
* userbar.php
* @version 1.0
*
* @copyright 2010 by Gorlum for http://supernova.ws
*
* modified version of
* CreateBanner.php
*
* @version 1.2 by Ihor
* @version 1.0
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

  // $img_name = '../images/userbar.png';
  $img_name = $config->mod_userBar_barLocation;
  $size = getimagesize($img_name);
  $im = imagecreatefrompng($img_name);
  $image = imagecreatetruecolor($size[0],$size[1]);
  imagecopy($image,$im,0,0,0,0,$size[0],$size[1]);
  imagedestroy($im);

  // Querys
  $Player = doquery("SELECT * FROM {{table}} WHERE `id` = '".$id."';", 'users', true);
  $Stats = doquery("SELECT * FROM {{table}} WHERE `stat_type` = '1' AND `stat_code` = '1' AND `id_owner` = '".$id."';", 'statpoints', true);
  $Planet = doquery("SELECT * FROM {{table}} WHERE `id_owner` = '".$id."' AND `planet_type` = '1' LIMIT 1;", 'planets', true);

  // Variables
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

  // Userbar 350 x 19
  // oGame.Triolan.COM.UA
  // $b_univ = 'OGame.Triolan.COM.UA';
  $b_univ = $config->game_name;
  $is = imagettfbbox(9, 0, "ARIALBD.TTF", $b_univ);
  $is = 348-$is[2];
  imagettftext($image, 9, 0, $is, 15, $txt_shadow, "ARIALBD.TTF", $b_univ);
  imagettftext($image, 9, 0, $is-1, 14, $txt_color, "ARIALBD.TTF", $b_univ);

  imagettftext($image, 22, 0, $is-8, 19, $txt_color, "ARIALBD.TTF", '/');

  // Player name
  imagettftext($image, 9, 0, 4, 15, $txt_shadow, "ARIALBD.TTF", $b_user);
  imagettftext($image, 9, 0, 2, 13, $txt_color, "ARIALBD.TTF", $b_user);

  // Player level - right-alligned
  $isp = imagettfbbox(9, 0, "ARIALBD.TTF", $b_lvl);
  imagettftext($image, 9, 0, $is-$isp[2]-10, 15, $txt_shadow, "ARIALBD.TTF", $b_lvl);
  imagettftext($image, 9, 0, $is-$isp[2]-1-10, 14, $txt_color, "ARIALBD.TTF", $b_lvl);

  //And now convert it back to PNG-8
  $im_result = imagecreate($size[0],$size[1]);
  imagecopy($im_result,$image,0,0,0,0,$size[0],$size[1]);
  imagedestroy($image);
  //And save it
  imagepng($im_result);
}
?>
