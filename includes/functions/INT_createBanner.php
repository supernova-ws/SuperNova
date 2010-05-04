<?php
/**
* INT_createBanner.php
* @version 1.0
*
* @copyright 2010 by Gorlum for http://supernova.ws
*
* hevily based on
*   CreateBanner.php
*   @version 1.2 by Ihor
*   @version 1.0 copyright 2008 By e-Zobar for XNova
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

function INT_createBanner($id, $type = 'userbar', $format = 'png'){
// banner.php?id=<userid>&type=<banner|userbar>&format=<png>

  if (empty($id)) exit;

  // Parameters
  header ("Content-type: image/png");

  // $img_name = '../images/userbar.png';
  switch ($type) {
    case 'banner':
      $img_name = $config->int_banner_background;
      break;
    default:
      $img_name = $config->int_userBar_background;
  }


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
  $b_lvl = "".$Stats['total_rank']."/".$config->users_amount."";

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


  switch ($type) {
    case 'banner':
      // Banner 416 x 58

      // Player name
      imagettftext($image, 11, 0, 8, 26, $txt_shadow, "terminator.TTF", $b_user);
      imagettftext($image, 11, 0, 6, 23, $txt_color, "terminator.TTF", $b_user);

      // Player level - right-alligned
      $is = imagettfbbox(11, 0, "terminator.TTF", $b_lvl);
      imagettftext($image, 11, 0, 412-$is[2], 25, $txt_shadow, "terminator.TTF", $b_lvl);
      imagettftext($image, 11, 0, 410-$is[2], 23, $txt_color, "terminator.TTF", $b_lvl);

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

      break;
    default:
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
  }

  //And now convert it back to PNG-8
  $im_result = imagecreate($size[0],$size[1]);
  imagecopy($im_result,$image,0,0,0,0,$size[0],$size[1]);
  imagedestroy($image);
  //And save it
  imagepng($im_result);
  imagedestroy ($im_result);
}
?>
