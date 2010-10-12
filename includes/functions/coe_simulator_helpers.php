<?php

function eco_sym_encode_replay($combat, $type)
{
  global $reslist;

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

function eco_sym_decode_replay($str_data)
{
  global $reslist, $sn_groups;

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

function eco_sym_to_combat($arr_sym_data, $str_fleet_type)
{
  global $reslist, $sn_data, $sn_groups;
  $combat = array();

  foreach($arr_sym_data as $int_fleet_id => $arr_sym_fleet)
  {
    foreach($arr_sym_fleet as $int_unit_id => $int_unit_count)
    {
      if(!$int_unit_count)
      {
        continue;
      }

      if(in_array($int_unit_id, $sn_groups['tech']))
      {
        $combat[$int_fleet_id]['user'][$sn_data[$int_unit_id]['name']] = intval($int_unit_count);
      }
      elseif(in_array($int_unit_id, $sn_groups['resources_loot']))
      {
        $combat[$int_fleet_id]['resources'][$sn_data[$int_unit_id]['name']] = $int_unit_count;
      }
      elseif(in_array($int_unit_id, $sn_groups['combat']))
      {
        $combat[$int_fleet_id][$str_fleet_type][$int_unit_id] = $int_unit_count;
      }
    }
  }

  return $combat;
}

?>