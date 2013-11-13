<?php

/**
 * dark_matter.php
 *
 * Adjust Dark Matter quantity
 *
 * @version 1.0 (c) copyright 2010 by Gorlum for http://supernova.ws
 *
 */
define('INSIDE', true);
define('INSTALL', false);
define('IN_ADMIN', true);

require('../common.' . substr(strrchr(__FILE__, '.'), 1));

if(!sn_module_get_active_count('payment'))
{
  sys_redirect(SN_ROOT_VIRTUAL . 'admin/overview.php');
}

if($user['authlevel'] < 3)
{
  AdminMessage($lang['adm_err_denied']);
}

$parse = $lang;
$PageTpl = gettemplate("admin/adm_metamatter", true);

$mode      = sys_get_param_str('mode');
$id_user   = sys_get_param_str('id_user');
$points    = sys_get_param_float('points');
$reason    = $_POST['reason'];

if($points)
{ // If points not empty...
  if($id_user)
  {
    if(is_numeric($id_user))
    {
      $queryPart = " or `id` = {$id_user}";
    }

    $query = doquery("SELECT id, username FROM {{users}} WHERE `username` like '{$id_user}'" . $queryPart);
    switch (mysql_num_rows($query))
    {
      case 0: // Error - no such ID or username
        $message = sprintf($lang['adm_mm_user_none'], $id_user);
      break;

      case 1: // Proceeding normal - only one user exists
        $row = mysql_fetch_assoc($query);
        // Does anything post to DB?
        if (mm_points_change($row['id'], RPG_ADMIN, $points, "Through admin interface for user {$row['username']} ID {$row['id']} " . $reason))
        {
          $message = sprintf($lang['adm_mm_user_added'], $row['username'], $row['id'], $points);
          $isNoError = true;
        }
        else // No? We will say it to user...
        {
          $message = $lang['adm_mm_add_err'];
        }
      break;

      default:// There too much results - can't apply
        $message = $lang['adm_mm_user_conflict'];
      break;
    }
  }
  else // Points not empty but destination is not set - this means error
  {
    $message = $lang['adm_mm_no_dest'];
  }
}
elseif($id_user) // Points is empty but destination is set - this again means error
{
  $message = $lang['adm_mm_no_quant'];
}

$parse['message'] = $message . "<br><br>";
if(!$isNoError)
{
  $parse['id_user']   = $id_user;
  $parse['points']    = $points;
  $parse['reason']    = $reason;
};

$Page = parsetemplate($PageTpl, $parse);
display($Page, $lang['adm_mm_title'], false, '', true);
