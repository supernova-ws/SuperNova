<?php

defined('INSIDE') || die();

// Missions
/*
mission = array(
DESTINATION => EMPTY/SAME/PLAYER/ALLY
ONE_WAY => true/false, // Is it mission one-way like Relocate/Colonize?
DURATION => array(duration list  in  second)/false,  //  List  of  possible durations
AGGRESIVE => true/false, // Should aggresive trigger rise?
AJAX => true/false, // Is mission can be launch via ajax?
REQUIRE => array( // requirements for mission. Empty = any unit from sn_get_groups('fleet')
  <any unit_id> => 0 // require any number
  <any unit_id> => <number> // require at least <number>
)
*/

$sn_data[UNIT_GROUP]['missions'] = array(
  MT_ATTACK => array(
    'dst_planet' => 1,
    'dst_user'   => 1,
    'dst_fleets' => 1,
    'src_planet' => 1,
    'src_user'   => 1,
    'transport'  => false,
  ),

  MT_ACS => array(
    'dst_planet' => 1,
    'dst_user'   => 1,
    'dst_fleets' => 1,
    'src_planet' => 1,
    'src_user'   => 1,
    'transport'  => false,
  ),

  MT_DESTROY => array(
    'dst_planet' => 1,
    'dst_user'   => 1,
    'dst_fleets' => 1,
    'src_planet' => 1,
    'src_user'   => 1,
    'transport'  => false,
  ),

  MT_SPY => array(
    'dst_user'   => 1,
    'dst_planet' => 1,
    'src_user'   => 1,
    'src_planet' => 1,
    'transport'  => false,
    'AJAX'       => true,
  ),

  MT_HOLD => array(
    'dst_planet' => 0,
    'dst_user'   => 0,
    'src_planet' => 0,
    'src_user'   => 0,
    'transport'  => false,
  ),


  MT_TRANSPORT => array(
    'dst_planet' => 1,
    'dst_user'   => 0,
    'src_planet' => 1,
    'src_user'   => 0,
    'transport'  => true,
  ),

  MT_RELOCATE => array(
    'dst_planet' => 1,
    'dst_user'   => 0,
    'src_planet' => 1,
    'src_user'   => 0,
    'transport'  => true,
  ),

  MT_RECYCLE => array(
    'dst_planet' => 1,
    'dst_user'   => 0,
    'src_planet' => 0,
    'src_user'   => 0,
    'transport'  => false,
    'AJAX'       => true,
  ),

  MT_EXPLORE => array(
    'dst_planet' => 0,
    'dst_user'   => 0,
    'src_planet' => 0,
    'src_user'   => 1,
    'transport'  => false,
  ),

  MT_COLONIZE => array(
    'dst_planet'                   => 1,
    'dst_user'                     => 0,
    'src_planet'                   => 0,
    'src_user'                     => 1,
    'transport'                    => true,
    P_MISSION_PLANET_TYPE_RESTRICT => array(PT_PLANET => PT_PLANET),
  ),

  MT_MISSILE => array(
    'src_planet' => 0,
    'src_user'   => 0,
    'dst_planet' => 0,
    'dst_user'   => 0,
    'transport'  => false,
    'AJAX'       => true,
    P_MISSION_PLANET_TYPE_RESTRICT => array(PT_PLANET => PT_PLANET),
  ),
);

require_once 'vars_mission_checks.php';
