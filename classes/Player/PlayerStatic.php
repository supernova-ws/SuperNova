<?php
/**
 * Created by Gorlum 05.03.2018 20:42
 */

namespace Player;

use Fleet\DbFleetStatic;
use SN;
use Planet\DBStaticPlanet;

/**
 * Class PlayerStatic
 * @package Player
 *
 * @deprecated
 */
class PlayerStatic {

  public static function getPlayerProduction($userId) {
    return doquery("       
       SELECT
         sum(metal_perhour) + sum(crystal_perhour) * 2 + sum(deuterium_perhour) * 4 AS `total`,
         sum(metal_perhour)                                                         AS `metal`,
         sum(crystal_perhour)                                                       AS `crystal`,
         sum(deuterium_perhour)                                                     AS `deuterium`,
         avg(metal_mine_porcent) AS `avg_metal_percent`,
         avg(crystal_mine_porcent) AS `avg_crystal_percent`,
         avg(deuterium_sintetizer_porcent) AS `avg_deuterium_percent`
       FROM
         `{{planets}}` AS p
       WHERE
         p.`id_owner` = {$userId}
       GROUP BY
         id_owner", true);
  }

  /**
   * @param $UserID
   *
   * @deprecated
   *
   * TODO: Full rewrite
   */
  public static function DeleteSelectedUser($UserID) {
    $internalTransaction = false;
    if (!sn_db_transaction_check(false)) {
      sn_db_transaction_start();

      $internalTransaction = true;
    }

    $TheUser = db_user_by_id($UserID);

    if (!empty($TheUser['ally_id'])) {
      $TheAlly = doquery("SELECT * FROM `{{alliance}}` WHERE `id` = '" . $TheUser['ally_id'] . "';", '', true);
      $TheAlly['ally_members'] -= 1;
      doquery("UPDATE `{{alliance}}` SET `ally_members` = '" . $TheAlly['ally_members'] . "' WHERE `id` = '" . $TheAlly['id'] . "';");

//      if ( $TheAlly['ally_members'] > 0 ) {
//        doquery ( "UPDATE `{{alliance}}` SET `ally_members` = '" . $TheAlly['ally_members'] . "' WHERE `id` = '" . $TheAlly['id'] . "';");
//      } else {
//        doquery ( "DELETE FROM `{{alliance}}` WHERE `id` = '" . $TheAlly['id'] . "';");
//        doquery ( "DELETE FROM `{{statpoints}}` WHERE `stat_type` = '2' AND `id_owner` = '" . $TheAlly['id'] . "';");
//      }
    }

    // Deleting all fleets
    DbFleetStatic::db_fleet_list_delete_by_owner($UserID);

    // Deleting all planets
    DBStaticPlanet::db_planet_list_delete_by_owner($UserID);

    // TEMPORARY player messages not deleted
//    doquery("DELETE FROM `{{messages}}` WHERE `message_sender` = '" . $UserID . "';");
//    doquery("DELETE FROM `{{messages}}` WHERE `message_owner` = '" . $UserID . "';");

    doquery("DELETE FROM `{{notes}}` WHERE `owner` = '" . $UserID . "';");

    doquery("DELETE FROM `{{buddy}}` WHERE `BUDDY_SENDER_ID` = '" . $UserID . "';");
    doquery("DELETE FROM `{{buddy}}` WHERE `BUDDY_OWNER_ID` = '" . $UserID . "';");

    doquery("DELETE FROM `{{referrals}}` WHERE (`id` = '{$UserID}') OR (`id_partner` = '{$UserID}');");

    doquery("DELETE FROM `{{statpoints}}` WHERE `stat_type` = '1' AND `id_owner` = '" . $UserID . "';");

    // Deleting all units
    SN::db_del_record_list(LOC_UNIT, "`unit_location_type` = " . LOC_PLAYER . " AND `unit_player_id` = " . $UserID);

    // Deleting player's record
    SN::db_del_record_by_id(LOC_USER, $UserID);
    dbUpdateUsersCount(SN::$config->pass()->users_amount - 1);

    if ($internalTransaction) {
      sn_db_transaction_commit();
    }
  }

  public static function dbUpdateBotStatus($botType, $onlineTime = SN_TIME_NOW) {
    SN::$db->doquery("UPDATE `{{users}}` SET `onlinetime` = " . $onlineTime . " WHERE `user_bot` = " . $botType);
  }

}
