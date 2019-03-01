<?php

class classLocale implements ArrayAccess {
  public $container = array();
  public $lang_list = null;
  public $active = null;

  public $enable_stat_usage = false;
  protected $stat_usage = array();
  protected $stat_usage_new = array();

  /**
   * Порядок проверки языков
   *
   * @var array $fallback
   */
  protected $fallback = array();

  /**
   * @var classCache $cache
   */
  protected $cache = null;
  protected $cache_prefix = 'lng_';
  protected $cache_prefix_lang = '';

  public function __construct($enable_stat_usage = false) {
    SN::log_file('locale.__constructor: Starting', 1);

    $this->container = array();

    if (SN::$cache->getMode() != classCache::CACHER_NO_CACHE && !SN::$config->locale_cache_disable) {
      $this->cache = SN::$cache;
      SN::log_file('locale.__constructor: Cache is present');
//$this->cache->unset_by_prefix($this->cache_prefix); // TODO - remove? 'cause debug!
    }

    if ($enable_stat_usage && empty($this->stat_usage)) {
      $this->enable_stat_usage = $enable_stat_usage;
      $this->usage_stat_load();
      // TODO shutdown function
      register_shutdown_function(array($this, 'usage_stat_save'));
    }

    SN::log_file("locale.__constructor: Switching language to default");
    $this->lng_switch(DEFAULT_LANG);

    SN::log_file("locale.__constructor: Complete - EXIT", -1);
  }

  /**
   * Фоллбэк для строки на другие локали
   *
   * @param array|string $offset
   */
  protected function locale_string_fallback($offset) {
    global $locale_cache_statistic;
    // Фоллбэк вызывается только если мы не нашли нужную строку в массиве...
    $fallback = $this->fallback;
    // ...поэтому $offset в активном языке заведомо нет
    unset($fallback[$this->active]);

    // Проходим по оставшимся локалям
    foreach ($fallback as $try_language) {
      // Если нет такой строки - пытаемся вытащить из кэша
      if (!isset($this->container[$try_language][$offset]) && $this->cache) {
        $this->container[$try_language][$offset] = $this->cache->__get($this->cache_prefix . $try_language . '_' . $offset);
        // Записываем результат работы кэша
        $locale_cache_statistic['queries']++;
        isset($this->container[$try_language][$offset]) ? $locale_cache_statistic['hits']++ : $locale_cache_statistic['misses']++;
        !isset($this->container[$try_language][$offset]) ? $locale_cache_statistic['missed_str'][] = $this->cache_prefix . $try_language . '_' . $offset : false;
      }

      // Если мы как-то где-то нашли строку...
      if (isset($this->container[$try_language][$offset])) {
        // ...значит она получена в результате фоллбэка и записываем её в кэш и контейнер
        $this[$offset] = $this->container[$try_language][$offset];
        $locale_cache_statistic['fallbacks']++;
        break;
      }
    }
  }

  public function offsetSet($offset, $value) {
    if (is_null($offset)) {
      $this->container[$this->active][] = $value;
    } else {
      $this->container[$this->active][$offset] = $value;
      if ($this->cache) {
        $this->cache->__set($this->cache_prefix_lang . $offset, $value);
      }
    }
  }

  public function offsetExists($offset) {
    // Шорткат если у нас уже есть строка в памяти PHP
    if (!isset($this->container[$this->active][$offset])) {
      if (!$this->cache || !($this->container[$this->active][$offset] = $this->cache->__get($this->cache_prefix_lang . $offset))) {
        // Если нету такой строки - делаем фоллбэк
        $this->locale_string_fallback($offset);
      }

      return isset($this->container[$this->active][$offset]);
    } else {
      return true;
    }
  }

  public function offsetUnset($offset) {
    unset($this->container[$this->active][$offset]);
  }

  public function offsetGet($offset) {
    $value = $this->offsetExists($offset) ? $this->container[$this->active][$offset] : null;
    if ($this->enable_stat_usage) {
      $this->usage_stat_log($offset, $value);
    }

    return $value;
  }


  public function merge($array) {
    $this->container[$this->active] = is_array($this->container[$this->active]) ? $this->container[$this->active] : array();
    // $this->container[$this->active] = array_merge($this->container[$this->active], $array);
    $this->container[$this->active] = array_replace_recursive($this->container[$this->active], $array);
  }


  public function usage_stat_load() {
    global $sn_cache;

    $this->stat_usage = $sn_cache->lng_stat_usage = array(); // TODO for debug
    if (empty($this->stat_usage)) {
      $query = doquery("SELECT * FROM {{lng_usage_stat}}");
      while ($row = db_fetch($query)) {
        $this->stat_usage[$row['lang_code'] . ':' . $row['string_id'] . ':' . $row['file'] . ':' . $row['line']] = $row['is_empty'];
      }
    }
  }

  public function usage_stat_save() {
    if (!empty($this->stat_usage_new)) {
      global $sn_cache;
      $sn_cache->lng_stat_usage = $this->stat_usage;
      doquery("SELECT 1 FROM {{lng_usage_stat}} LIMIT 1");
      foreach ($this->stat_usage_new as &$value) {
        foreach ($value as &$value2) {
          $value2 = '"' . db_escape($value2) . '"';
        }
        $value = '(' . implode(',', $value) . ')';
      }
      doquery("REPLACE INTO {{lng_usage_stat}} (lang_code,string_id,`file`,line,is_empty,locale) VALUES " . implode(',', $this->stat_usage_new));
    }
  }

  public function usage_stat_log(&$offset, &$value) {
    $trace = debug_backtrace();
    unset($trace[0]);
    unset($trace[1]['object']);

    $file = str_replace('\\', '/', substr($trace[1]['file'], strlen(SN_ROOT_PHYSICAL) - 1));

    $string_id = $this->active . ':' . $offset . ':' . $file . ':' . $trace[1]['line'];
    if (!isset($this->stat_usage[$string_id]) || $this->stat_usage[$string_id] != $empty) {
      $this->stat_usage[$string_id] = empty($value);
      $this->stat_usage_new[]       = array(
        'lang_code' => $this->active,
        'string_id' => $offset,
        'file'      => $file,
        'line'      => $trace[1]['line'],
        'is_empty'  => intval(empty($value)),
        'locale'    => '' . $value,
      );
    }
  }


  protected function lng_try_filepath($path, $file_path_relative) {
    $file_path = SN_ROOT_PHYSICAL . ($path && file_exists(SN_ROOT_PHYSICAL . $path . $file_path_relative) ? $path : '') . $file_path_relative;

    return file_exists($file_path) ? $file_path : false;
  }

  protected function make_fallback($language = '') {
    global $user;

    $this->fallback = array();
    $language ? $this->fallback[$language] = $language : false; // Desired language
    $this->active ? $this->fallback[$this->active] = $this->active : false; // Active language
    // TODO - account_language
    !empty($user['lang']) ? $this->fallback[$user['lang']] = $user['lang'] : false; // Player language
    $this->fallback[DEFAULT_LANG] = DEFAULT_LANG; // Server default language
    $this->fallback['ru']         = 'ru'; // Russian
    $this->fallback['en']         = 'en'; // English
  }

  public function lng_include($filename, $path = '', $ext = '.mo.php') {
    global $language;

    SN::log_file("locale.include: Loading data from domain '{$filename}'", 1);

    $cache_file_key = $this->cache_prefix_lang . '__' . $filename;

    // Подключен ли внешний кэш?
    if ($this->cache) {
      // Загружен ли уже данный файл?
      $cache_file_status = $this->cache->__get($cache_file_key);
      SN::log_file("locale.include: Cache - '{$filename}' has key '{$cache_file_key}' and is " . ($cache_file_status ? 'already loaded - EXIT' : 'EMPTY'), $cache_file_status ? -1 : 0);
      if ($cache_file_status) {
        // Если да - повторять загрузку нет смысла
        return null;
      }
    }

    // У нас нет внешнего кэша или в кэш не загружена данная локализация текущего файла

    $ext          = $ext ? $ext : '.mo.php';
    $filename_ext = "{$filename}{$ext}";

    $this->make_fallback($language);

    $file_path = '';
    foreach ($this->fallback as $lang_try) {
      if (!$lang_try /* || isset($language_tried[$lang_try]) */) {
        continue;
      }

      if ($file_path = $this->lng_try_filepath($path, "language/{$lang_try}/{$filename_ext}")) {
        break;
      }

      if ($file_path = $this->lng_try_filepath($path, "language/{$filename}_{$lang_try}{$ext}")) {
        break;
      }

      $file_path = '';
    }

    if ($file_path) {
      include($file_path);

      if (!empty($a_lang_array)) {
        $this->merge($a_lang_array);

        // Загрузка данных из файла в кэш
        if ($this->cache) {
          SN::log_file("Locale: loading '{$filename}' into cache");
          foreach ($a_lang_array as $key => $value) {
            $value_cache_key = $this->cache_prefix_lang . $key;
            if ($this->cache->__isset($value_cache_key)) {
              if (is_array($value)) {
                $alt_value = $this->cache->__get($value_cache_key);
                $value     = array_replace_recursive($alt_value, $value);
              }
            }
            $this->cache->__set($this->cache_prefix_lang . $key, $value);
          }
        }
      }

      if ($this->cache) {
        $this->cache->__set($cache_file_key, true);
      }

      unset($a_lang_array);
    }

    SN::log_file("locale.include: Complete - EXIT", -1);

    return null;
  }

  public function lng_load_i18n($i18n) {
    if (!isset($i18n)) {
      return;
    }

    foreach ($i18n as $i18n_data) {
      if (is_string($i18n_data)) {
        $this->lng_include($i18n_data);
      } elseif (is_array($i18n_data)) {
        $this->lng_include($i18n_data['file'], $i18n_data['path']);
      }
    }

    return null;
  }

  public function lng_switch($language_new) {
    global $language, $user, $sn_mvc;

    SN::log_file("locale.switch: Request for switch to '{$language_new}'", 1);

    $language_new = str_replace(array('?', '&', 'lang='), '', $language_new);
    $language_new = $language_new ? $language_new : (!empty($user['lang']) ? $user['lang'] : DEFAULT_LANG);

    SN::log_file("locale.switch: Trying to switch language to '{$language_new}'");

    if ($language_new == $this->active) {
      SN::log_file("locale.switch: New language '{$language_new}' is equal to current language '{$this->active}' - EXIT", -1);

      return false;
    }

    $this->active            = $language = $language_new;
    $this->cache_prefix_lang = $this->cache_prefix . $this->active . '_';

    $this['LANG_INFO'] = $this->lng_get_info($this->active);
    $this->make_fallback($this->active);

    if ($this->cache) {
      $cache_lang_init_status = $this->cache->__get($this->cache_prefix_lang . '__INIT');
      SN::log_file("locale.switch: Cache for '{$this->active}' prefixed '{$this->cache_prefix_lang}' is " . ($cache_lang_init_status ? 'already loaded. Doing nothing - EXIT' : 'EMPTY'), $cache_lang_init_status ? -1 : 0);
      if ($cache_lang_init_status) {
        return false;
      }

      // Чистим текущие локализации из кэша. Достаточно почистить только флаги инициализации языкового кэша и загрузки файлов - они начинаются с '__'
      SN::log_file("locale.switch: Cache - invalidating data");
      $this->cache->unset_by_prefix($this->cache_prefix_lang . '__');
    }

    $this->lng_include('system');
//    $this->lng_include('menu');
    $this->lng_include('tech');
    $this->lng_include('payment');
    // Loading global language files
    $this->lng_load_i18n($sn_mvc['i18n']['']);

    if ($this->cache) {
      SN::log_file("locale.switch: Cache - setting flag " . $this->cache_prefix_lang . '__INIT');
      $this->cache->__set($this->cache_prefix_lang . '__INIT', true);
    }

    SN::log_file("locale.switch: Complete - EXIT");

    return true;
  }


  public function lng_get_info($entry) {
    $file_name = SN_ROOT_PHYSICAL . 'language/' . $entry . '/language.mo.php';
    $lang_info = array();
    if (file_exists($file_name)) {
      include($file_name);
    }

    return ($lang_info);
  }

  public function lng_get_list() {
    if (empty($this->lang_list)) {
      $this->lang_list = array();

      $path = SN_ROOT_PHYSICAL . 'language/';
      $dir  = dir($path);
      while (false !== ($entry = $dir->read())) {
        if (is_dir($path . $entry) && $entry[0] != '.') {
          $lang_info = $this->lng_get_info($entry);
          if ($lang_info['LANG_NAME_ISO2'] == $entry) {
            $this->lang_list[$lang_info['LANG_NAME_ISO2']] = $lang_info;
          }
        }
      }
      $dir->close();
    }

    return $this->lang_list;
  }
}
