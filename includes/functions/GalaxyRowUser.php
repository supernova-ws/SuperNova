<?php

/**
 * GalaxyRowUser.php
 *
 * @version 1.0
 * @copyright 2008 By Chlorel for XNova
 */

function GalaxyRowUser ( $GalaxyRow, $GalaxyRowPlanet, $GalaxyRowUser, $Galaxy, $System, $Planet, $PlanetType ) {
  global $lang, $user, $game_config;

  // Joueur
  $Result  = "<th width=150 align=center>";
  $Result .= '<div style="line-height: 1em; height: 1em">';
  if ($GalaxyRowUser && $GalaxyRowPlanet["destruyed"] == 0) {

    $Noob['Prot']      = $game_config['noobprotection'];
    $Noob['Time']      = $game_config['noobprotectiontime'];
    $Noob['Multi']     = $game_config['noobprotectionmulti'];

    //$NoobProt      = doquery("SELECT * FROM `{{table}}` WHERE `config_name` = 'noobprotection'", 'config', true);
    //$NoobTime      = doquery("SELECT * FROM `{{table}}` WHERE `config_name` = 'noobprotectiontime'", 'config', true);
    //$NoobMulti     = doquery("SELECT * FROM `{{table}}` WHERE `config_name` = 'noobprotectionmulti'", 'config', true);

    $UserPoints    = doquery("SELECT * FROM `{{table}}` WHERE `stat_type` = '1' AND `stat_code` = '1' AND `id_owner` = '". $user['id'] ."'", 'statpoints', true);
    $User2Points   = doquery("SELECT * FROM `{{table}}` WHERE `stat_type` = '1' AND `stat_code` = '1' AND `id_owner` = '". $GalaxyRowUser['id'] ."'", 'statpoints', true);
    $CurrentPoints = $UserPoints['total_points'];
    $RowUserPoints = $User2Points['total_points'];
    $CurrentLevel  = $CurrentPoints * $Noob['Multi'];
    $RowUserLevel  = $RowUserPoints * $Noob['Multi'];
    if       ($GalaxyRowUser['bana'] == 1 AND
          $GalaxyRowUser['urlaubs_modus'] == 1) {
      $Systemtatus2 = $lang['vacation_shortcut']." <a href=\"banned.php\"><span class=\"banned\">".$lang['banned_shortcut']."</span></a>";
      $Systemtatus  = "<span class=\"vacation\">";
    } elseif ($GalaxyRowUser['bana'] == 1) {
      $Systemtatus2 = "<a href=\"banned.php\"><span class=\"banned\">".$lang['banned_shortcut']."</span></a>";
      $Systemtatus  = "";
    } elseif ($GalaxyRowUser['urlaubs_modus'] == 1) {
      $Systemtatus2 = "<span class=\"vacation\">".$lang['vacation_shortcut']."</span>";
      $Systemtatus  = "<span class=\"vacation\">";
    } elseif ($GalaxyRowUser['onlinetime'] < (time()-60 * 60 * 24 * 7) AND
          $GalaxyRowUser['onlinetime'] > (time()-60 * 60 * 24 * 28)) {
      $Systemtatus2 = "<span class=\"inactive\">".$lang['inactif_7_shortcut']."</span>";
      $Systemtatus  = "<span class=\"inactive\">";
    } elseif ($GalaxyRowUser['onlinetime'] < (time()-60 * 60 * 24 * 28)) {
      $Systemtatus2 = "<span class=\"inactive\">".$lang['inactif_7_shortcut']."</span><span class=\"longinactive\"> ".$lang['inactif_28_shortcut']."</span>";
      $Systemtatus  = "<span class=\"longinactive\">";
    } elseif ($RowUserLevel < $CurrentPoints AND
          $Noob['Prot'] == 1 AND
          $Noob['Time'] * 1000 > $RowUserPoints) {
      $Systemtatus2 = "<span class=\"noob\">".$lang['weak_player_shortcut']."</span>";
      $Systemtatus  = "<span class=\"noob\">";
    } elseif ($RowUserPoints > $CurrentLevel AND
          $Noob['Prot'] == 1 AND
          $Noob['Time'] * 1000 > $CurrentPoints) {
      $Systemtatus2 = $lang['strong_player_shortcut'];
      $Systemtatus  = "<span class=\"strong\">";
    } else {
      $Systemtatus2 = "";
      $Systemtatus  = "";
    }
    $Systemtatus4 = $User2Points['total_rank'];
    if ($Systemtatus2 != '') {
      $Systemtatus6 = "<font color=\"white\">(</font>";
      $Systemtatus7 = "<font color=\"white\">)</font>";
    }
    if ($Systemtatus2 == '') {
      $Systemtatus6 = "";
      $Systemtatus7 = "";
    }
    $admin = "";
    $show_admin = SHOW_ADMIN;
    if($show_admin > 0) {
    if ($GalaxyRowUser['authlevel'] > 2) {
      $admin = "<font color=\"lime\"><blink>A</blink></font>";
    }
    $operateur = "";
    if ($GalaxyRowUser['authlevel'] < 3) {
      $admin = "<font color=\"lime\"><blink>O</blink></font>";
    }
    $moderateur = "";
    if ($GalaxyRowUser['authlevel'] < 2) {
      $admin = "<font color=\"lime\"><blink>M</blink></font>";
    }
    $joueur = "";
    if ($GalaxyRowUser['authlevel'] < 1) {
      $admin = "<font color=\"lime\"><blink></blink></font>";
    }
    }
    $Systemtart = $User2Points['total_rank'];
    if (strlen($Systemtart) < 3) {
      $Systemtart = 1;
    } else {
      $Systemtart = (floor( $User2Points['total_rank'] / 100 ) * 100) + 1;
    }
    $Result .= "<a style=\"cursor: pointer;\"";
    $Result .= " onmouseover='return overlib(\"";
    $Result .= "<table width=190>";
    $Result .= "<tr>";
    $Result .= "<td class=c colspan=2>".$lang['Player']." ".$GalaxyRowUser['username']." ".$lang['Place']." ".$Systemtatus4."</td>";
    $Result .= "</tr><tr>";
    if ($GalaxyRowUser['id'] != $user['id']) {
      $Result .= "<td><a href=messages.php?mode=write&id=".$GalaxyRowUser['id'].">".$lang['gl_sendmess']."</a></td>";
      $Result .= "</tr><tr>";
      $Result .= "<td><a href=buddy.php?a=2&u=".$GalaxyRowUser['id'].">".$lang['gl_buddyreq']."</a></td>";
      $Result .= "</tr><tr>";
    }
    $Result .= "<td><a href=stat.php?who=player&start=".$Systemtart.">".$lang['gl_stats']."</a></td>";
    $Result .= "</tr>";
    $Result .= "</table>\"";
    $Result .= ", STICKY, MOUSEOFF, DELAY, 750, CENTER, OFFSETX, -40, OFFSETY, -40 );'";
    $Result .= " onmouseout='return nd();'>";

    $Result .= $Systemtatus;
    $Result .= $GalaxyRowUser["username"]."</span>";
    $Result .= $Systemtatus6;
    $Result .= $Systemtatus;
    $Result .= $Systemtatus2;
    $Result .= $Systemtatus7." ".$admin;
    $Result .= "</span></a>";
  }
  $Result .= "</div>";
  $Result .= "</th>";

  return $Result;
}
?>