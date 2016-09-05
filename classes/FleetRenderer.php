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

}
