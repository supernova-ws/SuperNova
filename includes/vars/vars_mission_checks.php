<?php

defined('INSIDE') || die();

// Here listed methods of Fleet, naturally ordered by priorities
// Each mission will filter only necessary checks and do it in this order

$sn_data[UNIT_GROUP]['mission_checks'] = array(
  // Cheap checks - class Fleet already have all this info internally
  'checkSpeedPercentOld'       => FLIGHT_FLEET_SPEED_WRONG,
  'checkTargetInUniverse'      => FLIGHT_VECTOR_BEYOND_UNIVERSE,
  'checkTargetNotSource'       => FLIGHT_VECTOR_SAME_SOURCE,
  'checkSenderNoVacation'      => FLIGHT_PLAYER_VACATION_OWN,  // tODO
  'checkTargetNoVacation'      => FLIGHT_PLAYER_VACATION,
  'checkFleetNotEmpty'         => FLIGHT_SHIPS_NO_SHIPS,
  // 'checkUnitsPositive'         => FLIGHT_SHIPS_NEGATIVE, // Unused - 'cause it's not allowed to put negative units into Unit class
  // 'checkOnlyFleetUnits'        => FLIGHT_SHIPS_UNIT_WRONG, // Unused - 'cause it's only possible to pass to fleet SHIP or RESOURCE
  'checkOnlyFlyingUnits'       => FLIGHT_SHIPS_UNMOVABLE,
  'checkResourcesPositive'     => FLIGHT_RESOURCES_NEGATIVE,
  'checkNotTooFar'             => FLIGHT_FLEET_TOO_FAR,
  'checkEnoughCapacity'        => FLIGHT_FLEET_OVERLOAD,

  // checkMissionAllowed
  // consumptionIsNegative ??????

  // Medium checks - currently requires access to DB but potentially doesn't
  'checkSourceEnoughShips'     => FLIGHT_SHIPS_NOT_ENOUGH,
  'checkSourceEnoughFuel'      => FLIGHT_RESOURCES_FUEL_NOT_ENOUGH,
  'checkSourceEnoughResources' => FLIGHT_RESOURCES_NOT_ENOUGH,

  'checkMultiAccountNot'            => FLIGHT_PLAYER_SAME_IP,

  // Heavy checks - will absolutely require DB access
  'checkEnoughFleetSlots'           => FLIGHT_FLEET_NO_SLOTS,

  // TODO - THIS CHECKS SHOULD BE ADDED IN UNIT_CAPTAIN MODULE!
  'checkCaptainSent'                => array(
    true => array(
      'checkCaptainExists'         => FLIGHT_CAPTAIN_NOT_HIRED,
      'checkCaptainOnPlanetType'   => FLIGHT_CAPTAIN_ALREADY_FLYING,
      'checkCaptainOnPlanetSource' => FLIGHT_CAPTAIN_ON_OTHER_PLANET,
      'checkCaptainNotRelocating'  => FLIGHT_CAPTAIN_RELOCATE_LOCK,
    ),
  ),

  // Forcing MT_ACS if group presents and ACS record exists
  // TODO - REWRITE
  'checkFleetGroupACS'              => array(),

  // Vector targeting space beyond MaxPlanet forces MT_EXPLORE mission
  'checkKnownSpace'                 => array(
    false => array(
      // However in Explore can't be send mission with missiles or mission with only spies in fleet
      'checkNotOnlySpies'    => FLIGHT_SHIPS_NOT_ONLY_SPIES,
      'checkNoMissiles'      => FLIGHT_SHIPS_NO_MISSILES,
      'checkExpeditionsMax'  => FLIGHT_MISSION_EXPLORE_NO_ASTROTECH,
      'checkExpeditionsFree' => FLIGHT_MISSION_EXPLORE_NO_SLOTS,

      // TODO - COMPACT????
      'checkRealFlight'  => array(
        true  => array(
          'checkMissionExactExplore' => array(
            true  => FLIGHT_ALLOWED,
            false => FLIGHT_VECTOR_BEYOND_SYSTEM,
          ),
        ),
        false  => array(
          'checkMissionExplore' => array(
            true  => FLIGHT_ALLOWED,
            false => FLIGHT_VECTOR_BEYOND_SYSTEM,
          ),
        ),
      ),
    ),
    // Removing mission MT_EXPLORE from list of available missions if we flying to known space
    true  => array(
      'unsetMissionExplore' => array(),
    ),
  ),
  // Beyond this point all missions goes to known space

  // Vector targeting non-exists point forces MT_COLONIZE mission
  'checkTargetExists'               => array(
    // If target not exists - it can be only Colonize mission
    false => array(
      'checkHaveColonizer'   => FLIGHT_SHIPS_NO_COLONIZER, // Replaces checkNotOnlySpies
      'checkNoMissiles'      => FLIGHT_SHIPS_NO_MISSILES,
      'checkTargetIsPlanet'  => FLIGHT_MISSION_COLONIZE_NOT_PLANET,
      'forceMissionColonize' => array(
        true  => FLIGHT_ALLOWED,
        false => FLIGHT_VECTOR_NO_TARGET,
      ),
    ),
    true  => array(
      // Removing mission MT_COLONIZE from list of available missions
      'unsetMissionColonize' => array(),
    ),
  ),
  // Beyond this point all missions goes to existing locations

  // Fleet from only attack missiles forces MT_MISSILE mission
  // TODO - check it on real missile attack
  'checkOnlyAttackMissiles'         => array(
    true  => array(
      // For now missile self-attack is enabled. By adding checkTargetOther it can be disabled again
      'checkSameGalaxy'              => FLIGHT_MISSION_MISSILE_DIFFERENT_GALAXY,
      'checkTargetIsPlanet'          => FLIGHT_MISSION_MISSILE_ONLY_PLANET,
      'checkMissileDistance'         => FLIGHT_MISSION_MISSILE_TOO_FAR,
      'checkSiloLevel'               => FLIGHT_MISSION_MISSILE_NO_SILO,
      'checkMissileTarget'           => FLIGHT_MISSION_MISSILE_WRONG_STRUCTURE,
      // Check target player activity
      'checkPlayerInactiveOrNotNoob' => FLIGHT_PLAYER_NOOB,
      // FLIGHT_MISSION_MISSILE_NO_MISSILES
      'forceMissionMissile'          => array(
        true  => FLIGHT_ALLOWED,
        false => FLIGHT_SHIPS_ONLY_MISSILES,
      ),
    ),
    false => array(
      // Removing mission MT_MISSILE from list of available missions
      'unsetMissionMissile' => array(),
    ),
  ),
  'checkNoMissiles'                 => FLIGHT_SHIPS_NO_MISSILES,
  // Beyond this point fleet doesn't have missiles

  // Vector targeting Debris forces MT_RECYCLE mission
  'checkTargetIsDebris'             => array(
    true  => array(
      // Recycle mission checks
      'checkHaveRecyclers'  => FLIGHT_SHIPS_NO_RECYCLERS, // Replaces checkNotOnlySpies
      'checkDebrisExists'   => FLIGHT_MISSION_RECYCLE_NO_DEBRIS,
      'forceMissionRecycle' => array(
        true  => FLIGHT_ALLOWED,
        false => FLIGHT_VECTOR_TARGET_DEBRIS,
      ),
    ),
    false => array(
      // Removing mission MT_RECYCLE from list of available missions
      'unsetMissionRecycle' => array(),
    ),

  ),
  // Beyond this point all missions targets MT_PLANET or MT_MOON

  // ''

  // Targeting self limits mission to one of the following: RELOCATE, HOLD, TRANSPORT, RECYCLE
  // However - RECYCLE was processed above
  'forceTargetOwn'                  => array(
    // Missions target same player
    true  => array(
      'checkMissionPeaceful' => FLIGHT_PLAYER_ATTACK_SELF,

      // Allow here missions filtered above if no RealFlight flag set. It means that we on page 2
      'checkRealFlight'      => FLIGHT_ALLOWED,

      // TODO - check real flight beyond this point

      // We on page 3 - so no empty missions here
      'checkNotEmptyMission' => FLIGHT_MISSION_UNKNOWN,

      'checkMissionAllowed'       => FLIGHT_MISSION_IMPOSSIBLE, // TODO - should never seen really

      // Processing MT_RELOCATE
      'checkMissionExactRelocate' => array(
        true => FLIGHT_ALLOWED,
      ),

      'checkNotOnlySpies'          => FLIGHT_SHIPS_NOT_ONLY_SPIES,

      // Processing MT_HOLD
      'checkMissionExactHold'      => array(
        true => array(
          // Check for Ally Deposit
          'checkTargetAllyDeposit' => array(
            true  => FLIGHT_ALLOWED,
            false => FLIGHT_MISSION_HOLD_NO_ALLY_DEPOSIT,
          ),
        ),
      ),

      // Processing MT_TRANSPORT
      'checkMissionExactTransport' => array(
        true => array(
          'checkCargo'  => FLIGHT_MISSION_TRANSPORT_EMPTY_CARGO,
          'alwaysFalse' => FLIGHT_ALLOWED,
        ),
      ),

      // All OWN mission was processed above. It's means that we got here with error
      // TODO - this error messages as well as checks for non-empty mission and allowed mission should be done in renderers
      'checkMissionExists'         => array(
        true  => FLIGHT_MISSION_IMPOSSIBLE,
        false => FLIGHT_MISSION_UNKNOWN,
      ),
    ),
    false => array(
      // Removing mission MT_COLONIZE from list of available missions
      'unsetMissionRelocate' => array(),
    ),
  ),
  // Past this point missions target only other players


  // TODO So far so good

  // Checking for fleet from only spies
  'checkNotOnlySpies'               => array(
    // Fleet from only spies forces MT_SPY
    false => array(
      'checkRealFlight' => array(
        true  => array(
          'checkMissionExactSpy' => array(
            true  => FLIGHT_ALLOWED,
            false => FLIGHT_SHIPS_NOT_ONLY_SPIES,
          ),
        ),
        false => array(
          'forceMissionSpy' => array(
            true  => FLIGHT_ALLOWED,
            false => FLIGHT_SHIPS_NOT_ONLY_SPIES,
          ),
        ),
      ),
    ),

    // Fleet with spies and other ships can't fly to MT_SPY
    true  => array(
      'unsetMissionSpy' => array(),
    ),
  ),
  // Beyond this point fleet can't contain ONLY spies


  //      'unsetMissionExplore' => array(),
  //      'unsetMissionColonize' => array(),
  //      'unsetMissionMissile' => array(),
  //      'unsetMissionRecycle' => array(),
  //      'unsetMissionRelocate' => array(),
  //      'unsetMissionSpy' => array(),

  // If it is Real flight - removing all missions except selected one (or zero)
  'checkRealFlight'                 => array(
    true  => array(
      'forceMissionExactTransport' => array(
        // Mission is Real Transport
        true => array(
          'checkPlayerInactiveOrNotNoob' => FLIGHT_PLAYER_NOOB,
          'checkCargo'                   => FLIGHT_MISSION_TRANSPORT_EMPTY_CARGO,
          // TODO - additional check to block Buffing?
          'alwaysFalse'                  => FLIGHT_ALLOWED,
        ),
      ),

      // TODO - check for moratorium

      'forceMissionExactHold' => array(
        true => array(
          'checkTargetAllyDeposit'    => FLIGHT_MISSION_HOLD_NO_ALLY_DEPOSIT, // No Deposit - it's a TRAP!
          'checkMissionHoldOnNotNoob' => FLIGHT_MISSION_HOLD_ON_NOOB,
          'alwaysFalse'               => FLIGHT_ALLOWED,
        ),
      ),

      // Aggressive missions begins here

      'forceMissionExactAttack'  => array(),
      'forceMissionExactAcs'     => array(),
      'forceMissionExactDestroy' => array(),

      'checkMissionAllowed'  => FLIGHT_MISSION_IMPOSSIBLE,
      'checkNotEmptyMission' => FLIGHT_MISSION_UNKNOWN,
    ),
    false => array(
      'checkPlayerInactiveOrNotNoob' => array(
        false => array(
          'unsetMissionTransport' => array(),
          'unsetMissionHold'      => array(),
          'unsetMissionAttack'    => array(),
          'unsetMissionAcs'       => array(),
          'unsetMissionDestroy'   => array(),
        ),
      ),

      'checkMissionTransport' => array(
        true => array(// TODO - additional check to block Buffing?
        ),
      ),

      // TODO - check for moratorium

      'checkMissionHold' => array(
        true => array(),
      ),

      // Aggressive missions begins here

      'checkMissionAttack'  => array(
        true => array(),
      ),
      'checkMissionAcs'     => array(
        true => array(),
      ),
      'checkMissionDestroy' => array(
        true => array(),
      ),
    ),

    'checkMissionAllowed' => FLIGHT_MISSION_IMPOSSIBLE,
  ),


//define('MT_TRANSPORT',  3);
//define('MT_HOLD'     ,  5);
//define('MT_ATTACK'   ,  1);
//define('MT_ACS'      ,  2);
//define('MT_DESTROY'  ,  9);
//      // TODO - REWRITE!!!!!!!!
//      'checkMissionTransportPossibleAndReal' => array(
//        true  => FLIGHT_ALLOWED,
//        false => array(
//          'checkRealFlight' => array(
//            true  => array(
//              'checkMissionTransportReal' => array(
//                true => array(
//                  'checkCargo'                   => FLIGHT_MISSION_TRANSPORT_EMPTY_CARGO,
//                  'checkPlayerInactiveOrNotNoob' => FLIGHT_PLAYER_NOOB,
//                  // TODO - additional check to block Buffing?
//                  'alwaysFalse'                  => FLIGHT_INTERNAL_ERROR,
//                ),
//              ),
//            ),
//            false => array(),
//          ),
//        ),
//        // Relocate
//        // TODO - WHAAAAAAAAAAAAAT????
////        'checkMissionExactRelocate' => array(
////          // No additional checks
////          true => FLIGHT_ALLOWED,
////        ),
//      ),


//define('MT_HOLD'     ,  5);
//define('MT_ATTACK'   ,  1);
//define('MT_ACS'      ,  2);
//define('MT_DESTROY'  ,  9);


  //
  //
  //
  //
  //
  //


  // TODO - later checks
  // NOPE! All this checks should be done on validators/renderer
  'checkMissionAllowed'             => FLIGHT_MISSION_IMPOSSIBLE,


//      // TODO - REWRITE!!!!!!!!
//      'checkMissionTransportPossibleAndReal' => array(
//        true  => FLIGHT_ALLOWED,
//        false => array(
//          'checkRealFlight' => array(
//            true  => array(
//              'checkMissionTransportReal' => array(
//                true => array(
//                  'checkNotOnlySpies'            => FLIGHT_SHIPS_NOT_ONLY_SPIES,
//                  'checkCargo'                   => FLIGHT_MISSION_TRANSPORT_EMPTY_CARGO,
//                  'checkPlayerInactiveOrNotNoob' => FLIGHT_PLAYER_NOOB,
//                  // TODO - additional check to block Buffing?
//                  'alwaysFalse'                  => FLIGHT_INTERNAL_ERROR,
//                ),
//              ),
//            ),
//            false => array(),
//          ),
//        ),
//        // Relocate
//        // TODO - WHAAAAAAAAAAAAAT????
////        'checkMissionExactRelocate' => array(
////          // No additional checks
////          true => FLIGHT_ALLOWED,
////        ),
//      ),


  // TODO - REWRITE!!!!!!!!
  // If HOLD is selected AND it is real mission (page 3)...
  'checkMissionHoldPossibleAndReal' => array(
    // Mission possible and it's real flight and mission selected
    true  => FLIGHT_ALLOWED,
    false => array(
      'checkRealFlight' => array( // If it is real flight - we need to say something about conditions
        true => array(
          'checkTargetAllyDeposit'    => FLIGHT_MISSION_HOLD_NO_ALLY_DEPOSIT, // No Deposit - it's a TRAP!
          'checkMissionHoldOnNotNoob' => FLIGHT_MISSION_HOLD_ON_NOOB,
          // HOLD/TRANSPORT with only spies in fleet should be prevented
          'checkNotOnlySpies'         => FLIGHT_SHIPS_NOT_ONLY_SPIES,
        ),
      ),
    ),
  ),
  // Past this point no HOLD mission can be present in real flight


  // Check for multiaccount
  'checkMultiAccountNot'            => FLIGHT_PLAYER_SAME_IP,
  // TODO - check for moratorium

  // Noob check
  'checkPlayerInactiveOrNotNoob'    => FLIGHT_PLAYER_NOOB,
  //


  // Bashing check
  'checkBashingNotRestricted'       => array(
    false => array(
      'checkBashingBothAlliesAndRelationWar' => array(
        true => array(
          'checkBashingAlliesWarNoDelay' => array(
            true  => FLIGHT_ALLOWED,
            false => FLIGHT_MISSION_ATTACK_BASHING_WAR_DELAY,
          ),
        ),
      ),
      'checkBashingNone'                     => FLIGHT_MISSION_ATTACK_BASHING,
    ),
  ),

  // MT_DESTROY
  'checkMissionDestroyAndReal'      => array(
    // Mission possible and it's real flight and mission selected
    true  => FLIGHT_ALLOWED,
    false => array(
      // If it is real flight - we need to say something about conditions
      'checkRealFlight' => array(
        true => array(
          'checkTargetIsMoon' => FLIGHT_MISSION_DESTROY_NOT_MOON,
          'checkHaveReapers'  => FLIGHT_MISSION_DESTROY_NO_REAPERS,
        ),
      ),
    ),
  ),

  // Multicheck - does mission MT_ACS is ever possible ?
  'checkMissionACSPossibleAndReal'  => array(
    // Mission possible and it's real flight and mission selected
    true  => FLIGHT_ALLOWED,
    // Otherwise...
    false => array(
      // If it is real flight - we need to say something about conditions
      'checkRealFlight' => array(
        true => array(
          'checkACSNotEmpty' => FLIGHT_MISSION_ACS_NOT_EXISTS,
          'checkACSInvited'  => FLIGHT_MISSION_ACS_NOT_INVITED,
          'checkACSInTime'   => FLIGHT_MISSION_ACS_TOO_LATE,
        ),
      ),
    ),
  ),

  // MT_ATTACK - no checks
  'checkMissionExactAttack'         => array(
    // Mission possible and it's real flight and mission selected
    true => FLIGHT_ALLOWED,
  ),
  // If it is real flight - we need to say something about conditions
//      'checkRealFlight'                 => array(
//        true => array(
//          'checkMissionExists' => array(
//            true  => FLIGHT_MISSION_IMPOSSIBLE,
//            false => FLIGHT_MISSION_UNKNOWN,
//          ),
//        ),
//      ),
);

