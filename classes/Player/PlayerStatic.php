<?php
/**
 * Created by Gorlum 05.03.2018 20:42
 */

namespace Player;


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

}
