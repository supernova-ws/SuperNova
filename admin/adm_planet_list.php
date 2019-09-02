<?php

define('INSIDE'  , true);
define('INSTALL' , false);
define('IN_ADMIN', true);

require('../common.' . substr(strrchr(__FILE__, '.'), 1));

global $config, $lang, $user;

SnTemplate::messageBoxAdminAccessDenied(AUTH_LEVEL_ADMINISTRATOR);

$planet_active = sys_get_param_int('planet_active');
if(!$planet_active) {
  $planet_type = sys_get_param_int('planet_type', 1);
  $planet_type = $planet_type == 3 ? 3 : 1;
} else {
  $active_time = SN_TIME_NOW - SN::$config->game_users_online_timeout;
}
$table_parent_columns = $planet_type == 3 || $planet_active;

$template = SnTemplate::gettemplate('admin/adm_planet_list', true);

$query = db_planet_list_admin_list($table_parent_columns, $planet_active, $active_time, $planet_type);
while ($planet_row = db_fetch($query)) {
  $template->assign_block_vars('planet', array(
    'ID'          => $planet_row['id'],
    'NAME'        => js_safe_string($planet_row['name']),
    'GALAXY'      => $planet_row['galaxy'],
    'SYSTEM'      => $planet_row['system'],
    'PLANET'      => $planet_row['planet'],
    'PLANET_TYPE' => $planet_row['planet_type'],
    'PLANET_TYPE_PRINT' => $lang['sys_planet_type_sh'][$planet_row['planet_type']],
    'PARENT_ID'   => js_safe_string($planet_row['parent_planet']),
    'PARENT_NAME' => js_safe_string($planet_row['parent_name']),
    'OWNER'       => js_safe_string($planet_row['username']),
    'OWNER_ID'    => $planet_row['id_owner'], 
  ));
}

$page_title = 
  $lang['adm_planet_list_title'] . ': ' . 
  ($planet_active ? $lang['adm_planet_active'] :
    ($planet_type ? ($planet_type == 3 ? $lang['sys_moons'] : $lang['sys_planets']) : '')
  );
$template->assign_vars(array(
  'PAGE_TITLE' => $page_title,

  'PLANET_COUNT'  => db_num_rows($query),
  'PARENT_COLUMN' => $table_parent_columns,
));

SnTemplate::display($template, $page_title);
