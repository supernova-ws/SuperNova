<?php
/**
 * Created by Gorlum 12.10.2017 15:54
 */

namespace Pm;

use \Fleet\MissionEspionageReport;
use \HelperString;
use SnTemplate;

class DecodeEspionage {
  const ALLOWED_UNITS = [UNIT_RESOURCES, UNIT_SHIPS, UNIT_DEFENCE, UNIT_STRUCTURES, UNIT_TECHNOLOGIES];

  /**
   * @param MissionEspionageReport $missionReport
   */
  public static function decode($missionReport) {
    $lang = \SN::$lang;
    $general = \SN::$gc->general;

    $template = SnTemplate::gettemplate('msg_message_spy');

    $groups = [];
    foreach ($missionReport->spiedUnits as $unitId => $unitAmount) {
      $groups[get_unit_param($unitId, P_UNIT_TYPE)][$unitId] = $unitAmount;
    }

    foreach(static::ALLOWED_UNITS as $groupId) {
      if(empty($groups[$groupId])) {
        continue;
      }

      $template->assign_block_vars('group', [
        'ID' => $groupId,
        'NAME' => $lang['tech'][$groupId],
      ]);

      foreach($general->getGroupsById($groupId) as $unitId) {
        if((!isset($groups[$groupId][$unitId]) || floor($groups[$groupId][$unitId]) < 1) && $unitId != RES_ENERGY) {
          continue;
        }

        $template->assign_block_vars('group.unit', [
          'ID' => $unitId,
          'NAME' => $lang['tech'][$unitId],
          'AMOUNT' => HelperString::numberFloorAndFormat($groups[$groupId][$unitId]),
        ]);
      }
    }

    $template->assign_vars([
      'REPORT_TIME' => date(FMT_DATE_TIME, $missionReport->fleetTime),

      'TARGET_PLAYER_ID' => $missionReport->targetPlayerId,
      'TARGET_PLAYER_NAME' => $missionReport->targetPlayerName,
      'TARGET_PLAYER_ALLY_TAG' => $missionReport->targetPlayerAllyTag,

      'TARGET_PLANET_NAME' => $missionReport->targetPlanetName,
      'TARGET_PLANET_GALAXY' => $missionReport->targetPlanetGalaxy,
      'TARGET_PLANET_SYSTEM' => $missionReport->targetPlanetSystem,
      'TARGET_PLANET_PLANET' => $missionReport->targetPlanetPlanet,
      'TARGET_PLANET_TYPE' => $missionReport->targetPlanetPlanetType,
      'TARGET_PLANET_TYPE_TEXT_SH' => $lang['sys_planet_type_sh'][$missionReport->targetPlanetPlanetType],

      'SPIES_DETECTION_CHANCE' => round($missionReport->getDetectionTrashold()),
      'SPIES_DESTROYED' => $missionReport->isSpyDetected(),

      'SIMULATOR_DATA' => $missionReport->getSimulatorLink(),
    ]);

    return $template->assign_display('msg_message_spy');
  }

}
