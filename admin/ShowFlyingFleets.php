<?php

/**
 * ShowFlyingFleets.php
 *
 * @version 1
 * @copyright 2008 By Chlorel for XNova
 */
define('INSIDE', true);
define('INSTALL', false);
define('IN_ADMIN', true);
require('../common.' . substr(strrchr(__FILE__, '.'), 1));

if ($user['authlevel'] < 2)
{
  AdminMessage($lang['adm_err_denied']);
}

$TableTPL = gettemplate('admin/fleet_rows');
$FlyingFleets = doquery("SELECT * FROM `{{fleets}}` ORDER BY `fleet_end_time` ASC;");
while ($CurrentFleet = mysql_fetch_assoc($FlyingFleets))
{
  $FleetOwner = doquery("SELECT `username` FROM `{{users}}` WHERE `id` = '" . $CurrentFleet['fleet_owner'] . "';", '', true);
  $TargetOwner = doquery("SELECT `username` FROM `{{users}}` WHERE `id` = '" . $CurrentFleet['fleet_target_owner'] . "';", '', true);
  $Bloc['Id'] = $CurrentFleet['fleet_id'];
  $Bloc['Mission'] = CreateFleetPopupedMissionLink($CurrentFleet, $lang['type_mission'][$CurrentFleet['fleet_mission']], '');
  $Bloc['Mission'] .= "<br>" . (($CurrentFleet['fleet_mess'] == 1) ? "R" : "A" );

  $Bloc['Fleet'] = CreateFleetPopupedFleetLink($CurrentFleet, $lang['tech'][200], '', $FleetOwner['username']);
  $Bloc['St_Owner'] = "[" . $CurrentFleet['fleet_owner'] . "]<br>" . $FleetOwner['username'];
  $Bloc['St_Posit'] = "[" . $CurrentFleet['fleet_start_galaxy'] . ":" . $CurrentFleet['fleet_start_system'] . ":" . $CurrentFleet['fleet_start_planet'] . "]<br>" . ( ($CurrentFleet['fleet_start_type'] == 1) ? "[P]" : (($CurrentFleet['fleet_start_type'] == 2) ? "D" : "L" )) . "";
  $Bloc['St_Time'] = date(FMT_DATE_TIME, $CurrentFleet['fleet_start_time']);
  if (is_array($TargetOwner))
  {
    $Bloc['En_Owner'] = "[" . $CurrentFleet['fleet_target_owner'] . "]<br>" . $TargetOwner['username'];
  }
  else
  {
    $Bloc['En_Owner'] = "";
  }
  $Bloc['En_Posit'] = "[" . $CurrentFleet['fleet_end_galaxy'] . ":" . $CurrentFleet['fleet_end_system'] . ":" . $CurrentFleet['fleet_end_planet'] . "]<br>" . ( ($CurrentFleet['fleet_end_type'] == 1) ? "[P]" : (($CurrentFleet['fleet_end_type'] == 2) ? "D" : "L" )) . "";
  if ($CurrentFleet['fleet_mission'] == 15)
  {
    $Bloc['Wa_Time'] = date(FMT_DATE_TIME, $CurrentFleet['fleet_stay_time']);
  }
  else
  {
    $Bloc['Wa_Time'] = "";
  }
  $Bloc['En_Time'] = date(FMT_DATE_TIME, $CurrentFleet['fleet_end_time']);

  $table .= parsetemplate($TableTPL, $Bloc);
}

$parse = $lang;
$parse['flt_table'] = $table;
$PageTPL = gettemplate('admin/fleet_body');
display(parsetemplate($PageTPL, $parse), $lang['flt_title'], false, '', true);

?>
