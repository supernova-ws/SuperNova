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
  global $pricelist, $resource;

  if ($Incremental) {
    $level = ($CurrentPlanet[$resource[$Element]]) ? $CurrentPlanet[$resource[$Element]] : $CurrentUser[$resource[$Element]];
    if($ForDestroy) $level--;
  }

  $cost = array('metal' => 0, 'crystal' => 0, 'deuterium' => 0, 'energy_max' => 0);
  foreach ($cost as $ResType => &$resCount) {
    $resCount = floor($pricelist[$Element][$ResType]);
    if ($Incremental)
      $resCount = floor($resCount * pow($pricelist[$Element]['factor'], $level));

    if ($ForDestroy == true)
      $resCount = floor($cost[$ResType] / 2);
  }

  return $cost;
}
?>