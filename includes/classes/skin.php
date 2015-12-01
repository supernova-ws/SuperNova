<?php

/**
 * User: Gorlum
 * Date: 23.10.2015
 * Time: 19:54
 */

/*

INI-файл:
  Спецпараметры начинаются с _:
      _inherit - брать отсутствующие картинки из другого скина
  Изображения:
      eisenplanet01 = "planeten/eisenplanet01.jpg" - путь относительно локального скина
      В качестве ID изображения можно указывать путь:
        img/galaxie.gif = "img/galaxie.gif"

Вызов в темплейте
   {I_<id>|парам1|парам2|...} - {I_abort|html}
   {I_<путь к картинке от корня скина>|парам1|парам2|...} - {I_img/e.jpg|html}
   {I_[<имя переменной в темплейте>]} - будет подставлено имя соответствующей переменной в момент выполнения. Поддерживаются:
       - Корневые значения, например {I_[UNIT_ID]}
       - Значения в блоках, например {I_[production.ID]}
       - Корневые значения DEFINE, например {I_[$PLANET_GOVERNOR_ID]}
   Параметры:
      html - отрендерить обрамление HTML-тэгом IMG: <img src="" />
*/

class skin {

  /**
   * Флаг инициализации статического объекта
   *
   * @var bool
   */
  protected static $is_init = false;
  /**
   * Список скинов
   *
   * @var skin[]
   */
  protected static $skin_list = array();
  /**
   * Текущий скин
   *
   * @var null|skin
   */
  protected static $active = null;

  /**
   * HTTP-путь к файлам скина
   *
   * @var string
   */
  protected $root_http = '';
  /**
   * Физический путь к директории скина
   *
   * @var string
   */
  protected $root_folder = '';
  /**
   * Флаг присутсвия конфигурации
   *
   * @var bool
   */
  protected $is_ini_present = false;
  /**
   * Родительский скин
   *
   * @var skin|null
   */
  protected $parent = null;
  /**
   * Конфигурация скина - читается из INI-файла
   *
   * @var array
   */
  protected $config = array();

  protected $image_path_list = array();
  public $name = '';

  /*

  Класс будет хранить инфу о скинах и их наследовании в привязке к темплейту

  Должно быть статик-хранилище, которое будет хранить между экземплярами класса инфу о других скинах - для наследования

  Должен быть метод парсинга конфигурации скина

  Должен быть статик-метод, который будет вызываться из PTL для парсинга I_xxx тэгов

  Иконки перекрываются загрузкой нестандартных иконок, если чо

  Бэкграунд - с ним надо что-то порешать. Например - не использовать. Или тоже перекрывать в CSS
    Типа, сделать пустой скин.цсс для ЭпикБлю, основные цвета прописать в _template.css, а в остальных просто перекрывать

  */

  public function __construct($skin_path = DEFAULT_SKINPATH) {
    strpos($skin_path, 'skins/') !== false ? $skin_path = substr($skin_path, 6) : false;
    strpos($skin_path, '/') !== false ? $skin_path = str_replace('/', '', $skin_path) : false;

    // $this->root_http = SN_ROOT_VIRTUAL . $skin_path;
    $this->root_http = 'skins/' . $skin_path . '/'; // Пока стоит base="" в body SN_ROOT_VIRTUAL - не нужен
    $this->root_folder = SN_ROOT_PHYSICAL . 'skins/' . $skin_path . '/';
    $this->name = $skin_path;
//    pdump($this->root_folder);
    // Искать скин среди пользовательских - когда будет конструктор скинов
    // Может не быть файла конфигурации - тогда используется всё "по дефаулту". Т.е. поданная строка - это именно имя файла

    $this->is_ini_present = false;
    // Проверка на корректность и существование пути
    if(file_exists($this->root_folder . 'skin.ini')) {
      // Пытаемся распарсить файл

      // По секциям? images и config? Что бы не копировать конфигурацию? Или просто unset(__inherit) а затем заново записать
      $this->config = parse_ini_file($this->root_folder . 'skin.ini');
      if(!empty($this->config)) {

        $this->is_ini_present = true;

        if(!empty($this->config['_inherit'])) {
          if(empty(static::$skin_list[$this->config['_inherit']])) {
            static::$skin_list[$this->config['_inherit']] = new skin($this->config['_inherit']);
          }
          $this->parent = static::$skin_list[$this->config['_inherit']];
        }
      } else {
        $this->config = array();
      }

      // Проверка на _inherit


//      pdump($this->config);
    }

    return $this;
  }

  /**
   *
   */
  public static function init() {
    global $user;
    // Читаем конфиг и парсим
    // Берем текущий скин
    $skin_path = !empty($user['dpath']) ? $user['dpath'] : DEFAULT_SKINPATH;
    strpos($skin_path, 'skins/') !== false ? $skin_path = substr($skin_path, 6) : false;
    strpos($skin_path, '/') !== false ? $skin_path = str_replace('/', '', $skin_path) : false;

    // Загружены ли уже данные по текущему скину?
//pdump(static::$skin_list[$ini_path], 'static');
    if(empty(static::$skin_list[$skin_path])) {
      // Прогружаем текущий скин
      static::$skin_list[$skin_path] = new skin($skin_path);
      static::$active = static::$skin_list[$skin_path];
    }

//    'dpath'         => $user['dpath'] ? $user['dpath'] : DEFAULT_SKINPATH,
// В $user['dpath'] 'skins/xnova/'
//    {D_SN_ROOT_VIRTUAL}{dpath}skin.css?{C_var_db_update}

    // Ресолвим инхериты - нужен кэш для объектов skin
    // Инхериты парсятся рекурсивным вызовом конструктора для кэша объектов skin
    // Перекрываем наше ихним

    static::$is_init = true;
  }

  protected function resolve_ptl_tags(&$image_name, $template) {
    // Есть переменные из темплейта ?
    if(strpos($image_name, '[') !== false) {
      preg_match_all('#(\[.+?\])#', $image_name, $matches);
      foreach($matches[0] as &$match) {
        $var_name = str_replace(array('[', ']'), '', $match);
        if(strpos($var_name, '.') !== false) {
          // Вложенная переменная темплейта
          list($block_name, $block_var) = explode('.', $var_name);
          isset($template->_block_value[$block_name][$block_var]) ? $image_name = str_replace($match, $template->_block_value[$block_name][$block_var], $image_name) : false;
        } elseif(strpos($var_name, '$') !== false) {
          // Корневой DEFINE
          $define_name = substr($var_name, 1);
          isset($template->_tpldata['DEFINE']['.'][$define_name]) ? $image_name = str_replace($match, $template->_tpldata['DEFINE']['.'][$define_name], $image_name) : false;
        } else {
          // Корневая переменная темплейта
          isset($template->_rootref[$var_name]) ? $image_name = str_replace($match, $template->_rootref[$var_name], $image_name) : false;
        }
      }
    }
  }

  /**
   * @param string $image_tag
   * @param template $template
   *
   * @return string
   */
  public function compile_image($image_tag, $template) {
    // Если у нас валидный имеджтаг и есть для него путь - просто возвращаем путь
    if(!empty($this->image_path_list[$image_tag])) {
      return $this->image_path_list[$image_tag];
    }
    $parse = explode('|', $image_tag);
    $image_name = $parse[0];

    if(!empty($this->image_path_list[$image_name])) {
      // TODO - неверно! Игнорируются параметры через |
      return $this->image_path_list[$image_name];
    }

    // Ресолвим текущие значения темплейта - их названия в квадратных скобочках типа [ID] или даже [production.ID]
    $this->resolve_ptl_tags($image_name, $template);
    if(!empty($this->image_path_list[$image_name])) {
      return $this->image_path_list[$image_name];
    }

    // Здесь у нас $image_name уже хранит прямой ключ для картинки - без всяких переменных

    // Теперь парсим конфигурацию
    if(empty($this->config[$image_name])) {
      // Нет пути для данного изображения

      // Фоллбэк на родителя если - он есть
      if($this->parent) {
        $this->image_path_list[$image_name] = $this->parent->compile_image($image_name, $template);
        if(!empty($this->image_path_list[$image_name])) {
          return $this->image_path_list[$image_name];
        }
      }

      // Все еще не найдена картинка ни в одном из скинов - тогда используем переданное имя как относительный путь к картинке
      $this->image_path_list[$image_name] = $this->root_http . $image_name . (strpos($image_name, '.') === false ? DEFAULT_PICTURE_EXTENSION : '');
    } else {
      // Есть путь для данного изображения
      $temp_image_path = $this->config[$image_name];

      // Если первый символ пути '/' - значит это путь от HTTP-корня. Откусываем его и пользуем остальное
      $this->image_path_list[$image_name] = (strpos($temp_image_path, '/') === 0 ? SN_ROOT_VIRTUAL . substr($temp_image_path, 1) : ($this->root_http . $temp_image_path));

//      $this->image_path_list[$image_name] = $this->root_http . $this->config[$image_name];
    }

    $image_url = $this->image_path_list[$image_name];

    if(in_array('html', $parse)) {
      $image_url = '<img src="' . $image_url . '" />';
    }

    return $image_url;
  }

  /**
   * Статический враппер для инициализации статики и текущего скина
   *
   * @param string $image_tag
   *
   *
   *
   * @return string
   */
  public static function image_url($image_tag, $template) {

    // Инициализируем текущий скин
    !static::$is_init ? static::init() : false;

    // Тут идёт собственно парсинг того, что нам дали на фходе

    // Тут делать фоллбэк? Или при ините?


//    return static::$active->compile_image($image_tag, $template) . '-image_url';
    return static::$active->compile_image($image_tag, $template);
  }

}
