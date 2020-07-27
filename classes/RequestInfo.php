<?php

/**
 * User: Gorlum
 * Date: 29.08.2015
 * Time: 16:49
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
   * Query param
   *
   * @var string $queryString
   */
  protected $queryString = '';
  /**
   * Query param ID
   *
   * @var int
   */
  public $queryStringId = 0;

  /**
   * Player entry ID - pointer to combination of player ID, device ID, browser ID, user IP, user proxy
   *
   * @var int $playerEntryId
   */
  protected $playerEntryId = 0;

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
    // TODO - CHANGE!!!!
    global $skip_log_query;

    $this->write_full_url = !SN::$config->security_write_full_url_disabled;

    // Инфа об устройстве и браузере - общая для всех
    sn_db_transaction_start();
    $this->device_cypher = $_COOKIE[SN_COOKIE_D];
    if ($this->device_cypher) {
      $cypher_safe = db_escape($this->device_cypher);
      /** @noinspection SqlResolve */
      $device_id = doquery("SELECT `device_id` FROM `{{security_device}}` WHERE `device_cypher` = '{$cypher_safe}' LIMIT 1 FOR UPDATE", true);
      if (!empty($device_id['device_id'])) {
        $this->device_id = $device_id['device_id'];
      }
    }

    if ($this->device_id <= 0) {
      do {
        $cypher_safe = db_escape($this->device_cypher = sys_random_string());

        /** @noinspection SqlResolve */
        $row = doquery("SELECT `device_id` FROM `{{security_device}}` WHERE `device_cypher` = '{$cypher_safe}' LIMIT 1 FOR UPDATE", true);
      } while (!empty($row));
      doquery("INSERT INTO {{security_device}} (`device_cypher`) VALUES ('{$cypher_safe}');");
      $this->device_id = db_insert_id();
      sn_setcookie(SN_COOKIE_D, $this->device_cypher, PERIOD_FOREVER, SN_ROOT_RELATIVE);
    }
    sn_db_transaction_commit();

    $this->user_agent = !empty($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    $this->browser_id = db_get_set_unique_id_value('security_browser', 'browser_id', ['browser_user_agent' => $this->user_agent,]);

    $this->page_address    = substr($_SERVER['PHP_SELF'], strlen(SN_ROOT_RELATIVE));
    $this->page_address_id = db_get_set_unique_id_value('security_url', 'url_id', ['url_string' => $this->page_address,]);

    // Not a simulator - because it can have loooooong string
    if (strpos($_SERVER['REQUEST_URI'], '/simulator.php') !== 0 && !$skip_log_query) {
      $this->queryString = !empty($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '';
      $this->queryStringId = db_get_set_unique_id_value('security_query_strings', 'id', ['query_string' => $this->queryString,]);
    }

    $ip                      = sec_player_ip();
    $this->ip_v4_string      = $ip['ip'];
    $this->ip_v4_int         = ip2longu($this->ip_v4_string);
    $this->ip_v4_proxy_chain = $ip['proxy_chain'];

    $this->playerEntryId = db_get_set_unique_id_value(
      'security_player_entry',
      'id',
      [
        'device_id'  => $this->device_id,
        'browser_id' => $this->browser_id,
        'user_ip'    => $this->ip_v4_int,
        'user_proxy' => $this->ip_v4_proxy_chain,
      ]
    );
  }

  /**
   * Вставляет запись системы безопасности
   *
   * @param $userId
   *
   * @return int
   * @deprecated
   */
  // TODO - remove
  public function db_security_entry_insert($userId) {
    // TODO $user_id = !empty(self::$user['id']) ? self::$user['id'] : 'NULL';
    if (empty($userId)) {
      // self::flog('Нет ИД пользователя');
      return true;
    }

    $pEntry = db_get_set_unique_id_value(
      'security_player_entry',
      'id',
      [
//        'player_id'  => $userId,
        'device_id'  => $this->device_id,
        'browser_id' => $this->browser_id,
        'user_ip'    => $this->ip_v4_int,
        'user_proxy' => $this->ip_v4_proxy_chain,
      ]
    );

    return $pEntry;


    // self::flog('Вставляем запись системы безопасности');
  }

  /**
   * Вставляет данные в счётчик
   *
   * @param $user_id_unsafe
   */
  public function db_counter_insert($user_id_unsafe) {
    global $config, $sys_stop_log_hit, $is_watching;

    if ($sys_stop_log_hit || !$config->game_counter) {
      return;
    }

    $user_id_safe = db_escape($user_id_unsafe);
    $proxy_safe   = db_escape($this->ip_v4_proxy_chain);

    $is_watching = true;
    doquery(
      "INSERT INTO {{counter}} SET
        `visit_time` = '" . SN_TIME_SQL . "',
        `user_id` = {$user_id_safe},
        `player_entry_id` = {$this->playerEntryId},
        `page_url_id` = {$this->page_address_id},
        `query_string_id` = {$this->queryStringId}" .
      ";");

//    `device_id` = {$this->device_id},
//        `browser_id` = {$this->browser_id},
//        `user_ip` = {$this->ip_v4_int},
//        `user_proxy` = '{$proxy_safe}',

    $is_watching = false;
  }

}
