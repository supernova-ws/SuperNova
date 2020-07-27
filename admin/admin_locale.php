<?php

/**
 * adm_locale.php
 *
 * @v1 (c) copyright 2011 by Gorlum for http://supernova.ws
 *
 */

define('INSIDE'  , true);
define('INSTALL' , false);
define('IN_ADMIN'  , true);

require('../common.' . substr(strrchr(__FILE__, '.'), 1));

global $lang, $user;

SnTemplate::messageBoxAdminAccessDenied(AUTH_LEVEL_ADMINISTRATOR);

function adm_lng_assign_string($lang_id, $locale_string_name, $value) {
  global $locale_string_template, $languages_info, $languages, $domain;

  if(is_array($value)) {
    foreach($value as $sub_key => $sub_value) {
      adm_lng_assign_string($lang_id, "{$locale_string_name}[{$sub_key}]", $sub_value);
    }
  } elseif($value) {
    if(!isset($locale_string_template[$locale_string_name])) {
      $locale_string_template[$locale_string_name] = array();
    }
    $locale_string_template[$locale_string_name] = array_merge($locale_string_template[$locale_string_name], array("[{$lang_id}]" => htmlentities($value, ENT_COMPAT, 'utf-8')));
  }
}

function adm_lng_load($full_filename) {
//  $lang_old = $lang;
//  $lang = array();
  require($full_filename);
//  $lang_new = $lang;
//  $lang = $lang_old;
//  return $lang_new;
  return $a_lang_array;
}

function adm_lng_parse_string($string_name, $string_value, $ident = '  ') {
  global $domain, $lang_id;

  $return = "{$ident}'{$string_name}' => ";
  if(isset($string_value[$lang_id]) && !is_array($string_value[$lang_id])) {
    $return .= "'" . str_replace(array("\\", "'"), array('\\\\', "\\'"), $string_value[$lang_id]) . "',";
  } else {
    $return .= "array(\r\n";
    foreach($string_value as $arr_name => $arr_data) {
      $return .= adm_lng_parse_string($arr_name, $arr_data, $ident . '  ');
    }
    $return .= "{$ident}),\r\n";
  }

  return $return . "\r\n";
}

$honor_constants = array(
  'admin' => array(
    '[adm_opt_ver_response]' => 'SNC_VER_',
    '[adm_opt_ver_response_short]' => 'SNC_VER_',
  ),

  'alliance' => array(
    '[ali_dip_relations]' => 'ALLY_DIPLOMACY_',
  ),

  'artifacts' => array(
    '[art_moon_create]' => 'ART_',
  ),

  'fleet' => array(
    '[fl_attack_error]' => 'ATTACK_',
    '[fl_shrtcup]' => 'PT_',
    '[fl_planettype]' => 'PT_',
  ),

  'infos' => array(
    '[info]' => array('TECH_', 'MRC_', 'SHIP_', 'RES_', 'ART_', 'STRUC_'),
  ),

  'market' => array(
    '[eco_mrk_errors]' => 'MARKET_', 
  ),

  'quest' => array(
    '[qst_status_list]' => 'QUEST_STATUS_', 
  ),

  'tech' => array(
    '[type_mission]' => 'MT_', 
    '[tech]' => array('TECH_', 'MRC_', 'SHIP_', 'RES_', 'ART_', 'STRUC_'),
  ),

);

function adm_lng_write_string($string_name, $string_value, $ident = '  ', $string_name_prefix = '') {
  global $lang_id, $file_handler, $constants, $honor_constants, $domain;

  $string_name_new = false;

  if(isset($honor_constants[$domain][$string_name_prefix])) {
    $found_constants = array_keys($constants, $string_name);
    foreach($found_constants as $constant_name) {
      $honor_prefix_list = is_array($honor_constants[$domain][$string_name_prefix]) ? $honor_constants[$domain][$string_name_prefix] : array($honor_constants[$domain][$string_name_prefix]);
      foreach($honor_prefix_list as $honor_prefix) {
        if(strpos($constant_name, $honor_prefix) === 0) {
          $string_name_new = $constant_name;
          break;
        }
      }
    }
  }

  $string_name_new = $string_name_new ? $string_name_new : "'{$string_name}'";
  fwrite($file_handler, "{$ident}{$string_name_new} => ");
  if(isset($string_value[$lang_id]) && !is_array($string_value[$lang_id])) {
    fwrite($file_handler, "'" . str_replace(array("\\", "'"), array('\\\\', "\\'"), $string_value[$lang_id]) . "',");
//    fwrite($file_handler, "'" . addslashes($string_value[$lang_id]) . "',");
  } else {
    $string_name_prefix = $string_name_prefix . "[{$string_name}]";
    fwrite($file_handler, "array(\r\n");
    foreach($string_value as $arr_name => $arr_data) {
      adm_lng_write_string($arr_name, $arr_data, $ident . '  ', $string_name_prefix);
    }
    fwrite($file_handler, "{$ident}),\r\n");
  }

  fwrite($file_handler, "\r\n");
}

$template = SnTemplate::gettemplate('admin/admin_locale', true);

lng_include('system');
lng_include('tech');
lng_include('admin');

$languages = array();
$language_domains = array();
$languages_info = lng_get_list();
$domain = sys_get_param_str('domain');

if($domain) {
  $lang_new = sys_get_param('lang_new');
  if(!empty($lang_new) && is_array($lang_new)) {
    $constants = get_defined_constants(true);
    $constants = $constants['user'];
    ksort($constants);
    foreach($languages_info as $lang_id => $land_data) {
      $file_handler = fopen(SN_ROOT_PHYSICAL . "language/{$lang_id}/{$domain}.mo.php.new", 'w');
      fwrite($file_handler, "<?php\r\n\r\n/*\r\n#############################################################################
#  Filename: {$domain}.mo.php
#  Project: SuperNova.WS
#  Website: http://www.supernova.ws
#  Description: Massive Multiplayer Online Browser Space Strategy Game\r\n#\r\n");

      foreach($land_data['LANG_COPYRIGHT'] as $lang_copyright) {
        $lang_copyright = str_replace(array('&copy;', '&quot;', '&lt;', '&gt;'), array('©', '"', '<', '>'), $lang_copyright);
        fwrite($file_handler, "#  {$lang_copyright}\r\n");
      }
      fwrite($file_handler, "#############################################################################\r\n*/\r\n
/**\r\n*\r\n* @package language\r\n* @system [{$land_data['LANG_NAME_ENGLISH']}]\r\n* @version " . SN_VERSION . "\r\n*\r\n*/\r\n
/**\r\n* DO NOT CHANGE\r\n*/\r\n\r\nif (!defined('INSIDE')) die();\r\n
\$a_lang_array = array(\r\n");
      foreach($lang_new as $string_name => $string_value) {
        adm_lng_write_string($string_name, $string_value);
      }
      fwrite($file_handler, ");\r\n");
      fclose($file_handler);
    }

    sys_redirect("admin_locale.php?domain={$domain}");
  }

  foreach($languages_info as $lang_id => $lang_data) {
    $template->assign_block_vars('language', $lang_data);
    $full_filename = SN_ROOT_PHYSICAL . "language/{$lang_id}/{$domain}.mo.php";
    $languages[$lang_id] = adm_lng_load($full_filename . (file_exists($full_filename . '.new') ? '.new' : ''));
    foreach($languages[$lang_id] as $locale_string_name => $cork) {
      adm_lng_assign_string($lang_id, "[{$locale_string_name}]", $languages[$lang_id][$locale_string_name]);
    }
  }

  foreach($locale_string_template as $locale_string_name => $locale_string_list) {
    $template->assign_block_vars('string', array(
      'NAME' => $locale_string_name,
    ));

    foreach($languages_info as $lang_id => $cork2) {
      $template->assign_block_vars('string.locale', array(
        'LANG' => $lang_id,
        'VALUE' => $locale_string_list["[{$lang_id}]"],
      ));
    }
  }

  $template->assign_vars(array(
    'DOMAIN' => $domain,
  ));
} else {
  $path = SN_ROOT_PHYSICAL . "language/";
  $dir = dir($path);
  while (false !== ($lang_id = $dir->read())) {
    $full_path = $path . $lang_id;
    if($lang_id[0] != "." && is_dir($full_path)) {
      $lang_file_list = dir($full_path);
      while (false !== ($filename = $lang_file_list->read())) {
        $lang_domain = strtolower(substr($filename, 0, strpos($filename, '.')));
        if(!$lang_domain) {
          continue;
        }

        $file_ext = strtolower(substr($filename, strpos($filename, '.')));
        if($lang_domain != 'language') {
          if($file_ext == '.mo.php.new' || ($file_ext == '.mo.php' && empty($languages[$lang_id][$lang_domain]))) {
            $language_domains[$lang_domain] = $lang_domain;
            $languages[$lang_id][$lang_domain] = $lang_domain;
          }
        }
      }
    }
  }
  $dir->close();

  foreach($language_domains as $lang_domain) {
    $template->assign_block_vars('domain', array(
      'NAME' => $lang_domain,
    ));
  }
}

SnTemplate::display($template, $lang['adm_lng_title']);
