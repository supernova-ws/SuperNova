<?php

/**
 * User: Gorlum
 * Date: 01.09.2015
 * Time: 15:58
 */
class db_mysql {
  const DB_MYSQL_TRANSACTION_SERIALIZABLE = 'SERIALIZABLE';
  const DB_MYSQL_TRANSACTION_REPEATABLE_READ = 'REPEATABLE READ';
  const DB_MYSQL_TRANSACTION_READ_COMMITTED = 'READ COMMITTED';
  const DB_MYSQL_TRANSACTION_READ_UNCOMMITTED = 'READ UNCOMMITTED';

  /**
   * Статус соеднения с MySQL
   *
   * @var bool
   */
  public $connected = false;

  /**
   * Соединение с MySQL
   *
   * @var resource $link
   */
  public $link;

  protected $dbsettings = array();


  function doquery($query, $table = '', $fetch = false, $skip_query_check = false) {
    global $numqueries, $debug, $sn_cache, $config, $db_prefix;

    if(!is_string($table)) {
      $fetch = $table;
    }

    if(!$this->connected) {
      $this->sn_db_connect();
    }

    $query = trim($query);
    $this->security_watch_user_queries($query);
    $skip_query_check or $this->security_query_check_bad_words($query);

    $sql = $query;
    if(strpos($sql, '{{') !== false) {
      foreach($sn_cache->tables as $tableName) {
        $sql = str_replace("{{{$tableName}}}", $db_prefix.$tableName, $sql);
      }
    }

    if($config->debug) {
      $numqueries++;
      $arr = debug_backtrace();
      $file = end(explode('/',$arr[0]['file']));
      $line = $arr[0]['line'];
      $debug->add("<tr><th>Query $numqueries: </th><th>$query</th><th>$file($line)</th><th>$table</th><th>$fetch</th></tr>");
    }

    if(defined('DEBUG_SQL_COMMENT')) {
      $backtrace = debug_backtrace();
      $sql_comment = $debug->compact_backtrace($backtrace, defined('DEBUG_SQL_COMMENT_LONG'));

      $sql_commented = '/* ' . implode("<br />", $sql_comment) . '<br /> */ ' . preg_replace("/\s+/", ' ', $sql);
      if(defined('DEBUG_SQL_ONLINE')) {
        $debug->warning($sql_commented, 'SQL Debug', LOG_DEBUG_SQL);
      }

      if(defined('DEBUG_SQL_ERROR')) {
        array_unshift($sql_comment, preg_replace("/\s+/", ' ', $sql));
        $debug->add_to_array($sql_comment);
        // $debug->add_to_array($sql_comment . preg_replace("/\s+/", ' ', $sql));
      }
      $sql = $sql_commented;
    }

    $sqlquery = $this->mysql_query($sql) or $debug->error(db_error()."<br />$sql<br />",'SQL Error');

    return $fetch ? $this->db_fetch($sqlquery) : $sqlquery;
  }


  function security_watch_user_queries($query) {
    // TODO Заменить это на новый логгер
    global $config, $is_watching, $user, $debug;

    if(!$is_watching && $config->game_watchlist_array && in_array($user['id'], $config->game_watchlist_array))
    {
      if(!preg_match('/^(select|commit|rollback|start transaction)/i', $query)) {
        $is_watching = true;
        $msg = "\$query = \"{$query}\"\n\r";
        if(!empty($_POST)) {
          $msg .= "\n\r" . dump($_POST,'$_POST');
        }
        if(!empty($_GET)) {
          $msg .= "\n\r" . dump($_GET,'$_GET');
        }
        $debug->warning($msg, "Watching user {$user['id']}", 399, array('base_dump' => true));
        $is_watching = false;
      }
    }
  }


  function security_query_check_bad_words($query) {
    global $user, $dm_change_legit, $mm_change_legit;

    switch(true) {
      case stripos($query, 'RUNCATE TABL') != false:
      case stripos($query, 'ROP TABL') != false:
      case stripos($query, 'ENAME TABL') != false:
      case stripos($query, 'REATE DATABAS') != false:
      case stripos($query, 'REATE TABL') != false:
      case stripos($query, 'ET PASSWOR') != false:
      case stripos($query, 'EOAD DAT') != false:
      case stripos($query, 'RPG_POINTS') != false && stripos(trim($query), 'UPDATE ') === 0 && !$dm_change_legit:
      case stripos($query, 'METAMATTER') != false && stripos(trim($query), 'UPDATE ') === 0 && !$mm_change_legit:
      case stripos($query, 'AUTHLEVEL') != false && $user['authlevel'] < 3 && stripos($query, 'SELECT') !== 0:
        $report  = "Hacking attempt (".date("d.m.Y H:i:s")." - [".time()."]):\n";
        $report .= ">Database Inforamation\n";
        $report .= "\tID - ".$user['id']."\n";
        $report .= "\tUser - ".$user['username']."\n";
        $report .= "\tAuth level - ".$user['authlevel']."\n";
        $report .= "\tAdmin Notes - ".$user['adminNotes']."\n";
        $report .= "\tCurrent Planet - ".$user['current_planet']."\n";
        $report .= "\tUser IP - ".$user['user_lastip']."\n";
        $report .= "\tUser IP at Reg - ".$user['ip_at_reg']."\n";
        $report .= "\tUser Agent- ".$_SERVER['HTTP_USER_AGENT']."\n";
        $report .= "\tCurrent Page - ".$user['current_page']."\n";
        $report .= "\tRegister Time - ".$user['register_time']."\n";
        $report .= "\n";

        $report .= ">Query Information\n";
        $report .= "\tQuery - ".$query."\n";
        $report .= "\n";

        $report .= ">\$_SERVER Information\n";
        $report .= "\tIP - ".$_SERVER['REMOTE_ADDR']."\n";
        $report .= "\tHost Name - ".$_SERVER['HTTP_HOST']."\n";
        $report .= "\tUser Agent - ".$_SERVER['HTTP_USER_AGENT']."\n";
        $report .= "\tRequest Method - ".$_SERVER['REQUEST_METHOD']."\n";
        $report .= "\tCame From - ".$_SERVER['HTTP_REFERER']."\n";
        $report .= "\tPage is - ".$_SERVER['SCRIPT_NAME']."\n";
        $report .= "\tUses Port - ".$_SERVER['REMOTE_PORT']."\n";
        $report .= "\tServer Protocol - ".$_SERVER['SERVER_PROTOCOL']."\n";

        $report .= "\n--------------------------------------------------------------------------------------------------\n";

        $fp = fopen(SN_ROOT_PHYSICAL . 'badqrys.txt', 'a');
        fwrite($fp, $report);
        fclose($fp);

        $message = 'Привет, я не знаю то, что Вы пробовали сделать, но команда, которую Вы только послали базе данных, не выглядела очень дружественной и она была заблокированна.<br /><br />Ваш IP, и другие данные переданны администрации сервера. Удачи!.';
        die($message);
        break;
    }
  }


  function load_db_settings() {
    $dbsettings = array();

    require(SN_ROOT_PHYSICAL . "config" . DOT_PHP_EX);

    $this->dbsettings = $dbsettings;
  }

  function sn_db_connect($external_db_settings = null) {
    if(!empty($external_db_settings) && is_array($external_db_settings)) {
      $this->db_disconnect();
      $this->dbsettings = $external_db_settings;
    }

    if(empty($this->dbsettings)) {
      $this->db_disconnect();

      $dbsettings = array();

      require(SN_ROOT_PHYSICAL . "config" . DOT_PHP_EX);

      $this->dbsettings = $dbsettings;
    }

    return $this->connected || $this->mysql_connect();
  }

  function mysql_connect() {
    global $debug;

    static $need_keys = array('server', 'user', 'pass', 'name', 'prefix');

    if($this->connected) {
      return true;
    }

    if(empty($this->dbsettings) || !is_array($this->dbsettings) || array_intersect($need_keys, array_keys($this->dbsettings)) != $need_keys) {
      $debug->error_fatal('There is missconfiguration in your config.php. Check it again', $this->db_error());
    }

    // TODO !!!!!! DEBUG -> error!!!!
    @$this->link = mysql_connect($this->dbsettings['server'], $this->dbsettings['user'], $this->dbsettings['pass']);
    if(!$this->link) {
      $debug->error_fatal('DB Error - cannot connect to server', $this->db_error());
    }

    $this->mysql_query("/*!40101 SET NAMES 'utf8' */") or $debug->error_fatal('DB error - cannot set names', $this->db_error());
    $this->mysql_query("SET NAMES 'utf8';") or $debug->error_fatal('DB error - cannot set names', $this->db_error());

    mysql_select_db($this->dbsettings['name']) or $debug->error_fatal('DB error - cannot find DB on server', $this->db_error());
    $this->mysql_query('SET SESSION TRANSACTION ISOLATION LEVEL ' . self::DB_MYSQL_TRANSACTION_REPEATABLE_READ . ';') or $debug->error_fatal('DB error - cannot set desired isolation level', $this->db_error());

    return $this->connected = true;
  }

  function db_disconnect() {
    if($this->connected || !empty($this->link)) {
      $this->connected = !$this->mysql_close();
    }

    return !$this->connected;
  }

  function db_get_table_list($db_prefix) {
    $query = $this->mysql_get_table_list();

    $prefix_length = strlen($db_prefix);

    $tl = array();
    while($row = $this->db_fetch($query)) {
      foreach($row as $table_name) {
        if(strpos($table_name, $db_prefix) === 0) {
          $table_name = substr($table_name, $prefix_length);
        }
        // $table_name = str_replace($db_prefix, '', $table_name);
        $tl[$table_name] = $table_name;
      }
    }

    return $tl;
  }

  function mysql_get_table_list() {
    return $this->mysql_query('SHOW TABLES;');
  }
  function mysql_get_innodb_status() {
    return $this->mysql_query('SHOW ENGINE INNODB STATUS;');
  }



  function mysql_query($query_string) {
    return mysql_query($query_string, $this->link);
  }
  function db_fetch(&$query) {
    return mysql_fetch_assoc($query);
  }
  function db_fetch_row(&$query) {
    return mysql_fetch_row($query);
  }
  function db_escape($unescaped_string) {
    return mysql_real_escape_string($unescaped_string, $this->link);
  }
  function mysql_close() {
    return mysql_close($this->link);
  }
  function db_error() {
    return mysql_error($this->link);
  }
  function db_insert_id() {
    return mysql_insert_id($this->link);
  }
  function db_num_rows(&$result) {
    return mysql_num_rows($result);
  }
  function db_affected_rows() {
    return mysql_affected_rows($this->link);
  }
  function db_get_client_info() {
    return mysql_get_client_info();
  }
  function db_get_server_info() {
    return mysql_get_server_info($this->link);
  }
  function db_get_host_info() {
    return mysql_get_host_info($this->link);
  }
  function db_get_server_stat() {
    return mysql_stat($this->link);
  }

}
