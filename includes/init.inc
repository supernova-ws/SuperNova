<?php
//print('бмхлюмхе! хцпю нярюмнбкемю мю опнтхкюйрхйс!');
//die();

set_magic_quotes_runtime(0);
ini_set('display_errors', 0);
ini_set('error_reporting', E_ALL ^ E_NOTICE);

define('VERSION','v. 0.26');                // Passera en version 1.0 quand toutes les fonctions ET l'install seront correct

define('INSIDE'  , true);
define('INSTALL' , false);

$user          = array();
$lang          = array();
$IsUserChecked = false;

// define('DEFAULT_SKINPATH' , 'skins/xnova/');
define('DEFAULT_SKINPATH' , 'skins/EpicBlue/');
define('TEMPLATE_DIR'     , 'templates/');
define('TEMPLATE_NAME'    , 'OpenGame');
define('DEFAULT_LANG'     , 'ru');

$HTTP_ACCEPT_LANGUAGE = DEFAULT_LANG;

$time_now = time();

$ugamela_root_path = str_replace('//', '/', $_SERVER['DOCUMENT_ROOT']) . '/';
include_once($ugamela_root_path . 'includes/extensions.inc');

include($ugamela_root_path . 'includes/debug.class.'.$phpEx);
$debug = new debug();

include_once($ugamela_root_path . 'includes/constants.'.$phpEx);
include_once($ugamela_root_path . 'includes/functions.'.$phpEx);
include_once($ugamela_root_path . 'includes/unlocalised.'.$phpEx);
include_once($ugamela_root_path . 'includes/todofleetcontrol.'.$phpEx);
include_once($ugamela_root_path . 'language/'. DEFAULT_LANG .'/lang_info.cfg');

include_once($ugamela_root_path . 'includes/vars.'.$phpEx);
include_once($ugamela_root_path . 'includes/db.'.$phpEx);
include_once($ugamela_root_path . 'includes/strings.'.$phpEx);

includeLang ("system");
includeLang ('tech');

// Initializing global "config" object
include_once($ugamela_root_path . 'config.'.$phpEx);
$config = classConfig::getInstance($dbsettings['prefix']);
$config->db_prefix = $dbsettings['prefix'];
unset($dbsettings);

$game_config   = $game_config_default;

// Lecture de la table de configuration
$query = doquery("SELECT * FROM {{table}}",'config');
while ( $row = mysql_fetch_assoc($query) ) {
  $game_config[$row['config_name']] = $row['config_value'];
}
?>