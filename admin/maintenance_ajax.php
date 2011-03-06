<?php

require('../includes/init.' . substr(strrchr(__FILE__, '.'), 1));

$user = sn_autologin();

if ($user['authlevel'] < 3)
{
  message($lang['sys_noalloaw'], $lang['sys_noaccess']);
  die();
}

includeLang('admin');

$totaltime = microtime(true);
$msg = '<div align="left"><ul>';

doquery('START TRANSACTION;');

// Delete users inactive for 45 days
/*
$query = doquery("SELECT * FROM {{users}} WHERE `onlinetime` < unix_timestamp(now()) - ( 60 * 60 * 24 * 45);");
$rows += mysql_num_rows($query);
while($u = mysql_fetch_assoc($query)){
  set_time_limit(30);
  DeleteSelectedUser ( $u['id'] );
};
*/

$msg .= sprintf($lang['adm_inactive_removed'], $rows);

$ques = array(
  'DELETE {{users}}.* FROM {{users}} WHERE `onlinetime` < unix_timestamp(now()) - ( 60 * 60 * 24 * 45);',

  'DELETE FROM `{{notes}}`    WHERE `owner`          not in (select id from {{users}});',
  'DELETE FROM `{{fleets}}`   WHERE `fleet_owner`    not in (select id from {{users}});',
  'DELETE FROM `{{buddy}}`    WHERE `sender`         not in (select id from {{users}});',
  'DELETE FROM `{{buddy}}`    WHERE `owner`          not in (select id from {{users}});',
  'DELETE FROM `{{annonce}}`  WHERE `user`           not in (select id from {{users}});',
  'DELETE FROM `{{messages}}` WHERE `message_sender` not in (select id from {{users}});',
  'DELETE FROM `{{messages}}` WHERE `message_owner`  not in (select id from {{users}});',
  'DELETE FROM `{{planets}}`  WHERE `id_owner`       not in (select id from {{users}});',
  'DELETE FROM `{{rw}}`       WHERE `id_owner1`      not in (select id from {{users}});',
  'DELETE FROM `{{rw}}`       WHERE `id_owner2`      not in (select id from {{users}});',
/*
  'DELETE {{messages}}.* FROM {{messages}} LEFT OUTER JOIN {{users}} ON {{messages}}.message_owner = {{users}}.id WHERE {{users}}.username IS NULL;',
  'DELETE {{planets}}.* FROM {{planets}} LEFT OUTER JOIN {{users}} ON {{planets}}.id_owner = {{users}}.id WHERE {{users}}.username IS NULL;',
  'DELETE {{rw}}.* FROM {{rw}} LEFT OUTER JOIN {{users}} ON {{rw}}.id_owner1 = {{users}}.id WHERE {{users}}.username IS NULL;',
  'DELETE {{rw}}.* FROM {{rw}} LEFT OUTER JOIN {{users}} ON {{rw}}.id_owner2 = {{users}}.id WHERE {{users}}.username IS NULL;',
*/

  'DELETE FROM {{statpoints}} WHERE stat_type=1 AND id_owner not in (select id from {{users}});',

  'DELETE FROM {{alliance}} WHERE id not in (select ally_id from {{users}} group by ally_id);',
  'DELETE FROM {{statpoints}} WHERE stat_type=2 AND id_owner not in (select id from {{alliance}});',
  "UPDATE {{users}} SET ally_id = 0, ally_name='', ally_rank_id=0 WHERE ally_id not in (select id from {{alliance}});",

  'DELETE FROM {{messages}} WHERE message_time < unix_timestamp(now()) - (60 * 60 * 24 * 35);',
  'DELETE FROM {{rw}} WHERE time < unix_timestamp(now()) - (60 * 60 * 24 * 14);',
  'DELETE FROM {{chat}} WHERE timestamp < unix_timestamp(now()) - (60 * 60 * 24 * 14);',
);

foreach($ques as $que) {
  $QryResult = doquery($que);

  $que = str_replace('{{', "", $que);
  $que = str_replace('}}', "", $que);
  $msg .= '<li>' .  htmlspecialchars($que) . ' --- <font color=';
  if ($QryResult) {
    $msg .= 'green>OK.';
  }else{
    $msg .= 'red>FAILED!';
  };
  $msg .= '</font> ' . mysql_affected_rows($link) . ' ' . $lang['adm_records'];
  set_time_limit(120);
}
$msg .= '</ul></div>';

doquery('COMMIT;');

$totaltime = microtime(true) - $totaltime;

$msg = iconv('CP1251', 'UTF-8', htmlspecialchars($msg));
$xml = "<message>" . $msg . ' ' . $totaltime . "</message>";

header('Content-type: text/xml');
echo $xml;

?>
