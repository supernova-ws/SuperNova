<?php

/**
 * Created by Gorlum 03.09.2016 16:40
 */

use Common\GlobalContainer;


class FleetRenderer {

  public function __construct(GlobalContainer $c) {

  }

  /**
   * @param Fleet $fleet
   */
  public function renderParamCoordinates(Fleet $fleet) {
    global $template_result;
    $template_result += array(
      'thisgalaxy'      => $fleet->dbSourcePlanetRow['galaxy'],
      'thissystem'      => $fleet->dbSourcePlanetRow['system'],
      'thisplanet'      => $fleet->dbSourcePlanetRow['planet'],
      'thisplanet_type' => $fleet->dbSourcePlanetRow['planet_type'],

      'galaxy'         => $fleet->targetVector->galaxy,
      'system'         => $fleet->targetVector->system,
      'planet'         => $fleet->targetVector->planet,
      'planet_type'    => $fleet->targetVector->type,
      'target_mission' => $fleet->mission_type,
      'MISSION_NAME'   => $fleet->mission_type ? classLocale::$lang['type_mission'][$fleet->mission_type] : '',

      'MT_COLONIZE' => MT_COLONIZE,
    );
  }

  /**
   * @param Fleet $fleet
   * @param int   $missionStartTimeStamp
   * @param int   $timeMissionJob
   *
   * @return array
   *
   * @throws Exception
   */
  public function renderFleet(Fleet $fleet, $missionStartTimeStamp = SN_TIME_NOW, $timeMissionJob = 0) {
    $unitList = $fleet->getUnitList();
    if ($unitList->unitsCount() <= 0) {
      message(classLocale::$lang['fl_err_no_ships'], classLocale::$lang['fl_error'], 'fleet' . DOT_PHP_EX, 5);
    }

    $timeToReturn = $fleet->travelData['duration'] * 2 + $timeMissionJob;
    $result = array(
      'ID'                 => 1,
      'START_TYPE_TEXT_SH' => classLocale::$lang['sys_planet_type_sh'][$fleet->dbSourcePlanetRow['planet_type']],
      'START_COORDS'       => uni_render_coordinates($fleet->dbSourcePlanetRow),
      'START_NAME'         => $fleet->dbSourcePlanetRow['name'],
      'START_TIME_TEXT'    => date(FMT_DATE_TIME, $missionStartTimeStamp + $timeToReturn + SN_CLIENT_TIME_DIFF),
      'START_LEFT'         => floor($timeToReturn),
      'END_TYPE_TEXT_SH'   =>
        !empty($fleet->targetVector->type)
          ? classLocale::$lang['sys_planet_type_sh'][$fleet->targetVector->type]
          : '',
      'END_COORDS'         => uniRenderVector($fleet->targetVector),
      'END_NAME'           => !empty($fleet->dbTargetRow['name']) ? $fleet->dbTargetRow['name'] : '',
      'END_TIME_TEXT'      => date(FMT_DATE_TIME, $missionStartTimeStamp + $fleet->travelData['duration'] + SN_CLIENT_TIME_DIFF),
      'END_LEFT'           => floor($fleet->travelData['duration']),
    );
    $result['.']['ships'] = $unitList->unitsRender();

    return $result;
  }


  /**
   * @return array
   */
  public function renderAllowedMissions(array $allowed_missions) {
    $result = array();

    foreach ($allowed_missions as $key => $value) {
      $result[] = array(
        'ID'   => $key,
        'NAME' => classLocale::$lang['type_mission'][$key],
      );
    };

    return $result;
  }

  /**
   * @param $mission_type
   * @param $max_duration
   *
   * @return array
   */
  public function renderDuration($mission_type, $max_duration) {
    $result = array();

    if (!$max_duration) {
      return $result;
    }

    $config_game_speed_expedition = ($mission_type == MT_EXPLORE && classSupernova::$config->game_speed_expedition ? classSupernova::$config->game_speed_expedition : 1);
    for ($i = 1; $i <= $max_duration; $i++) {
      $result[] = array(
        'ID'   => $i,
        'TIME' => pretty_time(ceil($i * 3600 / $config_game_speed_expedition)),
      );
    }

    return $result;
  }

  /**
   * @return array
   */
  public function renderAllowedPlanetTypes($allowed_planet_types) {
    $result = array();

    foreach ($allowed_planet_types as $possible_planet_type_id) {
      $result[] = array(
        'ID'         => $possible_planet_type_id,
        'NAME'       => classLocale::$lang['sys_planet_type'][$possible_planet_type_id],
        'NAME_SHORT' => classLocale::$lang['sys_planet_type_sh'][$possible_planet_type_id],
      );
    }

    return $result;
  }

  /**
   * @param array $planetResources
   *
   * @return array
   */
  // TODO - REDO to resource_id
  public function renderPlanetResources(&$planetResources) {
    $result = array();

    $i = 0;
    foreach ($planetResources as $resource_id => $resource_amount) {
      $result[] = array(
        'ID'        => $i++, // $resource_id,
        'ON_PLANET' => $resource_amount,
        'TEXT'      => pretty_number($resource_amount),
        'NAME'      => classLocale::$lang['tech'][$resource_id],
      );
    }

    return $result;
  }

  /**
   * @param $template_result
   */
  public function renderShipSortOptions(&$template_result) {
    foreach (classLocale::$lang['player_option_fleet_ship_sort'] as $sort_id => $sort_text) {
      $template_result['.']['ship_sort_list'][] = array(
        'VALUE' => $sort_id,
        'TEXT'  => $sort_text,
      );
    }
    $template_result += array(
      'FLEET_SHIP_SORT'         => classSupernova::$user_options[PLAYER_OPTION_FLEET_SHIP_SORT],
      'FLEET_SHIP_SORT_INVERSE' => classSupernova::$user_options[PLAYER_OPTION_FLEET_SHIP_SORT_INVERSE],
    );
  }


}
