<?php

global $lang, $debug;

if(!defined('SN_IN_ALLY') || SN_IN_ALLY !== true)
{
  $debug->error("Attempt to call ALLIANCE page mode {$mode} directly - not from alliance.php", 'Forbidden', 403);
}

if (!$user_admin) {
  SnTemplate::messageBox($lang['Denied_access'], $lang['ali_adm_rights_title']);
};

$template = SnTemplate::gettemplate('ali_admin_rights', true);

if ($new_rank_name = sys_get_param_str('newRankName'))
{
  foreach($ally_rights as $fieldName)
  {
    $newRank[$fieldName] = 0;
  }
  $newRank['name'] = $new_rank_name;

  $ranks[] = $newRank;
}

if (is_array($rankListInput = sys_get_param('u')))
{
  unset($ranks);

  foreach($rankListInput as $rankID => $rank)
  {
    foreach($ally_rights as $rightName)
    {
      $ranks[$rankID][$rightName] = $rank[$rightName] ? 1 : 0;
    }
    $ranks[$rankID]['name'] = strip_tags($rank['name']);
  }
}

$d = sys_get_param_int('d');
if ($d && isset($ranks[$d]))
{
  if(count($ranks) == 1)
  {
    SnTemplate::messageBox($lang['ali_adm_lastRank'], $lang['ali_adm_rights_title']);
  }
  array_splice($ranks, $d, 1);
  db_user_list_set_by_ally_and_rank($ally['id'], $d, '`ally_rank_id`=`ally_rank_id` - 1');
}

ali_rank_list_save($ranks);

if (count($ranks))
{
  foreach($ranks as $rankID => $rank)
  {
    $rank_data = array(
      'ID'   => $rankID,
      'NAME' => $rank['name'],
    );

    for($i = 1; $i < count($rank); $i++)
    {
      $rank_data['R' . $i] = (($rank[$ally_rights[$i]] == 1) ? ' checked' : '') ;
      $rank_data['N' . $i] = $ally_rights[$i];
    }

    $template->assign_block_vars('rank', $rank_data);
  }
}

SnTemplate::display($template, $lang['ali_adm_rights_title']);
