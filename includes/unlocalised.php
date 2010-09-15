<?php
/**
 * unlocalised.php
 *
 * @version 1
 * @copyright 2008 By Chlorel for XNova
 */

// ----------------------------------------------------------------------------------------------------------------
//
// Routine pour la gestion de flottes a envoyer
//

// Calcul de la distance entre 2 planetes
function GetTargetDistance ($OrigGalaxy, $DestGalaxy, $OrigSystem, $DestSystem, $OrigPlanet, $DestPlanet) {
  if (($OrigGalaxy - $DestGalaxy) != 0) {
    $distance = abs($OrigGalaxy - $DestGalaxy) * 20000;
  } elseif (($OrigSystem - $DestSystem) != 0) {
    $distance = abs($OrigSystem - $DestSystem) * 5 * 19 + 2700;
  } elseif (($OrigPlanet - $DestPlanet) != 0) {
    $distance = abs($OrigPlanet - $DestPlanet) * 5 + 1000;
  } else {
    $distance = 5;
  }

  return $distance;
}

// Calcul de la durГ©e de vol d'une flotte par rapport a sa vitesse max
function GetMissionDuration ($GameSpeed, $MaxFleetSpeed, $Distance, $SpeedFactor) {
  $Duration = round(((35000 / $GameSpeed * sqrt($Distance * 10 / $MaxFleetSpeed) + 10) / $SpeedFactor));

  return $Duration;
}

// Retourne la valeur ajustГ©e de vitesse des flottes
function GetGameSpeedFactor () {
  global $config;

  return $config->fleet_speed / 2500;
}

function get_ship_speed($ship_id, $user)
{
  global $resource, $reslist, $pricelist;

  if($pricelist[$ship_id]['tech_level'] && $user[$resource[$pricelist[$ship_id]['tech2']]] >= $pricelist[$ship_id]['tech_level'])
  {
    $speed = $pricelist[$ship_id]['speed2'];
    $tech  = $pricelist[$ship_id]['tech2'];
  }
  else
  {
    $speed = $pricelist[$ship_id]['speed'];
    $tech  = $pricelist[$ship_id]['tech'];
  }

  $speed *= 1 + $user[$resource[$tech]] * $pricelist[$tech]['speed_increase'];
  $speed *= 1 + $user['rpg_general'] * 0.25;

  return $speed;
}

// ----------------------------------------------------------------------------------------------------------------
// Calcul de la vitesse de la flotte par rapport aux technos du joueur
// Avec prise en compte
function GetFleetMaxSpeed ($FleetArray, $Fleet, $Player)
{
  if ($Fleet)
  {
    return get_ship_speed($Fleet, $Player);
  }

  foreach ($FleetArray as $Ship => $Count) {
    if(!$Count)
    {
      continue;
    }
    $speedalls[$Ship] = get_ship_speed($Ship, $Player);
  }

  return $speedalls;
}
// ----------------------------------------------------------------------------------------------------------------
// Calcul de la consommation de base d'un vaisseau au regard des technologies
function GetShipConsumption ( $ship_id, $user )
{
  global $pricelist, $resource;

  if($pricelist[$ship_id]['tech_level'] && $user[$resource[$pricelist[$ship_id]['tech2']]] >= $pricelist[$ship_id]['tech_level'])
  {
    $consumption = $pricelist[$ship_id]['consumption2'];
  }
  else
  {
    $consumption = $pricelist[$ship_id]['consumption'];
  }

  return $consumption;
}

// ----------------------------------------------------------------------------------------------------------------
// Calcul de la consommation de la flotte pour cette mission
function GetFleetConsumption ($FleetArray, $SpeedFactor, $MissionDuration, $MissionDistance, $FleetMaxSpeed, $Player, $speed_percent = 10) {
  $consumption     = 0;

  $MissionDuration = $MissionDuration < 1 ? 1 : $MissionDuration;
  $MissionDistance = $MissionDistance < 1 ? 1 : $MissionDistance;
  $SpeedFactor     = $SpeedFactor == 10 ? 11 : $SpeedFactor;

  $spd             = $speed_percent * sqrt( $FleetMaxSpeed );

  foreach ($FleetArray as $Ship => $Count) {
    if (!$Ship) {
      continue;
    }

    $ShipSpeed         = get_ship_speed ( $Ship, $Player );
    $ShipSpeed         = $ShipSpeed < 1 ? 1 : $ShipSpeed;

    $ShipConsumption   = GetShipConsumption ( $Ship, $Player );

    $consumption += $ShipConsumption * $Count  * pow($spd / sqrt($ShipSpeed) / 10 + 1, 2 );
  }

  $consumption = round($MissionDistance * $consumption  / 35000) + 1;

  return $consumption;
}

// ----------------------------------------------------------------------------------------------------------------
//
// Mise en forme de chaines pour affichage
//

// Mise en forme de la durГ©e sous forme xj xxh xxm xxs
function pretty_time ($seconds) {
  global $lang;

  $day = floor($seconds / (24 * 3600));
  $hs = floor($seconds / 3600 % 24);
  $ms = floor($seconds / 60 % 60);
  $sr = floor($seconds / 1 % 60);

  $time = sprintf("%s%02d:%02d:%02d", $day?$day . $lang['sys_day_short'] . ' ':'', $hs, $ms, $sr);

  return $time;
}

// Mise en forme de la durГ©e sous forme xxxmin
function pretty_time_hour ($seconds) {
  $min = floor($seconds / 60 % 60);

  $time = '';
  if ($min != 0) { $time .= $min . 'мин '; }

  return $time;
}

// Mise en forme du temps de construction (avec la phrase de description)
function ShowBuildTime ($time) {
  global $lang;

  return "<br>". $lang['ConstructionTime'] .": " . pretty_time($time);
}

// ----------------------------------------------------------------------------------------------------------------
//
function add_points ($res, $userid) {
  return false;
}

function remove_points ($res, $userid) {
  return false;
}

function get_userdata () {
  return '';
}

// ----------------------------------------------------------------------------------------------------------------
//
// Fonction de lecture / ecriture / exploitation de templates
//
function ReadFromFile($filename) {
  $content = @file_get_contents ($filename);
  return $content;
}

function SaveToFile ($filename, $content) {
  $content = @file_put_contents ($filename, $content);
}

// ----------------------------------------------------------------------------------------------------------------
//
// Gestion de la localisation des chaines
//
function includeLang ($filename, $ext = '.mo') {
  global $ugamela_root_path, $lang, $user;

  //if ($user['lang'] != '') {
  //  $SelLanguage = $user['lang'];
  //} else {
    $SelLanguage = DEFAULT_LANG;
  //}
  include_once $ugamela_root_path . "language/". $SelLanguage ."/". $filename.$ext;
}


// ----------------------------------------------------------------------------------------------------------------
//
// Affiche une adresse de depart sous forme de lien
function GetStartAdressLink ( $FleetRow, $FleetType ) {
  $Link  = "<a href=\"galaxy.php?mode=3&galaxy=".$FleetRow['fleet_start_galaxy']."&system=".$FleetRow['fleet_start_system']."\" ". $FleetType ." >";
  $Link .= "[".$FleetRow['fleet_start_galaxy'].":".$FleetRow['fleet_start_system'].":".$FleetRow['fleet_start_planet']."]</a>";
  return $Link;
}

// Affiche une adresse de cible sous forme de lien
function GetTargetAdressLink ( $FleetRow, $FleetType ) {
  $Link  = "<a href=\"galaxy.php?mode=3&galaxy=".$FleetRow['fleet_end_galaxy']."&system=".$FleetRow['fleet_end_system']."\" ". $FleetType ." >";
  $Link .= "[".$FleetRow['fleet_end_galaxy'].":".$FleetRow['fleet_end_system'].":".$FleetRow['fleet_end_planet']."]</a>";
  return $Link;
}

// Affiche une adresse de planete sous forme de lien
function BuildPlanetAdressLink ( $CurrentPlanet ) {
  $Link  = "<a href=\"galaxy.php?mode=3&galaxy=".$CurrentPlanet['galaxy']."&system=".$CurrentPlanet['system']."\">";
  $Link .= "[".$CurrentPlanet['galaxy'].":".$CurrentPlanet['system'].":".$CurrentPlanet['planet']."]</a>";
  return $Link;
}

// CrГ©ation d'un lien pour le joueur hostile
function BuildHostileFleetPlayerLink ( $FleetRow ) {
  global $lang, $dpath;

  $PlayerName = doquery ("SELECT `username` FROM {{table}} WHERE `id` = '". $FleetRow['fleet_owner']."';", 'users', true);
  $Link  = $PlayerName['username']. " ";
  $Link .= "<a href=\"messages.php?mode=write&id=".$FleetRow['fleet_owner']."\">";
  $Link .= "<img src=\"".$dpath."/img/m.gif\" alt=\"". $lang['ov_message']."\" title=\"". $lang['ov_message']."\" border=\"0\"></a>";
  return $Link;
}

function GetNextJumpWaitTime ( $CurMoon ) {
  global $resource;

  $JumpGateLevel  = $CurMoon[$resource[43]];
  $LastJumpTime   = $CurMoon['last_jump_time'];
  if ($JumpGateLevel > 0) {
    $WaitBetweenJmp = (60 * 60) * (1 / $JumpGateLevel);
    $NextJumpTime   = $LastJumpTime + $WaitBetweenJmp;
    if ($NextJumpTime >= time()) {
      $RestWait   = $NextJumpTime - time();
      $RestString = " ". pretty_time($RestWait);
    } else {
      $RestWait   = 0;
      $RestString = "";
    }
  } else {
    $RestWait   = 0;
    $RestString = "";
  }
  $RetValue['string'] = $RestString;
  $RetValue['value']  = $RestWait;

  return $RetValue;
}
// ----------------------------------------------------------------------------------------------------------------
//
// Создаёт состав флота (используеться в обзоре) при наведении, показывает
function CreateFleetPopupedFleetLink ( $FleetRow, $Texte, $FleetType, $Owner ) {
    global $lang, $user;

    $spy_tech = GetSpyLevel($user);
    $admin    = $user['authlevel'];
    $FleetRec     = explode(";", $FleetRow['fleet_array']);
    $FleetPopup   = "<span onmouseover=\"return overlib('";
    $FleetPopup  .= "<table width=200>";
    if (!$Owner && $spy_tech<2) {
        $FleetPopup .= "<tr><td width=80% align=left><font color=white>". $lang['ov_spy_failed'] ."<font></td><td width=20% align=right>&nbsp;</td></tr>";
    } elseif(!$Owner && $spy_tech<4) {
        $FleetPopup .= "<tr><td width=80% align=left><font color=white>". $lang['ov_total'] .":<font></td><td width=20% align=right><font color=white>". pretty_number(count($FleetRec)) ."<font></td></tr>";
    }
    foreach($FleetRec as $Item => $Group) {
        if ($Group  != '') {
            $Ship    = explode(",", $Group);
            if(!$Owner && $spy_tech>=4 && $spy_tech<8) {
                $FleetPopup .= "<tr><td width=80% align=left><font color=white>". $lang['tech'][$Ship[0]] ."<font></td><td width=20% align=right>&nbsp;</td></tr>";
            } elseif((!$Owner && $spy_tech>=8) || $Owner) {
                $FleetPopup .= "<tr><td width=80% align=left><font color=white>". $lang['tech'][$Ship[0]] .":<font></td><td width=20% align=right><font color=white>". pretty_number($Ship[1]) ."<font></td></tr>";
            }
        }
    }
    if (!$Owner && $admin == 3) {
        $FleetPopup .= "<tr><td width=80% align=left><font color=white>". $lang['tech'][$Ship[0]] .":<font></td><td width=20% align=right><font color=white>". pretty_number($Ship[1]) ."<font></td></tr>";
        $FleetPopup .= "<td width=100% align=center><font color=red>Все видящее Админское око :-D<font></td>";
    }
    $FleetPopup  .= "</table>";
    $FleetPopup  .= "');\" onmouseout=\"return nd();\" class=\"". $FleetType ."\">". $Texte ."</span>";

    return $FleetPopup;

}

// ----------------------------------------------------------------------------------------------------------------
//
// CГ©ation du lien avec popup pour le type de mission avec ou non les ressources si disponibles
function CreateFleetPopupedMissionLink ( $FleetRow, $Texte, $FleetType ) {
  global $lang;

  $FleetTotalC  = $FleetRow['fleet_resource_metal'] + $FleetRow['fleet_resource_crystal'] + $FleetRow['fleet_resource_deuterium'];
  if ($FleetTotalC <> 0) {
    $FRessource   = "<table width=200>";
    $FRessource  .= "<tr><td width=50% align=left><font color=white>". $lang['Metal'] ."<font></td><td width=50% align=right><font color=white>". pretty_number($FleetRow['fleet_resource_metal']) ."<font></td></tr>";
    $FRessource  .= "<tr><td width=50% align=left><font color=white>". $lang['Crystal'] ."<font></td><td width=50% align=right><font color=white>". pretty_number($FleetRow['fleet_resource_crystal']) ."<font></td></tr>";
    $FRessource  .= "<tr><td width=50% align=left><font color=white>". $lang['Deuterium'] ."<font></td><td width=50% align=right><font color=white>". pretty_number($FleetRow['fleet_resource_deuterium']) ."<font></td></tr>";
    $FRessource  .= "</table>";
  } else {
    $FRessource   = "";
  }

  if ($FRessource <> "") {
    $MissionPopup  = "<a href='#' onmouseover=\"return overlib('". $FRessource ."');";
    $MissionPopup .= "\" onmouseout=\"return nd();\" class=\"". $FleetType ."\">" . $Texte ."</a>";
  } else {
    $MissionPopup  = $Texte ."";
  }

  return $MissionPopup;
}

// ----------------------------------------------------------------------------------------------------------------
//
//
function SYS_mysqlSmartEscape($string)
{
  if(!isset($string))
  {
    return NULL;
  }

  if(get_magic_quotes_gpc())
  {
    $string = stripslashes($string);
  }
  return mysql_real_escape_string($string);
}

// ----------------------------------------------------------------------------------------------------------------
//
//
function INT_makeCoordinates ($from, $prefix = '')
{
//  return '[' . $from[$prefix.'galaxy'] . ':' . $from[$prefix.'system'] . ':' . $from[$prefix.'planet'] . ']';
  return "[{$from[$prefix.'galaxy']}:{$from[$prefix.'system']}:{$from[$prefix.'planet']}]";
}

function int_makeCoordinatesURL ($from, $prefix = '', $mode = 0)
{
  return "galaxy.php?mode={$mode}&galaxy={$from[$prefix.'galaxy']}&system={$from[$prefix.'system']}&planet={$from[$prefix.'planet']}";
}

function int_makeCoordinatesLink ($from, $prefix = '', $mode = 0)
{
  return '<a href="' . int_makeCoordinatesURL($from, $prefix, $mode) . '">' . INT_makeCoordinates ($from, $prefix) . '</a>';
}

function int_renderLastActiveHTML($last_active = 0, $isAllowed = true, $isAdmin = false)
{
  global $lang;

  if($isAdmin){
    if ( $last_active < 60 )
    {
      $tmp = "lime>{$lang['sys_online']}";
    }
    elseif ($last_active < 60 * 60)
    {
      $last_active = round($last_active / 60);
      $tmp = "lime>{$last_active} {$lang['sys_min_short']}";
    }
    elseif ($last_active < 60 * 60 * 24)
    {
      $last_active = round( $last_active / (60 * 60));
      $tmp = "green>{$last_active} {$lang['sys_hrs_short']}";
    }
    else
    {
      $last_active = round( $last_active / (60 * 60 * 24));

      if ($last_active < 7)
      {
        $tmp = 'yellow';
      }
      elseif ($last_active < 30)
      {
        $tmp = 'orange';
      }
      else
      {
        $tmp = 'red';
      }
      $tmp .= ">{$last_active} {$lang['sys_day_short']}";
    }
  }
  else
  {
    if($isAllowed)
    {
      if ( $last_active < 60 * 5 )
      {
        $tmp = "lime>{$lang['sys_online']}";
      }
      elseif ($last_active < 60 * 15)
      {
        $tmp = "yellow>{$lang['sys_lessThen15min']}";
      }
      else
      {
        $tmp = "red>{$lang['sys_offline']}";
      }
    }
    else
    {
      $tmp = "orange>-";
    }
  }
  return "<font color={$tmp}</font>";
}

/**
 * strings.php
 *
 * @version 1.0
 * @copyright 2008 by ??????? for XNova
 */

function colorNumber($n, $s = '') {
  if ($n > 0) {
    if ($s != '') {
      $s = colorGreen($s);
    } else {
      $s = colorGreen($n);
    }
  } elseif ($n < 0) {
    if ($s != '') {
      $s = colorRed($s);
    } else {
      $s = colorRed($n);
    }
  } else {
    if ($s != '') {
      $s = $s;
    } else {
      $s = $n;
    }
  }
  return $s;
}

function colorRed($n) {
  return '<font color="#ff0000">' . $n . '</font>';
}

function colorGreen($n) {
  return '<font color="#00ff00">' . $n . '</font>';
}

function pretty_number($n, $floor = true, $color = false) {
  if ($floor === true)
  {
    $n = floor($n);
  }
  elseif(is_numeric($floor))
  {
    $n = round($n, $floor, PHP_ROUND_HALF_DOWN);
  }
  $ret = number_format($n, 0, ',', '.');

  if(is_numeric($color))
  {
    if($color>0)
    {
      if($n<$color)
        $ret = colorGreen($ret);
      elseif($n>=$color)
        $ret = colorRed($ret);
    }
    else
    {
      if($n>=-$color)
        $ret = colorGreen($ret);
      elseif($n<-$color)
        $ret = colorRed($ret);
    }
  }
  elseif($color)
  {
    if($n>0)
      $ret = colorGreen($ret);
    elseif($n<0)
      $ret = colorRed($ret);
  }

  return $ret;
}

// Created by Perberos. All rights reversed (C) 2006
?>