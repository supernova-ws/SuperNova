<?php
function sys_fleetUnPack($strFleet){
  $arrTemp = explode(';', $strFleet);
  foreach($arrTemp as $temp){
    if($temp){
      $temp = explode(',', $temp);
      $arrFleet[$temp[0]] = $temp[1];
    }
  }

  return $arrFleet;
}

function sys_fleetPack($arrFleet){
  foreach($arrFleet as $shipID => $shipCount){
    $strFleet .= "$shipID,$shipCount;";
  }
  return $strFleet;
}
?>