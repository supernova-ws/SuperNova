<?php

/**
 * viewreport.php
 *
 * @version 1
 * @copyright 2008 by MadnessRed
 * Created by Anthony for Darkness of Evolution
 */

$ugamela_root_path = (defined('SN_ROOT_PATH')) ? SN_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include("{$ugamela_root_path}common.{$phpEx}");

if ($IsUserChecked == false) {
  includeLang('login');
  header("Location: login.php");
}

  includeLang('viewreport');

  $BodyTPL = gettemplate('viewreport');
  $parse   = $lang;

  $page = parsetemplate($BodyTPL, $parse);
  display($page, $lang['vr_title'], false);

// -----------------------------------------------------------------------------------------------------------
// History version
// 1.0 - Created by MadnessRed
?>
