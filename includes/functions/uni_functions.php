<?php

/**
 * uni_create_planet
 *
 * @version 1.0
 * @copyright 2008 By Chlorel for XNova
 */

function chance ($percent) {
  $chance = mt_rand(0,100);
  if($percent <= $chance){
    return true;
  }else{
    return false;
  }
}

function PlanetSizeRandomiser ($Position, $HomeWorld = false) {
  global $config, $user;

  //$ClassicBase           = 163;
  if (!$HomeWorld) {
    if(chance(60)){
      $Average          = array ( 64, 68, 73,173,167,155,144,150,159,101, 98,105,110, 84,101);
      $SixtyMin          = array ( 39, 53, 34, 83, 84, 82,116,123,129, 62, 81, 85, 60, 42, 54);
      $SixtyMax          = array ( 89, 83, 82,306,232,328,173,177,203,122,116,129,191,172,150);

      $FrmAvgMin          = $SixtyMin[$Position - 1] - $Average[$Position - 1];
      $FrmAvgMax          = $SixtyMax[$Position - 1] - $Average[$Position - 1];

      $DifInDeveation      = $FrmAvgMin + $FrmAvgMax;
      $BaseIncDeveatn      = $Average[$Position - 1] - ($DifInDeveation / 2);

      $PlanetFieldsLow  = mt_rand($SixtyMin[$Position - 1], $BaseIncDeveatn);
      $PlanetFieldsUpp  = mt_rand($BaseIncDeveatn, $SixtyMax[$Position - 1]);
      $PlanetFields      = ($PlanetFieldsLow + $PlanetFieldsUpp) / 2;

    }else{
      $MinSize          =  30;
      $MaxSize          = 330;
      $PlanetFields      = mt_rand($MinSize, $MaxSize);
    }
  } else {
    $PlanetFields     = $config->initial_fields;
  }
//  $SettingSize          = $config->initial_fields;
//  $PlanetFields          = ($PlanetFields / $ClassicBase) * $config->initial_fields;
  $PlanetFields          = floor($PlanetFields);

  $PlanetSize           = ($PlanetFields ^ (14 / 1.5)) * 75;

  $return['diameter']   = $PlanetSize;
  $return['field_max']  = $PlanetFields;
  return $return;
}

function uni_create_planet($Galaxy, $System, $Position, $PlanetOwnerID, $PlanetName = '', $HomeWorld = false) {
  global $lang, $config;

  // Avant tout, on verifie s'il existe deja une planete a cet endroit
  $QrySelectPlanet  = "SELECT `id` ";
  $QrySelectPlanet .= "FROM `{{planets}}` ";
  $QrySelectPlanet .= "WHERE ";
  $QrySelectPlanet .= "`galaxy` = '". $Galaxy ."' AND ";
  $QrySelectPlanet .= "`system` = '". $System ."' AND ";
  $QrySelectPlanet .= "`planet` = '". $Position ."';";
  $PlanetExist = doquery( $QrySelectPlanet, '', true);

  // Si $PlanetExist est autre chose que false ... c'est qu'il y a quelque chose la bas ...
  // C'est donc aussi que je ne peux pas m'y poser !!
  if (!$PlanetExist) {
    $planet                      = PlanetSizeRandomiser ($Position, $HomeWorld);
    $planet['diameter']          = ($planet['field_max'] ^ (14 / 1.5)) * 75 ;
    $planet['metal']             = BUILD_METAL;
    $planet['crystal']           = BUILD_CRISTAL;
    $planet['deuterium']         = BUILD_DEUTERIUM;
    $planet['metal_perhour']     = $config->metal_basic_income;
    $planet['crystal_perhour']   = $config->crystal_basic_income;
    $planet['deuterium_perhour'] = $config->deuterium_basic_income;
    $planet['metal_max']         = BASE_STORAGE_SIZE;
    $planet['crystal_max']       = BASE_STORAGE_SIZE;
    $planet['deuterium_max']     = BASE_STORAGE_SIZE;

    // Posistion  1 -  3: 80% entre  40 et  70 Cases (  55+ / -15 )
    // Posistion  4 -  6: 80% entre 120 et 310 Cases ( 215+ / -95 )
    // Posistion  7 -  9: 80% entre 105 et 195 Cases ( 150+ / -45 )
    // Posistion 10 - 12: 80% entre  75 et 125 Cases ( 100+ / -25 )
    // Posistion 13 - 15: 80% entre  60 et 190 Cases ( 125+ / -65 )

    $planet['galaxy'] = $Galaxy;
    $planet['system'] = $System;
    $planet['planet'] = $Position;

    if ($Position == 1 || $Position == 2 || $Position == 3) {
      $PlanetType         = array('trocken');
      $PlanetClass        = array('planet');
      $PlanetDesign       = array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10');
      $planet['temp_min'] = rand(0, 100);
      $planet['temp_max'] = $planet['temp_min'] + 40;
    } elseif ($Position == 4 || $Position == 5 || $Position == 6) {
      $PlanetType         = array('dschjungel');
      $PlanetClass        = array('planet');
      $PlanetDesign       = array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10');
      $planet['temp_min'] = rand(-25, 75);
      $planet['temp_max'] = $planet['temp_min'] + 40;
    } elseif ($Position == 7 || $Position == 8 || $Position == 9) {
      $PlanetType         = array('normaltemp');
      $PlanetClass        = array('planet');
      $PlanetDesign       = array('01', '02', '03', '04', '05', '06', '07');
      $planet['temp_min'] = rand(-50, 50);
      $planet['temp_max'] = $planet['temp_min'] + 40;
    } elseif ($Position == 10 || $Position == 11 || $Position == 12) {
      $PlanetType         = array('wasser');
      $PlanetClass        = array('planet');
      $PlanetDesign       = array('01', '02', '03', '04', '05', '06', '07', '08', '09');
      $planet['temp_min'] = rand(-75, 25);
      $planet['temp_max'] = $planet['temp_min'] + 40;
    } elseif ($Position == 13 || $Position == 14 || $Position == 15) {
      $PlanetType         = array('eis');
      $PlanetClass        = array('planet');
      $PlanetDesign       = array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10');
      $planet['temp_min'] = rand(-100, 10);
      $planet['temp_max'] = $planet['temp_min'] + 40;
    } else {
      $PlanetType         = array('dschjungel', 'gas', 'normaltemp', 'trocken', 'wasser', 'wuesten', 'eis');
      $PlanetClass        = array('planet');
      $PlanetDesign       = array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '00',);
      $planet['temp_min'] = rand(-120, 10);
      $planet['temp_max'] = $planet['temp_min'] + 40;
    }

    if($HomeWorld)
    {
      $planet['temp_min'] = 0;
      $planet['temp_max'] = $planet['temp_min'] + 40;
    }

    $planet['image']       = $PlanetType[ rand( 0, count( $PlanetType ) -1 ) ];
    $planet['image']      .= $PlanetClass[ rand( 0, count( $PlanetClass ) - 1 ) ];
    $planet['image']      .= $PlanetDesign[ rand( 0, count( $PlanetDesign ) - 1 ) ];
    $planet['planet_type'] = 1;
    $planet['id_owner']    = $PlanetOwnerID;
    $planet['last_update'] = time();

    $planet['name']        = $PlanetName ? $PlanetName : $lang['sys_colo_defaultname'];
    if(!$HomeWorld)
    {
      $OwnerName = doquery("SELECT `username` FROM {{users}} WHERE `id` = {$PlanetOwnerID};", '', true);
      $planet['name'] = "{$OwnerName['username']} {$planet['name']}";
    }
    $planet['name'] = mysql_real_escape_string(strip_tags(trim($planet['name'])));

    $QryInsertPlanet  = "INSERT INTO `{{planets}}` SET ";
    $QryInsertPlanet .= "`name` = '".              $planet['name']              ."', ";
    $QryInsertPlanet .= "`id_owner` = '".          $planet['id_owner']          ."', ";
//    $QryInsertPlanet .= "`id_level` = '".          $user['authlevel']           ."', ";
    $QryInsertPlanet .= "`galaxy` = '".            $planet['galaxy']            ."', ";
    $QryInsertPlanet .= "`system` = '".            $planet['system']            ."', ";
    $QryInsertPlanet .= "`planet` = '".            $planet['planet']            ."', ";
    $QryInsertPlanet .= "`last_update` = '".       $planet['last_update']       ."', ";
    $QryInsertPlanet .= "`planet_type` = '".       $planet['planet_type']       ."', ";
    $QryInsertPlanet .= "`image` = '".             $planet['image']             ."', ";
    $QryInsertPlanet .= "`diameter` = '".          $planet['diameter']          ."', ";
    $QryInsertPlanet .= "`field_max` = '".         $planet['field_max']         ."', ";
    $QryInsertPlanet .= "`temp_min` = '".          $planet['temp_min']          ."', ";
    $QryInsertPlanet .= "`temp_max` = '".          $planet['temp_max']          ."', ";
    $QryInsertPlanet .= "`metal` = '".             $planet['metal']             ."', ";
    $QryInsertPlanet .= "`metal_perhour` = '".     $planet['metal_perhour']     ."', ";
    $QryInsertPlanet .= "`metal_max` = '".         $planet['metal_max']         ."', ";
    $QryInsertPlanet .= "`crystal` = '".           $planet['crystal']           ."', ";
    $QryInsertPlanet .= "`crystal_perhour` = '".   $planet['crystal_perhour']   ."', ";
    $QryInsertPlanet .= "`crystal_max` = '".       $planet['crystal_max']       ."', ";
    $QryInsertPlanet .= "`deuterium` = '".         $planet['deuterium']         ."', ";
    $QryInsertPlanet .= "`deuterium_perhour` = '". $planet['deuterium_perhour'] ."', ";
    $QryInsertPlanet .= "`deuterium_max` = '".     $planet['deuterium_max']     ."';";
    doquery( $QryInsertPlanet);

    // On recupere l'id de planete nouvellement créé
    $QrySelectPlanet  = "SELECT `id` FROM `{{planets}}` WHERE ";
    $QrySelectPlanet .= "`galaxy` = '{$planet['galaxy']}' AND `system` = '{$planet['system']}' AND `planet` = '{$planet['planet']}' AND ";
    $QrySelectPlanet .= "`id_owner` = '{$planet['id_owner']}';";
    $GetPlanetID      = doquery( $QrySelectPlanet , '', true);

    $RetValue = $GetPlanetID['id'];
  } else {

    $RetValue = false;
  }

  return $RetValue;
}

/**
 * uni_create_moon.php
 *
 * UNI: Create moon record
 *
 * V2.1  - copyright (c) 2010-2011 by Gorlum for http://supernova.ws
 *   [~] Renamed CreateOneMoonRecord to uni_create_moon
 *   [-] Removed unsed $MoonID parameter from call
 *   [~] PCG1 compliant
 * V2.0  - copyright (c) 2010 by Gorlum for http://supernova.ws
 *   [+] Deep rewrite to rid of using `galaxy` and `lunas` tables greatly reduce numbers of SQL-queries
 * @version 1.1
 * @copyright 2008
*/

function uni_create_moon($pos_galaxy, $pos_system, $pos_planet, $user_id, $moon_chance, $moon_name = '')
{
  global $lang, $time_now;

  $moon_name = '';
  $moon = doquery("SELECT * FROM `{{planets}}` WHERE `galaxy` = '{$pos_galaxy}' AND `system` = '{$pos_system}' AND `planet` = '{$pos_planet}' AND `planet_type` = " . PT_MOON . " LIMIT 1;", '', true);
  if(!$moon['id'])
  {
    $moon_planet = doquery("SELECT * FROM `{{planets}}` WHERE `galaxy` = '{$pos_galaxy}' AND `system` = '{$pos_system}' AND `planet` = '{$pos_planet}' AND `planet_type` = 1 LIMIT 1;", '', true);

    if($moon_planet['id'])
    {
      $base_storage_size = BASE_STORAGE_SIZE;

      $size      = rand($moon_chance * 100 + 1000, $moon_chance * 200 + 2999);
      $temp_min  = $moon_planet['temp_min'] - rand(10, 45);
      $temp_max  = $temp_min + 40;

      $moon_name = $moon_name ? $moon_name : "{$moon_planet['name']} {$lang['sys_moon']}";
      $moon_name_safe = mysql_real_escape_string($moon_name);

      doquery(
        "INSERT INTO `{{planets}}` SET
          `id_owner` = '{$user_id}', `name` = '{$moon_name_safe}', `last_update` = '{$time_now}',
          `galaxy` = '{$pos_galaxy}', `system` = '{$pos_system}', `planet` = '{$pos_planet}', `planet_type` = '3', `parent_planet` = '{$moon_planet['id']}',
          `image` = 'mond', `diameter` = '{$size}', `temp_min` = '{$temp_min}', `temp_max` = '{$temp_max}', `field_max` = '1',
          `metal` = '0', `metal_perhour` = '0', `metal_max` = '{$base_storage_size}',
          `crystal` = '0', `crystal_perhour` = '0', `crystal_max` = '{$base_storage_size}',
          `deuterium` = '0', `deuterium_perhour` = '0', `deuterium_max` = '{$base_storage_size}';"
      );
      $debris_spent = $moon_chance * 1000000;
      $metal_spent  = min($moon_planet['debris_metal'], $debris_spent * mt_rand(50, 75) / 100);
      $crystal_spent = min($moon_planet['debris_crystal'], $debris_spent - $metal_spent);
      $metal_spent = min($moon_planet['debris_metal'], $debris_spent - $crystal_spent); // Need if crystal less then their part
      doquery("UPDATE {{planets}} SET `debris_metal` = GREATEST(0, `debris_metal` - {$metal_spent}), `debris_crystal` = GREATEST(0, `debris_crystal` - {$crystal_spent}) WHERE `id` = {$moon_planet['id']} LIMIT 1;");
    }
  }

  return $moon_name;
}

/*
 *
 * @function SetSelectedPlanet
 *
 * @history
 *    3 - copyright (c) 2009-2011 by Gorlum for http://supernova.ws
 *      [+] Added handling case when current_planet does not exists or didn't belong to user
 *      [+] Moved from SetSelectedPlanet.php
 *      [+] Function now return
 *      [~] Complies with PCG1
 *    2 - copyright (c) 2009-2011 by Gorlum for http://supernova.ws
 *      [~] Security checked for SQL-injection
 *    1 - copyright 2008 By Chlorel for XNova
 *
 */
function SetSelectedPlanet(&$user)
{
  $selected_planet = intval($_GET['cp']);
  $restore_planet = intval($_GET['re']);

  if (isset($selected_planet) && is_numeric($selected_planet) && $selected_planet && isset($restore_planet) && $restore_planet == 0)
  {
    $planet_row = doquery("SELECT `id` FROM {{planets}} WHERE `id` = '{$selected_planet}' AND `id_owner` = '{$user['id']}' LIMIT 1;", '', true);
    if (!$planet_row || !isset($planet_row['id']))
    {
      $selected_planet = $user['id_planet'];
    }
    doquery("UPDATE {{users}} SET `current_planet` = '{$selected_planet}' WHERE `id` = '{$user['id']}' LIMIT 1;");
    $user['current_planet'] = $selected_planet;
  }

  return $user['current_planet'];
}

/**
 * SortUserPlanets.php
 *
 * @version 1.0
 * @copyright 2008 By Chlorel for XNova
 */
function SortUserPlanets($CurrentUser, $planet = false, $field_list = '', $conditions = '')
{
  $Order = ( $CurrentUser['planet_sort_order'] == SORT_DESCENDING ) ? "DESC" : "ASC";
  $Sort = $CurrentUser['planet_sort'];

  if($field_list != '*')
  {
    $field_list = "`id`, `name`, `galaxy`, `system`, `planet`, `planet_type`{$field_list}";
  }

  $QryPlanets = "SELECT {$field_list} FROM {{planets}} WHERE `id_owner` = '{$CurrentUser['id']}' {$conditions} ";
  if ($planet)
  {
    $QryPlanets .= "AND `id` <> {$planet['id']} ";
  }

  $QryPlanets .= 'ORDER BY ';
  if ($Sort == SORT_ID)
  {
    $QryPlanets .= "`id` {$Order}";
  }
  elseif ($Sort == SORT_LOCATION)
  {
    $QryPlanets .= "`galaxy`, `system`, `planet`, `planet_type` {$Order}";
  }
  elseif ($Sort == SORT_NAME)
  {
    $QryPlanets .= "`name` {$Order}";
  }
  elseif ($Sort == SORT_SIZE)
  {
    $QryPlanets .= "(`field_max` + `terraformer` * 5 + `mondbasis` * 3) {$Order}";
  }

  $Planets = doquery($QryPlanets);
  return $Planets;
}

// ----------------------------------------------------------------------------------------------------------------
function uni_render_coordinates($from, $prefix = '')
{
  return "[{$from[$prefix . 'galaxy']}:{$from[$prefix . 'system']}:{$from[$prefix . 'planet']}]";
}

function uni_render_planet($from)
{
  return "{$from['name']} [{$from['galaxy']}:{$from['system']}:{$from['planet']}]";
}

function uni_render_coordinates_url($from, $prefix = '', $page = 'galaxy.php')
{
  return "{$page}" . (strpos($page, '?') === false ? '?' : '&') . "galaxy={$from[$prefix . 'galaxy']}&system={$from[$prefix . 'system']}&planet={$from[$prefix . 'planet']}";
}

function uni_render_coordinates_href($from, $prefix = '', $mode = 0, $fleet_type = '')
{
  return '<a href="' . uni_render_coordinates_url($from, $prefix, "galaxy.php?mode={$mode}") . '"' . ($fleet_type ? " {$fleet_type}" : '') . '>' . uni_render_coordinates($from, $prefix) . '</a>';
}

?>
