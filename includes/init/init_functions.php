<?php

// ------------------------------------------------------------------------------------------------------------------------------
function sn_sys_load_php_files($dir_name, $load_extension = '.php', $modules = false) {
  if(!file_exists($dir_name) || !is_dir($dir_name)) {
    return;
  }

  $dir = opendir($dir_name);
  while (($file = readdir($dir)) !== false) {
    if ($file == '..' || $file == '.') {
      continue;
    }

    $full_filename = $dir_name . $file;
    if ($modules && is_dir($full_filename)) {
      if (file_exists($full_filename = "{$full_filename}/{$file}{$load_extension}")) {
        require_once($full_filename);
        // Registering module
        if (class_exists($file)) {
          new $file($full_filename);
        }
      }
    } else {
      $extension = substr($full_filename, -strlen($load_extension));
      if ($extension == $load_extension) {
        require_once($full_filename);
      }
    }
  }
}

function sys_refresh_tablelist() {
  classSupernova::$cache->tables = classSupernova::$db->db_get_table_list();
}

/**
 *
 */
function init_update() {
  $update_file = SN_ROOT_PHYSICAL . "includes/update" . DOT_PHP_EX;
  if(file_exists($update_file)) {
    if(filemtime($update_file) > classSupernova::$config->db_loadItem('var_db_update') || classSupernova::$config->db_loadItem('db_version') < DB_VERSION) {
      if(defined('IN_ADMIN')) {
        sn_db_transaction_start(); // Для защиты от двойного запуска апдейта - начинаем транзакцию. Так запись в базе будет блокирована
        if(SN_TIME_NOW >= classSupernova::$config->db_loadItem('var_db_update_end')) {
          classSupernova::$config->db_saveItem('var_db_update_end', SN_TIME_NOW + (classSupernova::$config->upd_lock_time ? classSupernova::$config->upd_lock_time : 300));
          sn_db_transaction_commit();

          require_once($update_file);
          sys_refresh_tablelist();

          $current_time = time();
          classSupernova::$config->db_saveItem('var_db_update', $current_time);
          classSupernova::$config->db_saveItem('var_db_update_end', $current_time);
        } elseif(filemtime($update_file) > classSupernova::$config->var_db_update) {
          $timeout = classSupernova::$config->var_db_update_end - SN_TIME_NOW;
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
