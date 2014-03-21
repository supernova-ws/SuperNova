<?php

/**
 * uni_create_planet
 *
 * @version 1.0
 * @copyright 2008 By Chlorel for XNova
 */

function PlanetSizeRandomiser ($Position, $HomeWorld = false) {
  global $config;

  //$ClassicBase           = 163;
  if(!$HomeWorld)
  {
    if(mt_rand(0,100) >= 60){
      $Average          = array ( 64, 68, 73,173,167,155,144,150,159,101, 98,105,110, 84,101);
      $SixtyMin          = array ( 39, 53, 34, 83, 84, 82,116,123,129, 62, 81, 85, 60, 42, 54);
      $SixtyMax          = array ( 89, 83, 82,306,232,328,173,177,203,122,116,129,191,172,150);

      $FrmAvgMin          = $SixtyMin[$Position - 1] - $Average[$Position - 1];
      $FrmAvgMax          = $SixtyMax[$Position - 1] - $Average[$Position - 1];

      $DifInDeveation      = $FrmAvgMin + $FrmAvgMax;
      // $DifInDeveation = $SixtyMin[$Position - 1] + $SixtyMax[$Position - 1] - 2 * $Average[$Position - 1];
      $BaseIncDeveatn      = $Average[$Position - 1] - ($DifInDeveation / 2);
      // $BaseIncDeveatn = 2 * $Average[$Position - 1] - ($SixtyMin[$Position - 1] + $SixtyMax[$Position - 1]) / 2

      $PlanetFieldsLow  = mt_rand($SixtyMin[$Position - 1], $BaseIncDeveatn);
      $PlanetFieldsUpp  = mt_rand($BaseIncDeveatn, $SixtyMax[$Position - 1]);
      $PlanetFields      = ($PlanetFieldsLow + $PlanetFieldsUpp) / 2;

    }else{
      $MinSize          =  30;
      $MaxSize          = 330;
      $PlanetFields      = mt_rand($MinSize, $MaxSize);
    }
  }
  else
  {
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
  global $lang, $config, $sn_data;

/*
 Типы планет по плотности (г/см^3) - добыча при средней температуре:
1. <2 - Лёд (метановый, водный, водородный итд) + Газ (местами водород). Металл 0 Кристалл -- Дейтерий++
2. 2-3.5 Силикат (кремний) + Водяной лёд + Газ (водород, метан). Метал --. Кристалл норма. Дейтерий +
3. 3.5-5 - Камень. Металл - Кристалл+ Дейтерий норма
4. 5-6 - Руда. Металл норма. Кристалл норма. Дейтерий норма
5. 6-7 - Металл. Металл +. Кристалл -. Дейтерий -
6. >7 - тяжелый металл. Металл ++ Кристалл -- Дейтерий --

sn_rand_gauss_range($range_start, $range_end, $round = true, $strict = 4)

1-2-3 0..100
4-5-6 -25..75
7-8-9 -50..50
10-11-12 -75..25
13-14-15 -100..10
16+ -120..10


Типы планеты по средней температуре:
1. Замороженная - меньше -183 градусов Цельсия. Метановый лёд
2. Холодная - от -183 до -161 градусов. Жидкий метан, водный лёд
3. Ледяная - от -161 до -20. Газообразный метан, водный лёд
4. Земного типа - от -20 до +40 градусов
5. Горячая - от +40 до +80 градусов
6. Инферно - выше +80 градусов


 */


  /*
  $density = array(0,0,0,0,0,0,0,);

  for($i = 0;$i<10000;$i++)
  {
    $q = sn_rand_gauss_range(850, 9250, true, 3);
    if($q < 2000)
    {
      $density[0]++;
    }
    elseif($q < 3250)
    {
      $density[1]++;
    }
    elseif($q < 4500)
    {
      $density[2]++;
    }
    elseif($q < 5750)
    {
      $density[3]++;
    }
    elseif($q < 7000)
    {
      $density[4]++;
    }
    elseif($q < 8250)
    {
      $density[5]++;
    }
    else
    {
      $density[6]++;
    }
  //  pdump($q);
  }

  foreach($density as $key => $value)
  {
    echo $key,' ', $value, ' ',  str_repeat('*', $value/30), '<br />';
  0. 0.75-2 - Лёд (метановый, водный, водородный итд) + Газ (местами водород).    Металл  25%  Кристалл  25%  Дейтерий 175%  225%+
  1. 2-3.25 Силикат (кремний) + Водяной лёд + Газ (водород, метан).               Метал   25%  Кристалл 150%  Дейтерий  75%  250%+
  2. 3.25-4.5 - Камень.                                                           Металл  50%  Кристалл 125%  Дейтерий 100%  275%+
  3. 4.5-5.75 - Стандарт                                                          Металл 100%. Кристалл 100%  Дейтерий 100%  300%+
  4. 5.25-6.50 - Руда                                                             Металл 125%  Кристалл  50%  Дейтерий 100%  275%+
  5  6.50-7.75   Металл                                                           Металл 150%  Кристалл  25%  Дейтерий  75%  250%+
  6. >7.75-9 - тяжелый металл.                                                    Металл 175%  Кристалл  25%  Дейтерий  25%  225%+

  Лёд
  Силикат
  Камень
  Стандарт
  Руда
  Металл
  Тяжмет


  }

  /*
  */
  /*
  $planet_density = array(
    2000 => array(RES_METAL => 0.25, RES_CRYSTAL => 0.25, RES_DEUTERIUM => 1.75),
    3250 => array(RES_METAL => 0.25, RES_CRYSTAL => 1.50, RES_DEUTERIUM => 0.75),
    4500 => array(RES_METAL => 0.50, RES_CRYSTAL => 1.25, RES_DEUTERIUM => 1.00),
    5750 => array(RES_METAL => 1.00, RES_CRYSTAL => 1.00, RES_DEUTERIUM => 1.00),
    7000 => array(RES_METAL => 1.25, RES_CRYSTAL => 0.50, RES_DEUTERIUM => 1.00),
    8250 => array(RES_METAL => 1.50, RES_CRYSTAL => 0.25, RES_DEUTERIUM => 0.75),
    9250 => array(RES_METAL => 1.75, RES_CRYSTAL => 0.25, RES_DEUTERIUM => 0.25),
  );
  $planet_density = array(
     850 => array(RES_METAL => 0.10, RES_CRYSTAL => 0.10, RES_DEUTERIUM => 1.30),
    2000 => array(RES_METAL => 0.30, RES_CRYSTAL => 0.20, RES_DEUTERIUM => 1.20),  // Лёд
    3250 => array(RES_METAL => 0.40, RES_CRYSTAL => 1.40, RES_DEUTERIUM => 0.90),  // Силикат
    4500 => array(RES_METAL => 0.80, RES_CRYSTAL => 1.25, RES_DEUTERIUM => 0.80),  // Камень
    5750 => array(RES_METAL => 1.00, RES_CRYSTAL => 1.00, RES_DEUTERIUM => 1.00),  // Норма
    7000 => array(RES_METAL => 2.00, RES_CRYSTAL => 0.75, RES_DEUTERIUM => 0.75),  // Руда
    8250 => array(RES_METAL => 3.00, RES_CRYSTAL => 0.50, RES_DEUTERIUM => 0.50),  // Металл
    9250 => array(RES_METAL => 4.00, RES_CRYSTAL => 0.25, RES_DEUTERIUM => 0.25),  // Тяжмет
  );
  */
  $planet_density = sn_get_groups('planet_density');
  $density_min = reset($planet_density);
  $density_min = $density_min[UNIT_PLANET_DENSITY];
  $density_max = end($planet_density);
  $density_max = $density_max[UNIT_PLANET_DENSITY];
  $density = sn_rand_gauss_range($density_min, $density_max, true, 3);

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
//      $PlanetDesign       = array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20');
      $PlanetDesign       = array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10');
      $planet['temp_min'] = rand(0, 100);
      $planet['temp_max'] = $planet['temp_min'] + 40;
    } elseif ($Position == 4 || $Position == 5 || $Position == 6) {
      $PlanetType         = array('dschjungel');
      $PlanetClass        = array('planet');
//      $PlanetDesign       = array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20');
      $PlanetDesign       = array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10');
      $planet['temp_min'] = rand(-25, 75);
      $planet['temp_max'] = $planet['temp_min'] + 40;
    } elseif ($Position == 7 || $Position == 8 || $Position == 9) {
      $PlanetType         = array('normaltemp');
      $PlanetClass        = array('planet');
//      $PlanetDesign       = array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20');
      $PlanetDesign       = array('01', '02', '03', '04', '05', '06', '07');
      $planet['temp_min'] = rand(-50, 50);
      $planet['temp_max'] = $planet['temp_min'] + 40;
    } elseif ($Position == 10 || $Position == 11 || $Position == 12) {
      $PlanetType         = array('wasser');
      $PlanetClass        = array('planet');
//      $PlanetDesign       = array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20');
      $PlanetDesign       = array('01', '02', '03', '04', '05', '06', '07', '08', '09');
      $planet['temp_min'] = rand(-75, 25);
      $planet['temp_max'] = $planet['temp_min'] + 40;
    } elseif ($Position == 13 || $Position == 14 || $Position == 15) {
      $PlanetType         = array('eis');
      $PlanetClass        = array('planet');
//      $PlanetDesign       = array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20');
      $PlanetDesign       = array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10');
      $planet['temp_min'] = rand(-100, 10);
      $planet['temp_max'] = $planet['temp_min'] + 40;
    } else {
      $PlanetType         = array('dschjungel', 'gas', 'normaltemp', 'trocken', 'wasser', 'wuesten', 'eis');
      $PlanetClass        = array('planet');
      $PlanetDesign       = array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '00',);
      $planet['temp_min'] = rand(-140, 10);
      $planet['temp_max'] = $planet['temp_min'] + 40;
    }

    if($HomeWorld)
    {
      $planet['temp_min'] = 0;
      $planet['temp_max'] = $planet['temp_min'] + 40;
      $planet['density'] = 5500;
    }
    else
    {
      $planet['density'] = $density;
    }

    foreach($planet_density as $planet['density_index'] => $value)
    {
      if($planet['density'] < $value[UNIT_PLANET_DENSITY]) break;
    }

    $density_info_resources = &$planet_density[$planet['density_index']][UNIT_RESOURCES];

    $planet['metal_perhour']     = $config->metal_basic_income * $density_info_resources[RES_METAL];
    $planet['crystal_perhour']   = $config->crystal_basic_income * $density_info_resources[RES_CRYSTAL];
    $planet['deuterium_perhour'] = $config->deuterium_basic_income * $density_info_resources[RES_DEUTERIUM];

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
    $QryInsertPlanet .= "`galaxy` = '".            $planet['galaxy']            ."', ";
    $QryInsertPlanet .= "`system` = '".            $planet['system']            ."', ";
    $QryInsertPlanet .= "`planet` = '".            $planet['planet']            ."', ";
    $QryInsertPlanet .= "`last_update` = '".       $planet['last_update']       ."', ";
    $QryInsertPlanet .= "`planet_type` = '".       $planet['planet_type']       ."', ";
    $QryInsertPlanet .= "`image` = '".             $planet['image']             ."', ";
    $QryInsertPlanet .= "`diameter` = '".          $planet['diameter']          ."', ";
    $QryInsertPlanet .= "`density` = '".           $planet['density']           ."', ";
    $QryInsertPlanet .= "`density_index` = '".     $planet['density_index']     ."', ";
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
//pdump($QryInsertPlanet);die();
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

function uni_create_moon($pos_galaxy, $pos_system, $pos_planet, $user_id, $moon_chance = 0, $moon_name = '', $update_debris = true)
{
  global $lang, $time_now;

  $moon_name = '';
  $moon = doquery("SELECT `id` FROM `{{planets}}` WHERE `galaxy` = '{$pos_galaxy}' AND `system` = '{$pos_system}' AND `planet` = '{$pos_planet}' AND `planet_type` = " . PT_MOON . " LIMIT 1;", '', true);
  if(!$moon['id'])
  {
    $moon_planet = doquery("SELECT `id`, `temp_min`, `temp_max`, `name`, `debris_metal`, `debris_crystal` FROM `{{planets}}` WHERE `galaxy` = '{$pos_galaxy}' AND `system` = '{$pos_system}' AND `planet` = '{$pos_planet}' AND `planet_type` = 1 LIMIT 1;", '', true);

    if($moon_planet['id'])
    {
      $base_storage_size = BASE_STORAGE_SIZE;

      if(!$moon_chance)
      {
        $size = rand(1100, 8999);
      }
      elseif($moon_chance <= 100)
      {
        $size = rand($moon_chance * 100 + 1000, $moon_chance * 200 + 2999);
      }
      else
      {
        $size = $moon_chance;
      }

      $moon_chance = min(30, ceil($size / 1000));

      $temp_min  = $moon_planet['temp_min'] - rand(10, 45);
      $temp_max  = $temp_min + 40;

      $moon_name = $moon_name ? $moon_name : "{$moon_planet['name']} {$lang['sys_moon']}";
      $moon_name_safe = mysql_real_escape_string($moon_name);

      $field_max = ceil($size / 1000);

      doquery(
        "INSERT INTO `{{planets}}` SET
          `id_owner` = '{$user_id}', `name` = '{$moon_name_safe}', `last_update` = '{$time_now}',
          `galaxy` = '{$pos_galaxy}', `system` = '{$pos_system}', `planet` = '{$pos_planet}', `planet_type` = '3', `parent_planet` = '{$moon_planet['id']}',
          `image` = 'mond', `diameter` = '{$size}', `temp_min` = '{$temp_min}', `temp_max` = '{$temp_max}', `field_max` = '{$field_max}',
          `metal` = '0', `metal_perhour` = '0', `metal_max` = '{$base_storage_size}',
          `crystal` = '0', `crystal_perhour` = '0', `crystal_max` = '{$base_storage_size}',
          `deuterium` = '0', `deuterium_perhour` = '0', `deuterium_max` = '{$base_storage_size}', `density` = 2500, `density_index` = 2;"
      );

      if($update_debris)
      {
        $debris_spent = $moon_chance * 1000000;
        $metal_spent  = min($moon_planet['debris_metal'], $debris_spent * mt_rand(50, 75) / 100);
        $crystal_spent = min($moon_planet['debris_crystal'], $debris_spent - $metal_spent);
        $metal_spent = min($moon_planet['debris_metal'], $debris_spent - $crystal_spent); // Need if crystal less then their part
        doquery("UPDATE {{planets}} SET `debris_metal` = GREATEST(0, `debris_metal` - {$metal_spent}), `debris_crystal` = GREATEST(0, `debris_crystal` - {$crystal_spent}) WHERE `id` = {$moon_planet['id']} LIMIT 1;");
      }
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
function SortUserPlanets($user_row, $skip_planet_id = false, $field_list = '', $conditions = '')
{
  $field_list = $field_list != '*' ? "`id`, `name`, `image`, `galaxy`, `system`, `planet`, `planet_type`{$field_list}" : $field_list;
  $conditions .= $skip_planet_id ? " AND `id` <> {$skip_planet_id} " : '';

  $sort_orders = array(
    SORT_ID       => '`id`',
    SORT_LOCATION => '`galaxy`, `system`, `planet`, `planet_type`',
    SORT_NAME     => '`name`',
    SORT_SIZE     => '(`field_max` + `terraformer` * 5 + `mondbasis` * 3)',
  );
  $order_by = (isset($sort_orders[$user_row['planet_sort']]) ? $sort_orders[$user_row['planet_sort']] : $sort_orders[SORT_ID])
    . ($user_row['planet_sort_order'] == SORT_DESCENDING ? " DESC" : " ASC");

  // Compilating query
  $QryPlanets = "SELECT {$field_list} FROM {{planets}} WHERE `id_owner` = '{$user_row['id']}' {$conditions} ORDER BY {$order_by}";

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

function uni_render_planet_full($from, $prefix = '', $html_safe = true, $include_id = false)
{
  global $lang;

  if(!$from['id'])
  {
    $result = $lang['sys_planet_expedition'];
  }
  else
  {
    $from_planet_id = $include_id ? (
      'ID ' . ($from['id'] ? $from['id'] : ($from[$prefix . 'planet_id'] ? $from[$prefix . 'planet_id'] : 0)) . ' '
    ) : '';

    $from_planet_type = $from['planet_type'] ? $from['planet_type'] : ($from[$prefix . 'type'] ? $from[$prefix . 'type'] : 0);
    $from_planet_type = ($from_planet_type ? ' ' . $lang['sys_planet_type_sh'][$from_planet_type] : '');

    $result = $from_planet_id . uni_render_coordinates($from, $prefix) . $from_planet_type . ($from['name'] ? ' ' . $from['name'] : '');
    $result = $html_safe ? str_replace(' ', '&nbsp;', htmlentities($result, ENT_COMPAT, 'UTF-8')) : $result;
  }

  return $result;
}

function uni_render_coordinates_url($from, $prefix = '', $page = 'galaxy.php')
{
  return $page . (strpos($page, '?') === false ? '?' : '&') . "galaxy={$from[$prefix . 'galaxy']}&system={$from[$prefix . 'system']}&planet={$from[$prefix . 'planet']}";
}

function uni_render_coordinates_href($from, $prefix = '', $mode = 0, $fleet_type = '')
{
  return '<a href="' . uni_render_coordinates_url($from, $prefix, "galaxy.php?mode={$mode}") . '"' . ($fleet_type ? " {$fleet_type}" : '') . '>' . uni_render_coordinates($from, $prefix) . '</a>';
}

function uni_get_time_to_jump($moon_row)
{
  global $sn_data, $time_now;
  $jump_gate_level = $moon_row[$sn_data[STRUC_MOON_GATE]['name']];
  return $jump_gate_level ? max(0, $moon_row['last_jump_time'] + abs(60 * 60 / $jump_gate_level) - $time_now) : 0;
}

function uni_calculate_moon_chance($FleetDebris)
{
  $MoonChance = $FleetDebris / 1000000;
  return ($MoonChance < 1) ? 0 : ($MoonChance>30 ? 30 : $MoonChance);
}

function uni_coordinates_valid($coordinates, $prefix = '')
{
  global $config;

  array_walk($coordinates, 'intval');

  return
    isset($coordinates["{$prefix}galaxy"]) && $coordinates["{$prefix}galaxy"] > 0 && $coordinates["{$prefix}galaxy"] <= $config->game_maxGalaxy &&
    isset($coordinates["{$prefix}system"]) && $coordinates["{$prefix}system"] > 0 && $coordinates["{$prefix}system"] <= $config->game_maxSystem &&
    isset($coordinates["{$prefix}planet"]) && $coordinates["{$prefix}planet"] > 0 && $coordinates["{$prefix}planet"] <= $config->game_maxPlanet;
}

function uni_planet_teleport_check($user, $planetrow, $new_coordinates = null)
{
  global $lang, $time_now, $config;

  try
  {
    if($planetrow['planet_teleport_next'] && $planetrow['planet_teleport_next'] > $time_now)
    {
      throw new exception($lang['ov_teleport_err_cooldown'], ERR_ERROR);
    }

    if(mrc_get_level($user, false, RES_DARK_MATTER) < $config->planet_teleport_cost)
    {
      throw new exception($lang['ov_teleport_err_no_dark_matter'], ERR_ERROR);
    }

    // TODO: Replace quick-check with using gathered flying fleet data
    $incoming = doquery("SELECT COUNT(*) AS incoming FROM {{fleets}} WHERE 
      (fleet_start_galaxy = {$planetrow['galaxy']} and fleet_start_system = {$planetrow['system']} and fleet_start_planet = {$planetrow['planet']})
      or
      (fleet_end_galaxy = {$planetrow['galaxy']} and fleet_end_system = {$planetrow['system']} and fleet_end_planet = {$planetrow['planet']})", true);
    if($incoming['incoming'])
    {
      throw new exception($lang['ov_teleport_err_fleet'], ERR_ERROR);
    }

    $incoming = doquery("SELECT COUNT(*) AS incoming FROM {{iraks}} WHERE fleet_end_galaxy = {$planetrow['galaxy']} and fleet_end_system = {$planetrow['system']} and fleet_end_planet = {$planetrow['planet']}", true);
    if($incoming['incoming'])
    {
      throw new exception($lang['ov_teleport_err_fleet'], ERR_ERROR);
    }

    if(is_array($new_coordinates))
    {
      $incoming = doquery("SELECT COUNT(*) AS incoming FROM {{planets}} WHERE galaxy = {$new_coordinates['galaxy']} and system = {$new_coordinates['system']} and planet = {$new_coordinates['planet']}", true);
      if($incoming['incoming'])
      {
        throw new exception($lang['ov_teleport_err_destination_busy'], ERR_ERROR);
      }
    }

    $response = array(
      'result'  => ERR_NONE,
      'message' => '',
    );
  }
  catch(exception $e)
  {
    $response = array(
      'result'  => $e->getCode(),
      'message' => $e->getMessage(),
    );
  }

  return $response;
}
