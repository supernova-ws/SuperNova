<?php

/**
 * ShowTopNavigationBar.php
 *
 * @version 1
 * @copyright 2008 By Chlorel for XNova
 */

function ShowTopNavigationBar ( $CurrentUser, $CurrentPlanet ) {
  global $lang, $_GET, $game_config;

  if ($CurrentUser) {
    if ( !$CurrentPlanet ) {
      $CurrentPlanet = doquery("SELECT * FROM `{{table}}` WHERE `id` = '". $CurrentUser['current_planet'] ."';", 'planets', true);
    }

    // Actualisation des ressources de la planete
    PlanetResourceUpdate ( $CurrentUser, $CurrentPlanet, time() );

    $NavigationTPL       = gettemplate('topnav');

    $dpath               = (!$CurrentUser["dpath"]) ? DEFAULT_SKINPATH : $CurrentUser["dpath"];
    $parse               = $lang;
    $parse['dpath']      = $dpath;
    $parse['image']      = $CurrentPlanet['image'];

    // Genearation de la combo des planetes du joueur
    $parse['planetlist'] = '';
    $ThisUsersPlanets    = SortUserPlanets ( $CurrentUser );
    while ($CurPlanet = mysql_fetch_array($ThisUsersPlanets)) {
      if ($CurPlanet["destruyed"] == 0) {
        $parse['planetlist'] .= "\n<option ";
        if ($CurPlanet['id'] == $CurrentUser['current_planet']) {
          // Bon puisque deja on s'y trouve autant le marquer
          $parse['planetlist'] .= "selected=\"selected\" ";
        }
        $parse['planetlist'] .= "value=\"?cp=".$CurPlanet['id']."";
        $parse['planetlist'] .= "&amp;mode=".$_GET['mode'];
        $parse['planetlist'] .= "&amp;re=0\">";

        // Nom et coordonnées de la planete
        $parse['planetlist'] .= "".$CurPlanet['name'];
        $parse['planetlist'] .= "&nbsp;[".$CurPlanet['galaxy'].":";
        $parse['planetlist'] .= "".$CurPlanet['system'].":";
        $parse['planetlist'] .= "".$CurPlanet['planet'];
        $parse['planetlist'] .= "]&nbsp;&nbsp;</option>";
      }
    }

    $energy = pretty_number($CurrentPlanet["energy_max"] + $CurrentPlanet["energy_used"]) . "/" . pretty_number($CurrentPlanet["energy_max"]);
    // Energie
    if ($CurrentPlanet["energy_used"] > $CurrentPlanet["energy_max"]) {
      $parse['energy'] = colorRed($energy);
    } else {
      $parse['energy'] = $energy;
    }

    // Metal
    $metal = pretty_number($CurrentPlanet["metal"]);
    if (($CurrentPlanet["metal"] > $CurrentPlanet["metal_max"])) {
      $parse['metal'] = colorRed($metal);
    } else {
      $parse['metal'] = $metal;
    }
    // Cristal
    $crystal = pretty_number($CurrentPlanet["crystal"]);
    if (($CurrentPlanet["crystal"] > $CurrentPlanet["crystal_max"])) {
      $parse['crystal'] = colorRed($crystal);
    } else {
      $parse['crystal'] = $crystal;
    }
    // Deuterium
    $deuterium = pretty_number($CurrentPlanet["deuterium"]);
    if (($CurrentPlanet["deuterium"] > $CurrentPlanet["deuterium_max"])) {
      $parse['deuterium'] = colorRed($deuterium);
    } else {
      $parse['deuterium'] = $deuterium;
    }

    // Max Energie
    $res = $CurrentPlanet['energy_max'] - $CurrentPlanet['energy_used'];
    $parse['energy_total'] = colorNumber(pretty_number($res));
    $parse['energy_max'] = pretty_number($CurrentPlanet["energy_max"]);


// Metal maximo
if ($CurrentPlanet["metal_max"] < $CurrentPlanet["metal"]) {
  $parse['metal_max'] = '<font color="#ff0000">';
} else {
  $parse['metal_max'] = '<font color="#00ff00">';
}
$parse['metal_max'] .= pretty_number($CurrentPlanet["metal_max"] / 1) . " {$lang['']}</font>";

// Cristal maximo
if ($CurrentPlanet["crystal_max"] < $CurrentPlanet["crystal"]) {
  $parse['crystal_max'] = '<font color="#ff0000">';
} else {
  $parse['crystal_max'] = '<font color="#00ff00">';
}
$parse['crystal_max'] .= pretty_number($CurrentPlanet["crystal_max"] / 1) . " {$lang['']}";

// Deuterio maximo
if ($CurrentPlanet["deuterium_max"] < $CurrentPlanet["deuterium"]) {
  $parse['deuterium_max'] = '<font color="#ff0000">';
} else {
  $parse['deuterium_max'] = '<font color="#00ff00">';
}
$parse['deuterium_max'] .= pretty_number($CurrentPlanet["deuterium_max"] / 1) . " {$lang['']}";

$parse['metal_perhour'] .= $CurrentPlanet["metal_perhour"];
$parse['crystal_perhour'] .= $CurrentPlanet["crystal_perhour"];
$parse['deuterium_perhour'] .= $CurrentPlanet["deuterium_perhour"];

$parse['metalh'] .= round($CurrentPlanet["metal"]);
$parse['crystalh'] .= round($CurrentPlanet["crystal"]);
$parse['deuteriumh'] .= round($CurrentPlanet["deuterium"]);

$parse['metal_mmax'] .= $CurrentPlanet["metal_max"];
$parse['crystal_mmax'] .= $CurrentPlanet["crystal_max"];
$parse['deuterium_mmax'] .= $CurrentPlanet["deuterium_max"];



    // Message
    if ($CurrentUser['new_message'] > 0) {
      $parse['message'] = "<a href=\"messages.php\"><blink>[ ". $CurrentUser['new_message'] ." ]</blink></a>";
    } else {
      $parse['message'] = "0";
    }
    ///dark mater
     $rpg_points = pretty_number($CurrentUser["rpg_points"]);
     $parse['rpg_points'] = $rpg_points;
    // Le tout passe dans la template
    $parse['resource_multiplier'] = ($game_config['resource_multiplier']);
    $parse['rpg_geologue'] = ( 1 + ( $CurrentUser['rpg_geologue']  * 0.05 ));
    $TopBar = parsetemplate( $NavigationTPL, $parse);
  } else {
    $TopBar = "";
  }

  return $TopBar;
}

?>