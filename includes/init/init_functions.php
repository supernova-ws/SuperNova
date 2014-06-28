<?php

// ------------------------------------------------------------------------------------------------------------------------------
function sn_sys_load_php_files($dir_name, $phpEx = 'php', $modules = false)
{
  if(file_exists($dir_name))
  {
    $dir = opendir($dir_name);
    while(($file = readdir($dir)) !== false)
    {
      if($file == '..' || $file == '.')
      {
        continue;
      }

      $full_filename = $dir_name . $file;
      if($modules && is_dir($full_filename))
      {
        if(file_exists($full_filename = "{$full_filename}/{$file}.{$phpEx}"))
        {
          require_once($full_filename);
          // Registering module
          if(class_exists($file))
          {
            new $file($full_filename);
          }
        }
      }
      else
      {
        $extension = substr($full_filename, -strlen($phpEx));
        if($extension == $phpEx)
        {
          require_once($full_filename);
        }
      }
    }
  }
}

function sys_refresh_tablelist($db_prefix)
{
  global $sn_cache;

  $query = doquery('SHOW TABLES;');

  while ( $row = mysql_fetch_assoc($query) )
  {
    foreach($row as $row)
    {
      $tl[] = str_replace($db_prefix, '', $row);
    }
  }
  $sn_cache->tables = $tl;
}
