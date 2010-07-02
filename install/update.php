<?php
include_once('../includes/init.inc');

if (INSTALL != true) {
  if ($InLogin != true) {
    $Result        = CheckTheUser ( $IsUserChecked );
    $IsUserChecked = $Result['state'];
    $user          = $Result['record'];
  } elseif ($InLogin == false) {
    if( $config->game_disable)
      if ($user['authlevel'] < 1)
        message ( stripslashes ( $game_config['close_reason'] ), $game_config['game_name'] );
  }
}

// alliance_request

if ( $user['authlevel'] >= 3 ) {
  pr();
  // pdump($cache->tables);

  $q = doquery('SHOW TABLES;');
  while($r = mysql_fetch_row($q)){
    $q1 = doquery("SHOW COLUMNS FROM {$r[0]};");
    $tableName = str_replace($config->db_prefix, "", $r[0]);
    while($r1 = mysql_fetch_assoc($q1)){
      $tables[$tableName][$r1['Field']] = $r1;
    }
  }

  $config->db_loadItem('db_version');
  switch($config->db_version){
    case '':
      doquery("UPDATE `{{planets}}` AS lu
          LEFT JOIN `{{planets}}` AS pl ON pl.galaxy=lu.galaxy AND pl.system=lu.system AND pl.planet=lu.planet AND pl.planet_type=1
        SET lu.parent_planet=pl.id WHERE lu.planet_type=3;");
      $newVersion = 1;
      set_time_limit(30);
    case 1:
      if(!$tables['counter']){
        doquery(
"CREATE TABLE `{{counter}}` (
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
    case 2:
      if($tables['lunas']){
        mysql_query("DROP TABLE {$config->db_prefix}lunas;");
      };
      $newVersion = 3;
    case 4:
  };
  if($newVersion){
    $config->db_version = $newVersion;
    $config->db_saveItem('db_version');
    print("db_version is now {$newVersion}");
  }else
    print("db_version didn't changed from {$config->db_version}");
}

?>