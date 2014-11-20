<?php

/*
 * Читает настройки пользователя из таблицы
 *
 * На входе:
 *    null - все настройки
 *    array() - список настроек
 *    строка - конкретная настройка
 *
 * На выходе:
 *    null - если ничего не найдено
 *    значение опции - если на входе была строка
 *    массив значений вида <id> => <value> если на входе был массив
 *
 */
function player_load_option(&$user, $option_id = null) {
  $options = null;

  if(!empty($option_id)) {
    if(is_array($option_id)) {
      foreach($option_id as $key => $option) {
        if(isset($user['player_options'][$option])) {
          $options[$option] = $user['player_options'][$option];
          unset($option_id[$key]);
        }
      }
    } else {
      if(isset($user['player_options'][$option_id])) {
        $options = $user['player_options'][$option_id];
        $option_id = 0;
      }
    }
  }

  if(isset($user['id']) && is_numeric($user['id']) && (!isset($option_id) || !empty($option_id))) {
    !is_array($option_id) or array_walk($option_id, function(&$value){$value = "'{$value}'";});

    $query = doquery($q = "SELECT * FROM {{player_options}} WHERE `player_id` = {$user['id']}" .
        ($option_id ? " AND option_id " . (is_array($option_id) ? 'IN (' . implode(',',$option_id ) . ')' : "= '{$option_id}'") : '')
    );

    while($row = mysql_fetch_array($query)) {
      $user['player_options'][$row['option_id']] = $row['value'];
      $options[$row['option_id']] = $row['value'];
    }

    (is_array($option_id) || !$option_id) or ($options = isset($options[$option_id]) ? $options[$option_id] : null);
  }

  return empty($options) ? null : $options;
}

// TODO !!!!!!!!!!!!!!!!!!!!
function player_save_option_array(&$user, $options_array) {
  if(isset($user['id']) && is_numeric($user['id']) && !empty($options_array)) {
    foreach($options_array as $option_id => &$option_value) {
      $user[$option_id] = $option_value;

      $option_id = mysql_real_escape_string($option_id);
      $option_value = mysql_real_escape_string($option_value);

      $option_value = "({$user['id']},'{$option_id}','{$option_value}')";
    }

    doquery("REPLACE INTO {{player_options}} (`player_id`, `option_id`, `value`) VALUES " . implode(',', $options_array));
  }
}

function player_save_option(&$user, $option_id, $option_value) {
  player_save_option_array(&$user, array($option_id => $option_value));
}


