<?php
function CHT_messageParse($msg){
  $BBCodes = array (
    "^\[c=(white|blue|yellow|green|pink|red|orange|purple)\](.+)\[/c\]$" => "<font color=\"$1\">$2</font>",
    "\[a=(ft|https?://)(.+)\](.+)\[/a\]" => "<a href=\"$1$2\" target=\"_blank\"><u>$3</u></a>",
    "\[b\](.+)\[/b\]" => "<b>$1</b>", "\[i\](.+)\[/i\]" => "<i>$1</i>", "\[u\](.+)\[/u\]" => "<u>$1</u>",
  );

  $smiles = array (
    ':agr:' => 'aggressive', ':angel:' => 'angel', ':bad:' => 'bad', ':blink:' => 'blink',
    ':blush:' => 'blush', ':bomb:' => 'bomb', ':clap:' => 'clapping', ':cool:' => 'cool',
    ':c:' => 'cray', ':crz:' => 'crazy', ':diablo:' => 'diablo', ':cool2:' => 'dirol',
    ':fool:' => 'fool', ':rose:' => 'give_rose', ':good:' => 'good', ':huh:' => 'huh',
    ':D:' => 'lol', ':yu' => 'yu', ':unknw:' => 'unknw', ':sad' => 'sad',
    ':smile' => 'smile', ':shok:' => 'shok', ':rofl' => 'rofl', ':eye' => 'blackeye',
    ':p' => 'tongue', ':wink:' => 'wink', ':yahoo:' => 'yahoo', ':tratata:' => 'mill',
    ':fr' => 'friends', ':dr' => 'drinks', ':tease:' => 'tease',
    '\;\)' => 'wink', '\:\(' => 'mellow'
  );

  foreach ($BBCodes as $key => $html)
    $msg = preg_replace("#".$key."#isU", $html, $msg);

  foreach ($smiles as $key => $imgName)
    $msg = preg_replace("#" . $key . "#isU","<img src=\"design/images/smileys/".$imgName.".gif\" align=\"absmiddle\" title=\"".$key."\" alt=\"".$key."\">",$msg);

  return $msg;
}
?>