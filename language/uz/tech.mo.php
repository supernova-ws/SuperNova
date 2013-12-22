<?php

/*
#############################################################################
#  Filename: tech.mo
#  Project: SuperNova.WS
#  Website: http://www.supernova.ws
#  Description: Massive Multiplayer Online Browser Space Startegy Game
#
#  Copyright © 2009 Gorlum for Project "SuperNova.WS"
#  Copyright © 2009 MSW
#############################################################################
*/

/**
*
* @package language
* @system [Uzbekin]
* @version 38a6.0
*
*/

/**
* DO NOT CHANGE
*/

if (!defined('INSIDE')) die();


$lang = array_merge($lang, array(
  'tech_storage_max' => 'Ombor',
  'tech_storage' => 'Omborda',
  'Tech' => 'Texnologiya',
  'Requirements' => 'Talablar',
  'Metal' => 'Металл',
  'Crystal' => 'Kristal',
  'Deuterium' => 'Deyteriya',
  'Energy' => 'Energiya',
  'dark_matter' => 'To`q materiya',
  'ds' => 'Xat',
  'Message' => 'Xat',
  'level' => 'Bosqich',
  'treeinfo' => '[i]',
  'comingsoon' => 'Tezda',
  'te_dt_tx_pre' => 'Kuchsiz o`lja',
  'type_mission' => array(
    MT_ATTACK => 'Hujum',
    MT_AKS => 'Hamkorlikdagi hujum',
    MT_TRANSPORT => 'Transport',
    MT_RELOCATE => 'Bo`limni boshqatdan joylashtirish',
    MT_HOLD => 'Ushlab turish',
    MT_SPY => 'Shpionaj',
    MT_COLONIZE => 'Kolonizatsiya',
    MT_RECYCLE => 'Qayta ishlovchi',
    MT_DESTROY => 'Yo`q qilib tashlash',
    MT_MISSILE => 'Raketa xujumi',
    MT_EXPLORE => 'Ekspeditsiya',
  ),

  'tech' => array(
    UNIT_STRUCTURES => 'Qurulishlar',
    STRUC_MINE_METAL => 'Kon',
    STRUC_MINE_CRYSTAL => 'Kristall sintezatori',
    STRUC_MINE_DEUTERIUM => 'Deyteriya sentizatori',
    STRUC_MINE_SOLAR => 'Quyosh elektrostansiyasi',
    STRUC_MINE_FUSION => 'Termoyadro elektrostansiyasi',
    STRUC_FACTORY_ROBOT => 'Robotlar fabrikasi',
    STRUC_FACTORY_NANO => 'Nanofabrika',
    STRUC_FACTORY_HANGAR => 'Verf',
    STRUC_STORE_METAL => 'Ombordagi metall',
    STRUC_STORE_CRYSTAL => 'Ombordagi kristall',
    STRUC_STORE_DEUTERIUM => 'Deyteriya sig`imi',
    STRUC_LABORATORY => 'Labarotoriya',
    STRUC_TERRAFORMER => 'Terraformer',
    STRUC_ALLY_DEPOSIT => 'Ittifoq ombori',
    STRUC_LABORATORY_NANO => 'Nanolabarotoriya',

    UNIT_STRUCTURES_SPECIAL => 'Maxsus qurulishlar',
    STRUC_MOON_STATION => 'Oy bazasi',
    STRUC_MOON_PHALANX => 'Sensorli falanga',
    STRUC_MOON_GATE => 'Galaktikalararo darvoza',
    STRUC_SILO => 'Raketa shaxtasi',

    UNIT_TECHNOLOGIES => 'Texnologiyalar',
    TECH_ENERGY => 'Energetika texnologiyasi',
    TECH_COMPUTER => 'Kompyuter texnologiyasi',
    TECH_ARMOR => 'Havo kemasining zirxi',
    TECH_WEAPON => 'Qurol texnologiyasi',
    TECH_SHIELD => 'Zirx texnologiyasi',
    TECH_ENGINE_CHEMICAL => 'Kimyoviy dvigatel',
    TECH_ENGINE_ION => 'Ion dvigateli',
    TECH_ENGINE_HYPER => 'Giperfazoli dvigatel',
    TECH_LASER => 'Lazer texnologiyasi',
    TECH_ION => 'Ion texnologiyasi',
    TECH_PLASMA => 'Plazma texnologiyasi',
    TECH_HYPERSPACE => 'Giperfazoli texnologiyasi',
    TECH_SPY => 'Shpion texnologiyasi',
    TECH_EXPEDITION => 'Ekspeditsiya texnologiyasi',
    TECH_COLONIZATION => 'Kolanizatsiay texnologiyasi',
    TECH_ASTROTECH => 'Астрокартография',
    TECH_GRAVITON => 'Gravitatsiya texnologiyasi',
    TECH_RESEARCH => 'Galaktikalar aro tadqiqot tarmoq',

    UNIT_SHIPS => 'flot',
    SHIP_SATTELITE_SOLAR => 'Quyosh yo`ldoshi',
    SHIP_SPY => 'Josuslik zondi',
    SHIP_CARGO_SMALL => 'Kichik transport',
    SHIP_CARGO_BIG => 'Katta transport',
    SHIP_CARGO_SUPER => 'Supertransport',
    SHIP_CARGO_HYPER => 'Gipertransport',
    SHIP_RECYCLER => 'Qayta ishlovchi',
    SHIP_COLONIZER => 'Kolonizator',
    SHIP_FIGHTER_LIGHT => 'Yengil qiruvchi',
    SHIP_FIGHTER_HEAVY => 'Og`ir qiruvchi',
    SHIP_DESTROYER => 'Esmines',
    SHIP_CRUISER => 'Kreyser',
    SHIP_BOMBER => 'Bomberdimonchi',
    SHIP_BATTLESHIP => 'Tez yurar harbiy havo kemasi',
    SHIP_DESTRUCTOR => 'Yo`qotuvchi',
    SHIP_DEATH_STAR => 'O`lim yulduzi',
    SHIP_SUPERNOVA => 'Kreyser sinfidagi &quot;Supernova&quot;',

    UNIT_DEFENCE => 'Mudofaa',
    UNIT_DEF_TURRET_MISSILE => 'Raketa o`rnatish',
    UNIT_DEF_TURRET_LASER_SMALL => 'Yengil lazer',
    UNIT_DEF_TURRET_LASER_BIG => 'Og``ir lazer',
    UNIT_DEF_TURRET_GAUSS => 'Gauss pushkasi',
    UNIT_DEF_TURRET_ION => 'Ion quroli',
    UNIT_DEF_TURRET_PLASMA => 'Plazma quroli',
    UNIT_DEF_SHIELD_SMALL => 'Kichik gumbazli qalqon',
    UNIT_DEF_SHIELD_BIG => 'Katta gumbazli qalqon',
    UNIT_DEF_SHIELD_PLANET => 'Sayyoraviy ximoya',
    UNIT_DEF_MISSILE_INTERCEPTOR => 'Raketa -qaytargich',
    UNIT_DEF_MISSILE_INTERPLANET => 'Sayyarolar aro raketa',

    UNIT_MERCENARIES => 'Yollanma askarlar',
    MRC_STOCKMAN => 'Kargo-master',
    MRC_SPY => 'Josus',
    MRC_ACADEMIC => 'Akademik',
//    MRC_DESTRUCTOR => 'Vayron qiluvchi',
    MRC_ADMIRAL => 'Admiral',
    MRC_COORDINATOR => 'Kordinator',
    MRC_NAVIGATOR => 'Navigator',
//    MRC_ASSASIN => 'Assasin',

    UNIT_GOVERNORS => 'Gubernatorlar',
    MRC_TECHNOLOGIST => 'Texnolog',
    MRC_ENGINEER => 'Injener',
    MRC_FORTIFIER => 'Fortifikator',

    UNIT_RESOURCES => 'Resurslar',
    RES_METAL => 'Metall',
    RES_CRYSTAL => 'Kristal',
    RES_DEUTERIUM => 'Deytiriya',
    RES_ENERGY => 'Energiya',
    RES_DARK_MATTER => 'To`q Materiya',

    UNIT_ARTIFACTS => 'Artefaktlar',
    ART_LHC => 'Katta Adron Kollayderi',
    ART_RCD_SMALL  => 'Kichik АКК',
    ART_RCD_MEDIUM => 'Katta bo`lmagan АКК',
    ART_RCD_LARGE  => 'Katta АКК',
    ART_HEURISTIC_CHIP => 'Эвристический чип',
    ART_NANO_BUILDER   => 'Наностроитель',

    UNIT_PLANS => 'Chizmalar',
    UNIT_PLAN_STRUC_MINE_FUSION => '"Termoyadro elektrostansiyasi" ning chizmasi',
    UNIT_PLAN_SHIP_CARGO_SUPER => '"Supertransport" chizmasi',
    UNIT_PLAN_SHIP_CARGO_HYPER => '"Gippertransport" chizmasi',
    UNIT_PLAN_SHIP_DEATH_STAR => '"O`lim yulduzi" chizmasi',
    UNIT_PLAN_SHIP_SUPERNOVA => ' \'Kreyser "Supernova"\' chizmasi',
    UNIT_PLAN_DEF_SHIELD_PLANET => '"Sayyoraviy himoya" ning chizmasi',

    UNIT_PREMIUM => 'Premium',
    UNIT_CAPTAIN => 'Kapitan',

    UNIT_PLANET_DENSITY => 'Плотность',
  ),

));
