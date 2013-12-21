<?php

/**
 * MissionCaseExpedition.php
 *
 * version 2.0 returns results for new fleet handler
 * @version 1.0
 * @copyright 2008 By Chlorel for XNova
 */

function flt_mission_explore($mission_data)
{
  global $lang, $sn_data, $config;

  static $ship_data, $rates;

  if(!$ship_data)
  {
    foreach(sn_get_groups('fleet') as $unit_id)
    {
      if(!$sn_data[$unit_id]['speed'])
      {
        continue;
      }
      $ship_data[$unit_id]['metal_cost'] = get_unit_cost_in($sn_data[$unit_id]['cost']);
    }
    // pdump($ship_data);
    // arsort($ship_data);
    $rates = get_resource_exchange();
  }

  $fleet_row = $mission_data['fleet'];
  $fleet = sys_unit_str2arr($fleet_row['fleet_array']);
  $fleet_capacity = 0;
  $fleet_metal_points = 0;
  foreach($fleet as $ship_id => $ship_amount)
  {
    $fleet_capacity += $ship_amount * $sn_data[$ship_id]['capacity'];
    $fleet_metal_points += $ship_amount * $ship_data[$ship_id]['metal_cost'];
  }
  $fleet_capacity = max(0, $fleet_capacity - $fleet_row['fleet_resource_metal'] + $fleet_row['fleet_resource_crystal'] + $fleet_row['fleet_resource_deuterium']);

  $flt_stay_hours = ($fleet_row['fleet_end_stay'] - $fleet_row['fleet_start_time']) / 3600 * ($config->game_speed_expedition ? $config->game_speed_expedition : 1);
  // pdump($flt_stay_hours);
  // pdump($config->game_speed_expedition);
  // pdump(log($flt_stay_hours, 2), $flt_stay_hours);

  $mission_outcome_list = array(
    FLT_EXPEDITION_OUTCOME_NONE => array(
      'chance' => floor(max(100, 200 - log($flt_stay_hours, 2) * 10)),
    ),
    FLT_EXPEDITION_OUTCOME_LOST_FLEET => array(
      'chance' => 9,
    ),
    FLT_EXPEDITION_OUTCOME_LOST_FLEET_ALL => array(
      'chance' => 3,
    ),
    FLT_EXPEDITION_OUTCOME_FOUND_FLEET => array(
      'chance' => 200,
      'percent' => array(
        0 => 0.1,
        1 => 0.02,
        2 => 0.01,
      ),
    ),
    FLT_EXPEDITION_OUTCOME_FOUND_RESOURCES => array(
      'chance' => 300,
      'percent' => array(
        0 => 0.1,
        1 => 0.050,
        2 => 0.025,
      ),
    ),
    FLT_EXPEDITION_OUTCOME_FOUND_DM => array(
      'chance' => 100,
      'percent' => array(
        0 => 0.0100,
        1 => 0.0040,
        2 => 0.0010,
      ),
    ),
    /*
    FLT_EXPEDITION_OUTCOME_FOUND_ARTIFACT => array(
      'chance' => 10,
    ),
    */
  );

  $chance_max = 0;
  foreach($mission_outcome_list as &$value)
  {
    $value['value'] = $chance_max = $value['chance'] + $chance_max;
  }
// pdump($mission_outcome_list);
  $outcome_value = mt_rand(0, $chance_max);
// $outcome_value = 409;
  foreach($mission_outcome_list as $mission_outcome => &$outcome_description)
  {
    if($outcome_value <= $outcome_description['value'])
    {
      break;
    }
  }

  // Вычисляем вероятность выпадения данного числа в общем пуле
  $outcome_percent = ($outcome_description['value'] - $outcome_value) / $outcome_description['chance'];

  $msg_sender = $lang['flt_mission_expedition']['msg_sender'];
  $msg_title = $lang['flt_mission_expedition']['msg_title'];

  $msg_text = '';
  $msg_text_addon = '';
  $found_dark_matter = 0;
  $outcome_mission_sub = -1;
  switch($mission_outcome)
  {
    case FLT_EXPEDITION_OUTCOME_LOST_FLEET:
      // $fleet_left = 1 - mt_rand(1, 3) * 0.25;// * 0.25;
      $fleet_left = 1 - mt_rand(1, 3) * mt_rand(200000, 300000) / 1000000;
      $fleet_lost = array();
      foreach($fleet as $unit_id => &$unit_amount)
      {
        $ships_left = floor($unit_amount * $fleet_left);
        $fleet_lost[$unit_id] = $unit_amount - $ships_left;
        $unit_amount = $ships_left;
        if(!$unit_amount)
        {
          unset($fleet[$unit_id]);
        }
      }
    break;

    case FLT_EXPEDITION_OUTCOME_LOST_FLEET_ALL:
      $fleet_lost = $fleet;
      $fleet = array();
    break;

    case FLT_EXPEDITION_OUTCOME_FOUND_FLEET:
      $outcome_mission_sub = $outcome_percent >= 0.99 ? 0 : ($outcome_percent >= 0.90 ? 1 : 2);
      $outcome_percent = $outcome_description['percent'][$outcome_mission_sub];
      // Рассчитываем эквивалент найденного флота в метале
      $found_in_metal = min($outcome_percent * $fleet_metal_points, $config->resource_multiplier * 10000000); // game_speed
      //  13 243 754 000 g x1
      //  60 762 247 000 a x10
      // 308 389 499 488 000 b x500

      // Рассчитываем стоимость самого дорого корабля в металле
      $max_metal_cost = 0;
      foreach($fleet as $ship_id => $ship_amount)
      {
        $max_metal_cost = max($max_metal_cost, $ship_data[$ship_id]['metal_cost']);
      }

      // Ограничиваем корабли только теми, чья стоимость в металле меньше или равно стоимости самого дорогого корабля
      $can_be_found = array();
      foreach($ship_data as $ship_id => $ship_info)
      {
        if($ship_info['metal_cost'] <= $max_metal_cost)
        {
          $can_be_found[$ship_id] = $ship_info['metal_cost'];
        }
      }
      // Убираем колонизаторы и шпионов - миллиарды шпионов и колонизаторов нам не нужны
      unset($can_be_found[SHIP_COLONIZER]);
      unset($can_be_found[SHIP_SPY]);

      $fleet_found = array();
      // $i = 0;
      //while($found_in_metal)
      while($found_in_metal >= min($can_be_found) && count($can_be_found))
      {
        //if($found_in_metal < min($can_be_found) || !count($can_be_found))
        //{
        //  break;
        //}

        $found_index = mt_rand(1, count($can_be_found)) - 1;
        $found_ship = array_slice($can_be_found, $found_index, 1, true);
        $found_ship_cost = reset($found_ship);
        $found_ship_id = key($found_ship);

        if($found_ship_cost > $found_in_metal)
        {
          unset($can_be_found[$found_ship_id]);
        }
        else
        {
          $found_ship_count = mt_rand(1, floor($found_in_metal / $found_ship_cost));
          $fleet_found[$found_ship_id] += $found_ship_count;
          $found_in_metal -= $found_ship_count * $found_ship_cost;
        }
      }

      if(empty($fleet_found))
      {
        $msg_text_addon = $lang['flt_mission_expedition']['outcomes'][$mission_outcome]['no_result'];
      }
      else
      {
        foreach($fleet_found as $unit_id => $unit_amount)
        {
          $fleet[$unit_id] += $unit_amount;
        }
      }
    break;

    case FLT_EXPEDITION_OUTCOME_FOUND_RESOURCES:
      $outcome_mission_sub = $outcome_percent >= 0.99 ? 0 : ($outcome_percent >= 0.90 ? 1 : 2);
      $outcome_percent = $outcome_description['percent'][$outcome_mission_sub];
      // Рассчитываем количество найденных ресурсов
      $found_in_metal = ceil(min($outcome_percent * $fleet_metal_points, $config->resource_multiplier * 10000000, $fleet_capacity) * mt_rand(950000, 1050000) / 1000000); // game_speed
      // pdump($found_in_metal, '$found_in_metal');

      $resources_found[RES_METAL] = floor(mt_rand(300000, 700000) / 1000000 * $found_in_metal);
      $found_in_metal -= $resources_found[RES_METAL];
      $found_in_metal = floor($found_in_metal * $rates[RES_METAL] / $rates[RES_CRYSTAL]);

      $resources_found[RES_CRYSTAL] = floor(mt_rand(500000, 1000000) / 1000000 * $found_in_metal);
      $found_in_metal -= $resources_found[RES_CRYSTAL];
      $found_in_metal = floor($found_in_metal * $rates[RES_CRYSTAL] / $rates[RES_DEUTERIUM]);

      $resources_found[RES_DEUTERIUM] = $found_in_metal;

      $fleet_row['fleet_resource_metal'] += $resources_found[RES_METAL];
      $fleet_row['fleet_resource_crystal'] += $resources_found[RES_CRYSTAL];
      $fleet_row['fleet_resource_deuterium'] += $resources_found[RES_DEUTERIUM];

      if(array_sum($resources_found) == 0)
      {
        $msg_text_addon = $lang['flt_mission_expedition']['outcomes'][$mission_outcome]['no_result'];
      }
    break;

    case FLT_EXPEDITION_OUTCOME_FOUND_DM:
      $outcome_mission_sub = $outcome_percent >= 0.99 ? 0 : ($outcome_percent >= 0.90 ? 1 : 2);
      $outcome_percent = $outcome_description['percent'][$outcome_mission_sub];
      // Рассчитываем количество найденной ТМ
      $found_dark_matter = floor(min($outcome_percent * $fleet_metal_points / $rates[RES_DARK_MATTER], 10000) * mt_rand(750000, 1000000) / 1000000);

      if(!$found_dark_matter)
      {
        $msg_text_addon = $lang['flt_mission_expedition']['outcomes'][$mission_outcome]['no_result'];
      }
    break;

    case FLT_EXPEDITION_OUTCOME_FOUND_ARTIFACT:
    break;

    default:
    break;
  }


  $query_data = array();
  if($found_dark_matter)
  {
    rpg_points_change($fleet_row['fleet_owner'], RPG_EXPEDITION, $found_dark_matter, 'Expedition Bonus');
    $msg_text_addon = sprintf($lang['flt_mission_expedition']['found_dark_matter'], $found_dark_matter);
  }

  if(!empty($fleet_lost))
  {
    $msg_text_addon = $lang['flt_mission_expedition']['lost_fleet'];
    foreach($fleet_lost as $ship_id => $ship_amount)
    {
      $msg_text_addon .= $lang['tech'][$ship_id] . ' - ' . $ship_amount . "\r\n";
    }
  }

  $fleet_row['fleet_amount'] = array_sum($fleet);
  if(!empty($fleet) && $fleet_row['fleet_amount'])
  {
    if(!empty($fleet_found))
    {
      $msg_text_addon = $lang['flt_mission_expedition']['found_fleet'];
      foreach($fleet_found as $ship_id => $ship_amount)
      {
        $msg_text_addon .= $lang['tech'][$ship_id] . ' - ' . $ship_amount . "\r\n";
      }
    }

    if(!empty($resources_found) && array_sum($resources_found) > 0)
    {
      $msg_text_addon = $lang['flt_mission_expedition']['found_resources'];
      foreach($resources_found as $ship_id => $ship_amount)
      {
        $msg_text_addon .= $lang['tech'][$ship_id] . ' - ' . $ship_amount . "\r\n";
      }

      $query_data[] = "`fleet_resource_metal` = `fleet_resource_metal` + {$resources_found[RES_METAL]}";
      $query_data[] = "`fleet_resource_crystal` = `fleet_resource_crystal` + {$resources_found[RES_CRYSTAL]}";
      $query_data[] = "`fleet_resource_deuterium` = `fleet_resource_deuterium` + {$resources_found[RES_DEUTERIUM]}";
    }

    if(!empty($fleet_lost) || !empty($fleet_found))
    {
      $fleet_row['fleet_array'] = sys_unit_arr2str($fleet);

      $query_data[] = "`fleet_amount` = {$fleet_row['fleet_amount']}";
      $query_data[] = "`fleet_array` = '{$fleet_row['fleet_array']}'";
    }

    $query_data[] = '`fleet_mess` = 1';

    $query_data = "UPDATE {{fleets}} SET " . implode(',', $query_data);
  }
  else
  {
    // Удалить флот
    $query_data = "DELETE FROM {{fleets}}";
  }
  $query_data .=  " WHERE `fleet_id` = {$fleet_row['fleet_id']} LIMIT 1";

  doquery($query_data);

  if(!$msg_text)
  {
    $messages = &$lang['flt_mission_expedition']['outcomes'][$mission_outcome]['messages'];
    if($outcome_mission_sub >= 0 && is_array($messages))
    {
      $messages = &$messages[$outcome_mission_sub];
    }

    $msg_text = is_string($messages) ? $messages :
      (is_array($messages) ? $messages[mt_rand(0, count($messages) - 1)] : '');
  }
  $msg_text = sprintf($msg_text, $fleet_row['fleet_id'], uni_render_coordinates($fleet_row, 'fleet_end_')) .
    ($msg_text_addon ? "\r\n" . $msg_text_addon: '');

  msg_send_simple_message($fleet_row['fleet_owner'], '', $fleet_row['fleet_end_stay'], MSG_TYPE_EXPLORE, $msg_sender, $msg_title, $msg_text);
  // pdump($msg_text, '$msg_text');

  // pdump($msg_text_addon, '$msg_text_addon');

  // pdump($fleet);
  // pdump($fleet_capacity);
  // pdump($fleet_metal_points);
  // die();
  // pdump($msg_text_addon);
  /*
  pdump($query_data);
  pdump(mysql_error(), 'error');
  pdump($fleet, '$fleet');
  pdump($fleet_row, '$fleet_row');
  pdump($fleet_found, '$fleet_found');
  die();
  */

  return CACHE_FLEET | CACHE_USER_SRC;

  $msg_sender = "{$lang['sys_mess_qg']} ({$outcome})";
  $msg_title  = $lang['sys_expe_report'];
  $lang['sys_expe_blackholl_2'];













  $ship_points = array(
    SHIP_CARGO_SMALL     => 1.0,
    SHIP_CARGO_BIG       => 1.5,
    SHIP_CARGO_SUPER     => 1.0,
    SHIP_CARGO_HYPER     => 1.0,
    SHIP_FIGHTER_LIGHT   => 0.5,
    SHIP_FIGHTER_HEAVY   => 1.5,
    SHIP_FIGHTER_ASSAULT => 3.0,
    SHIP_DESTROYER       => 2.0,
    SHIP_CRUISER         => 2.5,
    SHIP_COLONIZER       => 0.5,
    SHIP_RECYCLER        => 1.0,
    SHIP_SPY             => 0.0,
    SHIP_BOMBER          => 3.0,
    SHIP_SATTELITE_SOLAR => 0.0,
    SHIP_DESTRUCTOR      => 3.5,
    SHIP_DEATH_STAR      => 5.0,
    SHIP_BATTLESHIP      => 3.2,
    SHIP_SUPERNOVA       => 9.9,
  );

  // Table de ratio de gains en nombre par type de vaisseau
  $ship_gain_ratio = array(
    SHIP_CARGO_SMALL     => 0.1,
    SHIP_CARGO_BIG       => 0.05,
    SHIP_CARGO_SUPER     => 0.0125,
    SHIP_CARGO_HYPER     => 0.0025,
    SHIP_FIGHTER_LIGHT   => 0.1,
    SHIP_FIGHTER_HEAVY   => 0.05,
    SHIP_FIGHTER_ASSAULT => 0.0125,
    SHIP_DESTROYER       => 0.25,
    SHIP_CRUISER         => 0.125,
    SHIP_COLONIZER       => 0.05,
    SHIP_CARGO_SUPER     => 0.05,
    SHIP_RECYCLER        => 0.1,
    SHIP_SPY             => 0.1,
    SHIP_BOMBER          => 0.0625,
    SHIP_SATTELITE_SOLAR => 0.0,
    SHIP_DESTRUCTOR      => 0.0625,
    SHIP_DEATH_STAR      => 0.03125,
    SHIP_BATTLESHIP      => 0.0625,
    SHIP_SUPERNOVA       => 0.00125,
  );




  // Initialisation du contenu de la Flotte
  $farray = explode(';', $fleet_row['fleet_array']);
  foreach($farray as $Item => $Group) {
    if ($Group != '') {
      $Class = explode (',', $Group);
      $ship_type = $Class[0];
      $ship_amount = $Class[1];

      $LaFlotte[$ship_type] = $ship_amount;

      //On calcul les ressources maximum qui peuvent être récupéré
      $fleet_capacity += $sn_data[$ship_type]['capacity'];
      // Maintenant on calcul en points toute la flotte
      $FleetPoints   += ($ship_amount * $ship_points[$ship_type]);
    }
  }

  // Espace deja occupé dans les soutes si ce devait etre le cas
  $FleetUsedCapacity  = $fleet_row['fleet_resource_metal'] + $fleet_row['fleet_resource_crystal'] + $fleet_row['fleet_resource_deuterium'];
  $fleet_capacity    -= $FleetUsedCapacity;

  //On récupère le nombre total de vaisseaux
  $FleetCount = $fleet_row['fleet_amount'];

  // Bon on les mange comment ces explorateurs ???
  $outcome = mt_rand(0, 10);

  $msg_sender = "{$lang['sys_mess_qg']} ({$outcome})";

  if ($outcome < 3) {
    // Pas de bol, on les mange tout crus
    $outcome     += 1;
    $LostAmount  = (($outcome * 33) + 1) / 100;

    // Message pour annoncer la bonne mauvaise nouvelle
    if ($LostAmount == 100) {
      // Supprimer effectivement la flotte
      msg_send_simple_message ( $fleet_owner, '', $fleet_row['fleet_end_stay'], MSG_TYPE_EXPLORE, $msg_sender, $msg_title, $lang['sys_expe_blackholl_2'] );
      doquery ("DELETE FROM {{fleets}} WHERE `fleet_id` = {$fleet_row['fleet_id']}");
    } else {
      foreach ($LaFlotte as $Ship => $Count) {
        $LostShips[$Ship] = intval($Count * $LostAmount);
        $NewFleetArray   .= $Ship.','. ($Count - $LostShips[$Ship]) .';';
      }

      doquery("UPDATE {{fleets}} SET `fleet_array` = '{$NewFleetArray}', `fleet_mess` = '1' WHERE `fleet_id` = '{$fleet_row['fleet_id']}';");
      msg_send_simple_message ( $fleet_owner, '', $fleet_row['fleet_end_stay'], MSG_TYPE_EXPLORE, $msg_sender, $msg_title, $lang['sys_expe_blackholl_1'] );
    }

  } elseif ($outcome == 3) {
    // Ah un tour pour rien
    doquery("UPDATE {{fleets}} SET `fleet_mess` = '1' WHERE `fleet_id` = {$fleet_row['fleet_id']}");
    rpg_points_change($fleet_row['fleet_owner'], RPG_EXPEDITION, $config->rpg_flt_explore, 'Expedition Bonus');
    msg_send_simple_message ( $fleet_owner, '', $fleet_row['fleet_end_stay'], MSG_TYPE_EXPLORE, $msg_sender, $msg_title, $lang['sys_expe_nothing_1'] );
  } elseif ($outcome >= 4 && $outcome < 7) {
    // Gains de ressources
    if ($fleet_capacity > 5000) {
      $MinCapacity = $fleet_capacity - 5000;
      $MaxCapacity = $fleet_capacity;
      $FoundGoods  = rand($MinCapacity, $MaxCapacity);
      $FoundMetal  = intval($FoundGoods / 2);
      $FoundCrist  = intval($FoundGoods / 4);
      $FoundDeute  = intval($FoundGoods / 6);

      $QryUpdateFleet  = "UPDATE {{fleets}} SET ";
      $QryUpdateFleet .= "`fleet_resource_metal` = `fleet_resource_metal` + '{$FoundMetal}', ";
      $QryUpdateFleet .= "`fleet_resource_crystal` = `fleet_resource_crystal` + '{$FoundCrist}', ";
      $QryUpdateFleet .= "`fleet_resource_deuterium` = `fleet_resource_deuterium` + '{$FoundDeute}', ";
      $QryUpdateFleet .= "`fleet_mess` = '1'  ";
      $QryUpdateFleet .= "WHERE `fleet_id` = '{$fleet_row['fleet_id']}';";
      doquery( $QryUpdateFleet);
      $Message = sprintf($lang['sys_expe_found_goods'],
        pretty_number($FoundMetal), $lang['Metal'],
        pretty_number($FoundCrist), $lang['Crystal'],
        pretty_number($FoundDeute), $lang['Deuterium']);
      msg_send_simple_message ( $fleet_owner, '', $fleet_row['fleet_end_stay'], MSG_TYPE_EXPLORE, $msg_sender, $msg_title, $Message );
    }
  } elseif ($outcome == 7) {
    // Ah un tour pour rien
    doquery("UPDATE {{fleets}} SET `fleet_mess` = '1' WHERE `fleet_id` = {$fleet_row['fleet_id']}");
    msg_send_simple_message ( $fleet_owner, '', $fleet_row['fleet_end_stay'], MSG_TYPE_EXPLORE, $msg_sender, $msg_title, $lang['sys_expe_nothing_2'] );
  } elseif ($outcome >= 8 && $outcome < 11) {
    // Gain de vaisseaux
    $FoundChance = $FleetPoints / $FleetCount;
    foreach($sn_data['groups']['fleet'] as $Ship)
    {
      if ($LaFlotte[$Ship] != 0) {
        $FoundShip[$Ship] = round($LaFlotte[$Ship] * $ship_gain_ratio[$Ship]);
        if ($FoundShip[$Ship] > 0) {
          $LaFlotte[$Ship] += $FoundShip[$Ship];
        }
      }
    }
    $NewFleetArray = '';
    $FoundShipMess = '';
    foreach ($LaFlotte as $Ship => $Count) {
      if ($Count > 0) {
        $NewFleetArray   .= "{$Ship},{$Count};";
      }
    }

    if($FoundShip)
    {
      foreach ($FoundShip as $Ship => $Count)
      {
        if ($Count != 0)
        {
          $FoundShipMess   .= "{$Count} {$lang['tech'][$Ship]},";
        }
      }
    }

    doquery("UPDATE {{fleets}} SET `fleet_array` = '{$NewFleetArray}', `fleet_mess` = '1' WHERE `fleet_id` = '{$fleet_row['fleet_id']}' LIMIT 1;");
    $Message = "{$lang['sys_expe_found_ships']}{$FoundShipMess}";
    msg_send_simple_message ( $fleet_owner, '', $fleet_row['fleet_end_stay'], MSG_TYPE_EXPLORE, $msg_sender, $msg_title, $Message );
  }

  return CACHE_FLEET | CACHE_USER_SRC;
}
