<?php
/*
 * debug.class.php ::  Clase Debug, maneja reporte de eventos
 *
 * V4.0 copyright 2010-2011 by Gorlum for http://supernova.ws
 *  [!] Merged `errors` to `logs`
 *  [+] Now debugger can work with database detached. All messages would be dumped to page
 *  [+] Now `logs` has both human-readable and machine-readable fields
 *
 * V3.0 copyright 2010 by Gorlum for http://supernova.ws
 *  [+] Full rewrtie & optimize
 *  [*] Now there is fallback procedure if no $link to db detected
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

if(!defined('INSIDE'))
{
  die("attemp hacking");
}

class debug
{
  var $log, $numqueries;

  function debug()
  {
    $this->vars = $this->log = '';
    $this->numqueries = 0;
  }

  function add($mes)
  {
    $this->log .= $mes;
    $this->numqueries++;
  }

  function echo_log()
  {
    echo "<br><table><tr><td class=k colspan=4><a href=\"" . SN_ROOT_PHYSICAL . "admin/settings.php\">Debug Log</a>:</td></tr>{$this->log}</table>";
    die();
  }

  function dump($dump = false, $force_base = false)
  {
    if($dump === false)
    {
      return;
    }

    $error_backtrace = array();
    $base_dump = false;

    if($force_base === true)
    {
      $base_dump = true;
    }

    if($dump === true)
    {
      $base_dump = true;
    }
    else
    {
      if(!is_array($dump))
      {
        $dump = array('var' => $dump);
      }

      foreach($dump as $dump_var_name => $dump_var)
      {
        if($dump_var_name == 'base_dump')
        {
          $base_dump = $dump_var;
        }
        else
        {
          $error_backtrace[$dump_var_name] = $dump_var;
        }
      }
    }

    if($base_dump)
    {
      $error_backtrace['backtrace'] = debug_backtrace();
      unset($error_backtrace['backtrace'][1]);
      unset($error_backtrace['backtrace'][0]);
      $error_backtrace['query_log'] = "\r\n\r\nQuery log\r\n<table><tr><th>Number</th><th>Query</th><th>Page</th><th>Table</th><th>Rows</th></tr>{$this->log}</table>\r\n";
      $error_backtrace['user'] = $GLOBALS['user'];
      $error_backtrace['planetrow'] = $GLOBALS['planetrow'];
      $error_backtrace['$_GET'] = $_GET;
      $error_backtrace['$_POST'] = $_POST;
      $error_backtrace['$_COOKIES'] = $_COOKIES;
      $error_backtrace['$_SESSION'] = $_SESSION;
      $error_backtrace['$_SERVER'] = $_SERVER;
    }

    return $error_backtrace;
  }

  function error($message = 'There is a error on page', $title = 'Internal Error', $error_code = 500, $dump = true)
  {
    mysql_query("ROLLBACK;");

    global $config;

    if($config->debug == 1)
    {
      echo "<h2>{$title}</h2><br><font color=red>{$message}</font><br><hr>";
      echo "<table>{$this->log}</table>";
    }

    global $link, $sys_stop_log_hit;

    require(SN_ROOT_PHYSICAL . 'config.' . PHP_EX);

    if(!$link)
    {
      $link = mysql_connect($dbsettings['server'], $dbsettings['user'], $dbsettings['pass']);
      mysql_query('/*!40101 SET NAMES \'cp1251\' */');
      mysql_select_db($dbsettings['name']);

      if(!$link)
      {
        die('mySQL server currently unavailable. Please contact Administration...');
      }
    }

    $fatal_error = 'Fatal error: cannot write to `logs` table. Please contact Administration...';

    $error_text = mysql_real_escape_string($message);
    $error_backtrace = $this->dump($dump, true);

    if(!$GLOBALS['sys_log_disabled'])
    {
      if($error_backtrace)
      {
        $error_backtrace = ", `log_dump` = '" . mysql_real_escape_string(serialize($error_backtrace)) . "'";
      }
      else
      {
        $error_backtrace = '';
      }
      mysql_query("INSERT INTO `{$dbsettings['prefix']}logs` SET
        `log_time` = '".time()."', `log_code` = '{$error_code}', `log_sender` = '{$GLOBALS['user']['id']}', `log_username` = '{$GLOBALS['user']['username']}',
        `log_title` = '{$title}',  `log_text` = '{$error_text}', `log_page` = '".mysql_real_escape_string($_SERVER['HTTP_REFERER'])."'{$error_backtrace};")
      or die($fatal_error . mysql_error());
/*
      $q = mysql_fetch_assoc(mysql_query("SELECT max(log_id) AS rows FROM {$dbsettings['prefix']}logs;"))
        or die($fatal_error);
*/
      $message = "Пожалуйста, свяжитесь с админом, если ошибка повторится. Ошибка №: <b>" . mysql_insert_id() . "</b>";

      $sys_stop_log_hit = true;
      $GLOBALS['sys_log_disabled'] = true;
      if (!function_exists('message'))
      {
        die($message);
      }
      else
      {
        message($message, 'Ошибка', $dest, 0, false);
      }
    }
    else
    {
      ob_start();
      print("<hr>User ID {$GLOBALS['user']['id']} raised error code {$error_code} titled '{$title}' with text '{$error_text}' on page {$_SERVER['HTTP_REFERER']}");

      foreach($error_backtrace as $name => $value)
      {
        print('<hr>');
        pdump($value, $name);
      }
      ob_end_flush();
      die();
    }
  }

  function warning($message, $title = 'System Message', $log_code = 300, $dump = false)
  {
    global $link, $user;

    require(SN_ROOT_PHYSICAL . 'config.' . PHP_EX);

    if(!$link)
    {
      $link = mysql_connect($dbsettings['server'], $dbsettings['user'], $dbsettings['pass']);
      mysql_query('/*!40101 SET NAMES \'cp1251\' */');
      mysql_select_db($dbsettings['name']);
    }

    $error_backtrace = $this->dump($dump, false);

    if(!$GLOBALS['sys_log_disabled'])
    {
      if($error_backtrace)
      {
        $error_backtrace = ", `log_dump` = '" . mysql_real_escape_string(serialize($error_backtrace)) . "'";
      }
      else
      {
        $error_backtrace = '';
      }
      $query = "INSERT INTO `{$dbsettings['prefix']}logs` SET
        `log_time` = '".time()."', `log_code` = '{$log_code}', `log_sender` = '{$user['id']}', `log_username` = '{$user['username']}',
        `log_title` = '{$title}',  `log_text` = '".mysql_real_escape_string($message)."',
        `log_page` = '".mysql_real_escape_string($_SERVER['HTTP_REFERER'])."'{$error_backtrace};";
      $sqlquery = mysql_query($query);
    }
    else
    {
      print("<hr>User ID {$GLOBALS['user']['id']} made log entry with code {$log_code} titled '{$title}' with text '{$message}' on page {$_SERVER['HTTP_REFERER']}");
    }
  }
}

// Copyright (c) 2009-2010 Gorlum for http://supernova.ws
// Dump variables nicer then var_dump()

function dump($value,$varname = "",$level=0,$dumper = "")
{
  if ($varname) $varname .= " = ";

  if ($level==-1)
  {
    $trans[' ']='&there4;';
    $trans["\t"]='&rArr;';
    $trans["\n"]='&para;;';
    $trans["\r"]='&lArr;';
    $trans["\0"]='&oplus;';
    return strtr(htmlspecialchars($value),$trans);
  }
  if ($level==0) $dumper = '<pre>' . $varname;

  $type = gettype($value);
  $dumper .= $type;

  if ($type=='string')
  {
    $dumper .= '('.strlen($value).')';
    $value = dump($value,"",-1);
  }
  elseif ($type=='boolean') $value= ($value?'true':'false');
  elseif ($type=='object')
  {
    $props= get_class_vars(get_class($value));
    $dumper .= '('.count($props).') <u>'.get_class($value).'</u>';
    foreach($props as $key=>$val)
    {
      $dumper .= "\n".str_repeat("\t",$level+1).$key.' => ';
      $dumper .= dump($value->$key,"",$level+1);
    }
    $value= '';
  }
  elseif ($type=='array')
  {
    $dumper .= '('.count($value).')';
    foreach($value as $key=>$val)
    {
      $dumper .= "\n".str_repeat("\t",$level+1).dump($key,"",-1).' => ';
      $dumper .= dump($val,"",$level+1);
    }
    $value= '';
  }
  $dumper .= " <b>$value</b>";
  if ($level==0) $dumper .= '</pre>';
  return $dumper;
}

function pdump($value, $varname = '')
{
  print('<span style="text-align: left">' . dump($value, $varname) . '</span>');
}

function debug($value, $varname = '')
{
  return pdump($value, $varname);
}

function pr($prePrint = false){
  if($prePrint)
    print("<br>");
  print(mt_rand() . "<br>");
}

function pc($prePrint = false){
  global $_PRINT_COUNT_VALUE;
  $_PRINT_COUNT_VALUE++;

  if($prePrint)
    print("<br>");
  print($_PRINT_COUNT_VALUE . "<br>");
}
?>
