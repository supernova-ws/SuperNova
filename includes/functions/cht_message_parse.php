<?php

global $supernova;

$supernova->design['bbcodes'] = array(
//  "^\[c=(white|blue|yellow|green|pink|red|orange|purple)\](.+)\[/c\]$" => "<font color=\"$1\">$2</font>",
  "\[c=(white|blue|yellow|green|pink|red|orange|purple)\](.+)\[/c\]" => "<font color=\"$1\">$2</font>",
  "\[a=(ft|https?://)(.+)\](.+)\[/a\]" => "<a href=\"$1$2\" target=\"_blank\"><u>$3</u></a>",
  "\[b\](.+)\[/b\]" => "<b>$1</b>",
  "\[i\](.+)\[/i\]" => "<i>$1</i>",
  "\[u\](.+)\[/u\]" => "<u>$1</u>",
  "\[s\](.+)\[/s\]" => "<strike>$1</strike>",
  "\[ube\=([0-9a-zA-Z]{32})\]" => "<a href=\"index.php?page=battle_report&cypher=$1\" target=_new><span class=\"battle_report_link\">($1)</span></a>",
);

$supernova->design['smiles'] = array(
  ':\)' => 'smile',
  ':p' => 'tongue',
  ':D' => 'lol',
  'rofl' => 'rofl',
  ':wink:' => 'wink',
  ':clap:' => 'clapping',
  ':good:' => 'good',
  ':yu:' => 'yu',
  ':yahoo:' => 'yahoo',
  ':dr:' => 'drinks',
  ':fr:' => 'friends',
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
  ':shok:' => 'shok',
  ':blink:' => 'blink',

  ':huh:' => 'huh',
  '\:\(' => 'mellow',
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
  ':diablo:' => 'diablo',
  ':tratata:' => 'mill',
);

function cht_message_parse($msg)
{
  global $supernova;

  $msg = htmlentities($msg, ENT_COMPAT, 'UTF-8');

  foreach($supernova->design['bbcodes'] as $key => $html)
  {
    $msg = preg_replace("#".$key."#isU", $html, $msg);
  }

  foreach($supernova->design['smiles'] as $key => $imgName)
  {
    $msg = preg_replace("#" . $key . "#isU","<img src=\"design/images/smileys/".$imgName.".gif\" align=\"absmiddle\" title=\"".$key."\" alt=\"".$key."\">",$msg);
  }

  $msg = str_replace("\r\n", '<br />', $msg);
//  $msg = str_replace('\r\n', '<br />', $msg);

  return $msg;
}

?>
