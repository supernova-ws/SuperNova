<?php
/**
* GetMaxConstructibleShips.php
*
* @version 1.0
* @copyright 2008 By Kejith for XNova
*/
function GetMaxConstructibleShips( $planet, $Element){
  global $pricelist, $resource;
  $creatable = array();
  $ressources = array(
    'metal' => $lang["Metal"],
    'crystal'=> $lang["Crystal"],
    'deuterium' => $lang["Deuterium"]
  );
  $i=0;

  foreach ($ressources as $ResType => $ResTitle){
    if ($pricelist[$Element][$ResType] != 0) {
      $cost = $pricelist[$Element][$ResType];
      $creatable[$i] = floor($planet[$ResType] / $cost);
      $i++;
    }
  }

  $format = number_format(min($creatable),0,",",".");
  $constrauctible = ($format != 0) ? "<b style=\"color:GreenYellow;\">".$format."</b>" : "<b style=\"color:red;\">".$format."</b>";

  return $constrauctible;
}
?>
