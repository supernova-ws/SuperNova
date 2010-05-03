<?php

/**
 * GetRestrictedConstructionNum.php
 *
 * @version 1.0
 * @copyright 2009 By Gorlum for http://ogame.triolan.com.ua
 */
function GetRestrictedConstructionNum($Planet) {
  global $resource;

  $limited = array(407 => 0, 408 =>0, 409 =>0, 502 => 0, 503 => 0);

  foreach($limited as $key => $value){
    $limited[$key] += $Planet[$resource[$key]];
  }

  $BuildQueue = $Planet['b_hangar_id'];
  if ($BuildQueue){
    $BuildArray = explode (";", $BuildQueue);
    foreach($BuildArray as $BuildArrayElement){
      $building = explode (",", $BuildArrayElement);
      if(array_key_exists($building[0], $limited)){
        $limited[$building[0]] += $building[1];
      }
    }
  }

  return $limited;
}
// Verion History
// - 1.0 Initial Version
?>