<?php

use Planet\DBStaticPlanet;
use Player\RecordPlayer;
use Unit\DBStaticUnit;

define('INSIDE', true);
define('INSTALL', false);
define('IN_ADMIN', true);

require('../common.' . substr(strrchr(__FILE__, '.'), 1));

global $lang, $user;

SnTemplate::messageBoxAdminAccessDenied(AUTH_LEVEL_ADMINISTRATOR);

//messageBoxAdmin('Временно не работает');

require("includes/admin_planet_edit.inc" . DOT_PHP_EX);

$template = SnTemplate::gettemplate('admin/admin_planet_edit', true);

$mode      = admin_planet_edit_mode($template, $admin_planet_edit_mode_list);
$planet_id = sys_get_param_id('planet_id');

$unit_list = sys_get_param('unit_list');
if (sys_get_param('change_data') && !empty($unit_list)) {
  planet_edit_model($planet_id, $unit_list, $mode);
}

if ($planet_id) {
  $edit_planet_row = DBStaticPlanet::db_planet_by_id($planet_id);
  admin_planet_edit_template($template, $edit_planet_row, $mode);
}

foreach ($admin_planet_edit_mode_list as $page_mode => $mode_locale) {
  $template->assign_block_vars('page_menu', array(
    'ID'   => $page_mode,
    'TEXT' => $mode_locale,
  ));
}

$template->assign_vars(array(
  'MODE'        => $mode,
  'PLANET_ID'   => $planet_id,
  'PLANET_NAME' => empty($edit_planet_row) ? '' : $lang['sys_planet_type'][$edit_planet_row['planet_type']] . ' ' . uni_render_planet($edit_planet_row),
  'PAGE_HINT'   => $lang['adm_planet_edit_hint'],
));

SnTemplate::display($template, $lang['adm_am_ttle']);


/**
 * @param       $planet_id
 * @param array $unit_list
 * @param       $mode
 */
function planet_edit_model($planet_id, array $unit_list, $mode) {
  $thePlanet = DBStaticPlanet::db_planet_by_id($planet_id);
  $theUserId = $thePlanet['id_owner'];
  $thePlayer = RecordPlayer::findRecordById($theUserId);

  $query_string = [];
  foreach ($unit_list as $unit_id => $unit_amount) {
    if ($mode === 'resources_loot') {
      if (!floatval($unit_amount)) {
        continue;
      }

      if ($unit_query_string = admin_planet_edit_query_string($unit_id, $unit_amount, $mode)) {
        $query_string[] = $unit_query_string;
      }
    } elseif (in_array($mode, [UNIT_SHIPS_STR, UNIT_STRUCTURES_STR, UNIT_DEFENCE_STR,]) ) {
      if (!floatval($unit_amount)) {
        continue;
      }

      $currentAmount = mrc_get_level($thePlayer, $thePlanet, $unit_id);

      $newAmount = $currentAmount + $unit_amount;

      if ($newAmount <= 0) {
        DBStaticUnit::db_unit_list_delete($theUserId, LOC_PLANET, $planet_id, $unit_id);
      } else {
        DBStaticUnit::dbChangeUnit($theUserId, $planet_id, $unit_id, $unit_amount);

        _SnCacheInternal::cache_clear(LOC_UNIT, true);
      }
    }

  }

  if (!empty($query_string)) {
    DBStaticPlanet::db_planet_set_by_id($planet_id, implode(', ', $query_string));
  }

}
