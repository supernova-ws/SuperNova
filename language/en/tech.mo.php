<?php

/*
#############################################################################
#  Filename: tech.mo
#  Project: SuperNova.WS
#  Website: http://www.supernova.ws
#  Description: Massive Multiplayer Online Browser Space Startegy Game
#
#  Copyright © 2011 madmax1991 for Project "SuperNova.WS"
#  Copyright © 2009 Gorlum for Project "SuperNova.WS"
#  Copyright © 2008 Aleksandar Spasojevic <spalekg@gmail.com>
#  Copyright © 2005 - 2008 KGsystem
#############################################################################
*/

/**
*
* @package language
* @system [English]
* @version 34a15
*
*/

/**
* DO NOT CHANGE
*/

if (!defined('INSIDE')) die();

$lang = array_merge($lang, array(
  'tech_storage_max' => 'Storage',
  'tech_storage' => 'In storage',
  'Tech' => 'Technology',
  'Requirements' => 'Requirements',
  'Metal' => 'Metal',
  'Crystal' => 'Crystal',
  'Deuterium' => 'Deuterium',
  'Energy' => 'Energy',
  'dark_matter' => 'Dark matter',
  'ds' => 'Messages',
  'Message' => 'Messages',
  'level' => 'Level',
  'treeinfo' => '[i]',
  'comingsoon' => 'Coming Soon',
  'te_dt_tx_pre' => 'Weak production',
  'type_mission' => array(
    MT_ATTACK => 'Attack',
    MT_AKS => 'Joint Attack',
    MT_TRANSPORT => 'Transport',
    MT_RELOCATE => 'Deployment',
    MT_HOLD => 'Retention',
    MT_SPY => 'Espionage',
    MT_COLONIZE => 'Colonization',
    MT_RECYCLE => 'Rework',
    MT_DESTROY => 'Destruction',
    MT_MISSILE => 'Missile attack',
    MT_EXPLORE => 'Expedition',
  ),

  'tech' => array(
    UNIT_STRUCTURES => 'Buildings',
    STRUC_MINE_METAL => 'Metal mine',
    STRUC_MINE_CRYSTAL => 'Crystal Mine',
    STRUC_MINE_DEUTERIUM => 'Deuterium Synthesizer',
    STRUC_MINE_SOLAR => 'Solar Plant',
    STRUC_MINE_FUSION => 'Fusion Reactor',
    STRUC_FACTORY_ROBOT => 'Robotics Factory',
    STRUC_FACTORY_NANO => 'Nanite Factory',
    STRUC_FACTORY_HANGAR => 'Shipyard',
    STRUC_STORE_METAL => 'Metal Storage',
    STRUC_STORE_CRYSTAL => 'Crystal Storage',
    STRUC_STORE_DEUTERIUM => 'Deuterium Tank',
    STRUC_LABORATORY => 'Research Lab',
    STRUC_TERRAFORMER => 'Terraformer',
    STRUC_ALLY_DEPOSIT => 'Alliance Depot',
    STRUC_LABORATORY_NANO => 'Special Building',

    UNIT_STRUCTURES_SPECIAL => 'Moon Buildings',
    STRUC_MOON_STATION => 'Moon Base',
    STRUC_MOON_PHALANX => 'Sensor Phalanx',
    STRUC_MOON_GATE => 'Stargate',
    STRUC_SILO => 'Missile Silo',

    UNIT_TECHNOLOGIES => 'Technologies',
    TECH_ENERGY => 'Energy Technology',
    TECH_COMPUTER => 'Computer Technology',
    TECH_SPY => 'Espionage Technology',
    TECH_ARMOR => 'Armor Technology',
    TECH_WEAPON => 'Weapons Technology',
    TECH_SHIELD => 'Shielding Technology',
    TECH_ENGINE_CHEMICAL => 'Rocket Engine',
    TECH_ENIGNE_ION => 'Impulse Engine',
    TECH_ENGINE_HYPER => 'Hyperspace Drive',
    TECH_LASER => 'Laser Technology',
    TECH_ION => 'Ion Technology',
    TECH_PLASMA => 'Plasma Technology',
    TECH_HYPERSPACE => 'Hyperspace Technology',
    TECH_EXPEDITION => 'Expedition Technology',
    TECH_COLONIZATION => 'Colonization Technology',
    TECH_GRAVITON => 'Graviton Technology',
    TECH_RESEARCH => 'Intergalactic Research Network',
    UNIT_SHIPS => 'Ships',
    SHIP_SATTELITE_SOLAR => 'Solar Satellite',
    SHIP_SPY => 'Spy Probe',
    SHIP_CARGO_SMALL => 'Small Cargo',
    SHIP_CARGO_BIG => 'Large Cargo',
    SHIP_CARGO_SUPER => 'Super Cargo',
    SHIP_CARGO_HYPER => 'Hypercargo',
    SHIP_RECYCLER => 'Recycler',
    SHIP_COLONIZER => 'Colony Ship',
    SHIP_FIGHTER_LIGHT => 'Light Fighter',
    SHIP_FIGHTER_HEAVY => 'Heavy Fighter',
    SHIP_DESTROYER => 'Destroyer',
    SHIP_CRUISER => 'Cruiser',
    SHIP_BOMBER => 'Bomber',
    SHIP_BATTLESHIP => 'Battleship',
    SHIP_DESTRUCTOR => 'Destructor',
    SHIP_DEATH_STAR => 'Deathstar',
    SHIP_SUPERNOVA => 'Cruiser Class &quot;Supernova&quot;',
    '400' => 'Defences',
    '401' => 'Rocket Launcher',
    '402' => 'Light Laser',
    '403' => 'Heavy Laser',
    '404' => 'Gauss Cannon',
    '405' => 'Ion Cannon',
    '406' => 'Plasma Turrent',
    '407' => 'Small Shield Dome',
    '408' => 'Large Shield Dome',
    '409' => 'Planetary Protection',
    '502' => 'Interceptor Missiles',
    '503' => 'Interplanetary Missiles',

    UNIT_MERCENARIES => 'Mercenaries',
    MRC_TECHNOLOGIST => 'Technologist',
    MRC_ENGINEER => 'Engineer',
    MRC_FORTIFIER => 'Fortifier',
    MRC_STOCKMAN => 'Cargo Master',
    MRC_SPY => 'Spy',
    MRC_ACADEMIC => 'Academician',
//    MRC_DESTRUCTOR => 'Destroyer',
    MRC_ADMIRAL => 'Admiral',
    MRC_COORDINATOR => 'Coordinator',
    MRC_NAVIGATOR => 'Navigator',
//    MRC_ASSASIN => 'Assassin',

    UNIT_RESOURCES => 'Resources',
    RES_METAL => 'Metal',
    RES_CRYSTAL => 'Crystal',
    RES_DEUTERIUM => 'Deuterium',
    RES_ENERGY => 'Energy',
    RES_DARK_MATTER => 'Dark Matter',

    UNIT_ARTIFACTS => 'Artifacts',
    ART_LHC => 'Large Hadron Collider',
    ART_RCD_SMALL => 'Small RCD',
    ART_RCD_MEDIUM => 'Medium RCD',
    ART_RCD_LARGE => 'Large RCD',

    UNIT_PLANS => 'Schematics',
    UNIT_PLAN_STRUC_MINE_FUSION => 'Schematic "Thermonuclear plant"',
    UNIT_PLAN_SHIP_CARGO_SUPER => 'Schematic "Supertransport"',
    UNIT_PLAN_SHIP_CARGO_HYPER => 'Schematic "Hyperstransport"',
    UNIT_PLAN_SHIP_DEATH_STAR => 'Schematic "Death Star"',
    UNIT_PLAN_SHIP_SUPERNOVA => 'Schematic \'"Supernova" cruiser\'',
    UNIT_PLAN_DEF_SHIELD_PLANET => 'Schematic "Planet defense"',

  ),

));

?>
