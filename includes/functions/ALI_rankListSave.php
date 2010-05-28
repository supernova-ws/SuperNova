<?php
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

  doquery("UPDATE {{table}} SET `ranklist` = '{$ranklist}' WHERE `id` ='{$user['ally_id']}';", 'alliance');
  return $ranklist;
}
?>