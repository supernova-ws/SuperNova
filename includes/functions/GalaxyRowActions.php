<?php

/**
 * GalaxyRowActions.php
 *
 * @version 1.1
 * @copyright 2008 by Chlorel for XNova
 */

function GalaxyRowActions ( $GalaxyRow, $GalaxyRowPlanet, $GalaxyRowPlayer, $Galaxy, $System, $Planet, $PlanetType ) {
  global $lang, $user, $dpath, $CurrentMIP, $CurrentSystem, $CurrentGalaxy;
  // Icones action
  $Result  = "<th style=\"white-space: nowrap;\" width=125>";
  $Result .= '<div style="float: center"><table width=100% cellspacing=0 cellpadding=0 border=0><tr>';
  if ($GalaxyRowPlayer['id'] != $user['id']) {

    $MissileBtn = false;
    if ($CurrentMIP <> 0) {
      if ($GalaxyRowUser['id'] != $user['id']) {
        if ($GalaxyRowPlanet["galaxy"] == $CurrentGalaxy) {
          $Range = GetMissileRange();
          $SystemLimitMin = $CurrentSystem - $Range;
          if ($SystemLimitMin < 1) {
            $SystemLimitMin = 1;
          }
          $SystemLimitMax = $CurrentSystem + $Range;
          if ($System <= $SystemLimitMax) {
            if ($System >= $SystemLimitMin) {
              $MissileBtn = true;
//            } else {
//              $MissileBtn = false;
            }
//          } else {
//            $MissileBtn = false;
          }
//        } else {
//          $MissileBtn = false;
        }
//      } else {
//        $MissileBtn = false;
      }
//    } else {
//      $MissileBtn = false;
    }

    if ($GalaxyRowPlayer && $GalaxyRowPlanet["destruyed"] == 0) {
      if ($user["settings_esp"] == "1" &&
        $GalaxyRowPlayer['id']) {
        $Result .= '<td>';
        $Result .= "<a href=# onclick=\"javascript:doit(6, ".$Galaxy.", ".$System.", ".$Planet.", 1, ".$user["spio_anz"].");\" >";
        $Result .= "<img src=". $dpath ."img/e.gif alt=\"".$lang['gl_espionner']."\" title=\"".$lang['gl_espionner']."\" border=0></a>";
        $Result .= '</td>';
        //$Result .= "&nbsp;";
      }
      if ($user["settings_wri"] == "1" &&
        $GalaxyRowPlayer['id']) {
        $Result .= '<td>';
        $Result .= "<a href=messages.php?mode=write&id=".$GalaxyRowPlayer["id"].">";
        $Result .= "<img src=". $dpath ."img/m.gif alt=\"".$lang['gl_sendmess']."\" title=\"".$lang['gl_sendmess']."\" border=0></a>";
        $Result .= '</td>';
        //$Result .= "&nbsp;";
      }
      if ($user["settings_bud"] == "1" &&
        $GalaxyRowPlayer['id']) {
        $Result .= '<td>';
        $Result .= "<a href=buddy.php?a=2&amp;u=".$GalaxyRowPlayer['id']." >";
        $Result .= "<img src=". $dpath ."img/b.gif alt=\"".$lang['gl_buddyreq']."\" title=\"".$lang['gl_buddyreq']."\" border=0></a>";
        $Result .= '</td>';
        //$Result .= "&nbsp;";
      }
      if ($user["settings_mis"] == "1" AND
        $MissileBtn == true          &&
        $GalaxyRowPlayer['id']) {
        $Result .= '<td>';
        $Result .= "<a href=galaxy.php?mode=2&galaxy=".$Galaxy."&system=".$System."&planet=".$Planet."&current=".$user['current_planet']." >";
        $Result .= "<img src=". $dpath ."img/r.gif alt=\"".$lang['gl_mipattack']."\" title=\"".$lang['gl_mipattack']."\" border=0></a>";
        $Result .= '</td>';
      }
    }
  }
  $Result .= '</tr></table></div>';
  $Result .= "</th>";

  return $Result;
}
?>