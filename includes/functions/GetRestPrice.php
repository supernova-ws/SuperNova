<?php

/**
 * GetRestPrice.php
 *
 * @version 1.0
 * @copyright 2008 By Chlorel for XNova
 */

// Calcul du surplus de ressources disponible apres l'achat d'un Element (Batiment / Recherche / Defense / Vaisseau )
// $user       -> Le Joueur lui meme
// $planet     -> La planete sur laquelle l'Element doit etre construit
// $Element    -> L'Element que l'on convoite
// $userfactor -> true  pour un batiment ou une recherche
// -> false pour une defense ou un vaisseau
//
// Reponse : Chaine de caractère mise en forme prete a etre affichée
function GetRestPrice ($user, $planet, $Element, $userfactor = true) {
  global $pricelist, $resource, $lang;

  if ($userfactor) {
    $level = ($planet[$resource[$Element]]) ? $planet[$resource[$Element]] : $user[$resource[$Element]];
  }

  $array = array(
    'metal'      => $lang["sys_metal"],
    'crystal'    => $lang["sys_crystal"],
    'deuterium'  => $lang["sys_deuterium"],
    'energy_max' => $lang["sys_energy"]
    );

  $fleet_list = flt_get_fleets_to_planet($planet);

  $text  = "<br><font color=\"#7f7f7f\">{$lang['Rest_ress']}: ";
  $text1 = "";
  foreach ($array as $ResType => $ResTitle) {
    if ($pricelist[$Element][$ResType] != 0) {
      $text .= $ResTitle . ": ";
      if ($userfactor) {
        $cost = floor($pricelist[$Element][$ResType] * pow($pricelist[$Element]['factor'], $level));
      } else {
        $cost = floor($pricelist[$Element][$ResType]);
      }
      if ($cost > $planet[$ResType]) {
        $text .= "<b style=\"color: rgb(127, 95, 96);\">". pretty_number($planet[$ResType] - $cost) ."</b> ";
      } else {
        $text .= "<b style=\"color: rgb(95, 127, 108);\">". pretty_number($planet[$ResType] - $cost) ."</b> ";
      }

      if ($cost > $planet[$ResType] + $fleet_list['own'][$ResType]) {
        $text1 .= "{$ResTitle}: <b style=\"color: rgb(127, 95, 96);\">". pretty_number($planet[$ResType] + $fleet_list['own'][$ResType] - $cost) ."</b> ";
      } else {
        $text1 .= "{$ResTitle}: <b style=\"color: rgb(95, 127, 108);\">". pretty_number($planet[$ResType] + $fleet_list['own'][$ResType] - $cost) ."</b> ";
      }
    }
  }
  $text .= '</font>';

  if($fleet_list['own']['count'])
  {
    $text .= "<br><font color=\"#7f7f7f\">{$lang['Rest_ress_fleet']}: {$text1}</font>";
  }

  return $text;
}
?>