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

$ugamela_root_path = (defined('SN_ROOT_PATH')) ? SN_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include("{$ugamela_root_path}common.{$phpEx}");

if ($IsUserChecked == false)
{
  includeLang('login');
  header("Location: login.php");
}

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

    'HOURLY'      => colorNumber(pretty_number($totalProduction)),
    'DAILY'       => colorNumber(pretty_number($totalProduction * 24)),
    'WEEKLY'      => colorNumber(pretty_number($totalProduction * 24 * 7)),
    'MONTHLY'     => colorNumber(pretty_number($totalProduction * 24 * 30)),

    'STORAGE'     => intval($storage_fill),
    'BAR'         => min($storage_fill, 100),
  ));
};

CheckPlanetUsedFields ( $planetrow );

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
    if(in_array($prod_id, $reslist['prod']))
    {
      $field_name              = "{$resource[$prod_id]}_porcent";
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
$BuildTemp                           = $planetrow[ 'temp_max' ];

for ($Option = 10; $Option >= 0; $Option--)
{
 $template->assign_block_vars('option', array(
   'VALUE' => $Option * 10,
 ));
}

$caps = ECO_getPlanetCaps($user, $planetrow);

$ProdID = 0;
$template->assign_block_vars('production', array(
  'TYPE'           => $lang['res_basic_income'],

  'METAL_TYPE'     => colorNumber(pretty_number($caps['metal_perhour'][$ProdID])),
  'CRYSTAL_TYPE'   => colorNumber(pretty_number($caps['crystal_perhour'][$ProdID])),
  'DEUTERIUM_TYPE' => colorNumber(pretty_number($caps['deuterium_perhour'][$ProdID])),
  'ENERGY_TYPE'    => colorNumber(pretty_number($caps['energy'][$ProdID])),
));

foreach($reslist['prod'] as $ProdID)
{
 if ($planetrow[$resource[$ProdID]] > 0 && isset($sn_data[$ProdID]))
 {
   $template->assign_block_vars('production', array(
     'ID'             => $ProdID,
     'NAME'           => $resource[$ProdID],
     'PERCENT'        => $planetrow[$resource[$ProdID] .'_porcent'] * 10,
     'TYPE'           => $lang['tech'][$ProdID],
     'LEVEL'          => $planetrow[ $resource[$ProdID] ],
     'LEVEL_TYPE'     => ($ProdID > 200) ? $lang['quantity'] : $lang['level'],

     'METAL_TYPE'     => colorNumber(pretty_number($caps['metal_perhour'][$ProdID]     * $caps['production'])),
     'CRYSTAL_TYPE'   => colorNumber(pretty_number($caps['crystal_perhour'][$ProdID]   * $caps['production'])),
     'DEUTERIUM_TYPE' => colorNumber(pretty_number($caps['deuterium_perhour'][$ProdID] * $caps['production'])),
     'ENERGY_TYPE'    => colorNumber(pretty_number($caps['energy'][$ProdID])),

     'SELECT'         => $row_select,
   ));
 }
}

$template->assign_block_vars('production', array(
  'TYPE'           => $lang['res_total'],

  'METAL_TYPE'     => colorNumber(pretty_number(floor($caps['planet']['metal_perhour'] * $caps['production'] + $caps['metal_perhour'][0]))),
  'CRYSTAL_TYPE'   => colorNumber(pretty_number(floor($caps['planet']['crystal_perhour'] * $caps['production'] + $caps['crystal_perhour'][0]))),
  'DEUTERIUM_TYPE' => colorNumber(pretty_number(floor($caps['planet']['deuterium_perhour'] * $caps['production'] + $caps['deuterium_perhour'][0]))),
  'ENERGY_TYPE'    => colorNumber(pretty_number($caps['planet']['energy_max'] - $caps['planet']['energy_used'])),
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