<?php
  // query para la allyrow
  $rights     = array( 0 => 'name', 1 => 'mail',  2 => 'online',       3 => 'invite',                4 => 'kick', 5 => 'admin',     );
  $rights_old = array( 0 => 'name', 1 => 'mails', 2 => 'onlinestatus', 3 => 'bewerbungenbearbeiten', 4 => 'kick', 5 => 'rechtehand',);

  $ally = doquery("SELECT * FROM {{table}} WHERE `id` ='{$user['ally_id']}'", "alliance", true);

  // This piece converting old ally data to new one
//  unset($ally['ranklist']);
  if(!$ally['ranklist'] && $ally['ally_ranks']){
    $ally_ranks = unserialize($ally['ally_ranks']);
    $i = 0;
    foreach($ally_ranks as $rank_id => $rank){
      foreach($rights as $key => $value)
        $ranks[$i][$value] = $rank[$rights_old[$key]];
      doquery("UPDATE {{table}} SET `ally_rank_id` = {$i} WHERE `ally_id` ='{$user['ally_id']}' AND `ally_rank_id`={$rank_id};", "users", true);
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

  // Everyone can leave alliance at his own!
  $user_can_exit_alliance = true;

  // Everyone can view memberlist!
  $user_can_watch_memberlist = true;

  if ($ranks[$user['ally_rank_id']]['mail'] == 1 || $ally['ally_owner'] == $user['id'])
    $user_can_send_mails = true;
  if ($ranks[$user['ally_rank_id']]['online'] == 1 || $ally['ally_owner'] == $user['id'])
    $user_onlinestatus = true;
  // Only those who can invite can see applications
  if ($ranks[$user['ally_rank_id']]['invite'] == 1 || $ally['ally_owner'] == $user['id'])
    $user_admin_applications = true;
  $user_can_see_applications = $user_admin_applications;

  if ($ranks[$user['ally_rank_id']]['kick'] == 1 || $ally['ally_owner'] == $user['id'])
    $user_can_kick = true;

  if ($ranks[$user['ally_rank_id']]['admin'] == 1 || $ally['ally_owner'] == $user['id']){
    $user_can_edit_rights = true;
    $user_admin = true;
  }

  if ($ally['ally_owner'] == $user['id'])
    $user_can_disband = true;

  if (!$ally) {
    doquery("UPDATE {{table}} SET `ally_id` = 0, `ally_name` = '' WHERE `id`='{$user['id']}'", "users");
    message($lang['ali_sys_notFound'], $lang['your_alliance'], 'alliance.php');
  }

  switch ($mode){
    case 'admin':
    	$allianceAdminMode = true;
      switch($edit){
        case 'rights':   include('ali_internal_admin_rights.inc'); break;
        case 'members':  include('ali_internal_memberlist.inc'); break;
        case 'requests': include('ali_internal_admin_invite.inc'); break;
        default:         include('ali_internal_admin.inc'); break;
      }
      break;
    case 'exit':         include('ali_internal_exit.inc'); break;
    case 'memberslist':  include('ali_internal_memberlist.inc'); break;
    case 'circular':     include('ali_internal_mail.inc'); break;
    default:             include('ali_internal_default.inc'); break;
  }
?>