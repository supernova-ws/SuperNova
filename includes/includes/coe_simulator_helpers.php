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
