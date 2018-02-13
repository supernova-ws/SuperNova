<?php

function sn_ube_simulator_encode_replay($combat, $type) {
  $strPacked = "{$type}!";

  foreach ($combat as $fleetID => $fleetCompress) {
    foreach ($fleetCompress as $key => $value) {
      $value = intval($value);
      $strPacked .= "{$key},{$value};";
    }
    $strPacked .= '!';
  }

  return $strPacked;
}

function sn_ube_simulator_decode_replay($str_data) {
  $fleet_id = 0;

  $arr_data_unpacked = explode('!', $str_data);
  foreach ($arr_data_unpacked as $data_piece) {
    if (!$data_piece) {
      continue;
    }

    if ($data_piece == 'A' || $data_piece == 'D') {
      $fleet_type = $data_piece;
      continue;
    }

    $arr_unit_strings = explode(';', $data_piece);
    foreach ($arr_unit_strings as $str_unit_string) {
      if (!$str_unit_string) {
        continue;
      }

      $arr_unit_data = explode(',', $str_unit_string);
      if ($arr_unit_data[1]) {
        $unpacked[$fleet_type][$fleet_id][$arr_unit_data[0]] = intval($arr_unit_data[1]);
      }
    }

    $fleet_id++;
  }

  return $unpacked;
}

// ------------------------------------------------------------------------------------------------
// Преобразовывает данные симулятора в данные для расчета боя
function sn_ube_simulator_fill_side(&$combat_data, $side_info, $attacker, $player_id = -1) {
  /**
   * @var \Ube\Ube4_1\Ube4_1Calc $ubeCalc
   */
  $ubeCalc = $combat_data[UBE_OBJ_CALCULATOR];

  $player_id = $player_id == -1 ? count($combat_data[UBE_PLAYERS]) : $player_id;

  foreach ($side_info as $fleet_data) {
    $combat_data[UBE_PLAYERS][$player_id][UBE_NAME] = $attacker ? 'Attacker' : 'Defender';
    $combat_data[UBE_PLAYERS][$player_id][UBE_ATTACKER] = $attacker;

    $combat_data[UBE_FLEETS][$player_id][UBE_OWNER] = $player_id;
    foreach ($fleet_data as $unit_id => $unit_count) {
      if (!$unit_count) {
        continue;
      }

      $unit_type = get_unit_param($unit_id, P_UNIT_TYPE);

      if ($unit_type == UNIT_SHIPS || $unit_type == UNIT_DEFENCE) {
        $combat_data[UBE_FLEETS][$player_id][UBE_COUNT][$unit_id] = $unit_count;
      } elseif ($unit_type == UNIT_RESOURCES) {
        $combat_data[UBE_FLEETS][$player_id][UBE_RESOURCES][$unit_id] = $unit_count;
      } elseif ($unit_type == UNIT_TECHNOLOGIES) {
        $combat_data[UBE_PLAYERS][$player_id][UBE_BONUSES][$ubeCalc->ube_convert_techs[$unit_id]] += $unit_count * get_unit_param($unit_id, P_BONUS_VALUE) / 100;
      } elseif ($unit_type == UNIT_GOVERNORS) {
        if ($unit_id == MRC_FORTIFIER) {
          foreach ($ubeCalc->ube_convert_techs as $ube_id) {
            $combat_data[UBE_FLEETS][$player_id][UBE_BONUSES][$ube_id] += $unit_count * get_unit_param($unit_id, P_BONUS_VALUE) / 100;
          }
        }
      } elseif ($unit_type == UNIT_MERCENARIES) {
        if ($unit_id == MRC_ADMIRAL) {
          foreach ($ubeCalc->ube_convert_techs as $ube_id) {
            $combat_data[UBE_PLAYERS][$player_id][UBE_BONUSES][$ube_id] += $unit_count * get_unit_param($unit_id, P_BONUS_VALUE) / 100;
          }
        }
      }
    }
  }
}

function sn_ube_simulator_fleet_converter($sym_attacker, $sym_defender) {
  $combat_data = array(
    UBE_OPTIONS => array(
      UBE_SIMULATOR    => sys_get_param_int('simulator'),
      UBE_MISSION_TYPE => MT_ATTACK,
    ),

    UBE_PLAYERS => array(),

    UBE_FLEETS         => array(),

// TODO !!!!!!!!!!!    UBE_OBJ_PREPARATOR => $this,
    UBE_OBJ_CALCULATOR => new \Ube\Ube4_1\Ube4_1Calc(),
  );

  sn_ube_simulator_fill_side($combat_data, $sym_defender, false);
  sn_ube_simulator_fill_side($combat_data, $sym_attacker, true);

  return ($combat_data);
}
