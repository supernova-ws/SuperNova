<?php

/**
 * Created by Gorlum 08.01.2018 13:23
 */

namespace Unit;

use \classSupernova;
use Planet\Planet;

class Governor {
//  protected $type = UNIT_GOVERNOR_PRIMARY;
//  protected $typeIdField = 'PLANET_GOVERNOR_ID';
//  protected $typeLevelField = 'PLANET_GOVERNOR_LEVEL';

  protected $id = 0;
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
    $this->getPlanetData();
  }

  /**
   * @param $hireId
   */
  public function hire($hireId) {
    if (!in_array($hireId, sn_get_groups('governors'))) {
      return;
    }

    if ($hireId == $this->id && $this->getMaxLevel() && $this->getMaxLevel() >= $this->getLevel()) {
      return;
    }

    sn_db_transaction_start();
    $user = db_user_by_id($this->planet->id_owner, true);
//    $this->planetRow = DBStaticPlanet::db_planet_by_id($this->planet->id, true);
//    $build_data = eco_get_build_data($user, $this->planetRow, $hireId, $this->getId() == $hireId ? $this->getLevel() : 0);
    $this->planet->dbLoadRecord($this->planet->id);

    $build_data = eco_get_build_data($user, $this->planet->_getContainer()->asArray(), $hireId, $this->getId() == $hireId ? $this->getLevel() : 0);
    if (
      $build_data['CAN'][BUILD_CREATE]
      &&
      mrc_get_level($user, [], RES_DARK_MATTER) >= $build_data[BUILD_CREATE][RES_DARK_MATTER]
      &&
      rpg_points_change(
        $user['id'],
        RPG_GOVERNOR,
        -$build_data[BUILD_CREATE][RES_DARK_MATTER],
        sprintf(classSupernova::$lang['ov_governor_purchase'],
          classSupernova::$lang['tech'][$hireId],
          $hireId,
          $this->level,
          uni_render_planet_object_full($this->planet, false, true)
        )
      )
    ) {
      $this->addLevel($hireId);
      $this->planet->_getContainer()->update();
//      DBStaticPlanet::db_planet_set_by_id($this->planet->id, "`{$this->typeIdField}` = {$this->id}, `{$this->typeLevelField}` = {$this->level}");
    }
    sn_db_transaction_commit();
  }


  /**
   * @return int
   */
  public function getId() {
    return $this->id;
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
    return $this->id ? get_unit_param($this->id, P_MAX_STACK) : 0;
  }

  /**
   * @param $hireId
   */
  protected function addLevel($hireId) {
    if ($this->id == $hireId) {
      $this->level++;
    } else {
      $this->level = 1;
    }

    $this->id = $hireId;

    $this->setPlanetData();
  }


  protected function reset() {
    unset($this->unit);
    $this->planet = null;

    $this->id = 0;
    $this->level = 0;
  }

  protected function setPlanetData() {
    $this->planet->PLANET_GOVERNOR_ID = $this->id;
    $this->planet->PLANET_GOVERNOR_LEVEL = $this->level;
  }

  protected function getPlanetData() {
    $this->id = !empty($this->planet->PLANET_GOVERNOR_ID) ? intval($this->planet->PLANET_GOVERNOR_ID) : 0;
    $this->level = !empty($this->planet->PLANET_GOVERNOR_LEVEL) ? intval($this->planet->PLANET_GOVERNOR_LEVEL) : 0;
  }

}
