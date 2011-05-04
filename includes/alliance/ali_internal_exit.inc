<?php

if(!defined('SN_IN_ALLY') || SN_IN_ALLY !== true)
{
  $debug->error("Attempt to call ALLIANCE page mode {$mode} directly - not from alliance.php", 'Forbidden', 403);
}

if ($ally['ally_owner'] == $user['id'])
{
  message($lang['Owner_cant_go_out'], $lang['Alliance']);
}

$yes = sys_get_param_int('yes');
if ($yes == 1)
{
  doquery("UPDATE {{users}} SET `ally_id`=0, `ally_name` = '' WHERE `id`='{$user['id']}'");
  $lang['Go_out_welldone'] = str_replace("%s", $ally_name, $lang['Go_out_welldone']);
  $page = MessageForm($lang['Go_out_welldone'], "<br>", $PHP_SELF, $lang['Ok']);
}
else
{
  $lang['Want_go_out'] = str_replace("%s", $ally_name, $lang['Want_go_out']);
  $page = MessageForm($lang['Want_go_out'], "<br>", "alliance.php?mode=exit&yes=1", $lang['Ok']);
}

display($page);

?>
