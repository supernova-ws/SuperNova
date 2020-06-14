<?php

/**
 * Created by Gorlum 08.01.2018 14:30
 */

namespace Planet;


use Core\EntityDb;
use Unit\Governor;
use Exception;
use HelperString;
use SN;

/**
 * Class Planet
 * @package Planet
 *
 * @method bool insert()
 *
 * @property int|string $id        - Record ID name would be normalized to 'id'
 * @property string     $name
 * @property int|float  $id_owner
 * @property int        $galaxy
 * @property int        $system
 * @property int        $planet
 * @property int        $planet_type
 * @property int|float  $metal
 * @property int|float  $crystal
 * @property int|float  $deuterium
 * property int|float $energy_max
 * property int|float $energy_used
 * property int $last_jump_time
 * property int $metal_perhour
 * property int $crystal_perhour
 * property int $deuterium_perhour
 * property int $metal_mine_porcent
 * property int $crystal_mine_porcent
 * property int $deuterium_sintetizer_porcent
 * property int $solar_plant_porcent
 * property int $fusion_plant_porcent
 * property int $solar_satelit_porcent
 * property int $last_update
 * property int $que_processed
 * @property string $image
 * property int|float $points
 * property int|float $ranks
 * property int $id_level
 * property int $destruyed
 * property int $diameter
 * @property int        $field_max - maximum allowed number of fields
 * property int $field_current
 * property int $temp_min
 * property int $temp_max
 * property int|float $metal_max
 * property int|float $crystal_max
 * property int|float $deuterium_max
 * property int|float $parent_planet
 * @property int|float  $debris_metal
 * @property int|float  $debris_crystal
 * @property int        $PLANET_GOVERNOR_ID
 * @property int        $PLANET_GOVERNOR_LEVEL
 * property int       $planet_teleport_next
 * property int $ship_sattelite_sloth_porcent
 * @property int        $density
 * @property int        $density_index
 * property int $position_original
 * property int $field_max_original
 * property int $temp_min_original
 * property int $temp_max_original
 */
class Planet extends EntityDb {

  /**
   * @var string $_activeClass
   */
  protected $_activeClass = '\\Planet\\RecordPlanet';

  /**
   * @var RecordPlanet $_container
   */
  protected $_container;

  /**
   * @var float[] $resources
   */
  protected $resources = [
    RES_METAL     => 0,
    RES_CRYSTAL   => 0,
    RES_DEUTERIUM => 0,
  ];

  /**
   * @var Governor $governor
   */
  protected $governor;

  /**
   * Planet constructor.
   */
  public function __construct() {
    parent::__construct();
  }

  public function getGovernor() {
    if (empty($this->governor)) {
      $this->governor = new Governor();
      $this->governor->setPlanet($this);
    }

    return $this->governor;
  }

  public function governorHire($hireId) {
    $this->getGovernor()->hire($hireId);
  }

  /**
   * @param string $redirect
   *
   * @deprecated
   * TODO - change saveing
   */
  public function sn_sys_sector_buy($redirect = 'overview.php') {
    if (!sys_get_param_str('sector_buy') || $this->planet_type != PT_PLANET) {
      return;
    }

    sn_db_transaction_start();
    $user = db_user_by_id($this->id_owner, true, '*');
    $this->setForUpdate()->dbLoadRecord($this->id);

    $sector_cost = eco_get_build_data($user, $this->asArray(), UNIT_SECTOR, mrc_get_level($user, $this->asArray(), UNIT_SECTOR), true);
    $sector_cost = $sector_cost[BUILD_CREATE][RES_DARK_MATTER];
    if ($sector_cost <= mrc_get_level($user, [], RES_DARK_MATTER)) {
      $planet_name_text = uni_render_planet($this->asArray());
      if (rpg_points_change($user['id'], RPG_SECTOR, -$sector_cost,
        sprintf(
          SN::$lang['sys_sector_purchase_log'],
          $user['username'],
          $user['id'],
          $planet_name_text,
          SN::$lang['sys_planet_type'][$this->planet_type],
          $this->id,
          $sector_cost
        )
      )) {
        $this->field_max++;
        $this->update();
      } else {
        sn_db_transaction_rollback();
      }
    }
    sn_db_transaction_commit();

    sys_redirect($redirect);
  }

  /**
   * @param $user
   *
   * @return array
   *
   * @deprecated
   * TODO - change saveing
   */
  public function sn_sys_planet_core_transmute(&$user) {
    if (!sys_get_param_str('transmute')) {
      return array();
    }

    try {
      if ($this->planet_type != PT_PLANET) {
        throw new exception(SN::$lang['ov_core_err_not_a_planet'], ERR_ERROR);
      }

      if ($this->density_index == ($new_density_index = sys_get_param_id('density_type'))) {
        throw new exception(SN::$lang['ov_core_err_same_density'], ERR_WARNING);
      }

      sn_db_transaction_start();
      $user = db_user_by_id($user['id'], true, '*');
      $this->setForUpdate()->dbLoadRecord($this->id);

      $planet_density_index = $this->density_index;
      $density_price_chart = $this->planet_density_price_chart();

      if (!isset($density_price_chart[$new_density_index])) {
        // Hack attempt
        throw new exception(SN::$lang['ov_core_err_denisty_type_wrong'], ERR_ERROR);
      }

      $user_dark_matter = mrc_get_level($user, false, RES_DARK_MATTER);
      $transmute_cost = $density_price_chart[$new_density_index];
      if ($user_dark_matter < $transmute_cost) {
        throw new exception(SN::$lang['ov_core_err_no_dark_matter'], ERR_ERROR);
      }

      $sn_data_planet_density = sn_get_groups('planet_density');
      foreach ($sn_data_planet_density as $key => $value) {
        if ($key == $new_density_index) {
          break;
        }
        $prev_density_index = $key;
      }

      $new_density = round(($sn_data_planet_density[$new_density_index][UNIT_PLANET_DENSITY] + $sn_data_planet_density[$prev_density_index][UNIT_PLANET_DENSITY]) / 2);

      rpg_points_change($user['id'], RPG_PLANET_DENSITY_CHANGE, -$transmute_cost,
        array(
          'Planet %1$s ID %2$d at coordinates %3$s changed density type from %4$d "%5$s" to %6$d "%7$s". New density is %8$d kg/m3',
          $this->name,
          $this->id,
          uni_render_coordinates($this->asArray()),
          $planet_density_index,
          SN::$lang['uni_planet_density_types'][$planet_density_index],
          $new_density_index,
          SN::$lang['uni_planet_density_types'][$new_density_index],
          $new_density
        )
      );

      DBStaticPlanet::db_planet_set_by_id($this->id, "`density` = {$new_density}, `density_index` = {$new_density_index}");
      sn_db_transaction_commit();

      $this->density = $new_density;
      $this->density_index = $new_density_index;
      $result = array(
        'STATUS'  => ERR_NONE,
        'MESSAGE' => sprintf(SN::$lang['ov_core_err_none'], SN::$lang['uni_planet_density_types'][$planet_density_index], SN::$lang['uni_planet_density_types'][$new_density_index], $new_density),
      );
    } catch (Exception $e) {
      sn_db_transaction_rollback();
      $result = array(
        'STATUS'  => $e->getCode(),
        'MESSAGE' => $e->getMessage(),
      );
    }

    return $result;
  }

  /**
   * @return array
   */
  public function planet_density_price_chart() {
    $sn_data_density = sn_get_groups('planet_density');
    $density_price_chart = array();

    foreach ($sn_data_density as $density_id => $density_data) {
      // Отсекаем записи с RARITY = 0 - служебные записи и супер-ядра
      $density_data[UNIT_PLANET_DENSITY_RARITY] ? $density_price_chart[$density_id] = $density_data[UNIT_PLANET_DENSITY_RARITY] : false;
    }
    unset($density_price_chart[PLANET_DENSITY_NONE]);

    $total_rarity = array_sum($density_price_chart);

    foreach ($density_price_chart as &$density_data) {
      $density_data = ceil($total_rarity / $density_data * $this->field_max * PLANET_DENSITY_TO_DARK_MATTER_RATE);
    }

    return $density_price_chart;
  }

  /**
   * @param int $user_dark_matter
   *
   * @return array
   */
  public function tpl_planet_density_info($user_dark_matter) {
    $result = [];

    $density_price_chart = Planet::planet_density_price_chart();

    foreach ($density_price_chart as $density_price_index => &$density_price_data) {
      $density_cost = $density_price_data;
      $density_price_data = array(
        'COST'            => $density_cost,
        'COST_TEXT'       => HelperString::numberFloorAndFormat($density_cost),
        'COST_TEXT_CLASS' => prettyNumberGetClass($density_cost, $user_dark_matter),
        'REST'            => $user_dark_matter - $density_cost,
        'ID'              => $density_price_index,
        'TEXT'            => SN::$lang['uni_planet_density_types'][$density_price_index],
      );
      $result[] = $density_price_data;
    }

    $planet_density_index = $this->density_index;

    return [
      '.'                    => [
        'densities' => $result,
      ],
      'PLANET_DENSITY_INDEX' => $planet_density_index,
      'PLANET_CORE_TEXT'     => \SN::$lang['uni_planet_density_types'][$planet_density_index],
    ];
  }

  /**
   * @param array $user
   *
   * @return array
   */
  public function int_planet_pretemplate($user) {
    $governor_id = $this->PLANET_GOVERNOR_ID;
    $governor_level_plain = mrc_get_level($user, $this->asArray(), $governor_id, false, true);

    return [
      'PLANET_ID'        => $this->id,
      'PLANET_NAME'      => htmlentities($this->name, ENT_QUOTES, 'UTF-8'),
      'PLANET_NAME_JS'   => htmlentities(js_safe_string($this->name), ENT_QUOTES, 'UTF-8'),
      'PLANET_GALAXY'    => $this->galaxy,
      'PLANET_SYSTEM'    => $this->system,
      'PLANET_PLANET'    => $this->planet,
      'PLANET_TYPE'      => $this->planet_type,
      'PLANET_TYPE_TEXT' => SN::$lang['sys_planet_type'][$this->planet_type],
      'PLANET_DEBRIS'    => $this->debris_metal + $this->debris_crystal,
      'PLANET_IMAGE'     => $this->image,

      'PLANET_GOVERNOR_ID'         => $governor_id,
      'PLANET_GOVERNOR_NAME'       => SN::$lang['tech'][$governor_id],
      'PLANET_GOVERNOR_LEVEL'      => $governor_level_plain,
      'PLANET_GOVERNOR_LEVEL_PLUS' => mrc_get_level($user, $this->asArray(), $governor_id, false, false) - $governor_level_plain,
      'PLANET_GOVERNOR_LEVEL_MAX'  => get_unit_param($governor_id, P_MAX_STACK),
    ];
  }

  public function reset() {
    $this->governor = null;

    $this->resources = [
      RES_METAL     => 0,
      RES_CRYSTAL   => 0,
      RES_DEUTERIUM => 0,
    ];

    return parent::reset();
  }

  /**
   * @return RecordPlanet
   */
  public function _getContainer() {
    return $this->_container;
  }


  /**
   * @param int   $resourceId
   * @param float $resourceCount
   *
   * @throws \Exception
   */
  public function changeResource($resourceId, $resourceCount) {
    if (empty($resourceCount)) {
      return;
    }

    if (!array_key_exists($resourceId, $this->resources)) {
      throw new \Exception("PLANET ERROR! Trying to change unknown resource type [{$resourceId}] '{$resourceCount}' on planet [{$this->id}]");
    }

    $resourceCount = ceil($resourceCount);

    if ($this->resources[$resourceId] + $resourceCount < 0) {
      throw new \Exception("PLANET ERROR! Trying to deduct more resources [{$resourceId}] '{$resourceCount}' when planet [{$this->id}] has only {$this->resources[$resourceId]}");
    }

    $this->resources[$resourceId] += $resourceCount;

    $fieldName = pname_resource_name($resourceId);
    $this->_getContainer()->inc()->$fieldName = $resourceCount;

//    $this->metal = $this->resources[RES_METAL];
//    $this->crystal = $this->resources[RES_CRYSTAL];
//    $this->deuterium = $this->resources[RES_DEUTERIUM];
  }

  public function dbLoadRecord($id) {
    $result = parent::dbLoadRecord($id);

    if(!$this->isNew()) {
      $this->resources[RES_METAL] = $this->_getContainer()->metal;
      $this->resources[RES_CRYSTAL] = $this->_getContainer()->crystal;
      $this->resources[RES_DEUTERIUM] = $this->_getContainer()->deuterium;
    }

    return $result;
  }

}
