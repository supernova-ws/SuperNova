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

// Main admin page save themes
$isSaveText       = !empty($_POST['isSaveText']);
$isSaveOptions    = !empty($_POST['isSaveOptions']);
$isDisband        = !empty($_POST['isDisband']);
$isConfirmDisband = !empty($_POST['isConfirmDisband']);
$isTransfer       = !empty($_POST['isTransfer']);

$edit = sys_get_param_str('edit');

lng_include('alliance');

if($mode == 'ainfo')
{
  include('includes/alliance/ali_internal_default.inc');
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
      break;
    }
  }
}
elseif(!$user_request['id_user'])
{
  $rights     = array(
    0 => 'name',
    1 => 'mail',
    2 => 'online',
    3 => 'invite',
    4 => 'kick',
    5 => 'admin',
    6 => 'forum',
    7 => 'diplomacy'
  );
  $rights_old = array(
    0 => 'name',
    1 => 'mails',
    2 => 'onlinestatus',
    3 => 'bewerbungenbearbeiten',
    4 => 'kick',
    5 => 'rechtehand'
  );

  $ally = doquery("SELECT * FROM {{alliance}} WHERE `id` ='{$user['ally_id']}'", '', true);

  // This piece converting old ally data to new one
  //  unset($ally['ranklist']);
  if(!$ally['ranklist'] && $ally['ally_ranks'])
  {
    $ally_ranks = unserialize($ally['ally_ranks']);
    $i = 0;
    foreach($ally_ranks as $rank_id => $rank)
    {
      foreach($rights as $key => $value)
      {
        $ranks[$i][$value] = $rank[$rights_old[$key]];
      }
      doquery("UPDATE {{users}} SET `ally_rank_id` = {$i} WHERE `ally_id` ='{$user['ally_id']}' AND `ally_rank_id`={$rank_id};", '', true);
      $i++;
    }

    if(!empty($ranks))
    {
      ALI_rankListSave($ranks);
    }
  }

  if($ally['ranklist'])
  {
     $str_ranks = explode(';', $ally['ranklist']);
     foreach($str_ranks as $str_rank)
     {
       if(!$str_rank)
       {
         continue;
       }

       $tmp = explode(',', $str_rank);
       $rank_id = count($ranks);
       foreach($rights as $key => $value)
       {
         $ranks[$rank_id][$value] = $tmp[$key];
       }
     }
  }

  if($ally['ally_owner'] == $user['id'])
    $isAllyOwner = true;

  if($ranks[$user['ally_rank_id']]['mail'] == 1 || $isAllyOwner)
    $user_can_send_mails = true;

  if($ranks[$user['ally_rank_id']]['forum'] == 1 || $isAllyOwner)
    $userCanPostForum = true;

  if($ranks[$user['ally_rank_id']]['online'] == 1 || $isAllyOwner)
    $user_onlinestatus = true;

  if($ranks[$user['ally_rank_id']]['invite'] == 1 || $isAllyOwner)
    $user_admin_applications = true;

  if($ranks[$user['ally_rank_id']]['kick'] == 1 || $isAllyOwner)
    $user_can_kick = true;

  if($ranks[$user['ally_rank_id']]['diplomacy'] == 1 || $isAllyOwner)
    $user_can_negotiate = true;

  if($ranks[$user['ally_rank_id']]['admin'] == 1 || $isAllyOwner)
  {
    $user_can_edit_rights = true;
    $user_admin = true;
  }

  if(!$ally)
  {
    doquery("UPDATE {{users}} SET `ally_id` = 0, `ally_name` = '' WHERE `id`='{$user['id']}' LIMIT 1;");
    message($lang['ali_sys_notFound'], $lang['your_alliance'], 'alliance.php');
  }

  switch ($mode)
  {
    case 'admin':
      $allianceAdminMode = true;
      switch($edit)
      {
        case 'rights':    require('includes/alliance/ali_internal_admin_rights.inc'); break;
        case 'members':   require('includes/alliance/ali_internal_memberlist.inc'); break;
        case 'requests':  require('includes/alliance/ali_internal_admin_request.inc'); break;
        case 'diplomacy': require('includes/alliance/ali_internal_admin_diplomacy.inc'); break;
        default:          require('includes/alliance/ali_internal_admin.inc'); break;
      }
    break;

    case 'exit':         require('includes/alliance/ali_internal_exit.inc'); break;
    case 'memberslist':  require('includes/alliance/ali_internal_memberlist.inc'); break;
    case 'circular':     require('includes/alliance/ali_internal_admin_mail.inc'); break;
    default:             require('includes/alliance/ali_internal_default.inc'); break;
  }
}

?>
