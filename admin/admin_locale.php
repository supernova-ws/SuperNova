<?php

/**
 * adm_locale.php
 *
 * @v1 (c) copyright 2011 by Gorlum for http://supernova.ws
 *
 */

function adm_lng_assign_string($lang_id, $locale_string_name, $value)
{
  global $locale_string_template, $languages_info, $languages, $domain;

  if(is_array($value))
  {
    foreach($value as $sub_key => $sub_value)
    {
      adm_lng_assign_string($lang_id, "{$locale_string_name}[{$sub_key}]", $sub_value);
    }
  }
  elseif($value)
  {
    if(!isset($locale_string_template[$locale_string_name]))
    {
      $locale_string_template[$locale_string_name] = array();
    }
    $locale_string_template[$locale_string_name] = array_merge($locale_string_template[$locale_string_name], array("[{$lang_id}]" => htmlentities($value, ENT_COMPAT, 'utf-8')));
  }
}

function adm_lng_save_string($string_name, $string_value, $ident = '  ')
{
  global $files, $languages_info;

  $first_element = current($string_value);

  if(is_array($first_element))
  {
    foreach($languages_info as $lang_id => $cork)
    {
      fwrite($files[$lang_id], "{$ident}'{$string_name}' => array(\r\n");
    }
    foreach($string_value as $arr_name => $arr_data)
    {
      adm_lng_save_string($arr_name, $arr_data, $ident . '  ');
    }
    foreach($languages_info as $lang_id => $cork)
    {
      fwrite($files[$lang_id], "{$ident}),\r\n\r\n");
    }
  }
  else
  {
    foreach($languages_info as $lang_id => $cork)
    {
      $safe_string = addslashes($string_value[$lang_id]);
      fwrite($files[$lang_id], "{$ident}'{$string_name}' => '{$safe_string}',\r\n");
    }
  }
}

define('INSIDE'  , true);
define('INSTALL' , false);
define('IN_ADMIN'  , true);
require('../common.' . substr(strrchr(__FILE__, '.'), 1));

$template = gettemplate('admin/admin_locale', true);

function adm_lng_load($full_filename)
{
//  $lang_old = $lang;
  $lang = array();
  require($full_filename);
//  $lang_new = $lang;
//  $lang = $lang_old;
//  return $lang_new;
  return $lang;
}

lng_include('system');
lng_include('tech');
lng_include('admin');

$languages = array();
$language_domains = array();
$languages_info = lng_get_list();

$path = SN_ROOT_PHYSICAL . "language/";
$dir = dir($path);
while (false !== ($lang_id = $dir->read()))
{
  $full_path = $path . $lang_id;
  if($lang_id[0] != "." && is_dir($full_path))
  {
    $lang_file_list = dir($full_path);
    while (false !== ($filename = $lang_file_list->read()))
    {
      if(substr($filename, strlen($filename) - 3, 3) == '.mo')
      {
        $lang_domain = substr($filename, 0, strlen($filename) - 3);
        if($lang_domain != 'language')
        {
          if(empty($languages[$lang_id][$lang_domain]))
          {
            $language_domains[$lang_domain] = $lang_domain;
            $full_filename = "{$full_path}/{$filename}";
            $languages[$lang_id][$lang_domain] = adm_lng_load($full_filename);
          }
        }
        else
        {
        }
      }
      elseif(substr($filename, strlen($filename) - 7, 7) == '.mo.new')
      {
        $lang_domain = substr($filename, 0, strlen($filename) - 7);
        if($lang_domain != 'language')
        {
          $language_domains[$lang_domain] = $lang_domain;
          $full_filename = "{$full_path}/{$filename}";
          $languages[$lang_id][$lang_domain] = adm_lng_load($full_filename);
        }
        else
        {
        }
      }
    }
  }
}
$dir->close();

$domain = sys_get_param_str('domain');

if($domain && !empty($language_domains[$domain]))
{
  $lang_new = sys_get_param('lang_new');
  if(!empty($lang_new))
  {
    $files = array();
    foreach($languages_info as $lang_id => $cork)
    {
      $files[$lang_id] = fopen(SN_ROOT_PHYSICAL . "language/{$lang_id}/{$domain}.mo.new", 'w');
      fwrite($files[$lang_id], "<?php\r\n\r\nif (!defined('INSIDE')) die();\r\n\r\n\$lang = array_merge(\$lang, array(\r\n");
    }

    foreach($lang_new as $string_name => $string_value)
    {
      adm_lng_save_string($string_name, $string_value);
    }

    foreach($languages_info as $lang_id => $cork)
    {
      fwrite($files[$lang_id], "));\r\n\r\n?>\r\n");
      fclose($files[$lang_id]);
    }
    header("Location: admin_locale.php?domain={$domain}");
    die();
  }

  foreach($languages_info as $lang_id => $lang_data)
  {
    $template->assign_block_vars('language', $lang_data);
  }

  foreach($languages['ru'][$domain] as $locale_string_name => $cork)
  {
    foreach($languages_info as $lang_id => $cork2)
    {
      adm_lng_assign_string($lang_id, "[{$locale_string_name}]", $languages[$lang_id][$domain][$locale_string_name]);
    }
  }

  foreach($locale_string_template as $string_id => $cork)
  {
    foreach($languages_info as $lang_id => $cork2)
    {
      if(!isset($locale_string_template[$string_id]["[{$lang_id}]"]))
      {
        $locale_string_template[$string_id]["[{$lang_id}]"] = '';
      }
    }
    ksort($locale_string_template[$string_id]);
  }

  foreach($locale_string_template as $locale_string_name => $locale_string_list)
  {
    $template->assign_block_vars('string', array(
      'NAME' => $locale_string_name,
    ));

    foreach($locale_string_list as $string_lang_id => $string_lang_value)
    {
      $template->assign_block_vars('string.locale', array(
        'LANG' => $string_lang_id,
        'VALUE' => $string_lang_value,
      ));
    }
  }

  $template->assign_vars(array(
    'DOMAIN' => $domain,
  ));
}
else
{
  foreach($language_domains as $lang_domain)
  {
    $template->assign_block_vars('domain', array(
      'NAME' => $lang_domain,
    ));
  }
}

display($template, $lang['adm_lng_title'], false, '', true);

?>
