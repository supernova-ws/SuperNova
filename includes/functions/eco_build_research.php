<?php

/**
 * ResearchBuildingPage.php
 *
 * @version 1.2s - Security checked for SQL-injection by Gorlum for http://supernova.ws
 * @version 1.2
 * @copyright 2008 by Chlorel for XNova
 */

function eco_lab_is_building($config, $que)
{
  return $que['in_que_abs'][31] && !$config->BuildLabWhileRun ? true : false;
}

// Page de Construction de niveau de Recherche
// $planet -> Planete sur laquelle la construction est lancée
//                   Parametre passé par adresse, cela permet de mettre les valeurs a jours
//                   dans le programme appelant
// $CurrentUser   -> Utilisateur qui a lancé la construction
// $InResearch    -> Indicateur qu'il y a une Recherche en cours
// $ThePlanet     -> Planete sur laquelle se realise la technologie eventuellement
function ResearchBuildingPage (&$planet, $CurrentUser, $InResearch, $ThePlanet, $que)
{
  global $lang, $resource, $reslist, $dpath, $config, $sn_data;

  $sn_data_group_tech = $sn_data['groups']['tech'];

  $NoResearchMessage = "";
  // Deja est qu'il y a un laboratoire sur la planete ???
  if ($planet[$resource[31]] == 0)
  {
    message($lang['no_laboratory'], $lang['Research']);
  }

  // Ensuite ... Est ce que la labo est en cours d'upgrade ?
  if (eco_lab_is_building($config, $que))
  {
    $NoResearchMessage = $lang['labo_on_update'];
  }

  // Boucle d'interpretation des eventuelles commandes
  $TheCommand = sys_get_param_str('cmd');
  $Techno     = sys_get_param_int('tech');
  if(isset($TheCommand))
  {
    if(is_numeric($Techno))
    {
      if ( in_array($Techno, $reslist['tech']) ) 
      {
        // Bon quand on arrive ici ... On sait deja qu'on a une technologie valide

        if ( is_array ($ThePlanet) ) {
          $WorkingPlanet = $ThePlanet;
        } else {
          $WorkingPlanet = $planet;
        }

        switch($TheCommand){
          case 'cancel':
            if ($ThePlanet['b_tech_id'] == $Techno) {
              $costs                        = GetBuildingPrice($CurrentUser, $WorkingPlanet, $Techno);
              $WorkingPlanet['metal']      += $costs['metal'];
              $WorkingPlanet['crystal']    += $costs['crystal'];
              $WorkingPlanet['deuterium']  += $costs['deuterium'];
              $WorkingPlanet['b_tech_id']   = 0;
              $WorkingPlanet['b_tech']      = 0;
              $CurrentUser['b_tech_planet'] = 0;
              $UpdateData                   = true;
              $InResearch                   = false;
              doquery("UPDATE {{planets}} SET `b_tech_id` = '{$WorkingPlanet['b_tech_id']}', `b_tech` = '{$WorkingPlanet['b_tech']}',
                `metal` = `metal` + {$costs['metal']}, `crystal` = `crystal` + '{$costs['crystal']}', `deuterium` = `deuterium` + '{$costs['deuterium']}' 
                WHERE `id` = '{$WorkingPlanet['id']}' LIMIT 1;");
            }
          break;
          
          case 'search':
//            if ( !($InResearch && eco_unit_busy($CurrentUser, $planet, false, $Techno)) )
            if ( !($InResearch || eco_lab_is_building($config, $que)) )
            {
              if ( eco_can_build_unit($CurrentUser, $WorkingPlanet, $Techno) && IsElementBuyable($CurrentUser, $WorkingPlanet, $Techno) ) 
              {
                $costs                        = GetBuildingPrice($CurrentUser, $WorkingPlanet, $Techno);
                $WorkingPlanet['metal']      -= $costs['metal'];
                $WorkingPlanet['crystal']    -= $costs['crystal'];
                $WorkingPlanet['deuterium']  -= $costs['deuterium'];
                $WorkingPlanet["b_tech_id"]   = $Techno;
                $WorkingPlanet["b_tech"]      = time() + GetBuildingTime($CurrentUser, $WorkingPlanet, $Techno);
                $CurrentUser["b_tech_planet"] = $WorkingPlanet["id"];
                $UpdateData                   = true;
                $InResearch                   = true;

                doquery("UPDATE {{planets}} SET `b_tech_id` = '{$WorkingPlanet['b_tech_id']}', `b_tech` = '{$WorkingPlanet['b_tech']}', 
                  `metal` = `metal` - {$costs['metal']}, `crystal` = `crystal` - '{$costs['crystal']}', `deuterium` = `deuterium` - '{$costs['deuterium']}' 
                  WHERE `id` = '{$WorkingPlanet['id']}' LIMIT 1;");
              }
            }
            elseif($InResearch)
            {
              $NoResearchMessage = $lang['build_research_in_progress'];
            }
            else
            {
              $NoResearchMessage = $lang['labo_on_update'];
            };
          break;
        }
        if($UpdateData == true)
        {
          doquery("UPDATE {{users}} SET `b_tech_planet` = '{$CurrentUser['b_tech_planet']}' WHERE `id` = '{$CurrentUser['id']}';");
        }
        //byo; FIXed by PekopT 05.08.2008 thread   http://forum.ragezone.com/showthread.php?p=3734880#post3734880
        $planet = $WorkingPlanet;
        if ( is_array ($ThePlanet) ) {
          $ThePlanet     = $WorkingPlanet;
        } else {
          $planet = $WorkingPlanet;
          if ($TheCommand == 'search') {
            $ThePlanet = $planet;
          }
        }
      }
    }
  }

  $template = gettemplate('buildings_research', true);

  foreach($sn_data_group_tech as $Tech)
  {
    if(!eco_can_build_unit($CurrentUser, $planet, $Tech))
    {
      continue;
    }

    $building_level          = $CurrentUser[$resource[$Tech]];
    $build_data = eco_get_build_data($CurrentUser, $planet, $Tech, $building_level);

    // Arbre de decision de ce que l'on met dans la derniere case de la ligne
    if($InResearch)
    {
      // Y a une construction en cours
      if($ThePlanet['b_tech_id'] == $Tech)
      {
        // C'est le technologie en cours de recherche
        $bloc       = $lang;
        if ($ThePlanet['id'] != $planet['id'])
        {
          $build_planet = $ThePlanet;
          $bloc['tech_name']  = "{$lang['on']}<br />{$build_planet['name']}";
        }
        else
        {
          $build_planet = $planet;
          $bloc['tech_name']  = '';
        }
        $bloc['tech_time']  = $build_planet['b_tech'] - time();
        $bloc['tech_home']  = $build_planet['id'];
        $bloc['tech_id']    = $build_planet['b_tech_id'];
        $TechnoLink  = parsetemplate(gettemplate('buildings_research_script'), $bloc);
      }
      else
      {
        // Technologie pas en cours recherche
        $TechnoLink  = "<center>-</center>";
      }
    }
    else
    {
      $LevelToDo = $building_level + 1;
      $TechnoLink = $lang['Rechercher'] . ($LevelToDo == 1 ? '' : "<br>{$lang['level']}&nbsp;{$LevelToDo}");
      if (IsElementBuyable($CurrentUser, $planet, $Tech) && !eco_lab_is_building($config, $que))
      {
        $TechnoLink  = "<a href=\"buildings.php?mode=research&cmd=search&tech={$Tech}\"><span class=\"success\">{$TechnoLink}</span></a>";
      } else {
        $TechnoLink  = "<span class=\"error\">{$TechnoLink}</span>";
      }
    }

    $temp[RES_METAL]     = floor($planet['metal'] - $build_data[BUILD_CREATE][RES_METAL]); // + $fleet_list['own']['total'][RES_METAL]
    $temp[RES_CRYSTAL]   = floor($planet['crystal'] - $build_data[BUILD_CREATE][RES_CRYSTAL]); // + $fleet_list['own']['total'][RES_CRYSTAL]
    $temp[RES_DEUTERIUM] = floor($planet['deuterium'] - $build_data[BUILD_CREATE][RES_DEUTERIUM]); // + $fleet_list['own']['total'][RES_DEUTERIUM]

    $template->assign_block_vars('production', array(
      'ID'                => $Tech,
      'NAME'              => $lang['tech'][$Tech],
      'LEVEL'             => $building_level,
      'DESCRIPTION'       => $lang['info'][$Tech]['description_short'],

     'BUILD_CAN'          => $build_data['CAN'][BUILD_CREATE],
     'TIME'               => pretty_time($build_data[BUILD_CREATE][RES_TIME]),
     'METAL'              => $build_data[BUILD_CREATE][RES_METAL],
     'CRYSTAL'            => $build_data[BUILD_CREATE][RES_CRYSTAL],
     'DEUTERIUM'          => $build_data[BUILD_CREATE][RES_DEUTERIUM],
     'ENERGY'             => $build_data[BUILD_CREATE][RES_ENERGY],
                          
     'METAL_PRINT'        => pretty_number($build_data[BUILD_CREATE][RES_METAL], true, $planet['metal']),
     'CRYSTAL_PRINT'      => pretty_number($build_data[BUILD_CREATE][RES_CRYSTAL], true, $planet['crystal']),
     'DEUTERIUM_PRINT'    => pretty_number($build_data[BUILD_CREATE][RES_DEUTERIUM], true, $planet['deuterium']),
     'ENERGY_PRINT'       => pretty_number($build_data[BUILD_CREATE][RES_ENERGY], true, $planet['energy_max']),
                          
     'METAL_REST'         => pretty_number($temp[RES_METAL], true, true),
     'CRYSTAL_REST'       => pretty_number($temp[RES_CRYSTAL], true, true),
     'DEUTERIUM_REST'     => pretty_number($temp[RES_DEUTERIUM], true, true),
     'METAL_REST_NUM'     => $temp[RES_METAL],
     'CRYSTAL_REST_NUM'   => $temp[RES_CRYSTAL],
     'DEUTERIUM_REST_NUM' => $temp[RES_DEUTERIUM],

     'TECH_LINK'          => $TechnoLink,
    ));
  }

  $template->assign_vars(array(
    'MESSAGE'            => $NoResearchMessage,
  ));

  display(parsetemplate($template), $lang['Research']);
}

// History revision
// 1.0 - Release initiale / modularisation / Reecriture / Commentaire / Mise en forme
// 1.1 - BUG affichage de la techno en cours
// 1.2 - Restructuration modification pour permettre d'annuller proprement une techno en cours

?>
