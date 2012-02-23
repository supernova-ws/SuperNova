<?php

/**
 * overview.php
 *
 * @version 1.0s - Security checked for SQL-injection by Gorlum for http://supernova.ws
 * @version 1.0
 * @copyright 2008 by ??????? for XNova
 */

define('INSIDE'  , true);
define('INSTALL' , false);
define('IN_ADMIN', true);

require('../common.' . substr(strrchr(__FILE__, '.'), 1));

if($user['authlevel'] < 1)
{
  AdminMessage($lang['adm_err_denied']);
}

$GET_cmd  = sys_get_param_str('cmd');
$TypeSort = sys_get_param_str('type', 'id');
/*
if ($GET_cmd == 'sort') {
} else {
  $TypeSort = "id";
}
*/
$PageTPL  = gettemplate('admin/overview_body');
$RowsTPL  = gettemplate('admin/overview_rows');

$parse                      = $lang;
$parse['dpath']             = $dpath;

$Last15Mins = doquery("SELECT * FROM {{users}} WHERE `onlinetime` >= '". (time() - 15 * 60) ."' ORDER BY user_as_ally, `". $TypeSort ."` ASC;");
$Count      = 0;
$Color      = "lime";
while ( $TheUser = mysql_fetch_assoc($Last15Mins) ) {
  if ($PrevIP != "") {
    if ($PrevIP == $TheUser['user_lastip']) {
      $Color = "red";
    } else {
      $Color = "lime";
    }
  }

//  $UserPoints = doquery("SELECT * FROM {{statpoints}} WHERE `stat_type` = '1' AND `stat_code` = '1' AND `id_owner` = '" . $TheUser['id'] . "';", '', true);
  $Bloc['dpath']               = $dpath;
  $Bloc['adm_ov_altpm']        = $lang['adm_ov_altpm'];
  $Bloc['adm_ov_wrtpm']        = $lang['adm_ov_wrtpm'];
  $Bloc['adm_ov_data_id']      = $TheUser['id'];
  $Bloc['adm_ov_data_name']    = ($TheUser['username']);
  $Bloc['adm_ov_data_agen']    = htmlentities($TheUser['user_agent']);
  $Bloc['adm_ov_data_clip']    = $Color;
//  $Bloc['adm_ov_data_adip']    = $TheUser['user_lastip'];
  $Bloc['adm_ov_data_ally']    = ($TheUser['ally_name']);
  $Bloc['adm_ov_data_point']   = pretty_number ( $TheUser['total_points'] );
  $Bloc['adm_ov_data_activ']   = pretty_time ( time() - $TheUser['onlinetime'] );
  $PrevIP                      = ($TheUser['user_lastip']);

  $parse['adm_ov_data_table'] .= parsetemplate( $RowsTPL, $Bloc );
  $Count++;
}

$parse['adm_ov_data_count']  = $Count;
$Page = parsetemplate($PageTPL, $parse);

display ( $Page, $lang['sys_overview'], false, '', true);

?>
