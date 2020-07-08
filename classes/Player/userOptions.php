<?php

namespace Player;

use Common\oldArrayAccessNd;

/**
 * Class Player\userOptions
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
    PLAYER_OPTION_MENU_SORT             => '',
    PLAYER_OPTION_MENU_HIDE_SHOW_BUTTON => PLAYER_OPTION_MENU_HIDE_SHOW_BUTTON_FIXED,
    PLAYER_OPTION_MENU_SHOW_ON_BUTTON   => 0,
    PLAYER_OPTION_MENU_HIDE_ON_BUTTON   => 0,
    PLAYER_OPTION_MENU_HIDE_ON_LEAVE    => 0,
    PLAYER_OPTION_MENU_UNPIN_ABSOLUTE   => 0,
    PLAYER_OPTION_MENU_ITEMS_AS_BUTTONS => 0,
    PLAYER_OPTION_MENU_WHITE_TEXT       => 0,
    PLAYER_OPTION_MENU_OLD              => 0,

    PLAYER_OPTION_SOUND_ENABLED => 0,

    PLAYER_OPTION_FLEET_SHIP_SORT         => PLAYER_OPTION_SORT_DEFAULT,
    PLAYER_OPTION_FLEET_SHIP_SORT_INVERSE => PLAYER_OPTION_SORT_ORDER_PLAIN,

    PLAYER_OPTION_CURRENCY_DEFAULT => 'RUR',

    PLAYER_OPTION_FLEET_SPY_DEFAULT => 1,
    // PLAYER_OPTION_FLEET_MESS_AMOUNT_MAX => 99,

    PLAYER_OPTION_UNIVERSE_ICON_SPYING      => 1,
    PLAYER_OPTION_UNIVERSE_ICON_MISSILE     => 1,
    PLAYER_OPTION_UNIVERSE_ICON_PM          => 1,
    PLAYER_OPTION_UNIVERSE_ICON_STATS       => 1,
    PLAYER_OPTION_UNIVERSE_ICON_PROFILE     => 1,
    PLAYER_OPTION_UNIVERSE_ICON_BUDDY       => 1,
    PLAYER_OPTION_UNIVERSE_OLD              => 0,
    PLAYER_OPTION_UNIVERSE_DISABLE_COLONIZE => 0,

    PLAYER_OPTION_PLANET_SORT         => 0,
    PLAYER_OPTION_PLANET_SORT_INVERSE => 0,
    PLAYER_OPTION_TOOLTIP_DELAY       => 500,

    PLAYER_OPTION_BASE_FONT_SIZE => FONT_SIZE_PERCENT_DEFAULT_STRING,

    PLAYER_OPTION_BUILD_AUTOCONVERT_HIDE => 0,

    PLAYER_OPTION_NAVBAR_PLANET_VERTICAL        => 0,
    PLAYER_OPTION_NAVBAR_RESEARCH_WIDE          => 0,
    PLAYER_OPTION_NAVBAR_DISABLE_EXPEDITIONS    => 0,
    PLAYER_OPTION_NAVBAR_DISABLE_FLYING_FLEETS  => 0,
    PLAYER_OPTION_NAVBAR_DISABLE_RESEARCH       => 0,
    PLAYER_OPTION_NAVBAR_DISABLE_PLANET         => 0,
    PLAYER_OPTION_NAVBAR_DISABLE_HANGAR         => 0,
    PLAYER_OPTION_NAVBAR_DISABLE_DEFENSE        => 0,
    PLAYER_OPTION_NAVBAR_DISABLE_QUESTS         => 0,
    PLAYER_OPTION_NAVBAR_DISABLE_META_MATTER    => 0,
    PLAYER_OPTION_NAVBAR_PLANET_OLD             => 0,
    PLAYER_OPTION_NAVBAR_PLANET_DISABLE_STORAGE => 0,

    PLAYER_OPTION_BUILDING_SORT         => array(
      QUE_RESEARCH   => PLAYER_OPTION_SORT_DEFAULT,
      QUE_STRUCTURES => PLAYER_OPTION_SORT_DEFAULT,
      SUBQUE_FLEET   => PLAYER_OPTION_SORT_DEFAULT,
      SUBQUE_DEFENSE => PLAYER_OPTION_SORT_DEFAULT,
    ),
    PLAYER_OPTION_BUILDING_SORT_INVERSE => array(
      QUE_RESEARCH   => PLAYER_OPTION_SORT_ORDER_PLAIN,
      QUE_STRUCTURES => PLAYER_OPTION_SORT_ORDER_PLAIN,
      SUBQUE_FLEET   => PLAYER_OPTION_SORT_ORDER_PLAIN,
      SUBQUE_DEFENSE => PLAYER_OPTION_SORT_ORDER_PLAIN,
    ),

    PLAYER_OPTION_ANIMATION_DISABLED     => 0,
    PLAYER_OPTION_DESIGN_DISABLE_BORDERS => 0,
    PLAYER_OPTION_TECH_TREE_TABLE        => 0,

    PLAYER_OPTION_PROGRESS_BARS_DISABLED => 0,

    PLAYER_OPTION_FLEET_SHIP_SELECT_OLD       => 0,
    PLAYER_OPTION_FLEET_SHIP_HIDE_SPEED       => 0,
    PLAYER_OPTION_FLEET_SHIP_HIDE_CAPACITY    => 0,
    PLAYER_OPTION_FLEET_SHIP_HIDE_CONSUMPTION => 0,

    PLAYER_OPTION_TUTORIAL_DISABLED => 1,
    PLAYER_OPTION_TUTORIAL_WINDOWED => 0,
    PLAYER_OPTION_TUTORIAL_CURRENT  => 1,
    PLAYER_OPTION_TUTORIAL_FINISHED => 0,

    PLAYER_OPTION_QUEST_LIST_FILTER => QUEST_STATUS_ALL,

    PLAYER_OPTION_LOGIN_REWARDED_LAST       => SN_DATE_PREHISTORIC_SQL,
    PLAYER_OPTION_LOGIN_REWARD_STREAK_BEGAN => SN_DATE_PREHISTORIC_SQL,
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

    if (!empty($this->to_write)) {
      foreach ($this->to_write as $key => $cork) {
        $value = is_array($this->data[$key]) ? serialize($this->data[$key]) : $this->data[$key]; // Сериализация для массивов при сохранении в БД
        $this->to_write[$key] = "({$this->user_id}, '" . db_escape($key) . "', '" . db_escape($value) . "')";
      }

      doquery("REPLACE INTO `{{player_options}}` (`player_id`, `option_id`, `value`) VALUES " . implode(',', $this->to_write));

      $this->to_write = array();
      $update_cache = true;
    }

    if (!empty($this->to_delete)) {
      foreach ($this->to_delete as $key => &$value) {
        $value = is_string($key) ? "'" . db_escape($key) . "'" : $key;
      }

      doquery("DELETE FROM `{{player_options}}` WHERE `player_id` = {$this->user_id} AND `option_id` IN (" . implode(',', $this->to_delete) . ") ");

      $this->to_delete = array();
      $update_cache = true;
    }

    if ($update_cache) {
      global $sn_cache;

      $field_name = $this->cached_name();
      $sn_cache->$field_name = $this->data;
    }

    return true;
  }


  public function __construct($user_id) {
    $this->user_change($user_id);
  }

  public function user_change($user_id, $forceLoad = false) {
    $this->loaded = false;
    $this->user_id = round(floatval($user_id));
    $this->load($forceLoad);
  }

  protected function cached_name() {
    return 'options_' . $this->user_id;
  }

  protected function load($forceLoad = false) {
    global $sn_cache;

    if ($this->loaded) {
      return;
    }

    $this->data = $this->defaults;
    $this->to_write = array();
    $this->to_delete = array();

    if (!$this->user_id) {
      $this->loaded = true;

      return;
    }

    $field_name = $this->cached_name();
    if (!$forceLoad) {
      $a_data = $sn_cache->$field_name;

      if (!empty($a_data)) {
        $this->data = array_replace_recursive($this->data, $a_data);

        return;
      }
    }

    $query = doquery("SELECT * FROM `{{player_options}}` WHERE `player_id` = {$this->user_id} FOR UPDATE");
    while ($row = db_fetch($query)) {
      // $this->data[$row['option_id']] = $row['value'];
      $this->data[$row['option_id']] = is_string($row['value']) && ($temp = unserialize($row['value'])) !== false ? $temp : $row['value']; // Десериализация
    }
    $sn_cache->$field_name = $this->data;
  }

}
