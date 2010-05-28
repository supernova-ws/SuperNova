<?php

if (!$user_can_edit_rights) {
  message($lang['Denied_access'], $lang['Members_list']);
};

if ($newRankName) {
  foreach($rights as $fieldName)
    $newRank[$fieldName] = 0;
  $newRank['name'] = $newRankName;

  $ranks[] = $newRank;
}

if (is_array($rankListInput)) {
  unset($ranks);

  foreach($rankListInput as $rankID => $rank){
    foreach($rights as $rightName){
      $ranks[$rankID][$rightName] = $rank[$rightName] ? 1 : 0;
    }
    $ranks[$rankID]['name'] = strip_tags($rank['name']);
  }
}

if ($d && isset($ranks[$d])) {
  if(count($ranks) == 1){
    message($lang['ali_onlyOneRankLeft'], $lang['Members_list']);
  }
  array_splice($ranks, $d, 1);
  doquery("UPDATE {{table}} SET `ally_rank_id`=`ally_rank_id` - 1 WHERE `ally_id`={$ally['id']} AND `ally_rank_id` >= {$d}", "users");
}

ALI_rankListSave($ranks);

if (!count($ranks)) { // si no hay rangos
  $list = "<th colspan=8>{$lang['There_is_not_range']}</th>";
} else {
  $template = gettemplate('alliance_admin_laws_row');

  foreach($ranks as $rankID => $rank) {
    $lang['rankID'] = $rankID;

    if (count($ranks) <= 1)
      $lang['delete'] = 'class="hide"';

    $lang['rN'] = $rank['name'];
    for($i = 1; $i < count($rank); $i++){
      $lang['r' . $i] = (($rank[$rights[$i]] == 1) ? ' checked' : '') ;
      $lang['n' . $i] = $rights[$i];
    }
    $list .= parsetemplate($template, $lang);
  }
}

$lang['list'] = $list;
$lang['dpath'] = $dpath;
$page .= parsetemplate(gettemplate('alliance_admin_laws'), $lang);

display($page, $lang['Law_settings']);
?>