<?php
/**
 * Created by Gorlum 29.10.2016 10:16
 */


/**
 *
 * This class is used to handle server configuration
 *
 * @package supernova
 *
 * @property string     $db_prefix                     - REMOVE! Just for compatibility!
 *
 * @property int        $debug
 *
 * @property string     $db_version
 *
 * @property string     $ali_bonus_members             => 10, // Minimum alliance size to start using bonus
 *
 * @property string     $auth_vkontakte_app_id
 * @property string     $auth_vkontakte_app_key
 * @property string     $auth_vkontakte_token
 * @property int        $auth_vkontakte_token_expire
 *
 * @property int        $BuildLabWhileRun              => 0, // Allow to build lab/Nanolab while tech researching AND allowing research tech while lab/Nanolab
 *
 * @property string     $COOKIE_NAME                   => 'SuperNova'
 *
 * @property int        $empire_mercenary_base_period  => PERIOD_MONTH, // Base hire period for price calculations
 * @property int        $empire_mercenary_temporary    => 0, // Temporary empire-wide mercenaries
 *
 * @property int        $fleet_update_max_run_time     => 30,         // Maximum length in seconds for single fleet dispatch run. Should be 1 second or more
 * @property int        $fleet_update_interval         => 4 second    // how often fleets should be updated
 * @property int        $fleet_update_last             => SN_TIME_NOW // unixtime - when fleet was updated last
 * @property int        $fleet_update_lock             => ''          // SQL time when lock was acquired
 *
 * @property string     $game_adminEmail               => 'root@localhost',    // Admin's email to show to users
 *
 * @property string     $game_default_language         => 'ru'
 * @property string     $game_default_skin             => 'skins/EpicBlue/'
 * @property string     $game_default_template         => 'OpenGame'
 *
 * @property int        $game_installed                => 0 - is game installed
 * @property int        $game_disable                  => GAME_DISABLE_INSTALL - Current game status - see GAME_DISABLE_xxx
 * @property string     $game_disable_reason           => 'SuperNova is in maintenance mode! Please return later!' - Status for custom disable reason
 *
 * @property int        $game_maxGalaxy                => 5
 * @property int        $game_maxSystem                => 199
 * @property int        $game_maxPlanet                => 15
 *
 * @property string     $game_name                     Server name as it would be seen through game
 *
 * @property string     $game_watchlist
 *
 * @property int        $metal_basic_income            => 40,
 * @property int        $crystal_basic_income          => 20,
 * @property int        $deuterium_basic_income        => 0,
 * @property int        $energy_basic_income           => 0,
 *
 * @property int        $game_news_actual              How long announcement would be marked as "New". In seconds. Default - 3 days PERIOD_DAY_3
 * @property int        $game_news_overview            How much last news to show in Overview page. Default - 3
 * @property int        $game_news_overview_show       How long news will be shown in Overview page in seconds. Default - 2 weeks. 0 - show all
 *
 * @property int        $game_noob_factor              => 5    // Multiplier to divide "stronger" and "weaker" users
 * @property int        $game_noob_points              => 5000 // Below this point user treated as noob. 0 to disable
 *
 * @property string     $int_format_date               => 'd.m.Y' // Date default format
 * @property string     $int_format_time               => 'H:i:s' // Time default format
 *
 * @property int        $menu_server_name_disabled     => 0
 * @property int        $menu_launch_date_disabled     => 0
 * @property int        $menu_server_logo              => MENU_SERVER_LOGO_DEFAULT
 * @property int        $menu_server_logo_disabled     => 0
 *
 * @property string     $payment_currency_default      => 'USD',
 * @property float      $payment_currency_exchange_dm_ => 20000,
 * @property float      $payment_currency_exchange_mm_ => 20000,
 * @property float      $payment_currency_exchange_eur => 0.90,
 * @property float      $payment_currency_exchange_rub => 60,
 * @property float      $payment_currency_exchange_uah => 30,
 * @property float      $payment_currency_exchange_usd => 1,
 * @property float      $payment_currency_exchange_wmb => 18000,
 * @property float      $payment_currency_exchange_wme => 0.9,
 * @property float      $payment_currency_exchange_wmr => 60,
 * @property float      $payment_currency_exchange_wmu => 30,
 * @property float      $payment_currency_exchange_wmz => 1,
 *
 * @property int        $tutorial_first_item           ID of first item of tutorial
 *
 * @property int        $url_faq                       URL of FAQ root
 *
 * @property int        $users_amount                  => 1,                // Total users count
 * @property int        $game_users_online_timeout     => PERIOD_MINUTE_15, // How long user should considered ONLINE for online counter (seconds)
 * @property int        $game_users_update_online      => 30,               // How often user online should be refreshed (seconds)
 * @property int        $var_online_user_time          => 0,                // When last time user online was refreshed (Unix timestamp)
 * @property int        $var_online_user_count         => 0,                // Last calculated online user count
 * @property int        $server_log_online             => 0,                // Log online user count
 *
 * @property int        $quest_total                   => 0, // Total number of quests
 *
 * @property float      $resource_multiplier           => 1, // aka Mining speed
 * @property float      $game_speed                    => 1, // Game speed aka Building/Research speed
 * @property float      $fleet_speed                   => 1, // Fleet speed
 * @property float      $game_speed_expedition         => 1, // Game expedition speed
 *
 * @property int        $tpl_minifier                  => 0, // Template minifier
 * @property int        $tpl_allow_php                 => 0, // PTL allow INCLUDEPHP and PHP tags
 *
 * @property int        $uni_galaxy_distance           => 20000, // Distance between galaxies
 *
 * ----- Player settings
 * @property int|float  $player_metamatter_immortal    => 200000, // MM amount to reward account with Immortal status
 *
 * @property int        $game_user_changename          => 2, // Is user allowed to change name after registration?
 * @property int        $game_user_changename_cost     => 100000, // Change name cost for paid changename
 *
 * @property int        $user_vacation_disable         => 0, // Disable vacation mode for players
 * @property int        $player_vacation_time          => PERIOD_WEEK, // Minimal vacation length in seconds
 * @property int        $player_vacation_timeout       => PERIOD_WEEK, // Timeout after leaving vacation to start new one in seconds
 *
 * @property string     $player_levels                 => '', // JSON-encoded array of [(int)level => (float)maxPointsForLevel]
 * @property string     $player_levels_calculated      => '2000-01-01 00:00:00', // Date and time where player level was calculated last
 *
 * @property int        $player_delete_time            => 3888000, //
 *
 *
 *
 * ----- Planet settings
 * @property int        $LastSettedGalaxyPos           => 1,
 * @property int        $LastSettedPlanetPos           => 1,
 * @property int        $LastSettedSystemPos           => 1,
 *
 * @property int        $eco_planet_starting_crystal   => 500,
 * @property int        $eco_planet_starting_deuterium => 0,
 * @property int        $eco_planet_starting_metal     => 500,
 * @property int        $eco_planet_storage_crystal    => 500000,
 * @property int        $eco_planet_storage_deuterium  => 500000,
 * @property int        $eco_planet_storage_metal      => 500000,
 *
 * @property int        $planet_capital_cost           => 25000, // Cost in DM to move Capital to current planet
 * @property float      $planet_capital_mining_rate    => 2.0,   // Capital Mining rates
 * @property float      $planet_capital_building_rate  => 2.0,   // Capital Building rates
 * @property int        $planet_teleport_cost          => 50000, // Cost of planet teleportation
 * @property int        $planet_teleport_timeout       => 86400, // Timeout for next teleportation
 *
 * @property string     $server_updater_check_auto     => 0, // Server autocheck version
 * @property int        $server_updater_check_last     => 0, // Server last check time
 * @property int        $server_updater_check_period   => PERIOD_DAY, // Server autocheck period
 * @property int        $server_updater_check_result   => SNC_VER_NEVER, // Server last check result
 * @property int|string $server_updater_id             => 0, // Server ID on update server
 * @property string     $server_updater_key            => '', // Server key on update server
 *
 * @property int        $stats_hide_admins             => 1,  // Hide admins accounts from stat and stat of admins
 * @property string     $stats_hide_player_list        => '', // Comma separated list of player IDs which stat to hide. Used for bots, for example
 * @property int        $stats_hide_pm_link            => 0,  // Hide PM link from stat screen
 * @property int        $stats_history_days            => 14, // За сколько дней хранить статистику в базе
 * @property string     $stats_minimal_interval        => STATS_RUN_INTERVAL_MINIMUM -  Minimal interval between stat runs in seconds. Default - 600s aka 10 minutes
 * @property string     $stats_schedule                => '04:00:00' - Schedule for running stat updates - see readme.txt
 * @property string     $stats_php_memory              => ???????????????
 *
 * @property int        $upd_lock_time                 => Update lock time
 *
 * @property string     $server_cypher                 => Internally generated cypher for in-server communications
 *
 * @property string            $url_purchase_metamatter       => URL to purchase MM for servers w/o payment modules
 *
 * @property string            $var_db_update                 => '0' - SQL_DATE_TIME
 * @property string            $var_db_update_end             => '0' - SQL_DATE_TIME
 *
 * @property string            $var_stat_update               => '0' - SQL_DATE_TIME - when stat update was started
 * @property string            $var_stat_update_end           => '0' - SQL_DATE_TIME - ?????????
 * @property string            $var_stat_update_admin_forced  => '0' - SQL_DATE_TIME - Last time when update was triggered from admin console
 * @property string            $var_stat_update_next          => ''  - SQL_DATE_TIME - Next time where stat update scheduled to run
 * @property string            $var_stat_update_msg           => 'Update never started' - Last stat update message
 *
 */
class classConfig extends classPersistent {
  const DATE_TYPE_UNIX = 0;
  const DATE_TYPE_SQL_STRING = 1;

  const FLEET_UPDATE_RUN_LOCK = 'fleet_update_run_lock';
  const FLEET_UPDATE_MAX_RUN_TIME = 'fleet_update_max_run_time';

  /**
   * Internal cypher string for server/server communication
   *
   * @var string $cypher
   */
  protected $cypher = '';

  protected $defaults = array(
    // SEO meta
    'adv_conversion_code_payment'  => '',
    'adv_conversion_code_register' => '',
    'adv_seo_meta_description'     => '',
    'adv_seo_meta_keywords'        => '',
    'adv_seo_javascript'           => '',

    // Advert banner
    'advGoogleLeftMenuIsOn'        => 0,
    'advGoogleLeftMenuCode'        => '(Place here code for banner)',

    // Alliance bonus calculations
    'ali_bonus_algorithm'          => 0,  // Bonus calculation algorithm
    'ali_bonus_brackets'           => 10, // Brackets count for ALI_BONUS_BY_RANK
    'ali_bonus_brackets_divisor'   => 10,// Bonus divisor for ALI_BONUS_BY_RANK
    'ali_bonus_divisor'            => 10000000, // Rank divisor for ALI_BONUS_BY_POINTS
    'ali_bonus_members'            => 10, // Minimum alliance size to start using bonus

    'allow_buffing'  => 0, // Disable buffing check for TRANSPORT missions
    'ally_help_weak' => 0, // Allow strong players to HOLD on weak co-ally planets

    'auth_vkontakte_app_id'       => '',
    'auth_vkontakte_app_key'      => '',
    'auth_vkontakte_token'        => '',
    'auth_vkontakte_token_expire' => '2000-01-01',

    // User avatar and alliance logo
    'avatar_max_height'           => 128, // Maximum height
    'avatar_max_width'            => 128, // Maximum width

    'BuildLabWhileRun'         => 0, // Allow to build lab/Nanolab while tech researching AND allowing research tech while lab/Nanolab

    // Chat settings
    // Nick highlighting
    'chat_highlight_developer' => '<span class=\"nick_developer\">$1</span>', // Developer nick
    'chat_highlight_admin'     => '<span class=\"nick_admin\">$1</span>', // Admin nick
    'chat_highlight_moderator' => '<span class=\"nick_moderator\">$1</span>', // Moderator nick
    'chat_highlight_operator'  => '<span class=\"nick_operator\">$1</span>', // Operator nick
    'chat_highlight_premium'   => '<span class=\"nick_premium\">$1</span>', // Premium nick
    // Other chat settings
    'chat_refresh_rate'        => 5, // in seconds. Chat AJAX refresh rate
    'chat_timeout'             => 900, // in seconds. Default = 15 min

    'COOKIE_NAME' => 'SuperNova',
    'debug'       => 0,
    'Defs_Cdr'    => 30,

    'eco_planet_starting_crystal'   => 500,
    'eco_planet_starting_deuterium' => 0,
    'eco_planet_starting_metal'     => 500,
    'eco_planet_storage_crystal'    => 500000,
    'eco_planet_storage_deuterium'  => 500000,
    'eco_planet_storage_metal'      => 500000,

    'eco_scale_storage'            => 1,
    'eco_stockman_fleet'           => '', // Black Market - Starting amount of s/h ship merchant to sell
    'eco_stockman_fleet_populate'  => 1,  // Populate empty Stockman fleet with ships or not
    'empire_mercenary_base_period' => PERIOD_MONTH, // Base
    'empire_mercenary_temporary'   => 0, // Temporary empire-wide mercenaries

    // Planet basic income
    'metal_basic_income'           => 40,
    'crystal_basic_income'         => 20,
    'deuterium_basic_income'       => 0,
    'energy_basic_income'          => 0,

    // Bashing protection settings
    'fleet_bashing_attacks'        => 3,      // Max amount of attack per wave - 3 by default
    'fleet_bashing_interval'       => 1800,   // Maximum interval between attacks when they still count as one wave - 30m by default
    'fleet_bashing_scope'          => 86400,  // Interval on which bashing waves counts - 24h by default
    'fleet_bashing_war_delay'      => 43200,  // Delay before start bashing after declaring war to alliance - 12h by default
    'fleet_bashing_waves'          => 3,      // Max amount of waves per day - 3 by default

    'Fleet_Cdr'   => 30,
    'fleet_speed' => 1,

    self::FLEET_UPDATE_MAX_RUN_TIME => 30,     // Maximum length in seconds for single fleet dispatch run
    'fleet_update_interval'         => 4,
    'fleet_update_lock'             => '', // SQL time when lock was acquired

    'game_adminEmail'       => 'root@localhost',    // Admin's email to show to users
    'game_counter'          => 0,  // Does built-in page hit counter is on?
    // Defaults
    'game_default_language' => 'ru',
    'game_default_skin'     => 'skins/EpicBlue/',
    'game_default_template' => 'OpenGame',

    'game_disable'        => GAME_DISABLE_INSTALL,
    'game_disable_reason' => 'SuperNova is in maintenance mode! Please return later!',
    'game_email_pm'       => 0, // Is allowed forwarding messages from PM to e-mail?
    // Universe size
    'game_maxGalaxy'      => 5,
    'game_maxSystem'      => 199,
    'game_maxPlanet'      => 15,
    // Game global settings
    'game_mode'           => 0,           // 0 - SuperNova, 1 - oGame
    'game_name'           => 'SuperNova', // Server name (would be on banners and on top of left menu)

    'game_news_actual'        => PERIOD_DAY_3, // How long announcement would be marked as "New". In seconds. Default - 3 days
    'game_news_overview'      => 3,    // How much last news to show in Overview page
    'game_news_overview_show' => PERIOD_WEEK_2,    // How long news will be shown in Overview page in seconds. Default - 2 weeks
    // Noob protection
    'game_noob_factor'        => 5,    // Multiplier to divide "stronger" and "weaker" users
    'game_noob_points'        => 5000, // Below this point user treated as noob. 0 to disable

    'game_multiaccount_enabled' => 0, // 1 - allow interactions for players with same IP (multiaccounts)

    'game_speed'            => 1, // Game speed
    'game_speed_expedition' => 1, // Game expedition speed

    'game_user_changename'      => 2, // Is user allowed to change name after registration?
    'game_user_changename_cost' => 100000, // Change name cost for paid changename

    'initial_fields'            => 163,

    // Interface - UserBanner
    'int_banner_background'     => 'design/images/banner.png',
    'int_banner_fontInfo'       => 'terminator.ttf',
    'int_banner_fontRaids'      => 'klmnfp2005.ttf',
    'int_banner_fontUniverse'   => 'cristal.ttf',
    'int_banner_showInOverview' => 1,
    'int_banner_URL'            => '/banner.php?type=banner',

    'int_format_date'            => 'd.m.Y', // Date default format
    'int_format_time'            => 'H:i:s', // Time default format

    // Interface - UserBar
    'int_userbar_background'     => 'design/images/userbar.png',
    'int_userbar_font'           => 'arialbd.ttf',
    'int_userbar_showInOverview' => 1,
    'int_userbar_URL'            => '/banner.php?type=userbar',

    'LastSettedGalaxyPos' => 1,
    'LastSettedPlanetPos' => 1,
    'LastSettedSystemPos' => 1,

    'locale_cache_disable' => 0, // Disable locale caching

    'menu_server_name_disabled' => 0,
    'menu_launch_date_disabled' => 0,
    'menu_server_logo'          => MENU_SERVER_LOGO_DEFAULT,
    'menu_server_logo_disabled' => 0,

    'payment_currency_default'      => 'USD',
    'payment_currency_exchange_dm_' => 20000,
    'payment_currency_exchange_mm_' => 20000,
    'payment_currency_exchange_eur' => 0.90,
    'payment_currency_exchange_rub' => 60,
    'payment_currency_exchange_uah' => 30,
    'payment_currency_exchange_usd' => 1,
    'payment_currency_exchange_wmb' => 18000,
    'payment_currency_exchange_wme' => 0.9,
    'payment_currency_exchange_wmr' => 60,
    'payment_currency_exchange_wmu' => 30,
    'payment_currency_exchange_wmz' => 1,
    'payment_currency_exchange_pln' => 3.86,

    'payment_lot_price' => 1,     // Lot price in default currency
    'payment_lot_size'  => 2500,  // Lot size. Also service as minimum amount of DM that could be bought with one transaction

    'planet_capital_cost'          => 25000, // Cost in DM to move Capital to current planet
    'planet_capital_mining_rate'   => 2.0,   // Capital Mining rates
    'planet_capital_building_rate' => 2.0,   // Capital Building rates
    'planet_teleport_cost'         => 50000, // Cost of planet teleportation
    'planet_teleport_timeout'      => 86400, // Timeout for next teleportation

    'player_delete_time'  => 3888000, //
    'player_max_colonies' => -1, // Max player planet count (NOT including main planet)

    'user_vacation_disable'   => 0, // Disable vacation mode for players
    'player_vacation_time'    => PERIOD_WEEK, //
    'player_vacation_timeout' => PERIOD_WEEK, //

    'player_metamatter_immortal' => 200000, // MM amount to reward account with Immortal status

    'player_levels'            => '', // JSON-encoded array of [(int)level => (float)maxPointsForLevel]
    'player_levels_calculated' => '2000-01-01 00:00:00', // Date and time where player level was calculated last

    // Quests
    'quest_total'              => 0, // Total number of quests

    'resource_multiplier'     => 1,

    //Roleplay system
    'rpg_bonus_divisor'       => 10,    // Amount of DM referral shoud get for partner have 1 DM bonus
    'rpg_bonus_minimum'       => 10000, // Minimum DM ammount for starting paying bonuses to affiliate

    // Black Market - General
    'rpg_cost_banker'         => 1000, // Banker can hold some resources
    'rpg_cost_exchange'       => 1000, // Exchange allows resource trade between players
    'rpg_cost_info'           => 10000, // Infotrader sells valuable information about users, alliances and universe
    'rpg_cost_pawnshop'       => 1000, // You can get loan in pawnshop
    'rpg_cost_scraper'        => 1000, // Scrapper buys ship for fraction of cost
    'rpg_cost_stockman'       => 1000, // Stockman resells ship that was scrapped
    'rpg_cost_trader'         => 1000, // Trader trades between resources

    // Black Market - Resource exachange rates
    'rpg_exchange_metal'      => 1,
    'rpg_exchange_crystal'    => 2,
    'rpg_exchange_deuterium'  => 4,
    'rpg_exchange_darkMatter' => 400,

    'rpg_flt_explore'      => 1000, // DM reward for finding Supernova in expedition

    // Black Market - Scraper rates for ship pre resource
    'rpg_scrape_crystal'   => 0.50,
    'rpg_scrape_deuterium' => 0.25,
    'rpg_scrape_metal'     => 0.75,

    'security_ban_extra'               => '',
    'security_write_full_url_disabled' => 1, // Disables writing full URLs to counter table

    'server_que_length_hangar'     => '5', //
    'server_que_length_research'   => '1', //
    'server_que_length_structures' => '5', //

    'server_start_date' => '', //

    'server_updater_check_auto'   => 0, // Server autocheck version
    'server_updater_check_last'   => 0, // Server last check time
    'server_updater_check_period' => PERIOD_DAY, // Server autocheck period
    'server_updater_check_result' => SNC_VER_NEVER, // Server last check result
    'server_updater_id'           => 0, // Server ID on update server
    'server_updater_key'          => '', // Server key on update server

    'stats_history_days'     => 14, // За сколько дней хранить статистику в базе
    'stats_hide_admins'      => 1,
    'stats_hide_player_list' => '',
    'stats_hide_pm_link'     => 0,
    'stats_minimal_interval' => STATS_RUN_INTERVAL_MINIMUM, // Minimal stats interval
    'stats_schedule'         => '04:00:00',
    'stats_php_memory'       => '1G',

    'tpl_minifier'  => 0, // Template minifier
    'tpl_allow_php' => 0, // PTL allow INCLUDEPHP and PHP tags

    'tutorial_first_item' => 1,

    'uni_galaxy_distance' => UNIVERSE_GALAXY_DISTANCE, // 20000 by default
    'uni_price_galaxy'    => 10000,
    'uni_price_system'    => 1000,

    'upd_lock_time' => 300, // How long update will lock table. Also update increment time when it requires

    'url_faq'                 => '',
    'url_forum'               => '',
    'url_purchase_metamatter' => '',
    'url_rules'               => '',

    'users_amount'              => 1,                // Total users count
    'game_users_online_timeout' => PERIOD_MINUTE_15, // Seconds, How long user should considered ONLINE for online counter
    'game_users_update_online'  => 30,               // How often user online should be refreshed (seconds)
    'var_online_user_time'      => 0,                // When last time user online was refreshed
    'var_online_user_count'     => 0,                // Last calculated online user count
    'server_log_online'         => 0,                // Log online user count

    'user_birthday_celebrate' => 0, // When last time celebrations (i.e. giftgiving) was made
    'user_birthday_gift'      => 0, // User birthday gift
    'user_birthday_range'     => PERIOD_MONTH, // How far in past can be user birthday for giving him gift


    'var_db_update'     => 0, // Time of last DB update
    'var_db_update_end' => 0, // Time when last DB update should end. Need to prevent duplicate update

    'var_news_last'       => 0, // Last news post time

    // Statistic
    'var_stat_update'     => 0,
    'var_stat_update_end' => 0,
    'var_stat_update_msg' => 'Update never started',

  );

  protected $notEmptyFields = [
    'upd_lock_time' => 'upd_lock_time',
  ];

  public function __construct($gamePrefix = 'sn_') {
    parent::__construct($gamePrefix, 'config');
  }

  public static function getInstance($gamePrefix = 'sn_', $table_name = 'config') {
    if (!isset(self::$cacheObject)) {
      $className         = get_class();
      self::$cacheObject = new $className($gamePrefix, $table_name);
    }

    return self::$cacheObject;
  }

  /**
   * @param int|string $date Date ether as Unix timestamp or mySQL timestamp
   * @param int        $as   Output format WATCHDOG_TIME_UNIX | WATCHDOG_TIME_SQL
   *
   * @return false|int|string Will return 0 on invalid string with WATCHDOG_TIME_UNIX and FALSE on invalid value with WATCHDOG_TIME_UNIX
   * @see FMT_DATE_TIME_SQL
   */
  public function dateConvert($date, $as) {
    if ($as === self::DATE_TYPE_UNIX && !is_numeric($date)) {
      // It is not a TIMESTAMP - may be it's SQL timestamp or other date-related string? Trying to convert to UNIX
      $date = intval(strtotime($date, SN_TIME_NOW));
    } elseif ($as === self::DATE_TYPE_SQL_STRING && (!is_string($date) || is_numeric($date))) {
      $date = date(FMT_DATE_TIME_SQL, $date);
    }

    return $date;
  }

  /**
   * Will write to DB date as specified format
   *
   * @param string     $name Config field name
   * @param int|string $date Date ether as Unix timestamp or mySQL timestamp
   * @param int        $as   Format of field in config table WATCHDOG_TIME_UNIX | WATCHDOG_TIME_SQL
   *
   * @return classConfig
   * @see dateConvert()
   */
  public function dateWrite($name, $date, $as = self::DATE_TYPE_SQL_STRING) {
    $this->pass()[$name] = $this->dateConvert($date, $as);

    return $this;
  }

  /**
   * Will read from DB date and convert it to specified format
   *
   * @param string $name Config field name
   * @param int    $as   Output format WATCHDOG_TIME_UNIX | WATCHDOG_TIME_SQL
   *
   * @return false|int|string
   * @see dateConvert()
   */
  public function dateRead($name, $as) {
    return $this->dateConvert($date = $this->pass()[$name], $as);
  }

  public function getCypher() {
    $db = SN::$gc->db;

    if (empty($this->cypher)) {
      $db->transactionStart();
      $cypher = $this->pass()->server_cypher;
      if (empty($cypher)) {
        $cypher = md5(sys_random_string(32));

        $this->pass()->server_cypher = $cypher;

        $db->transactionCommit();
      } else {
        $db->transactionRollback();
      }
      $this->cypher = $cypher;
    }

    return $this->cypher;
  }

}
