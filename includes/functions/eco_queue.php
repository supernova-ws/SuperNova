<?php

function que_get_unit_que($unit_id)
{
  $que_type = false;
  foreach(sn_get_groups('ques') as $que_id => $que_data)
  {
    if(in_array($unit_id, $que_data['unit_list']))
    {
      $que_type = $que_id;
      break;
    }
  }

  if(!$que_type)
  {
    die('wrong que type');
  }

  return $que_type;
}


function que_get_max_que_length($user, $planet, $que_id, $que_data = null)
{
  global $config;

  if(empty($que_data))
  {
    $que_data = sn_get_groups('ques');
    $que_data = $que_data[$que_id];
  }


  $que_length = 1;
  switch($que_id)
  {
    case QUE_RESEARCH:
      $que_length = $config->server_que_length_research;
    break;

    default:
      $que_length = isset($que_data['length']) ? $que_data['length'] + mrc_get_level($user, $planet, $que_data['mercenary']) : 1;
  }

  return $que_length;
}

function eco_que_str2arr($que_str)
{
  $que_arr = explode(';', $que_str);
  foreach($que_arr as $que_index => &$que_item)
  {
    if($que_item)
    {
      $que_item = explode(',', $que_item);
    }
    else
    {
      unset($que_arr[$que_index]);
    }
  }
  return $que_arr;
}

function eco_que_arr2str($que_arr)
{
  foreach($que_arr as &$que_item)
  {
    $que_item = implode(',', $que_item);
  }
  return implode(';', $que_arr);
}





function que_build($user, $planet, $build_mode = BUILD_CREATE)
{
  try
  {
    if(!$user['id'])
    {
      die('No user ID'); // TODO EXCEPTION
    }

    $unit_amount = 1;

    $unit_id = sys_get_param_int('unit_id');
    if(!$unit_id && is_array($unit_list = sys_get_param('fmenge')))
    {
      foreach($unit_list as $unit_id => $unit_amount)
      {
        if($unit_amount)
        {
          break;
        }
      }
    }
    if(!$unit_id)
    {
      die('no unit_id'); // TODO EXCEPTION
    }

    $unit_amount = floor($unit_amount);
    if($unit_amount < 1)
    {
      die('Unit amount is wrong'); // TODO EXCEPTION
    }

    $que_id = que_get_unit_que($unit_id);
    if(!$que_id)
    {
      die('No que ID'); // TODO EXCEPTION
    }

    $que_data = sn_get_groups('ques');
    $que_data = $que_data[$que_id];

    // TODO Переделать под подочереди
    if($que_id == QUE_STRUCTURES)
    {
      $sn_groups_build_allow = sn_get_groups('build_allow');
      $que_data['unit_list'] = $sn_groups_build_allow[$planet['planet_type']];
    }
    // TODO Разделить очереди для Верфи и Обороны
    elseif($que_id == QUE_HANGAR)
    {
      $que_data['mercenary'] = in_array($unit_id, sn_get_groups('defense')) ? MRC_FORTIFIER : MRC_ENGINEER;
    }


    sn_db_transaction_start();
    // Блокируем нужные записи
    // Это нужно, что бы заблокировать пользователя и работу с очередями
    $user = db_user_by_id($user['id'], true);
    // Это нужно, что бы заблокировать планету от списания ресурсов
    if(isset($planet['id']) && $planet['id'])
    {
      $planet = db_planet_by_id($planet['id'], true);
    }
    else
    {
      $planet['id'] = 0;
    }

    $planet_id = $que_id == QUE_RESEARCH ? 0 : intval($planet['id']);

    $que = que_get($que_id, $user['id'], $planet['id'], true);
    // TODO Добавить вызовы функций проверок текущей и максимальной длин очередей
    if(count($que['ques'][$que_id][$user['id']][$planet_id]) >= que_get_max_que_length($user, $planet, $que_id, $que_data))
    {
      die('Que full'); // TODO EXCEPTION
    }

    // TODO Отдельно посмотреть на уничтожение зданий - что бы можно было уничтожать их без планов
    switch(eco_can_build_unit($user, $planet, $unit_id))
    {
      case BUILD_ALLOWED:
        break;

      case BUILD_UNIT_BUSY:
        throw new exception('eco_bld_msg_err_laboratory_upgrading', ERR_ERROR); // TODO EXCEPTION
        break;

      case BUILD_REQUIRE_NOT_MEET:
      default:
        throw new exception('eco_bld_msg_err_requirements_not_meet', ERR_ERROR); // TODO EXCEPTION
        break;
    }

    $units_qued = isset($que['in_que'][$que_id][$user['id']][$planet_id][$unit_id]) ? $que['in_que'][$que_id][$user['id']][$planet_id][$unit_id] : 0;
    $unit_level = mrc_get_level($user, $planet, $unit_id, true, true) + $units_qued;
    if(($unit_max = get_unit_param($unit_id, P_MAX_STACK)) && $unit_level >= $unit_max)
    {
      throw new exception('Уже есть максимальное количество юнитов', ERR_ERROR); // TODO EXCEPTION
    }

    // TODO Переделать eco_unit_busy для всех типов зданий
    //  if(eco_unit_busy($user, $planet, $que, $unit_id))
    //  {
    //    die('Unit busy'); // TODO EXCEPTION
    //  }
    if(get_unit_param($unit_id, P_STACKABLE)) // TODO Поле 'max_Lot_size' для ограничения размера стэка в очереди - то ли в юниты, то ли в очередь
    {
      if(in_array($unit_id, $group_missile = sn_get_groups('missile')))
      {
        // TODO Поле 'container' - указывает на родительску структуру, в которой хранится данный юнит и по вместительности которой нужно применять размер юнита
        $used_silo = 0;
        foreach($group_missile as $missile_id)
        {
          $missile_qued = isset($que['in_que'][$que_id][$planet['id']][$missile_id]) ? $que['in_que'][$que_id][$planet['id']][$missile_id] : 0;
          $used_silo += (mrc_get_level($user, $planet, $missile_id, true, true) + $missile_qued) * get_unit_param($missile_id, P_UNIT_SIZE);
        }
        $free_silo = mrc_get_level($user, $planet, STRUC_SILO) * get_unit_param(STRUC_SILO, P_CAPACITY) - $used_silo - get_unit_param($unit_id, P_UNIT_SIZE) * $unit_amount;
        if($free_silo < 0)
        {
          throw new exception('Silo is full', ERR_ERROR); // TODO EXCEPTION
        }
      }
      $unit_amount = min($unit_amount, MAX_FLEET_OR_DEFS_PER_ROW);
      $unit_level = $new_unit_level = 0;
    }
    else
    {
      if($que_id == QUE_STRUCTURES)
      {
        // if($build_mode == BUILD_CREATE && eco_planet_fields_max($planet) - $planet['field_current'] - $que['sectors'][$planet['id']] <= 0)
        $sectors_qued = is_array($que['in_que'][$que_id][$planet['id']]) ? array_sum($que['in_que'][$que_id][$planet['id']]) : 0;
        if($build_mode == BUILD_CREATE && eco_planet_fields_max($planet) - $planet['field_current'] - $sectors_qued <= 0)
        {
          die('Not enough sectors'); // TODO EXCEPTION
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
    if($build_data['RESULT'][BUILD_CREATE] != BUILD_ALLOWED)
    {
      throw new exception('Строительство блокировано - разобраться почему', ERR_ERROR); // TODO EXCEPTION
    }

    if($build_data['CAN'][$build_mode] < $unit_amount)
    {
      throw new exception('Не хватает ресурсов', ERR_ERROR); // TODO EXCEPTION
    }


    if($new_unit_level < 0)
    {
      die('Еще какой-то эксепшен'); // TODO EXCEPTION
    }

    que_add_unit($unit_id, $user, $planet, $build_data, $new_unit_level, $unit_amount, $build_mode);

    sn_db_transaction_commit();

    // sys_redirect($_SERVER['REQUEST_URI']);
    sys_redirect("{$_SERVER['PHP_SELF']}?mode=" . sys_get_param_str('mode'));
    die();
  }
  catch(exception $e)
  {
    sn_db_transaction_rollback();
    $operation_result = array(
      'STATUS'  => in_array($e->getCode(), array(ERR_NONE, ERR_WARNING, ERR_ERROR)) ? $e->getCode() : ERR_ERROR,
      'MESSAGE' => $e->getMessage()
    );
  }

  return $operation_result;
}





function que_recalculate($old_que)
{
  $new_que = array();

  if(is_array($old_que['items']))
  foreach($old_que['items'] as $row)
  {
    if(!isset($row) || !$row || $row['que_unit_amount'] <= 0)
    {
      continue;
    }

    $new_que['items'][] = $row;

    $new_que['in_que'][$row['que_type']][$row['que_player_id']][intval($row['que_planet_id'])][$row['que_unit_id']] += $row['que_unit_amount'] * ($row['que_unit_mode'] == BUILD_CREATE ? 1 : -1);
    $new_que['in_que_abs'][$row['que_type']][$row['que_player_id']][intval($row['que_planet_id'])][$row['que_unit_id']] += $row['que_unit_amount'];

    $last_id = count($new_que['items']) - 1;

    if($row['que_planet_id'])
    {
      $new_que['planets'][$row['que_planet_id']][$row['que_type']][] = &$new_que['items'][$last_id];
    }
    elseif($row['que_type'] == QUE_RESEARCH)
    {
      $new_que['players'][$row['que_player_id']][$row['que_type']][] = &$new_que['items'][$last_id];
    }
    $new_que['ques'][$row['que_type']][$row['que_player_id']][intval($row['que_planet_id'])][] = &$new_que['items'][$last_id];

    // Это мы можем посчитать по длине очереди в players и planets
    //$ques['used_slots'][$row['que_type']][$row['que_player_id']][intval($row['que_planet_id'])][$row['que_unit_id']]++;
  }

  return $new_que;
}

/*
 * С $for_update === true эта функция должна вызываться только из транзакции! Все соответствующие записи в users и planets должны быть уже блокированы!
 *
 * $que_type
 *   !$que_type - все очереди
 *   QUE_XXXXXX - конкретная очередь по планете
 * $user_id - ID пользователя
 * $planet_id
 *   $que_type == QUE_RESEARCH - игнорируется
 *   null - обработка очередей планет не производится
 *   false/0 - обрабатываются очереди всех планет по $user_id
 *   (integer) - обрабатываются локальные очереди для планеты. Нужно, например, в обработчике флотов
 *   иначе - $que_type для указанной планеты
 * $for_update - true == нужно блокировать записи
 *
 * TODO Работа при !$user_id
 * TODO Переформатировать вывод данных, что бы можно было возвращать данные по всем планетам и юзерам в одном запросе: добавить подмассивы 'que', 'planets', 'players'
 *
 */
function que_get($que_type = false, $user_id, $planet_id = null, $for_update = false)
{
  sn_db_transaction_check($for_update);

  $ques = array();

  if(!$user_id)
  {
    die('No user_id for que_get_que()');
  }

  $sql = '';
  $sql .= $user_id ? " AND `que_player_id` = {$user_id}" : '';
  $sql .= $que_type == QUE_RESEARCH || $planet_id === null ? " AND `que_planet_id` IS NULL" :
    ($planet_id ? " AND (`que_planet_id` = {$planet_id}" . ($que_type ? '' : ' OR que_planet_id IS NULL') . ")" : '');
  $sql .= $que_type ? " AND `que_type` = {$que_type}" : '';

  if($sql)
  {
    $que_query = doquery("SELECT * FROM {{que}} WHERE 1 {$sql} ORDER BY que_id" . ($for_update ? ' FOR UPDATE' : ''));
    while($row = mysql_fetch_assoc($que_query))
    {
      $ques['items'][] = $row;
    }
  }

  return que_recalculate($ques);
}

function que_add_unit($unit_id, $user = array(), $planet = array(), $build_data, $unit_level = 0, $unit_amount = 1, $build_mode = BUILD_CREATE)
{
  // TODO Унифицировать проверки

  // TODO que_process() тут

  sn_db_transaction_check(true);

  $build_mode = $build_mode == BUILD_CREATE ? BUILD_CREATE : BUILD_DESTROY;

  // TODO: Some checks
  db_change_units($user, $planet, array(
    RES_METAL     => -$build_data[$build_mode][RES_METAL] * $unit_amount,
    RES_CRYSTAL   => -$build_data[$build_mode][RES_CRYSTAL] * $unit_amount,
    RES_DEUTERIUM => -$build_data[$build_mode][RES_DEUTERIUM] * $unit_amount,
  ));

  $que_type = que_get_unit_que($unit_id);
  $planet_id_origin = $planet['id'] ? $planet['id'] : 'NULL';
  $planet_id = $que_type == QUE_RESEARCH ? 'NULL' : $planet_id_origin;
  if(is_numeric($planet_id))
  {
    db_planet_set_by_id($planet_id, "`que_processed` = UNIX_TIMESTAMP(NOW())");
  }
  elseif(is_numeric($user['id']))
  {
    db_user_set_by_id($user['id'], '`que_processed` = UNIX_TIMESTAMP(NOW())');
  }

  $resource_list = sys_unit_arr2str($build_data[$build_mode]);

  doquery(
    "INSERT INTO
      `{{que}}`
    SET
      `que_player_id` = {$user['id']},
      `que_planet_id` = {$planet_id},
      `que_planet_id_origin` = {$planet_id_origin},
      `que_type` = {$que_type},
      `que_time_left` = {$build_data[RES_TIME][$build_mode]},
      `que_unit_id` = {$unit_id},
      `que_unit_amount` = {$unit_amount},
      `que_unit_mode` = {$build_mode},
      `que_unit_level` = {$unit_level},
      `que_unit_time` = {$build_data[RES_TIME][$build_mode]},
      `que_unit_price` = '{$resource_list}'"
  );
}

function que_delete($que_type, $user = array(), $planet = array(), $clear = false)
{
  $planets_locked = array();

  // TODO: Some checks
  sn_db_transaction_start();
  $user = db_user_by_id($user['id'], true);
  $planet['id'] = $planet['id'] && $que_type !== QUE_RESEARCH ? $planet['id'] : 0;
  $global_que = que_get($que_type, $user['id'], $planet['id'], true);
//pdump($global_que);
//pdump($planet['id']);
//pdump($global_que[$que_type][$planet['id']]);

  if(!empty($global_que['ques'][$que_type][$user['id']][$planet['id']]))
  {
    $que = array_reverse($global_que['ques'][$que_type][$user['id']][$planet['id']]);

    foreach($que as $que_item)
    {
      doquery("DELETE FROM {{que}} WHERE que_id = {$que_item['que_id']} LIMIT 1");

      if($que_item['que_planet_id_origin'])
      {
        $planet['id'] = $que_item['que_planet_id_origin'];
      }

      if(!isset($planets_locked[$planet['id']]))
      {
        $planets_locked[$planet['id']] = $planet['id'] ? db_planet_by_id($planet['id'], true) : $planet;
      }

      $build_data = sys_unit_str2arr($que_item['que_unit_price']);

      db_change_units($user, $planets_locked[$planet['id']], array(
        RES_METAL     => $build_data[RES_METAL] * $que_item['que_unit_amount'],
        RES_CRYSTAL   => $build_data[RES_CRYSTAL] * $que_item['que_unit_amount'],
        RES_DEUTERIUM => $build_data[RES_DEUTERIUM] * $que_item['que_unit_amount'],
      ));

      if(!$clear)
      {
        break;
      }
    }

    if(is_numeric($planet['id']))
    {
      db_planet_set_by_id($planet['id'], "`que_processed` = UNIX_TIMESTAMP(NOW())");
    }
    elseif(is_numeric($user['id']))
    {
      db_user_set_by_id($user['id'], '`que_processed` = UNIX_TIMESTAMP(NOW())');
    }

    sn_db_transaction_commit();
  }
  else
  {
    sn_db_transaction_rollback();
  }
//die();
  header("Location: {$_SERVER['PHP_SELF']}?mode={$que_type}");
}


function que_tpl_parse_element($que_element)
{
  global $lang;

  return
    array(
      'ID' => $que_element['que_unit_id'],
      'QUE' => $que_element['que_type'],
      'NAME' => $lang['tech'][$que_element['que_unit_id']],
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
function que_tpl_parse(&$template, $que_type, $user, $planet = array(), $que = null)
{
  // TODO: Переделать для $que_type === false
  global $lang, $config;

  $planet['id'] = $planet['id'] ? $planet['id'] : 0;
  // que_get_que($global_que, $que_type, $user['id'], $planet['id']);
  // que_get($que_type, )

  if(!is_array($que))
  {
    $que = que_get($que_type, $user['id'], $planet['id']);
  }

  if(is_array($que) && isset($que['items']))
  {
    $que = $que['ques'][$que_type][$user['id']][$planet['id']];
  }



  // pdump($que);die();

  if($que)
  {
    foreach($que as $que_element)
    {
      $template->assign_block_vars('que', que_tpl_parse_element($que_element));
    }
  }

  if($que_type == QUE_RESEARCH)
  {
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
function que_process(&$user, $planet = null, $on_time = SN_TIME_NOW)
{
  sn_db_transaction_check(true);

  $que = array();

  // Блокируем пользователя. Собственно, запись о нём нам не нужна - будем использовать старую
  $user = db_user_by_id($user['id'], true);

  $time_left[$user['id']][0] = max(0, $on_time - $user['que_processed']);
  if($planet === null && !$time_left[$user['id']][0]) // TODO
  {
    return $que;
  }

  // Определяем, какие очереди нам нужны и получаем их
  $que_type_id = $planet === null ? QUE_RESEARCH : false;
  $planet = isset($planet['id']) ? $planet['id'] : $planet; // В $planet у нас теперь только её ID или шаблон null/0/false
  $que = que_get($que_type_id, $user['id'], $planet, true);
//pdump($que);
  if(empty($que['items']))
  {
    return $que;
  }

  $planet_list = array();
  if($planet !== null)
  {
    // Если нужно изменять данные на планетах - блокируем планеты и получаем данные о них
    // TODO - от них не надо ничего, кроме ID и que_processed
    $planet_query = db_planet_list_by_user_or_planet($user['id'], $planet);
    while($planet_row = mysql_fetch_assoc($planet_query))
    {
      $planet_list[$planet_row['id']] = $planet_row;
      $time_left[$planet_row['id_owner']][$planet_row['id']] = max(0, $on_time - $planet_row['que_processed']);
    }
  }

  // pdump($time_left);

  // Теперь в $time_left лежит время обсчета всех очередей по каждой из планеты
  if(array_sum($time_left[$user['id']]) == 0)
  {
    return $que;
  }
  // pdump($que);


  $db_changeset = array();
  $unit_changes = array();
  foreach($que['items'] as &$que_item)
  {
    $que_player_id = &$que_item['que_player_id'];
    $que_planet_id = intval($que_item['que_planet_id']);
    // $que_type = &$que_item['que_type'];

    $que_time_left = &$que['time_left'][$que_player_id][$que_planet_id][$que_item['que_type']];
    if(!isset($que_time_left))
    {
      $que_time_left = $time_left[$que_player_id][$que_planet_id];
    }
    if($que_time_left <= 0 || $que_item['que_unit_amount'] <= 0)
    {
      continue;
    }
    // Дальше мы идем, если только осталось время в очереди И юниты к постройке

    // Вычисляем, сколько целых юнитов будет построено - от 0 до количества юнитов в очереди
    $unit_processed = min($que_item['que_unit_amount'] - 1, floor($que_time_left / $que_item['que_unit_time']));
    // Вычитаем это время из остатков
    $que_time_left -= $unit_processed * $que_item['que_unit_time'];

    // Теперь работаем с остатком времени на юните. Оно не может быть равно или меньше нуля

    // Если времени в очереди осталось не меньше, чем время текущего юнита - значит мы достроили юнит
    if($que_time_left >= $que_item['que_time_left'])
    {
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
    if($que_item['que_unit_amount'] > 0)
    {
      $que_item['que_time_left'] = $que_item['que_time_left'] - $que_time_left;
      $que_time_left = 0;
    }

    if($que_item['que_unit_amount'] <= 0)
    {
      $db_changeset['que'][$que_item['que_id']] = array(
        'action' => SQL_OP_DELETE,
        'where' => array(
          "`que_id` = {$que_item['que_id']}",
        ),
      );
    }
    else
    {
      $db_changeset['que'][$que_item['que_id']] = array(
        'action' => SQL_OP_UPDATE,
        'where' => array(
          "`que_id` = {$que_item['que_id']}",
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

    if($unit_processed)
    {
      $unit_processed_delta = $unit_processed * ($que_item['que_unit_mode'] == BUILD_CREATE ? 1 : -1);
      $unit_changes[$que_player_id][$que_planet_id][$que_item['que_unit_id']] += $unit_processed_delta;
    }
  }

  foreach($time_left as $player_id => $planet_data)
  {
    foreach($planet_data as $planet_id => $time_on_planet)
    {
      $table = $planet_id ? 'planets' : 'users';
      $id = $planet_id ? $planet_id : $player_id;
      $db_changeset[$table][$id] = array(
        'action' => SQL_OP_UPDATE,
        'where' => array(
          "`id` = {$id}",
        ),
        'fields' => array(
          'que_processed' => array(
            'set' => $on_time,
          ),
        ),
      );

      if(is_array($unit_changes[$player_id][$planet_id]))
      {
        foreach($unit_changes[$player_id][$planet_id] as $unit_id => $unit_amount)
        {
          $db_changeset['unit'][] = sn_db_unit_changeset_prepare($unit_id, $unit_amount, $user, $planet_id ? $planet_id : null);
        }
      }



      // Сюда впердолить подготовку чейнджсета для обновления пользователя




    }
  }

//pdump($db_changeset, '$db_changeset');

  $que = que_recalculate($que);
//pdump($que, '$que');

  /*
  // TODO: Re-enable quests for Alliances
  if(!empty($unit_changes) && !$user['user_as_ally'] && $user['id_planet'])
  {
    $planet = db_planet_by_id($user['id_planet'], true);
    $quest_list = qst_get_quests($user['id']);
    $quest_triggers = qst_active_triggers($quest_list);
  }
  else
  {
    $planet = array();
  }

  $quest_rewards = array();
  $xp_incoming = 0;
  foreach($unit_changes as $owner_id => $changes)
  {
    // $user_id_sql = $owner_id ? $owner_id : $user['id'];
    $planet_id_sql = $owner_id ? $owner_id : null;
    foreach($changes as $unit_id => $unit_value)
    {

      $db_changeset['unit'][] = sn_db_unit_changeset_prepare($unit_id, $unit_value, $user, $planet_id_sql);

      // TODO: Изменить согласно типу очереди
      $unit_level_new = mrc_get_level($user, array(), $unit_id, false, true) + $unit_value;
      $build_data = eco_get_build_data($user, array(), $unit_id, $unit_level_new - 1);
      $build_data = $build_data[BUILD_CREATE];
      foreach(sn_get_groups('resources_loot') as $resource_id)
      {
        $xp_incoming += $build_data[$resource_id];
      }

      if($planet['id'])
      {
        // TODO: Check mutiply condition quests
        $quest_trigger_list = array_keys($quest_triggers, $unit_id);
        foreach($quest_trigger_list as $quest_id)
        {
          if($quest_list[$quest_id]['quest_status_status'] != QUEST_STATUS_COMPLETE && $quest_list[$quest_id]['quest_unit_amount'] <= $unit_level_new)
          {
            $quest_rewards[$quest_id] = $quest_list[$quest_id]['quest_rewards'];
            $quest_list[$quest_id]['quest_status_status'] = QUEST_STATUS_COMPLETE;
          }
        }
      }
    }
  }
  // TODO: Изменить начисление награды за квесты на ту планету, на которой происходил ресеч
  // qst_reward($user, $planet, $quest_rewards, $quest_list);
  */

  // TODO: Изменить согласно типу очереди
  // rpg_level_up($user, RPG_TECH, $xp_incoming / 1000);

  sn_db_changeset_apply($db_changeset);

  // Сообщения о постройке
  // $user = db_user_by_id($user['id'], true);
  // TODO Так же пересчитывать планеты



  return $que;
















  // $local_que['time_left'][QUE_RESEARCH][0] = $time_left[QUE_RESEARCH][0];

//pdump($user_time_left, '$user_time_left');

  print('1');
  //foreach($local_que as $que_id => &$que_data)
  //{
  //  if(!intval($que_id))continue;
  foreach(sn_get_groups('que') as $que_id => $que_info)
  {
    if(!isset($que['ques'][$que_id]))continue;

    foreach($que_data as $owner_id => &$que_items)
    {
      foreach($que_items as &$que_item)
      {
        // Вычисляем, сколько целых юнитов будет построено - от 0 до количества юнитов в очереди
        $unit_processed = min($que_item['que_unit_amount'] - 1, floor($local_que['time_left'][$que_id][$owner_id] / $que_item['que_unit_time']));
        // Вычитаем это время из остатков
        $local_que['time_left'][$que_id][$owner_id] -= $unit_processed * $que_item['que_unit_time'];

        // Теперь работаем с остатком времени на юните. Оно не может быть равно или меньше нуля

        // Вычитаем остаток времени работы очереди с времени постройки юнита
        if($que_item['que_time_left'] <= $local_que['time_left'][$que_id][$owner_id])
        {
          // Если время постройки - неположительное, значит мы достроили юнит
          // Увеличиваем количество отстроенных юнитов
          $unit_processed++;
          // Вычитаем из времени очереди потраченное на постройку время
          $local_que['time_left'][$que_id][$owner_id] -= $que_item['que_time_left'];
          $que_item['que_time_left'] = $que_item['que_unit_time'];
          // Тут у нас может остатся время очереди - если постройка была не последняя
        }

        // Изменяем количество оставшихся юнитов
        $que_item['que_unit_amount'] -= $unit_processed;

        if($que_item['que_unit_amount'])
        {
          $que_item['que_time_left'] = $que_item['que_time_left'] - $local_que['time_left'][$que_id][$owner_id];
          $local_que['time_left'][$que_id][$owner_id] = 0;
        }

        if(!$que_item['que_unit_amount'])
        {
          $db_changeset['que'][$que_item['que_id']] = array(
            'action' => SQL_OP_DELETE,
            'where' => array(
              "`que_id` = {$que_item['que_id']}",
            ),
          );
        }
        else
        {
          $db_changeset['que'][$que_item['que_id']] = array(
            'action' => SQL_OP_UPDATE,
            'where' => array(
              "`que_id` = {$que_item['que_id']}",
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

        if($unit_processed)
        {
          $unit_processed_delta = $unit_processed * ($que_item['que_unit_mode'] == BUILD_CREATE ? 1 : -1);
          $unit_changes[$owner_id][$que_item['que_unit_id']] += $unit_processed_delta;
        }
/*
pdump($unit_processed, '$unit_processed');
$qi2 = $que_item;
unset($qi2['que_id']);
unset($qi2['que_player_id']);
unset($qi2['que_planet_id']);
unset($qi2['que_planet_id_origin']);
unset($qi2['que_type']);
unset($qi2['que_unit_mode']);
unset($qi2['que_unit_price']);
unset($qi2['que_unit_level']);
unset($qi2['que_unit_id']);
pdump($qi2, '$que_item');
pdump($local_que['time_left'][$que_id][$owner_id], '$local_que[time_left][$que_id][$owner_id]');
print('<hr />');
*/
        // Если на очереди времени не осталось - выходим
        if(!$local_que['time_left'][$que_id][$owner_id])
        {
          break;
        }
      }
    }
  }

  die();


  // TODO: Re-enable quests for Alliances
  if(!empty($unit_changes) && !$user['user_as_ally'] && $user['id_planet'])
  {
    $planet = db_planet_by_id($user['id_planet'], true);
    $quest_list = qst_get_quests($user['id']);
    $quest_triggers = qst_active_triggers($quest_list);
  }
  else
  {
    $planet = array();
  }

  $quest_rewards = array();
  $xp_incoming = 0;
  foreach($unit_changes as $owner_id => $changes)
  {
    // $user_id_sql = $owner_id ? $owner_id : $user['id'];
    $planet_id_sql = $owner_id ? $owner_id : null;
    foreach($changes as $unit_id => $unit_value)
    {

      $db_changeset['unit'][] = sn_db_unit_changeset_prepare($unit_id, $unit_value, $user, $planet_id_sql);

      // TODO: Изменить согласно типу очереди
      $unit_level_new = mrc_get_level($user, array(), $unit_id, false, true) + $unit_value;
      $build_data = eco_get_build_data($user, array(), $unit_id, $unit_level_new - 1);
      $build_data = $build_data[BUILD_CREATE];
      foreach(sn_get_groups('resources_loot') as $resource_id)
      {
        $xp_incoming += $build_data[$resource_id];
      }

      if($planet['id'])
      {
        // TODO: Check mutiply condition quests
        $quest_trigger_list = array_keys($quest_triggers, $unit_id);
        foreach($quest_trigger_list as $quest_id)
        {
          if($quest_list[$quest_id]['quest_status_status'] != QUEST_STATUS_COMPLETE && $quest_list[$quest_id]['quest_unit_amount'] <= $unit_level_new)
          {
            $quest_rewards[$quest_id] = $quest_list[$quest_id]['quest_rewards'];
            $quest_list[$quest_id]['quest_status_status'] = QUEST_STATUS_COMPLETE;
          }
        }
      }

    }
  }

  // TODO: Изменить согласно типу очереди
  rpg_level_up($user, RPG_TECH, $xp_incoming / 1000);
  // TODO: Изменить начисление награды за квесты на ту планету, на которой происходил ресеч
  qst_reward($user, $planet, $quest_rewards, $quest_list);

  sn_db_changeset_apply($db_changeset);

  // Сообщения о постройке
  $user = db_user_by_id($user['id'], true);
  // TODO Так же пересчитывать планеты

  // sn_db_transaction_commit();

  // TODO поменять que_processed у планеты и юзера


  return $local_que;
}
