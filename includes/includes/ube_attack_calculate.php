<?php

require_once('ube_report.php');

/*

// TODO: Для админского боя обнулять обломки до луны - что бы не создавалась луна и не писалось количество обломков

// TODO: В симуляторе все равно печтатать поле обломков и изменить надпись "Средний диаметр луны с указанным шансом %%" UBE_MOON_SIMULATED

// TODO: Отсылать каждому игроку сообщение на его языке!

// TODO: Перевод на английский

*/

/*
Планы

[*] UBEv4: Уменьшать каждый раунд значение общей брони кораблей на % от дамаджа, прошедшего за щиты
    Так будет эмулироваться распределение повреждений по всем кораблям. На этот же процент уменьшать атаку. Процент брать от брони кораблей
[*] UBEv4: Рандомизировать броню в самом начале боя
[*] UBEv4: Можно прописать разным кораблям разную стойкость ко взрыву
[*] UBEv4: Уровень регенерации щитов
[*] UBEv4: При атаке и проигрыше выбрасывать в космос излишки ресурсов со складов - часть ресурсов, которые за пределами складов. А часть - терять

[*] UBEv4: Боевые порядки юнитов
[*] UBEv4: Collateral damage: Взрыв корабля наносит урон соседям. Взрыв всех или только одиночек ?
[*] UBEv4: Регенерация брони - спецмодуль корабля. И вообще подумать о спецмодулях - скорее всего через прошивки
[*] UBEv4: Распределить планетарный щит в пропорции между всеми защитными сооружениями - эмуляция первого залпа. Или просто добавить такое количество щитов всем оборонам
[*] UBEv4: Динамическое цветовое кодирование результатов боя - для защитников отдельно, для атакующих - отдельно
[*] UBEv4: При отлете СН и ЗС могут уничтожить новосозданную луну (?)
[*] UBEv4: Броня влияет на количество брони, соотношение брони и структуры - т.е. как быстро рванет корабль, а так же на ходовые качества
    Типы брони: дюралюминий (легкая, но непрочная - минус к весу, минус к броне), броневая сталь (стандартная броня),
    легированная сталь (более прочная и более дорогая версия стальной - плюс к броне, плюс к цене), комозитная броня (по прочности, как сталь, но легче - минус к весу, плюс к цене),
    урановая броня (более прочная и заметно тяжелее, чем стальная - плюс к весу, плюс к броне, плюс к цене),
    титановая броня (хай-энд: прочная, как уран, легкая, как дюралюминия, дорогая, как пиздец)
    Модуль активной брони против кинетического оружия
[*] UBEv4: Щиты
    Модуль для щитов "Перегрузка": дает 200% щитов на первый залп

[*] ЧР: Инфа об удержании

[*] Симулятор: Поддержка мультифлотов. Переписать интерфейс симулятора под работу с любым количеством флотов

[*] Артефакты: Гиперсборщик - позволяет собрать обломки сразу после боя

[*] Наемники: Мародер. Капитан или офицер. +1% к вывозимым ресурсам за каждый уровень. Скажем, до +25%. Считаем от общей доли вывоза или от всех ресов на планете?

[*] Боты: Захватывать ничьи планеты

*/

if(BE_DEBUG === true)
{
  require_once('ube_zi_helpers.php');
}

global $ube_combat_bonus_list, $ube_convert_techs, $ube_convert_to_techs;

$ube_combat_bonus_list = array(
  UBE_ATTACK => UBE_ATTACK,
  UBE_ARMOR  => UBE_ARMOR,
  UBE_SHIELD => UBE_SHIELD,
);

$ube_convert_techs = array(
  TECH_WEAPON => UBE_ATTACK,
  TECH_ARMOR  => UBE_ARMOR,
  TECH_SHIELD => UBE_SHIELD,
);

$ube_convert_to_techs = array(
  UBE_ATTACK => 'attack',
  UBE_ARMOR  => 'armor',
  UBE_SHIELD => 'shield',
);


// ------------------------------------------------------------------------------------------------
// Заполняет данные по игроку
function ube_attack_prepare_player(&$combat_data, $player_id, $is_attacker)
{
  global $ube_convert_techs, $sn_data;

  if(!isset($combat_data[UBE_PLAYERS][$player_id]))
  {
    $combat_data[UBE_PLAYERS][$player_id] = array(UBE_ATTACKER => $is_attacker);
    $player_info = &$combat_data[UBE_PLAYERS][$player_id];

    $player_data = doquery("SELECT * FROM {{users}} WHERE `id` = {$player_id} LIMIT 1 FOR UPDATE;", true);
    $player_info[UBE_NAME] = $player_data['username'];
    $player_info[UBE_AUTH_LEVEL] = $player_data['authlevel'];
    $combat_data[UBE_OPTIONS][UBE_COMBAT_ADMIN] = $combat_data[UBE_OPTIONS][UBE_COMBAT_ADMIN] || $player_data['authlevel']; // Участвует ли админ в бою?
    $player_info[UBE_PLAYER_DATA] = $player_data;

    $admiral_bonus = mrc_get_level($player_data, false, MRC_ADMIRAL) * $sn_data[MRC_ADMIRAL]['bonus'] / 100;
    foreach($ube_convert_techs as $unit_id => $ube_id)
    {
      $player_info[UBE_BONUSES][$ube_id] += mrc_get_level($player_data, false, $unit_id) * $sn_data[$unit_id]['bonus'] / 100 + $admiral_bonus;
    }
  }
}

// ------------------------------------------------------------------------------------------------
// Заполняет данные по флоту
function ube_attack_prepare_fleet(&$combat_data, &$fleet, $is_attacker){return sn_function_call('ube_attack_prepare_fleet', array(&$combat_data, &$fleet, $is_attacker));}
function sn_ube_attack_prepare_fleet(&$combat_data, &$fleet, $is_attacker)
{
  global $sn_data;

  $fleet_owner_id = $fleet['fleet_owner'];
  $fleet_id = $fleet['fleet_id'];

  ube_attack_prepare_player($combat_data, $fleet_owner_id, $is_attacker);

  $fleet_data = sys_unit_str2arr($fleet['fleet_array']);

  $combat_data[UBE_FLEETS][$fleet_id][UBE_OWNER] = $fleet_owner_id;
  $fleet_info = &$combat_data[UBE_FLEETS][$fleet_id];
  $fleet_info[UBE_FLEET_GROUP] = $fleet['fleet_group'];
  foreach($fleet_data as $unit_id => $unit_count)
  {
    if(!$unit_count)
    {
      continue;
    }

    if($sn_data[$unit_id]['type'] == UNIT_SHIPS || $sn_data[$unit_id]['type'] == UNIT_DEFENCE)
    {
      $fleet_info[UBE_COUNT][$unit_id] = $unit_count;
    }
//    if($sn_data[$unit_id]['type'] == UNIT_TECHNOLOGIES)
//    {
//      $combat_data[UBE_PLAYERS][$fleet_owner_id][UBE_BONUSES][$ube_convert_techs[$unit_id]] = $unit_count;
//    }
//    else
//    elseif($sn_data[$unit_id]['type'] == UNIT_MERCENARIES)
//    {
//      if($unit_id == MRC_FORTIFIER)
//      {
//        foreach($ube_convert_techs as $ube_id)
//        {
//          $fleet_info[UBE_BONUSES][$ube_id] = $unit_count;
//        }
//      }
//    }
//    elseif($sn_data[$unit_id]['type'] == UNIT_RESOURCES)
//    {
//      $fleet_info[UBE_RESOURCES][$unit_id] = $unit_count;
//    }
  }

  $fleet_info[UBE_RESOURCES] = array(
    RES_METAL => $fleet['fleet_resource_metal'],
    RES_CRYSTAL => $fleet['fleet_resource_crystal'],
    RES_DEUTERIUM => $fleet['fleet_resource_deuterium'],
  );

  $fleet_info[UBE_PLANET] = array(
    // TODO: Брать имя и кэшировать ИД и имя планеты?
//    PLANET_ID => $fleet['fleet_start_id'],
//    PLANET_NAME => $fleet['fleet_start_name'],
    PLANET_GALAXY => $fleet['fleet_start_galaxy'],
    PLANET_SYSTEM => $fleet['fleet_start_system'],
    PLANET_PLANET => $fleet['fleet_start_planet'],
    PLANET_TYPE => $fleet['fleet_start_type'],
  );
}

// ------------------------------------------------------------------------------------------------
// Заполняет данные по планете
function ube_attack_prepare_planet(&$combat_data, &$planet)
{
  global $ube_combat_bonus_list, $sn_data;

  $player_id = $planet['id_owner'];

  ube_attack_prepare_player($combat_data, $player_id, false);

  $player = &$combat_data[UBE_PLAYERS][$player_id][UBE_PLAYER_DATA];

  $combat_data[UBE_FLEETS][0] = array(UBE_OWNER => $player_id);
  $fleet_info = &$combat_data[UBE_FLEETS][0];

  foreach(sn_get_groups('combat') as $unit_id)
  {
    if($unit_count = mrc_get_level($player, $planet, $unit_id))
    {
      $fleet_info[UBE_COUNT][$unit_id] = $unit_count;
    }
  }

  foreach(sn_get_groups('resources_loot') as $resource_id)
  {
    $fleet_info[UBE_RESOURCES][$resource_id] = floor(mrc_get_level($player, $planet, $resource_id));
  }

  if($fortifier_level = mrc_get_level($player, $planet, MRC_FORTIFIER))
  {
    $fortifier_bonus = $fortifier_level * $sn_data[MRC_FORTIFIER]['bonus'] / 100;
    foreach($ube_combat_bonus_list as $ube_id)
    {
      $fleet_info[UBE_BONUSES][$ube_id] += $fortifier_bonus;
    }
  }

  $combat_data[UBE_OUTCOME][UBE_PLANET] = $fleet_info[UBE_PLANET] = array(
    PLANET_ID     => $planet['id'],
    PLANET_NAME   => $planet['name'],
    PLANET_GALAXY => $planet['galaxy'],
    PLANET_SYSTEM => $planet['system'],
    PLANET_PLANET => $planet['planet'],
    PLANET_TYPE   => $planet['planet_type'],
    PLANET_SIZE   => $planet['diameter'],
  );

  $combat_data[UBE_OPTIONS][UBE_DEFENDER_ACTIVE] = $player['onlinetime'] >= $combat_data[UBE_TIME] - 60*60*24*7;
}


// ------------------------------------------------------------------------------------------------
// Заполняет начальные данные по данным миссии
function ube_attack_prepare(&$mission_data)
{
/*
UBE_OPTIONS[UBE_LOADED]   
UBE_OPTIONS[UBE_SIMULATOR]
UBE_OPTIONS[UBE_EXCHANGE] 
UBE_OPTIONS[UBE_MOON_WAS] 
*/

  $fleet_row          = &$mission_data['fleet'];
  $destination_planet = &$mission_data['dst_planet'];

  $ube_time = $fleet_row['fleet_start_time'];
  $combat_data = array(UBE_TIME => $ube_time);
// TODO: Не допускать атаки игроком своих же флотов - т.е. холд против атаки
  // Готовим инфу по атакуемой планете
  ube_attack_prepare_planet($combat_data, $destination_planet);

  // Готовим инфу по удержанию
  $fleets = doquery("SELECT * FROM {{fleets}} 
    WHERE
      `fleet_end_galaxy` = {$fleet_row['fleet_end_galaxy']} AND `fleet_end_system` = {$fleet_row['fleet_end_system']} AND `fleet_end_planet` = {$fleet_row['fleet_end_planet']} AND `fleet_end_type` = {$fleet_row['fleet_end_type']}
      AND `fleet_start_time` <= {$ube_time} AND `fleet_end_stay` >= {$ube_time}
      AND `fleet_mess` = 0 FOR UPDATE"
  );
  while($fleet = mysql_fetch_assoc($fleets))
  {
    ube_attack_prepare_fleet($combat_data, $fleet, false);
  }

  // Готовим инфу по атакующим
  if($fleet_row['fleet_group'])
  {
    $fleets = doquery("SELECT * FROM {{fleets}} WHERE fleet_group= {$fleet_row['fleet_group']} FOR UPDATE");
    while($fleet = mysql_fetch_assoc($fleets))
    {
      ube_attack_prepare_fleet($combat_data, $fleet, true);
    }
  }
  else
  {
    ube_attack_prepare_fleet($combat_data, $fleet_row, true);
  }

  // Готовим опции
  $combat_data[UBE_OPTIONS][UBE_MOON_WAS] = $destination_planet['planet_type'] == PT_MOON || is_array(doquery("SELECT `id` FROM {{planets}} WHERE `parent_planet` = {$destination_planet['id']} LIMIT 1;", true));
  $combat_data[UBE_OPTIONS][UBE_MISSION_TYPE] = $fleet_row['fleet_mission'];

  return $combat_data;
}

// ------------------------------------------------------------------------------------------------
function sn_ube_combat_prepare_first_round(&$combat_data)
{
  global $sn_data, $ube_combat_bonus_list, $ube_convert_to_techs;

  // Готовим информацию для первого раунда - проводим все нужные вычисления из исходных данных
  $first_round_data = array();
  foreach($combat_data[UBE_FLEETS] as $fleet_id => &$fleet_info)
  {
    $fleet_info[UBE_COUNT] = is_array($fleet_info[UBE_COUNT]) ? $fleet_info[UBE_COUNT] : array();
    $player_data = &$combat_data[UBE_PLAYERS][$fleet_info[UBE_OWNER]];
    $fleet_info[UBE_FLEET_TYPE] = $player_data[UBE_ATTACKER] ? UBE_ATTACKERS : UBE_DEFENDERS;

    foreach($ube_combat_bonus_list as $bonus_id => $bonus_value)
    {
      // Вычисляем бонус игрока
      $bonus_value = isset($player_data[UBE_BONUSES][$bonus_id]) ? $player_data[UBE_BONUSES][$bonus_id] : 0;
      // Добавляем к бонусам флота бонусы игрока
      $fleet_info[UBE_BONUSES][$bonus_id] += $bonus_value;
    }

    $first_round_data[$fleet_id][UBE_COUNT] = $fleet_info[UBE_PRICE] = array();
    foreach($fleet_info[UBE_COUNT] as $unit_id => $unit_count)
    {
      if($unit_count <= 0)
      {
        continue;
      }

      // Заполняем информацию о кораблях в информации флота
      foreach($ube_combat_bonus_list as $bonus_id => $bonus_value)
      {
        $fleet_info[$bonus_id][$unit_id] = floor($sn_data[$unit_id][$ube_convert_to_techs[$bonus_id]] * (1 + $fleet_info[UBE_BONUSES][$bonus_id]));
      }
      $fleet_info[UBE_AMPLIFY][$unit_id] = $sn_data[$unit_id]['amplify'];
      // TODO: Переделать через get_ship_data()
      $fleet_info[UBE_CAPACITY][$unit_id] = $sn_data[$unit_id]['capacity'];
      $fleet_info[UBE_TYPE][$unit_id] = $sn_data[$unit_id]['type'];
      // TODO: Переделать через список ресурсов
      $fleet_info[UBE_PRICE][RES_METAL]    [$unit_id] = $sn_data[$unit_id]['cost'][RES_METAL];
      $fleet_info[UBE_PRICE][RES_CRYSTAL]  [$unit_id] = $sn_data[$unit_id]['cost'][RES_CRYSTAL];
      $fleet_info[UBE_PRICE][RES_DEUTERIUM][$unit_id] = $sn_data[$unit_id]['cost'][RES_DEUTERIUM];
      $fleet_info[UBE_PRICE][RES_DARK_MATTER][$unit_id] = $sn_data[$unit_id]['cost'][RES_DARK_MATTER];

      // Копируем её в информацию о первом раунде
      $first_round_data[$fleet_id][UBE_ARMOR][$unit_id] = $fleet_info[UBE_ARMOR][$unit_id] * $unit_count;
      $first_round_data[$fleet_id][UBE_COUNT][$unit_id] = $unit_count;
    }
  }
  $combat_data[UBE_ROUNDS][0][UBE_FLEETS] = $first_round_data;
  $combat_data[UBE_ROUNDS][1][UBE_FLEETS] = $first_round_data;
  sn_ube_combat_round_prepare($combat_data, 0);
}

/**
 *
 * @copyright copyright (c) 2012 by Gorlum for http://supernova.ws/
 *
 * @param array $combat_data
 * @return array
 */

// ------------------------------------------------------------------------------------------------
// Вычисление дополнительной информации для расчета раунда
function sn_ube_combat_round_prepare(&$combat_data, $round)
{
  global $ube_combat_bonus_list;

  $is_simulator = $combat_data[UBE_OPTIONS][UBE_SIMULATOR];

  $round_data = &$combat_data[UBE_ROUNDS][$round];
  foreach($round_data[UBE_FLEETS] as $fleet_id => &$fleet_data)
  {
    // Кэшируем переменные для легкого доступа к подмассивам
    $fleet_info = &$combat_data[UBE_FLEETS][$fleet_id];
    $fleet_data[UBE_FLEET_INFO] = &$fleet_info;
    $fleet_type = $fleet_info[UBE_FLEET_TYPE];

    foreach($fleet_data[UBE_COUNT] as $unit_id => $unit_count)
    {
      if($unit_count <= 0)
      {
        continue;
      }

// TODO:  Добавить процент регенерации щитов

      // Для не-симулятора - рандомизируем каждый раунд значения атаки и щитов
      $fleet_data[UBE_ATTACK_BASE][$unit_id] = floor($fleet_info[UBE_ATTACK][$unit_id] * ($is_simulator ? 1 : mt_rand(80, 120) / 100));
      $fleet_data[UBE_SHIELD_BASE][$unit_id] = floor($fleet_info[UBE_SHIELD][$unit_id] * ($is_simulator ? 1 : mt_rand(80, 120) / 100));
      $fleet_data[UBE_ARMOR_BASE][$unit_id]  = floor($fleet_info[UBE_ARMOR][$unit_id]);// * ($is_simulator ? 1 : mt_rand(80, 120) / 100));

      $fleet_data[UBE_ATTACK][$unit_id] = $fleet_data[UBE_ATTACK_BASE][$unit_id] * $unit_count;
      $fleet_data[UBE_SHIELD][$unit_id] = $fleet_data[UBE_SHIELD_BASE][$unit_id] * $unit_count;
      // $fleet_data[UBE_ARMOR][$unit_id] = $fleet_info[UBE_ARMOR_BASE][$unit_id] * $unit_count;
    }

    // Суммируем данные по флоту
    foreach($ube_combat_bonus_list as $bonus_id)
    {
      $round_data[$fleet_type][$bonus_id][$fleet_id] += is_array($fleet_data[$bonus_id]) ? array_sum($fleet_data[$bonus_id]) : 0;
    }
  }

  // Суммируем данные по атакующим и защитникам
  foreach($ube_combat_bonus_list as $bonus_id)
  {
    $round_data[UBE_TOTAL][UBE_DEFENDERS][$bonus_id] = array_sum($round_data[UBE_DEFENDERS][$bonus_id]);
    $round_data[UBE_TOTAL][UBE_ATTACKERS][$bonus_id] = array_sum($round_data[UBE_ATTACKERS][$bonus_id]);
  }

  // Высчитываем долю атаки, приходящейся на юнит равную отношению брони юнита к общей броне - крупные цели атакуют чаще
  foreach($round_data[UBE_FLEETS] as &$fleet_data)
  {
    $fleet_type = $fleet_data[UBE_FLEET_INFO][UBE_FLEET_TYPE];
    foreach($fleet_data[UBE_COUNT] as $unit_id => $unit_count)
    {
      $fleet_data[UBE_DAMAGE_PERCENT][$unit_id] = $fleet_data[UBE_ARMOR][$unit_id] / $round_data[UBE_TOTAL][$fleet_type][UBE_ARMOR];
    }
  }
}

// ------------------------------------------------------------------------------------------------
// Рассчитывает результат столкновения флотов ака раунд
function sn_ube_combat_round_crossfire_fleet(&$combat_data, $round)
{
  if(BE_DEBUG === true)
  {
    sn_ube_combat_helper_round_header($round);
  }

  $round_data = &$combat_data[UBE_ROUNDS][$round];
  // Проводим бой. Сталкиваем каждый корабль атакующего с каждым кораблем атакуемого
  foreach($round_data[UBE_ATTACKERS][UBE_ATTACK] as $attack_fleet_id => $temp)
  {
    $attack_fleet_data = &$round_data[UBE_FLEETS][$attack_fleet_id];
    foreach($round_data[UBE_DEFENDERS][UBE_ATTACK] as $defend_fleet_id => $temp2)
    {
      $defend_fleet_data = &$round_data[UBE_FLEETS][$defend_fleet_id];

      foreach($attack_fleet_data[UBE_COUNT] as $attack_unit_id => $attack_unit_count)
      {
        // if($attack_unit_count <= 0) continue; // TODO: Это пока нельзя включать - вот если будут "боевые порядки юнитов..."
        foreach($defend_fleet_data[UBE_COUNT] as $defend_unit_id => $defend_unit_count)
        {
          sn_ube_combat_round_crossfire_unit($attack_fleet_data, $defend_fleet_data, $attack_unit_id, $defend_unit_id, $combat_data[UBE_OPTIONS]);
          sn_ube_combat_round_crossfire_unit($defend_fleet_data, $attack_fleet_data, $defend_unit_id, $attack_unit_id, $combat_data[UBE_OPTIONS]);
        }
      }
    }
  }

  if(BE_DEBUG === true)
  {
    sn_ube_combat_helper_round_footer();
  }
}

// ------------------------------------------------------------------------------------------------
// Рассчитывает результат столкновения двух юнитов ака ход
function sn_ube_combat_round_crossfire_unit(&$attack_fleet_data, &$defend_fleet_data, $attack_unit_id, $defend_unit_id, &$combat_options)
{
  $attack_fleet_info = &$attack_fleet_data[UBE_FLEET_INFO];
  $defend_fleet_info = &$defend_fleet_data[UBE_FLEET_INFO];
  $defend_unit_armor = $defend_fleet_info[UBE_ARMOR][$defend_unit_id];

  // Инициализируем переменные для таблицы
  $shields_original = $defend_fleet_data[UBE_SHIELD][$defend_unit_id];
  $armor_original = $defend_fleet_data[UBE_ARMOR][$defend_unit_id];

  // Вычисляем прямой дамадж от атакующего юнита с учетом размера атакуемого
  $direct_damage = floor($attack_fleet_data[UBE_ATTACK][$attack_unit_id] * $defend_fleet_data[UBE_DAMAGE_PERCENT][$defend_unit_id]);

  // TODO: Строим перекрестную таблицу амплификаций для ускорения. Вынести её вообще нафиг в инициализацию (?)
  // TODO: Амплификацию можно сразу считать с бонусами - тогда за ними не надо будет лазить каждый раз или делать лишние подсчеты выше в атаке
  // Применяем амплифай, если есть
  $amplify = $attack_fleet_info[UBE_AMPLIFY][$attack_unit_id][$defend_unit_id];
  $amplify = $amplify ? $amplify : 1;
  $amplified_damage = floor($direct_damage * $amplify);

  // Снимаем остатки щитов
  $shields_damage = min($defend_fleet_data[UBE_SHIELD][$defend_unit_id], $amplified_damage);
  $defend_fleet_data[UBE_SHIELD][$defend_unit_id] -= $shields_damage;

  // Вычисляем дамадж брони
  $damage_to_armor = $amplified_damage - $shields_damage;
  $armor_damage = min($defend_fleet_data[UBE_ARMOR][$defend_unit_id], $damage_to_armor);
  $defend_fleet_data[UBE_ARMOR][$defend_unit_id] -= $armor_damage;

  // Вычисляем сколько юнитов осталось
  $units_left = ceil($defend_fleet_data[UBE_ARMOR][$defend_unit_id] / $defend_unit_armor);
//  $defend_fleet_data[UBE_UNITS_LOST][$defend_unit_id] = $defend_fleet_data[UBE_COUNT][$defend_unit_id] - $units_left;

  // Вычисляем состояние последнего юнита и проверяем - взорвался ли он
  // TODO: Прописать разным кораблям разную стойкость ко взрыву
  $last_unit_hp = $defend_fleet_data[UBE_ARMOR][$defend_unit_id] - $defend_unit_armor * ($units_left - 1);
  $last_unit_percent = $last_unit_hp / $defend_unit_armor * 100;
  $boom = 0;
  $boom_limit = 75;
  $random = $combat_options[UBE_SIMULATOR] ? $boom_limit / 2 : mt_rand(0, 100);
  if($armor_damage && $units_left > 0 && $last_unit_percent <= $boom_limit && $last_unit_percent <= $random)
  {
    $boom = 1;
    $units_left--;
    $defend_fleet_data[UBE_ARMOR][$defend_unit_id] = $units_left * $defend_unit_armor;
    $defend_fleet_data[UBE_UNITS_BOOM][$defend_unit_id]++;
  }

  $defend_fleet_data[UBE_COUNT][$defend_unit_id] = $units_left;

  if(BE_DEBUG === true)
  {
    $debug_unit_crossfire_result = array(
      'attack_unit_id' => $attack_unit_id,
      'defend_unit_id' => $defend_unit_id,
      $attack_fleet_data[UBE_ATTACK][$attack_unit_id],
      $defend_fleet_data[UBE_DAMAGE_PERCENT][$defend_unit_id],
      $direct_damage,
      $amplify,
      $amplified_damage,
      $shields_original,
      $shields_damage,
      $defend_fleet_data[UBE_SHIELD][$defend_unit_id],
      $damage_to_armor,
      $armor_original,
      $armor_damage,

      $last_unit_hp,
      floor($last_unit_percent),
      $random,
      $boom,
    //  $units_lost,
      $defend_fleet_data[UBE_ARMOR][$defend_unit_id],
      $defend_fleet_data[UBE_COUNT][$defend_unit_id],
      'attack_fleet_data' => $attack_fleet_data,
      'defend_fleet_data' => $defend_fleet_data,
    );
    sn_ube_combat_helper_round_row($debug_unit_crossfire_result);
  }
}

// ------------------------------------------------------------------------------------------------
// Анализирует результаты раунда и генерирует данные для следующего раунда
function sn_ube_combat_round_analyze(&$combat_data, $round)
{
  $round_data = &$combat_data[UBE_ROUNDS][$round];
  $round_data[UBE_OUTCOME] = UBE_COMBAT_RESULT_DRAW;

  $outcome = array();
  $next_round_fleet = array();
  foreach($round_data[UBE_FLEETS] as $fleet_id => &$fleet_data)
  {
    if(array_sum($fleet_data[UBE_COUNT]) <= 0)
    {
      continue;
    }

    foreach($fleet_data[UBE_COUNT] as $unit_id => $unit_count)
    {
      if($unit_count <= 0)
      {
        continue;
      }
      $next_round_fleet[$fleet_id][UBE_COUNT][$unit_id] = $unit_count;
      $next_round_fleet[$fleet_id][UBE_ARMOR][$unit_id] = $fleet_data[UBE_ARMOR][$unit_id];
      $outcome[$fleet_data[UBE_FLEET_INFO][UBE_FLEET_TYPE]] = 1;
    }
  }

  // Проверяем - если кого-то не осталось или не осталось обоих - заканчиваем цикл
  if(count($outcome) == 0 || $round == 10)
  {
    $round_data[UBE_OUTCOME] = UBE_COMBAT_RESULT_DRAW_END;
  }
  elseif(count($outcome) == 1)
  {
    $round_data[UBE_OUTCOME] = isset($outcome[UBE_ATTACKERS]) ? UBE_COMBAT_RESULT_WIN : UBE_COMBAT_RESULT_LOSS;
  }
  elseif(count($outcome) == 2)
  {
    if($round < 10)
    {
      $combat_data[UBE_ROUNDS][$round + 1][UBE_FLEETS] = $next_round_fleet;
    }
  }

  return($round_data[UBE_OUTCOME]);
}

// ------------------------------------------------------------------------------------------------
// Общий алгоритм расчета боя
function sn_ube_combat(&$combat_data)
{
  // TODO: Сделать атаку по типам,  когда они будут

  $start = microtime(true);
  sn_ube_combat_prepare_first_round($combat_data);

  for($round = 1; $round <= 10; $round++)
  {
    // Готовим данные для раунда
    sn_ube_combat_round_prepare($combat_data, $round);

    // Проводим раунд
    sn_ube_combat_round_crossfire_fleet($combat_data, $round);

    // Анализируем итоги текущего раунда и готовим данные для следующего
    if(sn_ube_combat_round_analyze($combat_data, $round) != UBE_COMBAT_RESULT_DRAW)
    {
      break;
    }
  }
  $combat_data[UBE_TIME_SPENT] = microtime(true) - $start;

  // Делать это всегда - нам нужны результаты боя: луна->обломки->количество осташихся юнитов
  sn_ube_combat_analyze($combat_data);
}

// ------------------------------------------------------------------------------------------------
// Разбирает данные боя для генерации отчета
function sn_ube_combat_analyze(&$combat_data)
{
  global $config;

//  $combat_data[UBE_OUTCOME] = array();
  $combat_data[UBE_OPTIONS][UBE_EXCHANGE] = array(RES_METAL => $config->rpg_exchange_metal);

  $exchange = &$combat_data[UBE_OPTIONS][UBE_EXCHANGE];
  foreach(array(RES_CRYSTAL => 'rpg_exchange_crystal', RES_DEUTERIUM => 'rpg_exchange_deuterium', RES_DARK_MATTER => 'rpg_exchange_darkMatter') as $resource_id => $resource_name)
  {
    $exchange[$resource_id] = $config->$resource_name * $exchange[RES_METAL];
  }
//  $total_resources_value = array_sum($exchange);

  // Переменные для быстрого доступа к подмассивам
  $outcome = &$combat_data[UBE_OUTCOME];
  $fleets_info = &$combat_data[UBE_FLEETS];
  $last_round_data = &$combat_data[UBE_ROUNDS][count($combat_data[UBE_ROUNDS]) - 1];

  $outcome[UBE_DEBRIS] = array();

  // Генерируем результат боя
  foreach($fleets_info as $fleet_id => &$fleet_info)
  {
    $fleet_type = $fleet_info[UBE_FLEET_TYPE];
    // Инициализируем массив результатов для флота
    $outcome[UBE_FLEETS][$fleet_id] = array(UBE_UNITS_LOST => array());
    $outcome[$fleet_type][UBE_FLEETS][$fleet_id] = &$outcome[UBE_FLEETS][$fleet_id];

    // Переменные для быстрого доступа к подмассивам
    $fleet_outcome = &$outcome[UBE_FLEETS][$fleet_id];
    $fleet_data = &$last_round_data[UBE_FLEETS][$fleet_id];

    foreach($fleet_info[UBE_COUNT] as $unit_id => $unit_count)
    {
      // Вычисляем сколько юнитов осталось и сколько потеряно
      $units_left = $fleet_data[UBE_COUNT][$unit_id];

      // Восстановление обороны - 75% от уничтоженной
      if($fleet_info[UBE_TYPE][$unit_id] == UNIT_DEFENCE)
      {
        $giveback_chance = 75; // TODO Configure
        $units_lost = $unit_count - $units_left;
        if($combat_data[UBE_OPTIONS][UBE_SIMULATOR])
        { // for simulation just return 75% of loss
          $units_giveback = round($units_lost * $giveback_chance / 100);
        }
        else
        {
          if($unit_count > 10)
          { // if there were more then 10 defense elements - mass-calculating giveback
            $units_giveback = round($units_lost * mt_rand($giveback_chance * 0.8, $giveback_chance * 1.2) / 100);
          }
          else
          { //if there were less then 10 defense elements - calculating giveback per element
            $units_giveback = 0;
            for($i = 1; $i <= $units_lost; $i++)
            {
              if(mt_rand(1,100) <= $giveback_chance)
              {
                $units_giveback++;
              }
            }
          }
        }
        $units_left += $units_giveback;
        $fleet_outcome[UBE_DEFENCE_RESTORE][$unit_id] = $units_giveback;
      }

      // TODO: Сбор металла/кристалла от обороны

      $units_lost = $unit_count - $units_left;

      // Вычисляем емкость трюмов оставшихся кораблей
      $outcome[$fleet_type][UBE_CAPACITY][$fleet_id] += $fleet_info[UBE_CAPACITY][$unit_id] * $units_left;

      // Вычисляем потери в ресурсах
      if($units_lost)
      {
        $fleet_outcome[UBE_UNITS_LOST][$unit_id] = $units_lost;

        foreach($fleet_info[UBE_PRICE] as $resource_id => $unit_prices)
        {
          if(!$unit_prices[$unit_id])
          {
            continue;
          }

          // ...чистыми
          $resources_lost = $units_lost * $unit_prices[$unit_id];
          $fleet_outcome[UBE_RESOURCES_LOST][$resource_id] += $resources_lost;

          // Если это корабль - прибавляем потери к обломкам на орбите
          if($fleet_info[UBE_TYPE][$unit_id] == UNIT_SHIPS)
          {
            $outcome[UBE_DEBRIS][$resource_id] += floor($resources_lost * ($combat_data[UBE_OPTIONS][UBE_SIMULATOR] ? 30 : mt_rand(20, 40)) / 100); // TODO: Configurize
          }

          // ...в металле
          $resources_lost_in_metal = $resources_lost * $exchange[$resource_id];
          $fleet_outcome[UBE_RESOURCES_LOST_IN_METAL][RES_METAL] += $resources_lost_in_metal;
        }
      }
    }

    // На планете ($fleet_id = 0) ресурсы в космос не выбрасываются
    if($fleet_id == 0)
    {
      continue;
    }

    // Количество ресурсов флота
    $fleet_total_resources = empty($fleet_info[UBE_RESOURCES]) ? 0 : array_sum($fleet_info[UBE_RESOURCES]);
    // Если на борту нет ресурсов - зачем нам все это?
    if($fleet_total_resources == 0)
    {
      continue;
    }

    // Емкость трюмов флота
    $fleet_capacity = $outcome[$fleet_type][UBE_CAPACITY][$fleet_id];
    // Если емкость трюмов меньше количество ресурсов - часть ресов выбрасываем нахуй
    if($fleet_capacity < $fleet_total_resources)
    {
      $left_percent = $fleet_capacity / $fleet_total_resources; // Сколько ресурсов будет оставлено
      foreach($fleet_info[UBE_RESOURCES] as $resource_id => $resource_amount)
      {
        // Не просчитываем ресурсы, которых нет на борту кораблей флота
        if(!$resource_amount)
        {
          continue;
        }

        // TODO Восстанавливаем ошибку округления - придумать нормальный алгоритм - вроде round() должно быть достаточно. Проверить
        $fleet_outcome[UBE_RESOURCES][$resource_id] = round($left_percent * $resource_amount);
        $resource_dropped = $resource_amount - $fleet_outcome[UBE_RESOURCES][$resource_id];
        $fleet_outcome[UBE_CARGO_DROPPED][$resource_id] = $resource_dropped;

        $outcome[UBE_DEBRIS][$resource_id] += round($resource_dropped * ($combat_data[UBE_OPTIONS][UBE_SIMULATOR] ? 50 : mt_rand(30, 70)) / 100); // TODO: Configurize
        $fleet_outcome[UBE_RESOURCES_LOST_IN_METAL][RES_METAL] += $resource_dropped * $exchange[$resource_id];
      }
      $fleet_total_resources = array_sum($fleet_outcome[UBE_RESOURCES]);
    }

    $outcome[$fleet_type][UBE_CAPACITY][$fleet_id] = $fleet_capacity - $fleet_total_resources;
  }

  $outcome[UBE_COMBAT_RESULT] = !isset($last_round_data[UBE_OUTCOME]) || $last_round_data[UBE_OUTCOME] == UBE_COMBAT_RESULT_DRAW_END ? UBE_COMBAT_RESULT_DRAW : $last_round_data[UBE_OUTCOME];
  // SFR - Small Fleet Reconnaissance ака РМФ
  $outcome[UBE_SFR] = count($combat_data[UBE_ROUNDS]) == 2 && $outcome[UBE_COMBAT_RESULT] == UBE_COMBAT_RESULT_LOSS;

  if(!$combat_data[UBE_OPTIONS][UBE_LOADED])
  {
    if($combat_data[UBE_OPTIONS][UBE_MOON_WAS])
    {
      $outcome[UBE_MOON] = UBE_MOON_WAS;
    }
    else
    {
      sn_ube_combat_analyze_moon($outcome, $combat_data[UBE_OPTIONS][UBE_SIMULATOR]);
    }

    // Лутаем ресурсы - если аттакер выиграл
    if($outcome[UBE_COMBAT_RESULT] == UBE_COMBAT_RESULT_WIN)
    {
      sn_ube_combat_analyze_loot($combat_data);
      if($combat_data[UBE_OPTIONS][UBE_MOON_WAS] && $combat_data[UBE_OPTIONS][UBE_MISSION_TYPE] == MT_DESTROY)
      {
        sn_ube_combat_analyze_moon_destroy($combat_data);
      }
    }
  }

}

// ------------------------------------------------------------------------------------------------
function sn_ube_combat_analyze_loot(&$combat_data)
{
  $exchange = &$combat_data[UBE_OPTIONS][UBE_EXCHANGE];
  $planet_resource_list = &$combat_data[UBE_FLEETS][0][UBE_RESOURCES];
  $outcome = &$combat_data[UBE_OUTCOME];

  $planet_looted_in_metal = 0;
  $planet_resource_looted = array();
  $planet_resource_total = is_array($planet_resource_list) ? array_sum($planet_resource_list) : 0;
  if($planet_resource_total && ($total_capacity = array_sum($outcome[UBE_ATTACKERS][UBE_CAPACITY])))
  {
    // Можно вывести только половину ресурсов, но не больше, чем общая вместимость флотов атакующих
    $planet_lootable = min($planet_resource_total / 2, $total_capacity);
    // Вычисляем процент вывоза. Каждого ресурса будет вывезено в одинаковых пропорциях
    $planet_lootable_percent = $planet_lootable / $planet_resource_total;

    // Вычисляем какой процент общей емкости трюмов атакующих будет задействован
    $total_lootable = min($planet_lootable, $total_capacity);

    // Вычисляем сколько ресурсов вывезено
    foreach($outcome[UBE_ATTACKERS][UBE_CAPACITY] as $fleet_id => $fleet_capacity)
    {
      $looted_in_metal = 0;
      $fleet_loot_data = array();
      foreach($planet_resource_list as $resource_id => $resource_amount)
      {
        // TODO Восстанавливаем ошибку округления - придумать нормальный алгоритм - вроде round() должно быть достаточно. Проверить
        $fleet_lootable_percent = $fleet_capacity / $total_capacity;
        $looted = round($resource_amount * $planet_lootable_percent * $fleet_lootable_percent);
        $fleet_loot_data[$resource_id] = -$looted;
        $planet_resource_looted[$resource_id] += $looted;
        $looted_in_metal -= $looted * $exchange[$resource_id];
      }
      $outcome[UBE_FLEETS][$fleet_id][UBE_RESOURCES_LOOTED] = $fleet_loot_data;
      $outcome[UBE_FLEETS][$fleet_id][UBE_RESOURCES_LOST_IN_METAL][RES_METAL] += $looted_in_metal;
      $planet_looted_in_metal += $looted_in_metal;
    }
  }

  $outcome[UBE_FLEETS][0][UBE_RESOURCES_LOST_IN_METAL][RES_METAL] -= $planet_looted_in_metal;
  $outcome[UBE_FLEETS][0][UBE_RESOURCES_LOOTED] = $planet_resource_looted;
}

// ------------------------------------------------------------------------------------------------
function sn_ube_combat_analyze_moon(&$outcome, $is_simulator)
{
  $outcome[UBE_DEBRIS_TOTAL] = 0;
  foreach(array(RES_METAL, RES_CRYSTAL) as $resource_id) // TODO via array
  {
    $outcome[UBE_DEBRIS_TOTAL] += $outcome[UBE_DEBRIS][$resource_id];
  }

  if($outcome[UBE_DEBRIS_TOTAL])
  {
    // TODO uni_calculate_moon_chance
    $moon_chance = min($outcome[UBE_DEBRIS_TOTAL] / 1000000, 30); // TODO Configure
    $moon_chance = $moon_chance >= 1 ? $moon_chance : 0;
    $outcome[UBE_MOON_CHANCE] = $moon_chance;
    if($moon_chance)
    {
      if($is_simulator || mt_rand(1, 100) <= $moon_chance)
      {
        $outcome[UBE_MOON_SIZE] = round($is_simulator ? $moon_chance * 150 + 1999 : mt_rand($moon_chance * 100 + 1000, $moon_chance * 200 + 2999));
        $outcome[UBE_MOON] = UBE_MOON_CREATE_SUCCESS;

        if($outcome[UBE_DEBRIS_TOTAL] <= 30000000)
        {
          $outcome[UBE_DEBRIS_TOTAL] = 0;
          $outcome[UBE_DEBRIS] = array();
        }
        else
        {
          $moon_debris_spent = 30000000;
          $moon_debris_left_percent = ($outcome[UBE_DEBRIS_TOTAL] - $moon_debris_spent) / $outcome[UBE_DEBRIS_TOTAL];

          $outcome[UBE_DEBRIS_TOTAL] = 0;
          foreach(array(RES_METAL, RES_CRYSTAL) as $resource_id) // TODO via array
          {
            $outcome[UBE_DEBRIS][$resource_id] = floor($outcome[UBE_DEBRIS][$resource_id] * $moon_debris_left_percent);
            $outcome[UBE_DEBRIS_TOTAL] += $outcome[UBE_DEBRIS][$resource_id];
          }
        }
      }
      else
      {
        $outcome[UBE_MOON] = UBE_MOON_CREATE_FAILED;
      }
    }
  }
  else
  {
    $outcome[UBE_MOON] = UBE_MOON_NONE;
  }
}

// ------------------------------------------------------------------------------------------------
function sn_ube_combat_analyze_moon_destroy(&$combat_data)
{
  // TODO: $is_simulator
  $reapers = 0;
  foreach($combat_data[UBE_ROUNDS][count($combat_data[UBE_ROUNDS])-1][UBE_FLEETS] as $fleet_data)
  {
    if($fleet_data[UBE_FLEET_INFO][UBE_FLEET_TYPE] == UBE_ATTACKERS)
    {
      foreach($fleet_data[UBE_COUNT] as $unit_id => $unit_count)
      {
        // TODO: Работа по группам - группа "Уничтожители лун"
        $reapers += ($unit_id == SHIP_DEATH_STAR) ? $unit_count : 0;
      }
    }
  }

  $moon_size = $combat_data[UBE_OUTCOME][UBE_PLANET][PLANET_SIZE];
  if($reapers)
  {
    $random = mt_rand(1, 100);
    $combat_data[UBE_OUTCOME][UBE_MOON_DESTROY_CHANCE] = min(99, round((100 - sqrt($moon_size)) * sqrt($reapers)));
    $combat_data[UBE_OUTCOME][UBE_MOON_REAPERS_DIE_CHANCE] = round(sqrt($moon_size) / 2 + sqrt($reapers));
    $combat_data[UBE_OUTCOME][UBE_MOON] = $random <= $combat_data[UBE_OUTCOME][UBE_MOON_DESTROY_CHANCE] ? UBE_MOON_DESTROY_SUCCESS : UBE_MOON_DESTROY_FAILED;
    $combat_data[UBE_OUTCOME][UBE_MOON_REAPERS] = $random <= $combat_data[UBE_OUTCOME][UBE_MOON_REAPERS_DIE_CHANCE] ? UBE_MOON_REAPERS_DIED : UBE_MOON_REAPERS_RETURNED;
  }
  else
  {
    $combat_data[UBE_OUTCOME][UBE_MOON_REAPERS] = UBE_MOON_REAPERS_NONE;
  }
}

// ------------------------------------------------------------------------------------------------
// Рассылает письма всем участникам боя
function sn_ube_message_send(&$combat_data)
{
  global $lang;

  // TODO: Отсылать каждому игроку сообщение на его языке!

  $outcome = &$combat_data[UBE_OUTCOME];
  $planet_info = &$outcome[UBE_PLANET];

  // Генерируем текст письма
  $text_common = sprintf($lang['ube_report_msg_body_common'],
    date(FMT_DATE_TIME, $combat_data['UBE_TIME']),
    $lang['sys_planet_type_sh'][$planet_info[PLANET_TYPE]],
    $planet_info[PLANET_GALAXY],
    $planet_info[PLANET_SYSTEM],
    $planet_info[PLANET_PLANET],
    htmlentities($planet_info[PLANET_NAME], ENT_COMPAT, 'UTF-8'),
    $lang[$outcome['UBE_COMBAT_RESULT'] == UBE_COMBAT_RESULT_WIN ? 'ube_report_info_outcome_win' :
      ($outcome['UBE_COMBAT_RESULT'] == UBE_COMBAT_RESULT_DRAW ? 'ube_report_info_outcome_draw' : 'ube_report_info_outcome_loss')
    ]
  );

  $text_defender = '';
  foreach($outcome[UBE_DEBRIS] as $resource_id => $resource_amount)
  {
    if($resource_id == RES_DEUTERIUM)
    {
      continue;
    }

    $text_defender .= "{$lang['tech'][$resource_id]}: " . pretty_number($resource_amount) . '<br />';
  }
  if($text_defender)
  {
    $text_defender = "{$lang['ube_report_msg_body_debris']}{$text_defender}<br />";
  }

  if($outcome[UBE_MOON] == UBE_MOON_CREATE_SUCCESS)
  {
    $text_defender .= "{$lang['ube_report_moon_created']} {$outcome[UBE_MOON_SIZE]} {$lang['sys_kilometers_short']}<br /><br />";
  }
  elseif($outcome[UBE_MOON] == UBE_MOON_CREATE_FAILED)
  {
    $text_defender .= "{$lang['ube_report_moon_chance']} {$outcome[UBE_MOON_CHANCE]}%<br /><br />";
  }

  if($combat_data[UBE_OPTIONS][UBE_MISSION_TYPE] == MT_DESTROY)
  {
    if($outcome[UBE_MOON_REAPERS] == UBE_MOON_REAPERS_NONE)
    {
      $text_defender .= $lang['ube_report_moon_reapers_none'];
    }
    else
    {
      $text_defender .= "{$lang['ube_report_moon_reapers_wave']}. {$lang['ube_report_moon_reapers_chance']} {$outcome[UBE_MOON_DESTROY_CHANCE]}%. ";
      $text_defender .= $lang[$outcome[UBE_MOON] == UBE_MOON_DESTROY_SUCCESS ? 'ube_report_moon_reapers_success' : 'ube_report_moon_reapers_failure'] . "<br />";

      $text_defender .= "{$lang['ube_report_moon_reapers_outcome']} {$outcome[UBE_MOON_REAPERS_DIE_CHANCE]}%. ";
      $text_defender .= $lang[$outcome[UBE_MOON_REAPERS] == UBE_MOON_REAPERS_RETURNED ? 'ube_report_moon_reapers_survive' : 'ube_report_moon_reapers_died'];
    }
    $text_defender .= '<br /><br />';
  }

  $text_defender .= "{$lang['ube_report_info_link']}: <a href=\"index.php?page=battle_report&cypher={$combat_data[UBE_REPORT_CYPHER]}\">{$combat_data[UBE_REPORT_CYPHER]}</a>";

  // TODO: Оптимизировать отсылку сообщений - отсылать пакетами
  foreach($combat_data[UBE_PLAYERS] as $player_id => $player_info)
  {
    $message = $text_common . ($outcome[UBE_SFR] && $player_info[UBE_ATTACKER] ? $lang['ube_report_msg_body_sfr'] : $text_defender);
    msg_send_simple_message($player_id, '', $combat_data[UBE_TIME], MSG_TYPE_COMBAT, $lang['sys_mess_tower'], $lang['sys_mess_attack_report'], $message);
  }

}


// ------------------------------------------------------------------------------------------------
// Записывает результат боя в БД
/** @noinspection SpellCheckingInspection */
function ube_combat_result_apply(&$combat_data){return sn_function_call('ube_combat_result_apply', array(&$combat_data));}
function sn_ube_combat_result_apply(&$combat_data)
{
  global $sn_data;
// TODO: Поменять все отладки на запросы
  $destination_user_id = $combat_data[UBE_FLEETS][0][UBE_OWNER];

  $outcome = &$combat_data[UBE_OUTCOME];
  $planet_info = &$outcome[UBE_PLANET];
  $planet_id = $planet_info[PLANET_ID];
  // Обновляем поле обломков на планете
  if(!$combat_data[UBE_OPTIONS][UBE_COMBAT_ADMIN] && !empty($outcome[UBE_DEBRIS]))
  {
    doquery("
      UPDATE {{planets}}
        SET
          `debris_metal` = `debris_metal` + " . floor($outcome[UBE_DEBRIS][RES_METAL]) . "
          ,`debris_crystal` = `debris_crystal` + " . floor($outcome[UBE_DEBRIS][RES_CRYSTAL]) . "
        WHERE
          `galaxy` = '{$planet_info[PLANET_GALAXY]}'
          AND `system` = '{$planet_info[PLANET_SYSTEM]}'
          AND `planet` = '{$planet_info[PLANET_PLANET]}'
          AND `planet_type` = 1 LIMIT 1;
    ");
  }

  $db_save = array(
    UBE_FLEET_GROUP => array(), // Для САБов
  );

  $fleets_outcome = &$outcome[UBE_FLEETS];
  foreach($combat_data[UBE_FLEETS] as $fleet_id => &$fleet_info)
  {
    if($fleet_info[UBE_FLEET_GROUP])
    {
      $db_save[UBE_FLEET_GROUP][$fleet_info[UBE_FLEET_GROUP]] = $fleet_info[UBE_FLEET_GROUP];
    }

    $fleet_info[UBE_COUNT] = $fleet_info[UBE_COUNT] ? $fleet_info[UBE_COUNT] : array();
    $fleets_outcome[$fleet_id][UBE_UNITS_LOST] = $fleets_outcome[$fleet_id][UBE_UNITS_LOST] ? $fleets_outcome[$fleet_id][UBE_UNITS_LOST] : array();

    $fleet_query = array();
    $old_fleet_count = array_sum($fleet_info[UBE_COUNT]);
    $new_fleet_count = $old_fleet_count - array_sum($fleets_outcome[$fleet_id][UBE_UNITS_LOST]);
    // Перебираем юниты если во время боя количество юнитов изменилось и при этом во флоту остались юнити или это планета
    if($new_fleet_count != $old_fleet_count && (!$fleet_id || $new_fleet_count))
    {
      // Просматриваем результаты изменения флотов
      foreach($fleet_info[UBE_COUNT] as $unit_id => $unit_count)
      {
        // Перебираем аутком на случай восстановления юнитов
        $units_lost = (float)$fleets_outcome[$fleet_id][UBE_UNITS_LOST][$unit_id];

        $units_left = $unit_count - $units_lost;
        if($fleet_id)
        {
          // Не планета - всегда сразу записываем строку итогов флота
          $fleet_query[$unit_id] = "{$unit_id},{$units_left}";
        }
        elseif($units_lost)
        {
          // Планета - записываем в ИД юнита его потери только если есть потери
          $fleet_query[$unit_id] = "`{$sn_data[$unit_id]['name']}` = `{$sn_data[$unit_id]['name']}` - {$units_lost}";
        }

/*
        if($units_lost)
        {
          // Не планета - сразу записываем строку итогов флота
          // Планета - записываем в ИД юнита его потери
          $fleet_query[$unit_id] = $fleet_id ? "{$unit_id},{$units_left}" : "`{$sn_data[$unit_id]['name']}` = `{$sn_data[$unit_id]['name']}` - {$units_lost}";
        }
*/
        //$new_fleet_count += $units_left;
      }

      if($fleet_id)
      {
        // Для флотов перегенерируем массив как одно вхождение в SET SQL-запроса
        $fleet_query = implode(';', $fleet_query);
        $fleet_query = array("`fleet_array` = '{$fleet_query}'");
      }
    }

    // Если во флоте остались юниты или это планета - генерируем изменение ресурсов
    if($new_fleet_count || !$fleet_id)
    {
      foreach(sn_get_groups('resources_loot') as $resource_id)
      {
        $resource_change = (float)$fleets_outcome[$fleet_id][UBE_RESOURCES_LOOTED][$resource_id] + (float)$fleets_outcome[$fleet_id][UBE_CARGO_DROPPED][$resource_id];
        if($resource_change)
        {
          $resource_db_name = ($fleet_id ? 'fleet_resource_' : '') . $sn_data[$resource_id]['name'];
          $fleet_query[] = "`{$resource_db_name}` = `{$resource_db_name}` - ({$resource_change})";
        }
      }
    }
    /*
        if(empty($fleet_query))
        {
          continue;
        }
    */
    if($fleet_id && $new_fleet_count)
    {
      // Если защитник и не РМФ - отправляем флот назад
      if(($fleet_info[UBE_FLEET_TYPE] == UBE_DEFENDERS && !$outcome[UBE_SFR]) || $fleet_info[UBE_FLEET_TYPE] == UBE_ATTACKERS)
      {
        $fleet_query[] = '`fleet_mess` = 1';
      }

      // Если флот в группе - помечаем нулем
//      if($fleet_info[UBE_FLEET_GROUP])
//      {
//        $fleet_query[] = '`fleet_group` = 0';
//      }
    }

//debug($fleet_query);
global $debug;
    $fleet_query = implode(',', $fleet_query);
    if($fleet_id)
    {
      // Не планета
      if($new_fleet_count || ($fleet_info[UBE_FLEET_TYPE] == UBE_ATTACKERS && $outcome[UBE_MOON_REAPERS] == UBE_MOON_REAPERS_RETURNED))
      {
        if($fleet_query)
        {
$debug->warning($combat_data[UBE_REPORT_CYPHER] . ": UPDATE {{fleets}} SET {$fleet_query} WHERE `fleet_id` = {$fleet_id} LIMIT 1");
          doquery("UPDATE {{fleets}} SET {$fleet_query} WHERE `fleet_id` = {$fleet_id} LIMIT 1");
        }
      }
      else
      {
        // Удаляем пустые флоты
$debug->warning($combat_data[UBE_REPORT_CYPHER] . ": DELETE FROM {{fleets}} WHERE `fleet_id` = {$fleet_id} LIMIT 1");
        doquery("DELETE FROM {{fleets}} WHERE `fleet_id` = {$fleet_id} LIMIT 1");
$debug->warning($combat_data[UBE_REPORT_CYPHER] . ": DELETE FROM {{unit}} WHERE `unit_location_type` = " . LOC_FLEET . " AND `unit_location_id` = {$fleet_id}");
        doquery("DELETE FROM {{unit}} WHERE `unit_location_type` = " . LOC_FLEET . " AND `unit_location_id` = {$fleet_id}");
      }
    }
    elseif($fleet_query)
    {
      // Планета - сохраняем данные боя
$debug->warning($combat_data[UBE_REPORT_CYPHER] . ": UPDATE {{planets}} SET {$fleet_query} WHERE `id` = {$planet_id} LIMIT 1");
      doquery("UPDATE {{planets}} SET {$fleet_query} WHERE `id` = {$planet_id} LIMIT 1");
    }
  }

  // TODO: Связать сабы с флотами констраинтами ON DELETE SET NULL
  // $db_save[UBE_FLEET_GROUP][$fleet_info[UBE_FLEET_GROUP]] = $fleet_info[UBE_FLEET_GROUP];
  if(!empty($db_save[UBE_FLEET_GROUP]))
  {
    $db_save[UBE_FLEET_GROUP] = implode(',', $db_save[UBE_FLEET_GROUP]);
$debug->warning($combat_data[UBE_REPORT_CYPHER] . ": DELETE FROM {{aks}} WHERE `id` IN ({$db_save[UBE_FLEET_GROUP]})");
    doquery("DELETE FROM {{aks}} WHERE `id` IN ({$db_save[UBE_FLEET_GROUP]})");
  }

  if($outcome[UBE_MOON] == UBE_MOON_CREATE_SUCCESS)
  {
    $outcome[UBE_MOON_NAME] = uni_create_moon($planet_info[PLANET_GALAXY], $planet_info[PLANET_SYSTEM], $planet_info[PLANET_PLANET], $destination_user_id, $outcome[UBE_MOON_SIZE], '', false);
  }
  elseif($outcome[UBE_MOON] == UBE_MOON_DESTROY_SUCCESS)
  {
$debug->warning($combat_data[UBE_REPORT_CYPHER] . ": DELETE FROM {{planets}} WHERE `id` = {$planet_id} LIMIT 1");
    doquery("DELETE FROM {{planets}} WHERE `id` = {$planet_id} LIMIT 1");
  }

  {
    $bashing_list = array();
    foreach($combat_data[UBE_PLAYERS] as $player_id => $player_info)
    {
      if($player_info[UBE_ATTACKER])
      {
        if($outcome[UBE_MOON] != UBE_MOON_DESTROY_SUCCESS)
        {
          $bashing_list[] = "({$player_id}, {$planet_id}, {$combat_data[UBE_TIME]})";
        }
        if($combat_data[UBE_OPTIONS][UBE_MISSION_TYPE] == MT_ATTACK && $combat_data[UBE_OPTIONS][UBE_DEFENDER_ACTIVE])
        {
          /** @noinspection SpellCheckingInspection */
          $str_loose_or_win = $outcome[UBE_COMBAT_RESULT] == UBE_COMBAT_RESULT_WIN ? 'raidswin' : 'raidsloose';
$debug->warning($combat_data[UBE_REPORT_CYPHER] . ": UPDATE {{users}} SET `xpraid` = `xpraid` + 1, `raids` = `raids` + 1, `{$str_loose_or_win}` = `{$str_loose_or_win}` + 1 WHERE id = '{$player_id}' LIMIT 1;");
          doquery("UPDATE {{users}} SET `xpraid` = `xpraid` + 1, `raids` = `raids` + 1, `{$str_loose_or_win}` = `{$str_loose_or_win}` + 1 WHERE id = '{$player_id}' LIMIT 1;");
        }
      }
    }
    $bashing_list = implode(',', $bashing_list);
    if($bashing_list)
    {
$debug->warning($combat_data[UBE_REPORT_CYPHER] . ": INSERT INTO {{bashing}} (bashing_user_id, bashing_planet_id, bashing_time) VALUES {$bashing_list};");
      doquery("INSERT INTO {{bashing}} (bashing_user_id, bashing_planet_id, bashing_time) VALUES {$bashing_list};");
    }
  }

}

?>
