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
   {I_<путь к картинке от корня движка>|парам1|парам2|...} - {I_/img/e.jpg|html}
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
 * - Автоматическая поддержка WebP для браузеров, что поддерживают WebP с фоллбэком на обычный формат
 */
class SkinV2 implements SkinInterface {
  const PARAM_HTML = 'html';
  const PARAM_HTML_HEIGHT = 'height';
  const PARAM_HTML_WIDTH = 'width';
  // Use skin for image
  const PARAM_SKIN = 'skin';
  const WEBP_SUFFIX = '_webp';

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
   * @var self[] $skin_list
   */
  protected static $skin_list = array();
  /**
   * Текущий скин
   *
   * @var self|null
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
    self::PARAM_HTML        => '',
    // Will be dumped for all tags which have |html
    self::PARAM_HTML_HEIGHT => self::PARAM_HTML,
    self::PARAM_HTML_WIDTH  => self::PARAM_HTML,

    self::PARAM_SKIN => '',
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
  protected $name = '';

  /**
   * Cached value of no image string
   *
   * @var string $noImage
   */
  protected $noImage;

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
    return SN::$gc->skinModel->getImageCurrent($image_tag, $template);
  }

  /**
   * skin constructor.
   *
   * @param mixed|null|string $skinName
   * @param SkinModel         $skinModel
   */
  public function __construct($skinName = DEFAULT_SKINPATH, $skinModel) {
    $this->model = $skinModel;
    $this->name  = $skinName;

    $this->root_http_relative     = 'skins/' . $this->name . '/'; // Пока стоит base="" в body SN_ROOT_VIRTUAL - не нужен
    $this->root_physical_absolute = SN_ROOT_PHYSICAL . $this->root_http_relative;
    // Искать скин среди пользовательских - когда будет конструктор скинов
    // Может не быть файла конфигурации - тогда используется всё "по дефаулту". Т.е. поданная строка - это именно имя файла

    $this->loadIniFile();
    $this->setParentFromConfig();

    // Пытаемся скомпилировать _no_image заранее
    $model       = $this->model;
    $noImageID   = $model::NO_IMAGE_ID;
    $noImagePath = $model::NO_IMAGE_PATH;

    // Заглушка на самый крайний случай - когда скин является корневым и у него нет _no_image
    if (empty($this->config[$noImageID]) && !$this->parent) {
      // Если нет парента - берем хардкод
      // Используем стандартный файл из движка
      $this->container[$noImageID] = $this->compile_try_path($noImageID, $noImagePath);
      // Проверка, что файл - отсутствует. Если да - это повреждение движка
      // TODO - throw exception
      empty($this->container[$noImageID]) ? die('Game file missing: ' . $noImagePath) : false;
    } else {
      $this->container[$noImageID] = $this->imageFromPTLTag(
        new PTLTag(SKIN_IMAGE_MISSED_FIELD, null, $this->allowedParams)
      );
    }

    $this->noImage = $this->container[$noImageID];

    return $this;
  }

  /**
   * @inheritdoc
   */
  public function getName() {
    return $this->name;
  }

  /**
   * @inheritdoc
   */
  public function imageFromStringTag($stringTag, $template = null) {
    return $this->imageFromPTLTag(new PTLTag($stringTag, $template, $this->allowedParams));
  }

  /**
   * Возвращает строку для вывода в компилированном темплейте PTL
   *
   * @param PTLTag $ptlTag
   *
   * @return string
   */
  public function imageFromPTLTag($ptlTag) {
    // Проверяем наличие ключа RIT в хранилища. В нём не может быть несуществующих файлов по построению
    $cacheKey = $ptlTag->getCacheKey();
    if (!empty($this->container[$cacheKey])) {
      return $this->container[$cacheKey];
    }

    // Шорткат
    $imageId = $ptlTag->resolved;

    $this->tryWebp($imageId);

    // Нет ключа RIT в контейнере - обсчёт пути для RIT из конфигурации
    empty($this->container[$imageId]) && !empty($this->config[$imageId])
      ? $this->compile_try_path($imageId, $this->config[$imageId])
      : false;

    // Всё еще пусто? Может у нас не image ID, а просто путь к файлу?
    empty($this->container[$imageId]) ? $this->compile_try_path($imageId, $imageId) : false;

    // Нет - image ID не является путём к файлу. Пора обратиться к предкам за помощью...
    // Пытаемся вытащить путь из родителя и применить к нему свои параметры
    // Тащим по ID изображения, а не по ТЭГУ - мало ли что там делает с путём родитель и как преобразовывает его в строку?
    if (empty($this->container[$imageId]) && !empty($this->parent)) {
      $this->container[$imageId] = $this->parent->imageFromPTLTag(new PTLTag($imageId, $ptlTag->template, $this->allowedParams));
    }

    // Если у родителя нет картинки - он вернет пустую строку. Тогда нам надо использовать заглушку - свою или родительскую
    empty($this->container[$imageId]) ? $this->container[$imageId] = $this->noImage : false;

    return !empty($this->container[$imageId]) ? $this->apply_params($ptlTag) : '';
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
    $relative_path = strpos($file_path, '/') !== 0
      ? $this->root_http_relative . $file_path
      // Если первый символ пути '/' - значит это путь от HTTP-корня
      // Откусываем символ и пользуем остальное в качестве пути
      : substr($file_path, 1);

    return is_file(SN_ROOT_PHYSICAL . $relative_path) ? $this->container[$image_id] = SN_ROOT_VIRTUAL . $relative_path : '';
  }

  /**
   * @param PTLTag $ptlTag
   *
   * @return string
   */
  protected function apply_params(PTLTag $ptlTag) {
    if (!is_object($ptlTag) || empty($ptlTag->params) || !is_array($ptlTag->params)) {
      return $this->container[$ptlTag->resolved];
    }

    $params       = $ptlTag->params;
    $image_string = $this->container[$ptlTag->resolved];

    // Здесь автоматически произойдёт упорядочивание параметров

    // Параметр 'skin' - использовать изображение из другого скина
    if (array_key_exists(self::PARAM_SKIN, $params)) {
      if ($params[self::PARAM_SKIN] == $this->name) {
        // If skin - is this skin - then removing this param from list
        $ptlTag->removeParam(self::PARAM_SKIN);
      } else {
        $skin         = $this->model->getSkin($params[self::PARAM_SKIN]);
        $image_string = $skin->imageFromStringTag($ptlTag->resolved, $ptlTag->template);
      }
    }

    // Параметр 'html' - выводить изображение в виде HTML-тэга
    if (array_key_exists(self::PARAM_HTML, $params)) {
      $htmlParams   = '';
      $paramsNoHtml = $params;
      unset($paramsNoHtml[self::PARAM_HTML]);
      // Just dump other params
      foreach ($paramsNoHtml as $name => $data) {
        if ($this->allowedParams[$name] != self::PARAM_HTML) {
          continue;
        }

        $htmlParams .= ' ' . $name . '=' . $data;
      }

      $image_string = "<img src=\"{$image_string}\" {$htmlParams} />";
    }

    return $this->container[$ptlTag->getCacheKey()] = $image_string;
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
  }

  protected function setParentFromConfig() {
    // Проверка на _inherit
    if (empty($this->config['_inherit'])) {
      return;
    }

    $parentName = $this->config['_inherit'];
    // Если скин наследует себя...
    if ($parentName == $this->name) {
      // TODO - определять более сложные случаи циклических ссылок в _inherit
      // TODO - throw exception
      die('">circular skin inheritance!');
    }

    $this->parent = $this->model->getSkin($parentName);
  }

  /**
   * @param string $imageId Internal Image ID to try
   */
  private function tryWebp($imageId) {
    if (!is_object(SN::$gc->theUser) || !SN::$gc->theUser->isWebpSupported()) {
      return;
    }
    if (!empty($this->container[$imageId])) {
      // Something already there - nothing to do
      return;
    }

    $webpImageId = $imageId . self::WEBP_SUFFIX;
    if (empty($this->config[$webpImageId])) {
      // No WebP alternative - nothing to do
      // We WILL NOT check for parent if there is no WebP alternative!
      return;
    }

    // Trying to use WebP variant as original image
    $this->compile_try_path($imageId, $this->config[$webpImageId]);

    // Ready or not - we're out of here
  }

}
