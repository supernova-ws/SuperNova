<?php

/**
 * GetElementPrice.php
 *
 * @version 1.0
 * @copyright 2008 By Chlorel for XNova
 */

// ----------------------------------------------------------------------------------------------------------------
// Calcul du prix d'un Element (Batiment / Recherche / Defense / Vaisseau )
// $user       -> Le Joueur lui meme
// $planet     -> La planete sur laquelle l'Element doit etre construit
// $Element    -> L'Element que l'on convoite
// $userfactor -> true  pour un batiment ou une recherche
// -> false pour une defense ou un vaisseau
//
// Reponse : Une chaine de caractère decrivant proprement le tarif pret a etre affichée

function GetElementPrice ($user, $planet, $Element, $userfactor = true) {
  global $pricelist, $resource, $lang, $reslist, $CombatCaps;

  if ($userfactor) {
    $level = ($planet[$resource[$Element]]) ? $planet[$resource[$Element]] : $user[$resource[$Element]];
  }

  $is_buyeable = true;
  $array = array(
    'metal'      => $lang["Metal"],
    'crystal'    => $lang["Crystal"],
    'deuterium'  => $lang["Deuterium"],
    'energy_max' => $lang["Energy"]
    );

  if(in_array($Element, $reslist['fleet']) || in_array($Element, $reslist['defense'])){
    $text .= $lang['sys_ship_armour'] . ': ' . pretty_number($CombatCaps[$Element]['armor'], true, false, 1000)  . '; ';
    $text .= $lang['sys_ship_shield'] . ': ' . pretty_number($CombatCaps[$Element]['shield'], true, false, 1000) . '; ';
    $text .= $lang['sys_ship_weapon'] . ': ' . pretty_number($CombatCaps[$Element]['attack'], true, false, 1000) ;
    $text .= '<br>';
  };

  $text .= $lang['Requires'] . ": ";
  foreach ($array as $ResType => $ResTitle) {
    if ($pricelist[$Element][$ResType] != 0) {
      $text .= $ResTitle . ": ";
      if ($userfactor) {
        $cost = floor($pricelist[$Element][$ResType] * pow($pricelist[$Element]['factor'], $level));
      } else {
        $cost = floor($pricelist[$Element][$ResType]);
      }
      if ($cost > $planet[$ResType]) {
        $text .= "<b style=\"color:red;\"> <t title=\"-" . pretty_number ($cost - $planet[$ResType]) . "\">";
        $text .= "<span class=\"noresources\">" . pretty_number($cost) . "</span></t></b> ";
        $is_buyeable = false; //style="cursor: pointer;"
      } else {
        $text .= "<b style=\"color:lime;\"> <span class=\"noresources\">" . pretty_number($cost) . "</span></b> ";
      }
    }
  }
  return $text;
}
?>