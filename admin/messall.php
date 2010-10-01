<?php

/**
 * messall.php
 *
 * @version 1.0
 * @copyright 2008 by ??????? for XNova
 */

define('INSIDE'  , true);
define('INSTALL' , false);
define('IN_ADMIN', true);

$ugamela_root_path = './../';
include($ugamela_root_path . 'extension.inc');
include($ugamela_root_path . 'common.' . $phpEx);

if ($user['authlevel'] < 3)
{
  message( $lang['sys_noalloaw'], $lang['sys_noaccess'] );
  die();
}

  if ($user['authlevel'] >= 1) {
    if ($_POST && $mode == "change") {
      if (isset($_POST["tresc"]) && $_POST["tresc"] != '') {
        $config->tresc = $_POST['tresc'];
      }
      if (isset($_POST["temat"]) && $_POST["temat"] != '') {
        $config->temat = $_POST['temat'];
      }
      if ($user['authlevel'] == 3) {
        $kolor = 'red';
        $ranga = 'Administrator';
      } elseif ($user['authlevel'] == 4) {
        $kolor = 'skyblue';
        $ranga = 'GameOperator';
      } elseif ($user['authlevel'] == 5) {
        $kolor = 'yellow';
        $ranga = 'SuperGameOperator';
      }
      if ($config->tresc != '' and $config->temat) {
        $sq      = doquery("SELECT `id` FROM {{table}}", "users");
        $Time    = time();
        $From    = "<font color=\"". $kolor ."\">". $ranga ." ".$user['username']."</font>";
        $Subject = "<font color=\"". $kolor ."\">". $config->temat ."</font>";
        $Message = "<font color=\"". $kolor ."\"><b>". $config->tresc ."</b></font>";
        while ($u = mysql_fetch_array($sq)) {
          SendSimpleMessage ( $u['id'], $user['id'], $Time, 97, $From, $Subject, $Message);
        }
        message("<font color=\"lime\">Wys³a³e¶ wiadomo¶æ do wszystkich graczy</font>", "Complete", "../overview." . $phpEx, 3);
      }
    } else {
      $parse['dpath'] = $dpath;
      $parse['debug'] = ($config->debug == 1) ? " checked='checked'/":'';
      $page .= parsetemplate(gettemplate('admin/messall_body'), $parse);
      display($page, '', false,'', true);
    }
  } else {
    message($lang['sys_noalloaw'], $lang['sys_noaccess']);
  }
?>