<?php

// ----------------------------------------------------------------------------------------------------------------
//
// Gestion de la localisation des chaines
//
function lng_include($filename, $ext = '.mo')
{
  global $lang, $user;

  $SelLanguage = $user['lang'] ? $user['lang'] : DEFAULT_LANG;
  include_once(SN_ROOT_PHYSICAL . "language/{$SelLanguage}/{$filename}{$ext}");
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
      if (file_exists($path . $entry . '/language.mo'))
      {
        include($path . $entry . '/language.mo');
        if ($lang_info['LANG_NAME_ISO2'] == $entry)
        {
          $lang_list[$lang_info['LANG_NAME_ISO2']] = $lang_info;
        }
      }
    }
  }
  $dir->close();

  return $lang_list;
}

?>