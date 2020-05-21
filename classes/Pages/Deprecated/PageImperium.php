<?php
/**
 * Created by Gorlum 01.10.2017 17:53
 */

namespace Pages\Deprecated;

use Fleet\DbFleetStatic;
use SnTemplate;
use \template;
use \classLocale;
use Planet\DBStaticPlanet;
use \HelperString;

class PageImperium {

  const GROUPS_TO_NAMES = [
    UNIT_STRUCTURES         => 'structures',
    UNIT_STRUCTURES_SPECIAL => 'structures',
    UNIT_SHIPS              => 'fleet',
    UNIT_DEFENCE            => 'defense',
  ];

  /**
   * @var classLocale $lang ;
   */
  protected $lang;


  /**
   * @param template|null $template
   */
  public static function viewStatic(template $template = null) {
    $that = new static();

    $template = $that->view($template);
    unset($that);

    return $template;
  }

  /**
   * @param template|null $template
   */
  public static function modelStatic(template $template = null) {
    $that = new static();

    $that->modelAdjustMinePercent();
    unset($that);

    return $template;
  }

  public function __construct() {
    global $lang;
    $this->lang = $lang;
  }

  /**
   * @param template|null $template
   *
   * @return template
   */
  public function view(template $template = null) {
    global $user;

    list($planets, $ques) = $this->getUpdatedUserPlanetsAndQues($user);
    $fleets = $this->fleetGetFlyingToPlanets($planets);

    $template = SnTemplate::gettemplate('imperium', $template);

    $template->assign_recursive(templateFillPercent());

    $template->assign_recursive($this->tplRenderPlanets($user, $planets, $fleets));
    $template->assign_recursive($this->tplRenderFleets($planets, $fleets));
    $this->tplTotalPlanetInfo($template, $planets);

    foreach (self::GROUPS_TO_NAMES as $unit_group_id => $internalGroupName) {
      $template->assign_block_vars('prods', array(
        'NAME' => $this->lang['tech'][$unit_group_id],
      ));
      $this->imperiumTemplatizeUnitGroup($user, $template, $unit_group_id, $planets, $ques, $fleets);
    }

    $template->assign_var('amount', count($planets) + 2);
    $this->tplAddGlobals($user, $template);

    return $template;
  }

  /**
   * Store current mines load in DB
   */
  protected function modelAdjustMinePercent() {
    global $user;

    if (!sys_get_param('save_production') || !is_array($production = sys_get_param('percent')) || empty($production)) {
      return;
    }

    $sn_group_factories = sn_get_groups('factories');

    foreach (DBStaticPlanet::db_planet_list_sorted($user, false, '*') as $planetId => $planet) {
      $query = [];
      foreach ($sn_group_factories as $factory_unit_id) {
        $unit_db_name_porcent = pname_factory_production_field_name($factory_unit_id);
        if (
          // Changes required to mine production
          isset($production[$factory_unit_id][$planet['id']])
          // If mine is managed
          && get_unit_param($factory_unit_id, P_MINING_IS_MANAGED)
          // Input value is valid
          && ($actual_porcent = intval($production[$factory_unit_id][$planet['id']] / 10)) >= 0
          && $actual_porcent <= 10
          // And changes really should be stored to DB
          && $actual_porcent != $planet[$unit_db_name_porcent]
        ) {
          $actual_porcent = intval($actual_porcent);
          $query[] = "`{$unit_db_name_porcent}` = {$actual_porcent}";
        }
      }

      if (!empty($query)) {
        DBStaticPlanet::db_planet_set_by_id($planet['id'], implode(',', $query));
      }
    }
  }

  /**
   * @param array $user
   *
   * @return array[]
   */
  protected function getUpdatedUserPlanetsAndQues($user) {
    $planets = array();
    $ques = array();
    $planet_row_list = DBStaticPlanet::db_planet_list_sorted($user);
    foreach ($planet_row_list as $planet) {
      sn_db_transaction_start();
      $global_data = sys_o_get_updated($user, $planet['id'], SN_TIME_NOW, false, true);
      $planets[$planet['id']] = $global_data['planet'];
      $ques[$planet['id']] = $global_data['que'];
      sn_db_transaction_commit();
    }

    return array($planets, $ques);
  }

  /**
   * @param array    $user
   * @param template $template
   * @param int      $unit_group_id
   * @param array[]  $planets
   * @param array[]  $ques
   * @param array    $fleets
   */
  protected function imperiumTemplatizeUnitGroup(&$user, $template, $unit_group_id, $planets, $ques, $fleets) {
    $sn_group_factories = sn_get_groups('factories');

    foreach (get_unit_param('techtree', $unit_group_id) as $unit_id) {
      $unit_count = $unit_count_abs = 0;
      $block_vars = array();
      $unit_is_factory = in_array($unit_id, $sn_group_factories) && get_unit_param($unit_id, P_MINING_IS_MANAGED);
      foreach ($planets as $planet) {
        $unit_level_plain = mrc_get_level($user, $planet, $unit_id, false, true);

        $levelGreen = 0;
        $levelYellow = 0;

        switch ($unit_group_id) {
          /** @noinspection PhpMissingBreakStatementInspection */
          case UNIT_SHIPS:
            $levelYellow = !empty($fleets[$planet['id']]['own']['total'][$unit_id]) ? floatval($fleets[$planet['id']]['own']['total'][$unit_id]) : 0;

          case UNIT_STRUCTURES:
          case UNIT_STRUCTURES_SPECIAL:
          case UNIT_DEFENCE:
            $levelGreen = floatval($ques[$planet['id']]['in_que'][que_get_unit_que($unit_id)][$user['id']][$planet['id']][$unit_id]);
          break;

          default:
          break;
        }
        $unitsPresentOrChanged = $unit_level_plain + abs($levelYellow) + abs($levelGreen);

        $unit_count += $unit_level_plain;
        $unit_count_abs += $unitsPresentOrChanged;

        $block_vars[] = [
          'ID'                     => $planet['id'],
          'TYPE'                   => $planet['planet_type'],
          'LEVEL'                  => $unitsPresentOrChanged ? $unit_level_plain : '-',
          'LEVEL_PLUS_YELLOW'      => $levelYellow,
          'LEVEL_PLUS_GREEN'       => $levelGreen,
          'LEVEL_TEXT'             => $unitsPresentOrChanged ? HelperString::numberFloorAndFormat($unit_level_plain) : '-',
          'LEVEL_PLUS_YELLOW_TEXT' => SnTemplate::tplPrettyPlus($levelYellow),
          'LEVEL_PLUS_GREEN_TEXT'  => SnTemplate::tplPrettyPlus($levelGreen),
          'PERCENT'                => $unit_is_factory ? ($unit_level_plain ? $planet[pname_factory_production_field_name($unit_id)] * 10 : -1) : -1,
          'FACTORY'                => $unit_is_factory,
        ];
      }

      if ($unit_count_abs) {
        $this->tplRenderUnitLine($template, $unit_id, $block_vars, $unit_count, $unit_is_factory);
      }
    }
  }

  /**
   * Renders line of unit for each planet
   *
   * @param template $template
   * @param int      $unit_id
   * @param array    $block_vars
   * @param int      $unit_count
   * @param bool     $unit_is_factory
   *
   */
  protected function tplRenderUnitLine($template, $unit_id, $block_vars, $unit_count, $unit_is_factory) {
    // Adding unit cell name
    $template->assign_block_vars('prods', [
      'ID'    => $unit_id,
      'FIELD' => 'unit_' . $unit_id, // TODO Делать это прямо в темплейте
      'NAME'  => $this->lang['tech'][$unit_id],
      'MODE'  => static::GROUPS_TO_NAMES[get_unit_param($unit_id, P_UNIT_TYPE)],
    ]);

    $imperiumYellows = [];
    $imperiumGreens = [];
    // Adding data for each planet for this unit
    foreach ($block_vars as $block_var) {
      $imperiumYellows[$unit_id] += $block_var['LEVEL_PLUS_YELLOW'];
      $imperiumGreens[$unit_id] += $block_var['LEVEL_PLUS_GREEN'];
      $template->assign_block_vars('prods.planet', $block_var);
    }

    // Adding final cell with Imperium total stat about this unit
    $template->assign_block_vars('prods.planet', [
      'ID'                     => 0,
      'LEVEL'                  => $unit_count,
      'LEVEL_TEXT'             => HelperString::numberFloorAndFormat($unit_count),
      'LEVEL_PLUS_YELLOW'      => $imperiumYellows[$unit_id],
      'LEVEL_PLUS_GREEN'       => $imperiumGreens[$unit_id],
      'LEVEL_PLUS_YELLOW_TEXT' => $imperiumYellows[$unit_id] == 0 ? '' : SnTemplate::tplPrettyPlus($imperiumYellows[$unit_id]),
      'LEVEL_PLUS_GREEN_TEXT'  => $imperiumGreens[$unit_id] == 0 ? '' : SnTemplate::tplPrettyPlus($imperiumGreens[$unit_id]),
      'PERCENT'                => $unit_is_factory ? '' : -1,
      'FACTORY'                => $unit_is_factory,
    ]);
  }

  /**
   * @param array[] $planets
   * @param array   $fleets
   *
   * @return array[][]
   */
  protected function tplRenderPlanets($user, &$planets, $fleets) {
    $result = [];

    $planet_density = sn_get_groups('planet_density');

    foreach ($planets as $planetId => $planet) {
      $templatizedPlanet = tpl_parse_planet($user, $planet);

      if($planet['planet_type'] == PT_MOON) {
        $parentPlanet = DBStaticPlanet::db_planet_by_id($planet['parent_planet']);
      } else {
        $parentPlanet = $planet;
      }

      $fleet_list = $fleets[$planetId];
      foreach ([RES_METAL, RES_CRYSTAL, RES_DEUTERIUM] as $resourceId) {
        if (empty($fleet_list['own']['total'][$resourceId])) {
          $templatizedPlanet['RES_' . $resourceId] = 0;
        } else {
          $templatizedPlanet['RES_' . $resourceId] = $fleet_list['own']['total'][$resourceId];
          $templatizedPlanet['RES_' . $resourceId . '_TEXT'] = HelperString::numberFloorAndFormat($fleet_list['own']['total'][$resourceId]);
        }
      }
      $templatizedPlanet += tpl_parse_planet_result_fleet($planet, $fleet_list);

      $templatizedPlanet += [
        'METAL_CUR'  => prettyNumberStyledCompare($planet['metal'], $planet['metal_max']),
        'METAL_PROD_TEXT' => HelperString::numberFloorAndFormat($planet['metal_perhour']),

        'CRYSTAL_CUR'  => prettyNumberStyledCompare($planet['crystal'], $planet['crystal_max']),
        'CRYSTAL_PROD_TEXT' => HelperString::numberFloorAndFormat($planet['crystal_perhour']),

        'DEUTERIUM_CUR'  => prettyNumberStyledCompare($planet['deuterium'], $planet['deuterium_max']),
        'DEUTERIUM_PROD_TEXT' => HelperString::numberFloorAndFormat($planet['deuterium_perhour']),

        'ENERGY_CUR' => $planet['energy_max'] - $planet['energy_used'],
        'ENERGY_MAX' => $planet['energy_max'],

        'TEMP_MIN' => $planet['temp_min'],
        'TEMP_MAX' => $planet['temp_max'],

        'DENSITY_CLASS'      => $planet['density_index'],
        'DENSITY_RICHNESS'   => $planet_density[$planet['density_index']][UNIT_PLANET_DENSITY_RICHNESS],
        'DENSITY_CLASS_TEXT' => $this->lang['uni_planet_density_types'][$planet['density_index']],

        '_PARENT_PLANET' => &$parentPlanet,
      ];

      $templatizedPlanet['IS_CAPITAL'] = $parentPlanet['id'] == $user['id_planet'];

      $result[] = $templatizedPlanet;
    }

    return [
      '.' => [
        'planet' => $result
      ]
    ];
  }

  /**
   * @param array[] $planets
   * @param array[] $fleets
   *
   * @return array
   */
  protected function tplRenderFleets($planets, $fleets) {
    $fleetsRendered = [];
    foreach ($fleets as $planetId => $fleet_list) {
      if (!empty($fleet_list['own']['count'])) {
        $fleetsRendered[$planets[$planetId]['id']] = tpl_parse_fleet_sn($fleet_list['own']['total'], getUniqueFleetId(['id' => $planetId]));
      }
    }

    return tpl_assign_fleet_generate($fleetsRendered);
  }

  /**
   * @param array[] $planets
   *
   * @return array
   */
  protected function fleetGetFlyingToPlanets(&$planets) {
    $fleets = [];
    foreach ($planets as $planetId => &$planet) {
      $fleets[$planet['id']] = flt_get_fleets_to_planet($planet);
    }

    return $fleets;
  }

  /**
   * @param template $template
   * @param array    $planets
   */
  function tplTotalPlanetInfo($template, $planets) {
    $imperiumStats = [
      'temp_min' => 1000,
      'temp_max' => -999,
    ];

    foreach ($planets as $planetId => &$planet) {
      $imperiumStats['fields'] += $planet['field_current'];
      $imperiumStats['metal'] += $planet['metal'];
      $imperiumStats['crystal'] += $planet['crystal'];
      $imperiumStats['deuterium'] += $planet['deuterium'];
      $imperiumStats['energy'] += $planet['energy_max'] - $planet['energy_used'];

      $imperiumStats['fields_max'] += eco_planet_fields_max($planet);
      $imperiumStats['metal_perhour'] += $planet['metal_perhour'];
      $imperiumStats['crystal_perhour'] += $planet['crystal_perhour'];
      $imperiumStats['deuterium_perhour'] += $planet['deuterium_perhour'];
      $imperiumStats['energy_max'] += $planet['energy_max'];

      $imperiumStats['temp_min'] = min($planet['temp_min'], $imperiumStats['temp_min']);
      $imperiumStats['temp_max'] = max($planet['temp_max'], $imperiumStats['temp_max']);
    }

    $template->assign_block_vars('planet', array_merge([
      'ID'   => 0,
      'NAME' => $this->lang['sys_total'],

      'FIELDS_CUR' => $imperiumStats['fields'],
      'FIELDS_MAX' => $imperiumStats['fields_max'],

      'METAL_CUR'  => HelperString::numberFloorAndFormat($imperiumStats['metal']),
      'METAL_PROD_TEXT' => HelperString::numberFloorAndFormat($imperiumStats['metal_perhour']),

      'CRYSTAL_CUR'  => HelperString::numberFloorAndFormat($imperiumStats['crystal']),
      'CRYSTAL_PROD_TEXT' => HelperString::numberFloorAndFormat($imperiumStats['crystal_perhour']),

      'DEUTERIUM_CUR'  => HelperString::numberFloorAndFormat($imperiumStats['deuterium']),
      'DEUTERIUM_PROD_TEXT' => HelperString::numberFloorAndFormat($imperiumStats['deuterium_perhour']),

      'ENERGY_CUR' => $imperiumStats['energy'],
      'ENERGY_MAX' => $imperiumStats['energy_max'],

      'TEMP_MIN' => $imperiumStats['temp_min'],
      'TEMP_MAX' => $imperiumStats['temp_max'],
    ]));
  }

  /**
   * @param array    $user
   * @param template $template
   */
  protected function tplAddGlobals(&$user, $template) {
    $template->assign_vars([
      'COLONIES_CURRENT' => get_player_current_colonies($user),
      'COLONIES_MAX'     => get_player_max_colonies($user),

      'EXPEDITIONS_CURRENT' => DbFleetStatic::fleet_count_flying($user['id'], MT_EXPLORE),
      'EXPEDITIONS_MAX'     => get_player_max_expeditons($user),

      'PLANET_DENSITY_RICHNESS_NORMAL'  => PLANET_DENSITY_RICHNESS_NORMAL,
      'PLANET_DENSITY_RICHNESS_AVERAGE' => PLANET_DENSITY_RICHNESS_AVERAGE,
      'PLANET_DENSITY_RICHNESS_GOOD'    => PLANET_DENSITY_RICHNESS_GOOD,
      'PLANET_DENSITY_RICHNESS_PERFECT' => PLANET_DENSITY_RICHNESS_PERFECT,

      'PAGE_HEADER' => $this->lang['imp_overview'],
    ]);
  }

}
