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
  messageBox($lang['fl_fleet_not_exists'], $lang['fl_error']);
}
//$query = doquery("SELECT * FROM `{{fleets}}` WHERE fleet_id = '{$fleetid}'");
//if(db_num_rows($query) != 1) {
//  messageBox($lang['fl_fleet_not_exists'], $lang['fl_error']);
//}
//
//$fleet = db_fetch($query);

if ($fleet['fleet_start_time'] <= SN_TIME_NOW || $fleet['fleet_end_time'] < SN_TIME_NOW || $fleet['fleet_mess'] == 1) {
  messageBox($lang['fl_isback'], $lang['fl_error']);
}

if($fleet['fleet_owner'] != $user['id']) {
  $debug->warning($lang['fl_aks_hack_wrong_fleet'],'Wrong Fleet Owner',301);
  messageBox($lang['fl_aks_hack_wrong_fleet'], $lang['fl_error']);
}

$aks = doquery("SELECT * from `{{aks}}` WHERE `flotten` = {$fleetid} LIMIT 1;", '', true);
// If we got a message to add some1 to attack (MadnessRed code)
$userToAdd_unsafe = sys_get_param_str_unsafe('addtogroup');

!$aks && !$userToAdd_unsafe ? $userToAdd_unsafe = $user['username'] : false;

if($userToAdd_unsafe) {
  $userToAdd = db_escape($userToAdd_unsafe);
  $userToAddID = db_user_by_username($userToAdd_unsafe, true);
  $userToAddID = $userToAddID['id'];

  if($fleet['fleet_target_owner'] == $userToAddID) {
    messageBox($lang['flt_aks_player_same'], $lang['fl_error']);
  }

  if($userToAddID) {
    if(!$aks) {
      // No AСS exists - making one
      if(!$fleet['fleet_group']) {
        doquery("INSERT INTO {{aks}} SET
          `name` = '" . db_escape($lang['flt_acs_prefix'] . $fleetid) . "',
          `teilnehmer` = '" . $user['id'] . "',
          `flotten` = '" . $fleetid . "',
          `ankunft` = '" . $fleet['fleet_start_time'] . "',
          `galaxy` = '" . $fleet['fleet_end_galaxy'] . "',
          `system` = '" . $fleet['fleet_end_system'] . "',
          `planet` = '" . $fleet['fleet_end_planet'] . "',
          `planet_type` = '" . $fleet['fleet_end_type'] . "',
          `eingeladen` = '" . $user['id'] . "',
          `fleet_end_time` = '" . $fleet['fleet_end_time']. "'");

        $aks = doquery("SELECT * FROM {{aks}} WHERE `flotten` = {$fleetid} LIMIT 1;", true);

//        doquery("UPDATE {{fleets}} SET fleet_group = '{$aks['id']}', fleet_mission = '" . MT_AKS . "' WHERE fleet_id = '{$fleetid}'");
        DbFleetStatic::fleet_update_set($fleetid, array(
          'fleet_group' => $aks['id'],
          'fleet_mission' => MT_AKS,
        ));
        $fleet['fleet_group'] = $aks['id'];
        $fleet['fleet_mission'] = MT_AKS;
      } else {
        messageBox($lang['fl_aks_already_in_aks'], $lang['fl_error']);
      }
    }

    $isUserExists = false;
    $invited_ar = explode(",", $aks['eingeladen']);
    foreach($invited_ar as $inv) {
      if ($userToAddID == $inv) {
        $isUserExists = true;
      }
    }

    if(count($invited_ar) >= 5) {
      messageBox($lang['flt_aks_error_too_much_players'], $lang['fl_error']);
    }

    if($isUserExists) {
      $userToAdd_unsafe != $user['username'] ? $add_user_message_mr = sprintf($lang['fl_aks_player_invited_already'], $userToAdd) : false;
    } else {
      $add_user_message_mr = sprintf($lang['fl_aks_player_invited'], $userToAdd);
      doquery("UPDATE `{{aks}}` SET `eingeladen` = concat(`eingeladen`, ',{$userToAddID}') WHERE `flotten` = {$fleetid};")
        or die(sprintf($lang['fl_aks_adding_error'],db_error()));
      $aks['eingeladen'] .= ',' . $userToAddID;
    }
    msg_send_simple_message ( $userToAddID, $user['id'], SN_TIME_NOW, MSG_TYPE_COMBAT, $user['username'],
      $lang['fl_aks_invite_message_header'], sprintf( $lang['fl_aks_invite_message'], $user['username']));
  } else {
    $add_user_message_mr = sprintf($lang['fl_aks_player_error'], $userToAdd);
  }
}

$template = gettemplate('fleet_aks_invite', true);
$template->assign_vars(array(
  'FLEET_ID' => $fleetid,
  'MESSAGE' => $add_user_message_mr,
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

display($template, $lang['fl_title']);
