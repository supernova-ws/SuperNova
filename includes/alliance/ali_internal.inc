<?php

if(!defined('SN_IN_ALLY') || SN_IN_ALLY !== true)
{
  $debug->error("Attempt to call ALLIANCE page mode {$mode} directly - not from alliance.php", 'Forbidden', 403);
}

// function ALI_rankListSave
// Copyright (c) 2010 by Gorlum for http://supernova.ws
// Open Source
// V1

function ALI_rankListSave($ranks) {
  global $user;

  if(!empty($ranks)){
    foreach($ranks as $rank => $rights){
      $rights = implode(',', $rights);
      $ranklist .= $rights . ';';
    }
  }

  doquery("UPDATE {{alliance}} SET `ranklist` = '{$ranklist}' WHERE `id` ='{$user['ally_id']}';");
  return $ranklist;
}

$rights     = array( 0 => 'name', 1 => 'mail',  2 => 'online',       3 => 'invite',                4 => 'kick', 5 => 'admin',      6 => 'forum', 7 => 'diplomacy');
$rights_old = array( 0 => 'name', 1 => 'mails', 2 => 'onlinestatus', 3 => 'bewerbungenbearbeiten', 4 => 'kick', 5 => 'rechtehand',);

$ally = doquery("SELECT * FROM {{alliance}} WHERE `id` ='{$user['ally_id']}'", '', true);

// This piece converting old ally data to new one
//  unset($ally['ranklist']);
if(!$ally['ranklist'] && $ally['ally_ranks']){
  $ally_ranks = unserialize($ally['ally_ranks']);
  $i = 0;
  foreach($ally_ranks as $rank_id => $rank){
    foreach($rights as $key => $value)
      $ranks[$i][$value] = $rank[$rights_old[$key]];
    doquery("UPDATE {{users}} SET `ally_rank_id` = {$i} WHERE `ally_id` ='{$user['ally_id']}' AND `ally_rank_id`={$rank_id};", '', true);
    $i++;
  };

  if(!empty($ranks))
    ALI_rankListSave($ranks);
};

if($ally['ranklist']){
   $str_ranks = explode(';',$ally['ranklist']);
   foreach($str_ranks as $str_rank){
     if(!$str_rank)continue;
     $tmp = explode(',',$str_rank);
     $rank_id = count($ranks);
     foreach($rights as $key => $value)
       $ranks[$rank_id][$value] = $tmp[$key];
   };
};

if ($ally['ally_owner'] == $user['id'])
  $isAllyOwner = true;

if ($ranks[$user['ally_rank_id']]['mail'] == 1 || $isAllyOwner)
  $user_can_send_mails = true;

if ($ranks[$user['ally_rank_id']]['forum'] == 1 || $isAllyOwner)
  $userCanPostForum = true;

if ($ranks[$user['ally_rank_id']]['online'] == 1 || $isAllyOwner)
  $user_onlinestatus = true;

if ($ranks[$user['ally_rank_id']]['invite'] == 1 || $isAllyOwner)
  $user_admin_applications = true;

if ($ranks[$user['ally_rank_id']]['kick'] == 1 || $isAllyOwner)
  $user_can_kick = true;

if ($ranks[$user['ally_rank_id']]['diplomacy'] == 1 || $isAllyOwner)
  $user_can_negotiate = true;

if ($ranks[$user['ally_rank_id']]['admin'] == 1 || $isAllyOwner){
  $user_can_edit_rights = true;
  $user_admin = true;
}

if (!$ally) {
  doquery("UPDATE {{users}} SET `ally_id` = 0, `ally_name` = '' WHERE `id`='{$user['id']}' LIMIT 1;");
  message($lang['ali_sys_notFound'], $lang['your_alliance'], 'alliance.php');
}

switch ($mode){
  case 'admin':
  	 $allianceAdminMode = true;
    switch($edit)
    {
      case 'rights':    include('ali_internal_admin_rights.inc'); break;
      case 'members':   include('ali_internal_memberlist.inc'); break;
      case 'requests':  include('ali_internal_admin_request.inc'); break;
      case 'diplomacy': include('ali_internal_admin_diplomacy.inc'); break;
      default:          include('ali_internal_admin.inc'); break;
    }
  break;

  case 'exit':         include('ali_internal_exit.inc'); break;
  case 'memberslist':  include('ali_internal_memberlist.inc'); break;
  case 'circular':     include('ali_internal_admin_mail.inc'); break;
  default:             include('ali_internal_default.inc'); break;
}

?>
