<?php

/**
 * Class FleetValidator
 */
class FleetValidator {
  /**
   * @var Fleet $fleet
   */
  protected $fleet;

  /**
   * FleetValidator constructor.
   *
   * @param Fleet $fleet
   */
  public function __construct($fleet) {
    $this->fleet = $fleet;
  }

  /**
   *
   */
  public function validate() {
    $checklist = sn_get_groups('mission_checks');
    try {
      // TODO - Do the restrictMission checks

      // TODO - Кое-какие проверки дают FLIGHT_ALLOWED - ЧТО НЕПРАВДА В ДАННОМ СЛУЧАЕ!!!
      // На странице 1 некоторые проверки ДОЛЖНЫ БЫТЬ опущены - иначе будет некрасиво
      // А вот здесь надо проверять много дополнительной хуйни
      $this->checkMissionRestrictions($checklist);
//pdump('passed');

      // 2nd level restrictions
      // Still cheap
//      $this->restrict2ToAllowedMissions();
//      $this->restrict2ToAllowedPlanetTypes();
    } catch (ExceptionFleetInvalid $e) {
//pdump($e->getCode(), '$e->getCode()');
//pdump($e->getMessage(), '$e->getMessage()');
      if ($e->getCode() != FLIGHT_ALLOWED) {
        pdie(classLocale::$lang['fl_attack_error'][$e->getCode()]);
      } else {
        pdump('FLIGHT_ALLOWED', FLIGHT_ALLOWED);
      }
    }
  }

  /**
   * @param array $checklist
   *
   * @throws Exception
   */
  public function checkMissionRestrictions($checklist) {
    foreach ($checklist as $condition => $action) {

      $checkResult = call_user_func(array($this, $condition));
//pdump($checkResult, $condition);

      if (is_array($action)) {
        if(!empty($action[$checkResult])) {
          $action = $action[$checkResult];
        } else {
          continue;
        }
      }

//pdump($action, $condition);


      if (is_array($action)) {
        $this->checkMissionRestrictions($action);
      } elseif (!$checkResult) {
        throw new ExceptionFleetInvalid($action, $action);
      }
    }
  }


  /**
   * @throws Exception
   */
  protected function restrict2ToAllowedMissions() {
    if (empty($this->fleet->allowed_missions[$this->fleet->mission_type])) {
      throw new Exception('FLIGHT_MISSION_IMPOSSIBLE', FLIGHT_MISSION_IMPOSSIBLE);
    }
  }

  /**
   * @throws Exception
   */
  protected function restrict2ToAllowedPlanetTypes() {
    if (empty($this->fleet->allowed_planet_types[$this->fleet->targetVector->type])) {
      throw new Exception('FLIGHT_MISSION_IMPOSSIBLE', FLIGHT_MISSION_IMPOSSIBLE);
    }
  }


  /**
   * @return bool
   */
  protected function checkSpeedPercentOld() {
    return in_array($this->fleet->oldSpeedInTens, array(10, 9, 8, 7, 6, 5, 4, 3, 2, 1));
  }

  /**
   * @return bool
   */
  protected function checkSenderNoVacation() {
    return empty($this->fleet->dbOwnerRow['vacation']) || $this->fleet->dbOwnerRow['vacation'] >= SN_TIME_NOW;
  }

  /**
   * @return bool
   */
  protected function checkTargetNoVacation() {
    return empty($this->fleet->dbTargetOwnerRow['vacation']) || $this->fleet->dbTargetOwnerRow['vacation'] >= SN_TIME_NOW;
  }

  /**
   * @return bool
   */
  protected function checkMultiAccountNot() {
    return !sys_is_multiaccount($this->fleet->dbOwnerRow, $this->fleet->dbTargetOwnerRow);
  }

  /**
   * @return bool
   */
  protected function checkTargetNotSource() {
    return !$this->fleet->targetVector->isSameLocation($this->fleet->dbSourcePlanetRow);
  }

  /**
   * @return bool
   */
  protected function checkTargetInUniverse() {
    return $this->fleet->targetVector->isInUniverse();
  }

  /**
   * @return bool
   */
  protected function checkUnitsPositive() {
    return $this->fleet->shipsAllPositive();
  }

  /**
   * @return bool
   */
  protected function checkOnlyFleetUnits() {
    return $this->fleet->shipsAllFlying();
  }

  /**
   * @return bool
   */
  protected function checkOnlyFlyingUnits() {
    return $this->fleet->shipsAllMovable();
  }

  /**
   * @return bool
   */
  protected function checkEnoughFleetSlots() {
    return FleetList::fleet_count_flying($this->fleet->getPlayerOwnerId()) < GetMaxFleets($this->fleet->dbOwnerRow);
  }


  /**
   * @return bool
   */
  protected function checkEnoughCapacity($includeResources = true) {
    $checkVia = $this->fleet->travelData['consumption'];
    $checkVia = ceil(($includeResources ? array_sum($this->fleet->resource_list) : 0) + $checkVia);

    return
      !empty($this->fleet->travelData) &&
      is_array($this->fleet->travelData) &&
      floor($this->fleet->travelData['capacity']) >= $checkVia;
  }

  /**
   * @return bool
   */
  protected function checkNotTooFar() {
    return $this->checkEnoughCapacity(false);
  }

  /**
   * @return bool
   */
  protected function checkDebrisExists() {
    return is_array($this->fleet->dbTargetRow) && ($this->fleet->dbTargetRow['debris_metal'] + $this->fleet->dbTargetRow['debris_crystal'] > 0);
  }












  // Resources checks

  /**
   * @return bool
   */
  protected function checkResourcesPositive() {
    foreach ($this->fleet->resource_list as $resourceId => $resourceAmount) {
      if ($resourceAmount < 0) {
        return false;
      }
    }

    return true;
  }

  /**
   * @return bool
   */
  protected function checkCargo() {
    return array_sum($this->fleet->resource_list) >= 1;
  }

  /**
   * @return bool
   */
  protected function checkSourceEnoughFuel() {
    $deuteriumOnPlanet = mrc_get_level($this->fleet->dbOwnerRow, $this->fleet->dbSourcePlanetRow, RES_DEUTERIUM);
    return $deuteriumOnPlanet > ceil($this->fleet->travelData['consumption']);
  }

  /**
   * @return bool
   */
  protected function checkSourceEnoughResources() {
    $fleetResources = $this->fleet->resource_list;
    $fleetResources[RES_DEUTERIUM] = ceil($fleetResources[RES_DEUTERIUM] + $this->fleet->travelData['consumption']);
    foreach ($fleetResources as $resourceId => $resourceAmount) {
      if (mrc_get_level($this->fleet->dbOwnerRow, $this->fleet->dbSourcePlanetRow, $resourceId) < ceil($fleetResources[$resourceId])) {
        return false;
      }
    }

    return true;
  }

























  // Target vector checks (????????)

  /**
   * @return bool
   */
  protected function checkKnownSpace() {
    return $this->fleet->targetVector->isInKnownSpace();
  }

  /**
   * @return bool
   */
  protected function checkTargetExists() {
    return !empty($this->fleet->dbTargetRow['id']);
  }

  /**
   * @return bool
   */
  protected function checkTargetIsPlanet() {
    return $this->fleet->targetVector->type == PT_PLANET;
  }

  /**
   * @return bool
   */
  protected function checkTargetIsDebris() {
    return $this->fleet->targetVector->type == PT_DEBRIS;
  }

  /**
   * @return bool
   */
  protected function checkTargetIsMoon() {
    return $this->fleet->targetVector->type == PT_MOON;
  }






  // Ships checks

  /**
   * @return bool
   */
  protected function checkFleetNotEmpty() {
    return $this->fleet->shipsGetTotal() >= 1;
  }


  /**
   * @return bool
   */
  protected function checkSourceEnoughShips() {
    return $this->fleet->shipsIsEnoughOnPlanet();
  }


  /**
   * @return bool
   */
  protected function checkHaveColonizer() {
    // Colonization fleet should have at least one colonizer
    return $this->fleet->shipsGetTotalById(SHIP_COLONIZER) >= 1;
  }

  /**
   * @return bool
   */
  protected function checkHaveRecyclers() {
    $recyclers = 0;
    foreach (Fleet::$snGroupRecyclers as $recycler_id) {
      $recyclers += $this->fleet->shipsGetTotalById($recycler_id);
    }

    return $recyclers >= 1;
  }

  /**
   * @return bool
   */
  protected function checkSpiesOnly() {
    return $this->fleet->shipsGetTotalById(SHIP_SPY) == $this->fleet->shipsGetTotal();
  }

  /**
   * @return bool
   */
  protected function checkNotOnlySpies() {
    return !$this->checkSpiesOnly();
  }

  /**
   * @return bool
   */
  protected function checkNoMissiles() {
    return
      $this->fleet->shipsGetTotalById(UNIT_DEF_MISSILE_INTERPLANET) == 0
      &&
      $this->fleet->shipsGetTotalById(UNIT_DEF_MISSILE_INTERCEPTOR) == 0;
  }


  /**
   * @return bool
   */
  protected function checkTargetOwn() {
    return $this->fleet->dbTargetRow['id_owner'] == $this->fleet->dbSourcePlanetRow['id_owner'];
  }

  /**
   * @return bool
   */
  protected function forceTargetOwn() {
    if ($result = $this->checkTargetOwn()) {
      unset($this->fleet->allowed_missions[MT_MISSILE]);
      unset($this->fleet->allowed_missions[MT_SPY]);

      unset($this->fleet->allowed_missions[MT_ATTACK]);
      unset($this->fleet->allowed_missions[MT_ACS]);
      unset($this->fleet->allowed_missions[MT_DESTROY]);
    } else {
      unset($this->fleet->allowed_missions[MT_RELOCATE]);
    }

    return $result;
  }

  protected function checkMissionPeaceful() {
    return
      !$this->fleet->mission_type
      ||
      in_array($this->fleet->mission_type, array(
        MT_HOLD,
        MT_RELOCATE,
        MT_TRANSPORT,
      ));
  }

  /**
   * @return bool
   */
  protected function checkTargetOther() {
    return !$this->checkTargetOwn();
  }


  /**
   * @return bool
   */
  protected function alwaysFalse() {
    return false;
  }


  /**
   * @return bool
   */
  protected function checkTargetAllyDeposit() {
    $result = mrc_get_level($this->fleet->dbTargetOwnerRow, $this->fleet->dbTargetRow, STRUC_ALLY_DEPOSIT) >= 1;
    if (!$result) {
      unset($this->fleet->allowed_missions[MT_HOLD]);
    }

    return $result;
  }


  /**
   * Check mission type OR no mission - and limits available missions to this type if positive
   *
   * @param int $missionType
   *
   * @return bool
   */
  protected function forceMission($missionType) {
    $result = !$this->fleet->mission_type || $this->fleet->mission_type == $missionType;
    if ($result) {
      $this->fleet->allowed_missions = array(
        $missionType => $this->fleet->exists_missions[$missionType],
      );
    } else {
      unset($this->fleet->allowed_missions[$missionType]);
    }

    return $result;
  }

  /**
   * @return bool
   */
  protected function forceMissionExplore() {
    return $this->forceMission(MT_EXPLORE);
  }

  /**
   * @return bool
   */
  protected function forceMissionColonize() {
    return $this->forceMission(MT_COLONIZE);
  }

  /**
   * @return bool
   */
  protected function forceMissionRecycle() {
    return $this->forceMission(MT_RECYCLE);
  }

  /**
   * @return bool
   */
  protected function forceMissionMissile() {
    return $this->forceMission(MT_MISSILE);
  }

  /**
   * Just checks mission type
   *
   * @param int $missionType
   *
   * @return bool
   */
  protected function checkMissionNonRestrict($missionType) {
    return $this->fleet->mission_type == $missionType;
  }


  /**
   * @return bool
   */
  protected function checkNotEmptyMission() {
    return !empty($this->fleet->mission_type);
  }

  /**
   * @return bool
   */
  protected function checkMissionRelocate() {
    return $this->checkMissionNonRestrict(MT_RELOCATE);
  }

  /**
   * @return bool
   */
  protected function checkMissionHoldNonUnique() {
    $result = $this->checkMissionNonRestrict(MT_HOLD);

    return $result;
  }

  /**
   * @return bool
   */
  protected function checkMissionTransport() {
    return $this->checkMissionNonRestrict(MT_TRANSPORT);
  }

  /**
   * @return bool
   */
  protected function forceMissionSpy() {
    return $this->forceMission(MT_SPY);
  }

  /**
   * @return bool
   */
  protected function checkRealFlight() {
    return $this->fleet->isRealFlight;
  }


  /**
   * @return bool
   */
  protected function unsetMissionSpyComplex() {
    unset($this->fleet->allowed_missions[MT_SPY]);
    if ($this->fleet->mission_type == MT_SPY) {
      if ($this->checkRealFlight()) {
        return false;
      }
      $this->fleet->mission_type = MT_NONE;
    }

    return true;
  }


  /**
   * @return bool
   */
  protected function checkMissionExists() {
    return !empty($this->fleet->exists_missions[$this->fleet->mission_type]);
  }

  /**
   * @return bool
   */
  protected function checkPlayerInactiveOrNotNoob() {
    return
      $this->checkTargetNotActive()
      ||
      $this->checkTargetNotNoob();
  }

  /**
   * @return bool
   */
  protected function checkTargetActive() {
    return
      empty($this->fleet->dbTargetOwnerRow['onlinetime'])
      ||
      SN_TIME_NOW - $this->fleet->dbTargetOwnerRow['onlinetime'] >= PLAYER_TIME_ACTIVE_SECONDS;
  }

  /**
   * @return bool
   */
  // TODO - REDO MAIN FUNCTION
  protected function checkTargetNotActive() {
    return !$this->checkTargetActive();
  }


  /**
   * @return bool
   */
  protected function checkSameAlly() {
    return !empty($this->fleet->dbTargetOwnerRow['ally_id']) && $this->fleet->dbTargetOwnerRow['ally_id'] == $this->fleet->dbOwnerRow['ally_id'];
  }

  /**
   * @return bool
   */
  protected function checkTargetNoob() {
    $user_points = $this->fleet->dbTargetOwnerRow['total_points'];
    $enemy_points = $this->fleet->dbTargetOwnerRow['total_points'];

    return
      // Target is under Noob Protection but Fleet owner is not
      (
        classSupernova::$config->game_noob_points
        &&
        $enemy_points <= classSupernova::$config->game_noob_points
        &&
        $user_points > classSupernova::$config->game_noob_points
      ) || (
        classSupernova::$config->game_noob_factor
        &&
        $user_points > $enemy_points * classSupernova::$config->game_noob_factor
      );
  }

  /**
   * @return bool
   */
  // TODO - REDO MAIN FUNCTION
  protected function checkTargetNotNoob() {
    return !$this->checkTargetNoob();
  }


  /**
   * @return bool
   */
  protected function checkMissionHoldReal() {
    return
      $this->checkRealFlight()
      &&
      $this->checkMissionHoldNonUnique();
  }

  /**
   * @return bool
   */
  protected function checkMissionHoldOnNotNoob() {
    return
      $this->checkTargetNotActive()
      ||
      ($this->checkSameAlly() && classSupernova::$config->ally_help_weak)
      ||
      $this->checkTargetNotNoob();
  }


  // Missiles

  /**
   * @return bool
   */
  protected function checkOnlyAttackMissiles() {
    $missilesAttack = $this->fleet->shipsGetTotalById(UNIT_DEF_MISSILE_INTERPLANET);

    return $missilesAttack != 0 && $missilesAttack == $this->fleet->shipsGetTotal();
  }

  /**
   * @return bool
   */
  protected function checkSiloLevel() {
    $sn_data_mip = get_unit_param(UNIT_DEF_MISSILE_INTERPLANET);

    return mrc_get_level($this->fleet->dbOwnerRow, $this->fleet->dbSourcePlanetRow, STRUC_SILO) >= $sn_data_mip[P_REQUIRE][STRUC_SILO];
  }

  /**
   * @return bool
   */
  protected function checkSameGalaxy() {
    return $this->fleet->targetVector->galaxy == $this->fleet->dbSourcePlanetRow['galaxy'];
  }

  /**
   * @return bool
   */
  protected function checkMissileDistance() {
    return abs($this->fleet->dbSourcePlanetRow['system'] - $this->fleet->targetVector->system) <= flt_get_missile_range($this->fleet->dbOwnerRow);
  }

  /**
   * @return bool
   */
  protected function checkMissileTarget() {
    return empty($this->fleet->targetedUnitId) || in_array($this->fleet->targetedUnitId, sn_get_groups('defense_active'));
  }


  /**
   * @return int
   */
  protected function checkExpeditionsMax() {
    return get_player_max_expeditons($this->fleet->dbOwnerRow);
  }

  /**
   * @return bool
   */
  protected function checkExpeditionsFree() {
    return get_player_max_expeditons($this->fleet->dbOwnerRow) > FleetList::fleet_count_flying($this->fleet->dbOwnerRow['id'], MT_EXPLORE);
  }

  /**
   * @return bool
   */
  protected function checkCaptainSent() {
    return $this->fleet->captainId >= 1;
  }

  /**
   * @return bool
   */
  protected function checkCaptainExists() {
    return !empty($this->fleet->captain) && is_array($this->fleet->captain);
  }

  /**
   * @return bool
   */
  protected function checkCaptainOnPlanet() {
    return $this->fleet->captain['unit_location_type'] == LOC_PLANET;
  }

  /**
   * @return bool
   */
  protected function checkCaptainNotRelocating() {
    if ($this->fleet->mission_type == MT_RELOCATE) {
      $arriving_captain = mrc_get_level($this->fleet->dbOwnerRow, $this->fleet->dbTargetRow, UNIT_CAPTAIN, true);
    } else {
      $arriving_captain = false;
    }

    return empty($arriving_captain) || !is_array($arriving_captain);
  }


  /**
   * @return bool
   */
  protected function checkMissionDestroyReal() {
    return
      $this->checkRealFlight()
      &&
      $this->checkMissionNonRestrict(MT_DESTROY);
  }

  /**
   * @return bool
   */
  protected function checkHaveReapers() {
    $unitsTyped = 0;
    foreach (sn_get_groups('flt_reapers') as $unit_id) {
      $unitsTyped += $this->fleet->shipsGetTotalById($unit_id);
    }

    return $unitsTyped >= 1;
  }


  /**
   * @return bool
   */
  protected function checkMissionACSReal() {
    return
      $this->checkRealFlight()
      &&
      $this->checkMissionNonRestrict(MT_ACS);
  }

  protected function checkACSInTime() {
    return $this->fleet->acs['ankunft'] - $this->fleet->time_launch >= $this->fleet->travelData['duration'];
  }


  protected function checkMissionRealAndSelected($missionType) {
    return
      $this->checkRealFlight()
      &&
      $this->checkMissionNonRestrict($missionType);
  }

  protected function unsetMission($missionType, $result, $restrictToMission = false) {
    if (!$result) {
      unset($this->fleet->allowed_missions[$missionType]);
    } elseif ($restrictToMission) {
      $this->fleet->allowed_missions = array(
        $missionType => $this->fleet->exists_missions[$missionType],
      );
    }
  }

  protected function checkMissionResultAndUnset($missionType, $result, $forceMission = false) {
    $this->unsetMission($missionType, $result, $forceMission);

    return $result && $this->checkMissionRealAndSelected($missionType);
  }


  /**
   * @return bool
   */
  protected function checkMissionSpyPossibleAndReal() {
    return $this->checkMissionResultAndUnset(
      MT_SPY,
      $this->checkSpiesOnly() && $this->checkTargetOther(),
      true
    );
  }

  /**
   * @return bool
   */
  protected function checkMissionDestroyAndReal() {
    return $this->checkMissionResultAndUnset(
      MT_DESTROY,
      $this->checkTargetIsMoon() && $this->checkHaveReapers()
    );
  }

  /**
   * @return bool
   */
  protected function checkMissionHoldPossibleAndReal() {
    return $this->checkMissionResultAndUnset(
      MT_HOLD,
      $this->checkTargetAllyDeposit() && $this->checkMissionHoldOnNotNoob() && $this->checkNotOnlySpies()
    );
  }

  /**
   * @return bool
   */
  protected function checkSpiesOnlyFriendlyRestrictsToRelocate() {
    if ($result = $this->checkSpiesOnly()) {
      $this->fleet->allowed_missions = array(
        MT_RELOCATE => $this->fleet->exists_missions[MT_RELOCATE],
      );
    }

    return $result;
  }


  protected function checkFleetGroupACS() {
    $result = !empty($this->fleet->group_id) && !empty($this->fleet->acs);
    $this->unsetMission(MT_ACS, $result, true);
    if ($result) {
      $this->fleet->mission_type = MT_ACS;
    } else {
      $this->fleet->group_id = 0;
    }

    return $result;
  }

  protected function checkACSNotEmpty() {
    return !empty($this->fleet->acs);
  }

  /**
   * @return bool
   */
  protected function checkACSInvited() {
    $playersInvited = !empty($this->fleet->acs['eingeladen']) ? explode(',', $this->fleet->acs['eingeladen']) : array();
    foreach($playersInvited as $playerId) {
      if(intval($playerId) == $this->fleet->dbOwnerRow['id']) {
        return true;
      }
    }

    return false;
  }

  /**
   * @return bool
   */
  protected function checkMissionACSPossibleAndReal() {
    return $this->checkMissionResultAndUnset(
      MT_ACS,
      $this->checkACSNotEmpty() && $this->checkACSInvited() && $this->checkACSInTime(),
      true
    );
  }

  /**
   * @return bool
   */
  protected function checkMissionAttack() {
    return $this->checkMissionNonRestrict(MT_ATTACK);
  }

  /**
   * @return bool
   */
  protected function checkMissionTransportPossibleAndReal() {
    return $this->checkMissionResultAndUnset(
      MT_TRANSPORT,
      $this->checkCargo() && $this->checkPlayerInactiveOrNotNoob() && $this->checkNotOnlySpies()
    );
  }

  /**
   * @return bool
   */
  protected function checkMissionTransportReal() {
    return
      $this->checkMissionTransport()
      &&
      $this->checkRealFlight();
  }








  protected function checkBashingNotRestricted() {
    return classSupernova::$config->fleet_bashing_attacks <= 0;
  }

  protected function checkBashingBothAllies() {
    return $this->fleet->dbOwnerRow['ally_id'] && $this->fleet->dbTargetOwnerRow['ally_id'];
  }

  protected function checkBashingAlliesHaveRelationWar() {
    return ali_relation($this->fleet->dbOwnerRow['ally_id'], $this->fleet->dbTargetOwnerRow['ally_id']) == ALLY_DIPLOMACY_WAR;
  }

  protected function checkBashingBothAlliesAndRelationWar() {
    return $this->checkBashingBothAllies() && $this->checkBashingAlliesHaveRelationWar();
  }

  protected function checkBashingAlliesWarNoDelay() {
    $user = $this->fleet->dbOwnerRow;
    $enemy = $this->fleet->dbTargetOwnerRow;

    $relations = ali_relations($user['ally_id'], $enemy['ally_id']);

    return SN_TIME_NOW - $relations[$enemy['ally_id']]['alliance_diplomacy_time'] > classSupernova::$config->fleet_bashing_war_delay;
  }


  protected function checkBashingNone() {
    $user = $this->fleet->dbOwnerRow;

    $time_limit = SN_TIME_NOW + $this->fleet->travelData['duration'] - classSupernova::$config->fleet_bashing_scope;
    $bashing_list = array(SN_TIME_NOW);

    // Retrieving flying fleets
    $objFleetsBashing = FleetList::dbGetFleetListBashing($user['id'], $this->fleet->dbTargetRow);
    foreach($objFleetsBashing->_container as $fleetBashing) {
      // Checking for ACS - each ACS count only once
      if($fleetBashing->group_id) {
        $bashing_list["{$user['id']}_{$fleetBashing->group_id}"] = $fleetBashing->time_arrive_to_target;
      } else {
        $bashing_list[] = $fleetBashing->time_arrive_to_target;
      }
    }

    // Check for joining to ACS - if there are already fleets in ACS no checks should be done
    if($this->fleet->mission_type == MT_ACS && $bashing_list["{$user['id']}_{$this->fleet->group_id}"]) {
      return true;
    }

    $query = DBStaticFleetBashing::db_bashing_list_get($user, $this->fleet->dbTargetRow, $time_limit);
    while($bashing_row = db_fetch($query)) {
      $bashing_list[] = $bashing_row['bashing_time'];
    }

    sort($bashing_list);

    $last_attack = 0;
    $wave = 0;
    $attack = 1;
    foreach($bashing_list as &$bash_time) {
      $attack++;
      if(
        $bash_time - $last_attack > classSupernova::$config->fleet_bashing_interval
        ||
        $attack > classSupernova::$config->fleet_bashing_attacks
      ) {
        $wave++;
        $attack = 1;
      }

      $last_attack = $bash_time;
    }

    return $wave <= classSupernova::$config->fleet_bashing_waves;
  }

}
