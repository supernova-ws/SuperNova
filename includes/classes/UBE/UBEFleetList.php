<?php

/**
 * Class UBEFleetList
 *
 * @method UBEFleet offsetGet($offset)
 * @property UBEFleet[] $_container
 */
class UBEFleetList extends ArrayAccessV2 {

  public function load_from_players(UBEPlayerList $players) {
    foreach($this->_container as $fleet_id => $objFleet) {
      // TODO - эта последовательность должна быть при загрузке флота (?)

      $objFleet->copy_stats_from_player($players[$objFleet->UBE_OWNER]);

      // Вычисляем бонус игрока и добавляем его к бонусам флота
      $objFleet->bonuses_add_float($players[$objFleet->UBE_OWNER]->player_bonus_get_all());
      // TODO
//      $objFleet->add_planet_bonuses();
//      $objFleet->add_fleet_bonuses();
//      $objFleet->add_ship_bonuses();

      $objFleet->calculate_battle_stats();
    }
  }

}
