<?php
function sys_bbcodeParse($text){
  return str_replace("\r\n", "<br />", $text);
}

function sys_bbcodeUnParse($text){
  return str_replace("<br />", "\r\n", $text);
}
?>