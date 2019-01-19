<?php

use DBAL\OldDbChangeSet;
use Fleet\DbFleetStatic;
use Planet\DBStaticPlanet;
use Unit\DBStaticUnit;

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

if (BE_DEBUG === true) {
  require_once('ube_zi_helpers.php');
}

// ------------------------------------------------------------------------------------------------
// Рассылает письма всем участникам боя
function sn_ube_message_send(&$combat_data) {
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
      ($outcome['UBE_COMBAT_RESULT'] == UBE_COMBAT_RESULT_DRAW ? 'ube_report_info_outcome_draw' : 'ube_report_info_outcome_loss')]
  );

  $text_defender = '';
  foreach ($outcome[UBE_DEBRIS] as $resource_id => $resource_amount) {
    if ($resource_id == RES_DEUTERIUM) {
      continue;
    }

    $text_defender .= "{$lang['tech'][$resource_id]}: " . HelperString::numberFloorAndFormat($resource_amount) . '<br />';
  }
  if ($text_defender) {
    $text_defender = "{$lang['ube_report_msg_body_debris']}{$text_defender}<br />";
  }

  if ($outcome[UBE_MOON] == UBE_MOON_CREATE_SUCCESS) {
    $text_defender .= "{$lang['ube_report_moon_created']} {$outcome[UBE_MOON_SIZE]} {$lang['sys_kilometers_short']}<br /><br />";
  } elseif ($outcome[UBE_MOON] == UBE_MOON_CREATE_FAILED) {
    $text_defender .= "{$lang['ube_report_moon_chance']} {$outcome[UBE_MOON_CHANCE]}%<br /><br />";
  }

  if ($combat_data[UBE_OPTIONS][UBE_MISSION_TYPE] == MT_DESTROY) {
    if ($outcome[UBE_MOON_REAPERS] == UBE_MOON_REAPERS_NONE) {
      $text_defender .= $lang['ube_report_moon_reapers_none'];
    } else {
      $text_defender .= "{$lang['ube_report_moon_reapers_wave']}. {$lang['ube_report_moon_reapers_chance']} {$outcome[UBE_MOON_DESTROY_CHANCE]}%. ";
      $text_defender .= $lang[$outcome[UBE_MOON] == UBE_MOON_DESTROY_SUCCESS ? 'ube_report_moon_reapers_success' : 'ube_report_moon_reapers_failure'] . "<br />";

      $text_defender .= "{$lang['ube_report_moon_reapers_outcome']} {$outcome[UBE_MOON_REAPERS_DIE_CHANCE]}%. ";
      $text_defender .= $lang[$outcome[UBE_MOON_REAPERS] == UBE_MOON_REAPERS_RETURNED ? 'ube_report_moon_reapers_survive' : 'ube_report_moon_reapers_died'];
    }
    $text_defender .= '<br /><br />';
  }

  $text_defender .= "{$lang['ube_report_info_link']}: <a href=\"index.php?page=battle_report&cypher={$combat_data[UBE_REPORT_CYPHER]}\">{$combat_data[UBE_REPORT_CYPHER]}</a>";

  // TODO: Оптимизировать отсылку сообщений - отсылать пакетами
  foreach ($combat_data[UBE_PLAYERS] as $player_id => $player_info) {
    $message = $text_common . ($outcome[UBE_SFR] && $player_info[UBE_ATTACKER] ? $lang['ube_report_msg_body_sfr'] : $text_defender);
    msg_send_simple_message($player_id, '', $combat_data[UBE_TIME], MSG_TYPE_COMBAT, $lang['sys_mess_tower'], $lang['sys_mess_attack_report'], $message);
  }

}


// ------------------------------------------------------------------------------------------------
// Записывает результат боя в БД
function ube_combat_result_apply(&$combat_data) { return sn_function_call('ube_combat_result_apply', array(&$combat_data)); }

function sn_ube_combat_result_apply(&$combat_data) {
// TODO: Поменять все отладки на запросы
  $destination_user_id = $combat_data[UBE_FLEETS][0][UBE_OWNER];

  $outcome = &$combat_data[UBE_OUTCOME];
  $planet_info = &$outcome[UBE_PLANET];
  $planet_id = $planet_info[PLANET_ID];
  // Обновляем поле обломков на планете
  if (!$combat_data[UBE_OPTIONS][UBE_COMBAT_ADMIN] && !empty($outcome[UBE_DEBRIS])) {
    DBStaticPlanet::db_planet_set_by_gspt($planet_info[PLANET_GALAXY], $planet_info[PLANET_SYSTEM], $planet_info[PLANET_PLANET], PT_PLANET,
      "`debris_metal` = `debris_metal` + " . floor($outcome[UBE_DEBRIS][RES_METAL]) . ", `debris_crystal` = `debris_crystal` + " . floor($outcome[UBE_DEBRIS][RES_CRYSTAL])
    );
  }

  $db_save = array(
    UBE_FLEET_GROUP => array(), // Для САБов
  );

  $fleets_outcome = &$outcome[UBE_FLEETS];
  foreach ($combat_data[UBE_FLEETS] as $fleet_id => &$fleet_info) {
    if ($fleet_info[UBE_FLEET_GROUP]) {
      $db_save[UBE_FLEET_GROUP][$fleet_info[UBE_FLEET_GROUP]] = $fleet_info[UBE_FLEET_GROUP];
    }

    $fleet_info[UBE_COUNT] = $fleet_info[UBE_COUNT] ? $fleet_info[UBE_COUNT] : array();
    $fleets_outcome[$fleet_id][UBE_UNITS_LOST] = $fleets_outcome[$fleet_id][UBE_UNITS_LOST] ? $fleets_outcome[$fleet_id][UBE_UNITS_LOST] : array();

    $fleet_query = array();
    $db_changeset = array();
    $old_fleet_count = array_sum($fleet_info[UBE_COUNT]);
    $new_fleet_count = $old_fleet_count - array_sum($fleets_outcome[$fleet_id][UBE_UNITS_LOST]);
    // Перебираем юниты если во время боя количество юнитов изменилось и при этом во флоту остались юниты или это планета
    if ($new_fleet_count != $old_fleet_count && (!$fleet_id || $new_fleet_count)) {
      // Просматриваем результаты изменения флотов
      foreach ($fleet_info[UBE_COUNT] as $unit_id => $unit_count) {
        // Перебираем аутком на случай восстановления юнитов
        $units_lost = (float)$fleets_outcome[$fleet_id][UBE_UNITS_LOST][$unit_id];

        $units_left = $unit_count - $units_lost;
        if ($fleet_id) {
          // Не планета - всегда сразу записываем строку итогов флота
          $fleet_query[$unit_id] = "{$unit_id},{$units_left}";
        } elseif ($units_lost) {
          // Планета - записываем в ИД юнита его потери только если есть потери
          $db_changeset['unit'][] = OldDbChangeSet::db_changeset_prepare_unit($unit_id, -$units_lost, $combat_data[UBE_PLAYERS][$destination_user_id][UBE_PLAYER_DATA], $planet_id);
        }
      }

      if ($fleet_id) {
        // Для флотов перегенерируем массив как одно вхождение в SET SQL-запроса
        $fleet_query = array(
          'fleet_array' => implode(';', $fleet_query),
        );
      }
    }

    $fleet_delta = array();
    // Если во флоте остались юниты или это планета - генерируем изменение ресурсов
    if ($new_fleet_count || !$fleet_id) {
      foreach (sn_get_groups('resources_loot') as $resource_id) {
        $resource_change = (float)$fleets_outcome[$fleet_id][UBE_RESOURCES_LOOTED][$resource_id] + (float)$fleets_outcome[$fleet_id][UBE_CARGO_DROPPED][$resource_id];
        if ($resource_change) {
          $resource_db_name = ($fleet_id ? 'fleet_resource_' : '') . pname_resource_name($resource_id);
          $fleet_delta[$resource_db_name] = -($resource_change);
        }
      }
    }

    if ($fleet_id && $new_fleet_count) {
      // Если защитник и не РМФ - отправляем флот назад
      if (($fleet_info[UBE_FLEET_TYPE] == UBE_DEFENDERS && !$outcome[UBE_SFR]) || $fleet_info[UBE_FLEET_TYPE] == UBE_ATTACKERS) {
        $fleet_query['fleet_mess'] = 1;
      }
    }

    if ($fleet_id) // Не планета
    {
      if ($fleet_info[UBE_FLEET_TYPE] == UBE_ATTACKERS && $outcome[UBE_MOON_REAPERS] == UBE_MOON_REAPERS_DIED) {
        $new_fleet_count = 0;
      }

      if ($new_fleet_count) {
        if (!empty($fleet_query) || !empty($fleet_delta)) {
          $fleet_query['fleet_amount'] = $new_fleet_count;
          DbFleetStatic::fleet_update_set($fleet_id, $fleet_query, $fleet_delta);
        }
      } else {
        // Удаляем пустые флоты
        DbFleetStatic::db_fleet_delete($fleet_id);
        DBStaticUnit::db_unit_list_delete(0, LOC_FLEET, $fleet_id, 0);
      }
    } else // Планета
    {
      // Сохраняем изменения ресурсов - если они есть
      if (!empty($fleet_delta)) {
        $temp = array();
        foreach ($fleet_delta as $resource_db_name => $resource_amount) {
          $temp[] = "`{$resource_db_name}` = `{$resource_db_name}` + ({$resource_amount})";
        }
        DBStaticPlanet::db_planet_set_by_id($planet_id, implode(',', $temp));
      }
      if (!empty($db_changeset)) // Сохраняем изменения юнитов на планете - если они есть
      {
        OldDbChangeSet::db_changeset_apply($db_changeset);
      }
    }
  }

  // TODO: Связать сабы с флотами констраинтами ON DELETE SET NULL
  if (!empty($db_save[UBE_FLEET_GROUP])) {
    DbFleetStatic::dbAcsDelete($db_save[UBE_FLEET_GROUP]);

    $db_save[UBE_FLEET_GROUP] = implode(',', $db_save[UBE_FLEET_GROUP]);
  }

  if ($outcome[UBE_MOON] == UBE_MOON_CREATE_SUCCESS) {
    $moon_row = uni_create_moon($planet_info[PLANET_GALAXY], $planet_info[PLANET_SYSTEM], $planet_info[PLANET_PLANET], $destination_user_id, $outcome[UBE_MOON_SIZE], false);
    $outcome[UBE_MOON_NAME] = $moon_row['name'];
    unset($moon_row);
  } elseif ($outcome[UBE_MOON] == UBE_MOON_DESTROY_SUCCESS) {
    DBStaticPlanet::db_planet_delete_by_id($planet_id);
  }

  $bashing_list = array();
  foreach ($combat_data[UBE_PLAYERS] as $player_id => $player_info) {
    if ($player_info[UBE_ATTACKER]) {
      if ($outcome[UBE_MOON] != UBE_MOON_DESTROY_SUCCESS) {
        $bashing_list[] = "({$player_id}, {$planet_id}, {$combat_data[UBE_TIME]})";
      }
      if ($combat_data[UBE_OPTIONS][UBE_MISSION_TYPE] == MT_ATTACK && $combat_data[UBE_OPTIONS][UBE_DEFENDER_ACTIVE]) {
        $str_loose_or_win = $outcome[UBE_COMBAT_RESULT] == UBE_COMBAT_RESULT_WIN ? 'raidswin' : 'raidsloose';
        db_user_set_by_id($player_id, "`xpraid` = `xpraid` + 1, `raids` = `raids` + 1, `{$str_loose_or_win}` = `{$str_loose_or_win}` + 1");
      }
    }
  }
  $bashing_list = implode(',', $bashing_list);
  if ($bashing_list) {
    doquery("INSERT INTO {{bashing}} (bashing_user_id, bashing_planet_id, bashing_time) VALUES {$bashing_list};");
  }
}
