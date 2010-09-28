<?php
/**
 * index.php - overview.php
 *
 * 2.3 - copyright (c) 2010 by Gorlum for http://supernova.ws
 *     [*] Complying with PCG
 * 2.2 - copyright (c) 2010 by Gorlum for http://supernova.ws
 *     [+] Redo flying fleet list
 * 2.1 - copyright (c) 2010 by Gorlum for http://supernova.ws
 *     [+] Planets on planet list now have indication of planet fill
 *     [+] Planets on planet list now have indication when there is enemy fleet flying to planet
 * 2.0 - copyright (c) 2010 by Gorlum for http://supernova.ws
 *     [+] Now there is full planet list on right side of screen a-la oGame
 *     [+] Planet list now include icons for buildings/tech/fleet on progress
 * 1.5 - copyright (c) 2010 by Gorlum for http://supernova.ws
 *     [*] Subplanet timers now use sn_timer.js library
 * 1.4 - copyright (c) 2010 by Gorlum for http://supernova.ws
 *     [*] All mainplanet timers now use new sn_timer.js library
 * 1.3 - copyright (c) 2010 by Gorlum for http://supernova.ws
 *     [*] Adjusted layouts of player infos
 * 1.2 - copyright (c) 2010 by Gorlum for http://supernova.ws
 *     [*] Adjusted layouts of planet info
 * 1.1 - Security checks by Gorlum for http://supernova.ws
 * @version 1
 * @copyright 2008 By Chlorel for XNova
 */

if (filesize('config.php') == 0)
{
  header('location: install/');
  exit();
}

define('INSIDE'  , true);
define('INSTALL' , false);

$ugamela_root_path = './';
include("{$ugamela_root_path}extension.inc");
include("{$ugamela_root_path}common.{$phpEx}");

if ($IsUserChecked == false) {
  includeLang('login');
  header('Location: login.php');
}

if($user['authlevel'] >= 2)
{
  if(file_exists("{$ugamela_root_path}badqrys.txt"))
  {
    if(filesize("{$ugamela_root_path}badqrys.txt") > 0)
    {
      echo "<a href=\"badqrys.txt\" target=\"_NEW\"><font color=\"red\">{$lang['ov_hack_alert']}</font</a>";
    }
  }
}

check_urlaubmodus ($user);

function int_assign_event($fleet, $ov_label)
{
  global $fleets, $fleet_number, $planetrow, $planet_end_type, $user;

  $fleet['ov_label'] = $ov_label;
  switch($ov_label)
  {
    case 0:
      $fleet['ov_time'] = $fleet['fleet_start_time'];
      $is_this_planet = (
        ($planetrow['galaxy'] == $fleet['fleet_end_galaxy']) AND
        ($planetrow['system'] == $fleet['fleet_end_system']) AND
        ($planetrow['planet'] == $fleet['fleet_end_planet']) AND
        ($planetrow['planet_type'] == $planet_end_type));
    break;

    case 1:
      $fleet['ov_time'] = $fleet['fleet_end_stay'];
      $is_this_planet = (
        ($planetrow['galaxy'] == $fleet['fleet_end_galaxy']) AND
        ($planetrow['system'] == $fleet['fleet_end_system']) AND
        ($planetrow['planet'] == $fleet['fleet_end_planet']) AND
        ($planetrow['planet_type'] == $planet_end_type));
    break;

    case 2:
    case 3:
      $fleet['ov_time'] = $fleet['fleet_end_time'];
      $is_this_planet = (
        ($planetrow['galaxy'] == $fleet['fleet_start_galaxy']) AND
        ($planetrow['system'] == $fleet['fleet_start_system']) AND
        ($planetrow['planet'] == $fleet['fleet_start_planet']) AND
        ($planetrow['planet_type'] == $fleet['fleet_start_type']));
    break;

    case 3:
      $fleet['ov_time'] = $fleet['zeit'];
      $is_this_planet = (
        ($planetrow['galaxy'] == $fleet['galaxy']) AND
        ($planetrow['system'] == $fleet['system']) AND
        ($planetrow['planet'] == $fleet['planet']) AND
        ($planetrow['planet_type'] == $fleet['fleet_start_type']));
    break;
  }

  $fleet['ov_this_planet'] = $is_this_planet;

  if($fleet['fleet_owner'] == $user['id'])
  {
    $user_data = $user;
  }
  else
  {
    $user_data = doquery("SELECT * FROM `{{users}}` WHERE `id` = {$fleet['fleet_owner']};", '', true);
  };

  $fleets[] = tpl_parse_fleet($fleet, ++$fleet_number, $user_data);
}

// Compare function to sort fleet in time order
function int_fleet_compare($a, $b)
{
  if($a['fleet']['OV_THIS_PLANET'] == $b['fleet']['OV_THIS_PLANET'])
  {
    if($a['fleet']['OV_LEFT'] == $b['fleet']['OV_LEFT'])
    {
      return 0;
    }
    return ($a['fleet']['OV_LEFT'] < $b['fleet']['OV_LEFT']) ? -1 : 1;
  }
  else
  {
    return $a['fleet']['OV_THIS_PLANET'] ? -1 : 1;
  }
}

function int_template_assign(&$fleets)
{
  global $template;

  usort($fleets, 'int_fleet_compare');

  foreach($fleets as $fleet_data)
  {
    $template->assign_block_vars('fleets', $fleet_data['fleet']);

    foreach($fleet_data['ships'] as $ship_data)
    {
      $template->assign_block_vars('fleets.ships', $ship_data);
    }
  }
}

// includeLang('resources');
includeLang('overview');

$mode                 = $_GET['mode'];
$POST_deleteid        = intval($_POST['deleteid']);
$POST_action          = SYS_mysqlSmartEscape($_POST['action']);
$POST_kolonieloeschen = intval($_POST['kolonieloeschen']);
$POST_newname         = SYS_mysqlSmartEscape($_POST['newname']);

switch ($mode)
{
  case 'renameplanet':
    // -----------------------------------------------------------------------------------------------
    if ($POST_action == $lang['namer'])
    {
      // Reponse au changement de nom de la planete
      $UserPlanet     = CheckInputStrings ( $POST_newname );
      $newname        = mysql_escape_string(strip_tags(trim( $UserPlanet )));
      if ($newname)
      {
        // Deja on met jour la planete qu'on garde en memoire (pour le nom)
        $planetrow['name'] = $newname;
        // Ensuite, on enregistre dans la base de donnГ©es
        doquery("UPDATE {{planets}} SET `name` = '{$newname}' WHERE `id` = '{$user['current_planet']}' LIMIT 1;");
      }
    }
    elseif ($POST_action == $lang['colony_abandon'])
    {
      // Cas d'abandon d'une colonie
      // Affichage de la forme d'abandon de colonie
      $parse                   = $lang;
      $parse['planet_id']      = $planetrow['id'];
      $parse['galaxy_galaxy']  = $planetrow['galaxy'];
      $parse['galaxy_system']  = $planetrow['system'];
      $parse['galaxy_planet']  = $planetrow['planet'];
      $parse['planet_name']    = $planetrow['name'];

      display(parsetemplate(gettemplate('overview_deleteplanet'), $parse), $lang['rename_and_abandon_planet']);
    }
    elseif ($POST_kolonieloeschen == 1 && $POST_deleteid == $user['current_planet'])
    {
      if (md5($_POST['pw']) == $user['password'] && $user['id_planet'] != $user['current_planet'])
      {
        $destruyed        = $time_now + 60 * 60 * 24;
        doquery("UPDATE {{planets}} SET `destruyed`='{$destruyed}', `id_owner`='0' WHERE `id`='{$user['current_planet']}' LIMIT 1;");
        doquery("UPDATE {{users}} SET `current_planet` = `id_planet` WHERE `id` = '{$user['id']}' LIMIT 1");
        message($lang['deletemessage_ok'], $lang['colony_abandon'], 'overview.php?mode=renameplanet');
      }
      elseif ($user['id_planet'] == $user['current_planet'])
      {
        message($lang['deletemessage_wrong'], $lang['colony_abandon'], 'overview.php?mode=renameplanet');
      }
      else
      {
        message($lang['deletemessage_fail'] , $lang['colony_abandon'], 'overview.php?mode=renameplanet');
      }
    }

    $parse = $lang;

    $parse['planet_id']     = $planetrow['id'];
    $parse['galaxy_galaxy'] = $planetrow['galaxy'];
    $parse['galaxy_system'] = $planetrow['system'];
    $parse['galaxy_planet'] = $planetrow['planet'];
    $parse['planet_name']   = $planetrow['name'];

    $page .= parsetemplate(gettemplate('overview_renameplanet'), $parse);

    display($page, $lang['rename_and_abandon_planet']);
  break;

  default:
    // --- Gestion des messages ----------------------------------------------------------------------
    $template = gettemplate('overview', true);

    // --- Gestion Officiers -------------------------------------------------------------------------
    // Passage au niveau suivant, ajout du point de compГ©tence et affichage du passage au nouveau level

    if ($user['xpminier']>=rpg_get_miner_xp($user['lvl_minier']))
    {
      $minerXPLevel = $user['lvl_minier'];
      while ($user['xpminier']>=rpg_get_miner_xp($minerXPLevel))
      {
        $minerXPLevel++;
      }

      $miner_lvl_up = $minerXPLevel - $user['lvl_minier'];
      doquery("UPDATE `{{users}}` SET `lvl_minier` = `lvl_minier` + '{$miner_lvl_up}' WHERE `id` = '{$user['id']}'");
      rpg_pointsAdd($user['id'], $miner_lvl_up, 'Level Up For Structure Building');
      $user['lvl_minier'] += $miner_lvl_up;
      $user['rpg_points'] += $miner_lvl_up;
      $isNewLevelMiner = true;
    }

    if ($user['xpraid']>=RPG_get_raider_xp($user['lvl_raid']))
    {
      $raidXPLevel = $user['lvl_raid'];
      while ($user['xpraid']>=RPG_get_raider_xp($raidXPLevel))
      {
        $raidXPLevel++;
      }

      $raid_lvl_up = $raidXPLevel - $user['lvl_raid'];
      doquery("UPDATE `{{users}}` SET `lvl_raid` = `lvl_raid` + '{$raid_lvl_up}' WHERE `id` = '{$user['id']}'");
      rpg_pointsAdd($user['id'], $raid_lvl_up, 'Level Up For Raids');
      $user['lvl_raid']   += $raid_lvl_up;
      $user['rpg_points'] += $raid_lvl_up;
      $isNewLevelRaid = true;
    }

    // -----------------------------------------------------------------------------------------------
    // Filling table with fleet events relating to current users
    $fleets = array();
    $fleet_number = 0;
    $flying_fleets_mysql = doquery(
      "SELECT DISTINCT * FROM {{fleets}} WHERE `fleet_owner` = '{$user['id']}' OR `fleet_target_owner` = '{$user['id']}';"
    );
    while ($fleet = mysql_fetch_array($flying_fleets_mysql))
    {
      $planet_start_type = $fleet['fleet_start_type'] == 3 ? 3 : 1;
      $planet_start = doquery(
        "SELECT `name` FROM {{planets}}
          WHERE
            galaxy = {$fleet['fleet_start_galaxy']} AND
            system = {$fleet['fleet_start_system']} AND
            planet = {$fleet['fleet_start_planet']} AND
            planet_type = {$planet_start_type}
        ", '', true);
      $fleet['fleet_start_name'] = $planet_start['name'];

      if($fleet['fleet_end_planet'] > $config->game_maxPlanet)
      {
        $fleet['fleet_end_name'] = $lang['ov_fleet_exploration'];
      }
      elseif($fleet['fleet_mission'] == MT_COLONIZE)
      {
        $fleet['fleet_end_name'] = $lang['ov_fleet_colonization'];
      }
      else
      {
        $planet_end_type = $fleet['fleet_end_type'] == 3 ? 3 : 1;
        $planet_end = doquery(
          "SELECT `name` FROM {{planets}}
            WHERE
              galaxy = {$fleet['fleet_end_galaxy']} AND
              system = {$fleet['fleet_end_system']} AND
              planet = {$fleet['fleet_end_planet']} AND
              planet_type = {$planet_end_type}
          ", '', true);
        $fleet['fleet_end_name'] = $planet_end['name'];
      }

      if($fleet['fleet_start_time'] > $time_now)
      {
        int_assign_event($fleet, 0);
      }
      if($fleet['fleet_end_stay'] > $time_now)
      {
        int_assign_event($fleet, 1);
      }
      if($fleet['fleet_end_time'] > $time_now && $fleet['fleet_owner'] == $user['id'] &&
        !($fleet['fleet_mess'] == 0 &&
          ($fleet['fleet_mission'] == MT_RELOCATE || $fleet['fleet_mission'] == MT_COLONIZE)))
      {
        int_assign_event($fleet, 2);
      }
    }

    // -----------------------------------------------------------------------------------------------
    // Adding missile attacks to fleet event table
    $iraks_query = doquery("SELECT * FROM `{{iraks}}` WHERE `owner` = '{$user['id']}'");
    while ($irak = mysql_fetch_array ($iraks_query))
    {
      if ($irak['zeit'] >= $time_now) {
        $irak['fleet_id']             = -$irak['anzahl'];
        $irak['fleet_owner']          = $irak['owner'];
        $irak['fleet_mission']        = MT_MISSILE;
        $irak['fleet_array']          = "503,{$irak['anzahl']};";
        $irak['fleet_amount']         = $irak['anzahl'];

        $planet_end = doquery("SELECT `name` FROM `{{planets}}` WHERE
          `galaxy` = '{$irak['galaxy']}' AND
          `system` = '{$irak['system']}' AND
          `planet` = '{$irak['planet']}' AND
          `planet_type` = '1'", '', true);
        $irak['fleet_end_galaxy']     = $irak['galaxy'];
        $irak['fleet_end_system']     = $irak['system'];
        $irak['fleet_end_planet']     = $irak['planet'];
        $irak['fleet_end_type']       = 1;
        $irak['fleet_end_time']       = $irak['zeit'];
        $irak['fleet_end_name']       = $planet_end['name'];

        $planet_start = doquery("SELECT `name` FROM `{{planets}}` WHERE
          `galaxy` = '{$irak['galaxy_angreifer']}' AND
          `system` = '{$irak['system_angreifer']}' AND
          `planet` = '{$irak['planet_angreifer']}' AND
          `planet_type` = '1'", '', true);
        $irak['fleet_start_galaxy']   = $irak['galaxy_angreifer'];
        $irak['fleet_start_system']   = $irak['system_angreifer'];
        $irak['fleet_start_planet']   = $irak['planet_angreifer'];
        $irak['fleet_start_type']     = 1;
        $irak['fleet_start_name']     = $planet_start['name'];
        //$irak['fleet_start_time']   = $irak['zeit'];

        int_assign_event($irak, 3);
      }
    }

    int_template_assign($fleets);

    // -----------------------------------------------------------------------------------------------
    // --- Gestion de la liste des planetes ----------------------------------------------------------
    // Planetes ...
    switch($user['planet_sort'])
    {
      case 1:
        $planetSort = '`galaxy` %1$s, `system` %1$s, `planet` %1$s';
      break;

      case 2:
        $planetSort = '`name` %s';
      break;

      default:
        $planetSort = '`id` %s';
      break;
    }

    if($user['planet_sort_order'])
    {
      $planetSort = sprintf($planetSort, 'DESC');
    }
    else
    {
      $planetSort = sprintf($planetSort, 'ASC');
    }

    $planets_query = doquery("SELECT * FROM {{planets}} WHERE id_owner='{$user['id']}' AND planet_type = 1 ORDER BY {$planetSort};");
    $Colone  = 1;

    while ($UserPlanet = mysql_fetch_array($planets_query))
    {
      $buildArray = array();
      if ($UserPlanet['b_building']) {
        UpdatePlanetBatimentQueueList ($UserPlanet, $user);
        if ( $UserPlanet['b_building'] != 0 )
        {
          $QueueArray      = explode ( ';', $UserPlanet['b_building_id'] );
          $CurrentBuild    = explode ( ',', $QueueArray[0] );

          $buildArray['BUILD_NAME']  = $lang['tech'][$CurrentBuild[0]];
          $buildArray['BUILD_LEVEL'] = $CurrentBuild[1];
          $buildArray['BUILD_TIME']  = pretty_time( $CurrentBuild[3] - time() );
        }
        else
        {
          CheckPlanetUsedFields ($UserPlanet);
        }
      }

      $enemy_fleet = doquery("SELECT count(*) AS fleets_count FROM {{fleets}}
        WHERE
          fleet_end_galaxy = {$UserPlanet['galaxy']} AND
          fleet_end_system = {$UserPlanet['system']} AND
          fleet_end_planet = {$UserPlanet['planet']} AND
          fleet_end_type   = 1 AND
          fleet_mess       = 0 AND
          (fleet_mission = 1 OR fleet_mission = 2)
      ", '', true);

      $moon = doquery("SELECT * FROM {{planets}} WHERE `parent_planet` = '{$UserPlanet['id']}' AND `planet_type` = 3;", '', true);
      if($moon)
      {
        $enemy_fleet_moon = doquery("SELECT count(*) AS fleets_count FROM {{fleets}}
          WHERE
            fleet_end_galaxy = {$UserPlanet['galaxy']} AND
            fleet_end_system = {$UserPlanet['system']} AND
            fleet_end_planet = {$UserPlanet['planet']} AND
            fleet_end_type   = 3 AND
            fleet_mess       = 0 AND
            (fleet_mission = 1 OR fleet_mission = 2 OR fleet_mission = 9)", '', true);
        $moon_fill = min(100, floor($moon['field_current'] / CalculateMaxPlanetFields($moon) * 100));
      }
      else
      {
        $moon_fill = 0;
      }

      $moon_fleets = flt_get_fleets_to_planet($moon);
      $template->assign_block_vars('planet', array_merge(tpl_parse_planet($UserPlanet),
        array(
          'MOON_ID'      => $moon['id'],
          'MOON_NAME'    => $moon['name'],
          'MOON_IMG'     => $moon['image'],
          'MOON_FILL'    => min(100, $moon_fill),
          'MOON_ENEMY'   => $moon_fleets['enemy_count'],
        ), $buildArray));
    }

    // -----------------------------------------------------------------------------------------------
    $parse                         = $lang;

    // -----------------------------------------------------------------------------------------------
    // News Frame ...
    if ($config->game_news_overview)
    {
      $lastAnnounces = doquery("SELECT *, UNIX_TIMESTAMP(`tsTimeStamp`) AS unix_time FROM {{announce}} WHERE UNIX_TIMESTAMP(`tsTimeStamp`)<={$time_now} ORDER BY `tsTimeStamp` DESC LIMIT {$config->game_news_overview}");

      while ($lastAnnounce = mysql_fetch_array($lastAnnounces))
      {
        $template->assign_block_vars('news', array(
          'TIME'     => $lastAnnounce['tsTimeStamp'],
          'ANNOUNCE' => sys_bbcodeParse($lastAnnounce['strAnnounce']),
          'IS_NEW'   => $lastAnnounce['unix_time'] + $config->game_news_actual > $time_now,
        ));
      }
    }

    // SuperNova's banner for users to use
    if ($config->int_banner_showInOverview)
    {
      $delimiter = strpos($config->int_banner_URL, '?') ? '&' : '?';
      $template->assign_vars(array(
        'BANNER_URL' => "http://{$_SERVER["SERVER_NAME"]}{$config->int_banner_URL}{$delimiter}id={$user['id']}",
      ));
    }

    // SuperNova's userbar to use on forums
    if ($config->int_userbar_showInOverview)
    {
      $delimiter = strpos($config->int_userbar_URL, '?') ? '&' : '?';

      $template->assign_vars(array(
        'USERBAR_URL' => "http://{$_SERVER["SERVER_NAME"]}{$config->int_userbar_URL}{$delimiter}id={$user['id']}",
      ));
    }

    // --- Gestion de l'affichage d'une lune ---------------------------------------------------------
    if($planetrow['planet_type'] == 1)
    {
      $lune = doquery("SELECT * FROM {{planets}} WHERE `parent_planet` = '{$planetrow['id']}' AND `planet_type` = 3;", '', true);
    }
    else
    {
      $lune = doquery("SELECT * FROM {{planets}} WHERE `id` = '{$planetrow['parent_planet']}' AND `planet_type` = 1;", '', true);
    }

    if ($lune)
    {
      $template->assign_vars(array(
        'MOON_ID' => $lune['id'],
        'MOON_IMG' => $lune['image'],
        'MOON_NAME' => $lune['name'],
      ));
    }
    // Moon END


    $StatRecord = doquery("SELECT * FROM {{table}} WHERE `stat_type` = '1' AND `stat_code` = '1' AND `id_owner` = '". $user['id'] ."';", 'statpoints', true);

    $ile                           = $StatRecord['total_old_rank'] - $StatRecord['total_rank'];
    if ($ile >= 1)
    {
      $parse['ile']              = "<font color=lime>+" . $ile . "</font>";
    }
    elseif ($ile < 0)
    {
      $parse['ile']              = "<font color=red>-" . $ile . "</font>";
    }
    elseif ($ile == 0)
    {
      $parse['ile']              = "<font color=lightblue>" . $ile . "</font>";
    }

    $day_of_week = $lang['weekdays'][date('w')];
    $day         = date('d');
    $month       = $lang['months'][date('m')];
    $year        = date('Y');
    $hour        = date('H');
    $min         = date('i');
    $sec         = date('s');

    if ($planetrow['b_building'])
    {
      UpdatePlanetBatimentQueueList ( $planetrow, $user );
    }

    $planet_fill = floor($planetrow['field_current'] / CalculateMaxPlanetFields($planetrow) * 100);
    $planet_fill = $planet_fill > 100 ? 100 : $planet_fill;

    //Подсчет кол-ва онлайн и кто онлайн
    $time = $time_now - 15*60;
    $OnlineUsersNames2 = doquery("SELECT `username` FROM {{users}} WHERE `onlinetime`>'{$time}'");

/*
    $ally = $user['ally_id'];
    $OnlineUsersNames = doquery("SELECT `username` FROM {{table}} WHERE `onlinetime`>'".$time."' AND `ally_id`='".$ally."' AND `ally_id` != '0'",'users');

    $names = '';
    while ($OUNames = mysql_fetch_array($OnlineUsersNames)) {
      $names .= $OUNames['username'];
      $names .= ", ";
    }
    $parse['MembersOnline2'] = $names;
*/
/*
    //Последние сообщения чата.
    $mess = doquery("SELECT `user`,`message` FROM {{table}} WHERE `ally_id` = '0' ORDER BY `messageid` DESC LIMIT 5", 'chat');
    $msg = '<table>';
    while ($result = mysql_fetch_array($mess)) {
      //$str = substr($result['message'], 0, 85);
      $str = $result['message'];
      $usr = $result['user'];
      $msg .= "<tr><td align=\"left\">".$usr.":</td><td>".$str."</td></tr>";
    }
    $msg .= '</table>';
*/
    $template->assign_vars(array(
      'dpath'                => $dpath,
      'TIME_NOW'             => $time_now,
      'TIME_TEXT'            => "$day_of_week, $day $month $year {$lang['ov_of_year']},",

      'USERS_ONLINE'         => mysql_num_rows($OnlineUsersNames2),
      'USERS_TOTAL'          => $config->users_amount,

      'USER_ID'              => $user['id'],
      'user_username'        => $user['username'],

      'NEW_MESSAGES'         => $user['new_message'],
      'NEW_LEVEL_MINER'      => $isNewLevelMiner,
      'NEW_LEVEL_RAID'       => $isNewLevelRaid,

      'PLANET_ID'            => $planetrow['id'],
      'PLANET_GALAXY'        => $planetrow['galaxy'],
      'PLANET_SYSTEM'        => $planetrow['system'],
      'PLANET_PLANET'        => $planetrow['planet'],
      'PLANET_NAME'          => $planetrow['name'],
      'PLANET_TYPE_TEXT'     => $lang['sys_planet_type'][$planetrow['planet_type']],
      'BUILDING'             => int_buildCounter($planetrow, 'building'),
      'HANGAR'               => int_buildCounter($planetrow, 'hangar'),
      'TECH'                 => int_buildCounter($planetrow, 'tech'),
      'planet_diameter'      => pretty_number($planetrow['diameter']),
      'planet_field_current' => $planetrow['field_current'],
      'planet_field_max'     => CalculateMaxPlanetFields($planetrow),
      'PLANET_FILL'          => floor($planetrow['field_current'] / CalculateMaxPlanetFields($planetrow) * 100),
      'PLANET_FILL_BAR'      => $planet_fill,
      'metal_debris'         => pretty_number($planetrow['debris_metal']),
      'crystal_debris'       => pretty_number($planetrow['debris_crystal']),
      'CAN_RECYCLE'          => ($planetrow['debris_metal'] || $planetrow['debris_crystal']) && $planetrow[$resource[209]],
      'planet_temp_min'      => $planetrow['temp_min'],
      'planet_temp_max'      => $planetrow['temp_max'],

      'builder_xp'           => $user['xpminier'],
      'builder_lvl'          => $user['lvl_minier'],
      'builder_lvl_up'       => rpg_get_miner_xp($user['lvl_minier']),
      'raid_xp'              => $user['xpraid'],
      'raid_lvl'             => $user['lvl_raid'],
      'raid_lvl_up'          => RPG_get_raider_xp($user['lvl_raid']),
      'raids'                => $user['raids'],
      'raidswin'             => $user['raidswin'],
      'raidsloose'           => $user['raidsloose'],
      'user_points'          => pretty_number( $StatRecord['build_points'] ),
      'user_fleet'           => pretty_number( $StatRecord['fleet_points'] ),
      'player_points_tech'   => pretty_number( $StatRecord['tech_points'] ),
      'user_defs_points'     => pretty_number( $StatRecord['defs_points'] ),
      'total_points'         => pretty_number( $StatRecord['total_points'] ),
      'user_rank'            => $StatRecord['total_rank'],
      'RANK_DIFF'            => $StatRecord['total_old_rank'] - $StatRecord['total_rank'],

      'ADMIN_EMAIL'          => $config->game_adminEmail,

      'GAME_NEWS_OVERVIEW'   => $config->game_news_overview,

      //'LastChat'       => CHT_messageParse($msg),
    ));

    display(parsetemplate($template, $parse), "{$lang['ov_overview']} - {$lang['sys_planet_type'][$planetrow['planet_type']]} {$planetrow['name']} [{$planetrow['galaxy']}:{$planetrow['system']}:{$planetrow['planet']}]");
  break;
}
?>
