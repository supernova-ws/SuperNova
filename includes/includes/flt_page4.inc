<?php
// verband.php
// @version 1.0
// @copyright 2008 by ??????? for XNova

if(SN_IN_FLEET !== true)
{
  $debug->error("Attempt to call FLEET page mode {$mode} directly - not from fleet.php", 'Forbidden', 403);
}

$fleetid = sys_get_param_id('fleetid');

if(!is_numeric($fleetid) || empty($fleetid))
{
  header("Location: fleet.php");
  exit();
}

$query = doquery("SELECT * FROM `{{fleets}}` WHERE fleet_id = '{$fleetid}'");
if(db_num_rows($query) != 1)
{
  message($lang['fl_fleet_not_exists'], $lang['fl_error']);
}

$fleet = db_fetch($query);
if ($fleet['fleet_start_time'] <= $time_now || $fleet['fleet_end_time'] < $time_now || $fleet['fleet_mess'] == 1)
{
  message($lang['fl_isback'], $lang['fl_error']);
}

if($fleet['fleet_owner'] != $user['id'])
{
  $debug->warning($lang['fl_aks_hack_wrong_fleet'],'Wrong Fleet Owner',301);
  message($lang['fl_aks_hack_wrong_fleet'], $lang['fl_error']);
}

$aks = doquery("SELECT * from `{{aks}}` WHERE `flotten` = {$fleetid} LIMIT 1;", '', true);
// If we got a message to add some1 to attack (MadnessRed code)
$userToAdd_unsafe = sys_get_param_str_unsafe('addtogroup');
if($userToAdd_unsafe)
{
  $userToAdd = db_escape($userToAdd_unsafe);
  $userToAddID = db_user_by_username($userToAdd_unsafe, false, 'id', true, true);
  $userToAddID = $userToAddID['id'];

  if($fleet['fleet_target_owner'] == $userToAddID)
  {
    message($lang['flt_aks_player_same'], $lang['fl_error']);
  }

  if($userToAddID)
  {
    if(!$aks)
    {
      // No AKS exists - making one
      // SetSelectedPlanet ( $user );
      // CheckPlanetUsedFields($planetrow);

      if(!$fleet['fleet_group'])
      {
        doquery("INSERT INTO {{aks}} SET
          `name` = 'KV{$fleetid}',
          `teilnehmer` = '" . $user['id'] . "',
          `flotten` = '" . $fleetid . "',
          `ankunft` = '" . $fleet['fleet_start_time'] . "',
          `galaxy` = '" . $fleet['fleet_end_galaxy'] . "',
          `system` = '" . $fleet['fleet_end_system'] . "',
          `planet` = '" . $fleet['fleet_end_planet'] . "',
          `planet_type` = '" . $fleet['fleet_end_type'] . "',
          `eingeladen` = '" . $user['id'] . "',
          `fleet_end_time` = '" . $fleet['fleet_end_time']. "'");

        $aks = doquery("SELECT * FROM {{aks}} WHERE `flotten` = {$fleetid} LIMIT 1;", '', true);

        doquery("UPDATE {{fleets}} SET fleet_group = '{$aks['id']}', fleet_mission = '" . MT_AKS . "' WHERE fleet_id = '{$fleetid}'");
        $fleet['fleet_group'] = $aks['id'];
      }
      else
      {
        message($lang['fl_aks_already_in_aks'], $lang['fl_error']);
      }
    }

    $isUserExists = false;
    $invited_ar = explode(",", $aks['eingeladen']);
    foreach($invited_ar as $inv)
    {
      if ($userToAddID == $inv)
      {
        $isUserExists = true;
      }
    };

    if(count($invited_ar)>=5)
    {
      message($lang['flt_aks_error_too_much_players'], $lang['fl_error']);
    }

    if($isUserExists)
    {
      $add_user_message_mr = sprintf($lang['fl_aks_player_invited_already'], $userToAdd);
    }
    else
    {
      $add_user_message_mr = sprintf($lang['fl_aks_player_invited'], $userToAdd);
      doquery("UPDATE `{{aks}}` SET `eingeladen` = concat(`eingeladen`, ',{$userToAddID}') WHERE `flotten` = {$fleetid};")
        or die(sprintf($lang['fl_aks_adding_error'],db_error()));
      $aks['eingeladen'] .= ',' . $userToAddID;
    }
    msg_send_simple_message ( $userToAddID, $user['id'], $time_now, MSG_TYPE_COMBAT, $user['username'],
      $lang['fl_aks_invite_message_header'], sprintf( $lang['fl_aks_invite_message'], $user['username']));
  }
  else
  {
    $add_user_message_mr = sprintf($lang['fl_aks_player_error'], $userToAdd);
  }
}

$template = gettemplate('fleet_aks_invite', true);
$template->assign_vars(array(
  'FLEET_ID' => $fleetid,
  'MESSAGE' => $add_user_message_mr,
));

if($aks['eingeladen'] && is_array($members = classSupernova::db_get_record_list(LOC_USER, "`id` in ({$aks['eingeladen']})")) && !empty($members))
{
  foreach($members as $row)
  {
    $template->assign_block_vars('invited', array(
      'NAME' => $row['username'],
    ));
  }
}

display($template, $lang['fl_title']);
