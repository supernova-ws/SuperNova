<?php
function sys_parseBBCode($text){
  return str_replace("\r\n", "<br>", $text);
}
?>