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
    'DELETE FROM {{table}} WHERE message_time<unix_timestamp(now())-(60*60*24*35);',
    'DELETE FROM {{table}} WHERE time<unix_timestamp(now())-(60*60*24*14);',
    'DELETE FROM {{table}} WHERE timestamp<unix_timestamp( now())-(60*60*24*14);',
    'DELETE FROM {{table}} WHERE onlinetime<unix_timestamp( now())-(60*60*24*35);',
    'DELETE FROM {{table}} WHERE id_owner not in (select id from game_users);',
    'DELETE FROM {{table}} WHERE id_planet not in (select id from game_planets);',
    'DELETE FROM {{table}} WHERE message_owner not in (select id from game_users);',
    'DELETE FROM {{table}} WHERE id_owner1 not in (select id from game_users);',
    'DELETE FROM {{table}} WHERE id_owner2 not in (select id from game_users);'
  );

  $tables = array(
    'messages',
    'rw',
    'chat',
    'users',
    'planets',
    'galaxy',
    'messages',
    'rw',
    'rw'
  );

  $msg = '<ul>';

  foreach($ques as $key => $que) {
    $QryResult = doquery($que, $tables[$key]);

    $msg .= '<li>' .  htmlspecialchars(str_replace('{{table}}', $tables[$key], $que)) . ' --- <font color=';
    if ($QryResult) {
      $msg .= 'green>OK';
    }else{
      $msg .= 'red>FAILED!';
    };
    $msg .= '</font><br>';
  }

  $msg .= '</ul>';

  AdminMessage($msg,'Maintenance status');
} else {
  AdminMessage( $lang['sys_noalloaw'], $lang['sys_noaccess'] );
}
?>
