<?php
/**
 * Created by Gorlum 05.03.2018 12:57
 */

namespace Pages\Deprecated;

use Modules\sn_module;
use SN;
use SnTemplate;

class PageAdminModules extends PageDeprecated {
  const SORT_BY_PACKAGE = 0;
  const SORT_BY_ACTIVITY = 1;

  private static $sorting = [
    self::SORT_BY_PACKAGE  => ['PACKAGE', 'NAME', '!ACTIVE', '!INSTALLED',],
    self::SORT_BY_ACTIVITY => ['!ACTIVE', '!INSTALLED', 'PACKAGE', 'NAME',],
  ];

  private static $sortFields = [];

  /**
   * @param array $a
   * @param array $b
   *
   * @return int
   */
  private static function sortBy($a, $b) {
    $result = 0;
    foreach (static::$sortFields as $fieldName) {
      if (strpos($fieldName, '!') !== false) {
        $fieldName = substr($fieldName, 1);
        $c = $a;
        $a = $b;
        $b = $c;
      }
      if ($result = $a[$fieldName] > $b[$fieldName] ? 1 : ($a[$fieldName] < $b[$fieldName] ? -1 : 0)) {
        break;
      }
    }

    return $result;
  }

  public static function viewStatic($template = null) {
    define('IN_ADMIN', true);

    lng_include('admin');

    $modules = SN::$gc->modules->getModulesInGroup([], false);

    $render = [];
    foreach ($modules as $module) {
      $render[] = [
        'PACKAGE'   => $module->manifest['package'],
        'NAME'      => $module->manifest['name'],
        'VERSION'   => $module->getVersion(),
        'ACTIVE'    => $module->isActive(),
        'INSTALLED' => $module->isInstalled(),
      ];
    }

    $sortBy = sys_get_param_int('SORT_BY', self::SORT_BY_ACTIVITY);
    array_key_exists($sortBy, static::$sorting) ?: ($sortBy = self::SORT_BY_ACTIVITY);
    static::$sortFields = static::$sorting[$sortBy];
    usort($render, [static::class, 'sortBy']);

    $template = SnTemplate::gettemplate('admin/admin_modules');
    $template->assign_recursive([
      '.'         => ['modules' => $render],
      'SORT_BY'   => $sortBy,
      'PAGE_NAME' => SN::$lang['menu_admin_modules'],
    ]);

    SnTemplate::display($template, SN::$lang['menu_admin_modules']);
  }

}
