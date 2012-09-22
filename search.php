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

lng_include('search');

$searchtext = sys_get_param_str('searchtext');
$type = sys_get_param_str('type');


$template = gettemplate('search', true);

if($searchtext && $type)
{
  switch($type)
  {
    case "planetname":
      $sql = "SELECT
          p.galaxy, p.system, p.planet, p.planet_type, p.name as planet_name,
          u.id as uid, u.username, u.ally_id, u.id_planet,
          u.total_points, u.total_rank,
          u.ally_tag, u.ally_name
        FROM
          {{planets}} AS p
          LEFT JOIN {{users}} AS u ON u.id = p.id_owner
        WHERE
          name LIKE '%{$searchtext}%' AND u.user_as_ally IS NULL
        ORDER BY
          ally_tag, username, planet_name
        LIMIT 30;";
      $search = doquery($sql);
    break;

    case "ally":
      $search = doquery("SELECT ally_name, ally_tag, total_rank, ally_members FROM {{alliance}} WHERE ally_tag LIKE '%{$searchtext}%' OR ally_name LIKE '%{$searchtext}%' LIMIT 30");
    break;

    case "playername":
    default:
      $sql = "SELECT
          u.id as uid, u.username, u.ally_id, u.id_planet, u.total_points, u.total_rank,
          p.galaxy, p.system, p.planet, p.planet_type, p.name as planet_name,
          u.ally_tag, u.ally_name
        FROM 
          {{users}} AS u
          LEFT JOIN {{planets}} AS p ON p.id_owner = u.id AND p.id=u.id_planet
        WHERE
          username LIKE '%{$searchtext}%' AND u.user_as_ally IS NULL
        ORDER BY
          ally_tag, username, planet_name
        LIMIT 30;";
      $search = doquery($sql);
    break;
  }

  while($row = mysql_fetch_assoc($search))
  {
    if($type=='playername' || $type=='planetname')
    {
      $template->assign_block_vars('search_result', array(
        'PLAYER_ID' => $row['uid'],
        'PLAYER_NAME' => htmlentities($row['username'], ENT_COMPAT, 'UTF-8'),
        'PLAYER_RANK' => pretty_number($row['total_rank']),
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
        'ALLY_RANK' => pretty_number($row['total_rank']),
        'ALLY_MEMBERS' => pretty_number($row['ally_members']),
      ));
    }
  }
}

$search_type = array(
  'playername' => 'srch_player_name',
  'planetname' => 'srch_planet_name',
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
  'STATS_HIDE_PM_LINK' => $config->stats_hide_pm_link,
));

display($template);

?>
