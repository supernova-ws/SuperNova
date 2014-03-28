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

function int_calc_storage_bar($resource_id)
{
  global $lang, $template, $caps_real, $planetrow;

  $resource_name = get_unit_param($resource_id, P_NAME);

  $totalProduction      = $caps_real['total'][$resource_id];
  $storage_fill         = $caps_real['total_storage'][$resource_id] ? floor($planetrow[$resource_name] / $caps_real['total_storage'][$resource_id] * 100) : 0;

  $template->assign_block_vars('resources', array(
    'NAME'        => $lang["sys_$resource_name"],

//    'BASIC_INCOME'=> $config->$resource_income_name * $config->resource_multiplier,

    'HOURLY'      => pretty_number($totalProduction, true, true),
    'WEEKLY'      => pretty_number($totalProduction * 24 * 7, true, true),
    'DAILY'       => pretty_number($totalProduction * 24, true, true),
    'MONTHLY'     => pretty_number($totalProduction * 24 * 30, true, true),

    'STORAGE'     => intval($storage_fill),
    'BAR'         => min($storage_fill, 100),
  ));
};

$ValidList['percent'] = array (  0,  10,  20,  30,  40,  50,  60,  70,  80,  90, 100 );
$template = gettemplate('resources', true);

$transmutation_result = sn_sys_planet_core_transmute($user, $planetrow);
if(!empty($transmutation_result))
{
  $template->assign_block_vars('result', $transmutation_result); // array('STATUS' => $transmutation_result['STATUS'], 'MESSAGE' => $transmutation_result['MESSAGE']));
}

$sn_group_factories = sn_get_groups('factories');
$production = $_POST['production'];
$SubQry     = '';
if(is_array($production))
{
  foreach($production as $prod_id => $percent)
  {
    if($percent > 100 || $percent < 0)
    {
      $debug->warning('Supplying wrong production percent (less then 0 or greater then 100)', 'Hack attempt', 302, array('base_dump' => true));
      die();
    }

    $prod_id = intval($prod_id);
    if(in_array($prod_id, $sn_group_factories))
    {
      $field_name              = get_unit_param($prod_id, P_NAME) . '_porcent';
      $percent                 = floor($percent / 10);
      $planetrow[$field_name]  = $percent;
      $SubQry                 .= "`{$field_name}` = '{$percent}',";
    }
    else
    {
      $debug->warning('Supplying wrong ID in production array - attempt to change some field', 'Resource Page', 301);
      die();
    }
  }

  $SubQry = substr($SubQry, 0, -1);
  if($SubQry)
  {
   doquery("UPDATE {{planets}} SET {$SubQry} WHERE `id` = '{$planetrow['id']}';");
  }
}

// -------------------------------------------------------------------------------------------------------
$BuildTemp                   = $planetrow[ 'temp_max' ];
$BuildEnergyTech             = $user['energy_tech'];

for ($Option = 10; $Option >= 0; $Option--)
{
 $template->assign_block_vars('option', array(
   'VALUE' => $Option * 10,
 ));
}

$caps_real = eco_get_planet_caps($user, $planetrow, 3600);

$template->assign_block_vars('production', array(
  'TYPE'           => $lang['res_basic_income'],

  'METAL_TYPE'     => pretty_number($caps_real['production'][RES_METAL][0], true, true),
  'CRYSTAL_TYPE'   => pretty_number($caps_real['production'][RES_CRYSTAL][0], true, true),
  'DEUTERIUM_TYPE' => pretty_number($caps_real['production'][RES_DEUTERIUM][0], true, true),
  'ENERGY_TYPE'    => pretty_number($caps_real['production'][RES_ENERGY][0], true, true),
));

foreach($sn_group_factories as $unit_id)
{
  $resource_db_name = get_unit_param($unit_id, P_NAME);
  if($planetrow[$resource_db_name] > 0 && get_unit_param($unit_id))
  {
    $level_plain = $planetrow[$resource_db_name];
    $template->assign_block_vars('production', array(
      'ID'             => $unit_id,
      'NAME'           => $resource_db_name,
      'PERCENT'        => $planetrow[$resource_db_name . '_porcent'] * 10,
      'TYPE'           => $lang['tech'][$unit_id],
      'LEVEL'          => $level_plain,
      'LEVEL_BONUS'    => mrc_get_level($user, $planetrow, $unit_id) - $level_plain,
      'LEVEL_TYPE'     => ($unit_id > 200) ? $lang['quantity'] : $lang['level'],

      'METAL_TYPE'     => pretty_number($caps_real['production'][RES_METAL][$unit_id], true, true),
      'CRYSTAL_TYPE'   => pretty_number($caps_real['production'][RES_CRYSTAL][$unit_id], true, true),
      'DEUTERIUM_TYPE' => pretty_number($caps_real['production'][RES_DEUTERIUM][$unit_id], true, true),
      'ENERGY_TYPE'    => pretty_number($caps_real['production'][RES_ENERGY][$unit_id], true, true),

      'METAL_FULL'     => pretty_number($caps_real['production_full'][RES_METAL][$unit_id], true, true),
      'CRYSTAL_FULL'   => pretty_number($caps_real['production_full'][RES_CRYSTAL][$unit_id], true, true),
      'DEUTERIUM_FULL' => pretty_number($caps_real['production_full'][RES_DEUTERIUM][$unit_id], true, true),
      'ENERGY_FULL'    => pretty_number($caps_real['production_full'][RES_ENERGY][$unit_id], true, true),

      'SELECT'         => $row_select,
    ));
  }
}

$user_dark_matter = mrc_get_level($user, false, RES_DARK_MATTER);
$planet_density_index = $planetrow['density_index'];
$density_price_chart = planet_density_price_chart($planet_density_index);
tpl_planet_density_info($template, $density_price_chart, $user_dark_matter);

$template->assign_block_vars('production', array(
  'TYPE'           => $lang['res_total'],

  'METAL_TYPE'     => pretty_number($caps_real['total'][RES_METAL], true, true),
  'CRYSTAL_TYPE'   => pretty_number($caps_real['total'][RES_CRYSTAL], true, true),
  'DEUTERIUM_TYPE' => pretty_number($caps_real['total'][RES_DEUTERIUM], true, true),
  'ENERGY_TYPE'    => pretty_number($caps_real['total'][RES_ENERGY], true, true),

  'METAL_FULL'     => pretty_number($caps_real['total_production_full'][RES_METAL], true, true),
  'CRYSTAL_FULL'   => pretty_number($caps_real['total_production_full'][RES_CRYSTAL], true, true),
  'DEUTERIUM_FULL' => pretty_number($caps_real['total_production_full'][RES_DEUTERIUM], true, true),
  'ENERGY_FULL'    => pretty_number($caps_real['total_production_full'][RES_ENERGY], true, true),
));

int_calc_storage_bar(RES_METAL);
int_calc_storage_bar(RES_CRYSTAL);
int_calc_storage_bar(RES_DEUTERIUM);

$template->assign_vars(array(
 'PLANET_NAME'          => $planetrow['name'],
 'PLANET_TYPE'          => $planetrow['planet_type'],
 'PLANET_DENSITY_INDEX' => $planet_density_index,
 'PLANET_CORE_TEXT'     => $lang['uni_planet_density_types'][$planet_density_index],

 'PRODUCTION_LEVEL'     => floor($caps_real['efficiency'] * 100),

 'PAGE_HINT'            => $lang['res_hint'],
));

display($template, $lang['res_planet_production']);
