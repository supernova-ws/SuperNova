<?php

$ugamela_root_path = (defined('SN_ROOT_PATH')) ? SN_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include("{$ugamela_root_path}common.{$phpEx}");

if ($IsUserChecked == false) {
  includeLang('login');
  header("Location: login.php");
}

if(sys_get_param_str('return_fleet'))
{
  $fleet_id  = sys_get_param_int('fleet_id');
  if($fleet_id)
  {
    $FleetRow = doquery("SELECT * FROM {{fleets}} WHERE `fleet_id` = '{$fleet_id}';", '', true);

    if ($FleetRow['fleet_owner'] == $user['id'] && $FleetRow['fleet_mess'] == 0)
    {
/*
      if ($FleetRow['fleet_end_stay'] != 0)
      {
        if ($FleetRow['fleet_start_time'] <= $time_now)
        {
          $CurrentFlyingTime = $FleetRow['fleet_start_time'] - $FleetRow['start_time'];
        }
        else
        {
          $CurrentFlyingTime = $time_now - $FleetRow['start_time'];
        }
      }
      else
      {
        $CurrentFlyingTime = $time_now - $FleetRow['start_time'];
      }
*/
      $ReturnFlyingTime  = ($FleetRow['fleet_end_stay'] != 0 && $FleetRow['fleet_start_time'] < $time_now ? $FleetRow['fleet_start_time'] : $time_now) - $FleetRow['start_time'] + $time_now + 1;

      $QryUpdateFleet  = "UPDATE {{fleets}} SET `fleet_start_time` = '{$time_now}', `fleet_end_stay` = '0', ";
      $QryUpdateFleet .= "`fleet_end_time` = '{$ReturnFlyingTime}', `fleet_target_owner` = '{$user['id']}', `fleet_mess` = '1' ";
      $QryUpdateFleet .= "WHERE `fleet_id` = '{$fleet_id}' LIMIT 1;";
      doquery($QryUpdateFleet);
    }
    else
    {
      $debug->warning('Trying to return fleet that not belong to user', 'Hack attempt', 302, array('base_dump' => true));
      die();
    }
  }
}

includeLang('overview');
includeLang('fleet');

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


if($MaxExpeditions){
  $FlyingExpeditions  = doquery("SELECT COUNT(fleet_owner) AS `expedi` FROM {{fleets}} WHERE `fleet_owner` = {$user['id']} AND `fleet_mission` = '15';", '', true);
  $FlyingExpeditions  = $FlyingExpeditions['expedi'];
}else{
  $FlyingExpeditions = 0;
};

$fleet_flying_amount = doquery("SELECT COUNT(fleet_id) AS `flying_fleets` FROM {{fleets}} WHERE `fleet_owner`='{$user['id']}';", '', true);

$template->assign_vars(array(
  'TIME_NOW'           => $time_now,

  'FLEETS_FLYING'      => $fleet_flying_amount['flying_fleets'],
  'FLEETS_MAX'         => GetMaxFleets($user),
  'EXPEDITIONS_FLYING' => $FlyingExpeditions,
  'EXPEDITIONS_MAX'    => GetMaxExpeditions($user),
));

display(parsetemplate($template, $parse), $lang['fl_title']);

?>
