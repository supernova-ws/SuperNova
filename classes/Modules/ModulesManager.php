<?php
/**
 * Created by Gorlum 19.03.2018 19:56
 */

namespace Modules;

use Modules\sn_module;
use Core\GlobalContainer;


/**
 * Class ModulesManager
 *
 * Modules ModulesManager
 * Replacement for removed $sn_module and $sn_module_list global variables
 *
 * @package Modules
 */
class ModulesManager {
  /**
   * @var \Core\GlobalContainer $gc
   */
  protected $gc;

  /**
   * Is modules already loaded?
   *
   * @var bool $modulesAreLoaded
   */
  protected $modulesAreLoaded = false;

  /**
   * Plain list of available modules
   *
   * @var sn_module[] $modules - [(string)$moduleName => (sn_module)$module]
   */
  protected $modules = [];

  /**
   * Modules arranged per package
   *
   * @var sn_module[][] $packages - [[(string)$package] => [(string)$moduleName => (sn_module)$module]]
   */
  protected $packages = [];


  /**
   * ModulesManager constructor.
   *
   * @param GlobalContainer $gc
   */
  public function __construct($gc) {
    $this->gc = $gc;

    $this->loadModules(SN_ROOT_MODULES);
  }


  /**
   * @param string $dir_name
   */
  public function loadModules($dir_name = SN_ROOT_MODULES) {
    if ($this->modulesAreLoaded) {
      return;
    }

    if (file_exists($dir_name) && is_dir($dir_name)) {
      $this->loadModulesFromDirectory($dir_name, PHP_EX);
    }

    $this->modulesAreLoaded = true;
  }

  /**
   *
   */
  public function initModules() {
    $loadOrder = $this->getLoadOrder();

    // Инициализируем модули
    // По нормальным делам это должна быть загрузка модулей и лишь затем инициализация - что бы минимизировать размер процесса в памяти
    foreach ($loadOrder as $moduleName => $place) {
      $module = $this->modules[$moduleName];

      if ($place >= 0) {
        $module->check_status();
        if (!$module->isActive()) {
          unset($this->modules[$moduleName]);
          continue;
        }

        $module->initialize();
      } else {
        unset($module);
      }
    }
  }

  /**
   * @param $dir_name
   * @param $load_extension
   */
  protected function loadModulesFromDirectory($dir_name, $load_extension) {
    $dir = opendir($dir_name);
    while (($file = readdir($dir)) !== false) {
      if ($file == '..' || $file == '.') {
        continue;
      }

      $full_filename = $dir_name . $file;
      if (is_dir($full_filename) && file_exists($full_filename = "{$full_filename}/{$file}.{$load_extension}")) {
        require_once($full_filename);

        // Registering module
        if (class_exists($file)) {
          // TODO Module SHOULD register himself!
//          new $file($full_filename)
          $this->registerModule($file, new $file($full_filename));
        }
      }
    }
  }

  /**
   * Get module load order counting requirements and each module load order
   *
   * @return int[] - [(string)$moduleName => (int)$loadOrder]
   */
  protected function getLoadOrder() {
    // Генерируем список требуемых модулей
    $loadOrder = [];
    $sn_req = [];

    foreach ($this->modules as $loaded_module_name => $module) {
      $loadOrder[$loaded_module_name] = $module->getLoadOrder();
      if (!empty($module->manifest[sn_module::M_REQUIRE])) {
        foreach ($module->manifest[sn_module::M_REQUIRE] as $require_name) {
          $sn_req[$loaded_module_name][$require_name] = 0;
        }
      }
    }

    // Создаем последовательность инициализации модулей
    // По нормальным делам надо сначала читать их конфиги - вдруг какой-то модуль отключен?
    do {
      $prev_order = $loadOrder;

      foreach ($sn_req as $loaded_module_name => &$req_data) {
        $level = 1;
        foreach ($req_data as $req_name => &$req_level) {
          if ($loadOrder[$req_name] == -1 || !isset($loadOrder[$req_name])) {
            $level = $req_level = -1;
            break;
          } else {
            $level += $loadOrder[$req_name];
          }
          $req_level = $loadOrder[$req_name];
        }
        if ($level > $loadOrder[$loaded_module_name] || $level == -1) {
          $loadOrder[$loaded_module_name] = $level;
        }
      }
    } while ($prev_order != $loadOrder);

    asort($loadOrder);

    return $loadOrder;
  }

  /**
   * @param string|array $groups - Module group name or '' for any group
   * @param bool         $active - returns only active modules
   *
   * @return int
   */
  public function countModulesInGroup($groups = '', $active = true) {
    return count($this->getModulesInGroup($groups, $active));
  }

  /**
   * Getting list of active modules
   *
   * @param string|array $groups - Module group name or ''|[] for any group
   * @param bool         $active - returns only active modules
   *
   * @return sn_module[]
   */
  public function getModulesInGroup($groups = [], $active = true) {
    // If no groups specified - iterating all groups
    if (empty($groups)) {
      $groups = array_keys($this->packages);
    }

    if (!is_array($groups)) {
      $groups = [$groups];
    }

    $activeModules = [];
    foreach ($groups as $groupName) {
      if (is_array($this->packages[$groupName]) && !empty($this->packages[$groupName])) {
        foreach ($this->packages[$groupName] as $moduleName => $module) {
          if (!$active || $module->isActive()) {
            $activeModules[$moduleName] = $module;
          }
        }
      }
    }

    return $activeModules;
  }


  /**
   * Get module by name
   *
   * @param string $moduleName
   * @param bool   $active - return module only if it is active
   *
   * @return sn_module|null
   */
  public function getModule($moduleName, $active = true) {
    return !empty($this->modules[$moduleName]) && (!$active || $this->modules[$moduleName]->isActive()) ? $this->modules[$moduleName] : null;
  }

  /**
   * Register module for further use
   *
   * Basically modules does not exists anywhere except in Modules ModulesManager
   *
   * @param string    $moduleName
   * @param sn_module $module
   */
  public function registerModule($moduleName, $module) {
    $this->modules[$moduleName] = $module;
    $this->packages[$module->manifest['package']][$moduleName] = &$this->modules[$moduleName];
  }

}
