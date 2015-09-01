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

    $sqlquery = $this->__db_query($sql) or $debug->error(db_error()."<br />$sql<br />",'SQL Error');

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


  function sn_db_connect() {
    if(!$this->connected) {
      $dbsettings = array();

      require(SN_ROOT_PHYSICAL . "config" . DOT_PHP_EX);

      return $this->__sn_db_connect($dbsettings);
    } else {
      return true;
    }
  }

  function __sn_db_connect($dbsettings) {
    global $link, $debug;

    if(!$this->connected) {
      // TODO !!!!!! DEBUG -> error!!!!
      @$link = mysql_connect($dbsettings['server'], $dbsettings['user'], $dbsettings['pass']);
      if(!$link) {
        $debug->error($this->db_error(), 'DB Error - cannot connect to server');
      }

      $this->__db_query("/*!40101 SET NAMES 'utf8' */") or die('Error: ' . $this->db_error());
      $this->__db_query("SET NAMES 'utf8';") or die('Error: ' . $this->db_error());

      mysql_select_db($dbsettings['name']) or $debug->error($this->db_error(), 'DB error - cannot find DB on server');
      $this->__db_query('SET SESSION TRANSACTION ISOLATION LEVEL ' . self::DB_MYSQL_TRANSACTION_REPEATABLE_READ . ';') or die('Error: ' . $this->db_error());

      $this->connected = true;
    }

    return true;
  }

  function __db_query($query_string, $a_link = null) {
    return $a_link ? mysql_query($query_string, $a_link) : mysql_query($query_string);
  }

  function db_fetch(&$query) {
    return mysql_fetch_assoc($query);
  }

  function db_fetch_row(&$query) {
    return mysql_fetch_row($query);
  }

  function db_escape($unescaped_string, $link = null) {
    return $link ? mysql_real_escape_string($unescaped_string, $link) : mysql_real_escape_string($unescaped_string);
//    return mysql_real_escape_string($unescaped_string, $link);
  }

  function db_disconnect($link = null) {
    global $link;

    if($this->connected || !empty($link)) {
      $this->connected = !$this->__db_disconnect($link);
    }

    return !$this->connected;
  }

  function __db_disconnect($link = null) {
    return $link ? mysql_close($link) : mysql_close();
  }

  function db_error($link = null) {
    return $link ? mysql_error($link) : mysql_error();
//    return mysql_error($link);
  }

  function db_insert_id($link = null) {
    return $link ? mysql_insert_id($link) : mysql_insert_id();
//    return mysql_insert_id($link);
  }

  function db_num_rows(&$result) {
    return mysql_num_rows($result);
  }

  function db_affected_rows($link = null) {
    return $link ? mysql_affected_rows($link) : mysql_affected_rows();
//    return mysql_affected_rows($link);
  }


  function __db_get_innodb_status($link = null) {
    return $this->__db_query('SHOW ENGINE INNODB STATUS;', $link);
  }

  function __db_get_table_list($link = null) {
    return $this->__db_query('SHOW TABLES;', $link);
  }

  function db_get_table_list($db_prefix, $link = null) {
    $query = $this->__db_get_table_list($link);

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


  function db_get_client_info() {
    return mysql_get_client_info();
  }

  function db_get_server_info($link = null) {
    return $link ? mysql_get_server_info($link) : mysql_get_server_info();
//    return mysql_get_server_info($link);
  }

  function db_get_host_info($link = null) {
    return $link ? mysql_get_host_info($link) : mysql_get_host_info();
//    return mysql_get_host_info($link);
  }

  function db_get_server_stat($link = null) {
    return $link ? mysql_stat($link) : mysql_stat();
//    return mysql_stat($link);
  }

}
