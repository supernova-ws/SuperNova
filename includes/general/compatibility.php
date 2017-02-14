<?php
/**
 * Created by Gorlum 14.02.2017 11:21
 */

/**
 * Back-compatibility functions
 */

// includes/functions/sys_bbcode.php +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++


function cht_message_parse($msg, $escaped = false, $author_auth = AUTH_LEVEL_REGISTERED) {
  return BBCodeParser::parseStatic($msg, $escaped, $author_auth);
}

function sys_bbcodeParse($text, $escaped = false, $author_auth = AUTH_LEVEL_REGISTERED) {
  return str_replace($escaped ? '\r\n' : "\r\n", '<br />', $text);
}

function sys_bbcodeUnParse($text, $escaped = false, $author_auth = AUTH_LEVEL_REGISTERED)
{
  return str_replace('<br />', $escaped ? '\r\n' : "\r\n", $text);
}
