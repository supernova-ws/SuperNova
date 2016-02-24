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
      $objFleet->add_player_bonuses($players[$objFleet->UBE_OWNER]);
//      $objFleet->add_planet_bonuses(); // TODO
      $objFleet->calculate_battle_stats();
    }
  }

}
