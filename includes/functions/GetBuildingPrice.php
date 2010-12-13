<?php

/**
 * GetBuildingPrice.php
 *
 * 1.1 - copyright (c) 2010 by Gorlum for http://supernova.ws
 *     [*] Some optimizations done
 * @version 1.0
 * @copyright 2008 by Chlorel for XNova
 */

// Verifie si un element est achetable au moment demandé
// $CurrentUser   -> Le Joueur lui meme
// $CurrentPlanet -> La planete sur laquelle l'Element doit etre construit
// $Element       -> L'Element que l'on convoite
// $Incremental   -> true  par defaut pour un batiment ou une recherche
//                -> false pour une defense ou un vaisseau
// $ForDestroy    -> false par defaut pour une construction
//                -> true pour calculer la demi valeur du niveau en cas de destruction
//
// Reponse        -> un tableau avec les couts de construction (a ajouter ou retirer des ressources)
function GetBuildingPrice ($CurrentUser, $CurrentPlanet, $Element, $Incremental = true, $ForDestroy = false) {
  global $pricelist, $sn_data;

  $unit_factor = $sn_data[$Element]['factor'];
  $unit_name = $sn_data[$Element]['name'];

  if ($Incremental) {
    $level = ($CurrentPlanet[$unit_name]) ? $CurrentPlanet[$unit_name] : $CurrentUser[$unit_name];
    if($ForDestroy)
    {
      $level--;
    }
  }

  $cost = array(
    'metal' => 0,
    'crystal' => 0,
    'deuterium' => 0,
    'energy_max' => 0
  );

  $price_increase = pow($unit_factor, $level);
  foreach ($cost as $ResType => &$resCount)
  {
    $resCount = $pricelist[$Element][$ResType];
    if ($Incremental)
    {
      $resCount = $resCount * $price_increase;
      if ($ForDestroy)
      {
        $resCount = $cost[$ResType] / 2;
      }
    }

    $resCount = floor($resCount);
  }

  return $cost;
}

?>
