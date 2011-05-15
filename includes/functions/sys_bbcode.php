<?php

function sys_bbcodeParse($text, $escaped = false)
{
  if($escaped)
  {
    $text = str_replace('\r\n', '<br />', $text);
  }
  else
  {
    $text = str_replace("\r\n", '<br />', $text);
  }

  return $text;
}

function sys_bbcodeUnParse($text, $escaped = false)
{
  if($escaped)
  {
    $text = str_replace('<br />', '\r\n', $text);
  }
  else
  {
    $text = str_replace('<br />', "\r\n", $text);
  }

  return $text;
}

?>
