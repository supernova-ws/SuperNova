<?php
  if(empty($_GET) || !$_GET['module'])
  {
    ob_start();
    header('location: overview.php');
    ob_end_flush();
    die();
  }
/*
  require_once('common.' . substr(strrchr(__FILE__, '.'), 1));

  $template = gettemplate('affilates', true);

//  debug($sn_module);
//  debug($sn_menu_extra);

debug(mrc_get_level($user, false, MRC_STOCKMAN));
debug(mrc_get_level($user, false, UNIT_PREMIUM));

//debug($functions);

  display(parsetemplate($template), $lang['aff_title']);
*/
?>