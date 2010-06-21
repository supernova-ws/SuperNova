<?php

/**
 * ShowGalaxyRows.php
 *
 * @version 1.0
 * @copyright 2008 By Chlorel for XNova
 */

function ShowGalaxyRows ($Galaxy, $System) {
  global $lang, $planetcount, $CurrentRC, $dpath, $user, $config;

  $Result = "";
  for ($Planet = 1; $Planet < 16; $Planet++) {
    unset($GalaxyRowPlanet);
    unset($GalaxyRowMoon);
    unset($GalaxyRowava);
    unset($GalaxyRowUser);
    unset($GalaxyRowAlly);

    if ($Galaxy){
      $GalaxyRow = doquery("SELECT * FROM {{table}} WHERE `galaxy` = '".$Galaxy."' AND `system` = '".$System."' AND `planet` = '".$Planet."';", 'galaxy', true);
    }
    if ($GalaxyRow["id_planet"]) {
      $GalaxyRowPlanet = doquery("SELECT * FROM {{table}} WHERE `id` = '". $GalaxyRow["id_planet"] ."';", 'planets', true);
      if ($GalaxyRowPlanet['destruyed'] AND $GalaxyRowPlanet['id_owner']) {
        CheckAbandonPlanetState ($GalaxyRowPlanet);
      } else {
        $planetcount++;
        if($cached['users'][$GalaxyRowPlanet["id_owner"]])
          $GalaxyRowUser = $cached['users'][$GalaxyRowPlanet["id_owner"]];
        else{
          $GalaxyRowUser = doquery("SELECT * FROM {{table}} WHERE `id` = '". $GalaxyRowPlanet["id_owner"] ."';", 'users', true);
        }
      }

      if ($GalaxyRow["id_luna"] != 0) {
        $GalaxyRowMoon   = doquery("SELECT * FROM {{table}} WHERE `id` = '". $GalaxyRow["id_luna"] ."';", 'lunas', true);
        if ($GalaxyRowMoon["destruyed"] != 0) {
          CheckAbandonMoonState ($GalaxyRowMoon);
        }
      }
    }

    $parse['planetN']       = $Planet;
    $parse['rowPlanet']     = GalaxyRowPlanet     ( $GalaxyRow, $GalaxyRowPlanet, $GalaxyRowUser, $Galaxy, $System, $Planet, 1 );
    $parse['rowPlanetName'] = GalaxyRowPlanetName ( $GalaxyRow, $GalaxyRowPlanet, $GalaxyRowUser, $Galaxy, $System, $Planet, 1 );

    $parse['rowMoon']       = GalaxyRowMoon       ( $GalaxyRow, $GalaxyRowMoon  , $GalaxyRowUser, $Galaxy, $System, $Planet, 3 );
    $parse['rowDebris']     = GalaxyRowDebris     ( $GalaxyRow, $GalaxyRowPlanet, $GalaxyRowUser, $Galaxy, $System, $Planet, 2 );


    $ResultUser  = '';
    if ($GalaxyRowUser && !$GalaxyRowPlanet["destruyed"]) {
      $UserPoints    = doquery("SELECT * FROM `{{table}}` WHERE `stat_type` = '1' AND `stat_code` = '1' AND `id_owner` = '". $user['id'] ."'", 'statpoints', true);
      $User2Points   = doquery("SELECT * FROM `{{table}}` WHERE `stat_type` = '1' AND `stat_code` = '1' AND `id_owner` = '". $GalaxyRowUser['id'] ."'", 'statpoints', true);
      $CurrentPoints = $UserPoints['total_points'];
      $RowUserPoints = $User2Points['total_points'];
      $CurrentLevel  = $CurrentPoints * $config->noobprotectionmulti;
      $RowUserLevel  = $RowUserPoints * $config->noobprotectionmulti;

      if ($GalaxyRowUser['bana'] == 1 AND $GalaxyRowUser['urlaubs_modus'] == 1) {
        $Systemtatus2 = $lang['vacation_shortcut']." <a href=\"banned.php\"><span class=\"banned\">".$lang['banned_shortcut']."</span></a>";
        $Systemtatus  = "<span class=\"vacation\">";
      } elseif ($GalaxyRowUser['bana']) {
        $Systemtatus2 = "<a href=\"banned.php\"><span class=\"banned\">".$lang['banned_shortcut']."</span></a>";
        $Systemtatus  = "";
      } elseif ($GalaxyRowUser['urlaubs_modus'] == 1) {
        $Systemtatus2 = "<span class=\"vacation\">".$lang['vacation_shortcut']."</span>";
        $Systemtatus  = "<span class=\"vacation\">";
      } elseif ($GalaxyRowUser['onlinetime'] < (time()-60 * 60 * 24 * 7) AND $GalaxyRowUser['onlinetime'] > (time()-60 * 60 * 24 * 28)) {
        $Systemtatus2 = "<span class=\"inactive\">".$lang['inactif_7_shortcut']."</span>";
        $Systemtatus  = "<span class=\"inactive\">";
      } elseif ($GalaxyRowUser['onlinetime'] < (time()-60 * 60 * 24 * 28)) {
        $Systemtatus2 = "<span class=\"inactive\">".$lang['inactif_7_shortcut']."</span><span class=\"longinactive\"> ".$lang['inactif_28_shortcut']."</span>";
        $Systemtatus  = "<span class=\"longinactive\">";
      } elseif ($RowUserLevel < $CurrentPoints AND $config->noobprotection AND $RowUserPoints < $config->noobprotectiontime * 1000 ) {
        $Systemtatus2 = "<span class=\"noob\">".$lang['weak_player_shortcut']."</span>";
        $Systemtatus  = "<span class=\"noob\">";
      } elseif ($RowUserPoints > $CurrentLevel AND $config->noobprotection AND $config->noobprotectiontime * 1000 > $CurrentPoints) {
        $Systemtatus2 = $lang['strong_player_shortcut'];
        $Systemtatus  = "<span class=\"strong\">";
      } else {
        $Systemtatus2 = "";
        $Systemtatus  = "";
      }

      if ($Systemtatus2) {
        $Systemtatus6 = "<font color=\"white\">(</font>";
        $Systemtatus7 = "<font color=\"white\">)</font>";
      } else {
        $Systemtatus6 = "";
        $Systemtatus7 = "";
      }

      $admin = "";
      if(SHOW_ADMIN && $GalaxyRowUser['authlevel']) {
        $admin = "<font color=\"lime\"><blink>{$lang['user_level_shortcut'][$GalaxyRowUser['authlevel']]}</blink></font>";
      }
      if ($GalaxyRowUser['id'] != $user['id']) {
        $parse['isShowUserOther'] = '';
        $GalaxyRowUser['isShowUserOther'] = 1;
      } else {
        $parse['isShowUserOther'] = 'class="hide"';
        $GalaxyRowUser['isShowUserOther'] = 0;
      }
      $GalaxyRowUser['rank'] = intval($User2Points['total_rank']);

      $ResultUser .= $Systemtatus . $GalaxyRowUser['username']."</span>";
      $ResultUser .= $Systemtatus6;
      $ResultUser .= $Systemtatus;
      $ResultUser .= $Systemtatus2;
      $ResultUser .= $Systemtatus7." ".$admin;
      $ResultUser .= "</span>";

      $parse['isShowUser'] = '';
      $parse['username'] = $GalaxyRowUser['username'];
      $parse['id'] = $GalaxyRowUser['id'];
      $parse['rank'] = $GalaxyRowUser['rank'];
      $cached['users'][$GalaxyRowUser['id']] = $GalaxyRowUser;
    }else{
      $parse['isShowUser'] = 'class="hide"';
      $parse['username'] = '';
      $parse['id'] = '';
      $parse['rank'] = '';
    }

    $parse['rowUser']   = $ResultUser;

    $ResultAlly = '';
    $parse['isShowAlly'] = ' class="hide"';
    if ($GalaxyRowUser['ally_id']) {

      if($cached['allies'][$GalaxyRowUser['ally_id']])
        $allyquery = $cached['allies'][$GalaxyRowUser['ally_id']];
      else{
        $allyquery = doquery("SELECT * FROM `{{table}}` WHERE `id` = '" . $GalaxyRowUser['ally_id'] . "'", "alliance", true);
        $cached['allies'][$GalaxyRowUser['ally_id']] = $allyquery;
      }

      $parse['ally_class']  = '';
      if ($allyquery['id']) {
        $parse['ally_id']  = $allyquery['id'];
        $parse['ally_tag'] = $allyquery['ally_tag'];

        if ($user['ally_id'] == $GalaxyRowUser['ally_id'])
          $parse['ally_class'] = "allymember";

        $parse['isShowAlly'] = '';
      }
    }

    $parse['rowActions']    = GalaxyRowActions    ( $GalaxyRow, $GalaxyRowPlanet, $GalaxyRowUser, $Galaxy, $System, $Planet, 0 );
    $Result .= parsetemplate(gettemplate('gal_main_row'), $parse);
  }

  $Result .= '<script type="text/javascript" language="JavaScript">';
  foreach($cached['users'] as $PlanetUser){
    $Result .= "users[{$PlanetUser['id']}] = new Array('{$PlanetUser['username']}','{$PlanetUser['rank']}','{$PlanetUser['isShowUserOther']}','{$PlanetUser['id']}');";
  }

  foreach($cached['allies'] as $PlanetAlly){
    $Result .= "allies[{$PlanetAlly['id']}] = new Array('{$PlanetAlly['ally_web']}','{$PlanetAlly['ally_name']}','{$PlanetAlly['ally_members']}');";
  }
  $Result .= '</script>';

  return $Result;
}
?>