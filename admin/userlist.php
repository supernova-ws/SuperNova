<?php
/** @noinspection SqlResolve */
/** @noinspection PhpUnnecessaryCurlyVarSyntaxInspection */
/** @noinspection PhpDeprecationInspection */

/**
 * Project "SuperNova.WS" copyright (c) 2009-2025 Gorlum
 * @version #46d0#
 **/

use Player\PlayerStatic;

const INSIDE   = true;
const INSTALL  = false;
const IN_ADMIN = true;

require('../common.' . substr(strrchr(__FILE__, '.'), 1));

SnTemplate::messageBoxAdminAccessDenied(3);

global $config, $lang, $user;

if ($user['authlevel'] < 3) {
  sys_redirect(SN_ROOT_VIRTUAL . 'admin/banned.php');
}

ini_set('memory_limit', SN::$config->stats_php_memory ?: '256M');

lng_include('admin');

$is_players_online_page = defined('ADMIN_USER_OVERVIEW') && ADMIN_USER_OVERVIEW === true;

$sort_fields = array(
  SORT_ID              => 'id',
  SORT_NAME            => 'username',
  SORT_EMAIL           => 'email',
  SORT_IP              => 'user_lastip',
  SORT_TIME_REGISTERED => 'register_time',
  SORT_TIME_LAST_VISIT => 'onlinetime',
  SORT_TIME_BAN_UNTIL  => 'banaday',
  SORT_REFERRAL_COUNT  => 'referral_count',
  SORT_REFERRAL_DM     => 'referral_dm',
  SORT_VACATION        => 'vacation',
);

if (empty(sys_get_param('sort'))) {
  $sort = SORT_TIME_LAST_VISIT;
  $desc = true;
} else {
  $sort = sys_get_param_int('sort', SORT_ID);
  $sort = ($sort_fields[$sort] ?? null) ? $sort : SORT_ID;

  $desc = (bool)(sys_get_param_int('desc') ? 1 : 0);
}

if (($action = sys_get_param_int('action')) && ($user_id = sys_get_param_id('uid')) && ($user_selected = db_user_by_id($user_id, false))) {
  if ($user_selected['authlevel'] < $user['authlevel'] && $user['authlevel'] >= 3) {
    switch ($action) {
      case ACTION_DELETE:
        PlayerStatic::DeleteSelectedUser($user_id);
        sys_redirect("{$_SERVER['SCRIPT_NAME']}?sort={$sort}&desc=" . (int)$desc);
      break;

      case ACTION_USE:
        // Impersonate
        SN::$auth->impersonate($user_selected);
      break;
    }
  } else {
    // Restricted try to delete user higher or equal level
    SnTemplate::messageBoxAdmin($lang['adm_err_denied']);
  }
}

/** @noinspection SpellCheckingInspection */
$template = SnTemplate::gettemplate('admin/userlist', true);

$multi_ip = array();
$ip_query = db_user_list_admin_multi_accounts();
while ($ip = db_fetch($ip_query)) {
  $multi_ip[$ip['user_lastip']] = $ip['ip_count'];
}

$geoIp = geoip_status();

$query = db_user_list_admin_sorted($sort_fields[$sort], $is_players_online_page, $desc);
while ($user_row = db_fetch($query)) {
  if ($user_row['banaday']) {
    $ban_details = doquery("SELECT * FROM {{banned}} WHERE `ban_user_id` = {$user_row['id']} ORDER BY ban_id DESC LIMIT 1", true);
  } else {
    $ban_details = [
      'ban_time'        => 0,
      'ban_issuer_name' => '',
      'ban_reason'      => '',
    ];
  }

  $geoIpInfo = $geoIp ? geoip_ip_info(ip2longu($user_row['user_lastip'])) : array();
  foreach ($geoIpInfo as $key => $value) {
    $geoIpInfo[strtoupper($key)] = $value;
    unset($geoIpInfo[$key]);
  }

  $template->assign_block_vars('user', array(
      'ID'              => $user_row['id'],
      'NAME'            => $renderedNick = player_nick_render_to_html($user_row, ['player_rank' => true, 'vacancy' => true, 'birthday' => true, 'award' => true, NICK_RANK_NO_TEXT => true,]),
      'NAME_HTML'       => htmlentities($user_row['username'], ENT_QUOTES, 'UTF-8'),
      'IP'              => $user_row['user_lastip'],
      'IP_MULTI'        => intval($multi_ip[$user_row['user_lastip']]),
      'TIME_REGISTERED' => date(FMT_DATE_TIME_SQL, $user_row['register_time']),
      'TIME_PLAYED'     => date(FMT_DATE_TIME_SQL, $user_row['onlinetime']),
      'ACTIVITY'        => pretty_time(SN_TIME_NOW - $user_row['onlinetime']),
      'REFERRAL_COUNT'  => $user_row['referral_count'],
      'REFERRAL_DM'     => HelperString::numberFloorAndFormat($user_row['referral_dm']),
      'BANNED'          => $user_row['banaday'] ? date(FMT_DATE_TIME_SQL, $user_row['banaday']) : 0,
      'BAN_DATE'        => date(FMT_DATE_TIME_SQL, $ban_details['ban_time']),
      'BAN_ISSUER'      => $ban_details['ban_issuer_name'],
      'BAN_REASON'      => $ban_details['ban_reason'],
      'METAMATTER'      => HelperString::numberFloorAndFormat($user_row['metamatter_total']),
      'ACTION'          => $user_row['authlevel'] < $user['authlevel'],
      'RESTRICTED'      => $user['authlevel'] < 3,
      'EMAIL'           => $user_row['email_2'],
      'VACATION'        => $user_row['vacation'] ? date(FMT_DATE_TIME_SQL, $user_row['vacation']) : '-',
    ) + $geoIpInfo);
}

/** @noinspection SpellCheckingInspection */
$isMetaMatter = !empty(SN::$gc->modules->getModule('unit_res_metamatter'));
$template->assign_vars([
  'USER_COUNT'      => SN::$db->db_num_rows($query),
  'SORT'            => $sort,
  'DESC'            => (int)$desc,
  'GEOIP'           => $geoIp,
  'METAMATTER'      => $isMetaMatter,
  'GEOIP_WHOIS_URL' => SN::$config->geoip_whois_url,

  'PAGE_URL'    => $_SERVER['SCRIPT_NAME'],
  'PAGE_HEADER' => $is_players_online_page ? $lang['adm_ul_title_online'] : $lang['adm_ul_title'],
]);

/** @formatter:off */
$template->assign_recursive([
  '.'    => [
    'header' => [
      ['.' => ['header_row' => [
        ['TITLE' => $lang['sys_id'],                 'ROWSPAN' => 2, 'COLSPAN' => 1, 'SORT' => SORT_ID,],
        ['TITLE' => $lang['sys_user_name'],          'ROWSPAN' => 2, 'COLSPAN' => 1, 'SORT' => SORT_NAME,],
        $isMetaMatter ? ['TITLE' => $lang['sys_metamatter_sh'],      'ROWSPAN' => 2, 'COLSPAN' => 1, 'SORT' => SORT_NONE,] : [],
        ['TITLE' => $lang['adm_ul_referral'],        'ROWSPAN' => 1, 'COLSPAN' => 2, 'SORT' => SORT_NONE,],
        ['TITLE' => $lang['adm_ul_time_registered'], 'ROWSPAN' => 2, 'COLSPAN' => 1, 'SORT' => SORT_TIME_REGISTERED,],
        ['TITLE' => $lang['adm_ul_time_played'],     'ROWSPAN' => 2, 'COLSPAN' => 1, 'SORT' => SORT_TIME_LAST_VISIT,],
        ['TITLE' => $lang['adm_ov_activ'],           'ROWSPAN' => 2, 'COLSPAN' => 1, 'SORT' => SORT_NONE,],
        ['TITLE' => $lang['adm_ul_time_banned'],     'ROWSPAN' => 2, 'COLSPAN' => 1, 'SORT' => SORT_TIME_BAN_UNTIL,],
        ['TITLE' => $lang['sys_vacation_in'],        'ROWSPAN' => 2, 'COLSPAN' => 1, 'SORT' => SORT_VACATION,],
        ['TITLE' => $lang['adm_ul_mail'],            'ROWSPAN' => 2, 'COLSPAN' => 1, 'SORT' => SORT_EMAIL,],
        ['TITLE' => $lang['sys_ip'],                 'ROWSPAN' => 2, 'COLSPAN' => 1, 'SORT' => SORT_IP,],
        $geoIp ? ['TITLE' => $lang['geoip'],                  'ROWSPAN' => 2, 'COLSPAN' => 1, 'SORT' => SORT_NONE,] : [],
        ['TITLE' => $lang['adm_sys_actions'],        'ROWSPAN' => 2, 'COLSPAN' => 1, 'SORT' => SORT_NONE,],
      ]]],
      ['.' => ['header_row' => [
        ['TITLE' => $lang['adm_ul_players'], 'SORT' => SORT_REFERRAL_COUNT,],
        ['TITLE' => $lang['adm_ul_dms'], 'SORT' => SORT_REFERRAL_DM,],
      ]]],
    ],
  ],
]);
/** @formatter:on */

SnTemplate::display($template);
