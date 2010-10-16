<?php

/**
 * db.php
 * Previously mysql.php
 *
 * @version 1.0
 * @copyright 2008 by Chlorel for XNova
 */

if ( !defined('INSIDE') ) {
  die();
}

function doquery($query, $table = "", $fetch = false){
  global $numqueries, $link, $debug, $ugamela_root_path, $user, $tableList, $cache, $is_watching, $config;

  if($config->game_watchlist_array)
  {
    if(!$is_watching && in_array($user['id'], $config->game_watchlist_array))
    {
      if(stripos($query, 'SELECT') !== 0)
      {
        $is_watching = true;
        $msg = "\$query = \"{$query}\"\n\rtable = '{$table}', fetch = '{$fetch}'";
        if(!empty($_POST))
        {
          $msg .= "\n\r" . dump($_POST,'$_POST');
        }
        if(!empty($_GET))
        {
          $msg .= "\n\r" . dump($_GET,'$_GET');
        }
        $debug->warning($msg,"Watching user {$user['id']}",399);
        $is_watching = false;
      }
    }
  }

  require($ugamela_root_path.'config.php');

  $badword = false;
  if ((stripos($query, 'RUNCATE TABL') != FALSE) && ($table != 'errors')) {
    $badword = true;
  }elseif (stripos($query, 'ROP TABL') != FALSE) {
    $badword = true;
  }elseif (stripos($query, 'ENAME TABL') != FALSE) {
    $badword = true;
  }elseif (stripos($query, 'REATE DATABAS') != FALSE) {
    $badword = true;
  }elseif (stripos($query, 'REATE TABL') != FALSE) {
    $badword = true;
  }elseif (stripos($query, 'ET PASSWOR') != FALSE) {
    $badword = true;
  }elseif (stripos($query, 'EOAD DAT') != FALSE) {
    $badword = true;
  }
  if ($badword) {
    $message = 'Привет, я не знаю то, что Вы пробовали сделать, но команда, которую Вы только послали базе данных, не выглядела очень дружественной и она была заблокированна.<br /><br />Ваш IP, и другие данные переданны администрации сервера. Удачи!.';

    $report  = "Hacking attempt (".date("d.m.Y H:i:s")." - [".time()."]):\n";
    $report .= ">Database Info\n";
    $report .= "\tID - ".$user['id']."\n";
    $report .= "\tUser - ".$user['username']."\n";
    $report .= "\tAuth level - ".$user['authlevel']."\n";
    $report .= "\tAdmin Notes - ".$user['adminNotes']."\n";
    $report .= "\tCurrent Planet - ".$user['current_planet']."\n";
    $report .= "\tUser IP - ".$user['user_lastip']."\n";
    $report .= "\tUser IP at Reg - ".$user['ip_at_reg']."\n";
    $report .= "\tUser Agent- ".$user['user_agent']."\n";
    $report .= "\tCurrent Page - ".$user['current_page']."\n";
    $report .= "\tRegister Time - ".$user['register_time']."\n";

    $report .= "\n";

    $report .= ">Query Info\n";
    $report .= "\tTable - ".$table."\n";
    $report .= "\tQuery - ".$query."\n";

    $report .= "\n";

    $report .= ">\$_SERVER Info\n";
    $report .= "\tIP - ".$_SERVER['REMOTE_ADDR']."\n";
    $report .= "\tHost Name - ".$_SERVER['HTTP_HOST']."\n";
    $report .= "\tUser Agent - ".$_SERVER['HTTP_USER_AGENT']."\n";
    $report .= "\tRequest Method - ".$_SERVER['REQUEST_METHOD']."\n";
    $report .= "\tCame From - ".$_SERVER['HTTP_REFERER']."\n";
    $report .= "\tUses Port - ".$_SERVER['REMOTE_PORT']."\n";
    $report .= "\tServer Protocol - ".$_SERVER['SERVER_PROTOCOL']."\n";

    $report .= "\n--------------------------------------------------------------------------------------------------\n";

    $fp = fopen($ugamela_root_path.'badqrys.txt', 'a');
    fwrite($fp, $report);
    fclose($fp);

    die($message);
  }

  if(!$link) {
    $link = mysql_connect($dbsettings["server"], $dbsettings["user"], $dbsettings["pass"]) or
      $debug->error(mysql_error()."<br />$query","SQL Error");
    mysql_query("/*!40101 SET NAMES 'cp1251' */") or die("Error: " . mysql_error());
    mysql_select_db($dbsettings["name"]) or $debug->error(mysql_error()."<br />$query","SQL Error");
    echo mysql_error();
  }

  $sql = str_replace("{{table}}", $dbsettings["prefix"].$table, $query);
  if(!(strpos($sql, '{{') === false) )
  {
    foreach($cache->tables as $tableName)
    {
      $sql = str_replace("{{".$tableName."}}", $dbsettings["prefix"].$tableName, $sql);
    }
  }
  $sqlquery = mysql_query($sql) or
    $debug->error(mysql_error()."<br />$sql<br />","SQL Error");
  unset($dbsettings);

  $numqueries++;
  $arr = debug_backtrace();
  $file = end(explode('/',$arr[0]['file']));
  $line = $arr[0]['line'];
  $debug->add("<tr><th>Query $numqueries: </th><th>$query</th><th>$file($line)</th><th>$table</th><th>$fetch</th></tr>");

  if($fetch){
    $sqlrow = mysql_fetch_array($sqlquery);
    return $sqlrow;
  }else{
    return $sqlquery;
  }
}

?>