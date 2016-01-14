<?php
/**
 * Created by PhpStorm.
 * User: Gorlum
 * Date: 17.04.2015
 * Time: 6:37
 */

function sys_user_vacation($user) {
  global $config;

  if(sys_get_param_str('vacation') == 'leave') {
    if ($user['vacation'] < SN_TIME_NOW) {
      $user['vacation'] = 0;
      $user['vacation_next'] = SN_TIME_NOW + $config->player_vacation_timeout;
      db_user_set_by_id($user['id'], "`vacation` = {$user['vacation']}, `vacation_next` = {$user['vacation_next']}");
    }
  }

  if($user['vacation']) {
    // sn_sys_logout(false, true);
    // core_auth::logout(false, true);

    $template = gettemplate('vacation', true);

    $template->assign_vars(array(
      'NAME' => $user['username'],
      'VACATION_END' => date(FMT_DATE_TIME, $user['vacation']),
      'CAN_LEAVE' => $user['vacation'] <= SN_TIME_NOW,
      'RANDOM' => mt_rand(1, 2),
    ));

    display(parsetemplate($template), '', false, '', false, false);
  }

  return false;
}

function sys_is_multiaccount($user1, $user2) {
  global $config;

  return $user1['user_lastip'] == $user2['user_lastip'] && !$config->game_multiaccount_enabled;
}

/**
 * @param $UserID
 */
function DeleteSelectedUser($UserID) {
  // TODO: Full rewrite
  sn_db_transaction_start();
  $TheUser = db_user_by_id($UserID);
  if ( $TheUser['ally_id'] != 0 ) {
    $TheAlly = doquery ( "SELECT * FROM `{{alliance}}` WHERE `id` = '" . $TheUser['ally_id'] . "';", '', true );
    $TheAlly['ally_members'] -= 1;
    if ( $TheAlly['ally_members'] > 0 ) {
      doquery ( "UPDATE `{{alliance}}` SET `ally_members` = '" . $TheAlly['ally_members'] . "' WHERE `id` = '" . $TheAlly['id'] . "';");
    } else {
      doquery ( "DELETE FROM `{{alliance}}` WHERE `id` = '" . $TheAlly['id'] . "';");
      doquery ( "DELETE FROM `{{statpoints}}` WHERE `stat_type` = '2' AND `id_owner` = '" . $TheAlly['id'] . "';");
    }
  }
  doquery ( "DELETE FROM `{{statpoints}}` WHERE `stat_type` = '1' AND `id_owner` = '" . $UserID . "';");

  db_planet_list_delete_by_owner($UserID);

  doquery ( "DELETE FROM `{{messages}}` WHERE `message_sender` = '" . $UserID . "';");
  doquery ( "DELETE FROM `{{messages}}` WHERE `message_owner` = '" . $UserID . "';");
  doquery ( "DELETE FROM `{{notes}}` WHERE `owner` = '" . $UserID . "';");
  db_fleet_list_delete_by_owner($UserID);
//  doquery ( "DELETE FROM `{{rw}}` WHERE `id_owner1` = '" . $UserID . "';");
//  doquery ( "DELETE FROM `{{rw}}` WHERE `id_owner2` = '" . $UserID . "';");
  doquery ( "DELETE FROM `{{buddy}}` WHERE `BUDDY_SENDER_ID` = '" . $UserID . "';");
  doquery ( "DELETE FROM `{{buddy}}` WHERE `BUDDY_OWNER_ID` = '" . $UserID . "';");
  doquery ( "DELETE FROM `{{annonce}}` WHERE `user` = '" . $UserID . "';");


  classSupernova::db_del_record_by_id(LOC_USER, $UserID);
  doquery ( "DELETE FROM `{{referrals}}` WHERE (`id` = '{$UserID}') OR (`id_partner` = '{$UserID}');");
  global $config;
  $config->db_saveItem('users_amount', $config->db_loadItem('users_amount') - 1);
  sn_db_transaction_commit();
}

/**
 * @param        $banner
 * @param        $banned
 * @param        $term
 * @param bool   $is_vacation
 * @param string $reason
 */
function sys_admin_player_ban($banner, $banned, $term, $is_vacation = true, $reason = '') {
  $ban_current = db_user_by_id($banned['id'], false, 'banaday');
  $ban_until = ($ban_current['banaday'] ? $ban_current['banaday'] : SN_TIME_NOW) + $term;

  db_user_set_by_id($banned['id'], "`banaday` = {$ban_until} " . ($is_vacation ? ", `vacation` = '{$ban_until}' " : ''));

  $banned['username'] = db_escape($banned['username']);
  $banner['username'] = db_escape($banner['username']);
  doquery(
    "INSERT INTO
      {{banned}}
    SET
      `ban_user_id` = '{$banned['id']}',
      `ban_user_name` = '{$banned['username']}',
      `ban_reason` = '{$reason}',
      `ban_time` = " . SN_TIME_NOW . ",
      `ban_until` = {$ban_until},
      `ban_issuer_id` = '{$banner['id']}',
      `ban_issuer_name` = '{$banner['username']}',
      `ban_issuer_email` = '{$banner['email']}'
  ");

  db_planet_set_by_owner($banned['id'],
    "`metal_mine_porcent` = 0, `crystal_mine_porcent` = 0, `deuterium_sintetizer_porcent` = 0, `solar_plant_porcent` = 0,
    `fusion_plant_porcent` = 0, `solar_satelit_porcent` = 0, `ship_sattelite_sloth_porcent` = 0"
  );
}

/**
 * @param        $banner
 * @param        $banned
 * @param string $reason
 */
function sys_admin_player_ban_unset($banner, $banned, $reason = '') {
  db_user_set_by_id($banned['id'], "`banaday` = 0, `vacation` = " . SN_TIME_NOW . "");

  $banned['username'] = db_escape($banned['username']);
  $banner['username'] = db_escape($banner['username']);
  $reason = db_escape($reason);
  doquery(
    "INSERT INTO {{banned}}
    SET
      `ban_user_id` = '{$banned['id']}',
      `ban_user_name` = '{$banned['username']}',
      `ban_reason` = '{$reason}',
      `ban_time` = 0,
      `ban_until` = " . SN_TIME_NOW . ",
      `ban_issuer_id` = '{$banner['id']}',
      `ban_issuer_name` = '{$banner['username']}',
      `ban_issuer_email` = '{$banner['email']}'
  ");
}

function player_create($username_unsafe, $email_unsafe, $options) {
  sn_db_transaction_check(true);

  global $config, $lang;

  static $player_options_string = 'opt_mnl_spy^1|opt_email_mnl_spy^0|opt_email_mnl_joueur^0|opt_email_mnl_alliance^0|opt_mnl_attaque^1|opt_email_mnl_attaque^0|opt_mnl_exploit^1|opt_email_mnl_exploit^0|opt_mnl_transport^1|opt_email_mnl_transport^0|opt_email_msg_admin^1|opt_mnl_expedition^1|opt_email_mnl_expedition^0|opt_mnl_buildlist^1|opt_email_mnl_buildlist^0|opt_int_navbar_resource_force^1|';

  empty($options['planet_options']) ? $options['planet_options'] = array() : false;

  $field_set = array(
    'server_name' => SN_ROOT_VIRTUAL,
    'register_time' => SN_TIME_NOW,
    'user_bot' => $options['user_bot'] = empty($options['user_bot']) ? USER_BOT_PLAYER : $options['total_points'],

    'username' => $username_unsafe,
    'email' => $email_unsafe,
    'email_2' => $email_unsafe,

    'lang' => $options['language_iso'] ? $options['language_iso'] : DEFAULT_LANG,
    'dpath' => DEFAULT_SKINPATH,

    'total_points' => $options['total_points'] = empty($options['total_points']) ? 0 : $options['total_points'],

    'options' => (empty($options['options']) ? $player_options_string : $options['options']) . (empty($options['options_extra']) ? '' : $options['options_extra']),

    'galaxy' => $options['galaxy'] = intval($options['galaxy'] ? $options['galaxy'] : 0),
    'system' => $options['system'] = intval($options['system'] ? $options['system'] : 0),
    'planet' => $options['planet'] = intval($options['planet'] ? $options['planet'] : 0),
  );

  !empty($options['salt']) ? $field_set['salt'] = $options['salt'] : false;
  !empty($options['password_encoded_unsafe']) ? $field_set['password'] = $options['password_encoded_unsafe'] : false;

  $user_new = classSupernova::db_ins_field_set(LOC_USER, $field_set);
  if(!($options['galaxy'] && $options['system'] && $options['planet'])) {
    $options['galaxy'] = $config->LastSettedGalaxyPos;
    $options['system'] = $config->LastSettedSystemPos;
    $segment_size = floor($config->game_maxPlanet / 3);
    $segment = floor($config->LastSettedPlanetPos / $segment_size);
    $segment++;
    $options['planet'] = mt_rand(1 + $segment * $segment_size, ($segment + 1) * $segment_size);

    // $new_planet_id = 0;
    while(true) {
      if($options['planet'] > $config->game_maxPlanet) {
        $options['planet'] = mt_rand(0, $segment_size - 1) + 1;
        $options['system']++;
      }
      if($options['system'] > $config->game_maxSystem) {
        $options['system'] = 1;
        $options['galaxy']++;
      }
      $options['galaxy'] > $config->game_maxGalaxy ? $options['galaxy'] = 1 : false;

      $galaxy_row = db_planet_by_gspt($options['galaxy'], $options['system'], $options['planet'], PT_PLANET, true, 'id');
      if(!$galaxy_row['id']) {
        $config->db_saveItem(array(
          'LastSettedGalaxyPos' => $options['galaxy'],
          'LastSettedSystemPos' => $options['system'],
          'LastSettedPlanetPos' => $options['planet'],
        ));
        // $new_planet_id = uni_create_planet($options['galaxy'], $options['system'], $options['planet'], $user_new['id'], $username_unsafe . ' ' . $lang['sys_capital'], true, $options['planet_options']);
        break;
      }
      $options['planet'] += 3;
    }
  }
  $new_planet_id = uni_create_planet($options['galaxy'], $options['system'], $options['planet'], $user_new['id'], $lang['sys_capital'], true, $options['planet_options']);

//  db_user_set_by_id($user_new['id'],
//    "`id_planet` = '{$new_planet_id}', `current_planet` = '{$new_planet_id}',
//    `galaxy` = '{$options['galaxy']}', `system` = '{$options['$system']}', `planet` = '{$options['$planet']}',
//    `parent_account_id` = {$account['account_id']}"
//  );
  db_user_set_by_id($user_new['id'],
    "`id_planet` = '{$new_planet_id}', `current_planet` = '{$new_planet_id}',
    `galaxy` = '{$options['galaxy']}', `system` = '{$options['system']}', `planet` = '{$options['planet']}'"
  );

  $config->db_saveItem('users_amount', $config->users_amount + 1);

  $username_safe = db_escape($username_unsafe);
  doquery("REPLACE INTO {{player_name_history}} SET `player_id` = {$user_new['id']}, `player_name` = '{$username_safe}'");

  if(!empty($options['partner_id']) && ($referral_row = db_user_by_id($options['partner_id'], true))) {
    doquery("INSERT INTO {{referrals}} SET `id` = {$user_new['id']}, `id_partner` = {$options['partner_id']}");
  }

  sys_player_new_adjust($user_new['id'], $new_planet_id);

  return $result = db_user_by_id($user_new['id']);
}
