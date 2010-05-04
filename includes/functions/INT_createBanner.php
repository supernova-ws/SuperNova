<?php
/**
* INT_createBanner.php
* @version 1.0
*
* @copyright 2010 by Gorlum for http://supernova.ws
*
* heavily based on
*   CreateBanner.php
*   @version 1.2 by Ihor
*   @version 1.0 copyright 2008 By e-Zobar for XNova
*/

function INT_createBanner($id, $type = 'userbar', $format = 'png'){
// banner.php?id=<userid>&type=<banner|userbar>&format=<png>
  global $config, $lang, $ugamela_root_path;

  if (empty($id)) exit;

  switch ($type) {
    case 'banner':
      $img_name = $config->int_banner_background;
      break;
    default:
      $img_name = $config->int_userbar_background;
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

  $fonts = array (
    'userbar' => $ugamela_root_path . "fonts/" . $config->int_userbar_font,
    'universe' => $ugamela_root_path . "fonts/" . $config->int_banner_fontUniverse,
    'raids' => $ugamela_root_path . "fonts/" . $config->int_banner_fontRaids,
    'info' => $ugamela_root_path . "fonts/" . $config->int_banner_fontInfo,
  );

  $b_univ = $config->game_name;
  switch ($type) {
    case 'banner':
      // Banner size 416 x 58
      $fsize = 15;

      $is = imagettfbbox($fsize, 0, $fonts['universe'], $b_univ);
      imagettftext($image, $fsize, 0, $size[0] - 4 - $is[2], $size[1] - 2, $txt_shadow, $fonts['universe'], $b_univ);
      imagettftext($image, $fsize, 0, $size[0] - 6 - $is[2], $size[1] - 4, $txt_color, $fonts['universe'], $b_univ);

      // Player name
      imagettftext($image, 11, 0, 8, 26, $txt_shadow, $fonts['info'], $b_user);
      imagettftext($image, 11, 0, 6, 23, $txt_color, $fonts['info'], $b_user);

      // Player level - right-alligned
      $is = imagettfbbox(11, 0, $fonts['info'], $b_lvl);
      imagettftext($image, 11, 0, $size[0] - 4 - $is[2], 25, $txt_shadow, $fonts['info'], $b_lvl);
      imagettftext($image, 11, 0, $size[0] - 6 - $is[2], 23, $txt_color, $fonts['info'], $b_lvl);

      // Ally name
      $is = imagettfbbox(9, 0, $fonts['info'], $b_ally);
      imagettftext($image, 9, 0, 412-$is[2], 37, $txt_shadow, $fonts['info'], $b_ally);
      imagettftext($image, 9, 0, 410-$is[2], 35, $txt_color, $fonts['info'], $b_ally);

      // Player b_planet
      imagettftext($image, 6, 0, 8, 10, $txt_shadow2, $fonts['raids'], $b_planet." ".$b_xyz."");
      imagettftext($image, 6, 0, 6, 9, $txt_color2, $fonts['raids'], $b_planet." ".$b_xyz."");

      //StatPoint
      $b_points = $lang['Points'].": ".pretty_number($Stats['total_points'])."";
      $is = imagettfbbox(8, 0, $fonts['info'], $b_points);
      imagettftext($image, 8, 0, 412-$is[2], 11, $txt_shadow, $fonts['info'], $b_points);
      imagettftext($image, 8, 0, 410-$is[2], 9, $txt_color, $fonts['info'], $b_points);

      //Raids Total
      imagettftext($image, 6, 0, 8, 37, $txt_shadow2, $fonts['raids'], $lang['Raids']);
      imagettftext($image, 6, 0, 6, 35, $txt_color2, $fonts['raids'], $lang['Raids']);
      $b_points = ": ".pretty_number($Player['raids']);
      imagettftext($image, 6, 0, 61, 37, $txt_shadow2, $fonts['raids'], $b_points);
      imagettftext($image, 6, 0, 59, 35, $txt_color2, $fonts['raids'], $b_points);

      //Raids Won
      imagettftext($image, 6, 0, 8, 47, $txt_shadow2, $fonts['raids'], $lang['RaidsWin']);
      imagettftext($image, 6, 0, 6, 45, $txt_color2, $fonts['raids'], $lang['RaidsWin']);
      $b_points = ": ".pretty_number($Player['raidswin']);
      imagettftext($image, 6, 0, 61, 47, $txt_shadow2, $fonts['raids'], $b_points);
      imagettftext($image, 6, 0, 59, 45, $txt_color2, $fonts['raids'], $b_points);

      //Raids Lost
      imagettftext($image, 6, 0, 8, 57, $txt_shadow2, $fonts['raids'], $lang['RaidsLoose']);
      imagettftext($image, 6, 0, 6, 55, $txt_color2, $fonts['raids'], $lang['RaidsLoose']);
      $b_points = ": ".pretty_number($Player['raidsloose']);
      imagettftext($image, 6, 0, 61, 57, $txt_shadow2, $fonts['raids'], $b_points);
      imagettftext($image, 6, 0, 59, 55, $txt_color2, $fonts['raids'], $b_points);

      break;
    default:
      // Userbar 350 x 19
      $b_univ = strtoupper($b_univ);
      $is = imagettfbbox(9, 0, $fonts['userbar'], $b_univ);
      $is = $size[0] - $is[2] - 2;
      imagettftext($image, 9, 0, $is, $size[1] - 3, $txt_shadow, $fonts['userbar'], $b_univ);
      imagettftext($image, 9, 0, $is-1, $size[1] - 5, $txt_color, $fonts['userbar'], $b_univ);
      imagettftext($image, 22, 0, $is-8, $size[1], $txt_color, $fonts['userbar'], '/');

      // Player level - right-alligned
      $isp = imagettfbbox(9, 0, $fonts['userbar'], $b_lvl);
      imagettftext($image, 9, 0, $is-$isp[2] - 10, $size[1] - 4, $txt_shadow, $fonts['userbar'], $b_lvl);
      imagettftext($image, 9, 0, $is-$isp[2] - 10 - 1, $size[1] - 4 - 1, $txt_color, $fonts['userbar'], $b_lvl);

      // Player name
      imagettftext($image, 9, 0, 4, $size[1] - 4, $txt_shadow, $fonts['userbar'], $b_user);
      imagettftext($image, 9, 0, 2, $size[1] - 6, $txt_color, $fonts['userbar'], $b_user);
  }

  //And now convert it back to PNG-8
  $im_result = imagecreate($size[0],$size[1]);
  imagecopy($im_result,$image,0,0,0,0,$size[0],$size[1]);
  imagedestroy($image);
  //And save it
  header ("Content-type: image/png");
  imagepng($im_result);
  imagedestroy($im_result);
}
?>
