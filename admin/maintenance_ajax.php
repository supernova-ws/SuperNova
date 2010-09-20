<?php
include_once('../includes/init.inc');

$user          = CheckTheUser();

includeLang('admin');

if ($user['authlevel'] >= 3) {

  $totaltime = microtime(true);
  $msg = '<div align="left"><ul>';

  // Delete users inactive for 45 days
  $query = doquery("SELECT * FROM {{table}} WHERE `onlinetime` < unix_timestamp(now())-(60*60*24*45);", "users");
    $rows += mysql_num_rows($query);
    while($u = mysql_fetch_array($query)){
      set_time_limit(30);
      DeleteSelectedUser ( $u['id'] );
    };

  $msg .= sprintf($lang['adm_inactive_removed'], $rows);

  $ques = array(
    'DELETE FROM {{messages}} WHERE message_time<unix_timestamp(now())-(60*60*24*35);',
    'DELETE FROM {{rw}} WHERE time<unix_timestamp(now())-(60*60*24*14);',
    'DELETE FROM {{chat}} WHERE timestamp<unix_timestamp( now())-(60*60*24*14);',

    'DELETE FROM {{planets}} WHERE id_owner not in (select id from {{users}});',
    'DELETE FROM {{messages}} WHERE message_owner not in (select id from {{users}});',
    'DELETE FROM {{rw}} WHERE id_owner1 not in (select id from {{users}});',
    'DELETE FROM {{rw}} WHERE id_owner2 not in (select id from {{users}});',
    'DELETE FROM {{alliance}} WHERE id not in (select ally_id from {{users}} group by ally_id);',
    "DELETE FROM {{statpoints}} WHERE stat_type=1 AND id_owner not in (select id from {{users}});",

    "UPDATE {{users}} SET ally_id = 0, ally_name='', ally_rank_id=0 WHERE ally_id not in (select id from {{alliance}});",
    "DELETE FROM {{statpoints}} WHERE stat_type=2 AND id_owner not in (select id from {{alliance}});",

//    'DELETE FROM {{galaxy}} WHERE id_planet not in (select id from {{planets}});',
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
    set_time_limit(30);
  }
  $msg .= '</ul></div>';


  $totaltime = microtime(true) - $totaltime;

  $msg = iconv('CP1251', 'UTF-8', htmlspecialchars($msg));
  $xml = "<message>" . $msg . "</message>";

  header('Content-type: text/xml');
  echo $xml;
}
?>