<?php

// TODO: This functions is deprecated and should be replaced!

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
  global $sn_data;

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
    $resCount = $sn_data[$Element][$ResType];
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
  global $config, $sn_data;

  $isDefense = in_array($Element, $sn_data['groups']['defense']);
  $isFleet = in_array($Element, $sn_data['groups']['fleet']);

  if($level === false)
  {
    $unit_db_name = $sn_data[$Element]['name'];
    $level = ($planet[$unit_db_name]) ? $planet[$unit_db_name] : $user[$unit_db_name];
    $level = (($level) AND !($isDefense OR $isFleet)) ? $level : 1;
  }
  $time = ($sn_data[$Element]['metal'] + $sn_data[$Element]['crystal'] + $sn_data[$Element]['deuterium']) * pow($sn_data[$Element]['factor'], $level) / get_game_speed() / 2500;

  if (in_array($Element, $sn_data['groups']['structures']))
  {
    // Pour un batiment ...
    $time = $time * (1 / ($planet[$sn_data['14']['name']] + 1)) * pow(0.5, $planet[$sn_data['15']['name']]);
    $time = floor(mrc_modify_value($user, $planet, MRC_ARCHITECT, $time * 60 * 60));
  }
  elseif (in_array($Element, $sn_data['groups']['tech']))
  {
    // Pour une recherche
    $intergal_lab = $user[$sn_data[TECH_RESEARCH]['name']];
    if ( $intergal_lab < 1 )
    {
      $time = $time / (($planet[$sn_data['31']['name']] + 1) * 2) * pow(0.5, $planet[$sn_data['35']['name']]);
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
//      $time = $time / (($inves['laboratorio'] + 1) * 2) * pow(0.5, $planet[$sn_data[35]['name']]);

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
    $time = $time * (1 / ($planet[$sn_data[21]['name']] + 1)) * pow(1 / 2, $planet[$sn_data[15]['name']]);
    $time = floor(mrc_modify_value($user, $planet, MRC_FORTIFIER, $time * 60 * 60));
  }
  elseif ($isFleet)
  {
    $time = $time * (1 / ($planet[$sn_data[21]['name']] + 1)) * pow(1 / 2, $planet[$sn_data[15]['name']]);
    $time = floor(mrc_modify_value($user, $planet, MRC_CONSTRUCTOR, $time * 60 * 60));
  }

  if($for_building == BUILD_DESTROY)
  {
    $time = floor($time/2);
  }

  return $time ? $time : 1;
}

function int_buildCounter($planetrow, $type, $subType = '', $que = false)
{
  global $lang, $user, $time_now;

  if ( $planetrow["b_{$type}_id"] )
  {
    $BuildQueue = explode (';', $planetrow["b_{$type}_id"]);

    $start_prod = $time_now;
    if($type=='hangar'){
      $start_prod = $time_now - $planetrow["b_{$type}"];
    }

    $Build = "<script type='text/javascript'>sn_timers.unshift({id: 'ov_{$type}{$subType}', type: 0, active: true, start_time: {$start_prod}, options: { msg_done: '{$lang['Free']}', que: [";
    foreach($BuildQueue as $queItem){
      $CurrBuild  = explode (',', $queItem);
      if($type=='hangar'){
        $RestTime   = GetBuildingTime( $user, $planetrow, $CurrBuild[0] );
        $buildCount = $CurrBuild[1];
      }else{
        $RestTime   = $planetrow["b_{$type}"] - time();
        $buildCount = 1;
      }
      if($type=='building')
        $b1 .= ' (' . ($CurrBuild[1]) .')';

      $Build.= "['{$CurrBuild[0]}', '{$lang['tech'][$CurrBuild[0]]}{$b1}', {$RestTime}, '{$buildCount}'],";
    }
    $Build.= "]}});</script>";
  }
  elseif ($que)
  {
    $que_item = $que['que'][QUE_STRUCTURES][0];
    if(!empty($que_item))
    {
      $start_prod = $time_now - $que_item['TIME'];

      $Build = "<script type='text/javascript'>sn_timers.unshift({id: 'ov_{$type}{$subType}', type: 0, active: true, start_time: {$time_now}, options: { msg_done: '{$lang['Free']}', que: [";
      $RestTime   = $que_item['TIME'];
      $buildCount = $que_item['AMOUNT'];

      $Build.= "['{$que_item['ID']}', '{$lang['tech'][$que_item['ID']]} ({$que_item['LEVEL']})', {$RestTime}, '{$buildCount}'],";
      $Build.= "]}});</script>";
    }
  }

  return $Build;
}

/**
 * MessageForm.php
 *
 * @version 1
 * @copyright 2008 By Chlorel for XNova
 */

// Parametres en entrée:
// $Title    -> Titre du Message
// $Message  -> Texte contenu dans le message
// $Goto     -> Adresse de saut pour le formulaire
// $Button   -> Bouton de validation du formulaire
// $TwoLines -> Sur une ou sur 2 lignes
//
// Retour
//           -> Une chaine formatée affichable en html
function MessageForm ($Title, $Message, $Goto = '', $Button = ' ok ', $TwoLines = false) {
  $Form  = "<center>";
  $Form .= "<form action=\"". $Goto ."\" method=\"post\">";
  $Form .= "<table width=\"519\">";
  $Form .= "<tr>";
  $Form .= "<td class=\"c\" colspan=\"2\">". $Title ."</td>";
  $Form .= "</tr><tr>";

  if($Button){
    $Button = "<input type=\"submit\" value=\"{$Button}\">";
  }

  if ($TwoLines == true) {
    $Form .= "<th colspan=\"2\">". $Message ."</th>";
    $Form .= "</tr><tr>";
    if($Button)
      $Form .= "<th colspan=\"2\" align=\"center\">{$Button}</th>";
  } else {
    $Form .= "<th colspan=\"2\">". $Message . $Button . "</th>";
  }
  $Form .= "</tr>";
  $Form .= "</table>";
  $Form .= "</form>";
  $Form .= "</center>";

  return $Form;
}

// Release History
// - 1.0 Mise en fonction, Documentation

function GetElementRessources($Element, $Count)
{
  global $sn_data;

  $ResType['metal'] = ($sn_data[$Element]['metal'] * $Count);
  $ResType['crystal'] = ($sn_data[$Element]['crystal'] * $Count);
  $ResType['deuterium'] = ($sn_data[$Element]['deuterium'] * $Count);

  return $ResType;
}

?>
