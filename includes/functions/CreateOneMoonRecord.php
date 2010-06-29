<?php

/**
 * CreateOneMoonRecord.php
 *
 * GAL: Create moon record
 *
 * V2.0  - copyright (c) 2010 by Gorlum for http://supernova.ws
 *   [+] Deep rewrite to rid of using `galaxy` and `lunas` tables greatly reduce numbers of SQL-queries
 * @version 1.1
 * @copyright 2008
*/

function CreateOneMoonRecord ( $Galaxy, $System, $Planet, $Owner, $MoonID, $MoonName, $Chance ) {
  global $lang;

  $PlanetName = '';

  $moon = doquery (
    "SELECT * FROM `{{planets}}` WHERE `galaxy` = '{$Galaxy}' AND `system` = '{$System}' AND `planet` = '{$Planet}' AND `planet_type` = 3;",
  '', true);

  if (!$moon['id']) {
    $MoonPlanet = doquery (
      "SELECT * FROM `{{planets}}` WHERE `galaxy` = '{$Galaxy}' AND `system` = '{$System}' AND `planet` = '{$Planet}' AND `planet_type` = 1;",
    '', true);

    if ($MoonPlanet['id']) {
      $PlanetName = $MoonPlanet['name'];

      $size       = rand ( $Chance * 100 + 1000, $Chance * 200 + 2999 );
      $mintemp    = $MoonPlanet['temp_min'] - rand(10, 45);
      $maxtemp    = $MoonPlanet['temp_max'] - rand(10, 45);
      $MoonName   = $MoonName ? $MoonName : $lang['sys_moon'];

      $QryInsertMoonInPlanet  = "INSERT INTO `{{planets}}` SET ";
      $QryInsertMoonInPlanet .= "`name` = '{$MoonName}', ";
      $QryInsertMoonInPlanet .= "`id_owner` = '{$Owner}', ";
      $QryInsertMoonInPlanet .= "`galaxy` = '{$Galaxy}', ";
      $QryInsertMoonInPlanet .= "`system` = '{$System}', ";
      $QryInsertMoonInPlanet .= "`planet` = '{$Planet}', ";
      $QryInsertMoonInPlanet .= "`last_update` = '". time() ."', ";
      $QryInsertMoonInPlanet .= "`planet_type` = '3', ";
      $QryInsertMoonInPlanet .= "`parent_planet` = {$MoonPlanet['id']}, ";
      $QryInsertMoonInPlanet .= "`image` = 'mond', ";
      $QryInsertMoonInPlanet .= "`diameter` = '{$size}', ";
      $QryInsertMoonInPlanet .= "`field_max` = '1', ";
      $QryInsertMoonInPlanet .= "`temp_min` = '{$maxtemp}', ";
      $QryInsertMoonInPlanet .= "`temp_max` = '{$mintemp}', ";
      $QryInsertMoonInPlanet .= "`metal` = '0', ";
      $QryInsertMoonInPlanet .= "`metal_perhour` = '0', ";
      $QryInsertMoonInPlanet .= "`metal_max` = '".BASE_STORAGE_SIZE."', ";
      $QryInsertMoonInPlanet .= "`crystal` = '0', ";
      $QryInsertMoonInPlanet .= "`crystal_perhour` = '0', ";
      $QryInsertMoonInPlanet .= "`crystal_max` = '".BASE_STORAGE_SIZE."', ";
      $QryInsertMoonInPlanet .= "`deuterium` = '0', ";
      $QryInsertMoonInPlanet .= "`deuterium_perhour` = '0', ";
      $QryInsertMoonInPlanet .= "`deuterium_max` = '".BASE_STORAGE_SIZE."';";
      doquery( $QryInsertMoonInPlanet);
    }
  }
  return $PlanetName;
}
?>