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

function int_calc_storage_bar($resource_name)
{
  global $lang, $config, $template, $parse, $caps;

  $resource_income_name = $resource_name.'_basic_income';
  $resource_max_name    = $resource_name.'_max';

  $totalProduction      = floor($caps['planet'][$resource_name.'_perhour'] * $caps['production'] + $caps[$resource_name.'_perhour'][0]);
  $storage_fill         = floor($caps['planet'][$resource_name] / $caps['planet'][$resource_max_name] * 100);

  if ($caps['planet'][$resource_max_name] < $caps['planet'][$resource_name])
  {
    $parse[$resource_max_name] = '<font color="#ff0000">';
  }
  else
  {
    $parse[$resource_max_name] = '<font color="#00ff00">';
  }

  $template->assign_block_vars('resources', array(
    'NAME'        => $lang["sys_$resource_name"],

    'BASIC_INCOME'=> $config->$resource_income_name * $config->resource_multiplier,

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
    if(in_array($prod_id, $sn_data['groups']['factories']))
    {
      $field_name              = "{$sn_data[$prod_id]['name']}_porcent";
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

$caps = eco_get_planet_caps($user, $planetrow);

$ProdID = 0;
$template->assign_block_vars('production', array(
  'TYPE'           => $lang['res_basic_income'],

  'METAL_TYPE'     => pretty_number($caps['metal_perhour'][$ProdID], true, true),
  'CRYSTAL_TYPE'   => pretty_number($caps['crystal_perhour'][$ProdID], true, true),
  'DEUTERIUM_TYPE' => pretty_number($caps['deuterium_perhour'][$ProdID], true, true),
  'ENERGY_TYPE'    => pretty_number($caps['energy'][$ProdID], true, true),
));

foreach($sn_data['groups']['factories'] as $ProdID)
{
  $resource_db_name = $sn_data[$ProdID]['name'];
  if($planetrow[$resource_db_name] > 0 && isset($sn_data[$ProdID]))
  {
    $level_plain = $planetrow[$resource_db_name];
    $template->assign_block_vars('production', array(
      'ID'             => $ProdID,
      'NAME'           => $resource_db_name,
      'PERCENT'        => $planetrow[$resource_db_name . '_porcent'] * 10,
      'TYPE'           => $lang['tech'][$ProdID],
      'LEVEL'          => $level_plain,
      'LEVEL_BONUS'    => mrc_get_level($user, $planetrow, $ProdID) - $level_plain,
      'LEVEL_TYPE'     => ($ProdID > 200) ? $lang['quantity'] : $lang['level'],

      'METAL_TYPE'     => pretty_number($caps['metal_perhour'][$ProdID]     * $caps['production'], true, true),
      'CRYSTAL_TYPE'   => pretty_number($caps['crystal_perhour'][$ProdID]   * $caps['production'], true, true),
      'DEUTERIUM_TYPE' => pretty_number($caps['deuterium_perhour'][$ProdID] * $caps['production'], true, true),
      'ENERGY_TYPE'    => pretty_number($caps['energy'][$ProdID], true, true),

      'SELECT'         => $row_select,
    ));
  }
}

$template->assign_block_vars('production', array(
  'TYPE'           => $lang['res_total'],

  'METAL_TYPE'     => pretty_number($caps['planet']['metal_perhour'] * $caps['production'] + $caps['metal_perhour'][0], true, true),
  'CRYSTAL_TYPE'   => pretty_number($caps['planet']['crystal_perhour'] * $caps['production'] + $caps['crystal_perhour'][0], true, true),
  'DEUTERIUM_TYPE' => pretty_number($caps['planet']['deuterium_perhour'] * $caps['production'] + $caps['deuterium_perhour'][0], true, true),
  'ENERGY_TYPE'    => pretty_number($caps['planet']['energy_max'] - $caps['planet']['energy_used'], true, true),
));

int_calc_storage_bar('metal');
int_calc_storage_bar('crystal');
int_calc_storage_bar('deuterium');

$template->assign_vars(array(
 'PLANET_NAME'      => $planetrow['name'],

 'PRODUCTION_LEVEL' => floor($caps['production'] * 100),

 'PAGE_HINT'        => $lang['res_hint'],
));

display(parsetemplate( $template, $parse ), $lang['res_planet_production']);

?>
