<?php
/**
 * Created by Gorlum 05.03.2018 12:57
 */

namespace Pages\Deprecated;

use SN;
use DBAL\DbSqlPaging;
use General\Helpers\PagingRenderer;
use SnTemplate;

class PageAdminPayment extends PageDeprecated {

  public static function viewStatic() {
    define('INSIDE', true);
    define('INSTALL', false);
    define('IN_ADMIN', true);

    global $lang;

    SnTemplate::messageBoxAdminAccessDenied(AUTH_LEVEL_ADMINISTRATOR);

    $template = SnTemplate::gettemplate('admin/admin_payment', true);


    $flt_payer = sys_get_param_int('flt_payer', -1);
    $flt_module = sys_get_param_str('flt_module');
    $flt_status = sys_get_param_int('flt_status', PAYMENT_STATUS_ALL);
    $flt_test = sys_get_param_int('flt_test', PAYMENT_TEST_REAL);
    $flt_currency = sys_get_param_int('flt_currency', -1);
    $flt_stat = sys_get_param_int('flt_stat', PAYMENT_FILTER_STAT_NORMAL);
    $flt_stat_type = sys_get_param_int('flt_stat_type', -1);


    // TODO - remove debug
//    $flt_stat = PAYMENT_FILTER_STAT_YEAR;
//    $flt_stat = PAYMENT_FILTER_STAT_MONTH;

    $stats = [
      PAYMENT_FILTER_STAT_NORMAL => [
        'sql_fields' => [],
      ],
      PAYMENT_FILTER_STAT_MONTH  => [
        'sql_fields' => [
          'payment_status',
//          'payment_user_id',
//          'payment_user_name',
          'sum(if(payment_currency = "UAH", payment_amount / 11, payment_amount)) as payment_amount',
          'if(payment_currency = "UAH", "USD", payment_currency) as payment_currency',
          'sum(payment_dark_matter_paid) as payment_dark_matter_paid',
          'sum(payment_dark_matter_gained) as payment_dark_matter_gained',
          'date_format(payment_date, "%Y-%m") as payment_date',
          'payment_comment',
          'payment_module_name',
          'payment_external_id',
          'payment_external_date',
          'payment_external_lots',
          'sum(payment_external_amount) as payment_external_amount',
          'payment_external_currency',
          'payment_test',
        ],
        'sql_group'  => [
          'date_format(payment_date, "%Y-%m")',
          'if(payment_currency = "UAH", "USD", payment_currency)',
          'payment_external_currency',
          'payment_test',
          'payment_status',
        ],
        'sql_order'  => 'date_format(payment_date, "%Y-%m") desc',
      ],
      PAYMENT_FILTER_STAT_YEAR   => [
        'sql_fields' => [
          'payment_status',
//          'payment_user_id',
//          'payment_user_name',
          'sum(if(payment_currency = "UAH", payment_amount / 11, payment_amount)) as payment_amount',
          'if(payment_currency = "UAH", "USD", payment_currency) as payment_currency',
          'payment_dark_matter_paid',
          'payment_dark_matter_gained',
          'date_format(payment_date, "%Y") as payment_date',
          'payment_comment',
          'payment_module_name',
          'payment_external_id',
          'payment_external_date',
          'payment_external_lots',
          'sum(payment_external_amount) as payment_external_amount',
          'payment_external_currency',
          'payment_test',
        ],
        'sql_group'  => [
          'date_format(payment_date, "%Y")',
          'if(payment_currency = "UAH", "USD", payment_currency)',
          'payment_external_currency',
          'payment_test',
          'payment_status',
        ],
        'sql_order'  => 'date_format(payment_date, "%Y") desc',
      ],
      PAYMENT_FILTER_STAT_ALL  => [
        'sql_fields' => [
          'payment_status',
//          'payment_user_id',
//          'payment_user_name',
          'sum(if(payment_currency = "UAH", payment_amount / 11, payment_amount)) as payment_amount',
          'if(payment_currency = "UAH", "USD", payment_currency) as payment_currency',
          'payment_dark_matter_paid',
          'payment_dark_matter_gained',
//          'date_format(payment_date, "%Y") as payment_date',
          'payment_comment',
          'payment_module_name',
          'payment_external_id',
          'payment_external_date',
          'payment_external_lots',
          'sum(payment_external_amount) as payment_external_amount',
          'payment_external_currency',
          'payment_test',
        ],
        'sql_group'  => [
//          'date_format(payment_date, "%Y")',
          'if(payment_currency = "UAH", "USD", payment_currency)',
          'payment_external_currency',
          'payment_test',
          'payment_status',
        ],
        'sql_order'  => 'date_format(payment_date, "%Y") desc',
      ],
    ];


    if (!isset($stats[$flt_stat])) {
      $flt_stat = PAYMENT_FILTER_STAT_NORMAL;
    }

    $theStat = $stats[$flt_stat];

    if (!empty($theStat['sql_fields']) && $flt_payer != -1) {
      $theStat['sql_fields'] = array_merge($theStat['sql_fields'], [
        'payment_user_id',
        'payment_user_name',
      ]);
    }

    $query = new \DBAL\DbSqlPaging(
      "SELECT " .
      (!empty($theStat['sql_fields']) ? implode(',', $theStat['sql_fields']) : '*') .
      " FROM `{{payment}}` WHERE 1 " .
      ($flt_payer > 0 ? "AND payment_user_id = {$flt_payer} " : '') .
      ($flt_status >= 0 ? "AND payment_status = {$flt_status} " : '') .
      ($flt_test >= 0 ? "AND payment_test = {$flt_test} " : '') .
      ($flt_module ? "AND payment_module_name = '{$flt_module}' " : '') .

      (!empty($theStat['sql_group']) ? ' GROUP BY ' . implode(',', $theStat['sql_group']) : '') .
      ' ORDER BY ' . (!empty($theStat['sql_order']) ? $theStat['sql_order'] : 'payment_id desc')
      ,
      PAGING_PAGE_SIZE_DEFAULT_PAYMENTS,
      sys_get_param_int(PagingRenderer::KEYWORD)
    );

    $pager = new PagingRenderer($query, 'index.php?' . $_SERVER['QUERY_STRING']);
    $pager->setDelta(10);

    foreach ($query as $row) {
      $row2 = array();
      foreach ($row as $key => $value) {
        $row2[strtoupper($key)] = $value;
      }
      $template->assign_block_vars('payment', $row2);
    }

    SnTemplate::tpl_assign_select($template, 'payer', self::getPayers());
    SnTemplate::tpl_assign_select($template, 'module', self::getUsedModules());
    SnTemplate::tpl_assign_select($template, 'status', SN::$lang['adm_pay_filter_status']);
    SnTemplate::tpl_assign_select($template, 'test', SN::$lang['adm_pay_filter_test']);
    SnTemplate::tpl_assign_select($template, 'flt_stat', SN::$lang['adm_pay_filter_stat']);
    SnTemplate::tpl_assign_select($template, 'flt_currency', self::getCurrencies());

    $template->assign_vars(array(
      'FLT_PAYER'    => $flt_payer,
      'FLT_STATUS'   => $flt_status,
      'FLT_TEST'     => $flt_test,
      'FLT_MODULE'   => $flt_module,
      'FLT_CURRENCY' => $flt_currency,
      'FLT_STAT'     => $flt_stat,

      'PAGER_PAYMENTS' => $pager->render(),
    ));

    SnTemplate::display($template, $lang['adm_pay_stats']);
  }

  /**
   * @return array
   */
  protected static function getUsedModules() {
    $module_list = array(
      '' => SN::$lang['adm_pay_filter_all'],
    );
    $query = doquery("SELECT distinct payment_module_name FROM `{{payment}}` ORDER BY payment_module_name");
    while ($row = db_fetch($query)) {
      $module_list[$row['payment_module_name']] = $row['payment_module_name'];
    }

    return $module_list;
  }

  /**
   * @return array
   */
  protected static function getPayers() {
    $payer_list = array(
      -1 => SN::$lang['adm_pay_filter_all'],
    );
    $query = doquery("SELECT payment_user_id, payment_user_name FROM `{{payment}}` GROUP BY payment_user_id ORDER BY payment_user_name");
    while ($row = db_fetch($query)) {
      $payer_list[$row['payment_user_id']] = '[' . $row['payment_user_id'] . '] ' . $row['payment_user_name'];
    }

    return $payer_list;
  }

  /**
   * @return array
   */
  protected static function getCurrencies() {
    $payer_list = array(
      -1 => SN::$lang['adm_pay_filter_all'],
    );
    $query = doquery("SELECT distinct payment_external_currency FROM `{{payment}}`");
    while ($row = db_fetch($query)) {
      $payer_list[$row['payment_external_currency']] = $row['payment_external_currency'];
    }

    return $payer_list;
  }

}
