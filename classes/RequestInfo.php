<?php

/**
 * Created by Gorlum 29.08.2015 16:49
 */

/**
 * Подробности о запросе
 */
class RequestInfo {
  /**
   * Идентификационная строка устройства
   *
   * @var string
   */
  protected $device_cypher = '';
  /**
   * Идентификатор устройства
   *
   * @var string
   */
  public $device_id = 0;

  /**
   * Строка User-agent пользовательского браузера
   *
   * @var string
   */
  protected $user_agent = '';
  /**
   * Внутренний идентификатор строки браузера
   *
   * @var int
   */
  public $browser_id = 0;

  /**
   * Полный URL строки запроса
   *
   * @var string
   */
  protected $page_address = '';
  /**
   * ID запроса в таблице УРЛов
   *
   * @var int
   */
  public $page_address_id = 0;

  /**
   * Короткий УРЛ - без параметров
   *
   * @var string
   */
  protected $page_url = '';
  /**
   * ID короткого УРЛа в таблице УРЛов
   *
   * @var int
   */
  public $page_url_id = 0;

  /**
   * Адрес IPv4 в виде строки
   *
   * @var string
   */
  public $ip_v4_string = '';
  /**
   * Адрес IPv4 в виде целого
   *
   * @var int
   */
  public $ip_v4_int = 0;
  /**
   * Цепочка прокси IPv4
   *
   * @var string
   */
  public $ip_v4_proxy_chain = '';

  protected $write_full_url = false;

  public function __construct() {
    $this->write_full_url = !classSupernova::$config->security_write_full_url_disabled;

    // Инфа об устройстве и браузере - общая для всех
    sn_db_transaction_start();
    $this->device_cypher = $_COOKIE[SN_COOKIE_D];
    if($this->device_cypher) {
      $cypher_safe = db_escape($this->device_cypher);
      $device_id = classSupernova::$db->doSelectFetchArray("SELECT `device_id` FROM {{security_device}} WHERE `device_cypher` = '{$cypher_safe}' LIMIT 1 FOR UPDATE");
      if(!empty($device_id['device_id'])) {
        $this->device_id = $device_id['device_id'];
      }
    }

    if($this->device_id <= 0) {
      do {
        $cypher_safe = db_escape($this->device_cypher = sys_random_string());
        $row = classSupernova::$db->doSelectFetchArray("SELECT `device_id` FROM {{security_device}} WHERE `device_cypher` = '{$cypher_safe}' LIMIT 1 FOR UPDATE");
      } while (!empty($row));
      classSupernova::$db->doInsertSet(TABLE_SECURITY_DEVICE, array(
        'device_cypher' => $this->device_cypher,
      ));
      $this->device_id = classSupernova::$db->db_insert_id();
      sn_setcookie(SN_COOKIE_D, $this->device_cypher, PERIOD_FOREVER, SN_ROOT_RELATIVE);
    }
    sn_db_transaction_commit();

    sn_db_transaction_start();
    $this->user_agent = $_SERVER['HTTP_USER_AGENT'];
    $this->browser_id = db_get_set_unique_id_value($_SERVER['HTTP_USER_AGENT'], 'browser_id', 'security_browser', 'browser_user_agent');
    sn_db_transaction_commit();

    sn_db_transaction_start();
    $this->page_address = substr($_SERVER['PHP_SELF'], strlen(SN_ROOT_RELATIVE));
    $this->page_address_id = db_get_set_unique_id_value($this->page_address, 'url_id', 'security_url', 'url_string');
    sn_db_transaction_commit();

    if($this->write_full_url) {
      sn_db_transaction_start();
      $this->page_url = substr($_SERVER['REQUEST_URI'], strlen(SN_ROOT_RELATIVE));
      if(strpos($_SERVER['REQUEST_URI'], '/simulator.php') === 0) {
        $this->page_url = '/simulator.php';
      }
      $this->page_url_id = db_get_set_unique_id_value($this->page_url, 'url_id', 'security_url', 'url_string');
      sn_db_transaction_commit();
    }

    $ip = sec_player_ip();
    $this->ip_v4_string = $ip['ip'];
    $this->ip_v4_int = ip2longu($this->ip_v4_string);
    $this->ip_v4_proxy_chain = $ip['proxy_chain'];
  }

  /**
   * Вставляет запись системы безопасности
   *
   * @param $user_id_unsafe
   *
   * @return array|bool|mysqli_result|null
   *
   */
  public function db_security_entry_insert($user_id_unsafe) {
    // TODO $user_id = !empty(self::$user['id']) ? self::$user['id'] : 'NULL';
    if(empty($user_id_unsafe)) {
      // self::flog('Нет ИД пользователя');
      return true;
    }

    return classSupernova::$db->doInsertSet(TABLE_SECURITY_PLAYER_ENTRY, array(
      'player_id'  => empty($user_id_unsafe) ? null : $user_id_unsafe,
      'device_id'  => $this->device_id,
      'browser_id' => $this->browser_id,
      'user_ip'    => $this->ip_v4_int,
      'user_proxy' => $this->ip_v4_proxy_chain,
    ), DB_INSERT_IGNORE);
  }

  /**
   * Вставляет данные в счётчик
   *
   * @param $user_id_unsafe
   */
  public function db_counter_insert($user_id_unsafe) {
    global $sys_stop_log_hit;

    if($sys_stop_log_hit || !classSupernova::$config->game_counter) {
      return;
    }

    classSupernova::$db->isWatching = true;
    $values = array(
      'visit_time'  => SN_TIME_SQL,
      'user_id'     => $user_id_unsafe,
      'device_id'   => $this->device_id,
      'browser_id'  => $this->browser_id,
      'user_ip'     => $this->ip_v4_int,
      'user_proxy'  => $this->ip_v4_proxy_chain,
      'page_url_id' => $this->page_address_id,
    );
    $this->write_full_url ? $values['plain_url_id'] = $this->page_url_id : false;
    classSupernova::$db->doInsertSet(TABLE_COUNTER, $values);

    classSupernova::$db->isWatching = false;
  }

}
