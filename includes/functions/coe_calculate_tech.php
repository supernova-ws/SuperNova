<?php
/**
 * BE_calculateTech.php
 * Battle Engine Effective Tech levels calculations
 * "rf" stands for "Rapid Fire"
 * "rp" stands for "ResourcePoints"
*/

function BE_calculateTechs(&$user){
  $defTech    = (1 + (0.1 * ($user['defence_tech'])  + (0.05 * $user['rpg_amiral'])));
  $shieldTech = (1 + (0.1 * ($user['shield_tech'])   + (0.05 * $user['rpg_amiral'])));
  $attTech    = (1 + (0.1 * ($user['military_tech']) + (0.05 * $user['rpg_amiral'])));

  return array('def' => $defTech, 'shield' => $shieldTech, 'att' => $attTech);
}
?>