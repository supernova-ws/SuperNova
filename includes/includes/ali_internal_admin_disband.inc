<?php

// comprobamos el permiso
if ($ally['ally_owner'] != $user['id'] && !$user_can_exit_alliance) {
  message($lang['Denied_access'], $lang['Members_list']);
}
/* Si bien, se tendria que confirmar, no tengo animos para hacerlo mas detallado... sorry :( */
doquery("DELETE FROM {{table}} WHERE id='{$ally['id']}'", "alliance");
header('Location: alliance.php');
exit;

?>