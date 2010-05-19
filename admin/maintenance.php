<?php
/**
 * erreurs.php
 *
 * @version 1.0
 * @copyright 2009 by Gorlum for http://oGame.Triolan.COM.UA
 */

define('INSIDE'  , true);
define('INSTALL' , false);
define('IN_ADMIN', true);

$ugamela_root_path = '../';
include($ugamela_root_path . 'extension.inc');
include($ugamela_root_path . 'common.' . $phpEx);

includeLang('admin');
$parse = $lang;

if ($user['authlevel'] >= 3) {
  $ques = array(
    'messages' => 'DELETE FROM {{table}} WHERE message_time<unix_timestamp(now())-(60*60*24*35);',
    'rw' => 'DELETE FROM {{table}} WHERE time<unix_timestamp(now())-(60*60*24*14);',
    'chat' => 'DELETE FROM {{table}} WHERE timestamp<unix_timestamp( now())-(60*60*24*14);',
    'users' => 'DELETE FROM {{table}} WHERE onlinetime<unix_timestamp( now())-(60*60*24*35);',
    'planets' => 'DELETE FROM {{table}} WHERE id_owner not in (select id from {{users}});',
    'galaxy' => 'DELETE FROM {{table}} WHERE id_planet not in (select id from {{planets}});',
    'messages' => 'DELETE FROM {{table}} WHERE message_owner not in (select id from {{users}});',
    'rw' => 'DELETE FROM {{table}} WHERE id_owner1 not in (select id from {{users}});',
    'rw' => 'DELETE FROM {{table}} WHERE id_owner2 not in (select id from {{users}});',
    'alliance' => 'DELETE FROM {{table}} WHERE id not in (select ally_id from {{users}} group by ally_id);',
    'users' => "UPDATE {{table}} SET ally_id = 0, ally_name='', ally_rank_id=0 WHERE ally_id not in (select id from {{alliance}});",
    'statpoints' => "DELETE FROM {{table}} WHERE stat_type=1 AND id_owner not in (select id from {{users}});",
    'statpoints' => "DELETE FROM {{table}} WHERE stat_type=2 AND id_owner not in (select id from {{alliance}});",
  );

  $replaces = array('users', 'planets', 'alliance');

  $msg = '<ul>';

  foreach($ques as $table => $que) {
    foreach($replaces as $replace)
      $que = str_replace('{{'.$replace.'}}', $config->db_prefix . $replace, $que);
    //$que = str_replace('{{users}}', $dbsettings['prefix'] . 'users', $que);
    //$que = str_replace('{{planets}}', $dbsettings['prefix'] . 'planets', $que);
    $QryResult = doquery($que, $table);

    $msg .= '<li>' .  htmlspecialchars(str_replace('{{table}}', $config->db_prefix . $table, $que)) . ' --- <font color=';
    if ($QryResult) {
      $msg .= 'green>OK.';
    }else{
      $msg .= 'red>FAILED!';
    };
    $msg .= '</font> ' . mysql_affected_rows($link) . ' records deleted<br>';
  }

  $msg .= '</ul>';

  AdminMessage($msg,'Maintenance status');
} else {
  AdminMessage( $lang['sys_noalloaw'], $lang['sys_noaccess'] );
}
?>
