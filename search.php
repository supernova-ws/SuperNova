<?php
/**
 * search.php
 *
 * 1.3 copyright (c) 2009-2010 by Gorlum for http://supernova.ws
 *   [%] Fixed search of players without alliance
 * 1.2 - Security checks & tests by Gorlum for http://supernova.ws
 * @version 1.1
 * @copyright 2009 by angelus_ira for Project. XNova
 * @copyright 2008 by ??????? for XNova
 */

include('common.' . substr(strrchr(__FILE__, '.'), 1));

if(SN::$config->game_mode == GAME_BLITZ) {
  SnTemplate::messageBox($lang['sys_blitz_page_disabled'], $lang['sys_error'], 'overview.php', 10);
  die();
}

lng_include('search');

$searchtext = sys_get_param_str('searchtext');
$type = sys_get_param_str('type');


$template = SnTemplate::gettemplate('search', true);

if($searchtext && $type)
{
  switch($type)
  {
    case "planetname":
      // $search = db_planet_list_search($searchtext);
    break;

    case "ally":
      $search = doquery("SELECT ally_name, ally_tag, total_rank, ally_members FROM {{alliance}} WHERE ally_tag LIKE '%{$searchtext}%' OR ally_name LIKE '%{$searchtext}%' LIMIT 30");
    break;

    case "playername":
    default:
      $search = db_user_list_search($searchtext);
    break;
  }

  while($row = db_fetch($search))
  {
    if($type=='playername' || $type=='planetname')
    {
      $template->assign_block_vars('search_result', array(
        'PLAYER_ID' => $row['uid'],
        'PLAYER_NAME' => htmlentities($row['username'], ENT_COMPAT, 'UTF-8'),
        'PLAYER_NAME_OLD' => htmlentities($row['player_name'], ENT_COMPAT, 'UTF-8'),
        'PLAYER_RANK' => HelperString::numberFloorAndFormat($row['total_rank']),
        'PLAYER_RANK_RAW' => floatval($row['total_rank']),
        'PLANET_NAME' => htmlentities($row['planet_name'], ENT_COMPAT, 'UTF-8'),
        'PLANET_GALAXY' => $row['galaxy'],
        'PLANET_SYSTEM' => $row['system'],
        'PLANET_PLANET' => $row['planet'],
        'PLANET_TYPE' => $lang['sys_planet_type_sh'][$row['planet_type']],
        'ALLY_NAME' => htmlentities($row['ally_name'], ENT_COMPAT, 'UTF-8'),
        'ALLY_TAG' => htmlentities($row['ally_tag'], ENT_COMPAT, 'UTF-8'),
      ));
    }
    elseif($type=='ally')
    {
      $template->assign_block_vars('search_result', array(
        'ALLY_NAME' => htmlentities($row['ally_name'], ENT_COMPAT, 'UTF-8'),
        'ALLY_TAG' => htmlentities($row['ally_tag'], ENT_COMPAT, 'UTF-8'),
        'ALLY_RANK' => HelperString::numberFloorAndFormat($row['total_rank']),
        'ALLY_RANK_RAW' => floatval($row['total_rank']),
        'ALLY_MEMBERS' => HelperString::numberFloorAndFormat($row['ally_members']),
      ));
    }
  }
}

$search_type = array(
  'playername' => 'srch_player_name',
//  'planetname' => 'srch_planet_name',
  'ally' => 'sys_alliance',
);

foreach($search_type as $type_id => $type_lang)
{
  $template->assign_block_vars('type', array(
    'ID' => $type_id,
    'TEXT' => $lang[$type_lang],
    'SELECTED' => $type_id == $type,
  ));
}

$template->assign_vars(array(
  'PAGE_HEADER' => $lang['Search'],
  'PAGE_HINT' => $lang['srch_page_hint'],
  'TEXT' => $searchtext,
  'IS_ALLY' => $type == 'ally',
  'STATS_HIDE_PM_LINK' => SN::$config->stats_hide_pm_link,
));

SnTemplate::display($template);
