<?php

include('common.' . substr(strrchr(__FILE__, '.'), 1));

if(sys_get_param_str('return_fleet'))
{
  $fleet_id  = sys_get_param_int('fleet_id');
  if($fleet_id)
  {
    $FleetRow = doquery("SELECT * FROM {{fleets}} WHERE `fleet_id` = '{$fleet_id}' LIMIT 1;", '', true);

    if ($FleetRow['fleet_owner'] == $user['id'] && $FleetRow['fleet_mess'] == 0)
    {
      $ReturnFlyingTime = ($FleetRow['fleet_end_stay'] != 0 && $FleetRow['fleet_start_time'] < $time_now ? $FleetRow['fleet_start_time'] : $time_now) - $FleetRow['start_time'] + $time_now + 1;
      doquery("UPDATE {{fleets}} SET `fleet_start_time` = '{$time_now}', `fleet_group` = 0, `fleet_end_stay` = '0', `fleet_end_time` = '{$ReturnFlyingTime}', `fleet_target_owner` = '{$user['id']}', `fleet_mess` = '1' WHERE `fleet_id` = '{$fleet_id}' LIMIT 1;");

//      if($FleetRow['fleet_mission'] == MT_AKS)
      if($FleetRow['fleet_group'])
      {
        // TODO: Make here to delete only one AKS - by adding aks_fleet_count to AKS table
        doquery('DELETE FROM {{aks}} WHERE `id` NOT IN (SELECT DISTINCT `fleet_group` FROM {{fleets}});');
      }
    }
    elseif ($FleetRow['fleet_id'] && $FleetRow['fleet_owner'] != $user['id'])
    {
      $debug->warning('Trying to return fleet that not belong to user', 'Hack attempt', 302, array('base_dump' => true, 'fleet_row' => $FleetRow));
      die('Hack attempt 302');
    }
  }
}

lng_include('overview');
lng_include('fleet');

$parse = $lang;

if (!$planetrow)
{
  $parse_err['title'] = $lang['fl_error'];
  $parse_err['mes']   = $lang['fl_noplanetrow'];

  $parse['ErrorNoPlanetRow'] = parsetemplate(gettemplate('message_body'), $parse_err);
}

CheckPlanetUsedFields($planetrow);

$template = gettemplate('flying_fleets', true);

$i  = 0;
$fleet_query = doquery("SELECT * FROM {{fleets}} WHERE fleet_owner={$user['id']};");

while ($fleet_row = mysql_fetch_assoc($fleet_query))
{
  $i++;
  $fleet_data = tpl_parse_fleet_db($fleet_row, $i, $user);

  $template->assign_block_vars('fleets', $fleet_data['fleet']);

  foreach($fleet_data['ships'] as $ship_data)
  {
    $template->assign_block_vars('fleets.ships', $ship_data);
  }
}

$MaxExpeditions = GetMaxExpeditions($user);
if($MaxExpeditions)
{
  $FlyingExpeditions  = doquery("SELECT COUNT(fleet_owner) AS `expedi` FROM {{fleets}} WHERE `fleet_owner` = {$user['id']} AND `fleet_mission` = '15';", '', true);
  $FlyingExpeditions  = $FlyingExpeditions['expedi'];
}
else
{
  $FlyingExpeditions = 0;
};

$fleet_flying_amount = doquery("SELECT COUNT(fleet_id) AS `flying_fleets` FROM {{fleets}} WHERE `fleet_owner`='{$user['id']}';", '', true);

$template->assign_vars(array(
  'TIME_NOW'           => $time_now,

  'FLEETS_FLYING'      => $fleet_flying_amount['flying_fleets'],
  'FLEETS_MAX'         => GetMaxFleets($user),
  'EXPEDITIONS_FLYING' => $FlyingExpeditions,
  'EXPEDITIONS_MAX'    => $MaxExpeditions,
));

display(parsetemplate($template, $parse), $lang['fl_title']);

?>
