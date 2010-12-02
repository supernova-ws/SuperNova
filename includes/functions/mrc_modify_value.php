<?php

function mrc_modify_value($user, $planet = false, $mercenary_id, $value)
{
  global $sn_data;

  $mercenary = $sn_data[$mercenary_id];
  $mercenary_bonus = $mercenary['bonus'];
  $mercenary_level = $user[$mercenary['name']];

  switch($mercenary['bonus_type'])
  {
    case BONUS_PERCENT:
      $value *= 1 + $mercenary_level * $mercenary_bonus / 100;
    break;

    case BONUS_ADD:
      $value += $mercenary_level * $mercenary_bonus;
    break;

    case BONUS_ABILITY:
      $value = $mercenary_level ? 0 : $mercenary_level;
    break;

    default:
    break;
  }

  return $value;
}

?>