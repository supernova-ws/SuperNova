<?php
define('INSIDE'  , true);
define('INSTALL' , false);

$ugamela_root_path = (defined('SN_ROOT_PATH')) ? SN_ROOT_PATH : './../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include("{$ugamela_root_path}common.{$phpEx}");

if ($user['authlevel'] < 3)
{
  message( $lang['sys_noalloaw'], $lang['sys_noaccess'] );
  die();
}

$template = gettemplate('admin/planet_compensate', true);

// http://localhost/admin/t.php?g=1&s=19&p=10&g1=1&s1=2&p1=8&u=gorlum&f=1.1&c=1

if($_GET['g'])
{
  $galaxy_src = intval($_GET['g']);
  $planet_src = intval($_GET['s']);
  $system_src = intval($_GET['p']);

  $galaxy_dst = intval($_GET['g1']);
  $planet_dst = intval($_GET['s1']);
  $system_dst = intval($_GET['p1']);

  $username = mysql_real_escape_string($_GET['u']);

  $planet = doquery("SELECT * FROM {{planets}} WHERE galaxy = '{$galaxy_src}' AND system = '{$system_src}' AND planet = '{$planet_src}' AND planet_type = 1;", '', true);
  if(!$planet)
  {
    die('No src planet');
  }

  if($planet['destruyed'])
  {
    die('Planet already destroyed');
  }

  $destination = doquery("SELECT * FROM {{planets}} galaxy = '{$galaxy_dst}' AND system = '{$system_dst}' AND planet = '{$planet_dst}' AND planet_type = 1;", '', true);
  if(!$destination)
  {
    die('No dst planet');
  }

  if($planet['id_owner'] != $destination['id_owner'])
  {
    die('Planets has different owners');
  }

  $owner = doquery("SELECT * FROM {{users}} WHERE username like '{$username}'", '', true);
  if($planet['id_owner'] != $user['id'] || !$username)
  {
    die('User is not owner of planet');
  }

  if($_GET['c'])
  {
    PlanetResourceUpdate($planet, $owner, time());
    PlanetResourceUpdate($destination, $owner, time());

    killer_add_planet($planet);

    $moon = doquery("SELECT * FROM {{planets}} WHERE galaxy = '{$galaxy_src}' AND system = '{$system_src}' AND planet = '{$planet_src}' AND planet_type = 3;", '', true);
    if($moon)
    {
      PlanetResourceUpdate($moon, $owner, time());
      killer_add_planet($moon);
    }

    $factor = $_GET['f'] ? floatval($_GET['f']) : 1;
    foreach($sn_groups['resources_loot'] as $resource_id)
    {
      $final_cost[$resource_id] = floor($final_cost[$resource_id] * $factor);
    }

    pdump($final_cost, "Planet cost");
    doquery("UPDATE {{planets}} SET metal = metal + '{$final_cost['901']}', crystal = crystal + '{$final_cost['902']}', deuterium = deuterium + '{$final_cost['903']}' WHERE id = {$destination['id']};");

    $time = time() + 60*60*24*7;
    doquery("UPDATE {{planets}} SET id_owner = 0, destruyed = '{$time}' WHERE id = {$planet['id']};");
    if($moon)
    {
      doquery("UPDATE {{planets}} SET id_owner = 0, destruyed = '{$time}' WHERE id = {$moon['id']};");
    }

    die('Task complete');
  }
  else
  {
    die("All check done. <a href='?g={$galaxy_src}&s={$system_src}&p={$planet_src}&g1={$galaxy_dst}&s1={$system_dst}&p1={$planet_dst}&u={$_GET['u']}&f={$factor}&c=1'>Press here to complete process</a>");
  }
}
else
{
  display(parsetemplate($template, $parse), '', false, '', true );
}

function killer_add_planet($planet)
{
  global $sn_groups, $sn_data, $final_cost, $pricelist;

  foreach($sn_groups['build'] as $unit)
  {
    $build_level = $planet[$sn_data[$unit]['name']];
    if($build_level)
    {
      foreach($sn_groups['resources_loot'] as $resource_id)
      {
        $final_cost[$resource_id] += floor($pricelist[$unit][$sn_data[$resource_id]['name']] * pow($pricelist[$unit]['factor'], $build_level-1));
      }
    }
  }

  foreach(array_merge($sn_groups['defense'], $sn_groups['fleet']) as $unit)
  {
    $unit_count = $planet[$sn_data[$unit]['name']];
    if($unit_count)
    {
      foreach($sn_groups['resources_loot'] as $resource_id)
      {
        $final_cost[$resource_id] += floor($pricelist[$unit][$sn_data[$resource_id]['name']] * $unit_count);
      }
    }
  }

  foreach($sn_groups['resources_loot'] as $resource_id)
  {
    $final_cost[$resource_id] += $planet[$sn_data[$resource_id]['name']];
  }
}


?>
