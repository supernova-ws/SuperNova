<?php

/**
 * GetMaxConstructibleElements.php
 *
 * @version 1.2
 * @copyright 2008 By Chlorel for XNova
 */
// Retourne un entier du nombre maximum d'elements constructible
// par rapport aux ressources disponibles
// $Element    -> L'element visÃ©
// $Ressources -> Un table contenant metal, crystal, deuterium, energy de la planete
//                sur laquelle on veut construire l'Element
function GetMaxConstructibleElements ($Element, &$Ressources) {
  global $pricelist;

  // On test les 4 Type de ressource pour voir si au moins on sait en construire 1
  if ($pricelist[$Element]['metal']) {
    $MaxElements = floor($Ressources["metal"] / $pricelist[$Element]['metal']);
  };

  if ($pricelist[$Element]['crystal']) {
    $Buildable = floor($Ressources["crystal"] / $pricelist[$Element]['crystal']);
  }
  if ((isset($Buildable) AND $MaxElements > $Buildable)OR(!isset($MaxElements))) {
    $MaxElements      = $Buildable;
  }

  if ($pricelist[$Element]['deuterium']) {
    $Buildable        = floor($Ressources["deuterium"] / $pricelist[$Element]['deuterium']);
  }
  if ((isset($Buildable) AND $MaxElements > $Buildable)OR(!isset($MaxElements))) {
    $MaxElements      = $Buildable;
  }

  if ($pricelist[$Element]['energy']) {
    $Buildable        = floor($Ressources["energy_max"] / $pricelist[$Element]['energy']);
    if ($Buildable < 1) {
      $MaxElements      = 0;
    }
  }

  return $MaxElements;
}
// Verion History
// - 1.0 Version initiale (creation)
// - 1.1 Correction bug ressources négatives ...
// - 1.2 Correction bug quand pas de métal
?>