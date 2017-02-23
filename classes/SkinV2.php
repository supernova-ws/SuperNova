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

/**
 * Класс skin отвечает за работу скинов. В настоящее время - за маппинг {I_xxx} тэгов в HTTP-путь к файлу с картинкой
 *
 * Возможности:
 * - Поддержка конфигурации в файле skin.ini
 * - Работа через PTL тэги {I_xxx}
 * - Поддержка опций рендеринга через {I_xxx|param...}
 * - Поддержка абсолютных и относительных путей в skin.ini (абсоютный путь начинается с '/' - '/design/images/_no_image.png')
 *    - Относительные пути ресолвятся относительно корня скина - т.е. папки, где лежит skin.ini
 * - Подстановка значений переменных из класса template через {I_xxx[yyy]}:
 *    - Глобальные переменные - {I_xxx[UNIT_ID]}
 *    - Назначенные переменные - {I_xxx[$UNIT_ID]}
 *    - Переменные в блоках - {I_xxx[block.VAR]}
 * - Возможность указать в image-tag прямой путь - {I_/design/images/_no_image.png} - как абсолютный так и относительный
 * - Наследование скинов любой глубины вложенности (опция _inherit в skin.ini)
 * - Подстановка картинок из родителя при отсутствии данных в skin.ini или физическом отутствии файла
 * - Заглушка _NO_IMAGE при отсутствии картинки (опция _no_image в skin.ini)
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
   * @var string[] $params_order
   */
  protected $params_order = array('html'); // , 'test', 'skin'
  /**
   * Список полностью отрендеренных путей
   *
   * @var string[] $container
   */
  protected $container = array();
  /**
   * Название скина
   *
   * @var string $name
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

  /**
   * Точка входа
   *
   * @param string   $image_tag
   * @param template $template
   *
   * @return string
   */
  public static function image_url($image_tag, $template) {
    // Инициализируем текущий скин
    !static::$is_init ? static::init() : false;

    return static::$active->compile_image($image_tag, $template);
  }

  /**
   * Инициализация харнилища скинов
   */
  protected static function init() {
    if(static::$is_init) {
      return;
    }

    classSupernova::$gc->skinModel->init();

    global $user;
    // Читаем конфиг и парсим
    // Берем текущий скин
    $skin_path = !empty($user['dpath']) ? $user['dpath'] : DEFAULT_SKINPATH;
    strpos($skin_path, 'skins/') !== false ? $skin_path = substr($skin_path, 6) : false;
    strpos($skin_path, '/') !== false ? $skin_path = str_replace('/', '', $skin_path) : false;

    // Загружены ли уже данные по текущему скину?
    if(empty(static::$skin_list[$skin_path])) {
      // Прогружаем текущий скин
      static::$skin_list[$skin_path] = new skin($skin_path);
      static::$active = static::$skin_list[$skin_path];
    }

// В $user['dpath'] 'skins/xnova/'
//    {D_SN_ROOT_VIRTUAL}{dpath}skin.css?{C_var_db_update}

    // Ресолвим инхериты - нужен кэш для объектов skin
    // Инхериты парсятся рекурсивным вызовом конструктора для кэша объектов skin
    // Перекрываем наше ихним

    static::$is_init = true;
  }

  /**
   * skin constructor.
   *
   * @param mixed|null|string $skinName
   */
  public function __construct($skinName = DEFAULT_SKINPATH) {
    strpos($skinName, 'skins/') !== false ? $skinName = substr($skinName, 6) : false;
    strpos($skinName, '/') !== false ? $skinName = str_replace('/', '', $skinName) : false;

    $this->root_http_relative = 'skins/' . $skinName . '/'; // Пока стоит base="" в body SN_ROOT_VIRTUAL - не нужен
    $this->root_physical_absolute = SN_ROOT_PHYSICAL . 'skins/' . $skinName . '/';
    $this->name = $skinName;
    // Искать скин среди пользовательских - когда будет конструктор скинов
    // Может не быть файла конфигурации - тогда используется всё "по дефаулту". Т.е. поданная строка - это именно имя файла

    $this->is_ini_present = false;
    // Проверка на корректность и существование пути
    if(is_file($this->root_physical_absolute . 'skin.ini')) {
      // Пытаемся распарсить файл

      // По секциям? images и config? Что бы не копировать конфигурацию? Или просто unset(__inherit) а затем заново записать
      $this->config = parse_ini_file($this->root_physical_absolute . 'skin.ini');
      if(!empty($this->config)) {

        $this->is_ini_present = true;

        if(!empty($this->config['_inherit'])) {
          // Если скин наследует себя...
          if($this->config['_inherit'] == $skinName) {
            // TODO - определять более сложные случаи циклических ссылок в _inherit
            die('">circular skin inheritance!');
          }
          if(empty(static::$skin_list[$this->config['_inherit']])) {
            static::$skin_list[$this->config['_inherit']] = new skin($this->config['_inherit']);
          }
          $this->parent = static::$skin_list[$this->config['_inherit']];
        }
      } else {
        $this->config = array();
      }

      // Проверка на _inherit
    }

    // Пытаемся скомпилировать _no_image заранее
    if(!empty($this->config[SKIN_IMAGE_MISSED_FIELD])) {
      $this->container[SKIN_IMAGE_MISSED_FIELD] = $this->compile_try_path(SKIN_IMAGE_MISSED_FIELD, $this->config[SKIN_IMAGE_MISSED_FIELD]);
    }

    // Если нет заглушки
    if(empty($this->container[SKIN_IMAGE_MISSED_FIELD])) {
      $this->container[SKIN_IMAGE_MISSED_FIELD] = empty($this->parent)
        // Если нет парента - берем хардкод
        ? $this->container[SKIN_IMAGE_MISSED_FIELD] = SN_ROOT_VIRTUAL . SKIN_IMAGE_MISSED_FILE_PATH
        // Если есть парент - берем у парента. У предков всегда всё есть
        : $this->parent->compile_image(SKIN_IMAGE_MISSED_FIELD, null);
    }

    return $this;
  }

  /**
   * Возвращает строку для вывода в компилированном темплейте PTL
   *
   * @param string   $image_tag
   * @param template $template
   *
   * @return string
   */
  protected function compile_image($image_tag, $template) {
//    // Если у нас есть скомпилированная строка для данного тэга - возвращаем строку. Больше ничего делать не надо
//    if(!empty($this->image_path_list[$image_tag])) {
//      return $this->image_path_list[$image_tag];
//    }

    // Ресолвим переменные template в $image_tag - получаем Resolved Image Tag (RIT)
    // Их названия - в квадратных скобочках типа [ID] или даже [production.ID]
    $image_tag = $this->image_tag_parse($image_tag, $template);

    // Проверяем наличие ключа RIT в хранилища. В нём не может быть несуществующих файлов по построению
    if(!empty($this->container[$image_tag[SKIN_IMAGE_TAG_RESOLVED]])) {
      return $this->container[$image_tag[SKIN_IMAGE_TAG_RESOLVED]];
    }

    // Шорткат
    $image_id = $image_tag[SKIN_IMAGE_TAG_IMAGE_ID];

    // Нет ключа RIT в контейнере - обсчёт пути для RIT из конфигурации
    empty($this->container[$image_id]) && !empty($this->config[$image_id])
      ? $this->compile_try_path($image_id, $this->config[$image_id])
      : false;

    // Всё еще пусто? Может у нас не image ID, а просто путь к файлу?
    empty($this->container[$image_id]) ? $this->compile_try_path($image_id, $image_id) : false;

    // Нет - image ID не является путём к файлу. Пора обратиться к предкам за помощью...
    // Пытаемся вытащить путь из родителя и применить к нему свои параметры
    // Тащим по ID изображения, а не по ТЭГУ - мало ли что там делает с путём родитель и как преобразовывает его в строку?
    if(empty($this->container[$image_id]) && !empty($this->parent)) {
      $this->container[$image_id] = $this->parent->compile_image($image_id, $template);

      // Если у родителя нет картинки - он вернет пустую строку. Тогда нам надо использовать заглушку - свою или родительскую
      empty($this->container[$image_id]) ? $this->container[$image_id] = $this->compile_image(SKIN_IMAGE_MISSED_FIELD, $template) : false;
    }

    return !empty($this->container[$image_id]) ? $this->apply_params($image_tag) : '';
  }

  /**
   * Ресолвит переменные и парсит тэг
   *
   * @param string   $image_tag
   * @param template $template
   *
   * @return string
   */
  protected function image_tag_parse($image_tag, $template) {
    $image_tag_ptl_resolved = $image_tag;
    // Есть переменные из темплейта ?
    if(strpos($image_tag_ptl_resolved, '[') !== false && is_object($template)) {
      // Что бы лишний раз не запускать регексп
      // TODO - многоуровневые вложения ?! Надо ли и где их можно применить
      preg_match_all('#(\[.+?\])#', $image_tag_ptl_resolved, $matches);
      foreach($matches[0] as &$match) {
        $var_name = str_replace(array('[', ']'), '', $match);
        if(strpos($var_name, '.') !== false) {
          // Вложенная переменная темплейта - на текущем уровне
          // TODO Вложенная переменная из корня через "!"
          list($block_name, $block_var) = explode('.', $var_name);
          isset($template->_block_value[$block_name][$block_var]) ? $image_tag_ptl_resolved = str_replace($match, $template->_block_value[$block_name][$block_var], $image_tag_ptl_resolved) : false;
        } elseif(strpos($var_name, '$') !== false) {
          // Корневой DEFINE
          $define_name = substr($var_name, 1);
          isset($template->_tpldata['DEFINE']['.'][$define_name]) ? $image_tag_ptl_resolved = str_replace($match, $template->_tpldata['DEFINE']['.'][$define_name], $image_tag_ptl_resolved) : false;
        } else {
          // Корневая переменная темплейта
          isset($template->_rootref[$var_name]) ? $image_tag_ptl_resolved = str_replace($match, $template->_rootref[$var_name], $image_tag_ptl_resolved) : false;
        }
      }
    }

    if(strpos($image_tag_ptl_resolved, '|') !== false) {
      $params = explode('|', $image_tag_ptl_resolved);
      $image_id = $params[0];
      unset($params[0]);
      $params = $this->reorder_params($params);
      $image_tag_ptl_resolved = implode('|', array_merge(array($image_tag_ptl_resolved), $params));
    } else {
      $params = array();
      $image_id = $image_tag_ptl_resolved;
    }

    return array(
      SKIN_IMAGE_TAG_RAW      => $image_tag,
      SKIN_IMAGE_TAG_RESOLVED => $image_tag_ptl_resolved,
      SKIN_IMAGE_TAG_IMAGE_ID => $image_id,
      SKIN_IMAGE_TAG_PARAMS   => $params,
    );
  }

  /**
   * Проверка физического наличия файла с картинкой
   *
   * @param string $image_id
   * @param string $file_path
   *
   * @return string
   */
  protected function compile_try_path($image_id, $file_path) {
    // Если первый символ пути '/' - значит это путь от HTTP-корня
    // Откусываем его и пользуем остальное
    $relative_path = strpos($file_path, '/') !== 0 ? $this->root_http_relative . $file_path : substr($file_path, 1);

    return is_file(SN_ROOT_PHYSICAL . $relative_path) ? $this->container[$image_id] = SN_ROOT_VIRTUAL . $relative_path : '';
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
    // Быстро и грубо. Если будут более сложные параметры - надо будет переделать
    return array_intersect($this->params_order, $params);
  }

  /**
   * @param $ini_image_id_plain
   * @param $params
   *
   * @return string
   */
  protected function apply_params($image_tag_input) {
    $ini_image_id_plain = $image_tag_input[SKIN_IMAGE_TAG_IMAGE_ID];
    $params = $image_tag_input[SKIN_IMAGE_TAG_PARAMS];

    $image_tag = $ini_image_id_plain;
    $image_string = $this->container[$image_tag];

    // Нет параметров - просто возвращаем значение по $image_name из контейнера
    if(!empty($params) && is_array($params)) {
      // Здесь автоматически произойдёт упорядочивание параметров

      // Параметр 'html' - выводить изображение в виде HTML-тэга
      if(in_array('html', $params)) {
        $image_tag = $image_tag . '|html';
        $image_string = '<img src="' . $image_string . '" />';
        $this->container[$image_tag] = $image_string;
      }
    }

    return $image_string;
  }

}
