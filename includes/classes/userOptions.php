<?php

/**
 * Class userOptions
 *
 * Загрузка и сохранение настроек пользователя в БД
 *
 * Унаследованная поддержка многомерных индексов
 * Многоуровневое кэширование: память PHP -> память системы (xCache) -> БД
 * isset() работает на кэше в памяти PHP и не проверяет дальше
 * Поддержка удаления записей из БД и кэша через unset()
 * Поддержка отложенной записи
 *
 */
class userOptions extends oldArrayAccessNd {
  protected $user_id = 0;
  protected $loaded = false;
  protected $defaults = array(
    PLAYER_OPTION_MENU_SORT => '',
    PLAYER_OPTION_MENU_HIDE_SHOW_BUTTON => PLAYER_OPTION_MENU_HIDE_SHOW_BUTTON_FIXED,
    PLAYER_OPTION_MENU_SHOW_ON_BUTTON => 0,
    PLAYER_OPTION_MENU_HIDE_ON_BUTTON => 0,
    PLAYER_OPTION_MENU_HIDE_ON_LEAVE => 0,
    PLAYER_OPTION_MENU_UNPIN_ABSOLUTE => 0,
    PLAYER_OPTION_MENU_ITEMS_AS_BUTTONS => 0,
    PLAYER_OPTION_MENU_WHITE_TEXT => 0,
    PLAYER_OPTION_MENU_OLD => 0,

    PLAYER_OPTION_SOUND_ENABLED => 0,

    PLAYER_OPTION_FLEET_SHIP_SORT => PLAYER_OPTION_SORT_DEFAULT,
    PLAYER_OPTION_FLEET_SHIP_SORT_INVERSE => PLAYER_OPTION_SORT_ORDER_PLAIN,

    PLAYER_OPTION_CURRENCY_DEFAULT => 'RUB',

    PLAYER_OPTION_FLEET_SPY_DEFAULT => 1,
    // PLAYER_OPTION_FLEET_MESS_AMOUNT_MAX => 99,

    PLAYER_OPTION_UNIVERSE_ICON_SPYING => 1,
    PLAYER_OPTION_UNIVERSE_ICON_MISSILE => 1,
    PLAYER_OPTION_UNIVERSE_ICON_PM => 1,
    PLAYER_OPTION_UNIVERSE_ICON_STATS => 1,
    PLAYER_OPTION_UNIVERSE_ICON_PROFILE => 1,
    PLAYER_OPTION_UNIVERSE_ICON_BUDDY => 1,
    PLAYER_OPTION_UNIVERSE_OLD => 0,
    PLAYER_OPTION_UNIVERSE_DISABLE_COLONIZE => 0,

    PLAYER_OPTION_PLANET_SORT => 0,
    PLAYER_OPTION_PLANET_SORT_INVERSE => 0,
    PLAYER_OPTION_TOOLTIP_DELAY => 500,

    PLAYER_OPTION_BASE_FONT_SIZE => FONT_SIZE_PERCENT_DEFAULT_STRING,

    PLAYER_OPTION_BUILD_AUTOCONVERT_HIDE => 0,

    PLAYER_OPTION_NAVBAR_PLANET_VERTICAL => 0,
    PLAYER_OPTION_NAVBAR_RESEARCH_WIDE => 0,
    PLAYER_OPTION_NAVBAR_DISABLE_EXPEDITIONS => 0,
    PLAYER_OPTION_NAVBAR_DISABLE_FLYING_FLEETS => 0,
    PLAYER_OPTION_NAVBAR_DISABLE_RESEARCH => 0,
    PLAYER_OPTION_NAVBAR_DISABLE_PLANET => 0,
    PLAYER_OPTION_NAVBAR_DISABLE_HANGAR => 0,
    PLAYER_OPTION_NAVBAR_DISABLE_QUESTS => 0,
    PLAYER_OPTION_NAVBAR_DISABLE_META_MATTER => 0,

    PLAYER_OPTION_BUILDING_SORT => array(
      QUE_RESEARCH => PLAYER_OPTION_SORT_DEFAULT,
      QUE_STRUCTURES => PLAYER_OPTION_SORT_DEFAULT,
      SUBQUE_FLEET => PLAYER_OPTION_SORT_DEFAULT,
      SUBQUE_DEFENSE => PLAYER_OPTION_SORT_DEFAULT,
    ),
    PLAYER_OPTION_BUILDING_SORT_INVERSE => array(
      QUE_RESEARCH => PLAYER_OPTION_SORT_ORDER_PLAIN,
      QUE_STRUCTURES => PLAYER_OPTION_SORT_ORDER_PLAIN,
      SUBQUE_FLEET => PLAYER_OPTION_SORT_ORDER_PLAIN,
      SUBQUE_DEFENSE => PLAYER_OPTION_SORT_ORDER_PLAIN,
    ),

    PLAYER_OPTION_ANIMATION_DISABLED => 0,
    PLAYER_OPTION_DESIGN_DISABLE_BORDERS => 0,
    PLAYER_OPTION_TECH_TREE_TABLE => 0,

    PLAYER_OPTION_PROGRESS_BARS_DISABLED => 0,

    PLAYER_OPTION_FLEET_SHIP_SELECT_OLD => 0,
    PLAYER_OPTION_FLEET_SHIP_HIDE_SPEED => 0,
    PLAYER_OPTION_FLEET_SHIP_HIDE_CAPACITY => 0,
    PLAYER_OPTION_FLEET_SHIP_HIDE_CONSUMPTION => 0,
  );

  public $data = array(); // Container // TODO - make protected

  public $to_write = array(); // TODO - make protected
  public $to_delete = array(); // TODO - make protected

  public function __get($offset) {
    // TODO: Implement __get() method.
    return $this->__isset($offset) ? $this->data[$offset] : null;
  }

  public function __set($offset, $value = null) {
//    if(!$this->__isset($offset) || $this->__get($offset) != $value)
    {
      $this->data[$offset] = $value; // Сразу записываем данные во внутренний кэш
      $this->to_write[$offset] = 1; // Индекс измененного элемента для работы подсистемы отложенной записи
    }
  }

  public function __isset($offset) {
    // TODO: Implement __isset() method.
    return isset($this->data[$offset]);
  }

  public function __unset($offset) {
    // TODO: Implement __unset() method.
    unset($this->data[$offset]);
    $this->to_delete[$offset] = 1;
  }

  public function __flush() {
    // TODO Implement

    $update_cache = false;

    if(!empty($this->to_write)) {
      foreach($this->to_write as $key => $cork) {
        $value = is_array($this->data[$key]) ? serialize($this->data[$key]) : $this->data[$key]; // Сериализация для массивов при сохранении в БД
        $this->to_write[$key] = "({$this->user_id}, '" . db_escape($key) . "', '" . db_escape($value) . "')";
      }

      classSupernova::$db->doReplace("REPLACE INTO `{{player_options}}` (`player_id`, `option_id`, `value`) VALUES " . implode(',', $this->to_write));

      $this->to_write = array();
      $update_cache = true;
    }

    if(!empty($this->to_delete)) {
      foreach($this->to_delete as $key => &$value) {
        $value = is_string($key) ? "'". db_escape($key) . "'" : $key;
      }

      classSupernova::$db->doDeleteDeprecated(TABLE_PLAYER_OPTIONS, array(
        'player_id' => $this->user_id,
        "`option_id` IN (". implode(',', $this->to_delete) . ")",
      ));

      $this->to_delete = array();
      $update_cache = true;
    }

    if($update_cache) {
      $field_name = $this->cached_name();
      classSupernova::$cache->$field_name = $this->data;
    }

    return true;
  }


  public function __construct($user_id) {
    $this->user_change($user_id);
  }

  public function user_change($user_id) {
    $this->loaded = false;
    $this->user_id = round(floatval($user_id));
    $this->load();
  }
  protected function cached_name() {
    return 'options_' . $this->user_id;
  }

  protected function load() {
    if($this->loaded) {
      return;
    }

    $this->data = $this->defaults;
    $this->to_write = array();
    $this->to_delete = array();

    if(!$this->user_id) {
      $this->loaded = true;
      return;
    }

    $field_name = $this->cached_name();
    $a_data = classSupernova::$cache->$field_name;

    if(!empty($a_data)) {
      $this->data = array_replace_recursive($this->data, $a_data);
      return;
    }

    $query = classSupernova::$db->doSelect("SELECT * FROM `{{player_options}}` WHERE `player_id` = {$this->user_id} FOR UPDATE");
    while($row = db_fetch($query)) {
      // $this->data[$row['option_id']] = $row['value'];
      $this->data[$row['option_id']] = is_string($row['value']) && ($temp = unserialize($row['value'])) !== false ? $temp : $row['value']; // Десериализация
    }
    classSupernova::$cache->$field_name = $this->data;
  }
}




/**
 * Class userOptionsOld
 *
 * DEPRECATED
 *
 */
class userOptionsOld implements ArrayAccess {
  protected $user_id = 0;
  protected $loaded = false;
  protected $defaults = array(
    PLAYER_OPTION_MENU_SORT => '',
    PLAYER_OPTION_MENU_HIDE_SHOW_BUTTON => PLAYER_OPTION_MENU_HIDE_SHOW_BUTTON_FIXED,
    PLAYER_OPTION_MENU_SHOW_ON_BUTTON => 0,
    PLAYER_OPTION_MENU_HIDE_ON_BUTTON => 0,
    PLAYER_OPTION_MENU_HIDE_ON_LEAVE => 0,
    PLAYER_OPTION_MENU_UNPIN_ABSOLUTE => 0,
    PLAYER_OPTION_MENU_ITEMS_AS_BUTTONS => 0,
    PLAYER_OPTION_MENU_WHITE_TEXT => 0,
    PLAYER_OPTION_MENU_OLD => 0,

    PLAYER_OPTION_SOUND_ENABLED => 0,

    PLAYER_OPTION_FLEET_SHIP_SORT => PLAYER_OPTION_SORT_DEFAULT,
    PLAYER_OPTION_FLEET_SHIP_SORT_INVERSE => 0,

    PLAYER_OPTION_CURRENCY_DEFAULT => 'RUB',

    PLAYER_OPTION_FLEET_SPY_DEFAULT => 1,
    // PLAYER_OPTION_FLEET_MESS_AMOUNT_MAX => 99,

    PLAYER_OPTION_UNIVERSE_ICON_SPYING => 1,
    PLAYER_OPTION_UNIVERSE_ICON_MISSILE => 1,
    PLAYER_OPTION_UNIVERSE_ICON_PM => 1,
    PLAYER_OPTION_UNIVERSE_ICON_STATS => 1,
    PLAYER_OPTION_UNIVERSE_ICON_PROFILE => 1,
    PLAYER_OPTION_UNIVERSE_ICON_BUDDY => 1,
    PLAYER_OPTION_UNIVERSE_OLD => 0,
    PLAYER_OPTION_UNIVERSE_DISABLE_COLONIZE => 0,
    PLAYER_OPTION_DESIGN_DISABLE_BORDERS => 0,
    PLAYER_OPTION_TECH_TREE_TABLE => 1,

    PLAYER_OPTION_PLANET_SORT => 0,
    PLAYER_OPTION_PLANET_SORT_INVERSE => 0,
    PLAYER_OPTION_TOOLTIP_DELAY => 500,

    PLAYER_OPTION_BASE_FONT_SIZE => FONT_SIZE_PERCENT_DEFAULT_STRING,

    PLAYER_OPTION_BUILD_AUTOCONVERT_HIDE => 0,

    PLAYER_OPTION_FLEET_SHIP_SELECT_OLD => 0,
    PLAYER_OPTION_FLEET_SHIP_HIDE_SPEED => 0,
    PLAYER_OPTION_FLEET_SHIP_HIDE_CAPACITY => 0,
    PLAYER_OPTION_FLEET_SHIP_HIDE_CONSUMPTION => 0,
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
    // Если в массиве индекса только один элемент - значит это просто индекс
    is_array($option_id) && count($option_id) == 1 ? $option_id = reset($option_id) : false;

    if(!isset($this->data[is_array($option_id) ? reset($option_id) : $option_id])) {
      $this->load();
    }

    if(is_array($option_id)) {
      $result = $this->data;
      foreach($option_id as $sub_key) {
        if(!isset($result) || !isset($result[$sub_key])) {
          $result = null;
          break;
        }
        $result = $result[$sub_key];
      }
    } else {
      $result = isset($this->data[$option_id]) ? $this->data[$option_id] : null;
    }

    return $result;
  }

  /**
   * @param array|mixed $option
   * @param null|mixed $value
   */
  public function __set($option, $value = null) {
    if(empty($option) || !$this->user_id) {
      return;
    }

    // Если в массиве индекса только один элемент - значит это просто индекс
    if(is_array($option) && count($option) == 1) {
      // Разворачиваем его в индекс
      $option = array(reset($option) => $value);
      unset($value);
      // Дальше будет использоваться стандартный код для пары $option, $value
    }

    $to_write = array();
    // Адресация многомерного массива через массив индексов в $option
    if(is_array($option) && isset($value)) {
      $a_data = &$this->data;
      foreach($option as $option_id) {
        !is_array($a_data[$option_id]) ? $a_data[$option_id] = array() : false;
        $a_data = &$a_data[$option_id];
      }
      if($a_data != $value) {
        $a_data = $value;
        $to_write[reset($option)] = null;
      }
    } else {
      // Пакетная запись из массива ключ -> значение
      !is_array($option) ? $option = array($option => $value) : false;

      foreach($option as $option_id => $option_value) {
        if($this->data[$option_id] !== $option_value) {
          // TODO - вынести отдельно в обработчик
          if($option_id == PLAYER_OPTION_MENU_HIDE_SHOW_BUTTON &&  $option_value == PLAYER_OPTION_MENU_HIDE_SHOW_BUTTON_HIDDEN) {
            sn_setcookie(SN_COOKIE . '_menu_hidden', '0', time() - PERIOD_WEEK, SN_ROOT_RELATIVE);
          }

          $this->data[$option_id] = $option_value;
          $to_write[$option_id] = null;
        }
      }
    }

    if(!empty($to_write)) {
      $field_name = $this->cached_name();
      classSupernova::$cache->$field_name = $this->data;

      foreach($to_write as $option_id => &$option_value) {
        $option_value = is_array($this->data[$option_id]) ? serialize($this->data[$option_id]) : $this->data[$option_id]; // Сериализация для массивов при сохранении в БД
        $to_write[$option_id] = "({$this->user_id}, '" . db_escape($option_id) . "', '" . db_escape($option_value) . "')";
      }

      classSupernova::$db->doReplace("REPLACE INTO `{{player_options}}` (`player_id`, `option_id`, `value`) VALUES " . implode(',', $to_write));
    }
  }

  protected function load() {
    if($this->loaded) {
      return;
    }

    $this->data = $this->defaults;

    if(!$this->user_id) {
      return;
    }

    $field_name = $this->cached_name();
    $a_data = classSupernova::$cache->$field_name;

    if(!empty($a_data)) {
      $this->data = array_replace($this->data, $a_data);
      return;
    }

    $query = classSupernova::$db->doSelect("SELECT * FROM `{{player_options}}` WHERE `player_id` = {$this->user_id} FOR UPDATE");
    while($row = db_fetch($query)) {
      // $this->data[$row['option_id']] = $row['value'];
      $this->data[$row['option_id']] = is_string($row['value']) && ($temp = unserialize($row['value'])) !== false ? $temp : $row['value']; // Десериализация
    }
    classSupernova::$cache->$field_name = $this->data;
    $this->loaded = true;
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
