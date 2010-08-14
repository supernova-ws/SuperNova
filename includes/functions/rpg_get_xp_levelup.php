<?php
function RPG_calcXPForLevelUp($XPLevel, $b1, $q){
  return floor($b1 * (pow($q, $XPLevel) - 1)/($q - 1));
}

function RPG_getMinerXP($minerXPLevel){
  $minerB1 = 50;
  $minerQ  = 1.03;

  return RPG_calcXPForLevelUp($minerXPLevel, $minerB1, $minerQ);
}

function RPG_getRaidXP($raidXPLevel){
  $raidB1 = 10;
  $raidQ  = 1.03;

  return RPG_calcXPForLevelUp($raidXPLevel, $raidB1, $raidQ);
}
?>