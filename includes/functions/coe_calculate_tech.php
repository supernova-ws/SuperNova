<?php
/**
 * BE_calculateTech.php
 * Battle Engine Effective Tech levels calculations
 * "rf" stands for "Rapid Fire"
 * "rp" stands for "ResourcePoints"
*/

function BE_calculateTechs(&$user)
{
  global $sn_data;

  $armor_tech  = mrc_modify_value($user, MRC_ADMIRAL, 1 + 0.1 * $user['defence_tech']);
  $shield_tech = mrc_modify_value($user, MRC_ADMIRAL, 1 + 0.1 * $user['shield_tech']);
  $weapon_tech = mrc_modify_value($user, MRC_ADMIRAL, 1 + 0.1 * $user['military_tech']);

  return array('def' => $armor_tech, 'shield' => $shield_tech, 'att' => $weapon_tech);
}

?>