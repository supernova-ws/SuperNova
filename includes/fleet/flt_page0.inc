<?php
// fleet.php
// @version 2.1 - Rebuilt to use Slider element from jUI. Also shows preliminary info about fleet: speed, consumption etc
// @version 2.0 - Serious optimizations: remove redundant fields etc Also makes compliant with PCG
// @version 1.0
// @copyright 2008 by Chlorel for XNova

includeLang('overview');

if (!$planetrow)
{
  $parse_err['title'] = $lang['fl_error'];
  $parse_err['mes']   = $lang['fl_noplanetrow'];

  $parse['ErrorNoPlanetRow'] = parsetemplate(gettemplate('message_body'), $parse_err);
}

CheckPlanetUsedFields($planetrow);

$template = gettemplate('fleet0', true);

$i  = 0;
$fq = doquery("SELECT * FROM {{fleets}} WHERE fleet_owner={$user['id']}");

while ($f = mysql_fetch_array($fq))
{
  $i++;
  $fleet_data = tpl_parse_fleet($f, $i, $user);
  $template->assign_block_vars('fleets', $fleet_data['fleet']);

  foreach($fleet_data['ships'] as $ship_data)
  {
    $template->assign_block_vars('fleets.ships', $ship_data);
  }
}

foreach ($reslist['fleet'] as $n => $i)
{
  if ($planetrow[$resource[$i]] > 0)
  {
    $template->assign_block_vars('ships', array(
      'ID'          => $i,
      'NAME'        => $lang['tech'][$i],
		'AMOUNT'      => $planetrow[$resource[$i]],
      'CONSUMPTION' => GetShipConsumption ( $i, $user ),
      'SPEED'       => get_ship_speed($i, $user),
      'CAPACITY'    => $pricelist[$i]['capacity'],
    ));
  }
}

$template->assign_vars(array(
  'TIME_NOW'           => $time_now,

  'FLYING_FLEETS'      => $FlyingFleets,
  'MAX_FLEETS'         => $MaxFleets,
  'ExpeditionEnCours'  => $FlyingExpeditions,
  'EnvoiMaxExpedition' => $MaxExpeditions,
  'target_mission'     => $target_mission,
  'MISSION_NAME'		  => $lang['type_mission'][$target_mission],
  'TYPE_NAME'		     => $lang['fl_planettype'][$planet_type],

  'ShipList'           => $ShipList,

  'speed_factor'       => $SpeedFactor,

  'PLANET_RESOURCES'   => pretty_number(floor($planetrow['metal'] + $planetrow['crystal'] + $planetrow['deuterium'])),
));

display(parsetemplate($template, $parse), $lang['fl_title']);
?>