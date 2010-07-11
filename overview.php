<?php
/**
 * index.php - overview.php
 *
 * 1.0s - Security checks by Gorlum for http://supernova.ws
 * @version 1
 * @copyright 2008 By Chlorel for XNova
 */

if (filesize('config.php') == 0) {
  header('location: install/');
  exit();
}

define('INSIDE'  , true);
define('INSTALL' , false);

$ugamela_root_path = './';
include($ugamela_root_path . 'extension.inc');
include($ugamela_root_path . 'common.' . $phpEx);

function int_buildCounter($planetrow, $type){
  global $lang, $user;

  if ( $planetrow["b_{$type}_id"]) {
    $BuildQueue = explode (';', $planetrow["b_{$type}_id"]);
    $CurrBuild  = explode (',', $BuildQueue[0]);
    if($type=='hangar')
      $RestTime   = GetBuildingTime( $user, $planetrow, $CurrBuild[0] ) - $planetrow["b_{$type}"];
    else
      $RestTime   = $planetrow["b_{$type}"] - time();
    $Build  = InsertBuildListScript ( 'overview', $type );
    $Build .= $lang['tech'][$CurrBuild[0]] .' ';
    if($type=='building')
      $Build .= '('. ($CurrBuild[1]) .')';

    $Build .= InsertCounterLaunchScript($RestTime, $planetrow['id'], $type);
  } else {
    $Build = $lang['Free'];
  }

  return $Build;
}

$mode = $_GET['mode'];
$pl = mysql_escape_string($_GET['pl']);
$POST_deleteid = intval($_POST['deleteid']);
$POST_action = SYS_mysqlSmartEscape($_POST['action']);
$POST_kolonieloeschen = intval($_POST['kolonieloeschen']);
$POST_newname = SYS_mysqlSmartEscape($_POST['newname']);

// Русская дата
$dz_tyg=date("w");
$dzien=date("d");
$miesiac=date("m");
$rok=date("Y");
$hour=date("H");
$min=date("i");
$sec=date("s");
switch ($dz_tyg){
case '1': $dz_tyg = 'Понедельник'; break;
case '2': $dz_tyg = 'Вторник'; break;
case '3': $dz_tyg = 'Среда'; break;
case '4': $dz_tyg = 'Четверг'; break;
case '5': $dz_tyg = 'Пятница'; break;
case '6': $dz_tyg = 'Суббота'; break;
case '0': $dz_tyg = 'Воскресенье'; break;
}
switch ($miesiac)
{
case '01': $miesiac = 'Января'; break;
case '02': $miesiac = 'Февраля'; break;
case '03': $miesiac = 'Марта'; break;
case '04': $miesiac = 'Апреля'; break;
case '05': $miesiac = 'Мая'; break;
case '06': $miesiac = 'Июня'; break;
case '07': $miesiac = 'Июля'; break;
case '08': $miesiac = 'Августа'; break;
case '09': $miesiac = 'Сентября'; break;
case '10': $miesiac = 'Октября'; break;
case '11': $miesiac = 'Ноября'; break;
case '12': $miesiac = 'Декабря'; break;
}

if ($IsUserChecked == false) {
  includeLang('login');
  header("Location: login.php");
}

if((filesize($ugamela_root_path.'badqrys.txt') > 0) && ($user['authlevel'] >= 2)){
  echo "<a href=\"badqrys.txt\" target=\"_NEW\"><font color=\"red\">Попытка взлома БД!!!</font</a>";
}

check_urlaubmodus ($user);

includeLang('resources');
includeLang('overview');

$template = gettemplate('overview_body', true);

switch ($mode) {
  case 'renameplanet':
    // -----------------------------------------------------------------------------------------------
    if ($POST_action == $lang['namer']) {
      // Reponse au changement de nom de la planete
      $UserPlanet     = CheckInputStrings ( $POST_newname );
      $newname        = mysql_escape_string(strip_tags(trim( $UserPlanet )));
      if ($newname != "") {
        // Deja on met jour la planete qu'on garde en memoire (pour le nom)
        $planetrow['name'] = $newname;
        // Ensuite, on enregistre dans la base de donnГ©es
        doquery("UPDATE {{table}} SET `name` = '".$newname."' WHERE `id` = '". $user['current_planet'] ."' LIMIT 1;", "planets");
      }

    } elseif ($POST_action == $lang['colony_abandon']) {
      // Cas d'abandon d'une colonie
      // Affichage de la forme d'abandon de colonie
      $parse                   = $lang;
      $parse['planet_id']      = $planetrow['id'];
      $parse['galaxy_galaxy']  = $planetrow['galaxy'];
      $parse['galaxy_system']  = $planetrow['system'];
      $parse['galaxy_planet']  = $planetrow['planet'];
      $parse['planet_name']    = $planetrow['name'];

      $page                   .= parsetemplate(gettemplate('overview_deleteplanet'), $parse);

      // On affiche la forme pour l'abandon de la colonie
      display($page, $lang['rename_and_abandon_planet']);

    } elseif ($POST_kolonieloeschen == 1 && $POST_deleteid == $user['current_planet']) {
      // Controle du mot de passe pour abandon de colonie
      if (md5($_POST['pw']) == $user["password"] && $user['id_planet'] != $user['current_planet']) {
        $destruyed        = time() + 60 * 60 * 24;

        $QryUpdatePlanet  = "UPDATE {{table}} SET ";
        $QryUpdatePlanet .= "`destruyed` = '".$destruyed."', ";
        $QryUpdatePlanet .= "`id_owner` = '0' ";
        $QryUpdatePlanet .= "WHERE ";
        $QryUpdatePlanet .= "`id` = '".$user['current_planet']."' LIMIT 1;";
        doquery( $QryUpdatePlanet , 'planets');

        $QryUpdateUser    = "UPDATE {{table}} SET ";
        $QryUpdateUser   .= "`current_planet` = `id_planet` ";
        $QryUpdateUser   .= "WHERE ";
        $QryUpdateUser   .= "`id` = '". $user['id'] ."' LIMIT 1";
        doquery( $QryUpdateUser, "users");

        // Tout s'est bien passГ© ! La colo a Г©tГ© effacГ©e !!
        message($lang['deletemessage_ok']   , $lang['colony_abandon'], 'overview.php?mode=renameplanet');

      } elseif ($user['id_planet'] == $user["current_planet"]) {
        // Et puis quoi encore ??? On ne peut pas effacer la planete mere ..
        // Uniquement les colonies crГ©es apres coup !!!
        message($lang['deletemessage_wrong'], $lang['colony_abandon'], 'overview.php?mode=renameplanet');
      } else {
        // Erreur de saisie du mot de passe je n'efface pas !!!
        message($lang['deletemessage_fail'] , $lang['colony_abandon'], 'overview.php?mode=renameplanet');
      }
    }

    $parse = $lang;

    $parse['planet_id']     = $planetrow['id'];
    $parse['galaxy_galaxy'] = $planetrow['galaxy'];
    $parse['galaxy_system'] = $planetrow['system'];
    $parse['galaxy_planet'] = $planetrow['planet'];
    $parse['planet_name']   = $planetrow['name'];

    $page                  .= parsetemplate(gettemplate('overview_renameplanet'), $parse);

    // On affiche la page permettant d'abandonner OU de renomme une Colonie / Planete
    display($page, $lang['rename_and_abandon_planet']);
    break;

  default:
    // --- Gestion des messages ----------------------------------------------------------------------
    $Have_new_message = "";
    if ($user['new_message'] != 0) {
      $Have_new_message .= "<tr>";
      if       ($user['new_message'] == 1) {
        $Have_new_message .= "<th colspan=4><a href=messages.$phpEx>". $lang['Have_new_message']."</a></th>";
      } elseif ($user['new_message'] > 1) {
        $Have_new_message .= "<th colspan=4><a href=messages.$phpEx>";
        $m = pretty_number($user['new_message']);
        $Have_new_message .= str_replace('%m', $m, $lang['Have_new_messages']);
        $Have_new_message .= "</a></th>";
      }
      $Have_new_message .= "</tr>";
    }
    // -----------------------------------------------------------------------------------------------

    // --- Gestion Officiers -------------------------------------------------------------------------
    // Passage au niveau suivant, ajout du point de compГ©tence et affichage du passage au nouveau level

    $minerXP = $user['xpminier'];
    $minerXPLevel = $user['lvl_minier'];
    $minerXPLevelUp = RPG_getMinerXP($minerXPLevel);
    if ($minerXP>$minerXPLevelUp) {
      do {
        $minerXPLevel++;
        $minerXPLevelUp = RPG_getMinerXP($minerXPLevel);
      } while ($minerXP>$minerXPLevelUp);
      $QryUpdateUser  = "UPDATE `{{table}}` SET ";
      $QryUpdateUser .= "`lvl_minier` = '".$minerXPLevel."', ";
      $QryUpdateUser .= "`rpg_points` = `rpg_points` + '".($minerXPLevel - $user['lvl_minier'])."' ";
      $QryUpdateUser .= "WHERE ";
      $QryUpdateUser .= "`id` = '". $user['id'] ."'";
      doquery($QryUpdateUser, 'users');
      $HaveNewLevelMineur .= "<tr><th colspan=4><a href=officier.$phpEx>". $lang['Have_new_level_mineur']."</a></th></tr>";
    }

    $raidXP = $user['xpraid'];
    $raidXPLevel = $user['lvl_raid'];
    $raidXPLevelUp = RPG_getRaidXP($raidXPLevel);
    if ($raidXP>$raidXPLevelUp) {
      do {
        $raidXPLevel++;
        $raidXPLevelUp = RPG_getRaidXP($raidXPLevel);
      } while ($raidXP>$raidXPLevelUp);
      $QryUpdateUser  = "UPDATE `{{table}}` SET ";
      $QryUpdateUser .= "`lvl_raid` = '".$raidXPLevel."', ";
      $QryUpdateUser .= "`rpg_points` = `rpg_points` + '".($raidXPLevel - $user['lvl_raid'])."' ";
      $QryUpdateUser .= "WHERE ";
      $QryUpdateUser .= "`id` = '". $user['id'] ."'";
      doquery($QryUpdateUser, 'users');
      $HaveNewLevelMineur .= "<tr><th colspan=4><a href=officier.$phpEx>". $lang['Have_new_level_raid']."</a></th></tr>";
    }

    // -----------------------------------------------------------------------------------------------

    // --- Gestion des flottes personnelles ---------------------------------------------------------
    // Toutes de vert vetues
    $OwnFleets       = doquery("SELECT * FROM {{table}} WHERE `fleet_owner` = '". $user['id'] ."';", 'fleets');
    $Record          = 0;
    while ($FleetRow = mysql_fetch_array($OwnFleets)) {
      $Record++;

      $StartTime   = $FleetRow['fleet_start_time'];
      $StayTime    = $FleetRow['fleet_end_stay'];
      $EndTime     = $FleetRow['fleet_end_time'];

      // Flotte a l'aller
      $Label = "fs";
      if ($StartTime > time()) {
        $fpage[$StartTime] = BuildFleetEventTable ( $FleetRow, 0, true, $Label, $Record );
      }

      if ($FleetRow['fleet_mission'] <> 4) {
        // Flotte en stationnement
        $Label = "ft";
        if ($StayTime > time()) {
          $fpage[$StayTime] = BuildFleetEventTable ( $FleetRow, 1, true, $Label, $Record );
        }

        // Flotte au retour
        $Label = "fe";
        if ($EndTime > time()) {
          $fpage[$EndTime]  = BuildFleetEventTable ( $FleetRow, 2, true, $Label, $Record );
        }
      }
    } // End While

    // -----------------------------------------------------------------------------------------------

    // --- Gestion des flottes autres que personnelles ----------------------------------------------
    // Flotte ennemies (ou amie) mais non personnelles
    $OtherFleets     = doquery("SELECT * FROM {{table}} WHERE `fleet_target_owner` = '".$user['id']."';", 'fleets');

    $Record          = 2000;
    while ($FleetRow = mysql_fetch_array($OtherFleets)) {
      if ($FleetRow['fleet_owner'] != $user['id']) {
        if ($FleetRow['fleet_mission'] != 8) {
          $Record++;
          $StartTime = $FleetRow['fleet_start_time'];
          $StayTime  = $FleetRow['fleet_end_stay'];

          if ($StartTime > time()) {
            $Label = "ofs";
            $fpage[$StartTime] = BuildFleetEventTable ( $FleetRow, 0, false, $Label, $Record );
          }
          if ($FleetRow['fleet_mission'] == 5) {
            // Flotte en stationnement
            $Label = "oft";
            if ($StayTime > time()) {
              $fpage[$StayTime] = BuildFleetEventTable ( $FleetRow, 1, false, $Label, $Record );
            }
          }
        }
      }
    }

    // -----------------------------------------------------------------------------------------------

    // --- Gestion de la liste des planetes ----------------------------------------------------------
    // Planetes ...
    switch($user['planet_sort']){
      case 1: $planetSort = '`galaxy` %1$s, `system` %1$s, `planet` %1$s';break;
      case 2: $planetSort = '`name` %s';break;
      default:$planetSort = '`id` %s';break;
    }
    if($user['planet_sort_order'])
      $planetSort = sprintf($planetSort, 'DESC');
    else
      $planetSort = sprintf($planetSort, 'ASC');

    $planets_query = doquery("SELECT * FROM {{planets}} WHERE id_owner='{$user['id']}' AND planet_type = 1 ORDER BY {$planetSort};");
    $Colone  = 1;

    while ($UserPlanet = mysql_fetch_array($planets_query)) {
      $buildArray = array();
      if ($UserPlanet['b_building'] != 0) {
        UpdatePlanetBatimentQueueList ( $UserPlanet, $user );
        if ( $UserPlanet['b_building'] != 0 ) {
          $QueueArray      = explode ( ";", $UserPlanet['b_building_id'] );
          $CurrentBuild    = explode ( ",", $QueueArray[0] );

          $buildArray['BUILD_NAME']  = $lang['tech'][$CurrentBuild[0]];
          $buildArray['BUILD_LEVEL'] = $CurrentBuild[1];
          $buildArray['BUILD_TIME']  = pretty_time( $CurrentBuild[3] - time() );
        } else {
          CheckPlanetUsedFields ($UserPlanet);
        }
      }

      $moon = doquery("SELECT * FROM {{table}} WHERE `parent_planet` = '{$UserPlanet['id']}' AND `planet_type` = 3;", 'planets', true);

      $template->assign_block_vars('planet', array_merge(
        array(
          'ID'        => $UserPlanet['id'],
          'NAME'      => $UserPlanet['name'],
          'IMAGE'     => $UserPlanet['image'],

          'GALAXY'    => $UserPlanet['galaxy'],
          'SYSTEM'    => $UserPlanet['system'],
          'PLANET'    => $UserPlanet['planet'],

          'MOON_ID'   => $moon['id'],
          'MOON_NAME' => $moon['name'],
          'MOON_IMG'  => $moon['image'],
        ), $buildArray));
    }
    // -----------------------------------------------------------------------------------------------

    // --- Gestion des attaques missiles -------------------------------------------------------------
    $iraks_query = doquery("SELECT * FROM `{{table}}` WHERE `owner` = '" . $user['id'] . "'", 'iraks');
    $Record = 4000;
    while ($irak = mysql_fetch_array ($iraks_query)) {
      $Record++;
      $fpage[$irak['zeit']] = '';

      if ($irak['zeit'] > time()) {
        $time = $irak['zeit'] - time();

        $fpage[$irak['zeit']] .= InsertJavaScriptChronoApplet ( "fm", $Record, $time, true );

        $planet_start = doquery("SELECT * FROM `{{table}}` WHERE
        `galaxy` = '" . $irak['galaxy'] . "' AND
        `system` = '" . $irak['system'] . "' AND
        `planet` = '" . $irak['planet'] . "' AND
        `planet_type` = '1'", 'planets');

        $user_planet = doquery("SELECT * FROM `{{table}}` WHERE
        `galaxy` = '" . $irak['galaxy_angreifer'] . "' AND
        `system` = '" . $irak['system_angreifer'] . "' AND
        `planet` = '" . $irak['planet_angreifer'] . "' AND
        `planet_type` = '1'", 'planets', true);

        if (mysql_num_rows($planet_start) == 1) {
          $planet = mysql_fetch_array($planet_start);
        }

        $fpage[$irak['zeit']] .= "<tr><th><div id=\"bxxfm$Record\" class=\"z\"></div><font color=\"lime\">" . date("H:i:s", $irak['zeit'] + 1 * 60 * 60) . "</font> </th><th colspan=\"3\"><font color=\"#0099FF\">Ракетная атака (" . $irak['anzahl'] . ") с планеты " . $user_planet['name'] . " ";
        $fpage[$irak['zeit']] .= '<a href="galaxy.php?mode=3&galaxy=' . $irak["galaxy_angreifer"] . '&system=' . $irak["system_angreifer"] . '&planet=' . $irak["planet_angreifer"] . '">[' . $irak["galaxy_angreifer"] . ':' . $irak["system_angreifer"] . ':' . $irak["planet_angreifer"] . ']</a>';
        $fpage[$irak['zeit']] .= ' совершена на планету ' . $planet["name"] . ' ';
        $fpage[$irak['zeit']] .= '<a href="galaxy.php?mode=3&galaxy=' . $irak["galaxy"] . '&system=' . $irak["system"] . '&planet=' . $irak["planet"] . '">[' . $irak["galaxy"] . ':' . $irak["system"] . ':' . $irak["planet"] . ']</a>';
        $fpage[$irak['zeit']] .= '</font>';
        $fpage[$irak['zeit']] .= InsertJavaScriptChronoApplet ( "fm", $Record, $time, false );
        $fpage[$irak['zeit']] .= "</th>";
      }
    }

    // -----------------------------------------------------------------------------------------------

    $parse                         = $lang;

    // -----------------------------------------------------------------------------------------------
    // News Frame ...
    if ($game_config['OverviewNewsFrame'] == '1') {
      $parse['NewsFrame']          = "<tr><td colspan=4 class=\"c\">". $lang['ov_news_title'] . "</td></tr>";
      $lastAnnounces = doquery("SELECT * FROM {{table}} WHERE UNIX_TIMESTAMP(`tsTimeStamp`)<={$time_now} ORDER BY `tsTimeStamp` DESC LIMIT 3", 'announce');

      while ($lastAnnounce = mysql_fetch_array($lastAnnounces))
        $parse['NewsFrame']         .= "<tr><th><font color=Cyan>" . $lastAnnounce['tsTimeStamp'] . "</font>" ."</th><th colspan=\"3\" valign=top><div align=justify>" . sys_bbcodeParse($lastAnnounce['strAnnounce']) ."</div></th></tr>";
    }
    // External Chat Frame ...
    if ($game_config['OverviewExternChat'] == '1') {
      $parse['ExternalTchatFrame'] = "<tr><th colspan=\"4\">". stripslashes( $game_config['OverviewExternChatCmd'] ) ."</th></tr>";
    }

    // Banner ADS Google
    if ($game_config['OverviewClickBanner'] != '') {
      $parse['ClickBanner'] = stripslashes( $game_config['OverviewClickBanner'] );
    }

    // SuperNova's banner for users to use
    if ($config->int_banner_showInOverview) {
      $bannerURL = "http://".$_SERVER["SERVER_NAME"]. $config->int_banner_URL;
      $bannerURL .= strpos($bannerURL, '?') ? '&' : '?';
      $bannerURL .= "id=" . $user['id'];
      $parse['bannerframe'] = "<th colspan=\"4\"><img src=\"".$bannerURL."\"><br>".$lang['sys_banner_bb']."<br><input name=\"bannerlink\" type=\"text\" id=\"bannerlink\" value=\"[img]".$bannerURL."[/img]\" size=\"62\"></th></tr>";
    }

    // SuperNova's userbar to use on forums
    if ($config->int_userbar_showInOverview) {
      $userbarURL = "http://" . $_SERVER["SERVER_NAME"] . $config->int_userbar_URL;
      $userbarURL .= strpos($userbarURL, '?') ? '&' : '?';
      $userbarURL .= "id=" . $user['id'];
      $parse['userbarframe'] = "<th colspan=\"4\"><img src=\"".$userbarURL."\"><br>".$lang['sys_userbar_bb']."<br><input name=\"bannerlink\" type=\"text\" id=\"bannerlink\" value=\"[img]".$userbarURL."[/img]\" size=\"62\"></th></tr>";
    }

    // --- Gestion de l'affichage d'une lune ---------------------------------------------------------
    if($planetrow['planet_type'] == 1)
      $lune = doquery("SELECT * FROM {{table}} WHERE `parent_planet` = '{$planetrow['id']}' AND `planet_type` = 3;", 'planets', true);
    else
      $lune = doquery("SELECT * FROM {{table}} WHERE `id` = '{$planetrow['parent_planet']}' AND `planet_type` = 1;", 'planets', true);
    if ($lune) {
      $template->assign_vars(array(
        'MOON_ID' => $lune['id'],
        'MOON_IMG' => $lune['image'],
        'MOON_NAME' => $lune['name'],
      ));
    }
    // Moon END

    $parse['planet_name']          = $planetrow['name'];
    $parse['planet_diameter']      = pretty_number($planetrow['diameter']);
    $parse['planet_field_current'] = $planetrow['field_current'];
    $parse['planet_field_max']     = CalculateMaxPlanetFields($planetrow);
    $parse['planet_temp_min']      = $planetrow['temp_min'];
    $parse['planet_temp_max']      = $planetrow['temp_max'];
    $parse['galaxy_galaxy']        = $planetrow['galaxy'];
    $parse['galaxy_planet']        = $planetrow['planet'];
    $parse['galaxy_system']        = $planetrow['system'];
    $StatRecord = doquery("SELECT * FROM {{table}} WHERE `stat_type` = '1' AND `stat_code` = '1' AND `id_owner` = '". $user['id'] ."';", 'statpoints', true);

    $parse['user_points']          = pretty_number( $StatRecord['build_points'] );
    $parse['user_fleet']           = pretty_number( $StatRecord['fleet_points'] );
    $parse['player_points_tech']   = pretty_number( $StatRecord['tech_points'] );
    $parse['user_defs_points']     = pretty_number( $StatRecord['defs_points'] );
    $parse['total_points']         = pretty_number( $StatRecord['total_points'] );;

    $parse['user_rank']            = $StatRecord['total_rank'];
    $ile = $StatRecord['total_old_rank'] - $StatRecord['total_rank'];
    if ($ile >= 1) {
      $parse['ile']              = "<font color=lime>+" . $ile . "</font>";
    } elseif ($ile < 0) {
      $parse['ile']              = "<font color=red>-" . $ile . "</font>";
    } elseif ($ile == 0) {
      $parse['ile']              = "<font color=lightblue>" . $ile . "</font>";
    }
    $parse['u_user_rank']          = intval($StatRecord['total_rank']);
    $parse['user_username']        = $user['username'];

    if (count($fpage) > 0) {
      ksort($fpage);
      foreach ($fpage as $time => $content) {
        $flotten .= $content . "\n";
      }
    }

    $parse['fleet_list']  = $flotten;
    $parse['energy_used'] = $planetrow["energy_max"] - $planetrow["energy_used"];

    $parse['Have_new_message']      = $Have_new_message;
    $parse['Have_new_level_mineur'] = $HaveNewLevelMineur;
    $parse['Have_new_level_raid']   = $HaveNewLevelRaid;
    $parse['time']=" $dz_tyg, $dzien $miesiac $rok года - ";
    $parse['dpath']                 = $dpath;
    $parse['planet_image']          = $planetrow['image'];
    $parse['max_users']             = $game_config['users_amount'];

    $debris = doquery("SELECT * FROM {{table}} WHERE `id_planet` = '{$planetrow['id']}';", 'galaxy', true);
    $parse['metal_debris']          = pretty_number($debris['metal']);
    $parse['crystal_debris']        = pretty_number($debris['crystal']);
    if (($debris['metal'] || $debris['crystal']) && $planetrow[$resource[209]]) {
      $parse['get_link'] = " (<a href=\"quickfleet.php?mode=8&g=".$planetrow['galaxy']."&s=".$planetrow['system']."&p=".$planetrow['planet']."&t=2\">". $lang['type_mission'][8] ."</a>)";
    } else {
      $parse['get_link'] = '';
    }

    $PlanetID   = $planetrow['id'];
    if ($planetrow['b_building'])
      UpdatePlanetBatimentQueueList ( $planetrow, $user );

    $parse['building'] = int_buildCounter($planetrow, 'building');
    $parse['hangar'] = int_buildCounter($planetrow, 'hangar');
    $parse['tech'] = int_buildCounter($planetrow, 'tech');

    $query = doquery('SELECT username FROM {{table}} ORDER BY register_time DESC', 'users', true);
    $parse['last_user'] = $query['username'];
    $query = doquery("SELECT COUNT(DISTINCT(id)) FROM {{table}} WHERE onlinetime>" . (time()-900), 'users', true);
    $parse['online_users'] = $query[0];
    // $count = doquery(","users",true);
    $parse['users_amount'] = $game_config['users_amount'];

    // Rajout d'une barre pourcentage
    // Calcul du pourcentage de remplissage
    $parse['case_pourcentage'] = floor($planetrow["field_current"] / CalculateMaxPlanetFields($planetrow) * 100) . $lang['o/o'];
    // Barre de remplissage
    $parse['case_barre'] = floor($planetrow["field_current"] / CalculateMaxPlanetFields($planetrow) * 100) * 4;
    // Couleur de la barre de remplissage
    if ($parse['case_barre'] > (100 * 4)) {
      $parse['case_barre'] = 4;
      $parse['case_barre_barcolor'] = '#C00000';
    } elseif ($parse['case_barre'] > (80 * 4)) {
      $parse['case_barre_barcolor'] = '#C0C000';
    } else {
      $parse['case_barre_barcolor'] = '#00C000';
    }

    //Mode AmГ©liorations
    $parse['xpminier']= $user['xpminier'];
    $LvlMinier = $user['lvl_minier'];
    $parse['lvl_minier'] = $LvlMinier;
    $parse['lvl_up_minier'] = RPG_getMinerXP($LvlMinier);

    $parse['xpraid'] = $user['xpraid'];
    $LvlRaid = $user['lvl_raid'];
    $parse['lvl_raid'] = $LvlRaid;
    $parse['lvl_up_raid']   = RPG_getRaidXP($LvlRaid);

    // Nombre de raids, pertes, etc ...
    $parse['MAX_ECONOMIC_LVL'] = MAX_ECONOMIC_LVL;
    $parse['Raids'] = $lang['Raids'];
    $parse['NumberOfRaids'] = $lang['NumberOfRaids'];
    $parse['RaidsWin'] = $lang['RaidsWin'];
    $parse['RaidsLoose'] = $lang['RaidsLoose'];

    $parse['raids'] = $user['raids'];
    $parse['raidswin'] = $user['raidswin'];
    $parse['raidsloose'] = $user['raidsloose'];

    $parse['gameurl'] = GAMEURL;
    $parse['kod'] = $user['kiler'];

    //Подсчет кол-ва онлайн и кто онлайн
    $OnlineUsersNames2 = doquery("SELECT `username` FROM {{table}} WHERE `onlinetime`>'".$time."'",'users');
    $parse['NumberMembersOnline'] = mysql_num_rows($OnlineUsersNames2);

/*
    $time = time() - 15*60;
    $ally = $user['ally_id'];
    $OnlineUsersNames = doquery("SELECT `username` FROM {{table}} WHERE `onlinetime`>'".$time."' AND `ally_id`='".$ally."' AND `ally_id` != '0'",'users');

    $names = '';
    while ($OUNames = mysql_fetch_array($OnlineUsersNames)) {
      $names .= $OUNames['username'];
      $names .= ", ";
    }
    $parse['MembersOnline2'] = $names;
*/

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

    $template->assign_vars(array(
      'dpath' => $dpath,
      'PLANET_ID'   => $planetrow['id'],
      'PLANET_NAME' => $planetrow['name'],
      'PLANET_TYPE' => $planetrow['planet_type'],
      'LastChat'    => CHT_messageParse($msg),
      'admin_email' => $config->game_adminEmail,
    ));
    display(parsetemplate($template, $parse), $lang['Overview']);
    break;
}
?>
