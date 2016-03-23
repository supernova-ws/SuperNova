<?php

if(SN_IN_FLEET !== true) {
  $debug->error("Attempt to call FLEET page mode {$mode} directly - not from fleet.php", 'Forbidden', 403);
}

$fleetid = sys_get_param_id('fleetid');

if(!is_numeric($fleetid) || empty($fleetid)) {
  header("Location: fleet.php");
  exit();
}

$objFleet = new Fleet();
$objFleet->dbLoad($fleetid);
if(!$objFleet->dbId) {
  message($lang['fl_fleet_not_exists'], $lang['fl_error']);
}

if ($objFleet->time_arrive_to_target <= SN_TIME_NOW || $objFleet->time_return_to_source < SN_TIME_NOW || $objFleet->is_returning == 1) {
  message($lang['fl_isback'], $lang['fl_error']);
}

if($objFleet->playerOwnerId != $user['id']) {
  $debug->warning($lang['fl_aks_hack_wrong_fleet'],'Wrong Fleet Owner',301);
  message($lang['fl_aks_hack_wrong_fleet'], $lang['fl_error']);
}

$aks = db_acs_get_by_fleet($fleetid);
// If we got a message to add some1 to attack (MadnessRed code)
$userToAdd_unsafe = sys_get_param_str_unsafe('addtogroup');

!$aks && !$userToAdd_unsafe ? $userToAdd_unsafe = $user['username'] : false;

if($userToAdd_unsafe) {
  $userToAdd = db_escape($userToAdd_unsafe);
  $userToAddID = db_user_by_username($userToAdd_unsafe, false, 'id', true, true);
  $userToAddID = $userToAddID['id'];

  if($objFleet->target_owner_id == $userToAddID) {
    message($lang['flt_aks_player_same'], $lang['fl_error']);
  }

  if($userToAddID) {
    if(!$aks) {
      // No AСS exists - making one
      if(!$objFleet->group_id) {
        db_acs_insert($lang, $fleetid, $user, $objFleet);

        $aks = db_acs_get_by_fleet($fleetid);

        $objFleet->group_acs_set($aks['id'], MT_AKS);
        $objFleet->dbSave();
      } else {
        message($lang['fl_aks_already_in_aks'], $lang['fl_error']);
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
      message($lang['flt_aks_error_too_much_players'], $lang['fl_error']);
    }

    if($isUserExists) {
      $userToAdd_unsafe != $user['username'] ? $add_user_message_mr = sprintf($lang['fl_aks_player_invited_already'], $userToAdd) : false;
    } else {
      $add_user_message_mr = sprintf($lang['fl_aks_player_invited'], $userToAdd);
      db_acs_update($userToAddID, $fleetid)
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

if($aks['eingeladen'] && is_array($members = classSupernova::db_get_record_list(LOC_USER, "`id` in ({$aks['eingeladen']})")) && !empty($members)) {
  foreach($members as $row) {
    $template->assign_block_vars('invited', array(
      'NAME' => $row['username'],
    ));
  }
}

$i++;

//$fleet_row = Fleet::static_db_fleet_get($fleetid);
//$planet_start = db_planet_by_id($fleet_row['fleet_start_planet_id']);
//$fleet_row['fleet_start_name'] = !empty($planet_start['name']) ? $planet_start['name'] : '';
//$planet_end = db_planet_by_id($fleet_row['fleet_end_planet_id']);
//$fleet_row['fleet_end_name'] = !empty($planet_end['name']) ? $planet_end['name'] : '';
//$fleet_data = tpl_parse_fleet_db($fleet_row, $i, $user);

$objFleet = new Fleet();
$objFleet->dbLoad($fleetid);
$planet_start = db_planet_by_id($objFleet->fleet_start_planet_id);
$objFleet->fleet_start_name = !empty($planet_start['name']) ? $planet_start['name'] : '';
$planet_end = db_planet_by_id($objFleet->fleet_end_planet_id);
$objFleet->fleet_end_name = !empty($planet_end['name']) ? $planet_end['name'] : '';
$fleet_data = tplParseFleetObject($objFleet, $i, $user);

$template->assign_block_vars('fleets', $fleet_data['fleet']);
foreach($fleet_data['ships'] as $ship_data) {
  $template->assign_block_vars('fleets.ships', $ship_data);
}

display($template, $lang['fl_title']);
