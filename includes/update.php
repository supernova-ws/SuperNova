<?php
function sys_alterTable($table, $alters){
  global $config;

  if(!is_array($alters))
    $alters = array($alters);

  $qry = "ALTER TABLE {$config->db_prefix}{$table}";
  foreach($alters as $alteration)
    if($alteration)
      $qry .= ' ' . $alteration . ',';
  $qry = substr($qry, 0, -1) . ';';

  return mysql_query($qry);
}

include_once('init.inc');

if (INSTALL != true) {
  if ($InLogin != true) {
    $Result        = CheckTheUser ( $IsUserChecked );
    $IsUserChecked = $Result['state'];
    $user          = $Result['record'];

    if( $config->game_disable)
      if ($user['authlevel'] < 1)
        message ( stripslashes ( $config->game_disable_reason ), $config->game_name );
  }
}

// alliance_request

if ( $user['authlevel'] < 3 ) return;

print('Random number: ');
pr();

print('Loading table info... ');
$q = doquery('SHOW TABLES;');
while($r = mysql_fetch_row($q)){
  $tableName = str_replace($config->db_prefix, "", $r[0]);

  $q1 = doquery("SHOW COLUMNS FROM {$r[0]};");
  while($r1 = mysql_fetch_assoc($q1)){
    $tables[$tableName][$r1['Field']] = $r1;
  }

  $q1 = doquery("SHOW INDEX FROM {$r[0]};");
  while($r1 = mysql_fetch_assoc($q1)){
    $indexes[$tableName][$r1['Key_name']] .= $r1['Column_name'] . ',';
  }
}
print('done.<br>Now upgrading DB...');

$config->db_loadItem('db_version');
switch(intval($config->db_version)){
  case 0:
    if(!$tables['planets']['parent_planet'])
      mysql_query(
        "ALTER TABLE {$config->db_prefix}planets
          ADD `parent_planet` bigint(11) unsigned DEFAULT '0',
          ADD KEY `i_parent_planet` (`parent_planet`)
          ;");
    doquery("UPDATE `{{planets}}` AS lu
        LEFT JOIN `{{planets}}` AS pl ON pl.galaxy=lu.galaxy AND pl.system=lu.system AND pl.planet=lu.planet AND pl.planet_type=1
      SET lu.parent_planet=pl.id WHERE lu.planet_type=3;");
    $newVersion = 1;
    set_time_limit(30);

  case 1:
    if(!$tables['counter']){
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
    if($tables['lunas'])
      mysql_query("DROP TABLE {$config->db_prefix}lunas;");
    $newVersion = 3;
    set_time_limit(30);

  case 3:
    if(!$tables['counter']['url'])
      mysql_query("ALTER TABLE {$config->db_prefix}counter ADD `url` varchar(255) CHARACTER SET utf8 DEFAULT '';");
    $newVersion = 4;
    set_time_limit(30);

  case 4:
    if(!$tables['planets']['debris_metal'])
      mysql_query(
        "ALTER TABLE {$config->db_prefix}planets
           ADD `debris_metal` bigint(11) unsigned DEFAULT '0'
          ;");
    set_time_limit(30);

    if(!$tables['planets']['debris_crystal'])
      mysql_query(
        "ALTER TABLE {$config->db_prefix}planets
           ADD `debris_crystal` bigint(11) unsigned DEFAULT '0'
          ;");
    set_time_limit(30);

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
    if(!$indexes['fleets']['fleet_mess'])
      sys_alterTable('fleets', array(
        "ADD KEY `fleet_mess` (`fleet_mess`)",
        "ADD KEY `fleet_group` (`fleet_group`)"
      ));
    $newVersion = 8;
    set_time_limit(30);

  case 8:
    if(!$tables['referrals']['dark_matter'])
      sys_alterTable('referrals', "ADD `dark_matter` bigint(11) NOT NULL DEFAULT '0' COMMENT 'How much player have aquired Dark Matter'");

    if(!$indexes['referrals']['id_partner'])
      sys_alterTable('referrals', "ADD KEY `id_partner` (`id_partner`)");

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
};
print('done.<br>');

if($newVersion){
  $config->db_saveItem('db_version', $newVersion);
  print("DB version is now {$newVersion}");
}else
  print("DB version didn't changed from {$config->db_version}");

?>