<?php

/**
 * BatimentBuildingPage.php
 *
 * @version 1.5 - Using PTE (not everywhere) by Gorlum for http://supernova.ws
 * @version 1.4 - Complying with PCG by Gorlum for http://supernova.ws
 * @version 1.3 - Security checked for SQL-injection by Gorlum for http://supernova.ws
// 1.0 Mise en module initiale (creation)
// 1.1 FIX interception cheat +1
// 1.2 FIX interception cheat destruction a -1
 * @version 1.1
 * @copyright 2008 by Chlorel for XNova
 */

function BatimentBuildingPage (&$CurrentPlanet, $CurrentUser)
{
  if($CurrentUser['compat_builds'])
  {
    $template = gettemplate('buildings_builds_old', true);
  }
  else
  {
    $template = gettemplate('buildings_builds', true);
  }

  global $sn_data, $sn_groups, $lang, $resource, $reslist, $phpEx, $dpath, $config;

  $config_resource_multiplier = $config->resource_multiplier;

  $GET_cmd      = SYS_mysqlSmartEscape($_GET['cmd']);
  $GET_building = intval($_GET['building']);
  $GET_listid       = $_GET['listid'];

  CheckPlanetUsedFields ( $CurrentPlanet );

  $planet_type_builds = $sn_groups['build_allow'][$CurrentPlanet['planet_type']];

  // Boucle d'interpretation des eventuelles commandes
  if (!empty($GET_cmd))
  {
    // On passe une commande
    $bDoItNow   = false;
    if ((!$GET_building) && (($GET_cmd == 'insert') || ($GET_cmd == 'destroy')))
    {
      $debug->error("Buguser: {$user['username']} ({$user['id']})<br />Free building", 'Bug use');
      die();
    };

    if ( !empty ( $GET_building ))
    {
      if ( !strchr ( $GET_building, ' ') )
      {
        if (in_array( trim($GET_building), $planet_type_builds))
        {
          $bDoItNow = true;
        }
      }
    }
    elseif ( isset ( $GET_listid ))
    {
      $bDoItNow = true;
    }

    if ($bDoItNow == true)
    {
      switch($GET_cmd)
      {
        case 'cancel':
          // Interrompre le premier batiment de la queue
          CancelBuildingFromQueue ( $CurrentPlanet, $CurrentUser );
        break;

        case 'remove':
          // Supprimer un element de la queue (mais pas le premier)
          // $RemID -> element de la liste a supprimer
          RemoveBuildingFromQueue ( $CurrentPlanet, $CurrentUser, $GET_listid );
        break;

        case 'insert':
          // Insere un element dans la queue
          AddBuildingToQueue ( $CurrentPlanet, $CurrentUser, $GET_building, true );
        break;

        case 'destroy':
          // Detruit un batiment deja construit sur la planete !
          AddBuildingToQueue ( $CurrentPlanet, $CurrentUser, $GET_building, false );
        break;

        default:
        break;
      } // switch
    }
  }

  SetNextQueueElementOnTop ( $CurrentPlanet, $CurrentUser );

  $Queue = ShowBuildingQueue ( $CurrentPlanet, $CurrentUser );

  // On enregistre ce que l'on a modifi� dans planet !
  doquery("UPDATE `{{planets}}` SET `b_building_id` = '{$CurrentPlanet['b_building_id']}', `b_building` = '{$CurrentPlanet['b_building']}' WHERE `id` = '{$CurrentPlanet['id']}' LIMIT 1;");
  // On enregistre ce que l'on a eventuellement modifi� dans users
  doquery("UPDATE `{{users}}` SET `xpminier` = '{$CurrentUser['xpminier']}' WHERE `id` = '{$CurrentUser['id']}' LIMIT 1;");
  rpg_level_up($user, RPG_STRUCTURE);

  if ($Queue['lenght'] < MAX_BUILDING_QUEUE_SIZE)
  {
    $CanBuildElement = true;
  }
  else
  {
    $CanBuildElement = false;
  }

  if($CurrentPlanet['b_building_id'])
  {
    $now_building = explode(';', $CurrentPlanet['b_building_id']);
    $now_building = explode(',', $now_building[0]);
    $now_working  = $now_building[0];
    if($now_building[4] == destroy)
    {
      $now_building = 0;
    }
    else
    {
      $now_building = 1;
    }
  }
  else
  {
    $now_working = false;
  }

  $fleet_list = flt_get_fleets_to_planet($CurrentPlanet);

  $SubTemplate         = gettemplate('buildings_builds_row');
  $BuildingPage        = '';
  $caps = ECO_getPlanetCaps($CurrentUser, &$CurrentPlanet);
  foreach($sn_groups['build'] as $Element)
  {
    if (in_array($Element, $planet_type_builds))
    {
      $ElementName = $lang['tech'][$Element];
      $CurrentMaxFields      = eco_planet_fields_max($CurrentPlanet);
      if ($CurrentPlanet['field_current'] < ($CurrentMaxFields - $Queue['lenght']))
      {
        $RoomIsOk = true;
      }
      else
      {
        $RoomIsOk = false;
      }

      if (IsTechnologieAccessible($CurrentUser, $CurrentPlanet, $Element))
      {
        $element_sn_data = $sn_data[$Element];
        $element_deuterium_perhour = $element_sn_data['deuterium_perhour'];
        $element_energy_perhour    = $element_sn_data['energy_perhour'];

        $HaveRessources        = IsElementBuyable ($CurrentUser, $CurrentPlanet, $Element, true, false);
        $parse                 = array();
        $BuildingLevel         = $CurrentPlanet[$resource[$Element]];

        // show energy on BuildingPage
        //================================
        $BuildLevelFactor     = 10; //$CurrentPlanet[$resource[$Element].'_porcent'];
        $BuildTemp            = $CurrentPlanet['temp_max'];
        $CurrentBuildtLvl     = $BuildingLevel;
        $BuildLevel           = ($CurrentBuildtLvl > 0) ? $CurrentBuildtLvl : 1;

        $Prod[3] = mrc_modify_value($CurrentUser, $CurrentPlanet, MRC_GEOLOGIST, eval($element_deuterium_perhour) * $config_resource_multiplier);
        $Prod[4] = mrc_modify_value($CurrentUser, $CurrentPlanet, MRC_POWERMAN , eval($element_energy_perhour) * $config_resource_multiplier);

        if ($Element != 12)
        {
            $ActualNeed     = floor($Prod[4]);
        }
        else
        {
            $ActualNeed     = floor($Prod[3]);
        }

        $BuildLevel++;

        $Prod[3] = mrc_modify_value($CurrentUser, $CurrentPlanet, MRC_GEOLOGIST, eval($element_deuterium_perhour) * $config_resource_multiplier);
        $Prod[4] = mrc_modify_value($CurrentUser, $CurrentPlanet, MRC_POWERMAN , eval($element_energy_perhour) * $config_resource_multiplier);

        if ($Element != 12)
        {
            $EnergyNeed = floor($Prod[4] - $ActualNeed);
        }
        else
        {
            $EnergyNeed = floor($Prod[4] - $ActualNeed);
        }

        if ($Element >= 1 && $Element <= 3)
        {
          $parse['build_need_diff'] = "<font color=#FF0000>{$EnergyNeed} {$lang['Energy']}</font>";
          $BuildLevel = 0;
        }
        elseif ($Element == 4 || $Element == 12)
        {
          $parse['build_need_diff'] = "<font color=#00FF00>+{$EnergyNeed} {$lang['Energy']}</font>";
          $BuildLevel = 0;
        }

        //================================
        $ElementBuildTime      = GetBuildingTime($CurrentUser, $CurrentPlanet, $Element);
        $parse['click']        = '';
        $NextBuildLevel        = $CurrentPlanet[$resource[$Element]] + 1;

        if ($Element == 31 || $Element == 35)
        {
          // Sp�cial Laboratoire
          if ($CurrentUser['b_tech_planet'] != 0 && !$config->BuildLabWhileRun)
          {
            $parse['click'] = "<font color=#FF0000>{$lang['in_working']}</font>";
          }
        }

        if ($Element == 21)
        {
          if ($CurrentPlanet['b_hangar_id'] != 0)
          {
            $parse['click'] = "<font color=#FF0000>{$lang['in_working']}</font>";
          }
        }

        $can_build_unit = false;
        if ($parse['click'] != '')
        {
          // Bin on ne fait rien, vu que l'on l'a deja fait au dessus !!
        }
        elseif ($RoomIsOk && $CanBuildElement)
        {
          if ($Queue['lenght'] == 0)
          {
            if ($NextBuildLevel == 1)
            {
              if ( $HaveRessources == true )
              {
                $parse['click'] = "<a href=\"?cmd=insert&building={$Element}\"><font color=#00FF00>{$lang['BuildFirstLevel']}</font></a>";
                $can_build_unit = true;
              }
              else
              {
                $parse['click'] = "<font color=#FF0000>{$lang['BuildFirstLevel']}</font>";
              }
            }
            else
            {
              if ( $HaveRessources == true )
              {
                $parse['click'] = "<a href=\"?cmd=insert&building={$Element}\"><font color=#00FF00>{$lang['BuildNextLevel']} {$NextBuildLevel}</font></a>";
                $can_build_unit = true;
              } else {
                $parse['click'] = "<font color=#FF0000>{$lang['BuildNextLevel']} {$NextBuildLevel}</font>";
              }
            }
          }
          else
          {
            if ( $HaveRessources == true )
            {
              $parse['click'] = "<a href=\"?cmd=insert&building={$Element}\"><font color=#00FF00>{$lang['InBuildQueue']}</font></a>";
              $can_build_unit = true;
            }
            else
            {
              $parse['click'] = "<font color=#ff0000>{$lang['InBuildQueue']}</font>";
            }
          }
        }
        elseif ($RoomIsOk && !$CanBuildElement)
        {
          if ($NextBuildLevel == 1)
          {
            $parse['click'] = "<font color=#FF0000>{$lang['BuildFirstLevel']}</font>";
          }
          else
          {
            $parse['click'] = "<font color=#FF0000>{$lang['BuildNextLevel']} {$NextBuildLevel}</font>";
          }
        }
        else
        {
          $parse['click'] = "<font color=#FF0000>{$lang['NoMoreSpace']}</font>";
        }

        $build_price = GetBuildingPrice ($CurrentUser, $CurrentPlanet, $Element, true);
        $destroy_price = GetBuildingPrice ($CurrentUser, $CurrentPlanet, $Element, true, true);
        $template->assign_block_vars('production', array(
          'ID'                => $Element,
          'NAME'              => $ElementName,
          'DESCRIPTION'       => $lang['info'][$Element]['description_short'],
          'LEVEL'             => ($BuildingLevel == 0) ? '' : "{$BuildingLevel}",

          'TIME'              => pretty_time($ElementBuildTime),
          'DESTROY_TIME'      => pretty_time(GetBuildingTime  ($CurrentUser, $CurrentPlanet, $Element) / 2),

          'PRICE'             => GetElementPrice($CurrentUser, $CurrentPlanet, $Element),
          'RESOURCES_LEFT'    => GetRestPrice($CurrentUser, $CurrentPlanet, $Element),

          'METAL'             => $build_price['metal'],
          'CRYSTAL'           => $build_price['crystal'],
          'DEUTERIUM'         => $build_price['deuterium'],
          'DESTROY_METAL'     => $destroy_price['metal'],
          'DESTROY_CRYSTAL'   => $destroy_price['crystal'],
          'DESTROY_DEUTERIUM' => $destroy_price['deuterium'],

          'METAL_REST'        => pretty_number($CurrentPlanet['metal']     + $fleet_list['own']['total'][RES_METAL] - $build_price['metal'], false, true),
          'CRYSTAL_REST'      => pretty_number($CurrentPlanet['crystal']   + $fleet_list['own']['total'][RES_CRYSTAL] - $build_price['crystal'], false, true),
          'DEUTERIUM_REST'    => pretty_number($CurrentPlanet['deuterium'] + $fleet_list['own']['total'][RES_DEUTERIUM] - $build_price['deuterium'], false, true),
          'METAL_REST_NUM'    => $CurrentPlanet['metal']     + $fleet_list['own']['total'][RES_METAL] - $build_price['metal'],
          'CRYSTAL_REST_NUM'  => $CurrentPlanet['crystal']   + $fleet_list['own']['total'][RES_CRYSTAL] - $build_price['crystal'],
          'DEUTERIUM_REST_NUM'=> $CurrentPlanet['deuterium'] + $fleet_list['own']['total'][RES_DEUTERIUM] - $build_price['deuterium'],

          'METAL_BALANCE'     => $caps['metal_perhour'][$Element],
          'CRYSTAL_BALANCE'   => $caps['crystal_perhour'][$Element],
          'DEUTERIUM_BALANCE' => $caps['deuterium_perhour'][$Element],
          'ENERGY_BALANCE'    => $EnergyNeed,

          'BUILD_LINK'        => $parse['click'],
          'CAN_BUILD'         => $can_build_unit,
        ));
      }
    }
  }

  if ($Queue['lenght'] > 0)
  {
    $parse['BuildListScript']  = InsertBuildListScript ('buildings');
    $parse['BuildList']        = $Queue['buildlist'];
  }
  else
  {
    $parse['BuildListScript']  = '';
    $parse['BuildList']        = '';
  }

  $template->assign_vars(array(
    'planet_field_current' => $CurrentPlanet['field_current'],
    'planet_field_max'     => $CurrentPlanet['field_max'] + ($CurrentPlanet[$resource[33]] * 5),
    'field_libre'          => $CurrentPlanet['field_max'] + ($CurrentPlanet[$resource[33]] * 5) - $CurrentPlanet['field_current'],
    'NOW_WORKING'          => $now_working,
    'NOW_BUILDING'         => $now_building,
    'PAGE_HINT'            => $lang['eco_bld_page_hint'],

    'METAL'                => $CurrentPlanet['metal'],
    'CRYSTAL'              => $CurrentPlanet['crystal'],
    'DEUTERIUM'            => $CurrentPlanet['deuterium'],

    'METAL_INCOMING'       => $fleet_list['own']['total'][RES_METAL],
    'CRYSTAL_INCOMING'     => $fleet_list['own']['total'][RES_CRYSTAL],
    'DEUTERIUM_INCOMING'   => $fleet_list['own']['total'][RES_DEUTERIUM],

    'FLEET_OWN'            => $fleet_list['own']['count'],
  ));
  display(parsetemplate($template, $parse), $lang['Builds']);
}

/**
 *
 * AddBuildingToQueue.php
 *
 * @version 1
 * @copyright 2008 by Chlorel for XNova
 */

// Ajoute un batiment dans la queue
// $CurrentPlanet -> Planete sur laquelle on construit
// $CurrentUser   -> Joueur courrant
// $Element       -> Batiment a construire
//
// Retour         -> Valeur de l'element inseré
//                   ou false s'il ne peut pas l'inserer (queue pleine)
//
function AddBuildingToQueue ( &$CurrentPlanet, $CurrentUser, $Element, $AddMode = true) {
  global $lang, $resource;

  $CurrentQueue  = $CurrentPlanet['b_building_id'];
  if ($CurrentQueue != 0) {
    $QueueArray    = explode ( ";", $CurrentQueue );
    $ActualCount   = count ( $QueueArray );
  } else {
    $QueueArray    = "";
    $ActualCount   = 0;
  }

  if ($AddMode == true) {
    $BuildMode = 'build';
  } else {
    $BuildMode = 'destroy';
  }

  if ( $ActualCount < MAX_BUILDING_QUEUE_SIZE ) {
    $QueueID      = $ActualCount + 1;
  } else {
    $QueueID      = false;
  }
  if ($AddMode == true) {
    if ($CurrentPlanet['field_current'] < ($CurrentPlanet['field_max'] + ($CurrentPlanet[$resource[33]] * 5))) {
      //$CanBuildElement = true;
    } else {
      $QueueID = false;
      echo "1. You're Hacker. Your ip logged...";
    }
  }
  if (IsTechnologieAccessible($CurrentUser, $CurrentPlanet, $Element) == true) {
     $CanBuildElement = true;
  } else {
     $QueueID = false;
     echo "2. You're Hacker. Your ip logged...";
  }
  if ( $QueueID != false ) {
    // Faut verifier si l'Element que l'on veut integrer est deja dans le tableau !
    if ($QueueID > 1) {

      $InArray = 0;
      for ( $QueueElement = 0; $QueueElement < $ActualCount; $QueueElement++ ) {
        $QueueSubArray = explode ( ",", $QueueArray[$QueueElement] );
        if ($QueueSubArray[0] == $Element) {
          $InArray++;
        }
      }

    } else {
      $InArray = 0;
    }

    if ($InArray != 0) {
      $ActualLevel  = $CurrentPlanet[$resource[$Element]];
      if ($AddMode == true) {
        $BuildLevel   = $ActualLevel + 1 + $InArray;
        $CurrentPlanet[$resource[$Element]] += $InArray;
        $BuildTime    = GetBuildingTime($CurrentUser, $CurrentPlanet, $Element);
        $CurrentPlanet[$resource[$Element]] -= $InArray;
      } else {
        $BuildLevel   = $ActualLevel - 1 + $InArray;
        $CurrentPlanet[$resource[$Element]] -= $InArray;
        $BuildTime    = GetBuildingTime($CurrentUser, $CurrentPlanet, $Element) / 2;
        $CurrentPlanet[$resource[$Element]] += $InArray;
      }
    } else {
      $ActualLevel  = $CurrentPlanet[$resource[$Element]];
      if ($AddMode == true) {
        $BuildLevel   = $ActualLevel + 1;
        $BuildTime    = GetBuildingTime($CurrentUser, $CurrentPlanet, $Element);
      } else {
        $BuildLevel   = $ActualLevel - 1;
        $BuildTime    = GetBuildingTime($CurrentUser, $CurrentPlanet, $Element) / 2;
      }
    }

    if ($QueueID == 1) {
      $BuildEndTime = time() + $BuildTime;
    } else {
      $PrevBuild = explode (",", $QueueArray[$ActualCount - 1]);
      $BuildEndTime = $PrevBuild[3] + $BuildTime;
    }
    $QueueArray[$ActualCount]       = $Element .",". $BuildLevel .",". $BuildTime .",". $BuildEndTime .",". $BuildMode;
    $NewQueue                       = implode ( ";", $QueueArray );
    $CurrentPlanet['b_building_id'] = $NewQueue;
  }
  return $QueueID;
}

/**
 * CancelBuildingFromQueue
 *
 * @version 1
 * @copyright 2008 by Chlorel for XNova
 */

function CancelBuildingFromQueue ( &$CurrentPlanet, &$CurrentUser ) {

  $CurrentQueue  = $CurrentPlanet['b_building_id'];
  if ($CurrentQueue != 0) {
    // Creation du tableau de la liste de construction
    $QueueArray          = explode ( ";", $CurrentQueue );
    // Comptage du nombre d'elements dans la liste
    $ActualCount         = count ( $QueueArray );

    // Stockage de l'element a 'interrompre'
    $CanceledIDArray     = explode ( ",", $QueueArray[0] );
    $Element             = $CanceledIDArray[0];
    $BuildMode           = $CanceledIDArray[4]; // pour savoir si on construit ou detruit

    if ($ActualCount > 1) {
      array_shift( $QueueArray );
      $NewCount        = count( $QueueArray );
      // Mise a jour de l'heure de fin de construction theorique du batiment
      $BuildEndTime        = time();
      for ($ID = 0; $ID < $NewCount ; $ID++ ) {
        $ListIDArray          = explode ( ",", $QueueArray[$ID] );
        $BuildEndTime        += $ListIDArray[2];
        $ListIDArray[3]       = $BuildEndTime;
        $QueueArray[$ID]      = implode ( ",", $ListIDArray );
      }
      $NewQueue        = implode(";", $QueueArray );
      $ReturnValue     = true;
      $BuildEndTime    = '0';
    } else {
      $NewQueue        = '0';
      $ReturnValue     = false;
      $BuildEndTime    = '0';
    }

    // Ici on va rembourser les ressources engagées ...
    // Deja le mode (car quand on detruit ca ne coute que la moitié du prix de construction classique
    if ($BuildMode == 'destroy') {
      $ForDestroy = true;
    } else {
      $ForDestroy = false;
    }

    if ( $Element != false ) {
      $Needed                        = GetBuildingPrice ($CurrentUser, $CurrentPlanet, $Element, true, $ForDestroy);
      $CurrentPlanet['metal']       += $Needed['metal'];
      $CurrentPlanet['crystal']     += $Needed['crystal'];
      $CurrentPlanet['deuterium']   += $Needed['deuterium'];
    }

  } else {
    $NewQueue          = '0';
    $BuildEndTime      = '0';
    $ReturnValue       = false;
  }

  $CurrentPlanet['b_building_id']  = $NewQueue;
  $CurrentPlanet['b_building']     = $BuildEndTime;

  return $ReturnValue;
}

/**
 * RemoveBuildingFromQueue.php
 *
 * @version 1
 * @copyright 2008 by Chlorel for XNova
 */

function RemoveBuildingFromQueue ( &$CurrentPlanet, $CurrentUser, $QueueID ) {

  if ($QueueID > 1) {
    $CurrentQueue  = $CurrentPlanet['b_building_id'];
    if ($CurrentQueue != 0) {
      $QueueArray    = explode ( ";", $CurrentQueue );
      $ActualCount   = count ( $QueueArray );
      $ListIDArray   = explode ( ",", $QueueArray[$QueueID - 2] );
      $BuildEndTime  = $ListIDArray[3];
      for ($ID = $QueueID; $ID < $ActualCount; $ID++ ) {
        $ListIDArray          = explode ( ",", $QueueArray[$ID] );
        $BuildEndTime        += $ListIDArray[2];
        $ListIDArray[3]       = $BuildEndTime;
        $QueueArray[$ID - 1]  = implode ( ",", $ListIDArray );
      }
      unset ($QueueArray[$ActualCount - 1]);
      $NewQueue     = implode ( ";", $QueueArray );
    }
    $CurrentPlanet['b_building_id'] = $NewQueue;
  }

  return $QueueID;

}

/**
 * ShowBuildingQueue.php
 *
 * @version 1
 * @copyright 2008 by Chlorel for XNova
 */

// ----------------------------------------------------------------------------------------------------------
// Construit le code html pour afficher une liste de construction en cours ...
// Données en entree :
// $CurrentPlanet -> Planete sur la quelle on affiche la page de construction de batiments
// $CurrentUser   -> Joueur courrant (inutilisé pour le moment ... Mais sait on jamais)
// Données en sortie :
// $ListIDRow     -> lignes d'une table de 3 colonnes integrable au dessus (ou au dessous) de la page de
//                   construction des batiments
// Necessite :
// Attention il faut avoir integré une fois au moins le script de controle en java ...
// Donc lancer : InsertBuildListScript () avant la balise <table> de la page
//
function ShowBuildingQueue ( $CurrentPlanet, $CurrentUser ) {
  global $lang;

  $CurrentQueue  = $CurrentPlanet['b_building_id'];
  $QueueID       = 0;
  if ($CurrentQueue != 0) {
    // Queue de fabrication documentée ... Y a au moins 1 element a construire !
    $QueueArray    = explode ( ";", $CurrentQueue );
    // Compte le nombre d'elements
    $ActualCount   = count ( $QueueArray );
  } else {
    // Queue de fabrication vide
    $QueueArray    = "0";
    $ActualCount   = 0;
  }

  $ListIDRow    = "";
  if ($ActualCount != 0) {
    $PlanetID     = $CurrentPlanet['id'];
    for ($QueueID = 0; $QueueID < $ActualCount; $QueueID++) {
      // Chaque element de la liste de fabrication est un tableau de 5 données
      // [0] -> Le batiment
      // [1] -> Le niveau du batiment
      // [2] -> La durée de construction
      // [3] -> L'heure théorique de fin de construction
      // [4] -> type d'action
      $BuildArray   = explode (",", $QueueArray[$QueueID]);
      $BuildEndTime = floor($BuildArray[3]);
      $CurrentTime  = floor(time());
      if ($BuildEndTime >= $CurrentTime) {
        $ListID       = $QueueID + 1;
        $Element      = $BuildArray[0];
        $BuildLevel   = $BuildArray[1];
        $BuildMode    = $BuildArray[4];
        $BuildTime    = $BuildEndTime - time();
        $ElementTitle = $lang['tech'][$Element];

        if ($ListID > 0) {
          $ListIDRow .= "<tr>";
          if ($BuildMode == 'build') {
            $ListIDRow .= " <td class=\"l\" colspan=\"2\">". $ListID .".: ". $ElementTitle ." ". $BuildLevel ."</td>";
          } else {
            $ListIDRow .= " <td class=\"l\" colspan=\"2\">". $ListID .".: ". $ElementTitle ." ". $BuildLevel ." ". $lang['destroy'] ."</td>";
          }
          $ListIDRow .= " <td class=\"k\">";
          if ($ListID == 1) {
            $ListIDRow .= InsertCounterLaunchScript($BuildTime, $PlanetID);
            $ListIDRow .= "   <strong color=\"lime\"><br><font color=\"lime\">". date(FMT_DATE_TIME ,$BuildEndTime) ."</font></strong>";
          } else {
            $ListIDRow .= "   <font color=\"red\">";
            $ListIDRow .= "   <a href=\"buildings.php?listid=". $ListID ."&amp;cmd=remove&amp;planet=". $PlanetID ."\">". $lang['DelFromQueue'] ."</a></font>";
          }
          $ListIDRow .= " </td>";
          $ListIDRow .= "</tr>";
        }
      }
    }
  }

  $RetValue['lenght']    = $ActualCount;
  $RetValue['buildlist'] = $ListIDRow;

  return $RetValue;
}

?>
