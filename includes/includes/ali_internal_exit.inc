<?php
if ($ally['ally_owner'] == $user['id']) {
  message($lang['Owner_cant_go_out'], $lang['Alliance']);
}
// se sale de la alianza
if ($yes == 1) {
  doquery("UPDATE {{table}} SET `ally_id`=0, `ally_name` = '' WHERE `id`='{$user['id']}'", "users");
  $lang['Go_out_welldone'] = str_replace("%s", $ally_name, $lang['Go_out_welldone']);
  $page = MessageForm($lang['Go_out_welldone'], "<br>", $PHP_SELF, $lang['Ok']);
  // Se quitan los puntos del user en la alianza
} else {
  // se pregunta si se quiere salir
  $lang['Want_go_out'] = str_replace("%s", $ally_name, $lang['Want_go_out']);
  $page = MessageForm($lang['Want_go_out'], "<br>", "?mode=exit&yes=1", $lang['Ok']);
}

display($page);
?>