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
 * @property string auth_vkontakte_app_id
 * @property string auth_vkontakte_app_key
 * @property string auth_vkontakte_token
 * @property int    $auth_vkontakte_token_expire
 */
class classConfig extends classPersistent {
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
    'ali_bonus_members'            => 10, // Minumum alliace size to start using bonus

    'allow_buffing'                => 0, // Disable buffing check for TRANSPORT missions
    'ally_help_weak'               => 0, // Allow strong players to HOLD on weak co-ally planets

    'auth_vkontakte_app_id'        => '',
    'auth_vkontakte_app_key'       => '',
    'auth_vkontakte_token'         => '',
    'auth_vkontakte_token_expire'  => '2000-01-01',

    // User avatar and alliance logo
    'avatar_max_height'            => 128, // Maximum height
    'avatar_max_width'             => 128, // Maximum width

    'BuildLabWhileRun'             => 0,

    // Chat settings
    // Nick highliting
    'chat_highlight_developer'     => '<span class=\"nick_developer\">$1</span>', // Developer nick
    'chat_highlight_admin'         => '<span class=\"nick_admin\">$1</span>', // Admin nick
    'chat_highlight_moderator'     => '<span class=\"nick_moderator\">$1</span>', // Moderator nick
    'chat_highlight_operator'      => '<span class=\"nick_operator\">$1</span>', // Operator nick
    'chat_highlight_premium'       => '<span class=\"nick_premium\">$1</span>', // Premium nick
    // Other chat settings
    'chat_refresh_rate'            => 5, // in seconds. Chat AJAX refresh rate
    'chat_timeout'                 => 900, // in seconds. Default = 15 min

    'COOKIE_NAME'                  => 'SuperNova',
    'crystal_basic_income'         => 20,
    'debug'                        => 0,
    'Defs_Cdr'                     => 30,
    'deuterium_basic_income'       => 0,
    'eco_scale_storage'            => 1,
    'eco_stockman_fleet'           => '', // Black Market - Starting amount of s/h ship merchant to sell
    'eco_stockman_fleet_populate'  => 1,  // Populate empty Stockman fleet with ships or not
    'empire_mercenary_base_period' => PERIOD_MONTH, // Base
    'empire_mercenary_temporary'   => 0, // Temporary empire-wide mercenaries
    'energy_basic_income'          => 0,

    // Bashing protection settings
    'fleet_bashing_attacks'        => 3,      // Max amount of attack per wave - 3 by default
    'fleet_bashing_interval'       => 1800,   // Maximum interval between attacks when they still count as one wave - 30m by default
    'fleet_bashing_scope'          => 86400,  // Interval on which bashing waves counts - 24h by default
    'fleet_bashing_war_delay'      => 43200,  // Delay before start bashing after declaring war to alliance - 12h by default
    'fleet_bashing_waves'          => 3,      // Max amount of waves per day - 3 by default

    'Fleet_Cdr'                    => 30,
    'fleet_speed'                  => 1,

    'fleet_update_interval'        => 4,

    'game_adminEmail'              => 'root@localhost',    // Admin's email to show to users
    'game_counter'                 => 0,  // Does built-in page hit counter is on?
    // Defaults
    'game_default_language'        => 'ru',
    'game_default_skin'            => 'skins/EpicBlue/',
    'game_default_template'        => 'OpenGame',

    'game_disable'                 => GAME_DISABLE_INSTALL,
    'game_disable_reason'          => 'SuperNova is in maintenance mode! Please return later!',
    'game_email_pm'                => 0, // Is allowed forwarding messages from PM to e-mail?
    // Universe size
    'game_maxGalaxy'               => 5,
    'game_maxSystem'               => 199,
    'game_maxPlanet'               => 15,
    // Game global settings
    'game_mode'                    => 0,           // 0 - SuperNova, 1 - oGame
    'game_name'                    => 'SuperNova', // Server name (would be on banners and on top of left menu)

    'game_news_actual'             => 259200, // How long announcement would be marked as "New". In seconds. Default - 3 days
    'game_news_overview'           => 3,    // How much last news to show in Overview page
    // Noob protection
    'game_noob_factor'             => 5,    // Multiplier to divide "stronger" and "weaker" users
    'game_noob_points'             => 5000, // Below this point user threated as noob. 0 to disable

    'game_multiaccount_enabled'    => 0, // 1 - allow interactions for players with same IP (multiaccounts)

    'game_speed'                   => 1, // Game speed
    'game_user_changename'         => 2, // Is user allowed to change name after registration?
    'game_user_changename_cost'    => 100000, // Change name cost for paid changename

    'initial_fields'               => 163,

    // Interface - UserBanner
    'int_banner_background'        => 'design/images/banner.png',
    'int_banner_fontInfo'          => 'terminator.ttf',
    'int_banner_fontRaids'         => 'klmnfp2005.ttf',
    'int_banner_fontUniverse'      => 'cristal.ttf',
    'int_banner_showInOverview'    => 1,
    'int_banner_URL'               => '/banner.php?type=banner',

    'int_format_date'              => 'd.m.Y', // Date default format
    'int_format_time'              => 'H:i:s', // Time default format

    // Interface - UserBar
    'int_userbar_background'       => 'design/images/userbar.png',
    'int_userbar_font'             => 'arialbd.ttf',
    'int_userbar_showInOverview'   => 1,
    'int_userbar_URL'              => '/banner.php?type=userbar',

    'LastSettedGalaxyPos'          => 1,
    'LastSettedPlanetPos'          => 1,
    'LastSettedSystemPos'          => 1,

    'locale_cache_disable'         => 0, // Disable locale caching

    'metal_basic_income'           => 40,

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

    'payment_lot_price'             => 1,     // Lot price in default currency
    'payment_lot_size'              => 2500,  // Lot size. Also service as minimum amount of DM that could be bought with one transaction

    'planet_teleport_cost'         => 50000, //
    'planet_teleport_timeout'      => 86400, //

    'player_delete_time'           => 3888000, //
    'player_max_colonies'          => -1, // Max player planet count (NOT including main planet)
    'player_vacation_time'         => PERIOD_WEEK, //
    'player_vacation_timeout'      => PERIOD_WEEK, //

    'player_metamatter_immortal'   => 100000,

    // Quests
    'quest_total'                  => 0, // Total number of quests

    'resource_multiplier'          => 1,

    //Roleplay system
    'rpg_bonus_divisor'            => 10,    // Amount of DM referral shoud get for partner have 1 DM bonus
    'rpg_bonus_minimum'            => 10000, // Minimum DM ammount for starting paying bonuses to affiliate

    // Black Market - General
    'rpg_cost_banker'              =>  1000, // Banker can hold some resources
    'rpg_cost_exchange'            =>  1000, // Exchange allows resource trade between players
    'rpg_cost_info'                => 10000, // Infotrader sells valuable information about users, alliances and universe
    'rpg_cost_pawnshop'            =>  1000, // You can get loan in pawnshop
    'rpg_cost_scraper'             =>  1000, // Scrapper buys ship for fraction of cost
    'rpg_cost_stockman'            =>  1000, // Stockman resells ship that was scrapped
    'rpg_cost_trader'              =>  1000, // Trader trades between resources

    // Black Market - Resource exachange rates
    'rpg_exchange_metal'           =>   1,
    'rpg_exchange_crystal'         =>   2,
    'rpg_exchange_deuterium'       =>   4,
    'rpg_exchange_darkMatter'      => 400,

    'rpg_flt_explore'              => 1000, // DM reward for finding Supernova in expedition

    // Black Market - Scraper rates for ship pre resource
    'rpg_scrape_crystal'           => 0.50,
    'rpg_scrape_deuterium'         => 0.25,
    'rpg_scrape_metal'             => 0.75,

    'security_ban_extra'           => '',
    'security_write_full_url_disabled' => 1, // Disables writing full URLs to counter table

    'server_log_online'            => 0, //

    'server_que_length_hangar'     => '5', //
    'server_que_length_research'   => '1', //
    'server_que_length_structures' => '5', //

    'server_start_date'            => '', //

    'server_updater_check_auto'    => 0, // Server autocheck version
    'server_updater_check_last'    => 0, // Server last check time
    'server_updater_check_period'  => PERIOD_DAY, // Server autocheck period
    'server_updater_check_result'  => SNC_VER_NEVER, // Server last check result
    'server_updater_id'            => 0, // Server ID on update server
    'server_updater_key'           => '', // Server key on update server

    'stats_history_days'           => 14, // За сколько дней хранить статистику в базе
    'stats_hide_admins'            => 1,
    'stats_hide_player_list'       => '',
    'stats_hide_pm_link'           => 0,
    'stats_schedule'               => '04:00:00',

    'tpl_minifier'                 => 0, // Template minifier

    'uni_price_galaxy'             => 10000,
    'uni_price_system'             => 1000,

    'upd_lock_time'                => 60, // How long update will lock table. Also update increment time when it requires

    'url_faq'                      => '',
    'url_forum'                    => '',
    'url_purchase_metamatter'      => '',
    'url_rules'                    => '',

    'users_amount'                 => 1,

    'user_birthday_celebrate'      => 0, // When last time celebrations (i.e. giftgiving) was made
    'user_birthday_gift'           => 0, // User birthday gift
    'user_birthday_range'          => PERIOD_MONTH, // How far in past can be user birthday for giving him gift

    'user_vacation_disable'        => 0,

    'var_db_update'                => 0, // Time of last DB update
    'var_db_update_end'            => 0, // Time when last DB update should end. Need to prevent duplicate update

    'var_news_last'                => 0, // Last news post time

    // Statistic
    'var_stat_update'              => 0,
    'var_stat_update_end'          => 0,
    'var_stat_update_msg'          => 'Update never started',

  );

  public function __construct($gamePrefix = 'sn_') {
    parent::__construct($gamePrefix, 'config');
  }

  public static function getInstance($gamePrefix = 'sn_', $table_name = 'config') {
    if(!isset(self::$cacheObject)) {
      $className = get_class();
      self::$cacheObject = new $className($gamePrefix, $table_name);
    }
    return self::$cacheObject;
  }
}
