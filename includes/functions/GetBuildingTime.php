<?php

/**
 * GetBuildingTime
 *
 * @version 1.1 copyright (c) 2010 Gorlum for http://supernova.ws
   [~] With Intergalactic tech all labs now correctly joins research in order of their level
   [%] Fixed bug when low-level lab on planet with lesser PlanetID blocks Intergalactic Research Tech
   [%] Fixed bug when lab that normally can't paticipate in research join it anywhere with IRT
   [%] Fixed bug when every lab works with IRT doesn't counting level of IRT
 * @version 1.0
 * @copyright 2008 By Chlorel for XNova
 */

// Calcul du temps de construction d'un Element (Batiment / Recherche / Defense / Vaisseau )
// $user       -> Le Joueur lui meme
// $planet     -> La planete sur laquelle l'Element doit etre construit
// $Element    -> L'Element que l'on convoite
function GetBuildingTime ($user, $planet, $Element)
{
  global $pricelist, $resource, $reslist, $config, $sn_data;

  $isDefense = in_array($Element, $reslist['defense']);
  $isFleet = in_array($Element, $reslist['fleet']);

  $level = ($planet[$resource[$Element]]) ? $planet[$resource[$Element]] : $user[$resource[$Element]];

  $level = (($level) AND !($isDefense OR $isFleet)) ? $level : 1;

  $cost_metal   = floor($pricelist[$Element]['metal']   * pow($pricelist[$Element]['factor'], $level));
  $cost_crystal = floor($pricelist[$Element]['crystal'] * pow($pricelist[$Element]['factor'], $level));
  $cost_deuterium = floor($pricelist[$Element]['deuterium'] * pow($pricelist[$Element]['factor'], $level));
  $time = ($cost_metal + $cost_crystal + $cost_deuterium) / $config->game_speed;

  if (in_array($Element, $reslist['build']))
  {
    // Pour un batiment ...
    $time = $time * (1 / ($planet[$resource['14']] + 1)) * pow(0.5, $planet[$resource['15']]);
    $time = floor(($time * 60 * 60) * (1 - (($user['rpg_constructeur']) * 0.1)));
  }
  elseif (in_array($Element, $reslist['tech']))
  {
    // Pour une recherche
    $intergal_lab = $user[$resource[123]];
    if ( $intergal_lab < 1 )
    {
      $lablevel = $planet[$resource['31']];
    }
    else
    {
      $lab_require = intval($sn_data[$Element]['require'][31]);
      $limite = $intergal_lab + 1;
      $inves = doquery("SELECT SUM(laboratory) AS laboratorio FROM (SELECT laboratory FROM {{table}} WHERE id_owner='{$user['id']}' AND laboratory>={$lab_require} order by laboratory desc limit {$limite}) AS subquery;", 'planets', true);
      $lablevel = $inves['laboratorio'];
    }
    $time = $time / (($lablevel + 1) * 2) * pow(0.5, $planet[$resource['35']]);
    $time = floor(($time * 60 * 60) * (1 - (($user['rpg_scientifique']) * 0.1)));
  }
  elseif ($isDefense)
  {
    // Pour les defenses ou la flotte 'tarif fixe' durée adaptée a u niveau nanite et usine robot
    $time = $time * (1 / ($planet[$resource['21']] + 1)) * pow(1 / 2, $planet[$resource['15']]);
    $time = floor(($time * 60 * 60) * pow(0.5, $user['rpg_defenseur']));
  }
  elseif ($isFleet)
  {
    $time = $time * (1 / ($planet[$resource['21']] + 1)) * pow(1 / 2, $planet[$resource['15']]);
    $time = floor(($time * 60 * 60) * (1 - (($user['rpg_technocrate']) * 0.05)));
  }

  return $time ? $time : 1;
}
?>