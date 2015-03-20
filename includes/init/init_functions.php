<?php

// ------------------------------------------------------------------------------------------------------------------------------
function sn_sys_load_php_files($dir_name, $phpEx = 'php', $modules = false) {
  if(file_exists($dir_name)) {
    $dir = opendir($dir_name);
    while(($file = readdir($dir)) !== false) {
      if($file == '..' || $file == '.') {
        continue;
      }

      $full_filename = $dir_name . $file;
      if($modules && is_dir($full_filename)) {
        if(file_exists($full_filename = "{$full_filename}/{$file}.{$phpEx}")) {
          require_once($full_filename);
          // Registering module
          if(class_exists($file)) {
            new $file($full_filename);
          }
        }
      } else {
        $extension = substr($full_filename, -strlen($phpEx));
        if($extension == $phpEx) {
          require_once($full_filename);
        }
      }
    }
  }
}

function sys_refresh_tablelist($db_prefix) {
  global $sn_cache;

  $tl = array();
  $query = doquery('SHOW TABLES;');
  while($row = mysql_fetch_assoc($query)) {
    foreach($row as $row) {
      $table_name = str_replace($db_prefix, '', $row);
      $tl[$table_name] = $table_name;
    }
  }
  $sn_cache->tables = $tl;
}

function init_update(&$config) {
  global $db_prefix;

  $update_file = SN_ROOT_PHYSICAL . "includes/update" . DOT_PHP_EX;
  if(file_exists($update_file)) {
    if(filemtime($update_file) > $config->db_loadItem('var_db_update') || $config->db_loadItem('db_version') < DB_VERSION) {
      if(defined('IN_ADMIN')) {
        sn_db_transaction_start(); // Для защиты от двойного запуска апдейта - начинаем транзакцию. Так запись в базе будет блокирована
        if(SN_TIME_NOW >= $config->db_loadItem('var_db_update_end')) {
          $config->db_saveItem('var_db_update_end', SN_TIME_NOW + ($config->upd_lock_time ? $config->upd_lock_time : 300));
          sn_db_transaction_commit();

          require_once($update_file);
          sys_refresh_tablelist($db_prefix);

          $time_now = time();
          $config->db_saveItem('var_db_update', $time_now);
          $config->db_saveItem('var_db_update_end', $time_now);
        } elseif(filemtime($update_file) > $config->var_db_update) {
          $timeout = $config->var_db_update_end - SN_TIME_NOW;
          die(
            "Обновляется база данных. Рассчетное время окончания - {$timeout} секунд (время обновления может увеличиваться). Пожалуйста, подождите...<br />
            Obnovljaetsja baza dannyh. Rasschetnoe vremya okonchanija - {$timeout} secund. Pozhalujsta, podozhdute...<br />
            Database update in progress. Estimated update time {$timeout} seconds (can increase depending on update process). Please wait..."
          );
        }
        sn_db_transaction_rollback();
      } else {
        die(
          'Происходит обновление сервера - пожалуйста, подождите...<br />
          Proishodit obnovlenie servera - pozhalujsta, podozhdute...<br />
          Server upgrading now - please wait...<br />
          <a href="admin/overview.php">Admin link</a>'
        );
      }
    }
  }
}
