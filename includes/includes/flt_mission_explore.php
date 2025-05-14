<?php

use Fleet\MissionExploreResult;

/**
 * @param MissionExploreResult $outcome
 *
 * @return MissionExploreResult
 * @see MissionExploreResult::flt_mission_explore_addon()
 *
 * @deprecated
 */
function sn_flt_mission_explore_addon(MissionExploreResult $outcome) {
  return $outcome;
}
