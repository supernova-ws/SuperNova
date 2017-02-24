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
class SkinV2 implements SkinInterface {
  const PARAM_HTML = 'html';
  const PARAM_HEIGHT = 'height';
  const PARAM_WIDTH = 'width';

  const INDEX_TAG_RAW = 0;
  const INDEX_CACHE_KEY = 1;
  const INDEX_RESOLVED = 2;
  const INDEX_PARAMS = 3;

  /**
   * @var string $iniFileName
   */
  protected $iniFileName = 'skin.ini';

  /**
   * @var SkinModel $model
   */
  protected $model;


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
   * Родительский скин
   *
   * @var SkinInterface|null
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
   * @var string[] $allowedParams
   */
  protected $allowedParams = array(
    self::PARAM_HTML => '',
    // Will be dumped for all tags which have |html
    self::PARAM_HEIGHT => self::PARAM_HTML,
    self::PARAM_WIDTH => self::PARAM_HTML,
  );
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
    return classSupernova::$gc->skinModel->getImageCurrent($image_tag, $template);
  }

  /**
   * skin constructor.
   *
   * @param mixed|null|string $skinName
   * @param SkinModel         $skinModel
   */
  public function __construct($skinName = DEFAULT_SKINPATH, $skinModel) {
    $this->model = $skinModel;
    $this->name = $skinName;

    $this->root_http_relative = 'skins/' . $this->name . '/'; // Пока стоит base="" в body SN_ROOT_VIRTUAL - не нужен
    $this->root_physical_absolute = SN_ROOT_PHYSICAL . $this->root_http_relative;
    // Искать скин среди пользовательских - когда будет конструктор скинов
    // Может не быть файла конфигурации - тогда используется всё "по дефаулту". Т.е. поданная строка - это именно имя файла

    $this->loadIniFile();

// _no_image должен быть всегда - либо в самом классе, либо в парент-классе
// TODO - добавить стандартную компиляцию
//    // Пытаемся скомпилировать _no_image заранее
//    if(!empty($this->config[SKIN_IMAGE_MISSED_FIELD])) {
//      $this->container[SKIN_IMAGE_MISSED_FIELD] = $this->compile_try_path(SKIN_IMAGE_MISSED_FIELD, $this->config[SKIN_IMAGE_MISSED_FIELD]);
//    }
//
//    // Если нет заглушки
//    if(empty($this->container[SKIN_IMAGE_MISSED_FIELD])) {
//      $this->container[SKIN_IMAGE_MISSED_FIELD] = empty($this->parent)
//        // Если нет парента - берем хардкод
//        ? $this->container[SKIN_IMAGE_MISSED_FIELD] = SN_ROOT_VIRTUAL . SKIN_IMAGE_MISSED_FILE_PATH
//        // Если есть парент - берем у парента. У предков всегда всё есть
//        : $this->parent->compile_image(SKIN_IMAGE_MISSED_FIELD, null);
//    }

    return $this;
  }

  /**
   * @inheritdoc
   */
  public function compile_image($image_tag, $template) {
//    // Если у нас есть скомпилированная строка для данного тэга - возвращаем строку. Больше ничего делать не надо
//    if(!empty($this->image_path_list[$image_tag])) {
//      return $this->image_path_list[$image_tag];
//    }

    // Ресолвим переменные template в $image_tag - получаем Resolved Image Tag (RIT)
    // Их названия - в квадратных скобочках типа [ID] или даже [production.ID]

    $ptlTag = new PTLTag($image_tag, $template, $this->allowedParams);
    $image_tag = array(
      self::INDEX_TAG_RAW   => $ptlTag->raw,
      self::INDEX_CACHE_KEY => $ptlTag->cacheKey,
      self::INDEX_RESOLVED  => $ptlTag->resolved,
      self::INDEX_PARAMS    => $ptlTag->params,
    );

    // Проверяем наличие ключа RIT в хранилища. В нём не может быть несуществующих файлов по построению
    if (!empty($this->container[$image_tag[self::INDEX_CACHE_KEY]])) {
      return $this->container[$image_tag[self::INDEX_CACHE_KEY]];
    }

    // Шорткат
    $image_id = $image_tag[self::INDEX_RESOLVED];

    // Нет ключа RIT в контейнере - обсчёт пути для RIT из конфигурации
    empty($this->container[$image_id]) && !empty($this->config[$image_id])
      ? $this->compile_try_path($image_id, $this->config[$image_id])
      : false;

    // Всё еще пусто? Может у нас не image ID, а просто путь к файлу?
    empty($this->container[$image_id]) ? $this->compile_try_path($image_id, $image_id) : false;

    // Нет - image ID не является путём к файлу. Пора обратиться к предкам за помощью...
    // Пытаемся вытащить путь из родителя и применить к нему свои параметры
    // Тащим по ID изображения, а не по ТЭГУ - мало ли что там делает с путём родитель и как преобразовывает его в строку?
    if (empty($this->container[$image_id]) && !empty($this->parent)) {
      $this->container[$image_id] = $this->parent->compile_image($image_id, $template);

      // Если у родителя нет картинки - он вернет пустую строку. Тогда нам надо использовать заглушку - свою или родительскую
      empty($this->container[$image_id]) ? $this->container[$image_id] = $this->compile_image(SKIN_IMAGE_MISSED_FIELD, $template) : false;
    }

    return !empty($this->container[$image_id]) ? $this->apply_params($image_tag) : '';
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
   * @param $ini_image_id_plain
   * @param $params
   *
   * @return string
   */
  protected function apply_params($image_tag_input) {
    $params = $image_tag_input[self::INDEX_PARAMS];
    $cacheKey = $image_tag_input[self::INDEX_RESOLVED];

    $image_string = $this->container[$cacheKey];

    // Нет параметров - просто возвращаем значение по $image_name из контейнера
    if (!empty($params) && is_array($params)) {
      // Здесь автоматически произойдёт упорядочивание параметров

      // Параметр 'html' - выводить изображение в виде HTML-тэга
      if (array_key_exists(self::PARAM_HTML, $params)) {
        $cacheKey = $cacheKey . '|' . self::PARAM_HTML;

        $htmlParams = '';
        $paramsNoHtml = $params;
        unset($paramsNoHtml[self::PARAM_HTML]);
        // Just dump other params
        foreach ($paramsNoHtml as $name => $data) {
          if($this->allowedParams[$name] != self::PARAM_HTML) {
            continue;
          }

          $htmlParams .= ' ' . $name . '=' . $data;
          $cacheKey .= '|' . $name . '=' . $data;
        }

        $image_string = "<img src=\"{$image_string}\" {$htmlParams} />";
        $this->container[$cacheKey] = $image_string;
      }
    }

    return $image_string;
  }

  /**
   * Loads skin configuration
   */
  protected function loadIniFile() {
    // Проверка на корректность и существование пути
    if (!is_file($this->root_physical_absolute . $this->iniFileName)) {
      return;
    }

    // Пытаемся распарсить файл
    // По секциям? images и config? Что бы не копировать конфигурацию? Или просто unset(__inherit) а затем заново записать
    $aConfig = parse_ini_file($this->root_physical_absolute . $this->iniFileName);
    if (empty($aConfig)) {
      return;
    }

    $this->config = $aConfig;

    // Проверка на _inherit
    if (!empty($this->config['_inherit'])) {
      $parentName = $this->config['_inherit'];

      // Если скин наследует себя...
      if ($parentName == $this->name) {
        // TODO - определять более сложные случаи циклических ссылок в _inherit
        // TODO - throw exception
        die('">circular skin inheritance!');
      }

      $this->parent = $this->model->getSkin($parentName);
    }

  }

}
