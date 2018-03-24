<?php

use DBAL\OldDbChangeSet;
use Planet\DBStaticPlanet;
use Que\DBStaticQue;
use Que\QueUnitStatic;

function que_get_unit_que($unit_id) {
  $que_type = false;
  foreach(sn_get_groups('ques') as $que_id => $que_data) {
    if(in_array($unit_id, $que_data['unit_list'])) {
      $que_type = $que_id;
      break;
    }
  }

  return $que_type;
}


function que_get_max_que_length($user, $planet, $que_id, $que_data = null) {
  global $config;

  if(empty($que_data)) {
    $que_data = sn_get_groups('ques');
    $que_data = $que_data[$que_id];
  }


  $que_length = 1;
  switch($que_id) {
    case QUE_RESEARCH:
      $que_length = $config->server_que_length_research + mrc_get_level($user, '', UNIT_PREMIUM); // TODO - вынести в модуль
    break;

    default:
      $que_length = isset($que_data['length']) ? $que_data['length'] + mrc_get_level($user, $planet, $que_data['mercenary']) : 1;
  }

  return $que_length;
}

function eco_que_str2arr($que_str) {
  $que_arr = explode(';', $que_str);
  foreach($que_arr as $que_index => &$que_item) {
    if($que_item) {
      $que_item = explode(',', $que_item);
    } else {
      unset($que_arr[$que_index]);
    }
  }
  return $que_arr;
}

function eco_que_arr2str($que_arr) {
  foreach($que_arr as &$que_item) {
    $que_item = implode(',', $que_item);
  }
  return implode(';', $que_arr);
}


function que_build($user, $planet, $build_mode = BUILD_CREATE, $redirect = true) {
  global $lang, $config;

  $is_autoconvert = false;
  if($build_mode == BUILD_AUTOCONVERT || sys_get_param_int('auto_convert')) {
    $build_mode = BUILD_CREATE;
    $is_autoconvert = true;
  }

  $unit_amount_qued = 0;
  try {
    if(!$user['id']) {
      throw new exception('{Нет идентификатора пользователя - сообщите Администрации}', ERR_ERROR); // TODO EXCEPTION
    }

    $unit_id = sys_get_param_int('unit_id');
    /*
    if(!$unit_id && is_array($unit_list = sys_get_param('fmenge')))
    {
      foreach($unit_list as $unit_id => $unit_amount) if($unit_amount) break;
    }
    */
    if(!$unit_id) {
      throw new exception('{Нет идентификатора юнита - сообщите Администрации}', ERR_ERROR); // TODO EXCEPTION
    }

    $que_id = que_get_unit_que($unit_id);
    if(!$que_id) {
      throw new exception('{Неправильный тип очереди - сообщите Администрации}', ERR_ERROR); // TODO EXCEPTION
    }

    if($build_mode == BUILD_DESTROY && $que_id != QUE_STRUCTURES) {
      throw new exception('{Уничтожать можно только здания на планете}', ERR_ERROR); // TODO EXCEPTION
    }

    $que_data = sn_get_groups('ques');
    $que_data = $que_data[$que_id];

    // TODO Переделать под подочереди
    if($que_id == QUE_STRUCTURES) {
      $sn_groups_build_allow = sn_get_groups('build_allow');
      $que_data['unit_list'] = $sn_groups_build_allow[$planet['planet_type']];

      if(!isset($que_data['unit_list'][$unit_id])) {
        throw new exception('{Это здание нельзя строить на ' . ($planet['planet_type'] == PT_PLANET ? 'планете' : 'луне'), ERR_ERROR); // TODO EXCEPTION
      }
    }
    /*
    // TODO Разделить очереди для Верфи и Обороны
    elseif($que_id == QUE_HANGAR)
    {
      $que_data['mercenary'] = in_array($unit_id, sn_get_groups('defense')) ? MRC_FORTIFIER : MRC_ENGINEER;
    }
    elseif($que_id == QUE_HANGAR)
    {
      $que_data['mercenary'] = in_array($unit_id, sn_get_groups('defense')) ? MRC_FORTIFIER : MRC_ENGINEER;
    }
    */


    sn_db_transaction_start();
    // Это нужно, что бы заблокировать пользователя и работу с очередями
    $user = db_user_by_id($user['id']);
    // Это нужно, что бы заблокировать планету от списания ресурсов
    if(isset($planet['id']) && $planet['id']) {
      $planet = DBStaticPlanet::db_planet_by_id($planet['id'], true);
    } else {
      $planet['id'] = 0;
    }

    $planet_id = $que_id == QUE_RESEARCH ? 0 : intval($planet['id']);

    $que = que_get($user['id'], $planet['id'], $que_id, true);
    $in_que = &$que['in_que'][$que_id][$user['id']][$planet_id];
    $que_max_length = que_get_max_que_length($user, $planet, $que_id, $que_data);
    // TODO Добавить вызовы функций проверок текущей и максимальной длин очередей
    if(count($in_que) >= $que_max_length) {
      throw new exception('{Все слоты очереди заняты}', ERR_ERROR); // TODO EXCEPTION
    }

    // TODO Отдельно посмотреть на уничтожение зданий - что бы можно было уничтожать их без планов
    switch(eco_can_build_unit($user, $planet, $unit_id)) {
      case BUILD_ALLOWED: break;
      case BUILD_UNIT_BUSY: throw new exception('{Строение занято}', ERR_ERROR); break; // TODO EXCEPTION eco_bld_msg_err_laboratory_upgrading
      // case BUILD_REQUIRE_NOT_MEET:
      default:
        if($build_mode == BUILD_CREATE) {
          throw new exception('{Требования не удовлетворены}', ERR_ERROR);
        }
        break; // TODO EXCEPTION eco_bld_msg_err_requirements_not_meet
    }

    $unit_amount = floor(sys_get_param_float('unit_amount', 1));
    $unit_amount_qued = $unit_amount;
    $units_qued = isset($in_que[$unit_id]) ? $in_que[$unit_id] : 0;
    $unit_level = mrc_get_level($user, $planet, $unit_id, true, true) + $units_qued;
    if($unit_max = get_unit_param($unit_id, P_MAX_STACK)) {
      if($unit_level >= $unit_max) {
        throw new exception('{Максимальное количество юнитов данного типа уже достигнуто или будет достигнуто по окончанию очереди}', ERR_ERROR); // TODO EXCEPTION
      }
      $unit_amount = max(0, min($unit_amount, $unit_max - $unit_level));
    }

    if($unit_amount < 1) {
      throw new exception('{Неправильное количество юнитов - сообщите Администрации}', ERR_ERROR); // TODO EXCEPTION
    }

    /*
    if($unit_max && $unit_level + $unit_amount > $unit_max)
    {
      throw new exception("Постройка {$unit_amount} {$lang['tech'][$unit_id]} приведет к привышению максимально возможного количества юнитов данного типа", ERR_ERROR); // TODO EXCEPTION
    }
    */

    // TODO Переделать eco_unit_busy для всех типов зданий
    //  if(eco_unit_busy($user, $planet, $que, $unit_id))
    //  {
    //    die('Unit busy'); // TODO EXCEPTION
    //  }
    if(get_unit_param($unit_id, P_STACKABLE)) {
      // TODO Поле 'max_Lot_size' для ограничения размера стэка в очереди - то ли в юниты, то ли в очередь
      if(in_array($unit_id, $group_missile = sn_get_groups('missile'))) {
        // TODO Поле 'container' - указывает на родительску структуру, в которой хранится данный юнит и по вместительности которой нужно применять размер юнита
        $used_silo = 0;
        foreach($group_missile as $missile_id) {
          $missile_qued = isset($in_que[$missile_id]) ? $in_que[$missile_id] : 0;
          $used_silo += (mrc_get_level($user, $planet, $missile_id, true, true) + $missile_qued) * get_unit_param($missile_id, P_UNIT_SIZE);
        }
        $free_silo = mrc_get_level($user, $planet, STRUC_SILO) * get_unit_param(STRUC_SILO, P_CAPACITY) - $used_silo;
        if($free_silo <= 0) {
          throw new exception('{Ракетная шахта уже заполнена или будет заполнена по окончанию очереди}', ERR_ERROR); // TODO EXCEPTION
        }
        $unit_size = get_unit_param($unit_id, P_UNIT_SIZE);
        if($free_silo < $unit_size) {
          throw new exception("{В ракетной шахте нет места для {$lang['tech'][$unit_id]}}", ERR_ERROR); // TODO EXCEPTION
        }
        $unit_amount = max(0, min($unit_amount, floor($free_silo / $unit_size)));
      }
      $unit_level = $new_unit_level = 0;
    } else {
      $unit_amount = 1;
      if($que_id == QUE_STRUCTURES) {
        // if($build_mode == BUILD_CREATE && eco_planet_fields_max($planet) - $planet['field_current'] - $que['sectors'][$planet['id']] <= 0)
        $sectors_qued = is_array($in_que) ? array_sum($in_que) : 0;
        if($build_mode == BUILD_CREATE && eco_planet_fields_max($planet) - $planet['field_current'] - $sectors_qued <= 0)
        {
          throw new exception('{Не хватает секторов на планете}', ERR_ERROR); // TODO EXCEPTION
        }
        // И что это я такое написал? Зачем?
        //if($build_mode == BUILD_DESTROY && $planet['field_current'] <= $que['amounts'][$que_id])
        //{
        //  die('Too much buildings'); // TODO EXCEPTION
        //}
      }
      $build_multiplier = $build_mode == BUILD_CREATE ? 1 : -1;
      $new_unit_level = $unit_level + $unit_amount * $build_multiplier;
    }

    $build_data = eco_get_build_data($user, $planet, $unit_id, $unit_level);

    $exchange = array();
    $market_get_autoconvert_cost = market_get_autoconvert_cost();
    if($is_autoconvert && $build_data[BUILD_AUTOCONVERT]) {
      $dark_matter = mrc_get_level($user, null, RES_DARK_MATTER);
      if(mrc_get_level($user, null, RES_DARK_MATTER) < $market_get_autoconvert_cost) {
        throw new exception("{Нет хватает " . ($market_get_autoconvert_cost - $dark_matter) . "ТМ на постройки с автоконвертацией ресурсов}", ERR_ERROR); // TODO EXCEPTION
      }

      !get_unit_param($unit_id, P_STACKABLE) ? $unit_amount = 1 : false;
      $resources_loot = sn_get_groups('resources_loot');
      $resource_got = array();
      $resource_exchange_rates = array();
      $resource_diff = array();
      $all_positive = true;
      foreach($resources_loot as $resource_id) {
        $resource_db_name = pname_resource_name($resource_id);
        $resource_got[$resource_id] = floor(mrc_get_level($user, $planet, $resource_id));
        $resource_exchange_rates[$resource_id] = $config->__get("rpg_exchange_{$resource_db_name}");
        $resource_diff[$resource_id] = $resource_got[$resource_id] - $build_data[BUILD_CREATE][$resource_id] * $unit_amount;
        $all_positive = $all_positive && ($resource_diff[$resource_id] > 0);
      }
      // Нужна автоконвертация
      if($all_positive) {
        $is_autoconvert = false;
      } else {
        foreach($resource_diff as $resource_diff_id => &$resource_diff_amount) {
          if($resource_diff_amount >= 0) {
            continue;
          }
          foreach($resource_diff as $resource_got_id => &$resource_got_amount) {
            if($resource_got_amount <= 0) {
              continue;
            }
            $current_exchange = $resource_exchange_rates[$resource_got_id] / $resource_exchange_rates[$resource_diff_id];

            $will_exchage_to = min(-$resource_diff_amount, floor($resource_got_amount * $current_exchange));
            $will_exchage_from = $will_exchage_to / $current_exchange;

            $resource_diff_amount += $will_exchage_to;
            $resource_got_amount -= $will_exchage_from;
            $exchange[$resource_diff_id] += $will_exchage_to;
            $exchange[$resource_got_id] -= $will_exchage_from;
          }
        }

        $is_autoconvert_ok = true;
        foreach($resource_diff as $resource_diff_amount2) {
          if($resource_diff_amount2 < 0) {
            $is_autoconvert_ok = false;
            break;
          }
        }

        if($is_autoconvert_ok) {
          $build_data['RESULT'][$build_mode] = BUILD_ALLOWED;
          $build_data['CAN'][$build_mode] = $unit_amount;
        } else {
          $unit_amount = 0;
        }
      }
    }
    $unit_amount = min($build_data['CAN'][$build_mode], $unit_amount);
    if($unit_amount <= 0) {
      throw new exception('{Не хватает ресурсов}', ERR_ERROR); // TODO EXCEPTION
    }

    if($new_unit_level < 0) {
      throw new exception('{Нельзя уничтожить больше юнитов, чем есть}', ERR_ERROR); // TODO EXCEPTION
    }

    if($build_data['RESULT'][$build_mode] != BUILD_ALLOWED) {
      throw new exception('{Строительство блокировано}', ERR_ERROR); // TODO EXCEPTION
    }

    if($is_autoconvert) {
      ksort($exchange);
      ksort($resource_got);
      db_change_units($user, $planet, array(
        RES_METAL     => !empty($exchange[RES_METAL]) ? $exchange[RES_METAL] : 0,
        RES_CRYSTAL   => !empty($exchange[RES_CRYSTAL]) ? $exchange[RES_CRYSTAL] : 0,
        RES_DEUTERIUM => !empty($exchange[RES_DEUTERIUM]) ? $exchange[RES_DEUTERIUM] : 0,
      ));
      rpg_points_change($user['id'], RPG_BUILD_AUTOCONVERT, -$market_get_autoconvert_cost, sprintf(
        $lang['bld_autoconvert'], $unit_id, $unit_amount, uni_render_planet_full($planet, '', false, true), $lang['tech'][$unit_id],
        sys_unit_arr2str($build_data[BUILD_CREATE]), sys_unit_arr2str($resource_got), sys_unit_arr2str($exchange)
      ));
    }

    $unit_amount_qued = 0;
    while($unit_amount > 0 && count($que['ques'][$que_id][$user['id']][$planet_id]) < $que_max_length) {
      $place = min($unit_amount, MAX_FLEET_OR_DEFS_PER_ROW);
//      $sqlBlock = QueUnitStatic::que_unit_make_sql($unit_id, $user, $planet, $build_data, $new_unit_level, $place, $build_mode);
      $sqlBlock = SN::$gc->pimp->que_unit_make_sql($unit_id, $user, $planet, $build_data, $new_unit_level, $place, $build_mode);

      array_walk($sqlBlock, function (&$value, $field) {
        if($value === null) {
          $value = 'NULL';
        } elseif (is_string($value)) {
          $value = "'{$value}'";
        }

        $value = "`{$field}` = {$value}";
      });

      DBStaticQue::db_que_set_insert(implode(',', $sqlBlock)
//        "`que_player_id` = {$user['id']},
//      `que_planet_id` = {$planet_id},
//      `que_planet_id_origin` = {$planet_id_origin},
//      `que_type` = {$que_type},
//      `que_time_left` = {$build_data[RES_TIME][$build_mode]},
//      `que_unit_id` = {$unit_id},
//      `que_unit_amount` = {$unit_amount},
//      `que_unit_mode` = {$build_mode},
//      `que_unit_level` = {$unit_level},
//      `que_unit_time` = {$build_data[RES_TIME][$build_mode]},
//      `que_unit_price` = '{$resource_list}'"
      );


      $unit_amount -= $place;
      $que = que_get($user['id'], $planet['id'], $que_id, true);
      $unit_amount_qued += $place;
    }

    sn_db_transaction_commit();

    if($redirect) {
      sys_redirect("{$_SERVER['PHP_SELF']}?mode=" . sys_get_param_str('mode') . "&ally_id=" . sys_get_param_id('ally_id'));
    }

    $operation_result = array(
      'STATUS'  => ERR_NONE,
      'MESSAGE' => '{Строительство начато}',
    );
  } catch(exception $e) {
    sn_db_transaction_rollback();
    $operation_result = array(
      'STATUS'  => in_array($e->getCode(), array(ERR_NONE, ERR_WARNING, ERR_ERROR)) ? $e->getCode() : ERR_ERROR,
      'MESSAGE' => $e->getMessage()
    );
  }

  if(!empty($operation_result['MESSAGE'])) {
    $operation_result['MESSAGE'] .= ' ' . ($unit_amount_qued ? $unit_amount_qued : $unit_amount) . 'x[' . $lang['tech'][$unit_id] . ']';
  }

  return $operation_result;
}





function que_recalculate($old_que) {
  $new_que = array();

  if(!is_array($old_que['items'])) {
    return $new_que;
  }
  foreach($old_que['items'] as $row) {
    if(!isset($row) || !$row || $row['que_unit_amount'] <= 0) {
      continue;
    }

    $new_que['items'][] = $row;

    $new_que['in_que'][$row['que_type']][$row['que_player_id']][intval($row['que_planet_id'])][$row['que_unit_id']] += $row['que_unit_amount'] * ($row['que_unit_mode'] == BUILD_CREATE ? 1 : -1);
    $new_que['in_que_abs'][$row['que_type']][$row['que_player_id']][intval($row['que_planet_id'])][$row['que_unit_id']] += $row['que_unit_amount'];

    $last_id = count($new_que['items']) - 1;

    if($row['que_planet_id']) {
      $new_que['planets'][$row['que_planet_id']][$row['que_type']][] = &$new_que['items'][$last_id];
    } elseif($row['que_type'] == QUE_RESEARCH) {
      $new_que['players'][$row['que_player_id']][$row['que_type']][] = &$new_que['items'][$last_id];
    }
    $new_que['ques'][$row['que_type']][$row['que_player_id']][intval($row['que_planet_id'])][] = &$new_que['items'][$last_id];

    // Это мы можем посчитать по длине очереди в players и planets
    //$ques['used_slots'][$row['que_type']][$row['que_player_id']][intval($row['que_planet_id'])][$row['que_unit_id']]++;
  }

  return $new_que;
}

function que_get($user_id, $planet_id = null, $que_type = false, $for_update = false) {
  return SN::db_que_list_by_type_location($user_id, $planet_id, $que_type, $for_update);
}

function que_delete($que_type, $user = array(), $planet = array(), $clear = false) {
  $planets_locked = array();

  // TODO: Some checks
  sn_db_transaction_start();
  $user = db_user_by_id($user['id'], true);
  $planet['id'] = $planet['id'] && $que_type !== QUE_RESEARCH ? $planet['id'] : 0;
  $global_que = que_get($user['id'], $planet['id'], $que_type, true);

  if(!empty($global_que['ques'][$que_type][$user['id']][$planet['id']])) {
    $que = array_reverse($global_que['ques'][$que_type][$user['id']][$planet['id']]);

    foreach($que as $que_item) {
      DBStaticQue::db_que_delete_by_id($que_item['que_id']);

      if($que_item['que_planet_id_origin']) {
        $planet['id'] = $que_item['que_planet_id_origin'];
      }

      if(!isset($planets_locked[$planet['id']])) {
        $planets_locked[$planet['id']] = $planet['id'] ? DBStaticPlanet::db_planet_by_id($planet['id'], true) : $planet;
      }

      $build_data = sys_unit_str2arr($que_item['que_unit_price']);

      db_change_units($user, $planets_locked[$planet['id']], array(
        RES_METAL     => $build_data[RES_METAL] * $que_item['que_unit_amount'],
        RES_CRYSTAL   => $build_data[RES_CRYSTAL] * $que_item['que_unit_amount'],
        RES_DEUTERIUM => $build_data[RES_DEUTERIUM] * $que_item['que_unit_amount'],
      ));

      if(!$clear) {
        break;
      }
    }

    if(is_numeric($planet['id'])) {
      DBStaticPlanet::db_planet_set_by_id($planet['id'], "`que_processed` = UNIX_TIMESTAMP(NOW())");
    }
    elseif(is_numeric($user['id'])) {
      db_user_set_by_id($user['id'], '`que_processed` = UNIX_TIMESTAMP(NOW())');
    }

    sn_db_transaction_commit();
  } else {
    sn_db_transaction_rollback();
  }

  sys_redirect("{$_SERVER['PHP_SELF']}?mode={$que_type}" . "&ally_id=" . sys_get_param_id('ally_id'));
}


function que_tpl_parse_element($que_element, $short_names = false) {
  global $lang;

  return
    array(
      'ID' => $que_element['que_unit_id'],
      'QUE' => $que_element['que_type'],
      'NAME' => $short_names && !empty($lang['tech_short'][$que_element['que_unit_id']])
                  ? $lang['tech_short'][$que_element['que_unit_id']]
                  : $lang['tech'][$que_element['que_unit_id']],
      'TIME' => $que_element['que_time_left'],
      'TIME_FULL' => $que_element['que_unit_time'],
      'AMOUNT' => $que_element['que_unit_amount'],
      'LEVEL' => $que_element['que_unit_level'],
    );
}

/*
 *
 * Процедура парсит очереди текущего игрока в темплейт
 *
 * TODO: Переместить в хелперы темплейтов
 * TODO: Сделать поддержку нескольких очередей
 *
 * $que_type - тип очереди ОБЯЗАТЕЛЬНО
 * $que - либо результат $que_get(), либо конкретная очередь
 *
 */
function que_tpl_parse(&$template, $que_type, $user, $planet = array(), $que = null, $short_names = false) {
  // TODO: Переделать для $que_type === false
  $planet['id'] = $planet['id'] ? $planet['id'] : 0;

  if(!is_array($que)) {
    $que = que_get($user['id'], $planet['id'], $que_type);
  }

  if(is_array($que) && isset($que['items'])) {
    $que = $que['ques'][$que_type][$user['id']][$planet['id']];
  }

  if($que) {
    foreach($que as $que_element) {
      $template->assign_block_vars('que', que_tpl_parse_element($que_element, $short_names));
    }
  }

  if($que_type == QUE_RESEARCH) {
    // TODO Исправить
//    $template->assign_var('RESEARCH_ONGOING', count($global_que[QUE_RESEARCH][0]) >= $config->server_que_length_research);
  }
}


/*
 *
 * Эта процедура должна вызываться исключительно в транзакции!!!
 *
 * $user_id
 *   (integer) - обрабатываются глообальные очереди пользователя
 * $planet_id
 *   null - обработка очередей планет не производится
 * // TODO    false/0 - обрабатываются очереди всех планет по $user_id
 *   (integer) - обрабатываются локальные очереди для планеты. Нужно, например, в обработчике флотов
 *
 */
function que_process(&$user, $planet = null, $on_time = SN_TIME_NOW) {
  sn_db_transaction_check(true);

  $que = array();

  // Блокируем пользователя. Собственно, запись о нём нам не нужна - будем использовать старую
  $user = db_user_by_id($user['id'], true);

  $time_left[$user['id']][0] = max(0, $on_time - $user['que_processed']);
  if($planet === null && !$time_left[$user['id']][0]) {
    // TODO
    return $que;
  }

  // Определяем, какие очереди нам нужны и получаем их
  $que_type_id = $planet === null ? QUE_RESEARCH : false;
  $planet = intval(is_array($planet) ? $planet['id'] : $planet); // В $planet у нас теперь только её ID или шаблон null/0/false
  $que = que_get($user['id'], $planet, $que_type_id, true);
  if(empty($que['items'])) {
    return $que;
  }

  $planet_list = array();
  if($planet !== null) {
    // Если нужно изменять данные на планетах - блокируем планеты и получаем данные о них
    // TODO - от них не надо ничего, кроме ID и que_processed
    $planet_row = DBStaticPlanet::db_planet_list_by_user_or_planet($user['id'], $planet);
    $planet_list[$planet_row['id']] = $planet_row;
    $time_left[$planet_row['id_owner']][$planet_row['id']] = max(0, $on_time - $planet_row['que_processed']);
  }

  // Теперь в $time_left лежит время обсчета всех очередей по каждой из планеты
  if(array_sum($time_left[$user['id']]) == 0) {
    return $que;
  }

  $db_changeset = array();
  $unit_changes = array();
  foreach($que['items'] as &$que_item) {
    $que_player_id = &$que_item['que_player_id'];
    $que_planet_id = intval($que_item['que_planet_id']);

    $que_time_left = &$que['time_left'][$que_player_id][$que_planet_id][$que_item['que_type']];
    if(!isset($que_time_left)) {
      $que_time_left = $time_left[$que_player_id][$que_planet_id];
    }
    if($que_time_left <= 0 || $que_item['que_unit_amount'] <= 0) {
      continue;
    }
    // Дальше мы идем, если только осталось время в очереди И юниты к постройке

    // Вычисляем, сколько целых юнитов будет построено - от 0 до количества юнитов в очереди
    $unit_processed = min($que_item['que_unit_amount'] - 1, floor($que_time_left / $que_item['que_unit_time']));
    // Вычитаем это время из остатков
    $que_time_left -= $unit_processed * $que_item['que_unit_time'];

    // Теперь работаем с остатком времени на юните. Оно не может быть равно или меньше нуля

    // Если времени в очереди осталось не меньше, чем время текущего юнита - значит мы достроили юнит
    if($que_time_left >= $que_item['que_time_left']) {
      // Увеличиваем количество отстроенных юнитов
      $unit_processed++;
      // Вычитаем из времени очереди потраченное на постройку время
      $que_time_left -= $que_item['que_time_left'];
      // Полное время юнита равно времени нового юнита
      $que_item['que_time_left'] = $que_item['que_unit_time'];
      // Тут у нас может остатся время очереди - если постройка была не последняя
    }
    // Изменяем количество оставшихся юнитов
    $que_item['que_unit_amount'] -= $unit_processed;

    // Если еще остались юниты - значит ВСЁ оставшееся время приходится на достройку следующего юнита
    if($que_item['que_unit_amount'] > 0) {
      $que_item['que_time_left'] = $que_item['que_time_left'] - $que_time_left;
      $que_time_left = 0;
    }

    if($que_item['que_unit_amount'] <= 0) {
      $db_changeset['que'][] = array(
        'action' => SQL_OP_DELETE,
        P_VERSION => 1,
        'where' => array(
          "que_id" => $que_item['que_id'],
        ),
      );
    } else {
      $db_changeset['que'][] = array(
        'action' => SQL_OP_UPDATE,
        P_VERSION => 1,
        'where' => array(
          "que_id" => $que_item['que_id'],
        ),
        'fields' => array(
          'que_unit_amount' => array(
            'delta' => -$unit_processed
          ),
          'que_time_left' => array(
            'set' => $que_item['que_time_left']
          ),
        ),
      );
    }

    if($unit_processed) {
      $unit_processed_delta = $unit_processed * ($que_item['que_unit_mode'] == BUILD_CREATE ? 1 : -1);
      $unit_changes[$que_player_id][$que_planet_id][$que_item['que_unit_id']] += $unit_processed_delta;
    }
  }

  foreach($time_left as $player_id => $planet_data) {
    foreach($planet_data as $planet_id => $time_on_planet) {
      $table = $planet_id ? 'planets' : 'users';
      $id = $planet_id ? $planet_id : $player_id;
      $db_changeset[$table][] = array(
        'action' => SQL_OP_UPDATE,
        P_VERSION => 1,
        'where' => array(
          "id" => $id,
        ),
        'fields' => array(
          'que_processed' => array(
            'set' => $on_time,
          ),
        ),
      );

      if(is_array($unit_changes[$player_id][$planet_id])) {
        foreach($unit_changes[$player_id][$planet_id] as $unit_id => $unit_amount) {
          $db_changeset['unit'][] = OldDbChangeSet::db_changeset_prepare_unit($unit_id, $unit_amount, $user, $planet_id ? $planet_id : null);
        }
      }
    }
  }

  $que = que_recalculate($que);

  // TODO: Re-enable quests for Alliances
  if(!empty($unit_changes) && !$user['user_as_ally']) {
    $quest_list = qst_get_quests($user['id'], QUEST_STATUS_ALL);
    $quest_triggers = qst_active_triggers($quest_list);
    $quest_rewards = array();
    $quest_statuses = array();


    $xp_incoming = array();
    foreach($unit_changes as $user_id => $planet_changes) {
      foreach($planet_changes as $planet_id => $changes) {
        $planet_this = $planet_id ? SN::db_get_record_by_id(LOC_PLANET, $planet_id) : array();
        foreach($changes as $unit_id => $unit_value) {
          $que_id = que_get_unit_que($unit_id);
          $unit_level_new = mrc_get_level($user, $planet_this, $unit_id, false, true) + $unit_value;
          if($que_id == QUE_STRUCTURES || $que_id == QUE_RESEARCH) {
            $build_data = eco_get_build_data($user, $planet_this, $unit_id, $unit_level_new - 1);
            $build_data = $build_data[BUILD_CREATE];
            foreach(sn_get_groups('resources_loot') as $resource_id) {
              $xp_incoming[$que_id] += $build_data[$resource_id]; // TODO - добавить конверсию рейтов обмена
            }
          }

          if(is_array($quest_triggers)) {
            // TODO: Check mutiply condition quests
            $quest_trigger_list = array_keys($quest_triggers, $unit_id);

            if(is_array($quest_trigger_list)) {
              foreach($quest_trigger_list as $quest_id) {
                if ($quest_list[$quest_id]['quest_status_status'] != QUEST_STATUS_COMPLETE) {
                  if ($quest_list[$quest_id]['quest_unit_amount'] <= $unit_level_new) {
                    $quest_rewards[$quest_id][$user_id][$planet_id] = $quest_list[$quest_id]['quest_rewards_list'];
                    $quest_statuses[$quest_id] = QUEST_STATUS_COMPLETE;
                  } else {
                    $quest_statuses[$quest_id] = QUEST_STATUS_STARTED;
                  }
                }
              }
            }
          }
        }
      }
    }
    // TODO: Изменить начисление награды за квесты на ту планету, на которой происходил ресеч
    qst_reward($user, $quest_rewards, $quest_list, $quest_statuses);

    foreach($xp_incoming as $que_id => $xp) {
      rpg_level_up($user, $que_id == QUE_RESEARCH ? RPG_TECH : RPG_STRUCTURE, $xp / 1000);
    }
  }

  OldDbChangeSet::db_changeset_apply($db_changeset);

  // TODO Сообщения о постройке

  return $que;
}
