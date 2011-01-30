<?php
  // verband.php
  // @version 1.0
  // @copyright 2008 by ??????? for XNova

  $fleetid = intval($_POST['fleetid']);
  $userToAdd = mysql_real_escape_string($_POST['addtogroup']);

  if (!is_numeric($fleetid) || empty($fleetid)) {
    header("Location: fleet.php");
    exit();
  }

  $query = doquery("SELECT * FROM `{{table}}` WHERE fleet_id = '{$fleetid}'", 'fleets');
  if (mysql_num_rows($query) != 1) {
    message($lang['fl_fleet_not_exists'], $lang['fl_error']);
  }

  $fleet = mysql_fetch_assoc($query);
  if ($fleet['fleet_start_time'] <= $time_now || $fleet['fleet_end_time'] < $time_now || $fleet['fleet_mess'] == 1) {
    message($lang['fl_isback'], $lang['fl_error']);
  }

  if ($fleet['fleet_owner'] != $user['id']) {
    $debug->warning($lang['fl_aks_hack_wrong_fleet'],'Wrong Fleet Owner',301);
    message($lang['fl_aks_hack_wrong_fleet'], $lang['fl_error']);
  }

  $aks = doquery("SELECT * from `{{table}}` WHERE `flotten` = {$fleetid};", 'aks', true);
  // If we got a message to add some1 to attack (MadnessRed code)
  if($userToAdd){
    $userToAddID = doquery("SELECT `id` FROM `{{table}}` WHERE `username` like '{$userToAdd}';",'users',true);
    $userToAddID = $userToAddID['id'];

    if($userToAddID){
      if (!$aks){
        // No AKS exists - making one
        // SetSelectedPlanet ( $user );
        // CheckPlanetUsedFields($planetrow);

        if (!$fleet['fleet_group']) {
          doquery("INSERT INTO {{table}} SET
            `name` = 'KV{$fleetid}',
            `teilnehmer` = '" . $user['id'] . "',
            `flotten` = '" . $fleetid . "',
            `ankunft` = '" . $fleet['fleet_start_time'] . "',
            `galaxy` = '" . $fleet['fleet_end_galaxy'] . "',
            `system` = '" . $fleet['fleet_end_system'] . "',
            `planet` = '" . $fleet['fleet_end_planet'] . "',
            `planet_type` = '" . $fleet['fleet_end_type'] . "',
            `eingeladen` = '" . $user['id'] . "',
            `fleet_end_time` = '" . $fleet['fleet_end_time']. "'",'aks');

          $aks = doquery("SELECT * FROM {{table}} WHERE `flotten` = {$fleetid};", 'aks', true);

          doquery("UPDATE {{table}} SET fleet_group = '{$aks['id']}', fleet_mission = '" . MT_AKS . "' WHERE fleet_id = '{$fleetid}'", 'fleets');
          $fleet['fleet_group'] = $aks['id'];
        }else{
          message($lang['fl_aks_already_in_aks'],$lang['fl_error']);
        };
      };

      $isUserExists = false;
      $invited_ar = explode(",", $aks['eingeladen']);
      foreach($invited_ar as $inv){
        if ($userToAddID == $inv)
          $isUserExists = true;
      };

      if(count($invited_ar)>=5){
        message('Нельзя приглашать больше 5 человек','Ошибка');
      };

      if ($isUserExists) {
        $add_user_message_mr = sprintf($lang['fl_aks_player_invited_already'], $userToAdd);
      }else{
        print('adding user');
        $add_user_message_mr = sprintf($lang['fl_aks_player_invited'], $userToAdd);
        doquery("UPDATE `{{table}}` SET `eingeladen` = concat(`eingeladen`, ',{$userToAddID}') WHERE `flotten` = {$fleetid};",'aks')
          or die(sprintf($lang['fl_aks_adding_error'],mysql_error()));
        $aks['eingeladen'] .= ',' . $userToAddID;
      };
      SendSimpleMessage ( $userToAddID, $user['id'], $time_now, 1, $user['username'],
        $lang['fl_aks_invite_message_header'], sprintf( $lang['fl_aks_invite_message'], $user['username']));
    }else{
      $add_user_message_mr = sprintf($lang['fl_aks_player_error'], $userToAdd);
    }
  }

  $parse['fleetid'] = $fleetid;
  $parse['add_user_message_mr'] = $add_user_message_mr;

  $members = explode(",", $aks['eingeladen']);
  foreach($members as $a => $b) {
    if ($b != '') {
      $member_qry_mr = doquery("SELECT `username` FROM `{{table}}` WHERE `id` ='{$b}' ;",'users');
      while($row = mysql_fetch_assoc($member_qry_mr)){
        $invited .= "<option>".$row['username']."</option>";
      }
    }
  }
  $parse['members'] = $invited;

  $page = parsetemplate(gettemplate('fleet_aks_invite'), $parse);

  display($page, $lang['fl_title']);
?>