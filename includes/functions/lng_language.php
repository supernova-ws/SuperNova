<?php

// ----------------------------------------------------------------------------------------------------------------
//
// Gestion de la localisation des chaines
//
function lng_include($filename, $ext = '.mo')
{
  global $lang, $language;
  $SelLanguage = $language ? $language : DEFAULT_LANG;
  require(SN_ROOT_PHYSICAL . "language/{$SelLanguage}/{$filename}{$ext}");
}

function lng_get_list()
{
  $lang_list = array();

  $path = SN_ROOT_PHYSICAL . "language/";
  $dir = dir($path);
  while (false !== ($entry = $dir->read()))
  {
    if (is_dir($path . $entry) && $entry[0] != ".")
    {
      $lang_info = lng_get_info($entry);
      if ($lang_info['LANG_NAME_ISO2'] == $entry)
      {
        $lang_list[$lang_info['LANG_NAME_ISO2']] = $lang_info;
      }
    }
  }
  $dir->close();

  return $lang_list;
}

function lng_get_info($entry)
{
  $file_name = SN_ROOT_PHYSICAL . "language/" . $entry . '/language.mo';
  $lang_info = array();
  if (file_exists($file_name))
  {
    include($file_name);
  }
  return($lang_info);
}

function lng_switch($language_new)
{
  global $lang, $language, $user;

//  $lang = array();
  $language_new = $language_new ? $language_new : ($user['lang'] ? $user['lang'] : DEFAULT_LANG);
  if($language_new != $language)
  {
    $language = $language_new;
    $lang['LANG_INFO'] = lng_get_info($language_new);

    lng_include('system');
    lng_include('tech');
  }
}

?>
