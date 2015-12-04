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
   Параметры вывода:
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
   * TODO Переделать под контейнер
   *
   * @var skin[] $skin_list
   */
  protected static $skin_list = array();
  /**
   * Текущий скин
   *
   * @var null|skin
   */
  protected static $active = null;

  /**
   * HTTP-путь к файлам скина относительно корня движка
   *
   * @var string
   */
  protected $root_http_relative = '';
  /**
   * Абсолютный физический путь к директории скина
   *
   * @var string
   */
  protected $root_physical_absolute = '';
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

  /**
   * Сортированный список поддерживаемых параметров
   *
   * @var string[] $params
   */
//  protected $params = array('html', 'test', 'skin');
  protected $params = array('html');

  /**
   * Список полностью отрендеренных путей
   *
   * @var string[]
   */
  protected $image_path_list = array();

  /**
   * Название скина
   *
   * @var mixed|null|string
   */
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

    $this->root_http_relative = 'skins/' . $skin_path . '/'; // Пока стоит base="" в body SN_ROOT_VIRTUAL - не нужен
//    $this->root_http = SN_ROOT_VIRTUAL . 'skins/' . $skin_path . '/'; // Всегда абсолютный путь от корня сайта
    $this->root_physical_absolute = SN_ROOT_PHYSICAL . 'skins/' . $skin_path . '/';
    $this->name = $skin_path;
//    pdump($this->root_folder);
    // Искать скин среди пользовательских - когда будет конструктор скинов
    // Может не быть файла конфигурации - тогда используется всё "по дефаулту". Т.е. поданная строка - это именно имя файла

    $this->is_ini_present = false;
    // Проверка на корректность и существование пути
//    if(file_exists($this->root_physical_absolute . 'skin.ini')) {
    if(is_file($this->root_physical_absolute . 'skin.ini')) {
      // Пытаемся распарсить файл

      // По секциям? images и config? Что бы не копировать конфигурацию? Или просто unset(__inherit) а затем заново записать
      $this->config = parse_ini_file($this->root_physical_absolute . 'skin.ini');
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

  /**
   * @param string   $image_tag
   * @param template $template
   *
   * @return string
   */
  protected function resolve_PTL_tags($image_tag, $template) {
    // Есть переменные из темплейта ?
    if(strpos($image_tag, '[') === false) {
      // Что бы лишний раз не запускать регексп
      return $image_tag;
    }

    // TODO - многоуровневые вложения ?! Надо ли и где их можно применить

    preg_match_all('#(\[.+?\])#', $image_tag, $matches);
    foreach($matches[0] as &$match) {
      $var_name = str_replace(array('[', ']'), '', $match);
      if(strpos($var_name, '.') !== false) {
        // Вложенная переменная темплейта - на текущем уровне
        // TODO Вложенная переменная из корня через "!"
        list($block_name, $block_var) = explode('.', $var_name);
        isset($template->_block_value[$block_name][$block_var]) ? $image_tag = str_replace($match, $template->_block_value[$block_name][$block_var], $image_tag) : false;
      } elseif(strpos($var_name, '$') !== false) {
        // Корневой DEFINE
        $define_name = substr($var_name, 1);
        isset($template->_tpldata['DEFINE']['.'][$define_name]) ? $image_tag = str_replace($match, $template->_tpldata['DEFINE']['.'][$define_name], $image_tag) : false;
      } else {
        // Корневая переменная темплейта
        isset($template->_rootref[$var_name]) ? $image_tag = str_replace($match, $template->_rootref[$var_name], $image_tag) : false;
      }
    }

    return $image_tag;
  }

  protected function apply_params($ini_image_id_plain, $params) {
    $image_tag = $ini_image_id_plain;
    $image_string = $this->image_path_list[$image_tag];

    // Нет параметров - просто возвращаем значение по $image_name из контейнера
    if(!empty($params) && is_array($params)) {
      // Здесь автоматически произойдёт упорядочивание параметров

      // Параметр 'html' - выводить изображение в виде HTML-тэга
      if(in_array('html', $params)) {
        $image_tag = $image_tag . '|html';
        $image_string = '<img src="' . $image_string . '" />';
        $this->image_path_list[$image_tag] = $image_string;
      }
    }

    return $image_string;
  }

  /**
   * Переупорядочивает параметры в определенном порядке
   *
   * Параметры не транзитивны, а их порядок может влиять на вывод - чисто теоретически
   *
   * @param string[] $params
   *
   * @return string[]
   */
  protected function reorder_params($params) {
    static $parms_order = array('html', 'test', 'skin');

    // Быстро и грубо. Если будут более сложные параметры - надо будет переделать
    return array_intersect($parms_order, $params);
  }

  /**
   * Возвращает строку для вывода в компилированном темплейте PTL
   *
   * @param string   $image_tag
   * @param template $template
   *
   * @return string
   */
  public function compile_image($image_tag, $template) {
//    // Если у нас есть скомпилированная строка для данного тэга - возвращаем строку. Больше ничего делать не надо
//    if(!empty($this->image_path_list[$image_tag])) {
//      return $this->image_path_list[$image_tag];
//    }

    $ini_image_path = ''; // Путь к файлу из INI-файла скина
    $path_http_absolute = ''; // Полный HTTP-путь к файлу
    $image_string = ''; // Строка, которая будт сохранена в кэше

    // Ресолвим PTL-тэги в $image_tag - получаем Resolved Image Tag (RIT)
    $ini_image_id_with_params = $this->resolve_PTL_tags($image_tag, $template); // Ресолвим текущие значения темплейта - их названия в квадратных скобочках типа [ID] или даже [production.ID]

    // RIT содержит ini_image_id с параметрами

    // Проверяем наличие ключа для RIT в хранилище
    // В нём заведомо не может существовать отсутствующих файлов по построению
    if(!empty($this->image_path_list[$ini_image_id_with_params])) {
      return $this->image_path_list[$ini_image_id_with_params];
    }

    // TODO - Проверка на параметры в тэге - есть ли они у нас вообще. Если нету - это просто image_name

    // Нет ключа RIT в хранилище - достаем ID изображения
    $params = explode('|', $ini_image_id_with_params);
    $ini_image_id_plain = $params[0];
    unset($params[0]);
    $params = $this->reorder_params($params); // TODO - переупорядочить порядок параметров - параметры не транзитивны

    if(!empty($this->image_path_list[$ini_image_id_plain])) {
      // Есть такой ключ - значит у нас есть абсолютный HTTP-путь к файлу и файл существует, ведь все проверки осуществлялись раньше ниже по коду
//      $path_http_absolute = $this->image_path_list[$ini_image_id_plain];
      return $this->apply_params($ini_image_id_plain, $params);
    }

    // На текущий момент мы убедились в отсутствии в контейнере строк записей с ключами $ini_image_id_with_params и $ini_image_id_plain
    // Пора переходить к обсчёту конфигурации

    // Теперь парсим конфигурацию
    if(!empty($this->config[$ini_image_id_plain])) {
      // Есть путь для данного изображения
      $ini_file_path = $this->config[$ini_image_id_plain];

      $image_file_relative_path =
        // Если первый символ пути '/' - значит это путь от HTTP-корня
        strpos($ini_file_path, '/') !== 0
          // Откусываем его и пользуем остальное
          ? $this->root_http_relative . $ini_file_path
          : substr($ini_file_path, 1);

//      if(file_exists(SN_ROOT_PHYSICAL . $image_file_realtive_path)) {
      if(is_file(SN_ROOT_PHYSICAL . $image_file_relative_path)) {
        $this->image_path_list[$ini_image_id_plain] = SN_ROOT_VIRTUAL . $image_file_relative_path;
        return $this->apply_params($ini_image_id_plain, $params);
      }
//if(!$file_exists) {
//pdump(SN_ROOT_PHYSICAL . $this->root_http_relative . $temp_image_path, 'file not found RELATIVE' . ($is_path_from_sn_root ? 'root' : 'relative'));
//}
    }

    // Мы проверили теперь и конфигурацию. Однако и в ней не нашлось ID изображения

    // Может это - просто путь к файлу?
    $ini_file_path = $ini_image_id_plain;

    $image_file_relative_path =
      // Если первый символ пути '/' - значит это путь от HTTP-корня
      strpos($ini_file_path, '/') !== 0
        // Откусываем его и пользуем остальное
        ? $this->root_http_relative . $ini_file_path
        : substr($ini_file_path, 1);

//    if(file_exists(SN_ROOT_PHYSICAL . $image_file_realtive_path)) {
    if(is_file(SN_ROOT_PHYSICAL . $image_file_relative_path)) {
      $this->image_path_list[$ini_image_id_plain] = SN_ROOT_VIRTUAL . $image_file_relative_path;
      return $this->apply_params($ini_image_id_plain, $params);
    }

    $ini_file_path = '';
    $image_file_relative_path = '';


    // Нет - image_id не является путём к файлу
    // Пора обратиться к предкам...

    // Фоллбэк на родителя если - он есть
    if($this->parent) {
      // Пытаемся вытащить путь из родителя и применить к нему свои параметры - мало ли что там делает с путём родитель и как преобразовывает его в строку?
      // Так что тащим по ID изображения, а не по ТЭГУ
      $parent_image_file_http_full_path = $this->parent->compile_image($ini_image_id_plain, $template);
      // $this->image_path_list[$ini_image_id_plain] =

      // Если у родителя нет картинки - он вернет заглушку
      if(!empty($parent_image_file_http_full_path)) {
        $this->image_path_list[$ini_image_id_plain] = $parent_image_file_http_full_path;
        return $this->apply_params($ini_image_id_plain, $params);
      }
    }

    // Упс! А я и есть родитель... Возвращаем заглушку. У меня-то она всегда есть...
    $ini_image_id_plain = '_no_image';
    $ini_file_path = $this->config[$ini_image_id_plain];

    $image_file_relative_path =
      // Если первый символ пути '/' - значит это путь от HTTP-корня
      strpos($ini_file_path, '/') !== 0
        // Откусываем его и пользуем остальное
        ? $this->root_http_relative . $ini_file_path
        : substr($ini_file_path, 1);

//    if(file_exists(SN_ROOT_PHYSICAL . $image_file_realtive_path) && is_file(file_exists(SN_ROOT_PHYSICAL . $image_file_realtive_path))) {
    if(!is_file(SN_ROOT_PHYSICAL . $image_file_relative_path)) {
      // А если нет - то тогда совсем стандартную заглушку
      $image_file_relative_path = 'design/images/_no_image.png';
    }
    $this->image_path_list[$ini_image_id_plain] = SN_ROOT_VIRTUAL . $image_file_relative_path;
    return $this->apply_params($ini_image_id_plain, $params);
//
//
//
//
//
//
//
//
//
//
//    // Все еще не найдена картинка ни в одном из скинов - тогда используем переданное имя как относительный путь к картинке
//    // TODO - ЭТО НАДО ДЕЛАТЬ НЕ ЗДЕСЬ! Иначе будет возвращаться и из парента его ссылка - при том, что в скине может лежать файл прямо!
//    $this->image_path_list[$image_name] = SN_ROOT_VIRTUAL . $this->root_http_relative . $image_name . (strpos($image_name, '.') === false ? DEFAULT_PICTURE_EXTENSION_DOTTED : '');
//
//
//
//
//
//
//
//
//
//
//
//    // Есть путь - проверяем наличие файла
//    if(!empty($ini_image_path)) {
//      $path_http_absolute =
//        // Если первый символ пути '/' - значит это путь от HTTP-корня
//        strpos($ini_image_path, '/') !== 0
//          // Откусываем его и пользуем остальное
//          ? $this->root_http_relative . $ini_image_path
//          : substr($ini_image_path, 1);
//
//      if(file_exists(SN_ROOT_PHYSICAL . $path_http_absolute)) {
//        $this->image_path_list[$ini_image_id_plain] = SN_ROOT_VIRTUAL . $path_http_absolute;
//      } else {
//        $path_http_absolute = '';
//      }
//
//    }
//
//
//
//
//
//
//    if(!empty($ini_image_id_with_params)) {
//      $this->image_path_list[$ini_image_id_with_params] = $ini_image_id_with_params;
//      return $this->image_path_list[$image_tag];
//    }
//    // TODO - Если нет такого тэга - пробуем узнать у парента. В случае наличия простой строки у парента - тот проделает все нужные манипуляции с параметрами и вернет нам уже готовую строку
//
//
//        // Есть файл - применяем параметры RIT, сохраняем результат для дальнейшей работы и возвращаем его
//        // Нет файла - берем по тэгу данные из предка
//          // Есть данные - сохраняем данные по тэгу и возвращаем их
//          // Нет данных - значит это у нас просто путь. Генерируем путь, записываем в контейнер и возвращаем
//      // Нет пути - значит это у нас просто путь.
//
//    $params = explode('|', $image_tag);
//    $image_name = $params[0];
//    // Здесь у нас $image_name уже хранит прямой ключ для картинки - без всяких переменных
//
//    // Уже есть прямой путь для данной картинки - возвращаем его
//    if(!empty($this->image_path_list[$image_name])) {
//      $this->apply_params($image_name, $this->image_path_list[$image_name], $params);
//      // TODO - неверно! Игнорируются параметры через |
//      return $this->image_path_list[$image_name];
//    }
//
//    // Нет прямого пути
//    // Ресолвим текущие значения темплейта - их названия в квадратных скобочках типа [ID] или даже [production.ID]
////    $image_name_PTL_resolved = $this->resolve_PTL_tags($image_name, $template);
////    if(!empty($image_name_PTL_resolved)) {
////      $this->image_path_list[$image_name] = $image_name_PTL_resolved;
////      return $this->image_path_list[$image_name];
////    }
//
//    // Теперь парсим конфигурацию
//    if(!empty($this->config[$image_name])) {
//      // Есть путь для данного изображения
//      $temp_image_path = $this->config[$image_name];
//
////      $temp_image_path =
////        // Если первый символ пути '/' - значит это путь от HTTP-корня
////        strpos($temp_image_path, '/') === 0
////          // Откусываем его и пользуем остальное
////          ? SN_ROOT_VIRTUAL . substr($temp_image_path, 1)
////          : ($this->root_http_relative . $temp_image_path);
//
////      $is_path_from_sn_root = strpos($temp_image_path, '/') === 0;
////      if($is_path_from_sn_root) {
////        $file_exists = file_exists(SN_ROOT_PHYSICAL . substr($temp_image_path, 1));
////        if($file_exists) {
////          $temp_image_path = SN_ROOT_VIRTUAL . substr($temp_image_path, 1);
////        }
////      } else {
////        $file_exists = file_exists(SN_ROOT_PHYSICAL . $this->root_http_relative . $temp_image_path);
////        if($file_exists) {
////          $temp_image_path = SN_ROOT_VIRTUAL . $this->root_http_relative . $temp_image_path;
////        }
////      }
////
////      if($file_exists) {
////        $this->image_path_list[$image_name] = $temp_image_path;
////      }
//
//      $temp_image_path =
//        // Если первый символ пути '/' - значит это путь от HTTP-корня
//        strpos($temp_image_path, '/') !== 0
//          // Откусываем его и пользуем остальное
//          ? $this->root_http_relative . $temp_image_path
//          : substr($temp_image_path, 1);
//
//      if(file_exists(SN_ROOT_PHYSICAL . $temp_image_path)) {
//        $this->image_path_list[$image_name] = SN_ROOT_VIRTUAL . $temp_image_path;
//      }
////if(!$file_exists) {
////pdump(SN_ROOT_PHYSICAL . $this->root_http_relative . $temp_image_path, 'file not found RELATIVE' . ($is_path_from_sn_root ? 'root' : 'relative'));
////}
//    }
//
//    if(empty($this->image_path_list[$image_name])) {
//      // Нет пути для данного изображения
//
//      // Фоллбэк на родителя если - он есть
//      if($this->parent) {
//        // TODO - Неправильно! $image_tag на самом деле
//        $this->image_path_list[$image_name] = $this->parent->compile_image($image_name, $template);
//
//        if(!empty($this->image_path_list[$image_name])) {
//          return $this->image_path_list[$image_name];
//        }
//      }
//
//      // Все еще не найдена картинка ни в одном из скинов - тогда используем переданное имя как относительный путь к картинке
//      $this->image_path_list[$image_name] = SN_ROOT_VIRTUAL . $this->root_http_relative . $image_name . (strpos($image_name, '.') === false ? DEFAULT_PICTURE_EXTENSION_DOTTED : '');
//    }
//
//    $image_url = $this->image_path_list[$image_name];
//
//    // Параметр 'html' - выводить изображение в виде HTML-тэга
//    if(in_array('html', $params)) {
//      $image_url = '<img src="' . $image_url . '" />';
//    }
//
//    return $image_url;
  }

  /**
   * Статический враппер для инициализации статики и текущего скина
   *
   * @param string $image_tag
   *
   * @return string
   */
  public static function image_url($image_tag, $template) {
    // Инициализируем текущий скин
    !static::$is_init ? static::init() : false;

    return static::$active->compile_image($image_tag, $template);
  }

}
