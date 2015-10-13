<?php

global $supernova;

$supernova->design['bbcodes'] = array(
//  "^\[c=(white|blue|yellow|green|pink|red|orange|purple)\](.+)\[/c\]$" => "<font color=\"$1\">$2</font>",
  AUTH_LEVEL_ADMINISTRATOR => array(
    "#\[url=(ft|https?://)(.+)\](.+)\[/url\]#isU" => "<a href=\"$1$2\" target=\"_blank\" class=\"link\">$3</a>",
//    "#(?:^(?:href=\\\"))((?:ftp|https?)://.+)#i" => "<a href=\"$1$2\" target=\"_blank\" class=\"link\">$1$2</a>",
    "#^((?:ftp|https?|sn?)://[^\s\[]+)#i" => "<a href=\"$1$2\" target=\"_blank\" class=\"link\">$1$2</a>",
    "#([\s\)\]\}])((?:ftp|https?|sn)://[^\s\[]+)#i" => "$1<a href=\"$2$3\" target=\"_blank\" class=\"link\">$2$3</a>",
//    "#^((?:ftp|https?)://.+?)$#i" => "<a href=\"$1$2\" target=\"_blank\" class=\"link\">$1$2</a>",
//    "#\s((?:ftp|https?)://.+)$#i" => "<a href=\"$1$2\" target=\"_blank\" class=\"link\">$1$2</a>",
//    "#^((?:ftp|https?)://.+)\s#i" => "<a href=\"$1$2\" target=\"_blank\" class=\"link\">$1$2</a>",
//    "#\s((?:ftp|https?)://.+)?\s#i" => "<a href=\"$1$2\" target=\"_blank\" class=\"link\">$1$2</a>",
    "#\[c=(\#[0-9A-Fa-f]+|purple)\](.+)\[/c\]#isU" => "<span style=\"color: $1\">$2</span>",
  ),

  AUTH_LEVEL_REGISTERED => array(
    "#\[c=(white|cyan|yellow|green|pink|red|lime|maroon|orange)\](.+)\[/c\]#isU" => "<span style=\"color: $1\">$2</span>",
    "#\[b\](.+)\[/b\]#isU" => "<b>$1</b>",
    "#\[i\](.+)\[/i\]#isU" => "<i>$1</i>",
    "#\[u\](.+)\[/u\]#isU" => '<span style="text-decoration: underline;">$1</span>',
    "#\[s\](.+)\[/s\]#isU" => '<span style="text-decoration: line-through;">$1</span>',
    "#\[ube\=([0-9a-zA-Z]{32})\]#isU" => "<a href=\"index.php?page=battle_report&cypher=$1\" target=_new><span class=\"battle_report_link\">($1)</span></a>",
  ),
);

$supernova->design['smiles'] = array(
  AUTH_LEVEL_REGISTERED => array(
    ':)' => 'smile',
    ':p:' => 'tongue',
    //':D' => 'lol',
    'rofl' => 'rofl',
    ':wink:' => 'wink',
    ':clap:' => 'clapping',
    ':good:' => 'good',
    ':yu:' => 'yu',
    ':yahoo:' => 'yahoo',
    ':diablo:' => 'diablo',
    ':angel:' => 'angel',
    ':rose:' => 'give_rose',

    ':blush:' => 'blush',
    ':sorry:' => 'sorry',
    ':cool:' => 'cool',
    ':cool2:' => 'dirol',
    ':quote:' => 'pleasantry',
    ':shout:' => 'shout',
    ':unknw:' => 'unknw',
    ':ups:' => 'pardon',
    ':nea:' => 'nea',
    ':sarcasm:' => 'sarcasm',
    ':shok:' => 'shok',
    ':blink:' => 'blink',

    ':huh:' => 'huh',
    ':(' => 'mellow',
    ':sad:' => 'sad',
    ':c:' => 'cray',

    ':bad:' => 'bad',
    ':eye:' => 'blackeye',
    ':bomb:' => 'bomb',
    ':crz:' => 'crazy',
    ':fool:' => 'fool',
    //  ':wink:' => 'wink',
    ':tease:' => 'tease',

    ':spiteful:' => 'spiteful',
    ':agr:' => 'aggressive',
    // ':tratata:' => 'mill',
    ':wall:' => 'wall',
    ':suicide:' => 'suicide',
    ':plushit:' => 'plushit',

    ':fr:' => 'friends',
    ':dr:' => 'drinks',
    ':popcorn:' => 'popcorn',
    ':coctail:' => 'coctail',
    ':coffee:' => 'coffee',

    ':accordion:' => 'accordion',
    ':hmm:' => 'hmm',
    ':facepalm:' => 'facepalm',
    ':ban:' => 'ban',
    // ':bayan:' => 'bayan',
    ':censored:' => 'censored',
    ':contract:' => 'contract',
    ':help:' => 'help',
    // ':maniac:' => 'maniac',
    ':panic:' => 'panic',
    ':poke:' => 'poke',
    ':pray:' => 'pray',
    ':whistle:' => 'whistle',
  ),
);

function cht_message_parse($msg, $escaped = false, $author_auth = 0) {
  global $supernova;

  // $user_auth_level = isset($user['authlevel']) ? $user['authlevel'] : AUTH_LEVEL_ANONYMOUS;

  $msg = htmlentities($msg, ENT_COMPAT, 'UTF-8');

  $msg = str_replace('sn://', SN_ROOT_VIRTUAL, $msg);

  foreach($supernova->design['bbcodes'] as $auth_level => $replaces) {
    if($auth_level > $author_auth) {
      continue;
    }

    foreach($replaces as $key => $html) {
      $msg = preg_replace(''.$key.'', $html, $msg);
    }
  }

  foreach($supernova->design['smiles'] as $auth_level => $replaces) {
    if($auth_level > $author_auth) {
      continue;
    }

    foreach($replaces as $key => $imgName) {
      $msg = preg_replace("#" . addcslashes($key, '()[]{}') . "#isU","<img src=\"design/images/smileys/".$imgName.".gif\" align=\"absmiddle\" title=\"".$key."\" alt=\"".$key."\">",$msg);
    }
  }

  return str_replace($escaped ? '\r\n' : "\r\n", '<br />', $msg);
}

function sys_bbcodeParse($text, $escaped = false, $author_auth = 0) {
  return str_replace($escaped ? '\r\n' : "\r\n", '<br />', $text);
//  return cht_message_parse($text, $escaped, $author_auth);
}

function sys_bbcodeUnParse($text, $escaped = false, $author_auth = 0)
{
  return str_replace('<br />', $escaped ? '\r\n' : "\r\n", $text);
}
