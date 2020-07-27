<?php

if(!defined('SN_IN_ALLY') || SN_IN_ALLY !== true)
{
  $debug->error("Attempt to call ALLIANCE page mode {$mode} directly - not from alliance.php", 'Forbidden', 403);
}
/*
if (!$user_can_kick)
  messageBox($lang['Denied_access'], $lang['Members_list']);
*/
// Changing rank for single user
$id_user = sys_get_param_id('id_user');
if(isset($_GET['id_rank']))
{
  $id_rank = sys_get_param_int('id_rank');
}
if($id_user && isset($id_rank) && $user_admin){
  db_user_set_by_id($id_user, "`ally_rank_id` = {$id_rank}");
}

$id_kick = sys_get_param_id('kick');
if ($id_kick && $user_can_kick)
{
  $u = db_user_by_id($id_kick, true);
  if ($u['ally_id'] == $ally['id'] && $u['id'] != $ally['ally_owner'])
  {
    db_user_set_by_id($id_kick, '`ally_id`= null, `ally_name` = null, `ally_tag` = null, ally_register_time = 0, ally_rank_id = 0');
    doquery("UPDATE {{alliance}} SET `ally_members`= `ally_members` - 1 WHERE `id`='{$ally['id']}' LIMIT 1;");
  }
}

$sort2 = sys_get_param_int('sort2');
if ($sort2)
{
  $sort2s = "DESC";
  $sort2 = 0;
}
else
{
  $sort2s = "ASC";
  $sort2 = 1;
}

$sort1 = sys_get_param_int('sort1');
if($sort1>5 || $sort1<0) $sort1 = 0;
$sort1s = array(
  0 => '`ally_rank_id` %1$s;',
  1 => '`username` %1$s;',
  2 => '`total_points` %1$s;',
  3 => '`galaxy` %1$s, `system` %1$s, `planet` %1$s;',
  4 => '`ally_register_time` %1$s;',
  5 => '`onlinetime` %1$s;'
);

$template = SnTemplate::gettemplate('ali_members', true);

$userList = db_user_list("`ally_id`= {$user['ally_id']} ORDER BY " . sprintf($sort1s[$sort1], $sort2s), false,
  'id, username, galaxy, system, planet, onlinetime, ally_rank_id, ally_register_time, total_points');

// while ($userRow = db_fetch($userList))
foreach($userList as $userRow)
{
  $i++;
  if (!isset($ranks[$userRow['ally_rank_id']]))
  {
    $userRow['ally_rank_id'] = 0;
  }

  if ($ally['ally_owner'] == $userRow['id'])
  {
    $ally_range = ($ally['ally_owner_range'])?$ally['ally_owner_range']:$lang['Founder'];
  }
  else
  {
    if($user_admin)
    {
      $ally_range = '<select onchange="window.location=\'alliance.php?mode=admin&edit=members&id_user=' . $userRow['id'] . '&id_rank=\' + this.value">';

      foreach($ranks as $rankID => $rankArray){
        $ally_range .= '<option value="' . $rankID . '"';
        if($rankID == $userRow['ally_rank_id'])
          $ally_range .= " selected";
        $ally_range .= '>' . $rankArray['name'];
        $ally_range .= '</option>';
      }
      $ally_range .= '</select>';
    }
    else
    {
      $ally_range = $ranks[$userRow['ally_rank_id']]['name'];
    }
  }

  $last_active = time() - $userRow["onlinetime"];
  if($user_admin)
  {
    if ( $last_active < 60 )
    {
      $tmp = "lime>{$lang['On']}";
    }
    elseif ($last_active < 60 * 60)
    {
      $last_active = round($last_active / 60);
      $tmp = "lime>{$last_active} {$lang['sys_min_short']}";
    }
    elseif ($last_active < 60 * 60 * 24)
    {
      $last_active = round( $last_active / (60 * 60));
      $tmp = "green>{$last_active} {$lang['sys_hrs_short']}";
    }
    else
    {
      $last_active = round( $last_active / (60 * 60 * 24));
      if ($last_active < 7)
      {
        $tmp = "yellow";
      }
      elseif ($last_active < 30)
      {
        $tmp = "orange";
      }
      else
      {
        $tmp = "red";
      }
      $tmp .= ">{$last_active} {$lang['sys_day_short']}";
    }
  }
  else
  {
    if($user_onlinestatus)
    {
      if ( $last_active < 60 * 5 )
      {
        $tmp = "lime>{$lang['On']}";
      }
      elseif ($last_active < 60 * 15)
      {
        $tmp = "yellow>{$lang['ali_lessThen15min']}";
      }
      else
      {
        $tmp = "red>{$lang['Off']}";
      }
    }
    else
    {
      $sort1 = max($sort1, 4);
      $tmp = "orange>-";
    }
  }


  $template->assign_block_vars('member', array(
    'COUNT' => $i,
    'USER_ID' => $userRow['id'],
    'USER_NAME' => $userRow['username'],
    'GALAXY' => $userRow['galaxy'],
    'SYSTEM' => $userRow['system'],
    'PLANET' => $userRow['planet'],
    'POINTS' => HelperString::numberFloorAndFormat($userRow['total_points']),
    'ONLINE' => '<font color=' . $tmp . '</font>',
    'RANK' => $ally_range,
    'REGISTER_TIME' => date(FMT_DATE_TIME, $userRow['ally_register_time']),
    'SHOW_KICK' => $user['id'] != $userRow['id'] && $ally['ally_owner'] != $userRow['id'],
  ));
}

if ($i != $ally['ally_members'])
{
  doquery("UPDATE {{alliance}} SET `ally_members`='{$i}' WHERE `id`='{$ally['id']}'");
}

$template->assign_vars(array(
  'mode' => $mode . ($edit ? "&edit=" . $edit : ''),
  'ADMIN_MODE' => $user_can_kick,
  'onlineMessage' => $user_admin ? $lang['ali_sys_lastActive'] : $lang['Online'],
  'memberCount' => count($userList),
  'memberslist' => $page_list,
  's' => $sort2,
));

SnTemplate::display($template, $lang['members_admin']);
