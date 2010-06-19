<?php

/**
 * ShowGalaxyRows.php
 *
 * @version 1.0
 * @copyright 2008 By Chlorel for XNova
 */

function ShowGalaxyRows ($Galaxy, $System) {
  global $lang, $planetcount, $CurrentRC, $dpath, $user;

  $Result = "";
  for ($Planet = 1; $Planet < 16; $Planet++) {
    unset($GalaxyRowPlanet);
    unset($GalaxyRowMoon);
    unset($GalaxyRowava);
    unset($GalaxyRowPlayer);
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
          $GalaxyRowPlayer = $cached['users'][$GalaxyRowPlanet["id_owner"]];
        else{
          $GalaxyRowPlayer = doquery("SELECT * FROM {{table}} WHERE `id` = '". $GalaxyRowPlanet["id_owner"] ."';", 'users', true);
          $cached['users'][$GalaxyRowPlanet["id_owner"]] = $GalaxyRowPlayer;
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
    $parse['rowPlanet']     = GalaxyRowPlanet     ( $GalaxyRow, $GalaxyRowPlanet, $GalaxyRowPlayer, $Galaxy, $System, $Planet, 1 );
    $parse['rowPlanetName'] = GalaxyRowPlanetName ( $GalaxyRow, $GalaxyRowPlanet, $GalaxyRowPlayer, $Galaxy, $System, $Planet, 1 );

    $parse['rowMoon']       = GalaxyRowMoon       ( $GalaxyRow, $GalaxyRowMoon  , $GalaxyRowPlayer, $Galaxy, $System, $Planet, 3 );
    $parse['rowDebris']     = GalaxyRowDebris     ( $GalaxyRow, $GalaxyRowPlanet, $GalaxyRowPlayer, $Galaxy, $System, $Planet, 2 );
    $parse['rowUser']       = GalaxyRowUser       ( $GalaxyRow, $GalaxyRowPlanet, $GalaxyRowPlayer, $Galaxy, $System, $Planet, 0 );


    $ResultAlly = '';
    if ($GalaxyRowPlayer['ally_id']) {

      if($cached['allies'][$GalaxyRowPlayer['ally_id']])
        $allyquery = $cached['allies'][$GalaxyRowPlayer['ally_id']];
      else{
        $allyquery = doquery("SELECT * FROM `{{table}}` WHERE `id` = '" . $GalaxyRowPlayer['ally_id'] . "'", "alliance", true);
        $cached['allies'][$GalaxyRowPlayer['ally_id']] = $allyquery;
      }

      if ($allyquery['id']) {
        $ResultAlly .= "<a style=\"cursor: pointer;\"";
        $ResultAlly .= " onmouseover='javascript:showAlly({$allyquery['id']});'";
/*
        $ResultAlly .= " onmouseover='return overlib(\"";

        $ResultAlly .= "<table>";
        $ResultAlly .= "<tr>";
        $ResultAlly .= "<td class=c><center>".$lang['Alliance']."&nbsp;". $allyquery['ally_name'] ."<br>" . $lang['gal_sys_members'] . $allyquery['ally_members'] . "</center></td>";
        $ResultAlly .= "</tr>";
        $ResultAlly .= "<tr><th><a href=alliance.php?mode=ainfo&a=". $allyquery['id'] .">".$lang['gl_ally_internal']."</a></th></tr>";
        $ResultAlly .= "<tr>";
        $ResultAlly .= "<th><a href=stat.php?start=101&who=ally>".$lang['gl_stats']."</a></th>";
        if ($allyquery["ally_web"]) {
          $ResultAlly .= "</tr><tr>";
          $ResultAlly .= "<th><a href=". $allyquery["ally_web"] ." target=_new>".$lang['gl_ally_web']."</th>";
        }
        $ResultAlly .= "</tr>";
        $ResultAlly .= "</table>\"";
        $ResultAlly .= ", STICKY, MOUSEOFF, DELAY, 750, CENTER, OFFSETX, -40, OFFSETY, -40 );'";
    */
        $ResultAlly .= " onmouseout='return nd();'>";
        if ($user['ally_id'] == $GalaxyRowPlayer['ally_id']) {
          $class = "allymember";
        } else {
          $class = "";
        }
        $ResultAlly .= "<span class=\"{$class}\">". $allyquery['ally_tag'] ."</span></a>";
      }
    }
    $parse['rowAlly']       = $ResultAlly;


    $parse['rowActions']    = GalaxyRowActions    ( $GalaxyRow, $GalaxyRowPlanet, $GalaxyRowPlayer, $Galaxy, $System, $Planet, 0 );
    $Result .= parsetemplate(gettemplate('gal_main_row'), $parse);
  }

  $Result .= '<script type="text/javascript" language="JavaScript">';
  foreach($cached['users'] as $PlanetUser){
  }

  foreach($cached['allies'] as $PlanetAlly){
    $Result .= "allies[{$PlanetAlly['id']}] = new Array('{$PlanetAlly['ally_web']}','{$PlanetAlly['ally_name']}','{$PlanetAlly['ally_members']}');";

// 0 - ally_web
// 1 - ally_name
// 2 - ally_members

  }
  $Result .= '</script>';

  return $Result;
}
?>