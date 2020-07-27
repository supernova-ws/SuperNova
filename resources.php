<?php

/**
 * resources.php
 *
 * Planet resource interface page
 *
 * 2.2 - copyright (ñ) 2010 by Gorlum for http://supernova.ws
 *   [~] - more optimization to utilize PTE
 *   [~] - code formatting according to PCG
 *   [~] - content of BuildRessourcePage.php moved to resource.php
 * 2.1 - copyright 2010 by Gorlum for http://supernova.ws
 *   [~] - Security checked for SQL-injection
 * 2.0 - copyright 2010 by Gorlum for http://supernova.ws
 *   [+] - almost fully rewrote and optimized
 * 1.0 [BuildRessourcePage.php] copyright 2008 by ShadoV for XNova
 *   [+] - Mise en module initiale (creation)
 * 1.1 - copyright 2010 by Gorlum for http://supernova.ws
 *   [%] - Security checks & tests by Gorlum for http://supernova.ws
 * 1.0 copyright (ñ) 2008 by Chlorel for XNova
 *   [!] - Passage en fonction pour utilisation XNova
 *
**/

include('common.' . substr(strrchr(__FILE__, '.'), 1));

use \Meta\Economic\ResourceCalculations;
use Planet\DBStaticPlanet;
use Planet\Planet;

/**
 * @param $resource_id
 * @param ResourceCalculations $capsObj
 */
function int_calc_storage_bar($resource_id, $capsObj)
{
  global $lang, $template, $planetrow, $user;

  $totalProduction      = $capsObj->getProduction($resource_id);
  $storage_fill         = $capsObj->getStorage($resource_id) ? floor(mrc_get_level($user, $planetrow, $resource_id) / $capsObj->getStorage($resource_id) * 100) : 0;

  $template->assign_block_vars('resources', [
    'NAME'        => $lang["sys_" . pname_resource_name($resource_id)],

    'HOURLY'      => $totalProduction,
    'DAILY'       => $totalProduction * 24,
    'WEEKLY'      => $totalProduction * 24 * 7,
    'MONTHLY'     => $totalProduction * 24 * 30,

    'STORAGE'     => intval($storage_fill),
    'BAR'         => min($storage_fill, 100),
  ]);
};

$ValidList['percent'] = array (  0,  10,  20,  30,  40,  50,  60,  70,  80,  90, 100 );
$template = SnTemplate::gettemplate('resources', true);

/** @noinspection PhpUnhandledExceptionInspection */
$planet = SN::$gc->repoV2->getPlanet($planetrow['id']);
if(!empty($transmutation_result = $planet->sn_sys_planet_core_transmute($user))) {
  $template->assign_block_vars('result', $transmutation_result);
  $planet->dbLoadRecord($planetrow['id']);
}

$sn_group_factories = sn_get_groups('factories');
/**
 * @param debug  $debug
 * @param array  $sn_group_factories
 * @param array  $planetrow
 * @param Planet $planet
 *
 * @return mixed
 */
function updateProductionSpeeds($debug, $sn_group_factories, $planetrow, $planet) {
  $production = $_POST['production'];
  if (!is_array($production)) {
    return $planetrow;
  }

  $SubQry = [];
  foreach ($production as $prod_id => $percent) {
    if ($percent > 100 || $percent < 0) {
      $debug->warning('Supplying wrong production percent (less then 0 or greater then 100)', 'Hack attempt', 302, array('base_dump' => true));
      die();
    }

    $prod_id = intval($prod_id);
    if (in_array($prod_id, $sn_group_factories) && get_unit_param($prod_id, P_MINING_IS_MANAGED)) {
      $field_name = pname_factory_production_field_name($prod_id);
      $percent = floor($percent / 10);
      $planetrow[$field_name] = $percent;
      //$SubQry                 .= "`{$field_name}` = '{$percent}',";
      $SubQry[] = "`{$field_name}` = '{$percent}'";
    } else {
      $debug->warning('Supplying wrong ID in production array - attempt to change some field - ID' . $prod_id, 'Resource Page', 301);
      continue;
    }
  }

  !empty($SubQry) ? DBStaticPlanet::db_planet_set_by_id($planetrow['id'], implode(',', $SubQry)) : false;
  if (!empty($SubQry)) {
    $planet->dbLoadRecord($planetrow['id']);
  }

  return $planetrow;
}

$planetrow = updateProductionSpeeds($debug, $sn_group_factories, $planetrow, $planet);

// -------------------------------------------------------------------------------------------------------
// $BuildTemp                   = $planetrow[ 'temp_max' ];
// $BuildEnergyTech             = $user['energy_tech'];
for ($Option = 10; $Option >= 0; $Option--)
{
 $template->assign_block_vars('option', array(
   'VALUE' => $Option * 10,
 ));
}

$capsObj = new ResourceCalculations();
$capsObj->eco_get_planet_caps($user, $planetrow, 3600);

$template->assign_block_vars('production', array(
  'TYPE'           => $lang['res_basic_income'],

  'METAL_TYPE'     => $capsObj->productionCurrentMatrix[RES_METAL][0],
  'CRYSTAL_TYPE'   => $capsObj->productionCurrentMatrix[RES_CRYSTAL][0],
  'DEUTERIUM_TYPE' => $capsObj->productionCurrentMatrix[RES_DEUTERIUM][0],
  'ENERGY_TYPE'    => $capsObj->productionCurrentMatrix[RES_ENERGY][0],
));

foreach($sn_group_factories as $unit_id)
{
  if(mrc_get_level($user, $planetrow, $unit_id) > 0 && get_unit_param($unit_id))
  {
    $level_plain = mrc_get_level($user, $planetrow, $unit_id, false, true);
    $template->assign_block_vars('production', array(
      'ID'             => $unit_id,
      'PERCENT'        => $planetrow[pname_factory_production_field_name($unit_id)] * 10,
      'TYPE'           => $lang['tech'][$unit_id],
      'LEVEL'          => $level_plain,
      'LEVEL_BONUS'    => mrc_get_level($user, $planetrow, $unit_id) - $level_plain,
      'LEVEL_TYPE'     => ($unit_id > 200) ? $lang['quantity'] : $lang['level'],

      'METAL_TYPE'     => $capsObj->productionCurrentMatrix[RES_METAL][$unit_id],
      'CRYSTAL_TYPE'   => $capsObj->productionCurrentMatrix[RES_CRYSTAL][$unit_id],
      'DEUTERIUM_TYPE' => $capsObj->productionCurrentMatrix[RES_DEUTERIUM][$unit_id],
      'ENERGY_TYPE'    => $capsObj->productionCurrentMatrix[RES_ENERGY][$unit_id],

      'METAL_FULL'     => $capsObj->productionFullMatrix[RES_METAL][$unit_id],
      'CRYSTAL_FULL'   => $capsObj->productionFullMatrix[RES_CRYSTAL][$unit_id],
      'DEUTERIUM_FULL' => $capsObj->productionFullMatrix[RES_DEUTERIUM][$unit_id],
      'ENERGY_FULL'    => $capsObj->productionFullMatrix[RES_ENERGY][$unit_id],

      'P_MINING_IS_MANAGED' => get_unit_param($unit_id, P_MINING_IS_MANAGED),
    ));
  }
}


$user_dark_matter = mrc_get_level($user, false, RES_DARK_MATTER);
$template->assign_recursive($planet->tpl_planet_density_info($user_dark_matter));

$template->assign_block_vars('production', array(
  'TYPE'           => $lang['res_total'],

  'METAL_TYPE'     => $capsObj->getProduction(RES_METAL),
  'CRYSTAL_TYPE'   => $capsObj->getProduction(RES_CRYSTAL),
  'DEUTERIUM_TYPE' => $capsObj->getProduction(RES_DEUTERIUM),
  'ENERGY_TYPE'    => $capsObj->getProduction(RES_ENERGY),

  'METAL_FULL'     => $capsObj->getProductionFull(RES_METAL),
  'CRYSTAL_FULL'   => $capsObj->getProductionFull(RES_CRYSTAL),
  'DEUTERIUM_FULL' => $capsObj->getProductionFull(RES_DEUTERIUM),
  'ENERGY_FULL'    => $capsObj->getProductionFull(RES_ENERGY),
));

int_calc_storage_bar(RES_METAL, $capsObj);
int_calc_storage_bar(RES_CRYSTAL, $capsObj);
int_calc_storage_bar(RES_DEUTERIUM, $capsObj);

$template->assign_vars(array(
 'PLANET_NAME'          => $planetrow['name'],
 'PLANET_TYPE'          => $planetrow['planet_type'],

 'PRODUCTION_LEVEL'     => floor($capsObj->efficiency * 100),

 'PAGE_HINT'            => $lang['res_hint'],
));

SnTemplate::display($template, $lang['res_planet_production']);
