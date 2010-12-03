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
  global $pricelist, $resource, $lang, $sn_data;

  if ($userfactor) {
    $level = ($planet[$resource[$Element]]) ? $planet[$resource[$Element]] : $user[$resource[$Element]];
  }

  $array = array(
    RES_METAL        => $lang["sys_metal"],
    RES_CRYSTAL        => $lang["sys_crystal"],
    RES_DEUTERIUM        => $lang["sys_deuterium"],
    'energy_max' => $lang["sys_energy"]
    );

  $fleet_list = flt_get_fleets_to_planet($planet);
  $fleet_own_count = $fleet_list['own']['count'];

  $text  = "<br><font color=\"#7f7f7f\">{$lang['Rest_ress']}: ";
  $text1 = "";
  foreach ($array as $res_id => $ResTitle) {
    if(is_numeric($res_id))
    {
      $ResType = $sn_data[$res_id]['name'];
    }
    else
    {
      $ResType = $res_id;
    }

    if ($pricelist[$Element][$ResType] != 0) {
      $text .= "{$ResTitle}: ";
      if ($userfactor) {
        $cost = floor($pricelist[$Element][$ResType] * pow($pricelist[$Element]['factor'], $level));
      } else {
        $cost = floor($pricelist[$Element][$ResType]);
      }

      $resource_left = $planet[$ResType] - $cost;
      if ($resource_left < 0) {
        $color_rgb = '127, 95, 96';
      } else {
        $color_rgb = '95, 127, 108';
      }
      $resource_left = pretty_number($resource_left);
      $text .= "<b style=\"color: rgb({$color_rgb});\">{$resource_left}</b> ";

      if($fleet_own_count)
      {
        $resource_left = $planet[$ResType] + $fleet_list['own']['total'][$res_id] - $cost;
        if ($resource_left < 0) {
          $color_rgb = '127, 95, 96';
        } else {
          $color_rgb = '95, 127, 108';
        }
        $resource_left = pretty_number($resource_left);
        $text1 .= "{$ResTitle}: <b style=\"color: rgb({$color_rgb});\">{$resource_left}</b>, ";
      }
    }
  }
  $text .= '</font>';

  if($fleet_own_count)
  {
    $text1 = substr($text1, 0, -2);
    $text .= "<br><font color=\"#7f7f7f\">{$lang['Rest_ress_fleet']}: {$text1}</font>";
  }

  return $text;
}
?>