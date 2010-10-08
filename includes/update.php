<?php
if(!defined('INIT'))
{
  include_once('init.inc');
}

function sys_alter_table($table, $alters)
{
  global $config;

  if(!is_array($alters))
  {
    $alters = array($alters);
  }

  $qry = "ALTER TABLE {$config->db_prefix}{$table}";
  foreach($alters as $alteration)
  {
    if($alteration)
    {
      $qry .= " {$alteration},";
    }
  }
  $qry = substr($qry, 0, -1) . ';';

  return mysql_query($qry);
}

$msg = 'Loading table info... ';
$query = doquery('SHOW TABLES;');
while($row = mysql_fetch_row($query))
{
  $tableName = str_replace($config->db_prefix, '', $row[0]);

  $q1 = doquery("SHOW COLUMNS FROM {$row[0]};");
  while($r1 = mysql_fetch_assoc($q1))
  {
    $update_tables[$tableName][$r1['Field']] = $r1;
  }

  $q1 = doquery("SHOW INDEX FROM {$row[0]};");
  while($r1 = mysql_fetch_assoc($q1))
  {
    $update_indexes[$tableName][$r1['Key_name']] .= "{$r1['Column_name']},";
  }
}
$msg .= "done.\r\nNow upgrading DB...";

$config->db_loadItem('db_version');
switch(intval($config->db_version))
{
  case 0:
    if(!$update_tables['planets']['parent_planet'])
      mysql_query(
        "ALTER TABLE {$config->db_prefix}planets
          ADD `parent_planet` bigint(11) unsigned DEFAULT '0',
          ADD KEY `i_parent_planet` (`parent_planet`)
          ;");
    doquery(
      "UPDATE `{{planets}}` AS lu
        LEFT JOIN `{{planets}}` AS pl ON pl.galaxy=lu.galaxy AND pl.system=lu.system AND pl.planet=lu.planet AND pl.planet_type=1
      SET lu.parent_planet=pl.id WHERE lu.planet_type=3;");
    $newVersion = 1;
    set_time_limit(30);

  case 1:
    if(!$update_tables['counter'])
    {
      mysql_query(
        "CREATE TABLE `{$config->db_prefix}counter` (
          `id` bigint(11) NOT NULL AUTO_INCREMENT,
          `time` int(11) NOT NULL DEFAULT '0',
          `page` varchar(255) CHARACTER SET utf8 DEFAULT '0',
          `user_id` bigint(11) DEFAULT '0',
          `ip` varchar(15) DEFAULT NULL,
          PRIMARY KEY (`id`),
          KEY `i_user_id` (`user_id`),
          KEY `i_ip` (`ip`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
    };
    $newVersion = 2;
    set_time_limit(30);

  case 2:
    if($update_tables['lunas'])
      mysql_query("DROP TABLE IF EXISTS {$config->db_prefix}lunas;");
    $newVersion = 3;
    set_time_limit(30);

  case 3:
    if(!$update_tables['counter']['url'])
      mysql_query("ALTER TABLE {$config->db_prefix}counter ADD `url` varchar(255) CHARACTER SET utf8 DEFAULT '';");
    $newVersion = 4;
    set_time_limit(30);

  case 4:
    if(!$update_tables['planets']['debris_metal'])
      mysql_query(
        "ALTER TABLE {$config->db_prefix}planets
           ADD `debris_metal` bigint(11) unsigned DEFAULT '0'
          ;");
    set_time_limit(30);

    if(!$update_tables['planets']['debris_crystal'])
      mysql_query(
        "ALTER TABLE {$config->db_prefix}planets
           ADD `debris_crystal` bigint(11) unsigned DEFAULT '0'
          ;");
    set_time_limit(30);

    if($update_tables['galaxy'])
      doquery('UPDATE `{{planets}}`
        LEFT JOIN `{{galaxy}}` ON {{galaxy}}.id_planet = {{planets}}.id
      SET
        {{planets}}.debris_metal = {{galaxy}}.metal,
        {{planets}}.debris_crystal = {{galaxy}}.crystal
      WHERE {{galaxy}}.metal>0 OR {{galaxy}}.crystal>0;');
    $newVersion = 5;
    set_time_limit(30);

  case 5:
    mysql_query("DROP TABLE IF EXISTS `{$config->db_prefix}galaxy`;");
    $newVersion = 6;
    set_time_limit(30);

  case 6:
    doquery("DELETE FROM {{config}} WHERE `config_name` in ('BannerURL', 'banner_source_post', 'BannerOverviewFrame',
      'close_reason', 'dbVersion', 'ForumUserBarFrame', 'OverviewBanner', 'OverviewClickBanner', 'OverviewExternChat',
      'OverviewExternChatCmd', 'OverviewNewsText', 'UserbarURL', 'userbar_source');");
    $newVersion = 7;
    set_time_limit(30);

  case 7:
    if(!$update_indexes['fleets']['fleet_mess'])
      sys_alter_table('fleets', array(
        "ADD KEY `fleet_mess` (`fleet_mess`)",
        "ADD KEY `fleet_group` (`fleet_group`)"
      ));
    $newVersion = 8;
    set_time_limit(30);

  case 8:
    if(!$update_tables['referrals']['dark_matter'])
      sys_alter_table('referrals', "ADD `dark_matter` bigint(11) NOT NULL DEFAULT '0' COMMENT 'How much player have aquired Dark Matter'");

    if(!$update_indexes['referrals']['id_partner'])
      sys_alter_table('referrals', "ADD KEY `id_partner` (`id_partner`)");

    if(!$config->db_loadItem('rpg_bonus_divisor'))
      $config->db_saveItem('rpg_bonus_divisor', 10);

    if(!$config->db_loadItem('rpg_officer'))
      $config->db_saveItem('rpg_officer', 3);

    $newVersion = 9;
    set_time_limit(30);

  case 9:
    doquery("UPDATE {{referrals}} AS r
      LEFT JOIN {{users}} AS u ON u.id = r.id
      SET r.dark_matter = u.lvl_minier + u.lvl_raid;");

    doquery("UPDATE {{users}} AS u
      RIGHT JOIN {{referrals}} AS r ON r.id_partner = u.id AND r.dark_matter >= {$config->rpg_bonus_divisor}
      SET u.rpg_points = u.rpg_points + FLOOR(r.dark_matter/{$config->rpg_bonus_divisor});");

    $newVersion = 10;
    set_time_limit(30);

  case 10:
    if(!$config->db_loadItem('game_news_overview'))
      $config->db_saveItem('game_news_overview', 3);

    if(!$config->db_loadItem('game_news_actual'))
      $config->db_saveItem('game_news_actual', 259200);

    $newVersion = 11;
    set_time_limit(30);

  case 11:
    if($update_tables['users']['ataker'])
      mysql_query(
        "ALTER TABLE {$config->db_prefix}users
          DROP COLUMN `aktywnosc`,
          DROP COLUMN `time_aktyw`,
          DROP COLUMN `kiler`,
          DROP COLUMN `kod_aktywujacy`,
          DROP COLUMN `ataker`,
          DROP COLUMN `atakin`
          ;");

    doquery("DELETE FROM {{config}} WHERE `config_name` in ('OverviewNewsFrame');");
    $newVersion = 12;
    set_time_limit(30);

  case 12:
    if(!$update_tables['planets']['supercargo'])
      sys_alter_table('planets', "ADD `supercargo` bigint(11) NOT NULL DEFAULT '0' COMMENT 'Supercargo ship count'");

    if(!$update_tables['alliance_requests'])
    {
      mysql_query("
        CREATE TABLE `{$config->db_prefix}alliance_requests` (
          `id_user` int(11) NOT NULL,
          `id_ally` int(11) NOT NULL DEFAULT '0',
          `request_text` text,
          `request_time` int(11) NOT NULL DEFAULT '0',
          `request_denied` tinyint(1) unsigned NOT NULL DEFAULT '0',
          PRIMARY KEY (`id_user`,`id_ally`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
        ");
    };

    $newVersion = 13;
    set_time_limit(30);

  case 13:
    mysql_query("DROP TABLE IF EXISTS `{$config->db_prefix}update`;");
    $newVersion = 14;
    set_time_limit(30);

  case 14:
    if(!$config->db_loadItem('rules_url'))
      $config->db_saveItem('rules_url', 'http://forum.supernova.ws/viewtopic.php?f=3&t=974');
    $newVersion = 15;
    set_time_limit(30);

  case 15:
    if($update_tables['users']['current_luna'])
    {
      sys_alter_table('users', "DROP COLUMN `current_luna`");
    }
    if(!$update_tables['planets']['governor'])
    {
      sys_alter_table('planets', "ADD `governor` smallint unsigned NOT NULL DEFAULT '0' COMMENT 'Planet governor'");
    }
    if(!$update_tables['users']['options'])
    {
      sys_alter_table('users', "ADD `options` TEXT COMMENT 'Packed user options'");
    }
    $newVersion = 16;
    set_time_limit(30);

  case 16:
    if(!$config->db_loadItem('player_max_planets'))
    {
      $config->db_saveItem('player_max_planets', 10);
    }
    if(!$update_tables['users']['news_lastread'])
    {
      sys_alter_table('users', "ADD `news_lastread` int(11) NOT NULL DEFAULT '0' COMMENT 'News last read tag'");
    }
    $newVersion = 16;
    set_time_limit(30);

  case 17:
    set_time_limit(30);
};
$msg .= "done.\r\n";

if($newVersion)
{
  $config->db_saveItem('db_version', $newVersion);
  $msg .= "DB version is now {$newVersion}";
}
else
{
  $msg .= "DB version didn't changed from {$config->db_version}";
}

$debug->warning($msg, 'Database Update', 103);

if ($InLogin != true) {
  $user          = CheckTheUser();

  if( $config->game_disable)
    if ($user['authlevel'] < 1)
      message ( stripslashes ( $config->game_disable_reason ), $config->game_name );
}

if ( $user['authlevel'] >= 3 )
{
  print(sys_bbcodeParse($msg));
}
?>