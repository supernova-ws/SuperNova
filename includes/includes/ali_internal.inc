<?php
  // query para la allyrow
  $rights     = array( 0 => 'name', 1 => 'mail',  2 => 'online',       3 => 'invite',                4 => 'kick', 5 => 'admin',      6 => 'disband',        );
  $rights_old = array( 0 => 'name', 1 => 'mails', 2 => 'onlinestatus', 3 => 'bewerbungenbearbeiten', 4 => 'kick', 5 => 'rechtehand', 6 => 'administrieren', );
/*
  1 - View online status
  2 - Mass mail
  3 - Invite - обработка заявок
  4 - Kick
  5 - Delete ally
  6 - Edit rights

  delete => integer 0
		memberlist => integer 1  // Просмотр списка участников
		onlinestatus => integer 0 // Просмотр on-line статуса участников
		mails => integer 1    // Сообщение всему альянсу
		kick => integer 0 // Удаление игроков
		bewerbungen => integer 0  // Просмотр заявок
		bewerbungenbearbeiten => integer 0  // Проверить заявки
		rechtehand => integer 0 // 'Помощник главы' (Для передачи необходим ранг основателя)
		administrieren => integer 0 // Удаление альянса
*/

  $ally = doquery("SELECT * FROM {{table}} WHERE `id` ='{$user['ally_id']}'", "alliance", true);

  $ally_ranks = unserialize($ally['ally_ranks']);

//  if(!$ally['ranklist'] && $ally['ally_ranks']){
  if($ally['ally_ranks']){
    foreach($ally_ranks as $rank_id => $rank){
      foreach($rights as $key => $value)
        $ranks[$rank_id - 1][$value] = $rank[$rights_old[$key]];
    };
  };
/*
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
*/
  if(!empty($ranks)){
    foreach($ranks as $rank => $rights){
      $rights = implode(',', $rights);
      $ranklist .= $rights . ';';
    }
    doquery("UPDATE {{table}} SET ranklist = '$ranklist' WHERE `id` ='{$user['ally_id']}';",'alliance');
  }

  $ally_ranks = unserialize($ally['ally_ranks']);

  $allianz_raenge = unserialize($ally['ally_ranks']);

  // Everyone can leave alliance at his own!
  //if ($allianz_raenge[$user['ally_rank_id']-1]['delete'] == 1 || $ally['ally_owner'] == $user['id'])
  $user_can_exit_alliance = true;

  // Everyone can view memberlist!
  //if ($allianz_raenge[$user['ally_rank_id']-1]['memberlist'] == 1 || $ally['ally_owner'] == $user['id'])
  $user_can_watch_memberlist = true;

  //if ($allianz_raenge[$user['ally_rank_id']-1]['mails'] == 1 || $ally['ally_owner'] == $user['id'])
  if ($ranks[$user['ally_rank_id']]['mail'] == 1 || $ally['ally_owner'] == $user['id'])
    $user_can_send_mails = true;

  //if ($allianz_raenge[$user['ally_rank_id']-1]['kick'] == 1 || $ally['ally_owner'] == $user['id'])
  if ($ranks[$user['ally_rank_id']]['kick'] == 1 || $ally['ally_owner'] == $user['id'])
    $user_can_kick = true;

  //if ($allianz_raenge[$user['ally_rank_id']-1]['rechtehand'] == 1 || $ally['ally_owner'] == $user['id'])
  if ($ranks[$user['ally_rank_id']]['admin'] == 1 || $ally['ally_owner'] == $user['id'])
    $user_can_edit_rights = true;

  //if ($allianz_raenge[$user['ally_rank_id']-1]['bewerbungenbearbeiten'] == 1 || $ally['ally_owner'] == $user['id'])
  if ($ranks[$user['ally_rank_id']]['invite'] == 1 || $ally['ally_owner'] == $user['id'])
    $user_admin_applications = true;

  //if ($allianz_raenge[$user['ally_rank_id']-1]['bewerbungen'] == 1 || $ally['ally_owner'] == $user['id'])
  //  $user_can_see_applications = true;
  $user_can_see_applications = $user_admin_applications;

  //if ($allianz_raenge[$user['ally_rank_id']-1]['administrieren'] == 1 || $ally['ally_owner'] == $user['id'])
  if ($ranks[$user['ally_rank_id']]['admin'] == 1 || $ally['ally_owner'] == $user['id'])
    $user_admin = true;

  //if ($allianz_raenge[$user['ally_rank_id']-1]['onlinestatus'] == 1 || $ally['ally_owner'] == $user['id'])
  if ($ranks[$user['ally_rank_id']-1]['online'] == 1 || $ally['ally_owner'] == $user['id'])
    $user_onlinestatus = true;

  if (!$ally) {
    doquery("UPDATE {{table}} SET `ally_id`=0 WHERE `id`='{$user['id']}'", "users");
    message($lang['ally_notexist'], $lang['your_alliance'], 'alliance.php');
  }

  if ($mode == 'exit') {
    include('ali_internal_exit.inc');
  }

  if ($mode == 'memberslist') { // Lista de miembros.
    include('ali_internal_memberslist.inc');
  }

  if ($mode == 'circular') { // Mass-mail
    include('ali_internal_mail.inc');
  }

  if ($mode == 'admin' && $edit == 'rights') { // Administrar leyes
    include('ali_internal_admin_rights.inc');
  }

  if ($mode == 'admin' && $edit == 'ally') {
    include('ali_internal_admin_allyinfo.inc');
  }

  if ($mode == 'admin' && $edit == 'members') { // Administrar a los miembros
    include('ali_internal_admin_members.inc');
  }

  if ($mode == 'admin' && $edit == 'requests') { // Administrar solicitudes
    include('ali_internal_admin_invite.inc');
  }

  if ($mode == 'admin' && $edit == 'name') {
    include('ali_internal_admin_name.inc');
  }

  if ($mode == 'admin' && $edit == 'tag') {
    include('ali_internal_admin_tag.inc');
  }

  if ($mode == 'admin' && $edit == 'exit') { // disolver una alianza
    include('ali_internal_admin_disband.inc');
  }

  if ($mode == 'admin' && $edit == 'transfer') {
    include('ali_internal_admin_transfer.inc');
  }
?>