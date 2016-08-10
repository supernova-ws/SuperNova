<?php
/**
 * Created by Gorlum 30.07.2016 20:52
 */
namespace Buddy;
use Common\GlobalContainer;


class BuddyView {

  /**
   * @param GlobalContainer $gc
   * @param BuddyContainer $cBuddy
   *
   * @return \template
   */
  public function makeTemplate($gc, $cBuddy) {
    $playerLocale = $gc->localePlayer;

    $result = array();
    sn_db_transaction_start();
    try {
      $cBuddy->getModel()->route($cBuddy);
    } catch (BuddyException $e) {
      $exceptionCode = \ResultMessages::parseException($e, $result);

      $exceptionCode == ERR_NONE ? sn_db_transaction_commit() : sn_db_transaction_rollback();
    }
    sn_db_transaction_rollback();


    empty($template_result) ? $template_result = array() : false;

    foreach ($cBuddy->getModel()->db_buddy_list_by_user($cBuddy) as $row) {
      $row['BUDDY_REQUEST'] = sys_bbcodeParse($row['BUDDY_REQUEST']);

      $row['BUDDY_ACTIVE'] = $row['BUDDY_STATUS'] == BUDDY_REQUEST_ACTIVE;
      $row['BUDDY_DENIED'] = $row['BUDDY_STATUS'] == BUDDY_REQUEST_DENIED;
      $row['BUDDY_INCOMING'] = $row['BUDDY_OWNER_ID'] == $cBuddy->playerId;
      $row['BUDDY_ONLINE'] = floor((SN_TIME_NOW - $row['onlinetime']) / 60);

      $template_result['.']['buddy'][] = $row;
    }

    $template_result += array(
      'PAGE_HEADER'       => $playerLocale['buddy_buddies'],
      'PAGE_HINT'         => $playerLocale['buddy_hint'],
      'USER_ID'           => $cBuddy->playerId,
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
