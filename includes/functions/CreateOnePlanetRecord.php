<?php

/**
 * CreateOnePlanetRecord.php
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

function CreateOnePlanetRecord($Galaxy, $System, $Position, $PlanetOwnerID, $PlanetName = '', $HomeWorld = false) {
  global $lang;

  // Avant tout, on verifie s'il existe deja une planete a cet endroit
  $QrySelectPlanet  = "SELECT `id` ";
  $QrySelectPlanet .= "FROM `{{table}}` ";
  $QrySelectPlanet .= "WHERE ";
  $QrySelectPlanet .= "`galaxy` = '". $Galaxy ."' AND ";
  $QrySelectPlanet .= "`system` = '". $System ."' AND ";
  $QrySelectPlanet .= "`planet` = '". $Position ."';";
  $PlanetExist = doquery( $QrySelectPlanet, 'planets', true);

  // Si $PlanetExist est autre chose que false ... c'est qu'il y a quelque chose là bas ...
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

    $QryInsertPlanet  = "INSERT INTO `{{table}}` SET ";
    $QryInsertPlanet .= "`name` = '".              $planet['name']              ."', ";
    $QryInsertPlanet .= "`id_owner` = '".          $planet['id_owner']          ."', ";
    $QryInsertPlanet .= "`id_level` = '".          $user['authlevel']           ."', ";
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
    doquery( $QryInsertPlanet, 'planets');

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

?>
