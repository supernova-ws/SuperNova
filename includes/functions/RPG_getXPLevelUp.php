<?php
function RPG_calcXPForLevelUp($XPLevel, $b1, $q){
  return floor($b1 * (pow($q, $XPLevel) - 1)/($q - 1));
}

function RPG_getMinerXP($minerXPLevel){
  $minerB1 = 50;
  $minerQ  = 1.03;

//  return floor($minerB1 * (pow($minerQ, $minerXPLevel) - 1)/($minerQ - 1));
//  $t = RPG_calcXPForLevelUp($minerXPLevel, $minerB1, $minerQ);
//  return $t;
  return RPG_calcXPForLevelUp($minerXPLevel, $minerB1, $minerQ);
}

function RPG_getRaidXP($raidXPLevel){
  $raidB1 = 10;
  $raidQ  = 1.03;

//  return floor($raidB1 * (pow($raidQ, $raidXPLevel) - 1)/($raidQ - 1));
//  $t = RPG_calcXPForLevelUp($raidXPLevel, $raidB1, $raidQ);
//  return $t;
  return RPG_calcXPForLevelUp($raidXPLevel, $raidB1, $raidQ);
}
?>