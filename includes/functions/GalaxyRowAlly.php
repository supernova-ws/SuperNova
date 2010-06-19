<?php

/**
 * GalaxyRowAlly.php
 *
 * @version 1.0
 * @copyright 2008 By Chlorel for XNova
 */

function GalaxyRowAlly ( $GalaxyRow, $GalaxyRowPlanet, $GalaxyRowUser, $Galaxy, $System, $Planet, $PlanetType ) {
  global $lang, $user;

  $Result  = "<th width=80>";
  $Result .= '<div style="line-height: 1em; height: 1em">';
  if ($GalaxyRowUser['ally_id']) {
    $allyquery = doquery("SELECT * FROM `{{table}}` WHERE `id` = '" . $GalaxyRowUser['ally_id'] . "'", "alliance", true);
    if ($allyquery) {
      $Result .= "<a style=\"cursor: pointer;\"";
      $Result .= " onmouseover='return overlib(\"";

      $Result .= "<table>";
      $Result .= "<tr>";
      $Result .= "<td class=c><center>".$lang['Alliance']."&nbsp;". $allyquery['ally_name'] ."<br>" . $lang['gal_sys_members'] . $allyquery['ally_members'] . "</center></td>";
      $Result .= "</tr>";
      $Result .= "<tr><th><a href=alliance.php?mode=ainfo&a=". $allyquery['id'] .">".$lang['gl_ally_internal']."</a></th></tr>";
      $Result .= "<tr>";
      $Result .= "<th><a href=stat.php?start=101&who=ally>".$lang['gl_stats']."</a></th>";
      if ($allyquery["ally_web"]) {
        $Result .= "</tr><tr>";
        $Result .= "<th><a href=". $allyquery["ally_web"] ." target=_new>".$lang['gl_ally_web']."</th>";
      }
      $Result .= "</tr>";
      $Result .= "</table>\"";
      $Result .= ", STICKY, MOUSEOFF, DELAY, 750, CENTER, OFFSETX, -40, OFFSETY, -40 );'";
      $Result .= " onmouseout='return nd();'>";
      if ($user['ally_id'] == $GalaxyRowUser['ally_id']) {
        $class = "allymember";
      } else {
        $class = "";
      }
      $Result .= "<span class=\"{$class}\">". $allyquery['ally_tag'] ."</span></a>";
    }
  }
  $Result .= '</div>';
  $Result .= "</th>";

  return $Result;
}
?>