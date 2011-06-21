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

require('../common.' . substr(strrchr(__FILE__, '.'), 1));

if($user['authlevel'] < 3)
{
  AdminMessage($lang['adm_err_denied']);
}

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
    $sq      = doquery("SELECT `id` FROM {{users}}");
    $Time    = time();
    $From    = "<font color=\"". $kolor ."\">". $ranga ." ".$user['username']."</font>";
    $Subject = "<font color=\"". $kolor ."\">". $config->temat ."</font>";
    $Message = "<font color=\"". $kolor ."\"><b>". $config->tresc ."</b></font>";
    while ($u = mysql_fetch_assoc($sq)) {
      msg_send_simple_message ($u['id'], $user['id'], $Time, MSG_TYPE_ADMIN, $From, $Subject, $Message);
    }
    message("<font color=\"lime\">Wys�a�e� wiadomo�� do wszystkich graczy</font>", "Complete", "../overview." . PHP_EX, 3);
  }
} else {
  $parse['dpath'] = $dpath;
  $parse['debug'] = ($config->debug == 1) ? " checked='checked'/":'';
  $page .= parsetemplate(gettemplate('admin/messall_body'), $parse);
  display($page, '', false,'', true);
}

?>
