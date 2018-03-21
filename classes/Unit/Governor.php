<?php

/**
 * Created by Gorlum 08.01.2018 13:23
 */

namespace Unit;

use \SN;
use Planet\Planet;

class Governor extends Unit {
//  protected $type = UNIT_GOVERNOR_PRIMARY;
//  protected $typeIdField = 'PLANET_GOVERNOR_ID';
//  protected $typeLevelField = 'PLANET_GOVERNOR_LEVEL';

  protected $snId = 0;
  protected $level = 0;

  /**
   * @var RecordUnit $unit
   */
  protected $unit;

  /**
   * @var Planet $planet
   */
  protected $planet;

  /**
   * Governor constructor.
   */
  public function __construct() {
    $this->reset();
  }

  /**
   * @param Planet $planet
   */
  public function setPlanet($planet) {
    $this->reset();

    $this->planet = $planet;
    $this->getExternalData();
  }

  /**
   * @param int $hireId - Hire unit SN ID
   */
  public function hire($hireId) {
    if (!in_array($hireId, sn_get_groups('governors'))) {
      return;
    }

    if ($hireId == $this->getSnId() && $this->getMaxLevel() && $this->getMaxLevel() >= $this->getLevel()) {
      return;
    }

    sn_db_transaction_start();
    $user = db_user_by_id($this->planet->id_owner, true);
//    $this->planetRow = Planet\DBStaticPlanet::db_planet_by_id($this->planet->id, true);
//    $build_data = eco_get_build_data($user, $this->planetRow, $hireId, $this->getId() == $hireId ? $this->getLevel() : 0);
    $this->planet->dbLoadRecord($this->planet->id);

    $build_data = eco_get_build_data($user, $this->planet->asArray(), $hireId, $this->getSnId() == $hireId ? $this->getLevel() : 0);
    if (
      $build_data['CAN'][BUILD_CREATE]
      &&
      mrc_get_level($user, [], RES_DARK_MATTER) >= $build_data[BUILD_CREATE][RES_DARK_MATTER]
      &&
      rpg_points_change(
        $user['id'],
        RPG_GOVERNOR,
        -$build_data[BUILD_CREATE][RES_DARK_MATTER],
        sprintf(SN::$lang['ov_governor_purchase'],
          SN::$lang['tech'][$hireId],
          $hireId,
          $this->level,
          uni_render_planet_object_full($this->planet, false, true)
        )
      )
    ) {
      $this->addLevel($hireId);
      $this->planet->update();
    }
    sn_db_transaction_commit();
  }


  /**
   * @return int
   */
  public function getSnId() {
    return $this->snId;
  }

  /**
   * @return int
   */
  public function getLevel() {
    return $this->level;
  }

  /**
   * @return int
   */
  public function getMaxLevel() {
    $snId =  $this->getSnId();
    return !empty($snId) ? get_unit_param($snId, P_MAX_STACK) : 0;
  }

  /**
   * @param $hireId
   */
  protected function addLevel($hireId) {
    if ($this->getSnId() == $hireId) {
      $this->level++;
    } else {
      $this->level = 1;
    }

    $this->snId = $hireId;

    $this->setExternalData();
  }


  protected function reset() {
    unset($this->unit);
    $this->planet = null;

    $this->snId = 0;
    $this->level = 0;
  }

  /**
   * Sets data on external sources from internal properties
   */
  protected function setExternalData() {
    $this->planet->PLANET_GOVERNOR_ID = $this->getSnId();
    $this->planet->PLANET_GOVERNOR_LEVEL = $this->level;
  }

  /**
   * Loads some data from external sources
   */
  protected function getExternalData() {
    $this->snId = !empty($this->planet->PLANET_GOVERNOR_ID) ? intval($this->planet->PLANET_GOVERNOR_ID) : 0;
    $this->level = !empty($this->planet->PLANET_GOVERNOR_LEVEL) ? intval($this->planet->PLANET_GOVERNOR_LEVEL) : 0;
  }

}
