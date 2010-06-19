<?php

/**
 * GalaxyRowMoon.php
 *
 * @version 1.0
 * @copyright 2008 By Chlorel for XNova
 */

function GalaxyRowMoon ( $GalaxyRow, $GalaxyRowPlanet, $GalaxyRowUser, $Galaxy, $System, $Planet, $PlanetType ) {
  global $lang, $user, $dpath, $HavePhalanx, $CurrentSystem, $CurrentGalaxy, $CanDestroy;

  // Lune
  $Result  = "<th style=\"white-space: nowrap;\" width=30>";
  $Result .= '<div style="float: left; width: 100%; height: 100%;">';
  if ($GalaxyRowUser['id'] != $user['id']) {
    $MissionType6Link = "<a href=# onclick=&#039javascript:doit(6, ".$Galaxy.", ".$System.", ".$Planet.", ".$PlanetType.", ".$user["spio_anz"].");&#039 >". $lang['type_mission'][6] ."</a><br /><br />";
  } elseif ($GalaxyRowUser['id'] == $user['id']) {
    $MissionType6Link = "";
  }
  if ($GalaxyRowUser['id'] != $user['id']) {
    $MissionType1Link = "<a href=fleet.php?galaxy=".$Galaxy."&amp;system=".$System."&amp;planet=".$Planet."&amp;planettype=".$PlanetType."&amp;target_mission=1>". $lang['type_mission'][1] ."</a><br />";
  } elseif ($GalaxyRowUser['id'] == $user['id']) {
    $MissionType1Link = "";
  }

  if ($GalaxyRowUser['id'] != $user['id']) {
    $MissionType5Link = "<a href=fleet.php?galaxy=".$Galaxy."&system=".$System."&planet=".$Planet."&planettype=".$PlanetType."&target_mission=5>". $lang['type_mission'][5] ."</a><br />";
  } elseif ($GalaxyRowUser['id'] == $user['id']) {
    $MissionType5Link = "";
  }
  if ($GalaxyRowUser['id'] == $user['id']) {
    $MissionType4Link = "<a href=fleet.php?galaxy=".$Galaxy."&system=".$System."&planet=".$Planet."&planettype=".$PlanetType."&target_mission=4>". $lang['type_mission'][4] ."</a><br />";
  } elseif ($GalaxyRowUser['id'] != $user['id']) {
    $MissionType4Link = "";
  }

  if ($GalaxyRowUser['id'] != $user['id']) {
    if ($CanDestroy > 0) {
      $MissionType9Link = "<a href=fleet.php?galaxy=".$Galaxy."&system=".$System."&planet=".$Planet."&planettype=".$PlanetType."&target_mission=9>". $lang['type_mission'][9] ."</a>";
    } else {
      $MissionType9Link = "";
    }
  } elseif ($GalaxyRowUser['id'] == $user['id']) {
    $MissionType9Link = "";
  }

  $MissionType3Link = "<a href=fleet.php?galaxy=".$Galaxy."&system=".$System."&planet=".$Planet."&planettype=".$PlanetType."&target_mission=3>". $lang['type_mission'][3] ."</a><br />";

  if ($GalaxyRow && $GalaxyRowPlanet["destruyed"] == 0 && $GalaxyRow["id_luna"] != 0) {
    $Result .= "<a style=\"cursor: pointer;\"";
    $Result .= " onmouseover='return overlib(\"";
    $Result .= "<table width=240>";
    $Result .= "<tr>";
    $Result .= "<td class=c colspan=2>";
    $Result .= $lang['Moon'].": ".$GalaxyRowPlanet["name"]." [".$Galaxy.":".$System.":".$Planet."]";
    $Result .= "</td>";
    $Result .= "</tr><tr>";
    $Result .= "<th width=80>";
    $Result .= "<img src=". $dpath ."planeten/mond.jpg height=75 width=75 />";
    $Result .= "</th>";
    $Result .= "<th>";
    $Result .= "<table>";
    $Result .= "<tr>";
    $Result .= "<td class=c colspan=2>".$lang['caracters']."</td>";
    $Result .= "</tr><tr>";
    $Result .= "<th>".$lang['diameter']."</th>";
    $Result .= "<th>". number_format($GalaxyRowPlanet['diameter'], 0, '', '.') ."</th>";
    $Result .= "</tr><tr>";
    $Result .= "<th>".$lang['temperature']."</th><th>". number_format($GalaxyRowPlanet['temp_min'], 0, '', '.') ."</th>";
    $Result .= "</tr><tr>";
    $Result .= "<td class=c colspan=2>".$lang['Actions']."</td>";
    $Result .= "</tr><tr>";
    $Result .= "<th colspan=2 align=center>";
    $Result .= $MissionType6Link;
    $Result .= $MissionType3Link;
    $Result .= $MissionType4Link;
    $Result .= $MissionType1Link;
    $Result .= $MissionType5Link;
    $Result .= $MissionType9Link;
    $Result .= "</tr>";
    $Result .= "</table>";
    $Result .= "</th>";
    $Result .= "</tr>";
    $Result .= "</table>\"";
    $Result .= ", STICKY, MOUSEOFF, DELAY, 750, CENTER, OFFSETX, -40, OFFSETY, -40 );'";
    $Result .= " onmouseout='return nd();'>";
    $Result .= "<img src=". $dpath ."planeten/small/s_mond.jpg height=22 width=22>";
    $Result .= "</a>";
  }
  $Result .= '</div>';
  $Result .= "</th>";

  return $Result;
}
?>