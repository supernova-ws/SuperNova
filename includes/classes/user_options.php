<?php

class userOptions implements ArrayAccess {
  protected $user_id = 0;
  protected $defaults = array(
    PLAYER_OPTION_MENU_SORT => '',
    PLAYER_OPTION_MENU_HIDE_SHOW_BUTTON => PLAYER_OPTION_MENU_HIDE_SHOW_BUTTON_FIXED,
    PLAYER_OPTION_MENU_SHOW_ON_BUTTON => 0,
    PLAYER_OPTION_MENU_HIDE_ON_BUTTON => 0,
    PLAYER_OPTION_MENU_HIDE_ON_LEAVE => 0,
    PLAYER_OPTION_MENU_UNPIN_ABSOLUTE => 0,
    PLAYER_OPTION_MENU_ITEMS_AS_BUTTONS => 0,

    PLAYER_OPTION_SOUND_ENABLED => 0,

    PLAYER_OPTION_FLEET_SHIP_SORT => PLAYER_OPTION_FLEET_SHIP_SORT_DEFAULT,
    PLAYER_OPTION_FLEET_SHIP_SORT_INVERSE => 0,

    PLAYER_OPTION_CURRENCY_DEFAULT => 'RUR',

    PLAYER_OPTION_FLEET_SPY_DEFAULT => 1,
    // PLAYER_OPTION_FLEET_MESS_AMOUNT_MAX => 99,

    PLAYER_OPTION_UNIVERSE_ICON_SPYING => 1,
    PLAYER_OPTION_UNIVERSE_ICON_MISSILE => 1,
    PLAYER_OPTION_UNIVERSE_ICON_PM => 1,
    PLAYER_OPTION_UNIVERSE_ICON_STATS => 1,
    PLAYER_OPTION_UNIVERSE_ICON_PROFILE => 1,
    PLAYER_OPTION_UNIVERSE_ICON_BUDDY => 1,

    PLAYER_OPTION_PLANET_SORT => 0,
    PLAYER_OPTION_PLANET_SORT_INVERSE => 0,
    PLAYER_OPTION_TOOLTIP_DELAY => 500,

    PLAYER_OPTION_BASE_FONT_SIZE => 11,

    PLAYER_OPTION_BUILD_AUTOCONVERT_HIDE => 0,
  );
  public $data = array(); // Container // TODO - make protected

  public function __construct($user_id) {
    $this->user_change($user_id);
  }

  public function user_change($user_id) {
    $this->user_id = round(floatval($user_id));
    $this->load();
  }

  protected function cached_name() {
    return 'options_' . $this->user_id;
  }
  // TODO - serialize/unserialize options
  public function __get($option_id) {
    if(!isset($this->data[$option_id])) {
      $this->load();
    }

    return isset($this->data[$option_id]) ? $this->data[$option_id] : null;
  }
  public function __set($option, $value = null) {
    global $sn_cache;

    !is_array($option) ? $option = array($option => $value) : false;

    if(empty($option) || !$this->user_id) {
      return;
    }

    $to_write = array();
    foreach($option as $option_id => $option_value) {
      if($this->data[$option_id] !== $option_value) {
        // TODO - вынести отдельно в обработчик
        if($option_id == PLAYER_OPTION_MENU_HIDE_SHOW_BUTTON &&  $option_value == PLAYER_OPTION_MENU_HIDE_SHOW_BUTTON_HIDDEN) {
          sn_setcookie(SN_COOKIE . '_menu_hidden', '0', time() - PERIOD_WEEK, SN_ROOT_RELATIVE);
        }

        $this->data[$option_id] = $option_value;
        $to_write[] = "({$this->user_id}, '" . db_escape($option_id) . "', '" . db_escape($option_value) . "')";
      }
    }

    if(!empty($to_write)) {
      $field_name = $this->cached_name();
      $sn_cache->$field_name = $this->data;
      doquery("REPLACE INTO {{player_options}} (`player_id`, `option_id`, `value`) VALUES " . implode(',', $to_write));
    }
  }

  protected function load() {
    global $sn_cache;

    $this->data = $this->defaults;

    if(!$this->user_id) {
      return;
    }

    $field_name = $this->cached_name();
    $a_data = $sn_cache->$field_name;
    if(!empty($a_data)) {
      // $this->data = $a_data;
      $this->data = array_replace($this->data, $a_data);;
      return;
    }

    $query = doquery("SELECT * FROM `{{player_options}}` WHERE `player_id` = {$this->user_id} FOR UPDATE");
    while($row = db_fetch($query)) {
      $this->data[$row['option_id']] = $row['value'];
    }
    $sn_cache->$field_name = $this->data;
  }


  public function offsetExists($offset) {
    return isset($this->data[$offset]);
  }
  public function offsetGet($offset) {
    return $this->__get($offset);
  }
  public function offsetSet($offset, $value) {
    if(!is_null($offset)) {
      // $this->data[$offset] = $value;
      $this->__set($offset, $value);
    } else {
      // $this->data[] = $value;
    }
  }
  public function offsetUnset($offset) {
    // TODO
    unset($this->data[$offset]);
  }
}

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
//function player_load_option(&$user, $option_id = null) {
//  $options = null;
//
//  if(!empty($option_id)) {
//    if(is_array($option_id)) {
//      foreach($option_id as $key => $option) {
//        if(isset($user['player_options'][$option])) {
//          $options[$option] = $user['player_options'][$option];
//          unset($option_id[$key]);
//        }
//      }
//    } else {
//      if(isset($user['player_options'][$option_id])) {
//        $options = $user['player_options'][$option_id];
//        $option_id = 0;
//      }
//    }
//  }
//
//  if(isset($user['id']) && is_numeric($user['id']) && (!isset($option_id) || !empty($option_id))) {
//    !is_array($option_id) or array_walk($option_id, function(&$value){$value = "'{$value}'";});
//
//    $query = doquery($q = "SELECT * FROM {{player_options}} WHERE `player_id` = {$user['id']}" .
//        ($option_id ? " AND option_id " . (is_array($option_id) ? 'IN (' . implode(',',$option_id ) . ')' : "= '{$option_id}'") : '')
//    );
//
//    while($row = db_fetch($query)) {
//      $user['player_options'][$row['option_id']] = $row['value'];
//      $options[$row['option_id']] = $row['value'];
//    }
//
//    (is_array($option_id) || !$option_id) or ($options = isset($options[$option_id]) ? $options[$option_id] : null);
//  }
//
//  return empty($options) ? null : $options;
//}
//function player_save_option_array(&$user, $options_array) {
//  if(isset($user['id']) && is_numeric($user['id']) && !empty($options_array)) {
//    foreach($options_array as $option_id => &$option_value) {
//      $user[$option_id] = $option_value;
//
//      $option_id = db_escape($option_id);
//      $option_value = db_escape($option_value);
//
//      $option_value = "({$user['id']},'{$option_id}','{$option_value}')";
//    }
//
//    doquery("REPLACE INTO {{player_options}} (`player_id`, `option_id`, `value`) VALUES " . implode(',', $options_array));
//  }
//}
//function player_save_option(&$user, $option_id, $option_value) {
//  player_save_option_array($user, array($option_id => $option_value));
//}
