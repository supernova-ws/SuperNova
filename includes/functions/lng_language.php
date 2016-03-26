<?php

// ----------------------------------------------------------------------------------------------------------------
function lng_try_filepath($path, $file_path_relative)
{
  $file_path = SN_ROOT_PHYSICAL . ($path && file_exists(SN_ROOT_PHYSICAL . $path . $file_path_relative) ? $path : '') . $file_path_relative;
  return file_exists($file_path) ? $file_path : false;
}

function lng_die_not_an_object()
{
  print('Ошибка - lang не объект! Сообщите Администратору сервера и приложите содержимое страницы');
  $trace = debug_backtrace();
  unset($trace[0]);
  pdump($trace);
  return die();
}

// ----------------------------------------------------------------------------------------------------------------
function lng_include($filename, $path = '', $ext = '.mo.php')
{
  return is_object(classLocale::$lang) ? classLocale::$lang->lng_include($filename, $path, $ext) : lng_die_not_an_object();
}

function lng_get_list()
{
  return is_object(classLocale::$lang) ? classLocale::$lang->lng_get_list() : lng_die_not_an_object();
}

function lng_get_info($entry)
{
  return is_object(classLocale::$lang) ? classLocale::$lang->lng_get_info($entry) : lng_die_not_an_object();
}

function lng_switch($language_new)
{
  return is_object(classLocale::$lang) ? classLocale::$lang->lng_switch($language_new) : lng_die_not_an_object();
}

function lng_load_i18n($i18n)
{
  return is_object(classLocale::$lang) ? classLocale::$lang->lng_load_i18n($i18n) : lng_die_not_an_object();
}
