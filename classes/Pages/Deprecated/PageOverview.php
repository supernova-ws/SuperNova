<?php
/**
 * Created by Gorlum 25.01.2018 7:50
 */

namespace Pages\Deprecated;

use Fleet\DbFleetStatic;
use Planet\DBStaticPlanet;
use SN;
use HelperString;
use Exception;
use Planet\Planet;
use SnTemplate;
use template;

class PageOverview extends PageDeprecated {
  /**
   * @var \classConfig $config
   */
  protected $config;
  /**
   * @var \core_auth $auth
   */
  protected $auth;
  /**
   * @var \classLocale $lang
   */
  protected $lang;

  /**
   * @var \Core\RepoV2 $repo
   */
  protected $repo;

  /**
   * @var Planet $planet
   */
  protected $planet;

  public function __construct() {
    parent::__construct();

    $this->lang = SN::$lang;
    $this->config = SN::$config;
    $this->auth = SN::$auth;
    $this->repo = SN::$gc->repoV2;

    lng_include('overview');
    lng_include('mrc_mercenary');
  }


  public function setPlanetById($planetId) {
    /** @noinspection PhpUnhandledExceptionInspection */
    return $this->planet = $this->repo->getPlanet($planetId);
  }

  public function getPlanet() {
    if (empty($this->planet)) {
      throw new Exception('No planet in Overview');
    }

    return $this->planet;
  }

  public function route() {
    global $user, $planetrow, $que, $user_option_list;

    $this->setPlanetById($planetrow['id']);

    switch ($mode = sys_get_param_str('mode')) {
      case 'manage':
        $this->manage($user, $planetrow);
      break;

      default:
        $this->overview($user, $planetrow, $que, $user_option_list);
      break;
    }
  }

  /**
   * @param $user
   * @param &$planetrow
   * @param $que
   * @param $user_option_list
   */
  public function overview($user, &$planetrow, $que, $user_option_list) {
    $this->planet->sn_sys_sector_buy();

    rpg_level_up($user, RPG_STRUCTURE);
    rpg_level_up($user, RPG_RAID);
    rpg_level_up($user, RPG_TECH);
    rpg_level_up($user, RPG_EXPLORE);

    if (sys_get_param_str('rename') && $new_name = sys_get_param_str('new_name')) {
      $planetrow['name'] = $new_name;
      $new_name_safe = db_escape($new_name);
      DBStaticPlanet::db_planet_set_by_id($planetrow['id'], "`name` = '{$new_name_safe}'");
      $planetrow = DBStaticPlanet::db_planet_by_id($planetrow['id'], true, '*');
      $this->planet->reload();
    }

    if (!empty($theResult = $this->planet->sn_sys_planet_core_transmute($user))) {
      $this->resultMessageList->add($theResult['MESSAGE'], $theResult['STATUS']);
    }

    $template = SnTemplate::gettemplate('planet_overview', true);

    $user_dark_matter = mrc_get_level($user, false, RES_DARK_MATTER);
    $template->assign_recursive($this->planet->tpl_planet_density_info($user_dark_matter));

    $fleets_to_planet = [];
    $planet_count = 0;
    $planets_query = DBStaticPlanet::db_planet_list_sorted($user, false, '*');
    foreach ($planets_query as $an_id => $planetRecord) {
      $fleet_list = flt_get_fleets_to_planet($planetRecord);
      if (!empty($fleet_list['own']['count'])) {
        $fleets_to_planet[$an_id] = tpl_parse_fleet_sn($fleet_list['own']['total'], getUniqueFleetId($planetRecord));
      }

      if ($planetRecord['planet_type'] == PT_MOON) {
        continue;
      }

      $planet_count++;

      sn_db_transaction_start();
      $updatedData = sys_o_get_updated($user, $planetRecord['id'], SN_TIME_NOW, false, true);
      sn_db_transaction_commit();

      $templatizedPlanet = tpl_parse_planet($user, $updatedData['planet']);
      $templatizedPlanet += tpl_parse_planet_result_fleet($updatedData['planet'], $fleet_list);
      $templatizedPlanet += tpl_parse_planet_moon($planetRecord['id']);

      $template->assign_block_vars('planet', $templatizedPlanet);
    }

    $fleets = flt_parse_fleets_to_events(DbFleetStatic::fleet_and_missiles_list_incoming($user['id']));
    tpl_assign_fleet($template, $fleets_to_planet);
    tpl_assign_fleet($template, $fleets);

    $lune = $planetrow['planet_type'] == PT_PLANET ? DBStaticPlanet::db_planet_by_parent($planetrow['id']) : DBStaticPlanet::db_planet_by_id($planetrow['parent_planet']);
    if ($lune) {
      $template->assign_vars([
        'MOON_ID'   => $lune['id'],
        'MOON_IMG'  => $lune['image'],
        'MOON_NAME' => $lune['name'],
      ]);
    }

    $template->assign_recursive($this->planet->int_planet_pretemplate($user));

    if (!defined('GAME_STRUCTURES_DISABLED') || !GAME_STRUCTURES_DISABLED) {
      $this->templateQue($template, QUE_STRUCTURES, $que);
    }

    $this->templateQue($template, SUBQUE_FLEET, $que);
    if (!defined('GAME_DEFENSE_DISABLED') || !GAME_DEFENSE_DISABLED) {
      $this->templateQue($template, SUBQUE_DEFENSE, $que);
    }

    $template->assign_vars($this->templateGridSizes($user, $user_option_list, $planet_count));

    $template->assign_vars($this->templateSector($user, $user_dark_matter));

    $planet_recyclers_orbiting = 0;
    foreach (sn_get_groups('flt_recyclers') as $recycler_id) {
      $planet_recyclers_orbiting += mrc_get_level($user, $planetrow, $recycler_id);
    }
    $governor_level = $planetrow['PLANET_GOVERNOR_ID'] ? mrc_get_level($user, $planetrow, $planetrow['PLANET_GOVERNOR_ID'], false, true) : 0;
    $template->assign_vars([
      'USER_ID'        => $user['id'],
      'user_username'  => $user['username'],
      'USER_AUTHLEVEL' => $user['authlevel'],

      'NEW_MESSAGES' => $user['new_message'],
      // TODO
      // 'NEW_LEVEL_MINER' => $level_miner,
      // 'NEW_LEVEL_RAID'  => $level_raid,

      'planet_diameter' => HelperString::numberFloorAndFormat($planetrow['diameter']),

      'metal_debris'         => HelperString::numberFloorAndFormat($planetrow['debris_metal']),
      'crystal_debris'       => HelperString::numberFloorAndFormat($planetrow['debris_crystal']),
      'PLANET_RECYCLERS'     => $planet_recyclers_orbiting,
      'planet_image'         => $planetrow['image'],
      'planet_temp_min'      => $planetrow['temp_min'],
      'planet_temp_avg'      => round(($planetrow['temp_min'] + $planetrow['temp_max']) / 2),
      'planet_temp_max'      => $planetrow['temp_max'],
      'planet_density'       => $planetrow['density'],
      'planet_density_index' => $planetrow['density_index'],
      'planet_density_text'  => $this->lang['uni_planet_density_types'][$planetrow['density_index']],

      'GATE_LEVEL'          => mrc_get_level($user, $planetrow, STRUC_MOON_GATE),
      'GATE_JUMP_REST_TIME' => uni_get_time_to_jump($planetrow),

      'ADMIN_EMAIL' => $this->config->game_adminEmail,

      'PLANET_GOVERNOR_ID'         => $planetrow['PLANET_GOVERNOR_ID'],
      'PLANET_GOVERNOR_LEVEL'      => $governor_level,
      'PLANET_GOVERNOR_LEVEL_PLUS' => mrc_get_level($user, $planetrow, $planetrow['PLANET_GOVERNOR_ID']) - $governor_level,
      'PLANET_GOVERNOR_NAME'       => $this->lang['tech'][$planetrow['PLANET_GOVERNOR_ID']],

      'IS_CAPITAL' => $planetrow['id'] == $user['id_planet'],
      'IS_MOON'    => $planetrow['planet_type'] == PT_MOON,

      'DARK_MATTER' => $user_dark_matter,

      'PAGE_HEADER' => $this->lang['ov_overview'] . " - " . $this->lang['sys_planet_type'][$planetrow['planet_type']] . " {$planetrow['name']} [{$planetrow['galaxy']}:{$planetrow['system']}:{$planetrow['planet']}]",
    ]);
    tpl_set_resource_info($template, $planetrow, $fleets_to_planet);

    $this->resultMessageList->templateAdd($template);

    SnTemplate::display($template);
  }

  /**
   * @param $user
   * @param &$planetrow
   */
  public function manage($user, &$planetrow) {
    $this->planet->sn_sys_sector_buy('overview.php?mode=manage');

    $user_dark_matter = mrc_get_level($user, false, RES_DARK_MATTER);
    if (!empty($theResult = $this->planet->sn_sys_planet_core_transmute($user))) {
      $this->resultMessageList->add($theResult['MESSAGE'], $theResult['STATUS']);
    }

    $template = SnTemplate::gettemplate('planet_manage', true);
    $planet_id = sys_get_param_id('planet_id');

    if (sys_get_param_str('rename') && $new_name = sys_get_param_str('new_name')) {
      $planetrow['name'] = $new_name;
      DBStaticPlanet::db_planet_set_by_id($planetrow['id'], "`name` = '{$new_name}'");
    } elseif (sys_get_param_str('action') == 'make_capital') {
      try {
        sn_db_transaction_start();
        $user = db_user_by_id($user['id'], true, '*');
        $planetrow = DBStaticPlanet::db_planet_by_id($planetrow['id'], true, '*');

        if ($planetrow['planet_type'] != PT_PLANET) {
          throw new exception($this->lang['ov_capital_err_not_a_planet'], ERR_ERROR);
        }

        if ($planetrow['id'] == $user['id_planet']) {
          throw new exception($this->lang['ov_capital_err_capital_already'], ERR_ERROR);
        }

        if ($user_dark_matter < $this->config->planet_capital_cost) {
          throw new exception($this->lang['ov_capital_err_no_dark_matter'], ERR_ERROR);
        }

        rpg_points_change($user['id'], RPG_CAPITAL, -$this->config->planet_capital_cost,
          array('Planet %s ID %d at coordinates %s now become Empire Capital', $planetrow['name'], $planetrow['id'], uni_render_coordinates($planetrow))
        );

        db_user_set_by_id($user['id'], "id_planet = {$planetrow['id']}, galaxy = {$planetrow['galaxy']}, system = {$planetrow['system']}, planet = {$planetrow['planet']}");

        $user['id_planet'] = $planetrow['id'];
        $this->resultMessageList->add($this->lang['ov_capital_err_none'], ERR_NONE);
        sn_db_transaction_commit();
        sys_redirect('overview.php?mode=manage');
      } catch (exception $e) {
        sn_db_transaction_rollback();
        $this->resultMessageList->add($e->getMessage(), $e->getCode());
      }
    } elseif (sys_get_param_str('action') == 'planet_teleport') {
      try {
        if (!uni_coordinates_valid($new_coordinates = array(
          'galaxy' => sys_get_param_int('new_galaxy'),
          'system' => sys_get_param_int('new_system'),
          'planet' => sys_get_param_int('new_planet')))
        ) {
          throw new exception($this->lang['ov_teleport_err_wrong_coordinates'], ERR_ERROR);
        }

        sn_db_transaction_start();
        // При телепорте обновлять данные не надо - просто получить текущие данные и залочить их
        $user = db_user_by_id($user['id'], true, '*');
        $planetrow = DBStaticPlanet::db_planet_by_id($planetrow['id'], true, '*');

        $can_teleport = uni_planet_teleport_check($user, $planetrow, $new_coordinates);
        if ($can_teleport['result'] != ERR_NONE) {
          throw new exception($can_teleport['message'], $can_teleport['result']);
        }

        rpg_points_change($user['id'], RPG_TELEPORT, -$this->config->planet_teleport_cost,
          array($this->lang['ov_teleport_log_record'], $planetrow['name'], $planetrow['id'], uni_render_coordinates($planetrow), uni_render_coordinates($new_coordinates))
        );
        $planet_teleport_next = SN_TIME_NOW + $this->config->planet_teleport_timeout;
        DBStaticPlanet::db_planet_set_by_gspt($planetrow['galaxy'], $planetrow['system'], $planetrow['planet'], PT_ALL,
          "galaxy = {$new_coordinates['galaxy']}, system = {$new_coordinates['system']}, planet = {$new_coordinates['planet']}, planet_teleport_next = {$planet_teleport_next}");

        if ($planetrow['id'] == $user['id_planet']) {
          db_user_set_by_id($user['id'], "galaxy = {$new_coordinates['galaxy']}, system = {$new_coordinates['system']}, planet = {$new_coordinates['planet']}");
        }
        sn_db_transaction_commit();

        $user = db_user_by_id($user['id'], true, '*');
        $planetrow = DBStaticPlanet::db_planet_by_id($planetrow['id'], true, '*');
        $this->resultMessageList->add($this->lang['ov_teleport_err_none'], ERR_NONE);
        sys_redirect('overview.php?mode=manage');
      } catch (exception $e) {
        sn_db_transaction_rollback();
        $this->resultMessageList->add($e->getMessage(), $e->getCode());
      }
    } elseif (sys_get_param_str('action') == 'planet_abandon') {
      if ($this->auth->password_check(sys_get_param('abandon_confirm'))) {
        if ($user['id_planet'] != $user['current_planet'] && $user['current_planet'] == $planet_id) {
          $destroyed = SN_TIME_NOW + 60 * 60 * 24;
          DBStaticPlanet::db_planet_set_by_id($user['current_planet'], "`destruyed`='{$destroyed}', `id_owner`=0");
          DBStaticPlanet::db_planet_set_by_parent($user['current_planet'], "`destruyed`='{$destroyed}', `id_owner`=0");
          db_user_set_by_id($user['id'], '`current_planet` = `id_planet`');
          SnTemplate::messageBox($this->lang['ov_delete_ok'], $this->lang['colony_abandon'], 'overview.php?mode=manage');
        } else {
          SnTemplate::messageBox($this->lang['ov_delete_wrong_planet'], $this->lang['colony_abandon'], 'overview.php?mode=manage');
        }
      } else {
        SnTemplate::messageBox($this->lang['ov_delete_wrong_pass'], $this->lang['colony_abandon'], 'overview.php?mode=manage');
      }
    } elseif (($hire = sys_get_param_int('hire')) && in_array($hire, sn_get_groups('governors'))) {
      $this->planet->governorHire($hire);

      sys_redirect('overview.php?mode=manage');
      die();
    }

    // TODO - refresh planet by itself
//    $this->setPlanetById($planetrow['id']);
    $this->planet->dbLoadRecord($planetrow['id']);
    $template->assign_recursive($this->planet->int_planet_pretemplate($user));

    foreach (sn_get_groups('governors') as $governor_id) {
      if ($planetrow['planet_type'] == PT_MOON && $governor_id == MRC_TECHNOLOGIST) {
        continue;
      }

      $governor_level = $planetrow['PLANET_GOVERNOR_ID'] == $governor_id ? $planetrow['PLANET_GOVERNOR_LEVEL'] : 0;
      $build_data = eco_get_build_data($user, $planetrow, $governor_id, $governor_level);
      $template->assign_block_vars('governors', array(
        'ID'         => $governor_id,
        'NAME'       => $this->lang['tech'][$governor_id],
        'COST'       => $build_data[BUILD_CREATE][RES_DARK_MATTER],
        'MAX'        => get_unit_param($governor_id, P_MAX_STACK),
        'LEVEL'      => $governor_level,
        'LEVEL_PLUS' => mrc_get_level($user, $planetrow, $governor_id) - $governor_level,
      ));
    }

    $user_dark_matter = mrc_get_level($user, false, RES_DARK_MATTER);
    $template->assign_recursive($this->planet->tpl_planet_density_info($user_dark_matter));

    $template->assign_vars($this->templateSector($user, $user_dark_matter));

    $can_teleport = uni_planet_teleport_check($user, $planetrow);
    $template->assign_vars(array(
      'DARK_MATTER' => $user_dark_matter,

      'CAN_TELEPORT'         => $can_teleport['result'] == ERR_NONE,
      'CAN_NOT_TELEPORT_MSG' => $can_teleport['message'],
      'TELEPORT_COST_TEXT'   => prettyNumberStyledCompare($this->config->planet_teleport_cost, $user_dark_matter),

      'IS_CAPITAL'        => $planetrow['id'] == $user['id_planet'],
      'CAN_CAPITAL'       => $user_dark_matter >= $this->config->planet_capital_cost,
      'CAPITAL_COST_TEXT' => prettyNumberStyledCompare($this->config->planet_capital_cost, $user_dark_matter),

      'PAGE_HINT' => $this->lang['ov_manage_page_hint'],
    ));

    $this->resultMessageList->templateAdd($template);

    SnTemplate::display($template, $this->lang['rename_and_abandon_planet']);
  }

  /**
   * @param $user
   * @param $user_dark_matter
   *
   * @return array
   */
  protected function templateSector($user, $user_dark_matter) {
    $sector_cost = eco_get_build_data($user, $this->planet->asArray(), UNIT_SECTOR, mrc_get_level($user, $this->planet->asArray(), UNIT_SECTOR), true);
    $sector_cost = $sector_cost[BUILD_CREATE][RES_DARK_MATTER];
    $planet_fill = floor($this->planet->field_current / eco_planet_fields_max($this->planet->asArray()) * 100);
    $planet_fill = $planet_fill > 100 ? 100 : $planet_fill;
    $vararray = [
      'planet_field_current' => $this->planet->field_current,
      'planet_field_max'     => eco_planet_fields_max($this->planet->asArray()),
      'PLANET_FILL'          => floor($this->planet->field_current / eco_planet_fields_max($this->planet->asArray()) * 100),
      'PLANET_FILL_BAR'      => $planet_fill,
      'SECTOR_CAN_BUY'       => $sector_cost <= $user_dark_matter,
      'SECTOR_COST'          => $sector_cost,
      'SECTOR_COST_TEXT'     => HelperString::numberFloorAndFormat($sector_cost),
    ];

    return $vararray;
  }


  /**
   * @param template $template
   * @param int      $que_type
   * @param          $que
   */
  protected function templateQue($template, $que_type, $que) {
//    $que = que_get($planet->id_owner, $planet->id, $que_type);
    $que = $que['ques'][$que_type][$this->planet->id_owner][$this->planet->id];

    $que_length = 0;
    if (!empty($que)) {
      foreach ($que as $que_item) {
        $template->assign_block_vars('que', que_tpl_parse_element($que_item));
      }
      $que_length = count($que);
    }

    $template->assign_block_vars('ques', [
      'ID'     => $que_type,
      'NAME'   => $this->lang['sys_ques'][$que_type],
      'LENGTH' => $que_length,
    ]);
  }

  /**
   * Calculates planet grid sizes (horizontal and vertical) for Planet Overview
   *
   * @param $user
   * @param $user_option_list
   * @param $planet_count
   *
   * @return array
   */
  protected function templateGridSizes($user, $user_option_list, $planet_count) {
    $overview_planet_rows = $user['opt_int_overview_planet_rows'];
    $overview_planet_columns = $user['opt_int_overview_planet_columns'];

    if ($overview_planet_rows <= 0 && $overview_planet_columns <= 0) {
      $overview_planet_rows = $user_option_list[OPT_INTERFACE]['opt_int_overview_planet_rows'];
      $overview_planet_columns = $user_option_list[OPT_INTERFACE]['opt_int_overview_planet_columns'];
    }

    if ($overview_planet_rows > 0 && $overview_planet_columns <= 0) {
      $overview_planet_columns = ceil($planet_count / $overview_planet_rows);
    }
    $vararray = [
      'LIST_ROW_COUNT'    => $overview_planet_rows,
      'LIST_COLUMN_COUNT' => $overview_planet_columns,
    ];

    return $vararray;
  }

}
