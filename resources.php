<?php

/**
 * resources.php
 *
 * 1.0st - Security checks & tests by Gorlum for http://supernova.ws
 * @version 1.0
 * @copyright 2008 by Chlorel for XNova
 */

define('INSIDE'  , true);
define('INSTALL' , false);

$ugamela_root_path = './';
include($ugamela_root_path . 'extension.inc');
include($ugamela_root_path . 'common.' . $phpEx);

if ($IsUserChecked == false) {
  includeLang('login');
  header("Location: login.php");
}

check_urlaubmodus ($user);
  includeLang('resources');

  if (IsVacationMode($CurrentUser)){
    message($lang['Vacation_mode'], $lang['Error'], "overview.php", 1);
  }

      BuildRessourcePage ( $user, $planetrow );

// -----------------------------------------------------------------------------------------------------------
// History version
// 1.0 - Passage en fonction pour utilisation XNova
?>