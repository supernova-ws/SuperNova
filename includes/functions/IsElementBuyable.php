<?php

/**
 * IsElementBuyable.php
 *
 * 1.1 - copyright (c) 2010 by Gorlum for http://supernova.ws
 *     [*] Now using GetBuildingPrice proc to get building cost
 * @version 1
 * @copyright 2008 by Chlorel for XNova
 */

// Verifie si un element est achetable au moment demandé
// $CurrentUser   -> Le Joueur lui meme
// $CurrentPlanet -> La planete sur laquelle l'Element doit etre construit
// $Element       -> L'Element que l'on convoite
// $Incremental   -> true  pour un batiment ou une recherche
//                -> false pour une defense ou un vaisseau
// $ForDestroy    -> false par defaut pour une construction
//                -> true pour calculer la demi valeur du niveau en cas de destruction
//
// Reponse        -> boolean (oui / non)
function IsElementBuyable ($CurrentUser, $CurrentPlanet, $Element, $Incremental = true, $ForDestroy = false) {
  global $pricelist, $resource;

  if ($CurrentUser['urlaubs_modus'])
    return false;

  $array = GetBuildingPrice ($CurrentUser, $CurrentPlanet, $Element, $Incremental, $ForDestroy);
  foreach ($array as $ResType => $resorceNeeded)
    if ($resorceNeeded > $CurrentPlanet[$ResType])
      return false;

  return true;
}
?>