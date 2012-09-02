<?php
/**
 chat.php
   Main chat window

 Changelog:
   4.0 copyright © 2009-2012 Gorlum for http://supernova.ws
     [!] Another rewrite
     [+] preMVC-compatible
   3.0 copyright © 2009-2011 Gorlum for http://supernova.ws
     [!] Almost full rewrote
     [+] Complies with PCG1
   2.0 copyright © 2009-2010 Gorlum for http://supernova.ws
     [+] Rewrote to remove unnecessary code dupes
   1.5 copyright © 2009-2010 Gorlum for http://supernova.ws
     [~] More DDoS-realted fixes
   1.4 copyright © 2009-2010 Gorlum for http://supernova.ws
     [~] DDoS-realted fixes
   1.3 copyright © 2009-2010 Gorlum for http://supernova.ws
     [~] Security checks for SQL-injection
   1.2 by Ihor
   1.0 Shoutbox copyright © 2008 by e-Zobar for XNova
**/

$sn_mvc['model']['chat'][] = 'sn_chat_model';
$sn_mvc['view']['chat'][] = 'sn_chat_view';

function sn_chat_model()
{
  global $config, $user, $microtime, $template_result, $lang;

  $config->array_set('users', $user['id'], 'chat_last_activity', $microtime);
  $config->array_set('users', $user['id'], 'chat_last_refresh', 0);

  $mode = sys_get_param_int('mode');
  switch($mode)
  {
    case CHAT_MODE_ALLY:
      $template_result['ALLY'] = intval($user['ally_id']);
      $page_title = $lang['chat_ally'];
    break;

    case CHAT_MODE_COMMON:
    default:
      $page_title = $lang['chat_common'];
    break;
  }

  $template_result['PAGE_HEADER'] = $page_title;
}

function sn_chat_view($template = null)
{
  $template = gettemplate('chat_body', $template);

  return $template;
}

?>
