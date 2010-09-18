<?php

function ECO_calcResourceIncrease(&$Caps, $strResource, $ProductionTime){
  $Caps['planet'][$strResource] = max(0, $Caps['planet'][$strResource]);
  $resourceIncrease = ($Caps[$strResource.'_perhour'][0] + $Caps['planet'][$strResource.'_perhour'] * $Caps['production']) * $ProductionTime / 3600 ;

  if ( ($Caps['planet'][$strResource] + $resourceIncrease) > $Caps['planet'][$strResource.'_max'] ) {
    // $resourceIncrease = $Caps['planet'][$strResource.'_max'] - $Caps['planet'][$strResource]; // Drop resource above storage limit
    $resourceIncrease = max(0, $Caps['planet'][$strResource.'_max'] - $Caps['planet'][$strResource]); // Don't drop resource above storage limit
  };

  return $resourceIncrease;
}

?>