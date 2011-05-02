<?php
/**
 alliance.php
 Alliance manager

 History
 2.0  - Full rewrite by Gorlum for http://supernova.ws
 1.0s - Security checked for SQL-injection by Gorlum for http://supernova.ws
 1.0  - copyright 2008 by Chlorel for XNova
*/

include('common.' . substr(strrchr(__FILE__, '.'), 1));

define('SN_IN_ALLY', true);

// MINE VARS
$POST_text = sys_get_param_str('text');

$id_kick = intval($_GET['kick']);
$id_user = intval($_GET['id_user']);
if(isset($_GET['id_rank']))
  $id_rank = intval($_GET['id_rank']);

$newRankName = sys_get_param_str('newRankName');
$allyTextID = intval($_GET['t']);

// Main admin page save themes
$isSaveText       = !empty($_POST['isSaveText']);
$isSaveOptions    = !empty($_POST['isSaveOptions']);
$isDisband        = !empty($_POST['isDisband']);
$isConfirmDisband = !empty($_POST['isConfirmDisband']);
$isTransfer       = !empty($_POST['isTransfer']);
$idNewLeader      = intval($_POST['idNewLeader']);

$mode             = SYS_mysqlSmartEscape($_GET['mode']);
$edit             = SYS_mysqlSmartEscape($_GET['edit']);

// alliance ID
$id_ally          = intval($_GET['a']);

$d          = intval($_GET['d']);
$yes        = intval($_GET['yes']);
$sort1      = intval($_GET['sort1']);
$sort2      = intval($_GET['sort2']);
$id         = intval($_GET['id']);
$show       = intval($_GET['show']);
$sendmail   = intval($_GET['sendmail']);
$tag        = SYS_mysqlSmartEscape($_GET['tag']);

$POST_action = SYS_mysqlSmartEscape($_POST['action']);
$POST_further = SYS_mysqlSmartEscape($_POST['further']);
$POST_bcancel = SYS_mysqlSmartEscape($_POST['bcancel']);
$POST_newleader = SYS_mysqlSmartEscape($_POST['newleader']);

lng_include('alliance');

if ($mode == 'ainfo') {
  include('includes/alliance/ali_info.inc');
};

$user_request = doquery("SELECT * FROM {{alliance_requests}} WHERE `id_user` ='{$user['id']}' LIMIT 1;", '', true);

if (!$user['ally_id'])
{
  if($user_request['id_user'])
  {
    require('includes/alliance/ali_external_request.inc');
  }
  else
  {
    switch($mode)
    {
      case 'make':
        require('includes/alliance/ali_external_create_ally.inc');
      break;

      case 'search':
        require('includes/alliance/ali_external_search.inc');
      break;

      case 'apply':
        require('includes/alliance/ali_external_request.inc');
      break;

      default:
        display(parsetemplate(gettemplate('ali_external', true)), $lang['alliance']);
    }
  }
}
elseif (!$user_request['id_user'])
{
  include('includes/alliance/ali_internal.inc');
}

?>
