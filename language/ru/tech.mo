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
  'tech_storage_max' => 'Размер хранилища',
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
    '0' => 'Постройки',
    '1' => 'Рудник',
    '2' => 'Синтезатор кристаллов',
    '3' => 'Синтезатор дейтерия',
    '4' => 'Солнечная электростанция',
    '12' => 'Термоядерная электростанция',
    '14' => 'Фабрика роботов',
    '15' => 'Нанофабрика',
    '21' => 'Верфь',
    '22' => 'Хранилище металла',
    '23' => 'Хранилище кристаллов',
    '24' => 'Емкость для дейтерия',
    '31' => 'Лаборатория',
    '33' => 'Терраформер',
    '34' => 'Склад альянса',
    '35' => 'Нанолаборатория',
    '40' => 'Особые постройки',
    '41' => 'Лунная база',
    '42' => 'Сенсорная фаланга',
    '43' => 'Межгалактические врата',
    '44' => 'Ракетная шахта',
    TECH_TECHNOLOGY => 'Исследования',
    TECH_SPY => 'Технология шпионажа',
    TECH_COMPUTER => 'Компьютерная технология',
    TECH_WEAPON => 'Оружейная технология',
    TECH_SHIELD => 'Щитовая технология',
    TECH_ARMOR => 'Броня космических кораблей',
    TECH_ENERGY => 'Энергетическая технология',
    TECH_HYPERSPACE => 'Гиперпространственная технология',
    TECH_ENGINE_CHEMICAL => 'Химический двигатель',
    TECH_ENIGNE_ION => 'Ионный двигатель',
    TECH_ENGINE_HYPER => 'Гиперпространственный двигатель',
    TECH_LASER => 'Лазерная технология',
    TECH_ION => 'Ионная технология',
    TECH_PLASMA => 'Плазменная технология',
    TECH_RESEARCH => 'Межгалактическая исследовательская сеть',
    TECH_EXPEDITION => 'Экспедиционная технология',
    TECH_COLONIZATION => 'Колонизационная технология',
    TECH_GRAVITON => 'Гравитационная технология',
    SHIP_FLEET => 'Флот',
    SHIP_SATTELITE_SOLAR => 'Солнечный спутник',
    SHIP_SPY => 'Шпионский зонд',
    SHIP_CARGO_SMALL => 'Малый транспорт',
    SHIP_CARGO_BIG => 'Большой транспорт',
    SHIP_CARGO_SUPER => 'Супертранспорт',
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
