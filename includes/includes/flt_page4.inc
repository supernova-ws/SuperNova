<?php

use Fleet\DbFleetStatic;
use Planet\DBStaticPlanet;

if(SN_IN_FLEET !== true) {
  $debug->error("Attempt to call FLEET page mode {$mode} directly - not from fleet.php", 'Forbidden', 403);
}

$fleetid = sys_get_param_id('fleetid');

if(!is_numeric($fleetid) || empty($fleetid)) {
  sys_redirect("fleet.php");
}

$fleet = DbFleetStatic::db_fleet_get($fleetid);
if(!$fleet) {
  SnTemplate::messageBox($lang['fl_fleet_not_exists'], $lang['fl_error']);
}

if ($fleet['fleet_start_time'] <= SN_TIME_NOW || $fleet['fleet_end_time'] < SN_TIME_NOW || $fleet['fleet_mess'] == 1) {
  SnTemplate::messageBox($lang['fl_isback'], $lang['fl_error']);
}

if($fleet['fleet_owner'] != $user['id']) {
  $debug->warning($lang['fl_aks_hack_wrong_fleet'],'Wrong Fleet Owner',301);
  SnTemplate::messageBox($lang['fl_aks_hack_wrong_fleet'], $lang['fl_error']);
}

// If we got a message to add some1 to attack (MadnessRed code)
$userNameToAdd_unsafe = sys_get_param_str_unsafe('addtogroup');

if (!empty($userNameToAdd_unsafe)) {
  try {
    $userToAddRecord = db_user_by_username($userNameToAdd_unsafe, true);

    $aks = DbFleetStatic::acsAddUser($user, $fleet, $userToAddRecord);
    $fleet['fleet_group']   = $aks['id'];
    $fleet['fleet_mission'] = MT_AKS;

    msg_send_simple_message($userToAddRecord['id'], $user['id'], SN_TIME_NOW, MSG_TYPE_COMBAT, $user['username'],
      SN::$lang['fl_aks_invite_message_header'], sprintf(SN::$lang['fl_aks_invite_message'], $user['username']));

    SnTemplate::tplAddResult(sprintf($lang['fl_aks_player_invited'], $userNameToAdd_unsafe), ERR_NONE);
  } catch (Exception $e) {
    SnTemplate::tplAddResult(sprintf($e->getMessage(), $userNameToAdd_unsafe), ERR_ERROR);
  }
}

$fleet = DbFleetStatic::db_fleet_get($fleetid);
$aks = DbFleetStatic::dbAcsGetByFleet($fleetid);

$template = SnTemplate::gettemplate('fleet_aks_invite', true);
$template->assign_vars(array(
  'FLEET_ID' => $fleetid,
  'PAGE_HEADER' => "{$lang['fl_associate']} '{$aks['name']}'",
  'MISSION_NAME' => $lang['type_mission'][MT_AKS],
));

if($aks['eingeladen'] && is_array($members = SN::db_get_record_list(LOC_USER, "`id` in ({$aks['eingeladen']})")) && !empty($members)) {
  foreach($members as $row) {
    $template->assign_block_vars('invited', array(
      'NAME' => $row['username'],
    ));
  }
}

$i++;
$fleet_row = DbFleetStatic::db_fleet_get($fleetid);

if(is_array($fleet_row) && !empty($fleet_row)) {
$planet_start = DBStaticPlanet::db_planet_by_id($fleet_row['fleet_start_planet_id']);
$fleet_row['fleet_start_name'] = !empty($planet_start['name']) ? $planet_start['name'] : '';
$planet_end = DBStaticPlanet::db_planet_by_id($fleet_row['fleet_end_planet_id']);
$fleet_row['fleet_end_name'] = !empty($planet_end['name']) ? $planet_end['name'] : '';

$fleet_data = tpl_parse_fleet_db($fleet_row, $i, $user);
$template->assign_block_vars('fleets', $fleet_data['fleet']);
foreach($fleet_data['ships'] as $ship_data) {
  $template->assign_block_vars('fleets.ships', $ship_data);
}
}

SnTemplate::display($template, $lang['fl_title']);
