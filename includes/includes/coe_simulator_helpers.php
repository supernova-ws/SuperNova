<?php

function coe_sym_encode_replay($combat, $type)
{
  $strPacked = "{$type}!";

  foreach($combat as $fleetID => $fleetCompress)
  {
    foreach($fleetCompress as $key => $value)
    {
      $value = intval($value);
      $strPacked .= "{$key},{$value};";
    }
    $strPacked .= '!';
  }

  return $strPacked;
}

function coe_sym_decode_replay($str_data)
{
  global $sn_data;

  $fleet_id = 0;

  $arr_data_unpacked = explode('!', $str_data);
  foreach($arr_data_unpacked as $data_piece)
  {
    if(!$data_piece)
    {
      continue;
    }

    if($data_piece == 'A' || $data_piece == 'D')
    {
      $fleet_type = $data_piece;
      continue;
    }

    $arr_unit_strings = explode(';', $data_piece);
    foreach($arr_unit_strings as $str_unit_string)
    {
      if(!$str_unit_string)
      {
        continue;
      }

      $arr_unit_data = explode(',', $str_unit_string);
      if($arr_unit_data[1])
      {
        $unpacked[$fleet_type][$fleet_id][$arr_unit_data[0]] = intval($arr_unit_data[1]);
      }
    }

    $fleet_id++;
  }

  return $unpacked;
}

function coe_sym_to_combat($arr_sym_data, $str_fleet_type)
{
  global $sn_data;
  $combat = array();

  foreach($arr_sym_data as $int_fleet_id => $arr_sym_fleet)
  {
    foreach($arr_sym_fleet as $int_unit_id => $int_unit_count)
    {
      if(!$int_unit_count)
      {
        continue;
      }

      if(in_array($int_unit_id, $sn_data['groups']['tech']) || $int_unit_id == MRC_ADMIRAL)
      {
        $combat[$int_fleet_id]['user'][$sn_data[$int_unit_id]['name']] = intval($int_unit_count);
      }
      elseif(in_array($int_unit_id, $sn_data['groups']['resources_loot']))
      {
        $combat[$int_fleet_id]['resources'][$sn_data[$int_unit_id]['name']] = $int_unit_count;
      }
      elseif(in_array($int_unit_id, $sn_data['groups']['combat']))
      {
        $combat[$int_fleet_id][$str_fleet_type][$int_unit_id] = $int_unit_count;
      }
    }
  }

  return $combat;
}

function sn_ube_simulator_fleet_converter($sym_attacker, $sym_defender)
{
  global $sn_data;

  $combat_data = array(
    UBE_OPTIONS => array(
      UBE_SIMULATOR => true,
    ),
    UBE_PLAYERS => array(
/*
      0 => array(
        UBE_NAME => 'Defender',
        UBE_ATTACKER => false,
//        UBE_BONUSES => array(
//          UBE_ATTACK => 1,
//          UBE_ARMOR => 4,
//          UBE_SHIELD => 2,
//        ),
      ),
*/
/*
      1 => array(
        UBE_NAME => 'Attacker',
        UBE_ATTACKER => true,
//        UBE_BONUSES => array(
//          UBE_ATTACK => 5,
//          UBE_ARMOR => 6,
//          UBE_SHIELD => 7,
//        ),
      ),
*/
    ),

    UBE_FLEETS => array(
      // FLEET_ID => FLEET_OWNER,
/*
      0 => array(
        UBE_OWNER => 0,
        // Бонусы на флот - например, капитаны или Фортификатор
        UBE_BONUSES => array(
//          UBE_ATTACK => 2,
        ),
        UBE_COUNT => array(
          SHIP_SATTELITE_SOLAR => 200,
          UNIT_DEF_TURRET_GAUSS => 7,
          UNIT_DEF_TURRET_MISSILE => 700,
        ),
      ),

      1 => array(
        UBE_OWNER => 1,
        UBE_BONUSES => array(
        ),
        UBE_COUNT => array(
          SHIP_CARGO_SMALL => 500,
          SHIP_CARGO_SUPER => 520,
        ),
      ),
*/
    ),
  );

  $side_info = $sym_defender;
  $attacker = false;

  sn_ube_simulator_fill_side($combat_data, $sym_defender, false);
  sn_ube_simulator_fill_side($combat_data, $sym_attacker, true);


  // UBE_ATTACKER
//  sn_ube_combat_prepare_first_round($combat_data);

  return($combat_data);
}

function sn_ube_simulator_fill_side(&$combat_data, $side_info, $attacker)
{
  global $sn_data;

  $convert = array(
    TECH_WEAPON => UBE_ATTACK,
    TECH_ARMOR => UBE_ARMOR,
    TECH_SHIELD => UBE_SHIELD,
  );

  $id = count($combat_data[UBE_PLAYERS]);

  foreach($side_info as $fleet_data)
  {
    $combat_data[UBE_PLAYERS][$id][UBE_NAME] = $attacker ? 'Attacker' : 'Defender';
    $combat_data[UBE_PLAYERS][$id][UBE_ATTACKER] = $attacker;

    $combat_data[UBE_FLEETS][$id][UBE_OWNER] = $id;
    foreach($fleet_data as $unit_id => $unit_count)
    {
      if(!$unit_count)
      {
        continue;
      }
      if($sn_data[$unit_id]['type'] == UNIT_TECHNOLOGIES)
      {
        $combat_data[UBE_PLAYERS][$id][UBE_BONUSES][$convert[$unit_id]] = $unit_count;
      }
      elseif($sn_data[$unit_id]['type'] == UNIT_SHIPS || $sn_data[$unit_id]['type'] == UNIT_DEFENCE)
      {
        $combat_data[UBE_FLEETS][$id][UBE_COUNT][$unit_id] = $unit_count;
      }
      elseif($sn_data[$unit_id]['type'] == UNIT_MERCENARIES)
      {
        //TODO
      }
      elseif($sn_data[$unit_id]['type'] == UNIT_RESOURCES)
      {
        $combat_data[UBE_FLEETS][$id][UBE_RESOURCES][$unit_id] = $unit_count;
      }
    }
  }
}

?>
