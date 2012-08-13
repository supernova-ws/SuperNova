<?php

/**
 * infos.php
 *
 * Information about every aspect of in-game objects: buildings, officiers, techs etc
 *
 * @version 1.1st Security checks & tests by Gorlum for http://supernova.ws
 * @version 1.1
 * @copyright 2008 By Chlorel for XNova
 */
include('common.' . substr(strrchr(__FILE__, '.'), 1));

// ----------------------------------------------------------------------------------------------------------
// Creation du tableau de production de ressources
// Tient compte du parametrage de la planete (si la production n'est pas affectée a 100% par exemple
// Tient compte aussi du multiplicateur de ressources
//
function ShowProductionTable($CurrentUser, $CurrentPlanet, $BuildID, $Template)
{
  global $sn_data, $config;

  $unit_data = $sn_data[$BuildID];

  $config_resource_multiplier = $config->resource_multiplier;

  $BuildLevelFactor = $CurrentPlanet[$unit_data['name'] . "_porcent"];
  $BuildTemp = $CurrentPlanet['temp_max'];
  $BuildEnergyTech = $CurrentUser['energy_tech'];
  $CurrentBuildtLvl = $CurrentPlanet[$unit_data['name']];

  $BuildLevel = ($CurrentBuildtLvl > 0) ? $CurrentBuildtLvl : 1;

  $Prod[STRUC_MINE_METAL] = floor(mrc_modify_value($CurrentUser, $CurrentPlanet, MRC_TECHNOLOGIST, $config_resource_multiplier * eval($unit_data['metal_perhour'])));
  $Prod[STRUC_MINE_CRYSTAL] = floor(mrc_modify_value($CurrentUser, $CurrentPlanet, MRC_TECHNOLOGIST, $config_resource_multiplier * eval($unit_data['crystal_perhour'])));
  $Prod[STRUC_MINE_DEUTERIUM] = floor(mrc_modify_value($CurrentUser, $CurrentPlanet, MRC_TECHNOLOGIST, $config_resource_multiplier * eval($unit_data['deuterium_perhour'])));
  $Prod[STRUC_MINE_SOLAR] = floor(mrc_modify_value($CurrentUser, $CurrentPlanet, MRC_TECHNOLOGIST, /* $config_resource_multiplier * */ eval($unit_data['energy_perhour'])));

  $ActualProd = floor($Prod[$BuildID]);
  if ($BuildID != STRUC_MINE_FUSION)
  {
    $ActualNeed = floor($Prod[STRUC_MINE_SOLAR]);
  }
  else
  {
    $ActualNeed = floor($Prod[STRUC_MINE_DEUTERIUM]);
  }

  $BuildStartLvl = $CurrentBuildtLvl - 2;
  if ($BuildStartLvl < 1)
  {
    $BuildStartLvl = 1;
  }
  $Table = "";
  $ProdFirst = 0;
  for ($BuildLevel = $BuildStartLvl; $BuildLevel < $BuildStartLvl + 10; $BuildLevel++)
  {
    if ($BuildID != STRUC_MOON_PHALANX)
    {
      $Prod[STRUC_MINE_METAL] = floor(mrc_modify_value($CurrentUser, $CurrentPlanet, MRC_TECHNOLOGIST, $config_resource_multiplier * eval($unit_data['metal_perhour'])));
      $Prod[STRUC_MINE_CRYSTAL] = floor(mrc_modify_value($CurrentUser, $CurrentPlanet, MRC_TECHNOLOGIST, $config_resource_multiplier * eval($unit_data['crystal_perhour'])));
      $Prod[STRUC_MINE_DEUTERIUM] = floor(mrc_modify_value($CurrentUser, $CurrentPlanet, MRC_TECHNOLOGIST, $config_resource_multiplier * eval($unit_data['deuterium_perhour'])));
      $Prod[STRUC_MINE_SOLAR] = floor(mrc_modify_value($CurrentUser, $CurrentPlanet, MRC_TECHNOLOGIST, /* $config_resource_multiplier * */ eval($unit_data['energy_perhour'])));

      $bloc['build_lvl'] = ($CurrentBuildtLvl == $BuildLevel) ? "<font color=\"#ff0000\">" . $BuildLevel . "</font>" : $BuildLevel;
      if ($ProdFirst > 0)
      {
        if ($BuildID != STRUC_MINE_FUSION)
        {
          $bloc['build_gain'] = "<font color=\"lime\">(" . pretty_number(floor($Prod[$BuildID] - $ProdFirst)) . ")</font>";
        }
        else
        {
          $bloc['build_gain'] = "<font color=\"lime\">(" . pretty_number(floor($Prod[STRUC_MINE_SOLAR] - $ProdFirst)) . ")</font>";
        }
      }
      else
      {
        $bloc['build_gain'] = "";
      }
      if ($BuildID != STRUC_MINE_FUSION)
      {
        $bloc['build_prod'] = pretty_number(floor($Prod[$BuildID]));
        $bloc['build_prod_diff'] = pretty_number(floor($Prod[$BuildID] - $ActualProd), true, true);
        $bloc['build_need'] = pretty_number(floor($Prod[STRUC_MINE_SOLAR]), true, true);
        $bloc['build_need_diff'] = pretty_number(floor($Prod[STRUC_MINE_SOLAR] - $ActualNeed), true, true);
      }
      else
      {
        $bloc['build_prod'] = pretty_number(floor($Prod[STRUC_MINE_SOLAR]));
        $bloc['build_prod_diff'] = pretty_number(floor($Prod[STRUC_MINE_SOLAR] - $ActualProd), true, true);
        $bloc['build_need'] = pretty_number(floor($Prod[STRUC_MINE_DEUTERIUM]), true, true);
        $bloc['build_need_diff'] = pretty_number(floor($Prod[STRUC_MINE_DEUTERIUM] - $ActualNeed), true, true);
      }
      if ($ProdFirst == 0)
      {
        if ($BuildID != STRUC_MINE_FUSION)
        {
          $ProdFirst = floor($Prod[$BuildID]);
        }
        else
        {
          $ProdFirst = floor($Prod[STRUC_MINE_SOLAR]);
        }
      }
    }
    else
    {
      // Cas particulier de la phalange
      $bloc['build_lvl'] = ($CurrentBuildtLvl == $BuildLevel) ? "<font color=\"#ff0000\">" . $BuildLevel . "</font>" : $BuildLevel;
      $bloc['build_range'] = ($BuildLevel * $BuildLevel) - 1;
    }
    $Table .= parsetemplate($Template, $bloc);
  }

  return $Table;
}

function eco_render_rapid_fire($unit_id)
{
  global $lang, $sn_data;

  $unit_data = $sn_data[$unit_id];
  $unit_durability = $unit_data['shield'] + $unit_data['armor'];

  $str_rapid_from = '';
  $str_rapid_to = '';
  foreach (array_merge($sn_data[groups]['fleet'], $sn_data[groups]['defense_active']) as $enemy_id)
  {
    $enemy_data = $sn_data[$enemy_id];
    $enemy_durability = $enemy_data['shield'] + $enemy_data['armor'];

    $rapid = floor($unit_data['attack'] * (isset($unit_data['amplify'][$enemy_id]) ? $unit_data['amplify'][$enemy_id] : 1) / $enemy_durability);
    if ($rapid >= 1)
    {
      $str_rapid_to .= "{$lang['nfo_rf_again']} {$lang['tech'][$enemy_id]} <font color=\"#00ff00\">{$rapid}</font><br>";
    }

    $rapid = floor($enemy_data['attack'] * (isset($enemy_data['amplify'][$unit_id]) ? $enemy_data['amplify'][$unit_id] : 1) / $unit_durability);
    if ($rapid >= 1)
    {
      $str_rapid_from .= "{$lang['tech'][$enemy_id]} {$lang['nfo_rf_from']} <font color=\"#ff0000\">{$rapid}</font><br>";
    }
  }

  if ($str_rapid_to && $str_rapid_from)
  {
    $str_rapid_to .= '<hr>';
  }

  return array('to' => $str_rapid_to, 'from' => $str_rapid_from);
}

// ----------------------------------------------------------------------------------------------------------
// Construit la page par rapport a l'information demandée ...
// Permet de faire la differance entre les divers types et les pages speciales
//
$unit_id = sys_get_param_int('gid');

$sn_groups = &$sn_data['groups'];
$unit_data = &$sn_data[$unit_id];

lng_include('infos');

$DestroyTPL = '';
$TableHeadTPL = '';

$parse = $lang;
// Données de base
$parse['dpath'] = $dpath;
$parse['name'] = $lang['tech'][$unit_id];
$parse['image'] = $unit_id;
$parse['description'] = $lang['info'][$unit_id]['description'];

if ($unit_id >= 1 && $unit_id <= 3)
{
  // Cas des mines
  $PageTPL = gettemplate('info_buildings_table');
  $DestroyTPL = gettemplate('info_buildings_destroy');
  $TableHeadTPL = "<tr><td class=\"c\">{nfo_level}</td><td class=\"c\">{nfo_prod_p_hour}</td><td class=\"c\">{nfo_difference}</td><td class=\"c\">{nfo_used_energy}</td><td class=\"c\">{nfo_difference}</td></tr>";
  $TableTPL = "<tr><th>{build_lvl}</th><th>{build_prod} {build_gain}</th><th>{build_prod_diff}</th><th>{build_need}</th><th>{build_need_diff}</th></tr>";
}
elseif ($unit_id == 4)
{
  // Centrale Solaire
  $PageTPL = gettemplate('info_buildings_table');
  $DestroyTPL = gettemplate('info_buildings_destroy');
  $TableHeadTPL = "<tr><td class=\"c\">{nfo_level}</td><td class=\"c\">{nfo_prod_energy}</td><td class=\"c\">{nfo_difference}</td></tr>";
  $TableTPL = "<tr><th>{build_lvl}</th><th>{build_prod} {build_gain}</th><th>{build_prod_diff}</th></tr>";
}
elseif ($unit_id == STRUC_MINE_FUSION)
{
  // Centrale Fusion
  $PageTPL = gettemplate('info_buildings_table');
  $DestroyTPL = gettemplate('info_buildings_destroy');
  $TableHeadTPL = "<tr><td class=\"c\">{nfo_level}</td><td class=\"c\">{nfo_prod_energy}</td><td class=\"c\">{nfo_difference}</td><td class=\"c\">{nfo_used_deuter}</td><td class=\"c\">{nfo_difference}</td></tr>";
  $TableTPL = "<tr><th>{build_lvl}</th><th>{build_prod} {build_gain}</th><th>{build_prod_diff}</th><th>{build_need}</th><th>{build_need_diff}</th></tr>";
}
elseif ($unit_id >= STRUC_FACTORY_ROBOT && $unit_id <= 32)
{
  // Batiments Generaux
  $PageTPL = gettemplate('info_buildings_general');
  $DestroyTPL = gettemplate('info_buildings_destroy');
}
elseif ($unit_id == STRUC_TERRAFORMER)
{
  // Batiments Terraformer
  $PageTPL = gettemplate('info_buildings_general');
}
elseif ($unit_id == STRUC_ALLY_DEPOSIT)
{
  // Dépot d'alliance
  $PageTPL = gettemplate('info_buildings_general');
  $DestroyTPL = gettemplate('info_buildings_destroy');
}
elseif ($unit_id == STRUC_LABORATORY_NANO)
{
  // nano
  $PageTPL = gettemplate('info_buildings_general');
  $DestroyTPL = gettemplate('info_buildings_destroy');
}
elseif ($unit_id == STRUC_SILO)
{
  // Silo de missiles
  $PageTPL = gettemplate('info_buildings_general');
  $DestroyTPL = gettemplate('info_buildings_destroy');
}
elseif ($unit_id == STRUC_MOON_STATION)
{
  // Batiments lunaires
  $PageTPL = gettemplate('info_buildings_general');
}
elseif ($unit_id == STRUC_MOON_PHALANX)
{
  // Phalange
  $PageTPL = gettemplate('info_buildings_table');
  $TableHeadTPL = "<tr><td class=\"c\">{nfo_level}</td><td class=\"c\">{nfo_range}</td></tr>";
  $TableTPL = "<tr><th>{build_lvl}</th><th>{build_range}</th></tr>";
  $DestroyTPL = gettemplate('info_buildings_destroy');
}
elseif ($unit_id == STRUC_MOON_GATE)
{
  // Porte de Saut
  $PageTPL = gettemplate('info_buildings_general');
  $DestroyTPL = gettemplate('info_buildings_destroy');
}
elseif (in_array($unit_id, $sn_data['groups']['tech']))
{
  // Laboratoire
  $PageTPL = gettemplate('info_buildings_general');
}
elseif (in_array($unit_id, $sn_data['groups']['fleet']))
{
  // Flotte
  $PageTPL = gettemplate('info_buildings_fleet');

  $parse['element_typ'] = $lang['tech'][UNIT_SHIPS];
  $rapid_fire = eco_render_rapid_fire($unit_id);
  $parse['rf_info_to'] = $rapid_fire['to'];   // Rapid Fire vers
  $parse['rf_info_fr'] = $rapid_fire['from']; // Rapid Fire de

  $parse['hull_pt'] = pretty_number(($sn_data[$unit_id]['metal'] + $sn_data[$unit_id]['crystal']) / 10); // Points de Structure
  $parse['shield_pt'] = pretty_number($sn_data[$unit_id]['shield']);  // Points de Bouclier
  $parse['attack_pt'] = pretty_number($sn_data[$unit_id]['attack']);  // Points d'Attaque
  $parse['capacity_pt'] = pretty_number($sn_data[$unit_id]['capacity']); // Capacitée de fret
  $parse['base_speed'] = pretty_number($sn_data[$unit_id]['engine'][0]['speed']);    // Vitesse de base
  $parse['base_conso'] = pretty_number($sn_data[$unit_id]['engine'][0]['consumption']);  // Consommation de base

  $parse['ACTUAL_ARMOR'] = pretty_number(mrc_modify_value($user, false, array(MRC_ADMIRAL, TECH_ARMOR), ($sn_data[$unit_id]['metal'] + $sn_data[$unit_id]['crystal']) / 10));
  $parse['ACTUAL_SHIELD'] = pretty_number(mrc_modify_value($user, false, array(MRC_ADMIRAL, TECH_SHIELD), $sn_data[$unit_id]['shield']));
  $parse['ACTUAL_WEAPON'] = pretty_number(mrc_modify_value($user, false, array(MRC_ADMIRAL, TECH_WEAPON), $sn_data[$unit_id]['attack']));

  $ship_data = get_ship_data($unit_id, $user);
  $parse['ACTUAL_CAPACITY'] = pretty_number($ship_data['capacity']);
  $parse['ACTUAL_SPEED'] = pretty_number($ship_data['speed']);
  $parse['ACTUAL_CONSUMPTION'] = pretty_number($ship_data['consumption']);
  if(count($sn_data[$unit_id]['engine']) > 1)
  {
    $parse['upd_speed'] = "<font color=\"yellow\">(" . pretty_number($sn_data[$unit_id]['engine'][1]['speed']) . ")</font>";       // Vitesse rééquipée
    $parse['upd_conso'] = "<font color=\"yellow\">(" . pretty_number($sn_data[$unit_id]['engine'][1]['consumption']) . ")</font>"; // Consommation apres rééquipement
  }
}
elseif (in_array($unit_id, $sn_data['groups']['defense_active']))
{
  // Defenses
  $PageTPL = gettemplate('info_buildings_defense');
  $parse['element_typ'] = $lang['tech'][400];

  $rapid_fire = eco_render_rapid_fire($unit_id);
  $parse['rf_info_to'] = $rapid_fire['to'];   // Rapid Fire vers
  $parse['rf_info_fr'] = $rapid_fire['from']; // Rapid Fire de

  $parse['hull_pt'] = pretty_number(($sn_data[$unit_id]['metal'] + $sn_data[$unit_id]['crystal']) / 10); // Points de Structure
  $parse['shield_pt'] = pretty_number($sn_data[$unit_id]['shield']);  // Points de Bouclier
  $parse['attack_pt'] = pretty_number($sn_data[$unit_id]['attack']);  // Points d'Attaque
}
elseif ($unit_id >= 502 && $unit_id <= 503)
{
  // Misilles
  $PageTPL = gettemplate('info_buildings_defense');
  $parse['element_typ'] = $lang['tech'][400];
  $parse['hull_pt'] = pretty_number($sn_data[$unit_id]['metal'] + $sn_data[$unit_id]['crystal']); // Points de Structure
  $parse['shield_pt'] = pretty_number($sn_data[$unit_id]['shield']);  // Points de Bouclier
  $parse['attack_pt'] = pretty_number($sn_data[$unit_id]['attack']);  // Points d'Attaque
}
elseif(in_array($unit_id, $sn_data['groups']['mercenaries']) || in_array($unit_id, $sn_data['groups']['governors']) || in_array($unit_id, $sn_data['groups']['artifacts']) || in_array($unit_id, $sn_data['groups']['resources_all']))
{
  // Officiers
  $PageTPL = gettemplate('info_officiers_general');

  $mercenary = $sn_data[$unit_id];
  $mercenary_bonus = $mercenary['bonus'];
  $mercenary_bonus = $mercenary_bonus >= 0 ? "+{$mercenary_bonus}" : "{$mercenary_bonus}";
  switch ($mercenary['bonus_type'])
  {
    case BONUS_PERCENT:
      $mercenary_bonus = "{$mercenary_bonus}%";
      break;

    case BONUS_ADD:
      break;

    case BONUS_ABILITY:
      $mercenary_bonus = '';
      break;

    default:
      break;
  }

  $parse['EFFECT'] = $lang['info'][$unit_id]['effect'];
  $parse['mercenary_bonus'] = $mercenary_bonus;
  if(!(in_array($unit_id, $sn_data['groups']['artifacts']) || in_array($unit_id, $sn_data['groups']['resources_all'])))
  {
    $parse['max_level'] = $lang['sys_level'] . ' ' . 
    (in_array($unit_id, $sn_data['groups']['mercenaries']) ? mrc_get_level($user, $planetrow, $unit_id) : ($mercenary['location'] == LOC_USER ? $user[$sn_data[$unit_id]['name']] : ($planetrow['PLANET_GOVERNOR_ID'] == $unit_id ? $planetrow['PLANET_GOVERNOR_LEVEL'] : 0))) 
    . (isset($mercenary['max']) ? "/{$mercenary['max']}" : '');
  }
}

// ---- Tableau d'evolution
if ($TableHeadTPL != '')
{
  $parse['table_head'] = parsetemplate($TableHeadTPL, $lang);
  $parse['table_data'] = ShowProductionTable($user, $planetrow, $unit_id, $TableTPL);
}

// La page principale
$page = parsetemplate($PageTPL, $parse);

display($page, $lang['nfo_page_title']);

// -----------------------------------------------------------------------------------------------------------
// History version
// 2.0 - Using sn_timer instead of script generated by InsertScriptChronoApplet
// 1.1 - Ajout JumpGate pour la porte de saut comme la présente OGame ... Enfin un peu mieux quand meme !
// 1.0 - Réécriture (réinventation de l'eau tiède)

?>
