<?php

class classLocale implements ArrayAccess {
  public $container = array();
  public $lang_list = null;
  public $active = null;

  public $enable_stat_usage = false;
  protected $stat_usage = array();
  protected $stat_usage_new = array();

  // protected $cache = null;

  public function __construct($language = DEFAULT_LANG, $enable_stat_usage = false) {
    $this->active = $language;
    $this->container = array($this->active => array());
    // $this->cache = classCache::getInstance();

    if($enable_stat_usage && empty($this->stat_usage))
    {
      $this->enable_stat_usage = $enable_stat_usage;
      $this->usage_stat_load();
      // TODO shutdown function
      register_shutdown_function(array($this, 'usage_stat_save'));
    }
  }

  public function offsetSet($offset, $value) {
    if (is_null($offset)) {
      $this->container[$this->active][] = $value;
    } else {
      $this->container[$this->active][$offset] = $value;
    }
  }
  public function offsetExists($offset) {
    return isset($this->container[$this->active][$offset]);
  }
  public function offsetUnset($offset) {
    unset($this->container[$this->active][$offset]);
  }
  public function offsetGet($offset) {
    $value = isset($this->container[$this->active][$offset]) ? $this->container[$this->active][$offset] : null;
    if($this->enable_stat_usage)
    {
      $this->usage_stat_log($offset, $value);
    }
    return $value;
  }


  public function merge($array)
  {
    $this->container[$this->active] = array_merge($this->container[$this->active], $array);
  }


  public function usage_stat_load()
  {
    global $sn_cache;

    $this->stat_usage = $sn_cache->lng_stat_usage  = array(); // TODO for debug
    if(empty($this->stat_usage))
    {
      $query = doquery("SELECT * FROM {{lng_usage_stat}}");
      while($row = mysql_fetch_assoc($query))
      {
        $this->stat_usage[$row['lang_code'] . ':' . $row['string_id'] . ':' . $row['file'] . ':' . $row['line']] = $row['is_empty'];
      }
    }
  }
  public function usage_stat_save()
  {
    if(!empty($this->stat_usage_new))
    {
      global $sn_cache;
      $sn_cache->lng_stat_usage = $this->stat_usage;
      global $link;
      $link = null;
      doquery("SELECT 1 FROM {{lng_usage_stat}} LIMIT 1");
      foreach($this->stat_usage_new as &$value)
      {
        foreach($value as &$value2)
        {
          $value2 = '"' . mysql_real_escape_string($value2) . '"';
        }
        $value = '(' . implode(',', $value) .')';
      }
      doquery("REPLACE INTO {{lng_usage_stat}} (lang_code,string_id,`file`,line,is_empty,locale) VALUES " . implode(',', $this->stat_usage_new));
    }
  }
  public function usage_stat_log(&$offset, &$value)
  {
    $trace = debug_backtrace();
    unset($trace[0]);
    unset($trace[1]['object']);

//    pdump($trace, $offset);
//    pdump(SN_ROOT_PHYSICAL );
    $file = str_replace('\\', '/', substr($trace[1]['file'], strlen(SN_ROOT_PHYSICAL) - 1));
/*
    if(strpos($file, '/includes/classes/template.php') !== false)
    {

    }
*/
//    pdump($file);
    $string_id = $this->active . ':' . $offset . ':' . $file . ':' . $trace[1]['line'];
    if(!isset($this->stat_usage[$string_id]) || $this->stat_usage[$string_id] != $empty)
    {
      $this->stat_usage[$string_id] = empty($value);
      $this->stat_usage_new[] = array(
        'lang_code' => $this->active,
        'string_id' => $offset,
        'file' => $file,
        'line' => $trace[1]['line'],
        'is_empty' => intval(empty($value)),
        'locale' => '' . $value,
      );
    }
    // $this->stat_usage[$this->active . ':' . $offset . ':' . $file . ':' . $trace[1]['line']] = 1;
//    pdump($string_id);
//    die();
  }


  protected function lng_try_filepath($path, $file_path_relative)
  {
    $file_path = SN_ROOT_PHYSICAL . ($path && file_exists(SN_ROOT_PHYSICAL . $path . $file_path_relative) ? $path : '') . $file_path_relative;
    return file_exists($file_path) ? $file_path : false;
  }

  public function lng_include($filename, $path = '', $ext = '.mo.php')
  {
    global $language, $user;

    $lang = $this;

    $ext = $ext ? $ext : '.mo.php';
    $filename_ext = "{$filename}{$ext}";

    $language_fallback = array(
      $language => $language,          // Current language
      $user['lang'] => $user['lang'],  // User language
      DEFAULT_LANG => DEFAULT_LANG,    // Server default language
      'ru' => 'ru',                    // Russian
      'en' => 'en',                    // English
    );

    // $language_tried = array();
    $file_path = '';
    foreach($language_fallback as $lang_try)
    {
      if(!$lang_try /* || isset($language_tried[$lang_try]) */)
      {
        continue;
      }

      if($file_path = $this->lng_try_filepath($path, "language/{$lang_try}/{$filename_ext}"))
      {
        break;
      }

      if($file_path = $this->lng_try_filepath($path, "language/{$filename}_{$lang_try}{$ext}"))
      {
        break;
      }
      /*
          $file_path_relative = "language/{$lang_try}/{$filename_ext}";
          $file_path = SN_ROOT_PHYSICAL . ($path && file_exists(SN_ROOT_PHYSICAL . $path . $file_path_relative) ? $path : '') . $file_path_relative;
          if(file_exists($file_path))
          {
            break;
          }

          $file_path_relative = "language/{$filename_ext}_{$lang_try}";
          $file_path = SN_ROOT_PHYSICAL . ($path && file_exists(SN_ROOT_PHYSICAL . $path . $file_path_relative) ? $path : '') . $file_path_relative;
          if(file_exists($file_path))
          {
            break;
          }
      */
      $file_path = '';
      // $language_tried[$lang_try] = $lang_try;
    }

    if($file_path)
    {
      include($file_path);

      if(!empty($a_lang_array))
      {
        $this->merge($a_lang_array);
        unset($a_lang_array);
      }
    }

    return null;
  }

  public function lng_load_i18n($i18n)
  {
    if(isset($i18n))
    {
      foreach($i18n as $i18n_data)
      {
        if(is_string($i18n_data))
        {
          $this->lng_include($i18n_data);
        }
        elseif(is_array($i18n_data))
        {
          $this->lng_include($i18n_data['file'], $i18n_data['path']);
        }
      }
    }

    return null;
  }

  public function lng_switch($language_new)
  {
    global $language, $user, $sn_mvc;

    $language_new = $language_new ? $language_new : (isset($user['lang']) && $user['lang'] ? $user['lang'] : DEFAULT_LANG);

    $result = false;
    if($language_new != $language)
    {
      $language = $language_new;
      $this->active = $language_new;
      $lang['LANG_INFO'] = $this->lng_get_info($language_new);

      $this->lng_include('system');
      $this->lng_include('tech');
      $this->lng_include('payment');
      // Loading global language files
      $this->lng_load_i18n($sn_mvc['i18n']['']);
      $result = true;
      
    }

    return $result;
  }





  public function lng_get_info($entry)
  {
    $file_name = SN_ROOT_PHYSICAL . 'language/' . $entry . '/language.mo.php';
    $lang_info = array();
    if(file_exists($file_name))
    {
      include($file_name);
    }

    return($lang_info);
  }

  public function lng_get_list()
  {
    if(empty($this->lang_list))
    {
      $this->lang_list = array();

      $path = SN_ROOT_PHYSICAL . 'language/';
      $dir = dir($path);
      while(false !== ($entry = $dir->read()))
      {
        if(is_dir($path . $entry) && $entry[0] != '.')
        {
          $lang_info = $this->lng_get_info($entry);
          if($lang_info['LANG_NAME_ISO2'] == $entry)
          {
            $this->lang_list[$lang_info['LANG_NAME_ISO2']] = $lang_info;
          }
        }
      }
      $dir->close();
    }

    return $this->lang_list;
  }
}
