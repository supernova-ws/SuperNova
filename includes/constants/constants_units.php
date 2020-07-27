<?php
/**
 * Created by Gorlum 27.03.2018 10:48
 */

// === UNITS
define('GROUP_GROUP_ID_TO_NAMES', 'group_id_to_names');
define('GROUP_CAPITAL_BUILDING_BONUS_GROUPS', 'GROUP_CAPITAL_BUILDING_BONUS_GROUPS');

define('UNIT_ANY', 0);

// === Structures
define('UNIT_STRUCTURES_STR', 'structures');
define('UNIT_STRUCTURES', 99);
define('UNIT_STRUCTURES_PLANET', 98);
define('UNIT_STRUCTURES_ALL', 96);
define('STRUC_MINE_METAL', 1);
define('STRUC_MINE_CRYSTAL', 2);
define('STRUC_MINE_DEUTERIUM', 3);
define('STRUC_MINE_SOLAR', 4);
define('STRUC_MINE_FUSION', 12);
define('STRUC_FACTORY_ROBOT', 14);
define('STRUC_FACTORY_NANO', 15);
define('STRUC_FACTORY_HANGAR', 21);
define('STRUC_STORE_METAL', 22);
define('STRUC_STORE_CRYSTAL', 23);
define('STRUC_STORE_DEUTERIUM', 24);
define('STRUC_LABORATORY', 31);
define('STRUC_LABORATORY_NANO', 35);
define('STRUC_TERRAFORMER', 33);
define('STRUC_ALLY_DEPOSIT', 34);
define('STRUC_SILO', 44);

define('UNIT_STRUCTURES_SPECIAL', 40);
define('STRUC_MOON_STATION', 41);
define('STRUC_MOON_PHALANX', 42);
define('STRUC_MOON_GATE', 43);

// === Techs
define('UNIT_TECHNOLOGIES', 100); // 101-105
define('TECH_SPY', 106); // 107
define('TECH_COMPUTER', 108);
define('TECH_WEAPON', 109);
define('TECH_SHIELD', 110);
define('TECH_ARMOR', 111); // 112
define('TECH_ENERGY', 113);
define('TECH_HYPERSPACE', 114);
define('TECH_ENGINE_CHEMICAL', 115); // 116
define('TECH_ENGINE_ION', 117);
define('TECH_ENGINE_HYPER', 118);
define('TECH_ENGINE_NUCLEAR', 119);
define('TECH_LASER', 120);
define('TECH_ION', 121);
define('TECH_PLASMA', 122);
define('TECH_RESEARCH', 123);
define('TECH_EXPEDITION', 124);
define('TECH_NUCLEAR', 125); // 126-149
define('TECH_COLONIZATION', 150);
define('TECH_ASTROTECH', 151); // 152-198
define('TECH_GRAVITON', 199);

// === Hangar units
// --- Ships
define('UNIT_SHIPS_STR', 'fleet');
define('UNIT_SHIPS', 200);

define('SHIP_CARGO_SMALL', 202);
define('SHIP_CARGO_BIG', 203);
define('SHIP_CARGO_SUPER', 201);
define('SHIP_CARGO_HYPER', 218);
define('SHIP_COLONIZER', 208);
define('SHIP_RECYCLER', 209);
define('SHIP_RECYCLER_GLUTTONY', 223);
define('SHIP_SPY', 210);
define('SHIP_SATTELITE_SOLAR', 212);
define('SHIP_CARGO_GREED', 220);
define('SHIP_SATTELITE_SLOTH', 221);


define('SHIP_SMALL_FIGHTER_LIGHT', 204);
define('SHIP_SMALL_FIGHTER_WRATH', 219);
define('SHIP_SMALL_FIGHTER_HEAVY', 205);
define('SHIP_SMALL_FIGHTER_ASSAULT', 217);
define('SHIP_MEDIUM_DESTROYER', 206);
define('SHIP_MEDIUM_FRIGATE', 226); // Not used
define('SHIP_MEDIUM_BOMBER_ENVY', 224);
define('SHIP_LARGE_CRUISER', 207);
define('SHIP_LARGE_BOMBER', 211);
define('SHIP_LARGE_BATTLESHIP', 215);
define('SHIP_LARGE_BATTLESHIP_PRIDE', 222);
define('SHIP_LARGE_ORBITAL_HEAVY', 225);
define('SHIP_LARGE_DESTRUCTOR', 213);
define('SHIP_HUGE_DEATH_STAR', 214);
define('SHIP_HUGE_SUPERNOVA', 216);

const SHIP_SATELLITE_SPUTNIK = 228; // Anti-spy satellite THAT CAN FLY!!!
const SHIP_CARGO_FIREFLY = 229; // Fastest medium transport
const SHIP_RECYCLER_BURAN = 230; // Fastest recycler
const SHIP_SMALL_FIGHTER_SOYUZ = 231; // SHIP_SMALL_FIGHTER_SOYUZ ? с гауссовкой (!)
const SHIP_MEDIUM_TORPEDO_SPIRAL = 232; // Торпедоносец против больших и сверхбольших кораблей

const SHIP_NEXT = 233;

// --- Defense
define('UNIT_DEFENCE_STR', 'defense');
define('UNIT_DEFENCE', 400);
define('UNIT_DEF_TURRET_MISSILE', 401);
define('UNIT_DEF_TURRET_LASER_SMALL', 402);
define('UNIT_DEF_TURRET_LASER_BIG', 403);
define('UNIT_DEF_TURRET_GAUSS', 404);
define('UNIT_DEF_TURRET_ION', 405);
define('UNIT_DEF_TURRET_PLASMA', 406);
define('UNIT_DEF_SHIELD_SMALL', 407);
define('UNIT_DEF_SHIELD_BIG', 408);
define('UNIT_DEF_SHIELD_PLANET', 409);

// --- Missiles
define('UNIT_DEF_MISSILES_STR', 'missile');
define('UNIT_DEF_MISSILES', 500);
define('UNIT_DEF_MISSILE_INTERCEPTOR', 502);
define('UNIT_DEF_MISSILE_INTERPLANET', 503);

// === Mercenaries
// --- Mercenary list
define('UNIT_MERCENARIES', 600);
define('MRC_ACADEMIC', 606);
define('MRC_ADMIRAL', 602);
define('MRC_STOCKMAN', 607); // OK MODIFIER
define('MRC_SPY', 610);
define('MRC_COORDINATOR', 611);
define('MRC_DESTRUCTOR', 612);
define('MRC_NAVIGATOR', 613);
define('MRC_ASSASIN', 614);
define('MRC_EMPEROR', 615);

// --- Governors list
define('UNIT_GOVERNORS', 680);
define('MRC_TECHNOLOGIST', 601); // OK MODIFIER
define('MRC_ENGINEER', 605);
define('MRC_FORTIFIER', 608);

define('UNIT_GOVERNOR_PRIMARY', 695);
define('UNIT_GOVERNOR_SECONDARY', 696);

// Bonus category
define('BONUS_SERVER', 0);
define('BONUS_MERCENARY', UNIT_MERCENARIES); // DO NOT MOVE ABOVE MERCENARIES SECTION!

// === Resources
define('UNIT_RESOURCES_STR', 'resources');
define('UNIT_RESOURCES_STR_LOOT', 'resources_loot');
define('UNIT_RESOURCES_STR_TRADER', 'resources_trader');
define('UNIT_RESOURCES', 900);
define('RES_METAL', 901);
define('RES_CRYSTAL', 902);
define('RES_DEUTERIUM', 903);
define('RES_ENERGY', 904);
define('RES_DARK_MATTER', 905);
define('RES_METAMATTER', 950);
define('RES_TIME', 999);

// === Artifacts
define('UNIT_ARTIFACTS_STR', 'artifacts');
define('UNIT_ARTIFACTS', 1000);
define('ART_LHC', 1001);      // Additional moon chance
define('ART_RCD_SMALL', 1002);   // Rapid Colony Deployment - Set of buildings up to 10th level - 10/14/ 3/0 -   405 DM
define('ART_RCD_MEDIUM', 1003);  // Rapid Colony Deployment - Set of buildings up to 15th level - 15/20/ 8/0 -  4704 DM
define('ART_RCD_LARGE', 1004);   // Rapid Colony Deployment - Set of buildings up to 20th level - 20/25/10/1 - 39790 DM
define('ART_HEURISTIC_CHIP', 1005); // Speed up research
define('ART_NANO_BUILDER', 1006); // Speed up building
define('ART_NANO_CONSTRUCTOR', 1007); // RESERVED Speed up hangar constructions
define('ART_HOOK_SMALL', 1008);
define('ART_HOOK_MEDIUM', 1009);
define('ART_HOOK_LARGE', 1010);
define('ART_DENSITY_CHANGER', 1011); // RESERVED
// 1012 RESERVED
define('ART_PLANET_GATE', 1013); // RESERVED Planet gate

// === Blueprints
define('UNIT_PLANS', 1100);
define('UNIT_PLAN_STRUC_MINE_FUSION', 1101);
define('UNIT_PLAN_SHIP_CARGO_SUPER', 1102);
define('UNIT_PLAN_SHIP_CARGO_HYPER', 1103);
define('UNIT_PLAN_SHIP_DEATH_STAR', 1104);
define('UNIT_PLAN_SHIP_SUPERNOVA', 1105);
define('UNIT_PLAN_DEF_SHIELD_PLANET', 1106);
define('UNIT_PLAN_SHIP_ORBITAL_HEAVY', 1107);

define('UNIT_PREMIUM', 1200);
define('UNIT_LOGIN_TOKEN', 1210);
define('UNIT_MASS_OPERATIONS', 1290);
define('UNIT_MASS_OPERATIONS_TEST_DRIVE', 1291);
define('UNIT_SECTOR', 1300);
define('UNIT_RACE', 1400);
define('UNIT_CAPTAIN', 1500);

define('UNIT_PLANET_DENSITY', 1601);
define('UNIT_PLANET_DENSITY_INDEX', 1602);
define('UNIT_PLANET_DENSITY_RARITY', 1603);
define('UNIT_PLANET_DENSITY_RICHNESS', 1604);
define('UNIT_PLANET_DENSITY_MAX_SECTORS', 1605);
define('UNIT_PLANET_DENSITY_PROBABILITY', 1606);
define('UNIT_PLANET_DENSITY_MIN_ASTROTECH', 1607);

define('PLANET_DENSITY_NONE', 0);
define('PLANET_DENSITY_ICE_HYDROGEN', 8); // New
define('PLANET_DENSITY_ICE_METHANE', 1); // Old ICE
define('PLANET_DENSITY_ICE_WATER', 9); // New
define('PLANET_DENSITY_CRYSTAL_RAW', 10); // New
define('PLANET_DENSITY_CRYSTAL_SILICATE', 2);
define('PLANET_DENSITY_CRYSTAL_STONE', 3);
define('PLANET_DENSITY_STANDARD', 4);
define('PLANET_DENSITY_METAL_ORE', 5);
define('PLANET_DENSITY_METAL_PERIDOT', 6);
// define('PLANET_DENSITY_METAL_HEAVY', 7); // deprecated
define('PLANET_DENSITY_METAL_RAW', 11); // New
// MAXIMUM PLANET_DENSITY_METAL_RAW 11

define('PLANET_DENSITY_RICHNESS_NORMAL', 0);
define('PLANET_DENSITY_RICHNESS_AVERAGE', 1);
define('PLANET_DENSITY_RICHNESS_GOOD', 2);
define('PLANET_DENSITY_RICHNESS_PERFECT', 3);


// Награды игрока 2.000-2.999
define('UNIT_AWARD', 2000); // Награды игрока 2.000-2.999
define('UNIT_AWARD_ORDER', 2100); // Ордена за Выдающиеся Достижения - например, за спонсорство
define('UNIT_AWARD_ORDER_SPONSOR_BRONZE', 2101);
define('UNIT_AWARD_ORDER_SPONSOR_SILVER', 2102);
define('UNIT_AWARD_ORDER_SPONSOR_GOLD', 2103);
define('UNIT_AWARD_ORDER_SPONSOR_PLATINUM', 2104);
define('UNIT_AWARD_ORDER_SPONSOR_DIAMOND', 2105);
define('UNIT_AWARD_ORDER_SPONSOR_DARK', 2106);
define('UNIT_AWARD_ORDER_SPONSOR_META', 2107);
define('UNIT_AWARD_ORDER_SPONSOR', 2109);
// 2110 - следующая группа орденов

define('UNIT_AWARD_MEDAL', 2200); // Медали за Серъезные Достижения - например, за победу в конкурсе
define('UNIT_AWARD_MEDAL_BLITZ_R0_PLACE1', 2201); // Блиц-сервер, участник 0-го раунда, 1-е место
define('UNIT_AWARD_MEDAL_BLITZ_R0_PLACE2', 2202); // Блиц-сервер, участник 0-го раунда, 2-е место
define('UNIT_AWARD_MEDAL_BLITZ_R0_PLACE3', 2203); // Блиц-сервер, участник 0-го раунда, 3-е место
define('UNIT_AWARD_MEDAL_2016_WOMEN_DAY_BEST', 2204);  // Медаль Лучшему Кавалеру за максимум потраченной ММ/максимум одаренных женщин Женщине от Мужчины во время ивента 8 марта 2016 года
define('UNIT_AWARD_MEDAL_2017_WOMEN_DAY_BEST', 2205);  // Медаль Лучшему Кавалеру за максимум потраченной ММ/максимум одаренных женщин Женщине от Мужчины во время ивента 8 марта 2017 года
define('UNIT_AWARD_MEDAL_2017_WOMEN_DAY_QUEEN', 2206);  // Медаль Королевы Весны за максимум полученной ММ/максимум полученных подарков от Мужчины во время ивента 8 марта 2017 года
define('UNIT_AWARD_MEDAL_8_MARCH_BEST_CAVALIER_AMOUNT', 2207); // Медаль Лучшему Кавалеру за максимум потраченной ММ Женщине от Мужчины во время ивента 8 марта 2017 года
define('UNIT_AWARD_MEDAL_8_MARCH_BEST_CAVALIER_COUNT', 2208); // Медаль Лучшему Кавалеру за максимум одаренных женщин Женщине от Мужчины во время ивента 8 марта 2017 года
define('UNIT_AWARD_MEDAL_8_MARCH_SPRING_QUEEN_AMOUNT', 2209); // Медаль Королевы Весны за максимум полученной ММ от Мужчины во время ивента 8 марта 2017 года
define('UNIT_AWARD_MEDAL_8_MARCH_SPRING_QUEEN_COUNT', 2210); // Медаль Королевы Весны за максимум полученных подарков от Мужчины во время ивента 8 марта 2017 года

define('UNIT_AWARD_MEMORY', 2300); // Памятные знаки за существование и участие - например "4 года в игре". "Был онлайн в новогоднюю ночь 2013". итд
define('UNIT_AWARD_MEMORY_IMMORTAL', 2301);  // Бессмертный
define('UNIT_AWARD_MEMORY_2015_WOMEN_DAY', 2302);  // Значек за подарок Женщине от Мужчины во время ивента 8 марта 2015 года
define('UNIT_AWARD_MEMORY_BLITZ_R0', 2303); // Блиц-сервер, участник 0-го раунда
define('UNIT_AWARD_MEMORY_SUPER_BORN_2015_SIMPLE', 2304); // День Рождения СН
define('UNIT_AWARD_MEMORY_SUPER_BORN_2015_BRONZE', 2305); // День Рождения СН
define('UNIT_AWARD_MEMORY_SUPER_BORN_2015_SILVER', 2306); // День Рождения СН
define('UNIT_AWARD_MEMORY_SUPER_BORN_2015_GOLD', 2307); // День Рождения СН
define('UNIT_AWARD_MEMORY_SUPER_BORN_2015_PLATINUM', 2308); // День Рождения СН
define('UNIT_AWARD_MEMORY_2016_WOMEN_DAY', 2309);  // Значек за подарок Женщине от Мужчины во время ивента 8 марта 2016 года
define('UNIT_AWARD_MEMORY_2017_WOMEN_DAY', 2310);  // Значек за подарок Женщине от Мужчины во время ивента 8 марта 2017 года
define('UNIT_AWARD_MEMORY_SUPER_BORN_2017_SIMPLE', 2311); // День Рождения СН - 2017
define('UNIT_AWARD_MEMORY_SUPER_BORN_2017_BRONZE', 2312); // День Рождения СН - 2017
define('UNIT_AWARD_MEMORY_SUPER_BORN_2017_SILVER', 2313); // День Рождения СН - 2017
define('UNIT_AWARD_MEMORY_SUPER_BORN_2017_GOLD', 2314); // День Рождения СН - 2017
define('UNIT_AWARD_MEMORY_SUPER_BORN_2017_PLATINUM', 2315); // День Рождения СН - 2017
define('UNIT_AWARD_MEMORY_SUPER_BORN_2017_DIAMOND', 2316); // День Рождения СН - 2017

define('UNIT_AWARD_MEMORY_SUPER_BORN_2016_SIMPLE', 2317); // День Рождения СН - 2016
define('UNIT_AWARD_MEMORY_SUPER_BORN_2016_BRONZE', 2318); // День Рождения СН - 2016
define('UNIT_AWARD_MEMORY_SUPER_BORN_2016_SILVER', 2319); // День Рождения СН - 2016
define('UNIT_AWARD_MEMORY_SUPER_BORN_2016_GOLD', 2320); // День Рождения СН - 2016
define('UNIT_AWARD_MEMORY_SUPER_BORN_2016_PLATINUM', 2321); // День Рождения СН - 2016
define('UNIT_AWARD_MEMORY_SUPER_BORN_2016_DIAMOND', 2322); // День Рождения СН - 2016

define('UNIT_AWARD_MEMORY_SUPER_BORN_2017_BEST_3RD', 2323); // Лучший Гость - День Рождения СН - 2016
define('UNIT_AWARD_MEMORY_SUPER_BORN_2017_BEST_2ND', 2324); // Лучший Гость - День Рождения СН - 2016
define('UNIT_AWARD_MEMORY_SUPER_BORN_2017_BEST_1ST', 2325); // Лучший Гость - День Рождения СН - 2016

const UNIT_AWARD_MEMORY_NEW_YEAR_2018_SIMPLE   = 2326; // СуперНовый Год - 2018
const UNIT_AWARD_MEMORY_NEW_YEAR_2018_BRONZE   = 2327; // СуперНовый Год - 2018
const UNIT_AWARD_MEMORY_NEW_YEAR_2018_SILVER   = 2328; // СуперНовый Год - 2018
const UNIT_AWARD_MEMORY_NEW_YEAR_2018_GOLD     = 2329; // СуперНовый Год - 2018
const UNIT_AWARD_MEMORY_NEW_YEAR_2018_PLATINUM = 2330; // СуперНовый Год - 2018
const UNIT_AWARD_MEMORY_NEW_YEAR_2018_DIAMOND  = 2331; // СуперНовый Год - 2018
const UNIT_AWARD_MEMORY_NEW_YEAR_2018_BEST_1ST = 2332; // Лучший Гость - СуперНовый Год - 2018
const UNIT_AWARD_MEMORY_NEW_YEAR_2018_BEST_2ND = 2333; // Лучший Гость - СуперНовый Год - 2018
const UNIT_AWARD_MEMORY_NEW_YEAR_2018_BEST_3RD = 2334; // Лучший Гость - СуперНовый Год - 2018

const UNIT_AWARD_MEMORY_8_MARCH_MAN_MEMORIAL = 2335; // 2310 old  // Значек за подарок Женщине от Мужчины во время ивента 8 марта - универсальный

const UNIT_AWARD_MEMORY_SUPER_BORN_2018_BRONZE   = 2336; // День Рождения СН - 2018
const UNIT_AWARD_MEMORY_SUPER_BORN_2018_SILVER   = 2337; // День Рождения СН - 2018
const UNIT_AWARD_MEMORY_SUPER_BORN_2018_SIMPLE   = 2338; // День Рождения СН - 2018
const UNIT_AWARD_MEMORY_SUPER_BORN_2018_GOLD     = 2339; // День Рождения СН - 2018
const UNIT_AWARD_MEMORY_SUPER_BORN_2018_PLATINUM = 2340; // День Рождения СН - 2018
const UNIT_AWARD_MEMORY_SUPER_BORN_2018_DIAMOND  = 2341; // День Рождения СН - 2018
const UNIT_AWARD_MEMORY_SUPER_BORN_2018_BEST_1ST = 2342; // Лучший Гость - День Рождения СН - 2018
const UNIT_AWARD_MEMORY_SUPER_BORN_2018_BEST_2ND = 2343; // Лучший Гость - День Рождения СН - 2018
const UNIT_AWARD_MEMORY_SUPER_BORN_2018_BEST_3RD = 2344; // Лучший Гость - День Рождения СН - 2018

const UNIT_AWARD_MEMORY_NEW_YEAR_2019_SIMPLE   = 2345; // СуперНовый Год - 2019
const UNIT_AWARD_MEMORY_NEW_YEAR_2019_BRONZE   = 2346; // СуперНовый Год - 2019
const UNIT_AWARD_MEMORY_NEW_YEAR_2019_SILVER   = 2347; // СуперНовый Год - 2019
const UNIT_AWARD_MEMORY_NEW_YEAR_2019_GOLD     = 2348; // СуперНовый Год - 2019
const UNIT_AWARD_MEMORY_NEW_YEAR_2019_PLATINUM = 2349; // СуперНовый Год - 2019
const UNIT_AWARD_MEMORY_NEW_YEAR_2019_DIAMOND  = 2350; // СуперНовый Год - 2019
const UNIT_AWARD_MEMORY_NEW_YEAR_2019_BEST_1ST = 2351; // Лучший Гость - СуперНовый Год - 2019
const UNIT_AWARD_MEMORY_NEW_YEAR_2019_BEST_2ND = 2352; // Лучший Гость - СуперНовый Год - 2019
const UNIT_AWARD_MEMORY_NEW_YEAR_2019_BEST_3RD = 2353; // Лучший Гость - СуперНовый Год - 2019

const UNIT_AWARD_MEMORY_SUPER_BORN_2019_SIMPLE   = 2354; // День Рождения СН - 2019
const UNIT_AWARD_MEMORY_SUPER_BORN_2019_BRONZE   = 2355; // День Рождения СН - 2019
const UNIT_AWARD_MEMORY_SUPER_BORN_2019_SILVER   = 2356; // День Рождения СН - 2019
const UNIT_AWARD_MEMORY_SUPER_BORN_2019_GOLD     = 2357; // День Рождения СН - 2019
const UNIT_AWARD_MEMORY_SUPER_BORN_2019_PLATINUM = 2358; // День Рождения СН - 2019
const UNIT_AWARD_MEMORY_SUPER_BORN_2019_DIAMOND  = 2359; // День Рождения СН - 2019
const UNIT_AWARD_MEMORY_SUPER_BORN_2019_BEST_1ST = 2360; // Лучший Гость - День Рождения СН - 2019
const UNIT_AWARD_MEMORY_SUPER_BORN_2019_BEST_2ND = 2361; // Лучший Гость - День Рождения СН - 2019
const UNIT_AWARD_MEMORY_SUPER_BORN_2019_BEST_3RD = 2362; // Лучший Гость - День Рождения СН - 2019

const UNIT_AWARD_MEMORY_NEW_YEAR_2020_SIMPLE   = 2363; // СуперНовый Год - 2020
const UNIT_AWARD_MEMORY_NEW_YEAR_2020_BRONZE   = 2364; // СуперНовый Год - 2020
const UNIT_AWARD_MEMORY_NEW_YEAR_2020_SILVER   = 2365; // СуперНовый Год - 2020
const UNIT_AWARD_MEMORY_NEW_YEAR_2020_GOLD     = 2366; // СуперНовый Год - 2020
const UNIT_AWARD_MEMORY_NEW_YEAR_2020_PLATINUM = 2367; // СуперНовый Год - 2020
const UNIT_AWARD_MEMORY_NEW_YEAR_2020_DIAMOND  = 2368; // СуперНовый Год - 2020
const UNIT_AWARD_MEMORY_NEW_YEAR_2020_BEST_1ST = 2369; // Лучший Гость - СуперНовый Год - 2020
const UNIT_AWARD_MEMORY_NEW_YEAR_2020_BEST_2ND = 2370; // Лучший Гость - СуперНовый Год - 2020
const UNIT_AWARD_MEMORY_NEW_YEAR_2020_BEST_3RD = 2371; // Лучший Гость - СуперНовый Год - 2020

const UNIT_AWARD_MEMORY_SUPER_BORN_2020_SIMPLE   = 2380; // День Рождения СН - 2020
const UNIT_AWARD_MEMORY_SUPER_BORN_2020_BRONZE   = 2381; // День Рождения СН - 2020
const UNIT_AWARD_MEMORY_SUPER_BORN_2020_SILVER   = 2382; // День Рождения СН - 2020
const UNIT_AWARD_MEMORY_SUPER_BORN_2020_GOLD     = 2383; // День Рождения СН - 2020
const UNIT_AWARD_MEMORY_SUPER_BORN_2020_PLATINUM = 2384; // День Рождения СН - 2020
const UNIT_AWARD_MEMORY_SUPER_BORN_2020_DIAMOND  = 2385; // День Рождения СН - 2020
const UNIT_AWARD_MEMORY_SUPER_BORN_2020_BEST_1ST = 2386; // Лучший Гость - День Рождения СН - 2020
const UNIT_AWARD_MEMORY_SUPER_BORN_2020_BEST_2ND = 2387; // Лучший Гость - День Рождения СН - 2020
const UNIT_AWARD_MEMORY_SUPER_BORN_2020_BEST_3RD = 2388; // Лучший Гость - День Рождения СН - 2020

define('UNIT_AWARD_PENNANT', 2400); // Переходящий вымпел - индикация статуса на сервере: "Топ-1", "Топ", "Сабтоп", "Самый большой флот" итд
define('UNIT_AWARD_BADGE', 2600); // Бейджики/значки за ачивки - например, "Построил 1000 кораблей"
define('UNIT_AWARD_BADGE_BLITZ', 2601); // Медали за Блиц-сервер
// 2602-2999 // Reserved for Awards

// 3000-3019 - 20 // Christmas Highspot Units
// 3020-3039 - 20 // SuperNova Birthday Units
// 3040-3099 - 50 // Halloween Units
// 3100-3149 - 50 // SuperNova Christmas Gather Units
// 3150-3999 // Reserved for Festival

define('UNIT_NEXT', 4000); // !!! Next unit start on 4000 !!!

// WHEN CHANGING CONSTANT STRING VALUE TO INTEGER IT SHOULD BE CONSISTENT WITH UNIT_xxx FAMILY OF CONSTANTS!!!
define('UNIT_INTERNAL', 700000);

define('UNIT_SERVER_SPEED_BUILDING', 'UNIT_SERVER_SPEED_BUILDING');
define('UNIT_SERVER_SPEED_MINING', 'UNIT_SERVER_SPEED_MINING');
define('UNIT_SERVER_SPEED_FLEET', 'UNIT_SERVER_SPEED_FLEET');
define('UNIT_SERVER_SPEED_EXPEDITION', 'UNIT_SERVER_SPEED_EXPEDITION');
define('UNIT_SERVER_FLEET_NOOB_POINTS', 'UNIT_SERVER_FLEET_NOOB_POINTS');
define('UNIT_SERVER_FLEET_NOOB_FACTOR', 'UNIT_SERVER_FLEET_NOOB_FACTOR');
define('UNIT_SERVER_PAYMENT_MM_PER_CURRENCY', 'UNIT_SERVER_PAYMENT_MM_PER_CURRENCY');

define('UNIT_FESTIVAL_SPEED_BUILDING', 'UNIT_FESTIVAL_SPEED_BUILDING');
define('UNIT_FESTIVAL_SPEED_MINING', 'UNIT_FESTIVAL_SPEED_MINING');
define('UNIT_FESTIVAL_SPEED_FLEET', 'UNIT_FESTIVAL_SPEED_FLEET');
define('UNIT_FESTIVAL_SPEED_EXPEDITION', 'UNIT_FESTIVAL_SPEED_EXPEDITION');

define('UNIT_PLAYER_EMPIRE_SPY', 'UNIT_PLAYER_EMPIRE_SPY');

define('UNIT_PLANET_MINING_METAL', 'UNIT_PLANET_MINING_METAL');
define('UNIT_PLANET_MINING_CRYSTAL', 'UNIT_PLANET_MINING_CRYSTAL');
define('UNIT_PLANET_MINING_DEUTERIUM', 'UNIT_PLANET_MINING_DEUTERIUM');

define('UNIT_FLEET_PLANET_SPY', 'UNIT_FLEET_PLANET_SPY');

define('GROUP_HIGHSPOTS', 790000);
const UNIT_OBJECTS_IN_SPACE = 790001;
const GROUP_UNIT_OBJECTS_IN_SPACE = 790002;


define('GROUP_PART',         800000); // Зарезервировано для запчастей: 800.001 - 899.999
 define('GROUP_PART_HULL',    801000); // Корпуса - 1000 штук
 define('GROUP_PART_ARMOR',   802000); // Броня - 1000 штук
 define('GROUP_PART_SHIELD',  803000); // Щиты - 1000 штук
 define('GROUP_PART_WEAPON',  810000); // Оружие - 10000 штук


define('UNIT_GROUP', 'groups'); // 900.000 // Зарезервировано для груп юнитов: 900.001 - 999.999
define('GROUP_UNIT_USER', 1000000);// Зарезервировано для пользовательских юнитов: 1.000.001 - 1.999.999
define('GROUP_ID_RESERVED', 2000000);// Зарезервировано для прочих нужд: 2.000.000 - 1.999.999.999
const UNIT_CAN_NOT_BE_BUILD = 2000001; // Юнит не может быть построен - для спецюнитов
define('GROUP_PARAMS', 1000000000);// Зарезервировано для параметров: 1.000.000.001 - 1.999.999.999
define('GROUP_DEVELOPERS', 2000000000);// Пространство для разработчиков: 2.000.000.001 - 2.147.483.647

define('UNIT_PLAYER_COLONIES_CURRENT', 'COLONIES_CURRENT');
define('UNIT_PLAYER_COLONIES_MAX', 'COLONIES_MAX');
define('UNIT_PLAYER_EXPEDITIONS_MAX', 'EXPEDITIONS_MAX');

const GROUP_MISSION_EXPLORE_OUTCOMES = 'mission_explore_outcome_list';
const GROUP_UNIT_COMBAT_SORT_ORDER = 'unitCombatSortOrder';
