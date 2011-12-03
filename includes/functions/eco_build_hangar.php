<?php

function eco_hangar_is_building($que)
{
  return $que['in_que_abs'][21] ? true : false;
}

/**
 * @function GetMaxConstructibleElements
 *
 * @version 1.2
 * @copyright 2008 By Chlorel for XNova
 */
// Verion History
// - 1.0 Version initiale (creation)
// - 1.1 Correction bug ressources n�gatives ...
// - 1.2 Correction bug quand pas de m�tal
// Retourne un entier du nombre maximum d'elements constructible
// par rapport aux ressources disponibles
// $Element    -> L'element visé
// $Ressources -> Un table contenant metal, crystal, deuterium, energy de la planete
//                sur laquelle on veut construire l'Element
function GetMaxConstructibleElements ($Element, &$Ressources) {
  global $sn_data;

  // On test les 4 Type de ressource pour voir si au moins on sait en construire 1
  if ($sn_data[$Element]['metal']) {
    $MaxElements = floor($Ressources["metal"] / $sn_data[$Element]['metal']);
  };

  if ($sn_data[$Element]['crystal']) {
    $Buildable = floor($Ressources["crystal"] / $sn_data[$Element]['crystal']);
  }
  if ((isset($Buildable) AND $MaxElements > $Buildable)OR(!isset($MaxElements))) {
    $MaxElements      = $Buildable;
  }

  if ($sn_data[$Element]['deuterium']) {
    $Buildable        = floor($Ressources["deuterium"] / $sn_data[$Element]['deuterium']);
  }
  if ((isset($Buildable) AND $MaxElements > $Buildable)OR(!isset($MaxElements))) {
    $MaxElements      = $Buildable;
  }

  if ($sn_data[$Element]['energy']) {
    $Buildable        = floor($Ressources["energy_max"] / $sn_data[$Element]['energy']);
    if ($Buildable < 1) {
      $MaxElements      = 0;
    }
  }

  return $MaxElements;
}

/**
 * @function GetRestrictedConstructionNum
 *
 * @version 1.0
 * @copyright 2009 By Gorlum for http://ogame.triolan.com.ua
 */
function GetRestrictedConstructionNum($planet)
{
  global $sn_data;

  $limited = array(407 => 0, 408 =>0, 409 =>0, 502 => 0, 503 => 0);

  foreach($limited as $key => $value)
  {
    $limited[$key] += $planet[$sn_data[$key]['name']];
  }

  $BuildQueue = $planet['b_hangar_id'];
  if($BuildQueue)
  {
    $BuildArray = explode (";", $BuildQueue);
    foreach($BuildArray as $BuildArrayElement)
    {
      $building = explode (",", $BuildArrayElement);
      if(array_key_exists($building[0], $limited))
      {
        $limited[$building[0]] += $building[1];
      }
    }
  }

  return $limited;
}
// Verion History
// - 1.0 Initial Version

/**
 * DefensesBuildingPage.php
 *
 * @version 1.2s - Security checked for SQL-injection by Gorlum for http://supernova.ws
 * @version 1.2
 * @copyright 2008 By Chlorel for XNova
  * version 1.2 by F.E.A.R. aka PekopT, www.kodportal.ru, 2008
 * (adding  Del Fleet&Defense Queue)
// Version History
// - 1.0 Modularisation
// - 1.1 Correction mise en place d'une limite max d'elements constructibles par ligne
// - 1.2 Correction limitation bouclier meme si en queue de fabrication
//
 */

/**
 * FleetBuildingPage.php
 *
 * @version 1.2s - Security checked for SQL-injection by Gorlum for http://supernova.ws
 * @version 1.2
 * @copyright 2008 By Chlorel for XNova
 * version 1.2 by F.E.A.R. aka PekopT, www.kodportal.ru, 2008
// - 1.1 Correction mise en place d'une limite max d'elements constructibles par ligne
// - 1.0 Modularisation
//
 * (adding  Del Fleet&Defense Queue)
 */

// Page de Construction d'Elements de Flotte
// $planet -> Planete sur laquelle la construction est lancée
//                   Parametre passé par adresse, cela permet de mettre les valeurs a jours
//                   dans le programme appelant
// $user   -> Utilisateur qui a lancé la construction
//

function eco_build_hangar($que_type, $user, &$planet, $que)
{
  global $sn_data, $lang, $dpath, $debug, $time_now;

  $GET_action  = sys_get_param_str('action');
  $GET_mode    = sys_get_param_str('mode');
  $POST_fmenge = sys_get_param('fmenge');

  if(isset($GET_action))
  {
    switch($GET_action)
    {
      case 'trim':
        $ElementQueue = explode(';', $planet['b_hangar_id']);
        while(!empty($ElementQueue) && $ElementLine == '')
        {
          $ElementIndex = count($ElementQueue) - 1;
          $ElementLine = $ElementQueue[$ElementIndex];
          unset($ElementQueue[$ElementIndex]);
        }

        if($ElementLine)
        {
          $Element = explode(',', $ElementLine);

          $ResourcesToUpd[metal] += floor($sn_data[$Element[0]]['metal'] * $Element[1]);
          $ResourcesToUpd[crystal] += floor($sn_data[$Element[0]]['crystal'] * $Element[1]);
          $ResourcesToUpd[deuterium] += floor($sn_data[$Element[0]]['deuterium'] * $Element[1]);

          doquery(
            "UPDATE `{{planets}}` SET
              `metal` = metal + '{$ResourcesToUpd['metal']}', `crystal` = crystal + '{$ResourcesToUpd['crystal']}', `deuterium` = deuterium + '{$ResourcesToUpd['deuterium']}',".
              (empty($ElementQueue) ? '`b_hangar` = 0,' : '') . "`b_hangar_id` = '" . implode(';', $ElementQueue) . "' WHERE `id` = '{$planet['id']}' LIMIT 1;");
        }

        // PREVENT SUBMITS?
        header("location: {$_SERVER['PHP_SELF']}?mode={$GET_mode}");
        exit;

      break;

      case 'clear':
        $ElementQueue = explode(';', $planet['b_hangar_id']);
        foreach($ElementQueue as $ElementLine => $Element)
        {
          if($Element != '')
          {
            $Element = explode(',', $Element);

            $ResourcesToUpd[metal] += floor($sn_data[$Element[0]]['metal'] * $Element[1]);
            $ResourcesToUpd[crystal] += floor($sn_data[$Element[0]]['crystal'] * $Element[1]);
            $ResourcesToUpd[deuterium] += floor($sn_data[$Element[0]]['deuterium'] * $Element[1]);
          }
        }

        doquery(
          "UPDATE `{{planets}}` SET
            `metal` = metal + '{$ResourcesToUpd['metal']}', `crystal` = crystal + '{$ResourcesToUpd['crystal']}', `deuterium` = deuterium + '{$ResourcesToUpd['deuterium']}',
            `b_hangar` = '', `b_hangar_id` = '' WHERE `id` = '{$planet['id']}' LIMIT 1;");

        // PREVENT SUBMITS?
        header("location: {$_SERVER['PHP_SELF']}?mode={$GET_mode}");
        exit;

      break;
    }
  }

  $page_error = '';
  $page_mode = $que_type == SUBQUE_FLEET ? 'fleet' : 'defense';
  $sn_data_group = $sn_data['groups'][$page_mode];
  if (isset($POST_fmenge) && !eco_hangar_is_building ( $que ))
  {
    doquery('START TRANSACTION;');
    $planet = doquery("SELECT * FROM {{planets}} WHERE `id` = '{$planet['id']}' LIMIT 1 FOR UPDATE;", '', true);

    $units_cost = array();

    $hangar = $planet['b_hangar_id'];
    $built = GetRestrictedConstructionNum($planet);
    $SiloSpace = max(0, $planet[ $sn_data[44]['name'] ] * 10 - $built[502] - $built[503] * 2);

    foreach($POST_fmenge as $Element => $Count)
    {
      $Element = intval($Element);

      $Count   = min(max(0, intval($Count)), MAX_FLEET_OR_DEFS_PER_ROW);

      if (!(($Count) && ($Element) && in_array($Element, $sn_data_group) && eco_can_build_unit($user, $planet, $Element) == BUILD_ALLOWED))
      {
        continue;
      }

      // On verifie combien on sait faire de cet element au max
      $MaxElements = GetMaxConstructibleElements ( $Element, $planet );

      switch ($Element) {
        case 502:
          $Count = min($SiloSpace, $Count, $MaxElements);
          $SiloSpace -= $Count;
        break;

        case 503:
          $Count = min(floor($SiloSpace/2), $Count, $MaxElements);
          $SiloSpace -= $Count * 2;
        break;

        case 407:
        case 408:
        case 409:
          $Count = $built[$Element] >= 1 ? 0 : 1;
        break;

        default:
          $Count = min($Count, $MaxElements);
        break;
      };

      $unit_resources['metal'] = $sn_data[$Element]['metal'] * $Count;
      $unit_resources['crystal'] = $sn_data[$Element]['crystal'] * $Count;
      $unit_resources['deuterium'] = $sn_data[$Element]['deuterium'] * $Count;
      
      foreach($unit_resources as $res_name => $res_amount)
      {
        $units_cost[$res_name] += $res_amount;
      }

      $hangar .= "{$Element},{$Count};";
    }

    if ($hangar != $planet['b_hangar_id'])
    {
      $new_planet_data = $planet;

      $can_build_def = true;
      $query_string = '';
      foreach($units_cost as $res_name => $res_amount)
      {
        if($res_amount <= 0)
        {
          continue;
        }

        if($planet[$res_name] < $res_amount)
        {
          $can_build_def = false;
          $page_error = $lang['eco_bld_resources_not_enough'];
          break;
        }
        $new_planet_data[$res_name] -= $res_amount;
        $query_string .= "`{$res_name}` = `{$res_name}` - {$res_amount},";
      }

      if($can_build_def && $query_string)
      {
        $planet = $new_planet_data;
        $planet['b_hangar_id'] = $hangar;

        $query_string .= "`b_hangar_id` = '{$hangar}'";

        doquery("UPDATE {{planets}} SET {$query_string} WHERE `id` = '{$planet['id']}' LIMIT 1;");
      }
    }
    doquery('COMMIT');
  }

  // -------------------------------------------------------------------------------------------------------
  // S'il n'y a pas de Chantier ...
  if ($planet[$sn_data[21]['name']] == 0)
  {
    // Veuillez avoir l'obligeance de construire le Chantier Spacial !!
    message($lang['need_hangar'], $lang['tech'][21]);
  }

  $built = GetRestrictedConstructionNum($planet);
  $SiloSpace = max(0, $planet[$sn_data[44]['name'] ] * 10 - $built[502] - $built[503] * 2);

  $template = gettemplate("buildings_hangar", true);

  // -------------------------------------------------------------------------------------------------------
  // Construction de la page du Chantier (car si j'arrive ici ... c'est que j'ai tout ce qu'il faut pour ...
  $TabIndex  = 0;
  foreach($sn_data_group as $Element)
  {
    $unit_message = '';

    if(eco_can_build_unit($user, $planet, $Element) == BUILD_ALLOWED)
    {
      // On regarde si on peut en acheter au moins 1
      $build_data = eco_get_build_data($user, $planet, $Element);
      $CanBuildOne         = $build_data['CAN'][BUILD_CREATE];//IsElementBuyable($user, $planet, $Element, false);

      // Disponibilité actuelle
      $ElementCount        = $planet[$sn_data[$Element]['name']];

      // On affiche le temps de construction (c'est toujours tellement plus joli)
      $baubar= GetMaxConstructibleElements ( $Element, $planet );

      switch ($Element) {
        case 502:
          $baubar = min($SiloSpace, $baubar);
          $restrict = 1;
        break;

        case 503:
          $baubar = min(floor($SiloSpace/2), $baubar);
          $restrict = 1;
        break;

        case 407:
        case 408:
        case 409:
          $baubar = $built[$Element] >= 1 ? 0 : min(1, $baubar);
          $restrict = 2;
        break;

        default:
          $restrict = 0;
        break;
      }

      // Case nombre d'elements a construire
      if ($CanBuildOne) {
        if (!eco_hangar_is_building ( $que ))
        {
          if ($restrict == 2 && $baubar == 0) {
            $unit_message .= $lang['only_one'];
          } elseif ($restrict == 1 && !$baubar) {
            $unit_message .= $lang['b_no_silo_space'];
          } else {
            $TabIndex++;
          }
        }else {
          $unit_message = $lang['fleet_on_update'];
        }
      }

//      $build_data = eco_get_build_data($user, $planet, $Element, 0);

      $temp[RES_METAL]     = floor($planet['metal'] - $build_data[BUILD_CREATE][RES_METAL]); // + $fleet_list['own']['total'][RES_METAL]
      $temp[RES_CRYSTAL]   = floor($planet['crystal'] - $build_data[BUILD_CREATE][RES_CRYSTAL]); // + $fleet_list['own']['total'][RES_CRYSTAL]
      $temp[RES_DEUTERIUM] = floor($planet['deuterium'] - $build_data[BUILD_CREATE][RES_DEUTERIUM]); // + $fleet_list['own']['total'][RES_DEUTERIUM]

      $template->assign_block_vars('production', array(
        'ID'                => $Element,
        'NAME'              => $lang['tech'][$Element],
        'DESCRIPTION'       => $lang['info'][$Element]['description_short'],
        'LEVEL'             => $ElementCount,
        'LEVEL_OLD'         => $CurentPlanet[$sn_data[$Element]['name']],
        'LEVEL_CHANGE'      => $que['in_que'][$Element],

        'BUILD_CAN'         => min($baubar, $build_data['CAN'][BUILD_CREATE]),
        'TIME'              => pretty_time($build_data[BUILD_CREATE][RES_TIME]),
        'METAL'             => $build_data[BUILD_CREATE][RES_METAL],
        'CRYSTAL'           => $build_data[BUILD_CREATE][RES_CRYSTAL],
        'DEUTERIUM'         => $build_data[BUILD_CREATE][RES_DEUTERIUM],

        'METAL_PRINT'       => pretty_number($build_data[BUILD_CREATE][RES_METAL], true, $planet['metal']),
        'CRYSTAL_PRINT'     => pretty_number($build_data[BUILD_CREATE][RES_CRYSTAL], true, $planet['crystal']),
        'DEUTERIUM_PRINT'   => pretty_number($build_data[BUILD_CREATE][RES_DEUTERIUM], true, $planet['deuterium']),

        'DESTROY_CAN'       => $build_data['CAN'][BUILD_DESTROY],
        'DESTROY_TIME'      => pretty_time($build_data[BUILD_DESTROY][RES_TIME]),
        'DESTROY_METAL'     => $build_data[BUILD_DESTROY][RES_METAL],
        'DESTROY_CRYSTAL'   => $build_data[BUILD_DESTROY][RES_CRYSTAL],
        'DESTROY_DEUTERIUM' => $build_data[BUILD_DESTROY][RES_DEUTERIUM],

        'METAL_REST'        => pretty_number($temp[RES_METAL], true, true),
        'CRYSTAL_REST'      => pretty_number($temp[RES_CRYSTAL], true, true),
        'DEUTERIUM_REST'    => pretty_number($temp[RES_DEUTERIUM], true, true),
        'METAL_REST_NUM'    => $temp[RES_METAL],
        'CRYSTAL_REST_NUM'  => $temp[RES_CRYSTAL],
        'DEUTERIUM_REST_NUM'=> $temp[RES_DEUTERIUM],

        'ARMOR'  => pretty_number($sn_data[$Element]['armor']),
        'SHIELD' => pretty_number($sn_data[$Element]['shield']),
        'WEAPON' => pretty_number($sn_data[$Element]['attack']),

        'TABINDEX' => $TabIndex,

        'MESSAGE' => $unit_message,

//        'UNIT_BUSY'         => eco_unit_busy($user, $CurentPlanet, $que, $Element),
      ));
    }
  }

  $template->assign_vars(array(
    'noresearch' => $NoFleetMessage,
    'error_msg' => $page_error,
    'MODE' => $que_type,

    'QUE_ID' => $que_type,
    'TIME_NOW'           => $time_now,
  ));

  tpl_assign_hangar($que_type, $planet, $template);

  display(parsetemplate($template), $lang[$page_mode]);
}

function tpl_assign_hangar($que_type, $planet, &$template)
{
  global $user, $lang;

  $que_length = 0;
  $hangar_que_strings = explode(';', $planet['b_hangar_id']);
  foreach($hangar_que_strings as $hangar_que_string_id => $hangar_que_string)
  {
    if(!$hangar_que_string)
    {
      continue;
    }

    list($unit_id, $unit_amount) = explode(',', $hangar_que_string);

    $unit_data = eco_get_build_data($user, $planet, $unit_id, 0);

    $template->assign_block_vars('que', array(
      'ID' => $unit_id,
      'QUE' => $que_type,
      'NAME' => $lang['tech'][$unit_id],
      'TIME' => $unit_data[BUILD_CREATE][RES_TIME] - ($hangar_que_string_id ? 0 : $planet['b_hangar']),
      'TIME_FULL' => $unit_data[BUILD_CREATE][RES_TIME],
      'AMOUNT' => $unit_amount,
      'LEVEL' => 0,
    ));

    $que_length++;
  }

  return($que_length);
}

?>
