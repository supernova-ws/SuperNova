<?php
/**
 * Created by Gorlum 05.03.2018 12:57
 */

namespace Deprecated;


use SN;
use DBAL\DbSqlPaging;
use Helpers\PagingRenderer;

class PageAdminMining extends PageDeprecated {

  public static function viewStatic() {
    define('IN_ADMIN', true);

    $filterActive = sys_get_param_int('ACTIVE_STATUS', 0);

    $stringSqlQuery = "
SELECT
  u.id, username, total_rank, total_points,
  sum(metal_perhour)      AS `metal_prod`,
  sum(crystal_perhour)    AS `crystal_prod`,
  sum(deuterium_perhour)  AS `deuterium_prod`,
  avg(metal_mine_porcent) AS `average_metal_percent`,
  avg(crystal_mine_porcent)  AS `average_crystal_percent`,
  avg(deuterium_sintetizer_porcent)  AS `average_deuterium_percent`
FROM
  `{{planets}}` AS p
  LEFT JOIN `{{users}}` AS u ON u.id = p.id_owner
WHERE
  u.total_rank <> 0
	AND metal_mine_porcent != 0
	AND crystal_mine_porcent != 0
	AND deuterium_sintetizer_porcent != 0
	AND `user_as_ally` IS NULL"
      . ($filterActive == 1 ? " AND FROM_UNIXTIME(u.`onlinetime`) >= DATE_SUB(NOW(), INTERVAL " . PLAYER_INACTIVE_TIMEOUT_LONG . " SECOND) " : '')
      . ($filterActive == 2 ? " AND FROM_UNIXTIME(u.`onlinetime`) >= DATE_SUB(NOW(), INTERVAL " . PLAYER_INACTIVE_TIMEOUT . " SECOND) " : '')
      . "
GROUP BY
  id_owner
ORDER BY
  u.total_rank
;";


    // View all at one page //    $iterator = SN::$db->selectIterator($stringSqlQuery);
    $iterator = new DbSqlPaging($stringSqlQuery, 50, sys_get_param_int(PagingRenderer::KEYWORD));
    $pager = new PagingRenderer($iterator, 'index.php?page=admin/admin_mining&ACTIVE_STATUS=' . intval($filterActive));

    $render = [];
    foreach ($iterator as $row) {
      $render[] = [
        'ID'        => $row['id'],
        'NAME'      => player_nick_render_to_html($row, true),
        'RANK'      => $row['total_rank'],
        'METAL'     => $row['metal_prod'],
        'CRYSTAL'   => $row['crystal_prod'],
        'DEUTERIUM' => $row['deuterium_prod'],
        'TOTAL'     => $row['metal_prod'] + $row['crystal_prod'] + $row['deuterium_prod'],
        'PERCENT'   =>
          round($row['average_metal_percent'], 1) . '/' .
          round($row['average_crystal_percent'], 1) . '/' .
          round($row['average_deuterium_percent'], 1),
      ];
    }

    $template = gettemplate('admin/admin_mining');
    $template->assign_recursive([
      '.'              => ['production' => $render],
      'PAGER_MESSAGES' => $pager ? $pager->render() : '',
      'ACTIVE_STATUS'  => $filterActive,
      'PAGE_NAME'      => SN::$lang['menu_admin_minig'],
    ]);
    display($template, SN::$lang['menu_admin_minig']);
  }

}
