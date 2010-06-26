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
  $game_version = doquery("SELECT `config_value` FROM {{config}} WHERE `config_name` = 'dbVersion';", '', true);
  switch($game_version['config_value']){
    case '':
      doquery("
        UPDATE `{{planets}}` AS lu
          LEFT JOIN `{{planets}}` AS pl
            ON pl.galaxy=lu.galaxy AND pl.system=lu.system AND pl.planet=lu.planet AND pl.planet_type=1
        SET lu.parent_planet=pl.id
        WHERE lu.planet_type=3;
      ", '', true);
      $newVersion = 1;
      set_time_limit(30);
    case 1:
  };
  if($newVersion){
    doquery("UPDATE {{config}} SET `config_value` = '{$newVersion}' WHERE `config_name` = 'dbVersion';", '', true);
    if(!mysql_affected_rows())
      doquery("INSERT INTO {{config}} SET `config_value` = '{$newVersion}', `config_name` = 'dbVersion';", '', true);
  };
}

?>