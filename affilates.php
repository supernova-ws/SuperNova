<?php

/**
 * affilates.php
 *
 * v2 (c) copyright 2010 by Gorlum for http://supernova.ws
 *  [~] Complies with PCG1
 * v1 (c) copyright 2010 by Gorlum for http://supernova.ws
 */

$ugamela_root_path = (defined('SN_ROOT_PATH')) ? SN_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
require_once("{$ugamela_root_path}common.{$phpEx}");

includeLang('affilates');

$template = gettemplate('affilates', true);

$rpg_bonus_divisor = $config->rpg_bonus_divisor ? $config->rpg_bonus_divisor : 10;

$affilates = doquery("SELECT r.*, u.username, u.register_time FROM {{referrals}} AS r LEFT JOIN {{users}} AS u ON u.id = r.id WHERE id_partner = {$user['id']};");
while ($affilate = mysql_fetch_assoc($affilates))
{
  $affilate_gain = floor($affilate['dark_matter']/$rpg_bonus_divisor);

  $template->assign_block_vars('affilates', array(
    'REGISTERED'  => date(FMT_DATE_TIME, $affilate['register_time']),
    'USERNAME'    => $affilate['username'],
    'DARK_MATTER' => $affilate['dark_matter'],
    'GAINED'      => $affilate_gain,
  ));

  $gained += $affilate_gain;
}

$bannerURL  = SN_ROOT_VIRTUAL . $config->int_banner_URL;
$bannerURL .= strpos($bannerURL, '?') ? '&' : '?';
$bannerURL .= "id={$user['id']}";

$userbarURL  = SN_ROOT_VIRTUAL . $config->int_userbar_URL;
$userbarURL .= strpos($userbarURL, '?') ? '&' : '?';
$userbarURL .= "id={$user['id']}";

$template->assign_vars(array(
  'GAINED'     => $gained,
  'dpath'      => $dpath,
  'user_id'    => $user['id'],
  'serverURL'  => SN_ROOT_VIRTUAL,
  'bannerURL'  => $bannerURL,
  'userbarURL' => $userbarURL,
));

display(parsetemplate($template), $lang['aff_title']);

?>
