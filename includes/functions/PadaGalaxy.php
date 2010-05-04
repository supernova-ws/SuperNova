<?php
/**
 * Based on Chlorel - Moded to PadaGalaxy v0.3
 *
 * @version 0.8s - Security checked for SQL-injection by Gorlum for http://supernova.ws
 * @package XNova
 * @version 0.8
 * @copyright 2008 Chlorel, XNova Group
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

if (!defined('INSIDE'))
{
  die();
}

function _TooltipActions($Row, $Galaxy, $System, $Planet, $PlanetType ) {
  global $lang, $user, $dpath, $CurrentMIP, $CurrentSystem, $CurrentGalaxy;


  if ($Row['id'] != $user['id']) {

    if ($CurrentMIP <> 0) {
      if ($Row['id'] != $user['id']) {
        if ($Row['galaxy'] == $Galaxy) {
          $Range = GetMissileRange();
          $SystemLimitMin = $CurrentSystem - $Range;
          if ($SystemLimitMin < 1) {
            $SystemLimitMin = 1;
          }
          $SystemLimitMax = $CurrentSystem + $Range;
          if ($System <= $SystemLimitMax) {
            if ($System >= $SystemLimitMin) {
              $MissileBtn = true;
            } else {
              $MissileBtn = false;
            }
          } else {
            $MissileBtn = false;
          }
        } else {
          $MissileBtn = false;
        }
      } else {
        $MissileBtn = false;
      }
    } else {
      $MissileBtn = false;
    }

    if ($Row && $Row["destruyed"] == 0) {
      if ($user["settings_esp"]) {
        $Result .= "<a href=# onclick=\"javascript:pada_galaxy(6, ".$Galaxy.", ".$System.", ".$Planet.", 1, ".$user["spio_anz"].");\" >";
        $Result .= "<img src=". $dpath ."img/e.gif alt=\"".$lang['gl_espionner']."\" title=\"".$lang['gl_espionner']."\" border=0></a>";
        $Result .= "&nbsp;";
      }
      if ($user["settings_wri"]) {
        $Result .= "<a href=messages.php?mode=write&id=".$Row["id"].">";
        $Result .= "<img src=". $dpath ."img/m.gif alt=\"".$lang['gl_sendmess']."\" title=\"".$lang['gl_sendmess']."\" border=0></a>";
                $Result .= "&nbsp;";
      }
      if ($user["settings_bud"]) {
        $Result .= "<a href=buddy.php?a=2&amp;u=".$Row['id']." >";
        $Result .= "<img src=". $dpath ."img/b.gif alt=\"".$lang['gl_buddyreq']."\" title=\"".$lang['gl_buddyreq']."\" border=0></a>";
                $Result .= "&nbsp;";
      }
      if ($user["settings_mis"] AND $MissileBtn) {
        $Result .= "<a href=galaxy.php?mode=2&galaxy=".$Galaxy."&system=".$System."&planet=".$Planet."&current=".$user['current_planet']." >";
        $Result .= "<img src=". $dpath ."img/r.gif alt=\"".$lang['gl_mipattack']."\" title=\"".$lang['gl_mipattack']."\" border=0></a>";
      }
    }
  }

  return $Result;
}

function _TooltipAlliance($Row, $Galaxy, $System, $Planet, $PlanetType ) {
  global $lang, $user;

  if ($Row['ally_members'] > 1) {
    $add = "s";
  } else {
    $add = "";
  }

  $Result .= "<a style=\"cursor: pointer;\"";
  $Result .= " onmouseover='return overlib(\"";
  $Result .= "<table width=240>";
  $Result .= "<tr>";
  $Result .= "<td class=c>".$lang['Alliance']." ". $Row['ally_name'] ." ".$lang['gl_with']." ". $Row['ally_members'] ." ". $lang['gl_membre'] . $add ."</td>";
  $Result .= "</tr>";
  $Result .= "<th>";
  $Result .= "<table>";
  $Result .= "<tr>";
  $Result .= "<td><a href=alliance.php?mode=ainfo&a=". $Row['ally_id'] .">".$lang['gl_ally_internal']."</a></td>";
  $Result .= "</tr><tr>";
  $Result .= "<td><a href=stat.php?start=101&who=ally>".$lang['gl_stats']."</a></td>";
  if ($Row["ally_web"]) {
    $Result .= "</tr><tr>";
    $Result .= "<td><a href=". $Row["ally_web"] ." target=_new>".$lang['gl_ally_web']."</td>";
  }
  $Result .= "</tr>";
  $Result .= "</table>";
  $Result .= "</th>";
  $Result .= "</table>\"";
  $Result .= ", STICKY, MOUSEOFF, DELAY, 750, CENTER, OFFSETX, -40, OFFSETY, -40 );'";
  $Result .= " onmouseout='return nd();'>";
  if ($user['ally_id'] == $Row['ally_id']) {
    $Result .= "<span class=\"allymember\">". $Row['ally_tag'] ."</span></a>";
  } else {
    $Result .= $Row['ally_tag'] ."</a>";
  }

  return $Result;
}

function _TooltipUser($Row, $Galaxy, $System, $Planet, $PlanetType ) {
  global $game_config, $UserPoints, $lang, $user;

  if ($Row && $Row["destruyed"] == 0) {
    $NoobProt = $game_config['noobprotection'];
    $NoobTime = $game_config['noobprotectiontime'];
    $NoobMulti = $game_config['noobprotectionmulti'];

    $CurrentPoints = $UserPoints['total_points'];
    $RowUserPoints = $Row['total_points'];

    $RowUserLevel  = $RowUserPoints * $NoobMulti;
    $CurrentLevel  = $CurrentPoints * $NoobMulti;

    $CurrentLevel  = $CurrentPoints * $NoobMulti['config_value'];
    $RowUserLevel  = $RowUserPoints * $NoobMulti['config_value'];
    if($Row['bana'] == 1 AND $Row['urlaubs_modus'] == 1) {
      $Systemtatus2 = $lang['vacation_shortcut']." <a href=\"banned.php\"><span class=\"banned\">".$lang['banned_shortcut']."</span></a>";
      $Systemtatus  = "<span class=\"vacation\">";
    } elseif ($Row['bana'] == 1) {
      $Systemtatus2 = "<a href=\"banned.php\"><span class=\"banned\">".$lang['banned_shortcut']."</span></a>";
      $Systemtatus  = "";
    } elseif ($Row['urlaubs_modus'] == 1) {
      $Systemtatus2 = "<span class=\"vacation\">".$lang['vacation_shortcut']."</span>";
      $Systemtatus  = "<span class=\"vacation\">";
    } elseif ($Row['onlinetime'] < (time()-60 * 60 * 24 * 7) AND
          $Row['onlinetime'] > (time()-60 * 60 * 24 * 28)) {
      $Systemtatus2 = "<span class=\"inactive\">".$lang['inactif_7_shortcut']."</span>";
      $Systemtatus  = "<span class=\"inactive\">";
    } elseif ($Row['onlinetime'] < (time()-60 * 60 * 24 * 28)) {
      $Systemtatus2 = "<span class=\"inactive\">".$lang['inactif_7_shortcut']."</span><span class=\"longinactive\"> ".$lang['inactif_28_shortcut']."</span>";
      $Systemtatus  = "<span class=\"longinactive\">";
    } elseif ($RowUserLevel < $CurrentPoints
          AND $NoobProt
          AND ($NoobTime * 1000) > $RowUserPoints) {
      $Systemtatus2 = "<span class=\"noob\">".$lang['weak_player_shortcut']."</span>";
      $Systemtatus  = "<span class=\"noob\">";
    } elseif ($RowUserPoints > $CurrentLevel
          AND $NoobProt
          AND ($NoobTime * 1000) > $CurrentPoints) {
      $Systemtatus2 = $lang['strong_player_shortcut'];
      $Systemtatus  = "<span class=\"strong\">";
    } else {
      $Systemtatus2 = "";
      $Systemtatus  = "";
    }
    $Systemtatus4 = $Row['total_rank'];
    if ($Systemtatus2 != '') {
      $Systemtatus6 = "<font color=\"white\">(</font>";
      $Systemtatus7 = "<font color=\"white\">)</font>";
    }
    if ($Systemtatus2 == '') {
      $Systemtatus6 = "";
      $Systemtatus7 = "";
    }
    $admin = "";
    if ($Row['authlevel'] > 0) {
      $admin = "<font color=\"lime\"><blink>A</blink></font>";
    }
    $Systemtart = $Row['total_rank'];
    if (strlen($Systemtart) < 3) {
      $Systemtart = 1;
    } else {
      $Systemtart = (floor( $Systemtart / 100 ) * 100) + 1;
    }
    $Result .= "<a style=\"cursor: pointer;\"";
    $Result .= " onmouseover='return overlib(\"";
    $Result .= "<table width=190>";
    $Result .= "<tr>";
    $Result .= "<td class=c colspan=2>".$lang['Player']." ".$Row['username']." (".$Systemtatus4.")</td>";
    $Result .= "</tr><tr>";
    if ($Row['id'] != $user['id']) {
      $Result .= "<td><a href=messages.php?mode=write&id=".$Row['id'].">".$lang['gl_sendmess']."</a></td>";
      $Result .= "</tr><tr>";
      $Result .= "<td><a href=buddy.php?a=2&u=".$Row['id'].">".$lang['gl_buddyreq']."</a></td>";
      $Result .= "</tr><tr>";
    }
    $Result .= "<td><a href=stat.php?who=player&start=".$Systemtart.">".$lang['gl_stats']."</a></td>";
    $Result .= "</tr>";
    $Result .= "</table>\"";
    $Result .= ", STICKY, MOUSEOFF, DELAY, 750, CENTER, OFFSETX, -40, OFFSETY, -40 );'";
    $Result .= " onmouseout='return nd();'>";
    $Result .= $Systemtatus;
    $Result .= $Row["username"]."</span>";
    $Result .= $Systemtatus6;
    $Result .= $Systemtatus;
    $Result .= $Systemtatus2;
    $Result .= $Systemtatus7." ".$admin;
    $Result .= "</span></a>";
  }

  return $Result;
}

function _TooltipDebris($Row, $Galaxy, $System, $Planet, $PlanetType){
  global $lang, $dpath, $CurrentRC, $user, $pricelist;

  $RecNeeded = ceil(($Row['debris_metal'] + $Row['debris_crystal']) / $pricelist[209]['capacity']);
  if ($RecNeeded < $CurrentRC) {
    $RecSended = $RecNeeded;
  } elseif ($RecNeeded >= $CurrentRC) {
    $RecSended = $CurrentRC;
  } else {
    $RecSended = $CurrentRC;
  }

  $Result .= "<a style=\"cursor: pointer;\"";
  $Result .= " onmouseover='return overlib(\"";
  $Result .= "<table width=240>";
  $Result .= "<tr>";
  $Result .= "<td class=c colspan=2>";
  $Result .= $lang['Debris']." [".$Galaxy.":".$System.":".$Planet."]";
  $Result .= "</td>";
  $Result .= "</tr><tr>";
  $Result .= "<th width=80>";
  $Result .= "<img src=". $dpath ."planeten/debris.jpg height=75 width=75 />";
  $Result .= "</th>";
  $Result .= "<th>";
  $Result .= "<table>";
  $Result .= "<tr>";
  $Result .= "<td class=c colspan=2>".$lang['gl_ressource']."</td>";
  $Result .= "</tr><tr>";
  $Result .= "<th>".$lang['Metal']." </th><th>". pretty_number($Row['metal']) ."</th>";
  $Result .= "</tr><tr>";
  $Result .= "<th>".$lang['Crystal']." </th><th>". pretty_number($Row['crystal']) ."</th>";
  $Result .= "</tr><tr>";
  $Result .= "<td class=c colspan=2>".$lang['gl_action']."</td>";
  $Result .= "</tr><tr>";
  $Result .= "<th colspan=2 align=left>";
  $Result .= "<a href= # onclick=&#039javascript:pada_galaxy (8, ".$Galaxy.", ".$System.", ".$Planet.", ".$PlanetType.", ".$RecSended.");&#039 >". $lang['type_mission'][8] ."</a>";
  $Result .= "</tr>";
  $Result .= "</table>";
  $Result .= "</th>";
  $Result .= "</tr>";
  $Result .= "</table>\"";
  $Result .= ", STICKY, MOUSEOFF, DELAY, 750, CENTER, OFFSETX, -40, OFFSETY, -40 );'";
  $Result .= " onmouseout='return nd();'>";
  $Result .= "<img src=". $dpath ."planeten/debris.jpg height=22 width=22></a>";

  return $Result;
}

function _TooltipMoon($Row, $Galaxy, $System, $Planet, $PlanetType) {
  global $lang, $user, $dpath, $HavePhalanx, $CurrentSystem, $CurrentGalaxy, $CanDestroy;

  if ($Row['id'] != $user['id']) {
    $MissionType6Link = "<a href=# onclick=&#039javascript:pada_galaxy(6, ".$Galaxy.", ".$System.", ".$Planet.", ".$PlanetType.", ".$user["spio_anz"].");&#039 >". $lang['type_mission'][6] ."</a><br /><br />";
  } elseif ($Row['id'] == $user['id']) {
    $MissionType6Link = "";
  }
  if ($Row['id'] != $user['id']) {
    $MissionType1Link = "<a href=fleet.php?galaxy=".$Galaxy."&amp;system=".$System."&amp;planet=".$Planet."&amp;planettype=".$PlanetType."&amp;target_mission=1>". $lang['type_mission'][1] ."</a><br />";
  } elseif ($Row['id'] == $user['id']) {
    $MissionType1Link = "";
  }

  if ($Row['id'] != $user['id']) {
    $MissionType5Link = "<a href=fleet.php?galaxy=".$Galaxy."&system=".$System."&planet=".$Planet."&planettype=".$PlanetType."&target_mission=5>". $lang['type_mission'][5] ."</a><br />";
  } elseif ($Row['id'] == $user['id']) {
    $MissionType5Link = "";
  }
  if ($Row['id'] == $user['id']) {
    $MissionType4Link = "<a href=fleet.php?galaxy=".$Galaxy."&system=".$System."&planet=".$Planet."&planettype=".$PlanetType."&target_mission=4>". $lang['type_mission'][4] ."</a><br />";
  } elseif ($Row['id'] != $user['id']) {
    $MissionType4Link = "";
  }

  if ($Row['id'] != $user['id']) {
    if ($CanDestroy > 0) {
      $MissionType9Link = "<a href=fleet.php?galaxy=".$Galaxy."&system=".$System."&planet=".$Planet."&planettype=".$PlanetType."&target_mission=9>". $lang['type_mission'][9] ."</a>";
    } else {
      $MissionType9Link = "";
    }
  } elseif ($Row['id'] == $user['id']) {
    $MissionType9Link = "";
  }

  $MissionType3Link = "<a href=fleet.php?galaxy=".$Galaxy."&system=".$System."&planet=".$Planet."&planettype=".$PlanetType."&target_mission=3>". $lang['type_mission'][3] ."</a><br />";

  if ($Row AND !$Row["destruyed"] AND $Row["id_luna"]){
    $Result .= "<a style=\"cursor: pointer;\"";
    $Result .= " onmouseover='return overlib(\"";
    $Result .= "<table width=240>";
    $Result .= "<tr>";
    $Result .= "<td class=c colspan=2>";
    $Result .= $lang['Moon'].": " . $Row['moon_name'] . " [".$Galaxy.":".$System.":".$Planet."]";
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
    $Result .= "<th>". pretty_number($Row['diameter']) ."</th>";
    $Result .= "</tr><tr>";
    $Result .= "<th>".$lang['temperature']."</th><th>". pretty_number($Row['temp_min']) ."</th>";
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

  return $Result;
}

function _TooltipPlanetStatus($Row, $Galaxy, $System, $Planet, $PlanetType){
  global $lang, $user, $HavePhalanx, $CurrentSystem, $CurrentGalaxy;

  if ($Row['ally_id'] == $user['ally_id']
    AND $Row['id'] != $user['id']
    AND $user['ally_id'] != '') {
    $TextColor = "<font color=\"green\">";
    $EndColor  = "</font>";
  } elseif ($Row['id'] == $user['id']) {
    $TextColor = "<font color=\"red\">";
    $EndColor  = "</font>";
  } else {
    $TextColor = '';
    $EndColor  = "";
  }

  if (  $Row['last_update'] > (time()-59 * 60)
    AND $Row['id'] != $user['id']) {
    $Inactivity = pretty_time_hour(time() - $Row['last_update']);
  }
  if ($Row && $Row["destruyed"] == 0) {
    if ($HavePhalanx > 0) {
      if ($Row["galaxy"] == $CurrentGalaxy) {
        $Range = GetPhalanxRange ( $HavePhalanx );
        if ($SystemLimitMin < 1) {
          $SystemLimitMin = 1;
        }
        $SystemLimitMax = $CurrentSystem + $Range;
        if (  $System <= $SystemLimitMax
          AND $System >= $SystemLimitMin) {
          $PhalanxTypeLink = "<a href=# onclick=fenster('phalanx.php?galaxy=".$Galaxy."&amp;system=".$System."&amp;planet=".$Planet."&amp;planettype=".$PlanetType."')  title=\"".$lang['gl_phalanx']."\">" . $Row['planet_name'] . "</a><br />";
        }
      }
    }

    $PhalanxTypeLink = (isset($PhalanxTypeLink)) ? $PhalanxTypeLink : $Row['planet_name'];

    $Result .= $TextColor . $PhalanxTypeLink . $EndColor;

    if ($Row['last_update']  > (time()-59 * 60)
      AND $Row['id'] != $user['id']) {
      if ($Row['last_update']  > (time()-10 * 60)
        AND $Row['id'] != $user['id']) {
        $Result .= "(*)";
      } else {
        $Result .= " (".$Inactivity.")";
      }
    }
  } elseif ($Row["destruyed"] != 0) {
    $Result .= $lang['gl_destroyedplanet'];
  }

  return $Result;
}

function _TooltipPlanet($Row, $Galaxy, $System, $Planet, $PlanetType ) {
  global $lang, $dpath, $user, $HavePhalanx, $CurrentSystem, $CurrentGalaxy;

  if ($Row && $Row["destruyed"] == 0 && $Row["id_planet"] != 0) {
    if ($HavePhalanx <> 0) {
      if ($Row['id'] != $user['id']) {
        if ($Row["galaxy"] == $CurrentGalaxy) {
          $Range = GetPhalanxRange ( $HavePhalanx );
          if ($SystemLimitMin < 1) {
            $SystemLimitMin = 1;
          }
          $SystemLimitMax = $CurrentSystem + $Range;
          if ($System <= $SystemLimitMax) {
            if ($System >= $SystemLimitMin) {
              $PhalanxTypeLink = "<a href=# onclick=fenster(&#039;phalanx.php?galaxy=".$Galaxy."&amp;system=".$System."&amp;planet=".$Planet."&amp;planettype=".$PlanetType."&#039;) >".$lang['gl_phalanx']."</a><br />";
            } else {
              $PhalanxTypeLink = "";
            }
          } else {
            $PhalanxTypeLink = "";
          }
        } else {
          $PhalanxTypeLink = "";
        }
      } else {
        $PhalanxTypeLink = "";
      }
    } else {
      $PhalanxTypeLink = "";
    }

    if ($Row['id'] != $user['id']) {
      $MissionType6Link = "<a href=# onclick=&#039javascript:pada_galaxy(6, ".$Galaxy.", ".$System.", ".$Planet.", ".$PlanetType.", ".$user["spio_anz"].");&#039 >". $lang['type_mission'][6] ."</a><br /><br />";
    } elseif ($Row['id'] == $user['id']) {
      $MissionType6Link = "";
    }
    if ($Row['id'] != $user['id']) {
      $MissionType1Link = "<a href=fleet.php?galaxy=".$Galaxy."&amp;system=".$System."&amp;planet=".$Planet."&amp;planettype=".$PlanetType."&amp;target_mission=1>". $lang['type_mission'][1] ."</a><br />";
    } elseif ($Row['id'] == $user['id']) {
      $MissionType1Link = "";
    }
    if ($Row['id'] != $user['id']) {
      $MissionType5Link = "<a href=fleet.php?galaxy=".$Galaxy."&system=".$System."&planet=".$Planet."&planettype=".$PlanetType."&target_mission=5>". $lang['type_mission'][5] ."</a><br />";
    } elseif ($Row['id'] == $user['id']) {
      $MissionType5Link = "";
    }
    if ($Row['id'] == $user['id']) {
      $MissionType4Link = "<a href=fleet.php?galaxy=".$Galaxy."&system=".$System."&planet=".$Planet."&planettype=".$PlanetType."&target_mission=4>". $lang['type_mission'][4] ."</a><br />";
    } elseif ($Row['id'] != $user['id']) {
      $MissionType4Link = "";
    }
    $MissionType3Link = "<a href=fleet.php?galaxy=".$Galaxy."&system=".$System."&planet=".$Planet."&planettype=".$PlanetType."&target_mission=3>". $lang['type_mission'][3] ."</a>";

    $Result .= "<a style=\"cursor: pointer;\"";
    $Result .= " onmouseover='return overlib(\"";
    $Result .= "<table width=240>";
    $Result .= "<tr>";
    $Result .= "<td class=c colspan=2>";
    $Result .= $lang['gl_planet'] . " " . cleanHTML($Row['planet_name']) . " [".$Galaxy.":".$System.":".$Planet."]";
    $Result .= "</td>";
    $Result .= "</tr>";
    $Result .= "<tr>";
    $Result .= "<th width=80>";
    $Result .= "<img src=". $dpath ."planeten/small/s_". $Row["image"] .".jpg height=75 width=75 />";
    $Result .= "</th>";
    $Result .= "<th align=left>";
    $Result .= $MissionType6Link;
    $Result .= $PhalanxTypeLink;
    $Result .= $MissionType1Link;
    $Result .= $MissionType5Link;
    $Result .= $MissionType4Link;
    $Result .= $MissionType3Link;
    $Result .= "</th>";
    $Result .= "</tr>";
    $Result .= "</table>\"";
    $Result .= ", STICKY, MOUSEOFF, DELAY, 750, CENTER, OFFSETX, -40, OFFSETY, -40 );'";
    $Result .= " onmouseout='return nd();'>";
    $Result .= "<img src=". $dpath ."planeten/small/s_". $Row["image"] .".jpg height=30 width=30>";
    $Result .= "</a>";
  }

  return $Result;
}

function secureNumericGet(){
  if(!$_GET) return false;

  foreach($_GET as $name => $value){
    if(secureNumeric($value) == false){
      unset($_GET[$name]);
    }
  }
  return;
}

function secureNumeric($value){
  if(!$value) return false;
/*
  if(ereg("[0-9]", $value) === false){
    return false;
  }
  return true;
*/
  return is_numeric($value);
}

function actual_time($format, $offset, $timestamp){
  $offset = getActualTimeOffset();
  $timestamp = $timestamp + $offset;
  return gmdate($format, $timestamp);
}

function getActualTimeOffset($offset = "+3"){
  return $offset * 60 * 60;
}
?>