<?php
/**
 * erreurs.php
 *
 * @version 1.0
 * @copyright 2009 by Gorlum for http://oGame.Triolan.COM.UA
 */

define('INSIDE', true);
define('INSTALL', false);
define('IN_ADMIN', true);
require('../common.' . substr(strrchr(__FILE__, '.'), 1));

global $lang, $user, $template_result;

SnTemplate::messageBoxAdminAccessDenied(AUTH_LEVEL_ADMINISTRATOR);

$mode = sys_get_param_int('mode');

switch ($mode) {
  case ADM_COUNTER_RECALC:
    $template_result['.']['result'][] = [
      'STATUS'  => ERR_NONE,
      'MESSAGE' => number_format(memory_get_usage()) . ' - memory Before',
    ];
    $t = new \General\LogCounterShrinker(SN::$gc);
    $template_result['.']['result'][] = $t->process();
    unset($t);
    $template_result['.']['result'][] = [
      'STATUS'  => ERR_NONE,
      'MESSAGE' => number_format(memory_get_usage()) . ' - memory After',
    ];
  break;

  case ADM_TOOL_CONFIG_RELOAD:
    SN::$config->db_loadAll();
    SN::$db->schema()->clear();

    SN::$config->pass()->game_watchlist;
    if (SN::$config->game_watchlist) {
      SN::$config->game_watchlist_array = explode(';', SN::$config->game_watchlist);
    } else {
      unset(SN::$config->game_watchlist_array);
    }
  break;

  case ADM_TOOL_MD5:
    $template = SnTemplate::gettemplate("admin/md5enc", true);
    $password_seed = sys_get_param_str_unsafe('seed', SN_SYS_SEC_CHARS_ALLOWED);
    $password_length = sys_get_param_int('length', 16);
    $string = ($string = sys_get_param_str_unsafe('string')) ? $string : sys_random_string($password_length, $password_seed);

    $template->assign_vars(array(
      'SEED'   => $password_seed,
      'LENGTH' => $password_length,
      'STRING' => htmlentities($string),
      'MD5'    => md5($string),
    ));
    SnTemplate::display($template, $lang['adm_tools_md5_header']);
  break;

  case ADM_TOOL_FORCE_ALL:
    SN::$config->db_saveItem('db_version', 40);
    require_once('../includes/update.php');
  break;

  case ADM_TOOL_FORCE_LAST:
    SN::$config->db_saveItem('db_version', floor(SN::$config->db_version - 1));
    require_once('../includes/update.php');
  break;

  case ADM_TOOL_INFO_PHP:
    phpinfo();
  break;

  case ADM_TOOL_INFO_SQL:
    $template = SnTemplate::gettemplate("simple_table", true);

    $template->assign_block_vars('table', $lang['adm_tool_sql_table']['server']);
    $status = array(
      $lang['adm_tool_sql_server_version'] => db_get_server_info(),
      $lang['adm_tool_sql_client_version'] => db_get_client_info(),
      $lang['adm_tool_sql_host_info']      => db_get_host_info(),
    );
    foreach ($status as $key => $value) {
      $template->assign_block_vars('table.row', array(
        'VALUE_1' => $key,
        'VALUE_2' => $value,
      ));
    }

    $template->assign_block_vars('table', $lang['adm_tool_sql_table']['status']);
    $status = explode('  ', db_server_stat());
    foreach ($status as $value) {
      $row = explode(': ', $value);
      $template->assign_block_vars('table.row', array(
        'VALUE_1' => $row[0],
        'VALUE_2' => $row[1],
      ));
    }


    $template->assign_block_vars('table', $lang['adm_tool_sql_table']['params']);
    $result = doquery('SHOW STATUS;');
    while ($row = db_fetch($result)) {
      $template->assign_block_vars('table.row', array(
        'VALUE_1' => $row['Variable_name'],
        'VALUE_2' => $row['Value'],
      ));
    }

    $template->assign_vars(array(
      'PAGE_HEADER'   => $lang['adm_tool_sql_page_header'],
      'COLUMN_NAME_1' => $lang['adm_tool_sql_param_name'],
      'COLUMN_NAME_2' => $lang['adm_tool_sql_param_value'],
      'TABLE_FOOTER'  => 'test',
    ));

    SnTemplate::display($template);
  break;

  case ADM_PTL_TEST:
    $template = SnTemplate::gettemplate("admin/admin_ptl_test", true);

    $template->assign_vars(array(
      'PAGE_TITLE' => $lang['adm_ptl_test'],

      'VAR'                => 'VALUE',
      'RENDER_VAR'         => '{VAR}',
      'RENDER_DEFINED_VAR' => '{$VAR}',


      'VAR_VALUE' => 'VAR_VALUE',

      'RENDER_VAR_VALUE'       => '{VAR_VALUE}',
      'RENDER_NAVBAR_RESEARCH' => '{I_navbar_research|html}',
    ));

    $template->assign_block_vars('render_test_block', array(
      'BLOCK_VAR' => '{VAR}',
    ));


    $tests = array(
      array('HEADER' => '{VAR} and {$VAR} Variables'),
      array(
        'SAMPLE'      => '{VAR}',
        'EXPECTED'    => 'VALUE',
        'DESCRIPTION' => 'Root variable - existing',
      ),
      array(
        'SAMPLE'      => '{VAR_NOT_EXISTS}',
        'EXPECTED'    => '',
        'DESCRIPTION' => 'Root variable - non-existing',
      ),
      array(
        'SAMPLE'      => '{АБВГД}',
        'EXPECTED'    => '{АБВГД}',
        'DESCRIPTION' => 'Root variable - wrong name',
      ),
      array(
        'SAMPLE'      => '{$VAR}',
        'EXPECTED'    => '$VALUE',
        'DESCRIPTION' => 'DEFINE-d variable - existing',
      ),
      array(
        'SAMPLE'      => '{$VAR_NOT_EXISTS}',
        'EXPECTED'    => '',
        'DESCRIPTION' => 'DEFINE-d variable - non-existing',
      ),
      array(
        'SAMPLE'      => '{$АБВГД}',
        'EXPECTED'    => '{$АБВГД}',
        'DESCRIPTION' => 'DEFINE-d variable - wrong name',
      ),

      array('HEADER' => '{C_xxx} - Config'),
      array(
        'SAMPLE'      => '{C___ptl_test}',
        'EXPECTED'    => 'config_ptl_test',
        'DESCRIPTION' => 'Config variable',
      ),
      array(
        'SAMPLE'      => '{C___ptl_test_array[value]}',
        'EXPECTED'    => 'config_ptl_test_array',
        'DESCRIPTION' => 'Config array variable',
      ),

      array('HEADER' => '{L_xxx} and {LA_xxx} - Language'),
      array(
        'SAMPLE'      => '{L_admin_ptl_test_la_}',
        'EXPECTED'    => 'Single\'Double"ZeroEnd',
        'DESCRIPTION' => 'Language string',
      ),
      array(
        'SAMPLE'      => '{LA_admin_ptl_test_la_}',
        'EXPECTED'    => 'Single\\\'Double\"Zero\0End',
        'DESCRIPTION' => 'JavaScript-safe language string',
      ),
      array(
        'SAMPLE'      => '{L_surely_not_exists_string_test}',
        'EXPECTED'    => '{ L_surely_not_exists_string_test }',
        'DESCRIPTION' => 'Language string - non-existing',
      ),
      array(
        'SAMPLE'      => '{LA_surely_not_exists_string_test}',
        'EXPECTED'    => '{ LA_surely_not_exists_string_test }',
        'DESCRIPTION' => 'JS-safe language string - non-existing',
      ),

      array('HEADER' => '{I_xxx} - Image rendering'),
      array(
        'SAMPLE'      => "{" . ($tag = "I_NO_IMAGE|height=\"20%\"|width=\"20%\"") . "}<br />{{$tag}|html}",
        'EXPECTED'    => ($imgPath = SN_ROOT_VIRTUAL . 'design/images/_no_image.png') . "<br /><img src=\"{$imgPath}\" height=\"20%\" width=\"20%\" />",
        'DESCRIPTION' => 'Image - not existing',
      ),

      array(
        'SAMPLE'      => "{" . ($tag = "I_/design/images/icon_note_pinned_64x64.png") . "}<br />{{$tag}|html}",
        'EXPECTED'    => ($imgPath = SN_ROOT_VIRTUAL . 'design/images/icon_note_pinned_64x64.png') . "<br /><img src=\"{$imgPath}\" />",
        'DESCRIPTION' => 'Direct image access by absolute path',
      ),
      array(
        'SAMPLE'      => "{" . ($tag = "I_images/border_small.png") . "}<br />{{$tag}|html}",
        'EXPECTED'    => ($imgPath = SN_ROOT_VIRTUAL . 'skins/EpicBlue/images/border_small.png') . "<br /><img src=\"{$imgPath}\" />",
        'DESCRIPTION' => 'Access image in skin by relative path',
      ),
      array(
        'SAMPLE'      => "{" . ($tag = "I_navbar_research") . "}<br />{{$tag}|html}",
        'EXPECTED'    => ($imgPath = SN_ROOT_VIRTUAL . 'design/images/navbar_research_64x64.png') . "<br /><img src=\"{$imgPath}\" />",
        'DESCRIPTION' => 'Image direct access by ID in skin.ini',
      ),
      array(
        'SAMPLE'      => "{" . ($tag = "I_navbar_research|skin=supernova-ivash") . "}<br />{{$tag}|html}",
        'EXPECTED'    => ($imgPath = SN_ROOT_VIRTUAL . 'skins/supernova-ivash/navbar/navbar_research_64x64.png') . "<br /><img src=\"{$imgPath}\" />",
        'DESCRIPTION' => 'Param \'skin\' - get image by Image ID from other skin',
      ),
      array(
        'SAMPLE'      => "{" . ($tag = "I_navbar_research|height=\"20%\"|width=\"20%\"") . "}<br />{{$tag}|html}",
        'EXPECTED'    => ($imgPath = SN_ROOT_VIRTUAL . 'design/images/navbar_research_64x64.png') . "<br /><img src=\"{$imgPath}\" height=\"20%\" width=\"20%\" />",
        'DESCRIPTION' => 'Image attributes - height 20%, width 20%',
      ),
      array(
        'SAMPLE'      => "{" . ($tag = "I_navbar_research|skin=supernova-ivash|height=\"40px\"") . "}<br />{{$tag}|html}",
        'EXPECTED'    => ($imgPath = SN_ROOT_VIRTUAL . 'skins/supernova-ivash/navbar/navbar_research_64x64.png') . "<br /><img src=\"{$imgPath}\" height=\"40px\" />",
        'DESCRIPTION' => 'Param \'skin\' with other params',
      ),

      array(
        'SAMPLE'      => "{" . ($tag = "I_[\$BLACK]") . "}<br />{{$tag}|html}",
        'EXPECTED'    => ($imgPath = SN_ROOT_VIRTUAL . 'skins/EpicBlue/planeten/black_moon.jpg') . "<br /><img src=\"{$imgPath}\" />",
        'DESCRIPTION' => 'DEFINE-d test',
      ),

      array(
        'SAMPLE'      => "{" . ($tag = "I_s_[\$BLACK]") . "}<br />{{$tag}|html}",
        'EXPECTED'    => ($imgPath = SN_ROOT_VIRTUAL . 'skins/EpicBlue/planeten/small/s_black_moon.jpg') . "<br /><img src=\"{$imgPath}\" />",
        'DESCRIPTION' => 'DEFINE-d and prefix test',
      ),

      array(
        'SAMPLE'      => '{R_[RENDER_NAVBAR_RESEARCH]}',
        'EXPECTED'    => '<img src="' . SN_ROOT_VIRTUAL . 'design/images/navbar_research_64x64.png"/>',
        'DESCRIPTION' => 'Re-rendering image',
      ),

      array('HEADER' => 'Blocks'),
    );

//    $tests = array(
//      array('HEADER' => '!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!'),
//      array(
//        'SAMPLE'      => '{L_admin_ptl_test_la_}',
//        'EXPECTED'    => 'Single\'Double"ZeroEnd',
//        'DESCRIPTION' => 'Language string',
//      ),
//    );

    SN::$config->__ptl_test = 'config_ptl_test';
    SN::$config->__ptl_test_array = array('value' => 'config_ptl_test_array');

    foreach ($tests as $test) {
      $test['CONSTRUCTION'] = str_replace(array('{', '}'), array('&#123;', '&#125;'), $test['SAMPLE']);
      $template->assign_block_vars('test', $test);
    }

    SnTemplate::display($template);
  break;
}

$template = SnTemplate::gettemplate("admin/admin_tools", true);
$template->assign_vars(array(
  'PAGE_HEADER' => $lang['adm_tools'],
));

$template->assign_recursive($template_result);

SnTemplate::display($template);
