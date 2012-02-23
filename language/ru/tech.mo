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
* @system [Russian]
* @version 31a13
*
*/

/**
* DO NOT CHANGE
*/

if (!defined('INSIDE')) die();


$lang = array_merge($lang, array(
  'tech_storage_max' => 'Хранилища',
  'tech_storage' => 'В хранилищах',
  'Tech' => 'Технология',
  'Requirements' => 'Требуется',
  'Metal' => 'Металл',
  'Crystal' => 'Кристалл',
  'Deuterium' => 'Дейтерий',
  'Energy' => 'Энергия',
  'dark_matter' => 'Темная Материя',
  'ds' => 'Сообщения',
  'Message' => 'Сообщения',
  'level' => 'Уровень',
  'treeinfo' => '[i]',
  'comingsoon' => 'Скоро',
  'te_dt_tx_pre' => 'Слабая добыча',
  'type_mission' => array(
    MT_ATTACK => 'Атака',
    MT_AKS => 'Совместная атака',
    MT_TRANSPORT => 'Транспорт',
    MT_RELOCATE => 'Передислокация',
    MT_HOLD => 'Удержание',
    MT_SPY => 'Шпионаж',
    MT_COLONIZE => 'Колонизация',
    MT_RECYCLE => 'Переработать',
    MT_DESTROY => 'Уничтожение',
    MT_MISSILE => 'Ракетная атака',
    MT_EXPLORE => 'Экспедиция',
  ),

  'tech' => array(
    STRUC_STRUCTURES => 'Постройки',
    STRUC_MINE_METAL => 'Рудник',
    STRUC_MINE_CRYSTAL => 'Синтезатор кристаллов',
    STRUC_MINE_DEUTERIUM => 'Синтезатор дейтерия',
    STRUC_MINE_SOLAR => 'Солнечная электростанция',
    STRUC_MINE_FUSION => 'Термоядерная электростанция',
    STRUC_FACTORY_ROBOT => 'Фабрика роботов',
    STRUC_FACTORY_NANO => 'Нанофабрика',
    STRUC_FACTORY_HANGAR => 'Верфь',
    STRUC_STORE_METAL => 'Хранилище металла',
    STRUC_STORE_CRYSTAL => 'Хранилище кристаллов',
    STRUC_STORE_DEUTERIUM => 'Емкость для дейтерия',
    STRUC_LABORATORY => 'Лаборатория',
    STRUC_TERRAFORMER => 'Терраформер',
    STRUC_ALLY_DEPOSIT => 'Склад альянса',
    STRUC_LABORATORY_NANO => 'Нанолаборатория',

    STRUC_STRUCTURES_SPECIAL => 'Особые постройки',
    STRUC_MOON_STATION => 'Лунная база',
    STRUC_MOON_PHALANX => 'Сенсорная фаланга',
    STRUC_MOON_GATE => 'Межгалактические врата',
    STRUC_SILO => 'Ракетная шахта',

    TECH_TECHNOLOGY => 'Технологии',
    TECH_ENERGY => 'Энергетическая технология',
    TECH_COMPUTER => 'Компьютерная технология',
    TECH_ARMOR => 'Броня космических кораблей',
    TECH_WEAPON => 'Оружейная технология',
    TECH_SHIELD => 'Щитовая технология',
    TECH_ENGINE_CHEMICAL => 'Химический двигатель',
    TECH_ENIGNE_ION => 'Ионный двигатель',
    TECH_ENGINE_HYPER => 'Гиперпространственный двигатель',
    TECH_LASER => 'Лазерная технология',
    TECH_ION => 'Ионная технология',
    TECH_PLASMA => 'Плазменная технология',
    TECH_HYPERSPACE => 'Гиперпространственная технология',
    TECH_SPY => 'Технология шпионажа',
    TECH_EXPEDITION => 'Экспедиционная технология',
    TECH_COLONIZATION => 'Колонизационная технология',
    TECH_GRAVITON => 'Гравитационная технология',
    TECH_RESEARCH => 'Межгалактическая исследовательская сеть',
    SHIP_FLEET => 'Флот',
    SHIP_SATTELITE_SOLAR => 'Солнечный спутник',
    SHIP_SPY => 'Шпионский зонд',
    SHIP_CARGO_SMALL => 'Малый транспорт',
    SHIP_CARGO_BIG => 'Большой транспорт',
    SHIP_CARGO_SUPER => 'Супертранспорт',
    SHIP_CARGO_HYPER => 'Гипертранспорт',
    SHIP_RECYCLER => 'Переработчик',
    SHIP_COLONIZER => 'Колонизатор',
    SHIP_FIGHTER_LIGHT => 'Лёгкий истребитель',
    SHIP_FIGHTER_HEAVY => 'Тяжёлый истребитель',
    SHIP_DESTROYER => 'Эсминец',
    SHIP_CRUISER => 'Крейсер',
    SHIP_BOMBER => 'Бомбардировщик',
    SHIP_BATTLESHIP => 'Линейный крейсер',
    SHIP_DESTRUCTOR => 'Уничтожитель',
    SHIP_DEATH_STAR => 'Звезда смерти',
    SHIP_SUPERNOVA => 'Крейсер класса &quot;Сверхновая&quot;',
    '400' => 'Оборона',
    '401' => 'Ракетная установка',
    '402' => 'Легкий лазер',
    '403' => 'Тяжёлый лазер',
    '404' => 'Пушка Гаусса',
    '405' => 'Ионное орудие',
    '406' => 'Плазменное орудие',
    '407' => 'Малый щитовой купол',
    '408' => 'Большой щитовой купол',
    '409' => 'Планетарная защита',
    '502' => 'Ракета-перехватчик',
    '503' => 'Межпланетная ракета',
    MRC_MERCENARIES => 'Наемники',
    MRC_TECHNOLOGIST => 'Технолог',
    MRC_ENGINEER => 'Инженер',
    MRC_FORTIFIER => 'Фортификатор',
    MRC_STOCKMAN => 'Карго-мастер',
    MRC_SPY => 'Шпион',
    MRC_ACADEMIC => 'Академик',
    MRC_DESTRUCTOR => 'Разрушитель',
    MRC_ADMIRAL => 'Адмирал',
    MRC_COORDINATOR => 'Координатор',
    MRC_NAVIGATOR => 'Навигатор',
    MRC_ASSASIN => 'Ассасин',
    RES_RESOURCES => 'Ресурсы',
    RES_METAL => 'Металл',
    RES_CRYSTAL => 'Кристалл',
    RES_DEUTERIUM => 'Дейтерий',
    RES_ENERGY => 'Энергия',
    RES_DARK_MATTER => 'Темная Материя',

    ART_ARTIFACTS => 'Артефакты',
    ART_LHC => 'Большой Адронный Коллайдер',
    ART_RCD_SMALL  => 'Малый АКК',
    ART_RCD_MEDIUM => 'Средний АКК',
    ART_RCD_LARGE  => 'Большой АКК',
  ),

));

?>
