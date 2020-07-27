<?php
/**
 * Created by Gorlum 05.03.2018 12:57
 */

namespace Pages\Deprecated;

use SN;
use DBAL\DbSqlPaging;
use General\Helpers\PagingRenderer;
use SnTemplate;

class PageAdminMining extends PageDeprecated {
  const PAGE_SORT_BY_PRODUCTION = 0;
  const PAGE_SORT_BY_RANK = 1;
  const PAGE_SORT_BY_ID = 2;
  const PAGE_SORT_BY_NAME = 3;
  const PAGE_SORT_BY_POINTS = 4;

  public static function viewStatic() {
    $sorting = [
      static::PAGE_SORT_BY_PRODUCTION => [
        'ID'        => static::PAGE_SORT_BY_PRODUCTION,
        'HTML_ID'   => 'sortByTotalProduction',
        'HTML_NAME' => '{ byTotalProduction }',
        'SQL_SORT'  => ['$specialField'],
      ],
      self::PAGE_SORT_BY_RANK   => [
        'ID'        => self::PAGE_SORT_BY_RANK,
        'HTML_ID'   => 'byTotalRank',
        'HTML_NAME' => '{ byTotalRank }',
        'SQL_SORT'  => ['u.total_rank',],
      ],
      self::PAGE_SORT_BY_ID => [
        'ID'        => self::PAGE_SORT_BY_ID,
        'HTML_ID'   => 'byId',
        'HTML_NAME' => '{ byId }',
        'SQL_SORT'  => ['u.id',],
      ],
      self::PAGE_SORT_BY_NAME => [
        'ID'        => self::PAGE_SORT_BY_NAME,
        'HTML_ID'   => 'byName',
        'HTML_NAME' => '{ byName }',
        'SQL_SORT'  => ['u.username',],
      ],

      self::PAGE_SORT_BY_POINTS => [
        'ID'        => self::PAGE_SORT_BY_POINTS,
        'HTML_ID'   => 'byTotalPoints',
        'HTML_NAME' => '{ byTotalPoints }',
        'SQL_SORT'  => ['u.total_points DESC'],
      ],
    ];

    // Currently unused - because it duplicates sorting by rank for now
    unset($sorting[self::PAGE_SORT_BY_POINTS]);

    define('IN_ADMIN', true);

    lng_include('admin');

    $specialField =
      'sum(metal_perhour) * ' . get_unit_cost_in([RES_METAL => 1]) . ' + ' .
      'sum(crystal_perhour) * ' . get_unit_cost_in([RES_CRYSTAL => 1]) . ' + ' .
      'sum(deuterium_perhour) * ' . get_unit_cost_in([RES_DEUTERIUM => 1]);
    $sorting[static::PAGE_SORT_BY_PRODUCTION]['SQL_SORT'] = [$specialField . ' DESC'];

    $filterActive = sys_get_param_int('ACTIVE_STATUS', 0);
    $sortBy = sys_get_param_int('SORT_BY', static::PAGE_SORT_BY_PRODUCTION);
    array_key_exists($sortBy, $sorting) ?: ($sortBy = static::PAGE_SORT_BY_PRODUCTION);

    $stringSqlQuery = "
SELECT
  u.id, username, total_rank, total_points,
  sum(metal_perhour)      AS `metal_prod`,
  sum(crystal_perhour)    AS `crystal_prod`,
  sum(deuterium_perhour)  AS `deuterium_prod`,
  {$specialField} AS `total_prod`, 
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
      . ($filterActive == self::PAGE_SORT_BY_NAME ? " AND FROM_UNIXTIME(u.`onlinetime`) >= DATE_SUB(NOW(), INTERVAL " . PLAYER_INACTIVE_TIMEOUT_LONG . " SECOND) " : '')
      . ($filterActive == self::PAGE_SORT_BY_ID ? " AND FROM_UNIXTIME(u.`onlinetime`) >= DATE_SUB(NOW(), INTERVAL " . PLAYER_INACTIVE_TIMEOUT . " SECOND) " : '')
      . "
GROUP BY
  id_owner 
" . " ORDER BY " . implode(',', $sorting[$sortBy]['SQL_SORT']);

    // View all at one page //    $iterator = SN::$db->selectIterator($stringSqlQuery);
    $iterator = new DbSqlPaging($stringSqlQuery, 50, sys_get_param_int(PagingRenderer::KEYWORD));

    $render = [];
    foreach ($iterator as $row) {
      $render[] = [
        'ID'         => $row['id'],
        'NAME'       => player_nick_render_to_html($row, true),
        'NAME_clear' => $row['username'],
        'RANK'       => $row['total_rank'],
        'POINTS'     => $row['total_points'],
        'METAL'      => $row['metal_prod'],
        'CRYSTAL'    => $row['crystal_prod'],
        'DEUTERIUM'  => $row['deuterium_prod'],
        'TOTAL'      => $row['total_prod'],
        'TOTAL_CALC' => get_unit_cost_in([
          RES_METAL     => $row['metal_prod'],
          RES_CRYSTAL   => $row['crystal_prod'],
          RES_DEUTERIUM => $row['deuterium_prod'],
        ]),
        'PERCENT'    =>
          round($row['average_metal_percent'], 2) . '/' .
          round($row['average_crystal_percent'], 2) . '/' .
          round($row['average_deuterium_percent'], 2),
      ];
    }

    $template = SnTemplate::gettemplate('admin/admin_mining');

    $sorting[$sortBy]['CHECKED'] = true;
    $template->assign_recursive(['.' => ['sorting' => $sorting]]);

    $pager = new PagingRenderer($iterator,
      'index.php?page=admin/admin_mining' .
      '&ACTIVE_STATUS=' . intval($filterActive) .
      '&SORT_BY=' . intval($sortBy)
    );

    $template->assign_recursive([
      'PAGE_NAME' => SN::$lang['menu_admin_mining'],

      'PAGER_MESSAGES' => $pager ? $pager->render() : '',

      'SORT_BY'       => $sortBy,
      'ACTIVE_STATUS' => $filterActive,

      '.' => ['production' => $render],
    ]);
    SnTemplate::display($template, SN::$lang['menu_admin_mining']);
  }

}
