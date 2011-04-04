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
function GetBuildingTime ($user, $planet, $Element, $for_building = BUILD_CREATE, $level = false)
{
  global $pricelist, $resource, $reslist, $config, $sn_data;

  $isDefense = in_array($Element, $reslist['defense']);
  $isFleet = in_array($Element, $reslist['fleet']);

  if($level === false)
  {
    $level = ($planet[$resource[$Element]]) ? $planet[$resource[$Element]] : $user[$resource[$Element]];
    $level = (($level) AND !($isDefense OR $isFleet)) ? $level : 1;
  }
  $time = ($pricelist[$Element]['metal'] + $pricelist[$Element]['crystal'] + $pricelist[$Element]['deuterium']) * pow($pricelist[$Element]['factor'], $level) / get_game_speed() / 2500;

  if (in_array($Element, $reslist['build']))
  {
    // Pour un batiment ...
    $time = $time * (1 / ($planet[$resource['14']] + 1)) * pow(0.5, $planet[$resource['15']]);
    $time = floor(mrc_modify_value($user, $planet, MRC_ARCHITECT, $time * 60 * 60));
  }
  elseif (in_array($Element, $reslist['tech']))
  {
    // Pour une recherche
    $intergal_lab = $user[$resource[TECH_RESEARCH]];
    if ( $intergal_lab < 1 )
    {
      $time = $time / (($planet[$resource['31']] + 1) * 2) * pow(0.5, $planet[$resource['35']]);
    }
    else
    {
      $lab_require = intval($sn_data[$Element]['require'][31]);
      $limite = $intergal_lab + 1;

      $inves = doquery("SELECT SUM(laboratory) AS laboratorio
        FROM
        (
          SELECT laboratory
            FROM {{planets}}
            WHERE id_owner='{$user['id']}' AND laboratory>={$lab_require}
            ORDER BY laboratory DESC
            LIMIT {$limite}
        ) AS subquery;", '', true);
//      $time = $time / (($inves['laboratorio'] + 1) * 2) * pow(0.5, $planet[$resource['35']]);

      $inves = doquery(
        "SELECT SUM(lab) AS laboratorio
          FROM
          (
            SELECT ({$sn_data[31]['name']} + 1) * 2 / pow(0.5, {$sn_data[35]['name']}) AS lab
              FROM {{planets}}
                WHERE id_owner='{$user['id']}' AND {$sn_data[31]['name']} >= {$lab_require}
                ORDER BY lab DESC
                LIMIT {$limite}
          ) AS subquery;", '', true);
      $time = $time / $inves['laboratorio'];
    }
    $time = floor(mrc_modify_value($user, $planet, MRC_ACADEMIC, $time * 60 * 60));
  }
  elseif ($isDefense)
  {
    // Pour les defenses ou la flotte 'tarif fixe' durée adaptée a u niveau nanite et usine robot
    $time = $time * (1 / ($planet[$resource['21']] + 1)) * pow(1 / 2, $planet[$resource['15']]);
    $time = floor(mrc_modify_value($user, $planet, MRC_FORTIFIER, $time * 60 * 60));
  }
  elseif ($isFleet)
  {
    $time = $time * (1 / ($planet[$resource['21']] + 1)) * pow(1 / 2, $planet[$resource['15']]);
    $time = floor(mrc_modify_value($user, $planet, MRC_CONSTRUCTOR, $time * 60 * 60));
  }

  if($for_building == BUILD_DESTROY)
  {
    $time = floor($time/2);
  }

  return $time ? $time : 1;
}

?>
