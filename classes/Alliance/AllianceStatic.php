<?php
/**
 * Created by Gorlum 15.06.2017 11:56
 */

namespace Alliance;


use Player\RecordPlayer;

class AllianceStatic {

  public static function parseTitleList($string) {
    global $ally_rights;

    $result = [];

    $titleList = explode(';', $string);
    foreach ($titleList as $titleIndex => $titleString) {
      if (empty($titleString)) {
        continue;
      }
      $accessList = explode(',', $titleString);
      $result[$titleIndex] = array_combine($ally_rights, $accessList);
    }

    return $result;
  }

  public static function getMemberList($allyId) {
    $allyId_safe = \classSupernova::$db->db_escape($allyId);

    return db_user_list("`ally_id`= {$allyId_safe} ORDER BY `ally_rank_id`", false,
      'id, username, galaxy, system, planet, onlinetime, ally_rank_id, ally_register_time, total_points');
  }

  public static function compileTitleRights(&$titleList) {
    foreach ($titleList as &$titleDesc) {
      $titleRights = [];
      foreach ($titleDesc as $rightId => $rightValue) {
        if ($rightValue == 1) {
          $titleRights[] = $rightId;
        }
      }
      $titleDesc['rights'] = implode(',', $titleRights);
    }

  }

  public static function titleMembers($memberList, RecordAlliance $alliance) {
    global $ally_rights;

    $copy = $ally_rights;
    array_shift($copy);

    $result = [];
    if (empty($memberList)) {
      return $result;
    }

    $titleList = self::parseTitleList($alliance->titleList);
    self::compileTitleRights($titleList);


    foreach ($memberList as $playerRecord) {
      $temp = [
        'PLAYER_ID'    => $playerRecord['id'],
        'PLAYER_NAME'  => $playerRecord['username'],
        'ONLINE'       => $playerRecord['onlinetime'],
        'ONLINE_SQL'   => date(FMT_DATE_TIME_SQL, $playerRecord['onlinetime']),
        'VACATION'     => $playerRecord['vacation'],
        'VACATION_SQL' => date(FMT_DATE_TIME_SQL, $playerRecord['vacation']),
        'BAN'          => $playerRecord['banaday'],
        'BAN_SQL'      => date(FMT_DATE_TIME_SQL, $playerRecord['banaday']),
        'TITLE'        => $titleList[$playerRecord['ally_rank_id']]['name'],
        'TITLE_ID'     => $playerRecord['ally_rank_id'],
        'RIGHTS'       => $titleList[$playerRecord['ally_rank_id']]['rights'],
      ];
      if ($playerRecord['id'] == $alliance->ownerId) {
        $temp = array_merge($temp, [
          'TITLE'    => $alliance->ownerRankName,
          'TITLE_ID' => -1,
          'RIGHTS'   => implode(',', $copy),
          'OWNER'    => true,
        ]);
      }
      $result[] = $temp;
    }

    return $result;
  }


  public static function passAlliance($allyId, $newOwnerId) {
    try {
      sn_db_transaction_start();
      if (empty($alliance = \Alliance\RecordAlliance::findFirst($allyId))) {
        throw new \Exception('{ Альянс с указанным ID не найден }', ERR_ERROR);
      }

      if ($newOwnerId == $alliance->ownerId) {
        throw new \Exception('{ Указанный пользователь уже является владельцем указанного Альянса }', ERR_NOTICE);
      }

      if (!empty($oldOwner = RecordPlayer::findFirst($alliance->ownerId))) {
        $oldOwner->ally_rank_id = 0;
        if (!$oldOwner->update()) {
          throw new \Exception('{ Ошибка изменения ранга у старого владельца }', ERR_ERROR);
        }
      }

      if (empty($newOwner = RecordPlayer::findFirst($newOwnerId))) {
        throw new \Exception('{ Новый владелец Альянса не найден }', ERR_ERROR);
      }

      $newOwner->ally_rank_id = 0;
      if (!$newOwner->update()) {
        throw new \Exception('{ Ошибка изменения ранга у нового владельца }', ERR_ERROR);
      }

      $alliance->ownerId = $newOwnerId;
      if (!$alliance->update()) {
        throw new \Exception('{ Ошибка изменения владельца Альянса }', ERR_ERROR);
      }

      sn_db_transaction_commit();
    } catch (\Exception $e) {
      sn_db_transaction_rollback();

      throw $e;
    }

    return true;
  }

}
