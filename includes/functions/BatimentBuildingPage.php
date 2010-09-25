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
  $template = gettemplate('buildings_builds', true);

  global $sn_data, $lang, $resource, $reslist, $phpEx, $dpath, $_GET, $config;

  $GET_cmd      = SYS_mysqlSmartEscape($_GET['cmd']);
  $GET_building = intval($_GET['building']);
  // $Element      = intval($_GET['building']);
  $GET_listid       = $_GET['listid'];

  CheckPlanetUsedFields ( $CurrentPlanet );

  // Tables des batiments possibles par type de planete
  $Allowed['1'] = array(  1,  2,  3,  4, 12, 14, 15, 21, 22, 23, 24, 31, 33, 34, 35, 44);
  $Allowed['3'] = array( 12, 14, 21, 22, 23, 24, 34, 41, 42, 43);

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
        if (in_array( trim($GET_building), $Allowed[$CurrentPlanet['planet_type']]))
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

  // On enregistre ce que l'on a modifié dans planet !
  BuildingSavePlanetRecord ( $CurrentPlanet );
  // On enregistre ce que l'on a eventuellement modifié dans users
  BuildingSaveUserRecord ( $CurrentUser );

  if ($Queue['lenght'] < MAX_BUILDING_QUEUE_SIZE)
  {
    $CanBuildElement = true;
  }
  else
  {
    $CanBuildElement = false;
  }

  $SubTemplate         = gettemplate('buildings_builds_row');
  $BuildingPage        = '';
  $caps = ECO_getPlanetCaps($CurrentUser, &$CurrentPlanet);
  foreach($lang['tech'] as $Element => $ElementName)
  {
    if (in_array($Element, $Allowed[$CurrentPlanet['planet_type']]))
    {
      $CurrentMaxFields      = CalculateMaxPlanetFields($CurrentPlanet);
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
        $HaveRessources        = IsElementBuyable ($CurrentUser, $CurrentPlanet, $Element, true, false);
        $parse                 = array();
        $BuildingLevel         = $CurrentPlanet[$resource[$Element]];

        // show energy on BuildingPage
        //================================
        $BuildLevelFactor     = 10; //$CurrentPlanet[$resource[$Element].'_porcent'];
        $BuildTemp            = $CurrentPlanet['temp_max'];
        $CurrentBuildtLvl     = $BuildingLevel;
        $BuildLevel           = ($CurrentBuildtLvl > 0) ? $CurrentBuildtLvl : 1;

        $Prod[3] = (floor(eval($sn_data[$Element]['deuterium_perhour']) * $config->resource_multiplier) * (1 + ($CurrentUser['rpg_geologue']  * 0.05)));
        $Prod[4] = (floor(eval($sn_data[$Element]['energy_perhour'])    * $config->resource_multiplier) * (1 + ($CurrentUser['rpg_ingenieur'] * 0.05)));

        if ($Element != 12)
        {
            $ActualNeed     = floor($Prod[4]);
        }
        else
        {
            $ActualNeed     = floor($Prod[3]);
        }

        $BuildLevel++;

        $Prod[3] = (floor(eval($sn_data[$Element]['deuterium_perhour']) * $config->resource_multiplier) * (1 + ($CurrentUser['rpg_geologue']  * 0.05)));
        $Prod[4] = (floor(eval($sn_data[$Element]['energy_perhour'])    * $config->resource_multiplier) * (1 + ($CurrentUser['rpg_ingenieur'] * 0.05)));

        if ($Element != 12)
        {
//            $EnergyNeed = colorNumber( pretty_number(floor($Prod[4] - $ActualNeed)) );
            $EnergyNeed = floor($Prod[4] - $ActualNeed);
        }
        else
        {
//            $EnergyNeed = colorNumber( pretty_number(floor($Prod[3] - $ActualNeed)) );
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
          // Spécial Laboratoire
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

        $destroy_price = GetBuildingPrice ($CurrentUser, $CurrentPlanet, $Element, true, true);
        $template->assign_block_vars('production', array(
          'ID'                => $Element,
          'NAME'              => $ElementName,
          'DESCRIPTION'       => $lang['res']['descriptions'][$Element],
          'LEVEL'             => ($BuildingLevel == 0) ? '' : "{$BuildingLevel}",

          'PRICE'             => GetElementPrice($CurrentUser, $CurrentPlanet, $Element),
          'TIME'              => ShowBuildTime($ElementBuildTime),
          'RESOURCES_LEFT'    => GetRestPrice($CurrentUser, $CurrentPlanet, $Element),

          'DESTROY_METAL'     => $destroy_price['metal'],
          'DESTROY_CRYSTAL'   => $destroy_price['crystal'],
          'DESTROY_DEUTERIUM' => $destroy_price['deuterium'],
          'DESTROY_TIME'      => pretty_time(GetBuildingTime  ($CurrentUser, $CurrentPlanet, $Element) / 2),

          'METAL_BALANCE'     => $caps['metal_perhour'][$Element],
          'CRYSTAL_BALANCE'   => $caps['crystal_perhour'][$Element],
          'DEUTERIUM_BALANCE' => $caps['deuterium_perhour'][$Element],
          'ENERGY_BALANCE'    => $EnergyNeed,

          'BUILD_LINK'        => $parse['click'],
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

  $parse['planet_field_current'] = $CurrentPlanet['field_current'];
  $parse['planet_field_max']     = $CurrentPlanet['field_max'] + ($CurrentPlanet[$resource[33]] * 5);
  $parse['field_libre']          = $parse['planet_field_max']  - $CurrentPlanet['field_current'];

  $page                          = parsetemplate($template, $parse);
  display($page, $lang['Builds']);
}

?>