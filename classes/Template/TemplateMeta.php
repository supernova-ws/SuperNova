<?php
/**
 * Created by Gorlum 03.09.2019 0:16
 */

namespace Template;

use SnTemplate;
use template;

/**
 * Meta template - should work as interface between game and any rendering engine
 *
 * @package Template
 */
class TemplateMeta {
  const INI_FILE_NAME = '_template.ini';
  const CONFIG_RENDER_WHOLE = '_renderWhole';
  /**
   * Конфигурация скина - читается из INI-файла
   *
   * @var array $config
   */
  protected $config = [];

  /**
   * @var string
   */
  protected $name = '';
  /**
   * Full path to template root
   *
   * @var string $pathFull
   */
  protected $pathFull = '';
  /**
   * Relative path from any template
   *
   * @var string $pathRelative
   */
  protected $pathRelative = '';

  /**
   * @var self|null $parent
   */
  protected $parent = null;

  /**
   * @var SnTemplate|null $manager
   */
  protected $manager = null;

  /**
   * TemplateMeta constructor.
   *
   * @param SnTemplate $manager
   * @param            $templateName
   * @param string     $templatePath Path to template. Can be absolute (starting with '/', should also include template name if any) or relative to game root. Empty means "autodetect"
   */
  public function __construct($manager, $templateName, $templatePath = '') {
    $this->name    = $templateName;
    $this->manager = $manager;

    if (empty($templatePath) || !is_string($templatePath)) {
      $this->pathFull = SN_ROOT_PHYSICAL . SnTemplate::SN_TEMPLATES_PARTIAL_PATH . $templateName . '/';
    } else {
      if (substr($templatePath, -1) !== '/') {
        $templatePath .= '/';
      }
      if (substr($templatePath, 0, 1) === '/') {
        // Absolute path
        $this->pathFull = $templatePath;
      } else {
        // Game root relative path
        $this->pathFull = SN_ROOT_PHYSICAL . $templatePath . $templateName . '/';
      }
    }

    $this->loadIniFile();
    $this->setParentFromConfig();

  }

  /**
   * Loads skin configuration
   */
  protected function loadIniFile() {
    // Проверка на корректность и существование пути
    if (!is_file($this->pathFull . static::INI_FILE_NAME)) {
      return;
    }

    // Пытаемся распарсить файл
    // По секциям? images и config? Что бы не копировать конфигурацию? Или просто unset(__inherit) а затем заново записать
    $aConfig = parse_ini_file($this->pathFull . static::INI_FILE_NAME);
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
    if ($parentName === $this->name) {
      // TODO - определять более сложные случаи циклических ссылок в _inherit
      // TODO - throw exception
      die('">circular skin inheritance!');
    }

    $this->parent = $this->manager->registerTemplate($parentName);
  }

  /**
   * @param template $template
   * @param string   $template_path
   *
   * @return template
   */
  public function getTemplate($template, $template_path) {
    !is_object($template) ? $template = new template(SN_ROOT_PHYSICAL) : false;

    !$template_path || !is_string($template_path) ? $template_path = SN_ROOT_PHYSICAL . SnTemplate::SN_TEMPLATES_PARTIAL_PATH : false;

    if (!$this->parent || empty($fallbackName = $this->parent->getName()) || !$this->isTemplateExists()) {
      // If no parent template - then using default template as fallback one
      $fallbackName = SnTemplate::SN_TEMPLATE_NAME_DEFAULT;
    }

    $template->set_custom_template($template_path . $this->name . '/', $this->name, $template_path . $fallbackName . '/');

    return $template;
  }


  public function getName() {
    return $this->name;
  }

  public function cssAddFileName($cssFileName, array $standard_css) {
    if ($this->parent) {
      $standard_css = $this->parent->cssAddFileName($cssFileName, $standard_css);
    } elseif (!$this->isTemplateExists()) {
      // If template dir does not exists - falling back to default CSS file
      $standard_css = SnTemplate::cssAddFileName(SnTemplate::SN_TEMPLATES_PARTIAL_PATH . SnTemplate::SN_TEMPLATE_NAME_DEFAULT . '/' . $cssFileName, $standard_css);
    }

    $standard_css = SnTemplate::cssAddFileName(SnTemplate::SN_TEMPLATES_PARTIAL_PATH . $this->name . '/' . $cssFileName, $standard_css);

    return $standard_css;
  }

  /**
   * Does template files physically exists on disk?
   * Only full path to template checked
   *
   * @return bool
   */
  public function isTemplateExists() {
    return file_exists($this->pathFull);
  }

  /**
   * Is template rendered at once - not header/footer separately?
   *
   * @return bool
   */
  public function isRenderWhole() {
    return !empty($this->config[self::CONFIG_RENDER_WHOLE]);
  }

}
