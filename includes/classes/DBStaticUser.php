<?php

class DBStaticUser extends DBStaticRecord {

  public static $_table = 'users';
  public static $_idField = 'id';


  /**
   * @param int $user_id
   *
   * @return string[]
   */
  public static function getOnlineTime($user_id) {
    $user_record = static::getRecordById($user_id, array('username', 'onlinetime'));

    return !empty($user_record) ? $user_record : array();
  }

}
