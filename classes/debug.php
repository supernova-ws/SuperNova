<?php
/*
 * debug.php ::  Clase Debug, maneja reporte de eventos
 *
 * V4.0 copyright 2010-2011 by Gorlum for http://supernova.ws
 *  [!] Merged `errors` to `logs`
 *  [+] Now debugger can work with database detached. All messages would be dumped to page
 *  [+] Now `logs` has both human-readable and machine-readable fields
 *
 * V3.0 copyright 2010 by Gorlum for http://supernova.ws
 *  [+] Full rewrtie & optimize
 *  [*] Now there is fallback procedure if no link to db detected
 *
 * V2.0 copyright 2010 by Gorlum for http://supernova.ws
 *  [*] Now error also contains backtrace - to see exact way problem comes
 *  [*] New method 'warning' sends message to dedicated SQL-table for non-errors
 *
 * V1.0 Created by Perberos. All rights reversed (C) 2006
 *
 *  Experiment code!!!
 *
 * vamos a experimentar >:)
 * le veo futuro a las classes, ayudaria mucho a tener un codigo mas ordenado...
 * que esperabas!!! soy newbie!!! D':<
*/

if(!defined('INSIDE')) {
  die("attemp hacking");
}

class debug {
  var $log, $numqueries;
  var $log_array;

  private $log_file_handler = null;

  function log_file($message, $ident_change = 0) {
    static $ident = 0;

    if(!defined('SN_DEBUG_LOG')) {
      return;
    }

    if($this->log_file_handler === null) {
      $this->log_file_handler = @fopen(SN_ROOT_PHYSICAL . '/.logs/supernova.log', 'a+');
      @fwrite($this->log_file_handler, "\r\n\r\n");
    }
    $ident_change < 0 ? $ident += $ident_change * 2 : false;
    if($this->log_file_handler) {
      @fwrite($this->log_file_handler, date(FMT_DATE_TIME_SQL, time()) . str_repeat(' ', $ident + 1) . $message . "\r\n");
    }
    $ident_change > 0 ? $ident += $ident_change * 2 : false;
  }

  public function __construct() {
    $this->vars = $this->log = '';
    $this->numqueries = 0;
  }

  function add($mes) {
    $this->log .= $mes;
    $this->numqueries++;
  }

  function add_to_array($mes) {
    $this->log_array[] = $mes;
  }

  function echo_log() {
    echo '<br><table><tr><td class=k colspan=4><a href="' . SN_ROOT_PHYSICAL . "admin/settings.php\">Debug Log</a>:</td></tr>{$this->log}</table>";
    die();
  }

  function compact_backtrace($backtrace, $long_comment = false) {
    static $exclude_functions = array(
//      'doquery',
//      'db_query_select', 'db_query_delete', 'db_query_insert', 'db_query_update',
//      'db_get_record_list', 'db_user_by_id', 'db_get_user_by_id'
    );

    $result = array();
    $transaction_id = SN::db_transaction_check(false) ? SN::$transaction_id : SN::$transaction_id++;
    $result[] = "tID {$transaction_id}";
    foreach($backtrace as $a_trace) {
      if(in_array($a_trace['function'], $exclude_functions)) {
        continue;
      }
      $function =
        (!empty($a_trace['type'])
          ? ($a_trace['type'] == '->'
            ? "({$a_trace['class']})" . get_class($a_trace['object'])
            : $a_trace['class']
          ) . $a_trace['type']
          : ''
        ) . $a_trace['function'] . '()';

      $file = str_replace(SN_ROOT_PHYSICAL, '', str_replace('\\', '/', !empty($a_trace['file']) ? $a_trace['file'] : ''));

      $line = !empty($a_trace['line']) ? $a_trace['line'] : '_UNDEFINED_';
      $result[] = "{$function} - '{$file}' Line {$line}";

      if(!$long_comment) {
        break;
      }
    }

    return $result;
  }

  function dump($dump = false, $force_base = false, $deadlock = false) {
    if($dump === false) {
      return;
    }

    $error_backtrace = array();
    $base_dump = false;

    if($force_base === true) {
      $base_dump = true;
    }

    if($dump === true) {
      $base_dump = true;
    } else {
      if(!is_array($dump)) {
        $dump = array('var' => $dump);
      }

      foreach($dump as $dump_var_name => $dump_var) {
        if($dump_var_name == 'base_dump') {
          $base_dump = $dump_var;
        } else {
          $error_backtrace[$dump_var_name] = $dump_var;
        }
      }
    }

    if($deadlock && ($q = db_fetch(SN::$db->mysql_get_innodb_status()))) {
      $error_backtrace['deadlock'] = explode("\n", $q['Status']);
      $error_backtrace['locks'] = _SnCacheInternal::$locks;
      $error_backtrace['cSN_data'] = _SnCacheInternal::$data;
      foreach($error_backtrace['cSN_data'] as &$location) {
        foreach($location as $location_id => &$location_data) //          $location_data = $location_id;
        {
          $location_data = isset($location_data['username']) ? $location_data['username'] :
            (isset($location_data['name']) ? $location_data['name'] : $location_id);
        }
      }
    }

    if($base_dump) {
      if(!is_array($this->log_array) || empty($this->log_array)) {
        $this->log_array = [];
      } else {
        foreach($this->log_array as $log) {
          $error_backtrace['queries'][] = $log;
        }
      }

      $error_backtrace['backtrace'] = debug_backtrace();
      unset($error_backtrace['backtrace'][1]);
      unset($error_backtrace['backtrace'][0]);

      // Converting object instances to object names

      foreach ($error_backtrace['backtrace'] as &$backtrace) {
        if(is_object($backtrace['object'])) {
          $backtrace['object'] = get_class($backtrace['object']);
        }

        if(empty($backtrace['args'])) {
          continue;
        }

        // Doing same conversion for backtrace params
        foreach($backtrace['args'] as &$arg) {
          if(is_object($arg)) {
            $arg = 'object::' . get_class($arg);
          }
        }
      }

      // $error_backtrace['query_log'] = "\r\n\r\nQuery log\r\n<table><tr><th>Number</th><th>Query</th><th>Page</th><th>Table</th><th>Rows</th></tr>{$this->log}</table>\r\n";
      $error_backtrace['$_GET'] = $_GET;
      $error_backtrace['$_POST'] = $_POST;
      $error_backtrace['$_REQUEST'] = $_REQUEST;
      $error_backtrace['$_COOKIE'] = $_COOKIE;
      $error_backtrace['$_SESSION'] = $_SESSION;
      $error_backtrace['$_SERVER'] = $_SERVER;
      global $user, $planetrow;
      $error_backtrace['user'] = $user;
      $error_backtrace['planetrow'] = $planetrow;
    }

    return $error_backtrace;
  }

  function error_fatal($die_message, $details = 'There is a fatal error on page') {
    // TODO - Записывать детали ошибки в лог-файл
    die($die_message);
  }

  function error($message = 'There is a error on page', $title = 'Internal Error', $error_code = 500, $dump = true) {
    global $config, $sys_stop_log_hit, $lang, $sys_log_disabled, $user;

    if(empty(SN::$db->connected)) {
      // TODO - писать ошибку в файл
      die('SQL server currently unavailable. Please contact Administration...');
    }

    sn_db_transaction_rollback();

    if(SN::$config->debug == 1) {
      echo "<h2>{$title}</h2><br><font color=red>" . htmlspecialchars($message) . "</font><br><hr>";
      echo "<table>{$this->log}</table>";
    }

    $fatal_error = 'Fatal error: cannot write to `logs` table. Please contact Administration...';

    $error_text = db_escape($message);
    $error_backtrace = $this->dump($dump, true, strpos($message, 'Deadlock') !== false);

    if(!$sys_log_disabled) {
      $query = "INSERT INTO `{{logs}}` SET
        `log_time` = '" . time() . "', `log_code` = '" . db_escape($error_code) . "', `log_sender` = '" . ($user['id'] ? db_escape($user['id']) : 0) . "',
        `log_username` = '" . db_escape($user['user_name']) . "', `log_title` = '" . db_escape($title) . "',  `log_text` = '" . db_escape($message) . "',
        `log_page` = '" . db_escape(strpos($_SERVER['SCRIPT_NAME'], SN_ROOT_RELATIVE) === false ? $_SERVER['SCRIPT_NAME'] : substr($_SERVER['SCRIPT_NAME'], strlen(SN_ROOT_RELATIVE))) . "'" .
        ", `log_dump` = '" . ($error_backtrace ? db_escape(serialize($error_backtrace)) : '') . "'" . ";";
      doquery($query, '', false, true) or die($fatal_error . db_error());

      $message = "Пожалуйста, свяжитесь с админом, если ошибка повторится. Ошибка №: <b>" . db_insert_id() . "</b>";

      $sys_stop_log_hit = true;
      $sys_log_disabled = true;
      !function_exists('messageBox') ? die($message) : SnTemplate::messageBox($message, 'Ошибка', '', 0, false);
    } else {
//        // TODO Здесь надо писать в файло
      ob_start();
      print("<hr>User ID {$user['id']} raised error code {$error_code} titled '{$title}' with text '{$error_text}' on page {$_SERVER['SCRIPT_NAME']}");

      foreach($error_backtrace as $name => $value) {
        print('<hr>');
        pdump($value, $name);
      }
      ob_end_flush();
      die();
    }
  }

  function warning($message, $title = 'System Message', $log_code = 300, $dump = false) {
    global $user, $lang, $sys_log_disabled;

    if(empty(SN::$db->connected)) {
      // TODO - писать ошибку в файл
      die('SQL server currently unavailable. Please contact Administration...');
    }

    $error_backtrace = $this->dump($dump, false);

    if(!$sys_log_disabled) {
      $query = "INSERT INTO `{{logs}}` SET
        `log_time` = '" . time() . "', `log_code` = '" . db_escape($log_code) . "', `log_sender` = '" . ($user['id'] ? db_escape($user['id']) : 0) . "',
        `log_username` = '" . db_escape($user['user_name']) . "', `log_title` = '" . db_escape($title) . "',  `log_text` = '" . db_escape($message) . "',
        `log_page` = '" . db_escape(strpos($_SERVER['SCRIPT_NAME'], SN_ROOT_RELATIVE) === false ? $_SERVER['SCRIPT_NAME'] : substr($_SERVER['SCRIPT_NAME'], strlen(SN_ROOT_RELATIVE))) . "'" .
        ", `log_dump` = '" . ($error_backtrace ? db_escape(serialize($error_backtrace)) : '') . "'" . ";";
      doquery($query, '', false, true);
    } else {
//        // TODO Здесь надо писать в файло
      print("<hr>User ID {$user['id']} made log entry with code {$log_code} titled '{$title}' with text '{$message}' on page {$_SERVER['SCRIPT_NAME']}");
    }
  }
}

// Copyright (c) 2009-2010 Gorlum for http://supernova.ws
// Dump variables nicer then var_dump()

function dump($value, $varname = null, $level = 0, $dumper = '') {
  if(isset($varname)) {
    $varname .= " = ";
  }

  if($level == -1) {
    $trans[' '] = '&there4;';
    $trans["\t"] = '&rArr;';
    $trans["\n"] = '&para;;';
    $trans["\r"] = '&lArr;';
    $trans["\0"] = '&oplus;';

    return strtr(htmlspecialchars($value), $trans);
  }
  if($level == 0) {
//    $dumper = '<pre>' . mt_rand(10, 99) . '|' . $varname;
    $dumper = mt_rand(10, 99) . '|' . $varname;
  }

  $type = gettype($value);
  $dumper .= $type;

  if($type == 'string') {
    $dumper .= '(' . strlen($value) . ')';
    $value = dump($value, '', -1);
  } elseif($type == 'boolean') {
    $value = ($value ? 'true' : 'false');
  } elseif($type == 'object') {
    $props = get_class_vars(get_class($value));
    $dumper .= '(' . count($props) . ') <u>' . get_class($value) . '</u>';
    foreach($props as $key => $val) {
      $dumper .= "\n" . str_repeat("\t", $level + 1) . $key . ' => ';
      $dumper .= dump($value->$key, '', $level + 1);
    }
    $value = '';
  } elseif($type == 'array') {
    $dumper .= '(' . count($value) . ')';
    foreach($value as $key => $val) {
      $dumper .= "\n" . str_repeat("\t", $level + 1) . dump($key, '', -1) . ' => ';
      $dumper .= dump($val, '', $level + 1);
    }
    $value = '';
  }
  $dumper .= " <b>$value</b>";
//  if($level == 0) {
//    $dumper .= '</pre>';
//  }

  return $dumper;
}

function pdump($value, $varname = null) {
  $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
//  print_rr($backtrace);
//  $backtrace = $backtrace[1];

  $caller = '';
  if(defined('SN_DEBUG_PDUMP_CALLER') && SN_DEBUG_PDUMP_CALLER) {
    $caller = (!empty($backtrace[1]['class']) ? $backtrace[1]['class'] : '') .
      (!empty($backtrace[1]['type']) ? $backtrace[1]['type'] : '') .
      $backtrace[1]['function'] .
      (!empty($backtrace[0]['file'])
        ? (
          ' (' . substr($backtrace[0]['file'], SN_ROOT_PHYSICAL_STR_LEN) .
          (!empty($backtrace[0]['line']) ? ':' . $backtrace[0]['line'] : '') .
          ')'
        )
        : ''
      );
    $caller = "\r\n" . $caller;
  }

  print('<pre style="text-align: left; background-color: #111111; color: #0A0; font-family: Courier, monospace !important; padding: 1em 0; font-weight: 800; font-size: 14px;">' .
    dump($value, $varname) .
    $caller .
    '</pre>'
  );
}

function debug($value, $varname = null) {
  pdump($value, $varname);
}

function pr($prePrint = false) {
  if($prePrint) {
    print("<br>");
  }
  print(mt_rand() . "<br>");
}

function pc($prePrint = false) {
  global $_PRINT_COUNT_VALUE;
  $_PRINT_COUNT_VALUE++;

  if($prePrint) {
    print("<br>");
  }
  print($_PRINT_COUNT_VALUE . "<br>");
}

function prep($message) {
  print('<pre>' . $message . '</pre>');
}

function backtrace_no_arg() {
  $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
  array_shift($trace);
  return $trace;
}