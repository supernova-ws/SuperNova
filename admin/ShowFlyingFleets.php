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

// if ($user['authlevel'] < 2)
if ($user['authlevel'] < 3)
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
  if ($CurrentFleet['fleet_mission'] == MT_EXPLORE)
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

// ----------------------------------------------------------------------------------------------------------------
//
// 
function CreateFleetPopupedFleetLink($FleetRow, $Texte, $FleetType, $Owner)
{
  global $lang, $user;

  $spy_tech = GetSpyLevel($user);
  $admin = $user['authlevel'];
  $FleetRec = explode(";", $FleetRow['fleet_array']);
  $FleetPopup = "<span onmouseover=\"popup_show('";
  $FleetPopup .= "<table width=200>";
  if (!$Owner && $spy_tech < 2)
  {
    $FleetPopup .= "<tr><td width=80% align=left><font color=white>" . $lang['ov_spy_failed'] . "<font></td><td width=20% align=right>&nbsp;</td></tr>";
  }
  elseif (!$Owner && $spy_tech < 4)
  {
    $FleetPopup .= "<tr><td width=80% align=left><font color=white>" . $lang['ov_total'] . ":<font></td><td width=20% align=right><font color=white>" . pretty_number(count($FleetRec)) . "<font></td></tr>";
  }
  foreach ($FleetRec as $Item => $Group)
  {
    if ($Group != '')
    {
      $Ship = explode(",", $Group);
      if (!$Owner && $spy_tech >= 4 && $spy_tech < 8)
      {
        $FleetPopup .= "<tr><td width=80% align=left><font color=white>" . $lang['tech'][$Ship[0]] . "<font></td><td width=20% align=right>&nbsp;</td></tr>";
      }
      elseif ((!$Owner && $spy_tech >= 8) || $Owner)
      {
        $FleetPopup .= "<tr><td width=80% align=left><font color=white>" . $lang['tech'][$Ship[0]] . ":<font></td><td width=20% align=right><font color=white>" . pretty_number($Ship[1]) . "<font></td></tr>";
      }
    }
  }
  if (!$Owner && $admin == 3)
  {
    $FleetPopup .= "<tr><td width=80% align=left><font color=white>" . $lang['tech'][$Ship[0]] . ":<font></td><td width=20% align=right><font color=white>" . pretty_number($Ship[1]) . "<font></td></tr>";
    $FleetPopup .= "<td width=100% align=center><font color=red>Все видящее Админское око :-D<font></td>";
  }
  $FleetPopup .= "</table>";
  $FleetPopup .= "');\" onmouseout=\"popup_hide();\" class=\"" . $FleetType . "\">" . $Texte . "</span>";

  return $FleetPopup;
}

// ----------------------------------------------------------------------------------------------------------------
//
// CГ©ation du lien avec popup pour le type de mission avec ou non les ressources si disponibles
function CreateFleetPopupedMissionLink($FleetRow, $Texte, $FleetType)
{
  global $lang;

  $FleetTotalC = $FleetRow['fleet_resource_metal'] + $FleetRow['fleet_resource_crystal'] + $FleetRow['fleet_resource_deuterium'];
  if ($FleetTotalC <> 0)
  {
    $FRessource = "<table width=200>";
    $FRessource .= "<tr><td width=50% align=left><font color=white>" . $lang['Metal'] . "<font></td><td width=50% align=right><font color=white>" . pretty_number($FleetRow['fleet_resource_metal']) . "<font></td></tr>";
    $FRessource .= "<tr><td width=50% align=left><font color=white>" . $lang['Crystal'] . "<font></td><td width=50% align=right><font color=white>" . pretty_number($FleetRow['fleet_resource_crystal']) . "<font></td></tr>";
    $FRessource .= "<tr><td width=50% align=left><font color=white>" . $lang['Deuterium'] . "<font></td><td width=50% align=right><font color=white>" . pretty_number($FleetRow['fleet_resource_deuterium']) . "<font></td></tr>";
    $FRessource .= "</table>";
  }
  else
  {
    $FRessource = "";
  }

  if ($FRessource <> "")
  {
    $MissionPopup = "<a href='#' onmouseover=\"popup_show('" . $FRessource . "');";
    $MissionPopup .= "\" onmouseout=\"popup_hide();\" class=\"" . $FleetType . "\">" . $Texte . "</a>";
  }
  else
  {
    $MissionPopup = $Texte . "";
  }

  return $MissionPopup;
}

?>
