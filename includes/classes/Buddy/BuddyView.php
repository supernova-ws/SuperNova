<?php
/**
 * Created by Gorlum 30.07.2016 20:52
 */
namespace Buddy;
use Common\GlobalContainer;


class BuddyView {

  /**
   * @param GlobalContainer $gc
   * @param array           $user
   *
   * @return \template
   */
  public function makeTemplate($gc, $user) {
    $playerLocale = $gc->localePlayer;

    $cBuddy = new BuddyRoutingParams();

    $cBuddy->gc = $gc;
    $cBuddy->buddy_id = sys_get_param_id('buddy_id');
    $cBuddy->mode = sys_get_param_str('mode');
    $cBuddy->newFriendIdSafe = sys_get_param_id('request_user_id');
    $cBuddy->new_friend_name_unsafe = sys_get_param_str_unsafe('request_user_name');
    $cBuddy->new_request_text_unsafe = sys_get_param_str_unsafe('request_text');
    $cBuddy->playerArray = $user;

    $cBuddy->playerId = function (BuddyRoutingParams $cBuddy) {
      return $cBuddy->playerArray['id'];
    };
    $cBuddy->playerName = function (BuddyRoutingParams $cBuddy) {
      return $cBuddy->playerArray['username'];
    };
    $cBuddy->playerNameAndCoordinates = function (BuddyRoutingParams $cBuddy) {
      return "{$cBuddy->playerArray['username']} " . uni_render_coordinates($cBuddy->playerArray);
    };

    $result = array();
    sn_db_transaction_start();
    try {
      $gc->buddy->route($cBuddy);
    } catch (BuddyException $e) {
      $exceptionCode = \ResultMessages::parseException($e, $result);

      $exceptionCode == ERR_NONE ? sn_db_transaction_commit() : sn_db_transaction_rollback();
    }
    sn_db_transaction_rollback();
    unset($buddy);

    empty($template_result) ? $template_result = array() : false;

//    foreach (BuddyModel::db_buddy_list_by_user_static($gc->db, $user['id']) as $row) {
    foreach (\classSupernova::$gc->buddy->db_buddy_list_by_user($user['id']) as $row) {
      $row['BUDDY_REQUEST'] = sys_bbcodeParse($row['BUDDY_REQUEST']);

      $row['BUDDY_ACTIVE'] = $row['BUDDY_STATUS'] == BUDDY_REQUEST_ACTIVE;
      $row['BUDDY_DENIED'] = $row['BUDDY_STATUS'] == BUDDY_REQUEST_DENIED;
      $row['BUDDY_INCOMING'] = $row['BUDDY_OWNER_ID'] == $user['id'];
      $row['BUDDY_ONLINE'] = floor((SN_TIME_NOW - $row['onlinetime']) / 60);

      $template_result['.']['buddy'][] = $row;
    }

    $template_result += array(
      'PAGE_HEADER'       => $playerLocale['buddy_buddies'],
      'PAGE_HINT'         => $playerLocale['buddy_hint'],
      'USER_ID'           => $user['id'],
      'REQUEST_USER_ID'   => $cBuddy->newFriendIdSafe,
      'REQUEST_USER_NAME' => $cBuddy->new_friend_name_unsafe,
    );

    $template_result['.']['result'] = is_array($template_result['.']['result']) ? $template_result['.']['result'] : array();
    $template_result['.']['result'] += $result;

    $template = gettemplate('buddy', true);
    $template->assign_recursive($template_result);

    return $template;
  }

}
