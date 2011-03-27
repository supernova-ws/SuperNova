<?php
/**
 * dark_matter.php
 *
 * Adjust Dark Matter quantity
 *
 * @version 1.0 (c) copyright 2010 by Gorlum for http://supernova.ws
 *
*/

define('INSIDE'  , true);
define('INSTALL' , false);
define('IN_ADMIN', true);

require('../common.' . substr(strrchr(__FILE__, '.'), 1));

if($user['authlevel'] < 3)
{
  AdminMessage($lang['adm_err_denied']);
}

$mode      = $_POST['mode'];
$PageTpl   = gettemplate("admin/admin_darkmatter", true);
$parse     = $lang;

$id_planet = SYS_mysqlSmartEscape($_POST['id_planet']);
$id_user   = SYS_mysqlSmartEscape($_POST['id_user']);
$points    = intval($_POST['points']);
$reason    = $_POST['reason'];

if($points){ // If points not empty...
  if($id_user){
    if(is_numeric($id_user))
      $queryPart = " or `id` = {$id_user}";
    $query = doquery("SELECT id, username FROM {{users}} WHERE `username` like '{$id_user}'" . $queryPart);
    switch (mysql_num_rows($query)){
      case 0: // Error - no such ID or username
        $message = sprintf($lang['adm_dm_user_none'], $id_user);
        break;
      case 1: // Proceeding normal - only one user exists
        $row = mysql_fetch_assoc($query);
        // Does anything post to DB?
        if(rpg_points_change($row['id'], $points, "Through admin interface for user {$row['username']} ID {$row['id']} " . $reason)){
          $message = sprintf($lang['adm_dm_user_added'], $row['username'], $row['id'], $points);
          $isNoError = true;
        }else // No? We will say it to user...
          $message = $lang['adm_dm_add_err'];
        break;
      default:// There too much results - can't apply
        $message = $lang['adm_dm_user_conflict'];
        break;
    }
  }elseif($id_planet){ // id_user is not set. Trying id_planet
    $error_id = 'adm_dm_planet_conflict_name';
    if(is_numeric($id_planet)){
      $queryPart = " or `id` = {$id_planet}";
      $error_id = 'adm_dm_planet_conflict_id';
    };
    if(preg_match(PLANET_COORD_PREG, $id_planet, $preg)){
      $queryPart = " or (`galaxy` = {$preg[1]} and `system` = {$preg[2]} and `planet` = {$preg[3]} and `planet_type` = 1)";
      $error_id = 'adm_dm_planet_conflict_coords';
    };

    $query = doquery("SELECT id, name, id_owner, galaxy, system, planet FROM {{planets}} WHERE `name` like '{$id_planet}'" . $queryPart);
    switch (mysql_num_rows($query)){
      case 0: // Error - no such planet ID or name or coordinates
        $message = sprintf($lang['adm_dm_planet_none'], $id_planet);
        break;
      case 1: // Proceeding normal - only one user exists
        $row = mysql_fetch_assoc($query);
        if(rpg_points_change($row['id_owner'], $points, "Through admin interface to planet '{$row['name']} ID: {$row['id']} for user ID: {$row['id_owner']} " . $reason)){
          $message = sprintf($lang['adm_dm_planet_added'], $row['id_owner'], $row['name'], $row['id'], INT_makeCoordinates($row), $points);
          $isNoError = true;
        }else
          $message = $lang['adm_dm_add_err'];
        break;
      default:// There too much results - can't apply
        $message = $lang['adm_dm_planet_conflict'] . sprintf($lang[$error_id], mb_strtoupper($id_planet));
        break;
    }
  }else // Points not empty but destination is not set - this means error
    $message = $lang['adm_dm_no_dest'];

}elseif($id_user || $id_planet) // Points is empty but destination is set - this again means error
  $message = $lang['adm_dm_no_quant'];

$parse['message'] = $message . "<br><br>";
if(!$isNoError){
  $parse['id_planet'] = $id_planet;
  $parse['id_user'] = $id_user;
  $parse['points'] = $points;
  $parse['reason'] = $reason;
};

$Page = parsetemplate($PageTpl, $parse);
display ($Page, $lang['adm_dm_title'], false, '', true);

?>
