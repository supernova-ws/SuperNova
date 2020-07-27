<?php

/*
#############################################################################
#  Filename: admin.mo
#  Project: SuperNova.WS
#  Website: http://www.supernova.ws
#  Description: Massive Multiplayer Online Browser Space Strategy Game
#
#  Copyright © 2009-2018 Gorlum for Project "SuperNova.WS"
#  Copyright © 2008 Aleksandar Spasojevic <spalekg@gmail.com>
#  Copyright © 2005 - 2008 KGsystem
#############################################################################
*/

/**
*
* @package language
* @system [English]
* @version 45d0
*
*/

/**
* DO NOT CHANGE
*/

if (!defined('INSIDE')) die();


$a_lang_array = (array(
  'menu_admin_ally' => 'Alliances',

  'adm_tool_md5_header' => 'Password encoding (MD5)',
  'adm_tool_md5_hash' => 'MD5 hash',
  'adm_tool_md5_encode' => '[ Encode ]',
  'adm_tool_md5_generate' => '[ Generate ]',

  'adm_tool_sql_page_header' => 'SQL server parameters',

  'adm_tool_sql_server_version' => 'Server version',
  'adm_tool_sql_client_version' => 'Libriary version',
  'adm_tool_sql_host_info' => 'OS communication method',

  'adm_tool_sql_table' => array(
    'server' => array(
      'TABLE_HEADER'  => 'SQL server',
      'COLUMN_NAME_1' => 'Parameter',
      'COLUMN_NAME_2' => 'Value',
    ),

    'status' => array(
      'TABLE_HEADER'  => 'SQL status',
      'COLUMN_NAME_1' => 'Parameter',
      'COLUMN_NAME_2' => 'Value',
    ),

    'params' => array(
      'TABLE_HEADER'  => 'SQL settings',
      'COLUMN_NAME_1' => 'Parameter',
      'COLUMN_NAME_2' => 'Value',
    ),
  ),

  'adm_pl_image' => 'Planet image',
  'adm_pl_fields_max' => 'Max sectors',
  'adm_pl_fields_busy' => 'Sectors occupied',
  'adm_pl_temp_min' => 'Minimal temperature',
  'adm_pl_temp_max' => 'Maximal temperature',
  'adm_pl_governor' => 'Governor',
  'adm_pl_debris_metal' => 'Metal debris',
  'adm_pl_debris_crystal' => 'Crystal debris',

  'adm_sys_write_message' => 'Write message',

  'adm_opt_user_settings' => 'User settings',
  'adm_opt_user_birthday_gift' => 'Birthday gift',
  'adm_opt_user_birthday_gift_disable' => '0 - disable gifts',
  'adm_opt_user_birthday_range' => 'Retro-birthday, in days',
  'adm_opt_user_birthday_range_hint' => 'How far in past can be user birthday for giving him gift. Obviously can not be more then 364 days',

  'adm_done' => 'Successfully Completed',
  'adm_inactive_removed' => '<li>Remove inactive players: %d</li>',
  'adm_stat_title' => 'Update statistics',
  'adm_maintenance_title' => 'Database Services',
  'adm_records' => 'Processed records',
  'adm_cleaner_title' => 'Clean queue structures',
  'adm_cleaned' => 'Number of deleted tasks: ',
  'adm_schedule_none' => 'There are no tasks in the schedule for now',
  'Fix' => 'Updated',
  'Welcome_to_Fix_section' => 'Section Patches',
  'There_is_not_need_fix' => 'Fix unnecessary!',
  'Fix_welldone' => 'Done!',
  'adm_ov_title' => 'Overview',
  'adm_ov_infos' => 'Information',
  'adm_ov_yourv' => 'The current version',
  'adm_ov_lastv' => 'Available version',
  'adm_ov_here' => 'Here',
  'adm_ov_onlin' => 'Online',
  'adm_ov_ally' => 'Alliance',
  'adm_ov_point' => 'Point',
  'adm_ov_activ' => 'Active',
  'adm_ov_count' => 'Online players',
  'adm_ov_wrtpm' => 'Write Private Message',
  'adm_ov_altpm' => '[PM]',
  'adm_ov_hint' => '<ul><li>Таблица пользователей онлайн может быть отсортирована по колонкам "ID", "Имя игрока", "Альянс", "Очки" и "Активность". Для сортировки по определенной колонке кликните на её заголовке</li></ul>',


  'adm_ul_title' => 'Player list',
    'adm_ul_title_online' => 'Players online',
  'adm_ul_time_registered' => 'Register date',
  'adm_ul_time_played' => 'Last played',
  'adm_ul_time_banned' => 'Banned until',
  'adm_ul_referral' => 'Referrals',
  'adm_ul_players' => 'Players',
  'adm_ul_dms' => 'DM',
  'adm_sys_actions' => 'Actions',


  'adm_ul_bana' => 'Ban',
  'adm_ul_detai' => 'Details',
  'adm_ul_actio' => 'Actions',
  'adm_ul_playe' => ' Players',
  'adm_ul_yes' => 'Yes',
  'adm_ul_no' => 'No',
  'adm_pl_title' => 'Active planet',
  'adm_pl_activ' => 'Active planet',
  'adm_pl_name' => 'Planet name',
  'adm_pl_posit' => 'Coordinates',
  'adm_pl_point' => 'Value',
  'adm_pl_since' => 'Is Active',
  'adm_pl_they' => 'Total',
  'adm_pl_apla' => 'Planet(s)',
  'adm_am_plid' => 'Planet ID',
  'adm_am_done' => 'Add was successful',
  'adm_am_ttle' => 'Add resources',
  'adm_am_add' => 'Confirm',
  'adm_am_form' => 'One-step add link form resources',
  'adm_ban_title' => 'Banned Players',
  'adm_bn_plto' => 'Banned Players',
  'adm_bn_name' => 'Player name',
  'adm_bn_reas' => 'Reason for ban',
  'adm_bn_isvc' => 'Vacation mode',
  'adm_bn_time' => 'Duration of Ban',
  'adm_bn_days' => 'Days',
  'adm_bn_hour' => 'Hours',
  'adm_bn_mins' => 'Mins',
  'adm_bn_secs' => 'Seconds',
  'adm_bn_bnbt' => 'The ban',
  'adm_bn_thpl' => 'Player',
  'adm_bn_isbn' => 'Successfully Banned!',
  'adm_bn_vctn' => ' Vacation mode.',
  'adm_bn_errr' => 'Error: Banning player! perhaps Name %s not found.',
  'adm_bn_err2' => 'Error: Unable to stop production on the planets!',
  'adm_bn_plnt' => 'Production on the planets is disabled.',
  'adm_ban_msg_issued_date' => 'banned player on',
  'adm_unbn_ttle' => 'Unban',
  'adm_unbn_plto' => 'Unban player',
  'adm_unbn_name' => 'Name',
  'adm_unbn_bnbt' => 'Unban',
  'adm_unbn_thpl' => 'Player',
  'adm_unbn_isbn' => 'Unbanned!',
  'adm_rz_ttle' => 'Zeroing Universe',
  'adm_rz_done' => 'User(s) of transfer(s)',
  'adm_rz_conf' => 'Confirmation',
  'adm_rz_text' => 'Clicking (reset) You delete all database. You did backup??? Accounts will not be removed...',
  'adm_rz_doit' => 'Zero out',
  'adm_ch_ttle' => 'Admin chat',
  'adm_ch_list' => 'Message list',
  'adm_ch_clear' => 'Clear',
  'adm_ch_idmsg' => 'ID',
  'adm_ch_delet' => 'Delete',
  'adm_ch_play' => 'Player',
  'adm_ch_time' => 'Date',
  'adm_ch_chat' => 'Chat',
  'adm_ch_nbs' => 'Total messages...',
  'adm_er_ttle' => 'Log records',
  'adm_er_clear' => 'Clear',
  'adm_er_idmsg' => 'ID',
  'adm_er_type' => '[Code] Title',
  'adm_er_play' => 'Player',
  'adm_er_time' => 'Date',
  'adm_er_page' => 'Address of the page',
  'adm_er_nbs' => 'Total log records:',
  'adm_er_text' => 'Log record',
  'adm_er_bktr' => 'Debugging information',



  'adm_dm_title' => 'Change the number of dark matter',
  'adm_dm_planet' => 'ID, Coordinates or name of the planet',
  'adm_dm_oruser' => 'Or',
  'adm_dm_user' => 'ID or username',
  'adm_or_large' => 'OR',
  'adm_dm_no_quant' => 'Specify amount of Dark Matter (negative - for removal)',
  'adm_dm_no_dest' => 'Specify the player ID or name to change Dark Matter',
  'adm_dm_add_err' => 'It look like during charging Dark Matter error occured.',
  'adm_dm_user_none' => 'Error: could not find user with ID or name "%s"',
  'adm_dm_user_added' => 'Dark Matter on user [%2$d] "%1$s" succesfully changed by %3$s DM',
  'adm_dm_user_conflict' => 'Error locating user: looks like the Database is the user and with the same name, and with the same ID',
  'adm_dm_planet_none' => 'Error locating planet: Planet ID is not found, coordinates or name %s',
  'adm_dm_planet_added' => 'The user ID number DM %1$d (owner of planet %4$s %2$s ID %3$d) successfully renamed to %5$d DM.',
  'adm_dm_planet_conflict' => 'Non-unique data to search for the planet.<br>This means that the Database at the same time there is a ',
  'adm_dm_planet_conflict_id' => 'Planet named "%1$s" and the planet with ID %1$s .<br>try using the coordinates of the planet.',
  'adm_dm_planet_conflict_name' => 'Multiple planets named "%1$s".<br>try using coordinates or ID planet.',
  'adm_dm_planet_conflict_coords' => 'Planet named "%1$s" and the planet coordinates %1$s.<br>try using the ID of the planet.',

  'adm_mm_change_hint' => 'User IDs searched first. If not found - name search performed',

  'adm_apply' => 'Apply',
  'adm_maint' => 'Servicing',
  'adm_backup' => 'Backup',
  'adm_tools' => 'Utilities',
  'adm_tools_reloadConfig' => 'Recalculate configuration',
  'adm_reason' => 'The reason for',
  'adm_opt_title' => 'Configuration of the universe',
  'adm_opt_game_settings' => 'Game parameters',
  'adm_opt_game_name' => 'Universe name',
  'adm_opt_multiaccount_enabled' => 'Enable multiaccounts',
  'adm_opt_speed' => 'Speed',
  'adm_opt_game_gspeed' => 'Games',
  'adm_opt_game_fspeed' => 'Fleet',
  'adm_opt_game_pspeed' => 'Resource',
  'adm_opt_colonies_not_counted' => '(apart from Capital)',
  'adm_opt_colonies_no_restrictions' => '(-1 - no restrictions)',
  'adm_opt_game_speed_normal' => '(1&nbsp;-&nbsp;normal)',
  'adm_opt_game_faq' => 'Link to FAQ',
  'adm_opt_game_forum' => 'Forum address',
  'adm_opt_game_metamatter' => 'Reference &quot;Purchase Metamatter&quot;',
  'adm_opt_game_copyrigh' => 'Copyright',
  'adm_opt_game_online' => 'Turn off the game. Users will see the following message:',
  'adm_opt_game_offreaso' => 'Turn off reason',
  'adm_opt_plan_settings' => 'Planet settings',
  'adm_opt_plan_initial' => 'Size of start planet',
  'adm_opt_plan_base_inc' => 'Basic production',
  'adm_opt_game_debugmod' => 'Enable debug mode',
  'adm_opt_game_counter' => 'Add game counter',
  'adm_opt_geoip_whois_url' => 'WHOIS provider URL',
  'adm_opt_geoip_whois_url_example' => '(i.e. "http://1whois.ru/?ip=")',
  'adm_opt_game_oth_info' => 'Other options',
  'adm_opt_int_news_count' => 'News count',
  'adm_opt_int_page_imperor' => 'On the page &quot;Emperor&quot;',
  'adm_opt_game_zero_disable' => '(0&nbsp;-&nbsp;Disable)',
  'adm_opt_game_advertise' => 'Ad units',
  'adm_opt_game_oth_adds' => 'Enable the ad block in the left menu. Banner code:',
  'adm_opt_game_oth_gala' => 'Galaxy',
  'adm_opt_game_oth_syst' => 'System',
  'adm_opt_game_oth_plan' => 'Planet',
  'adm_opt_btn_save' => 'Save',
  'adm_opt_vacation_mode' => 'Turn off vacation',
  'adm_opt_sectors' => 'Fields',
  'adm_opt_per_hour' => 'per hour',
  'adm_opt_saved' => 'Game settings saved successfully',
  'adm_opt_players_online' => 'Players on the server',
  'adm_opt_vacation_mode_is' => 'Vacation mode',
  'adm_opt_game_status' => 'Game status',
  'adm_opt_links' => 'Links and banners',
  'adm_opt_universe_size' => 'Universe size',
  'adm_opt_galaxies' => 'Galaxies',
  'adm_opt_systems' => 'Systems',
  'adm_opt_planets' => 'Planets',
  'adm_opt_build_on_research' => 'Build on research',
  'adm_opt_eco_scale_storage' => 'Scale storages with production speed',
  'adm_opt_game_rules' => 'Game rules',
  'adm_opt_max_colonies' => 'Number of colonies',
  'adm_opt_exchange' => 'Exchange resources',
  'adm_opt_game_mode' => 'Type of universe',
  'adm_opt_chat' => 'Chat settings',
  'adm_opt_chat_timeout' => 'Timeout for idle',
  'adm_opt_allow_buffing' => 'Allow buffing',
  'adm_opt_ally_help_weak' => 'Allow HOLD on weak co-ally',
  'adm_opt_email_pm' => 'Enables sending PM to e-mail',
  'adm_opt_player_defaults' => 'Default player setting',
  'adm_opt_game_default_language' => 'Default language',
  'adm_opt_game_default_skin' => 'Skin',
  'adm_opt_game_default_template' => 'Template',
  'adm_opt_player_change_name' => 'Player can change nickname',
  'adm_opt_player_change_name_options' => [
    SERVER_PLAYER_NAME_CHANGE_NONE => 'Name change is forbidden',
    SERVER_PLAYER_NAME_CHANGE_FREE => 'Player can chane nickname',
    SERVER_PLAYER_NAME_CHANGE_PAY  => 'Player can change nickname for DM',
  ],
  'adm_opt_player_change_name_cost' => 'DM cost for player to change nickname',
  'adm_opt_empire_mercenary_temporary' => 'Temporary mercenaries',
  'adm_opt_empire_mercenary_temporary_base' => 'Base hire period, seconds',
  'adm_opt_empire_mercenary_temporary_hint' => 'When switching this option on all permanent Mercenaries would be converted to temporary with base active period<br />When switching this option off all active Mercenaries would be converted to permanent ones. If newly converted Mercenaries are not accessible for hiring they can not be bought but still be active and will affect game',
  'adm_opt_experimental' => 'EXPERIMENTAL OPTIONS! USE WITH CAUTION!',
  'adm_opt_tpl_minifier' => 'Template minifier',
  'adm_opt_tpl_minifier_hint' => 'Minifier compress templates by replacing several repetive spacechars (new line, tabulation, space) with single space. More infor about template minifier in /docs/changelog.txt',
  'adm_lm_compensate' => 'Compensation',
  'adm_pl_comp_title' => 'Compensation for destroyed planet',
  'adm_pl_comp_src' => 'Destroy the planet',
  'adm_pl_comp_dst' => 'Credit resources on the planet',
  'adm_pl_comp_bonus' => 'Bonus player',
  'adm_pl_comp_check' => 'Check',
  'adm_pl_comp_confirm' => 'Confirm',
  'adm_pl_comp_done' => 'Finish',
  'adm_pl_comp_price' => 'Cost structures',
  'adm_pl_comp_got' => 'Be enrolled',
  'adm_pl_com_of_plr' => 'Player',
  'adm_pl_comp_will_be' => 'will be',
  'adm_pl_comp_destr' => 'destroyed.',
  'adm_pl_comp_recieve' => 'The specified number of resources',
  'adm_pl_comp_recieve2' => 'enrolled on the planet',
  'adm_pl_comp_err_0' => 'Not found to be destroyed planet',
  'adm_pl_comp_err_1' => 'Planet destroyed',
  'adm_pl_comp_err_2' => 'Not found, the planet you want to enroll',
  'adm_pl_comp_err_3' => 'From the planets different owners. Credit resources can only be the same player on the planet',
  'adm_pl_comp_err_4' => 'Planet belongs to the specified player',
  'adm_pl_comp_err_5' => 'Planet for -- and for credit resources match',
  'adm_ver_versions' => 'Version of server components',
  'adm_ver_version_sn' => 'Version',
  'adm_ver_version_db' => 'Database version',
  'adm_update_force' => 'Force Update',
  'adm_update_repeat' => 'Repeat last system update',
  'adm_ptl_test' => 'phpBB Template Engine test',
  'adm_counter_recalc' => 'Recalc `counter` table',
  'adm_lm_planet_edit' => 'Edit planet',
  'adm_planet_edit' => 'Edit planet',
  'adm_planet_id' => 'Planet ID',
  'adm_name' => 'Name',
  'adm_planet_change' => 'Change',
  'adm_planet_parent' => 'Parent Planet',
  'adm_planet_active' => 'Active Planets',
  'adm_planet_edit_hint' => '<ul>    <li>Entering planet ID and pressing "Confirm" on empty page will print info about planet: type, name, coordinates and current amount of units/resources of    selected type</li>    <li>To remove units/resources from planet enter negative value</li>  </ul>',
  'adm_planet_list_title' => 'Planet List',
  'adm_sys_owner' => 'Owner',
  'adm_sys_owner_id' => 'Onwer ID',
  'addm_title' => 'Add Moon',
  'addm_addform' => 'Form new moon',
  'addm_playerid' => 'ID world accommodation',
  'addm_moonname' => 'The name of the Moon',
  'addm_moongala' => 'Specify the Galaxy',
  'addm_moonsyst' => 'Specify system',
  'addm_moonplan' => 'Specify position',
  'addm_moondoit' => 'Add',
  'addm_done' => 'The Moon formed',
  'adm_usr_level' => array(
    '0' => 'Player',
    '1' => 'Operator',
    '2' => 'Moderator',
    '3' => 'Administrator',
  ),

  'adm_usr_genre' => array(
    GENDER_UNKNOWN => 'Not set',
    GENDER_MALE => 'Male',
    GENDER_FEMALE => 'Female',
  ),

  'panel_mainttl' => 'Admin Panel',
  'adm_panel_mnu' => 'Search player',
  'adm_panel_ttl' => 'Type of search',
  'adm_search_pl' => 'Search by name',
  'adm_search_ip' => 'Search by IP',
  'adm_stat_play' => 'Player statistics',
  'adm_mod_level' => 'Access level',
  'adm_player_nm' => 'Player name',
  'adm_ip' => 'IP',
  'adm_plyer_wip' => 'Players with IP',
  'adm_frm1_id' => 'ID',
  'adm_frm1_name' => 'Name',
  'adm_frm1_ip' => 'IP',
  'adm_frm1_mail' => 'E-Mail',
  'adm_frm1_acc' => 'Rank',
  'adm_frm1_gen' => 'Gender',
  'adm_frm1_main' => 'Planet ID',
  'adm_frm1_gpos' => 'Coordinates',
  'adm_mess_lvl1' => 'Access level',
  'adm_mess_lvl2' => '&quot;now&quot; ',
  'adm_colony' => 'Colony',
  'adm_planet' => 'Planet',
  'adm_moon' => 'Moon',
  'adm_technos' => 'Technology',
  'adm_bt_search' => 'Search',
  'adm_bt_change' => 'Change',
  'flt_id' => 'ID',
  'flt_fleet' => 'Fleet',
  'flt_ships' => 'Composition',
  'flt_mission' => 'Mission',
  'flt_here' => 'Back',
  'flt_there' => 'There',
  'flt_here_there' => 'There/Back',
  'flt_departure' => 'Source',
  'flt_owner' => 'Owner',
  'flt_planet' => 'Planet',
  'flt_time_return' => 'Return',
  'flt_e_owner' => 'Destination',
  'flt_time_arrive' => 'Arrival',
  'flt_staying' => 'Stay',
  'flt_action' => 'Action',
  'flt_title' => 'Fleets in flight',
  'flt_no_fleet' => 'There are no fleets in flight',
  'mlst_title' => 'Message list',
  'mlst_mess_del' => 'Delete messages',
  'mlst_hdr_page' => 'Page.',
  'mlst_hdr_title' => ' ) Messages:',
  'mlst_hdr_prev' => '[ &lt;- ]',
  'mlst_hdr_next' => '[ -&gt; ]',
  'mlst_hdr_id' => 'ID',
  'mlst_hdr_type' => 'Type',
  'mlst_hdr_time' => 'Here',
  'mlst_hdr_from' => 'From',
  'mlst_hdr_to' => 'To',
  'mlst_hdr_text' => 'Text',
  'mlst_hdr_action' => 'Action.',
  'mlst_del_mess' => 'Delete',
  'mlst_bt_delsel' => 'Delete selected',
  'mlst_bt_deldate' => 'Delete',
  'mlst_hdr_delfrom' => 'Delete selected type messages before date',
  'mlst_no_messages' => 'No messages',
  'mlst_messages_deleted' => 'Deleted messages with ID(s) %s',
  'mlst_messages_deleted_date' => 'Deleted messages with type "%s" before date %s (does not includes messages on indicated date)',

  'adm_lng_title' => 'Localization',
  'adm_lng_warning' => 'WARNING! Locale editor is in alpha stage! Use it on your own risk!',
  'adm_lng_domain' => 'Domain',
  'adm_lng_string_name' => 'String name',
  'adm_lng_string_add' => 'Add string',
  'adm_uni_price_galaxy' => 'Base galactic rename cost',
  'adm_uni_price_system' => 'Base system rename cost',

  'adm_opt_ver_check' => 'Version check',
  'adm_opt_ver_check_hint' => 'Version check activated by admin by pressing button "Check version" below. This action is transferring only anonymous data: current DB version, release and full game version.',
  'adm_opt_ver_check_do' => 'Check version',
  'adm_opt_ver_check_last' => 'Last version check was performed at',
  'adm_opt_ver_check_auto' => 'Automatic version check',
  'adm_opt_ver_check_auto_hint' => 'You can enable automatic game version check. Data transferred to update server will be exact the same as when you use manual version check. But with automatic check engine will check version by itself once in period of time (by default - once per day). More info in documentation',

  'adm_opt_ver_response' => array(
    SNC_VER_NEVER => 'Version check was never performed',

    SNC_VER_ERROR_CONNECT => 'Version check error. Game can not communicate with update server. Be sure you do not have restriction in PHP to communicate with remote servers or that you have installed CURL and activated it on PHP',
    SNC_VER_ERROR_SERVER => 'Fatal update server error! Check - if there is newer version of game with advanced update server support. Otherwise immediatly contact developer to diagnose and fix this problem!',

    SNC_VER_EXACT => 'You have latest alpha version of incoming release. Thanks for participate in testing!',
    SNC_VER_LESS => 'You using alpha version of incoming release. However there is more recent alpha! You can update your game if you want to have fixes for errors in current version and participate in testing new features of game.',
    SNC_VER_FUTURE => 'You have game version from future! Immediatly contact develpoer and pass him this version! Also be prpeated to Time Militias visit for broking space-time continuum and violating laws of causality...',

    SNC_VER_RELEASE_EXACT => 'You have most recent version of latest release',
    SNC_VER_RELEASE_MINOR => 'Your game version is outdated - there is new version of your release. Most luckely it contains some bugfixes to current release. It is recomeded to update your game.',
    SNC_VER_RELEASE_MAJOR => 'You have very outdated game version - there is available new release. It is provide new features and bugfixes. Please update your game version!',
    SNC_VER_RELEASE_ALPHA => 'You have most recent version of latest release. However there is alpha version of next release. May be you would like to explore and test new features of upcoming release?',

    SNC_VER_MAINTENANCE => 'Update server currently turned off for maintenance. Please check again later',
    SNC_VER_UNKNOWN_RESPONSE => 'Update server gives unknown response. In most ases it means that there is new game version with more advanced update server support. Also it can mean error in update server. Please check and update your game or contact developer to diagnose and fix problem.',
    SNC_VER_INVALID => 'I just can not understand which version of game you have. Please contact developer to diagnose and fix this problem.',
    SNC_VER_STRANGE => 'You should not see this message. If you saw this message - something going terrible wrong. Please contact developer to diagnose and fix this problem.',

    SNC_VER_REGISTER_UNREGISTERED => 'Your server still not registered',
    SNC_VER_REGISTER_ERROR_MULTISERVER => 'Error - your server has multiply registration entries! Contact developer for diagnose and fix problem',
    SNC_VER_REGISTER_ERROR_REGISTERED => 'Error - your server already registered! Check your key and ID in server configuration.',
    SNC_VER_REGISTER_ERROR_NO_NAME => 'Error - no server name supplied! You should define your server name to register.',
    SNC_VER_REGISTER_ERROR_WRONG_URL => 'Error - wrong URL! Passed string is not a correct URL. If you tried to registered server from localhost you should be awared that update server did not work with local servers.',
    SNC_VER_REGISTER_REGISTERED => 'Your site sucesfully registered',

    SNC_VER_ERROR_INCOMPLETE_REQUEST => 'Error - missed correct ID or key in request! Check your key and ID in server configuration.',
    SNC_VER_ERROR_UNKNOWN_KEY => 'Error - unknown key! Passed key did not found in update server DB. Check your key in server configuration.',
    SNC_VER_ERROR_MISSMATCH_KEY_ID => 'Error - passed key did not match server ID! Check your key and ID in server configuration.',
  ),

  'adm_opt_ver_response_short' => array(
    SNC_VER_NEVER => 'Never made',

    SNC_VER_ERROR_CONNECT => 'Error connect',
    SNC_VER_ERROR_SERVER => 'Server error',

    SNC_VER_EXACT => 'Latest alpha',
    SNC_VER_LESS => 'Old alpha',
    SNC_VER_FUTURE => 'Future alpha',

    SNC_VER_RELEASE_EXACT => 'Freshest release',
    SNC_VER_RELEASE_MINOR => 'Update recomended',
    SNC_VER_RELEASE_MAJOR => 'Update mandatory',
    SNC_VER_RELEASE_ALPHA => 'Fresh release',

    SNC_VER_MAINTENANCE => 'Maintenance',
    SNC_VER_UNKNOWN_RESPONSE => 'Unknown response',
    SNC_VER_INVALID => 'Version error',
    SNC_VER_STRANGE => 'Unpredictable shit',

    SNC_VER_REGISTER_UNREGISTERED => 'Not registered',
    SNC_VER_REGISTER_ERROR_MULTISERVER => 'Multiregistration',
    SNC_VER_REGISTER_ERROR_REGISTERED => 'Key error',
    SNC_VER_REGISTER_ERROR_NO_NAME => 'Server name error',
    SNC_VER_REGISTER_REGISTERED => 'Registered',

    SNC_VER_ERROR_INCOMPLETE_REQUEST => 'ID or key error',
    SNC_VER_ERROR_UNKNOWN_KEY => 'Unknown key',
    SNC_VER_ERROR_MISSMATCH_KEY_ID => 'Key not match ID',
  ),

  'adm_upd_register' => 'Server registration',
  'adm_upd_register_hint' => '
    Server registration need for specials queries to update server. When you register there are passed required minimum for unique server identification:
    <ul>
      <li>Full server URL - i.e. http://myserver.com/myfolder/. It is necessary to distinguish several servers that shares same IP or domain</li>
      <li>Internal server name. Used in server messages.</li>
    </ul>
    Why would you like to register? In future there are many features planned wich would be acessed only to registered servers. Short list of planned features:
    <ul>
     <li>Automatic changelog refresh</li>
     <li>Automatic game update</li>
     <li>Listing in server ratings</li>
     <li>Bugreport and ticket system</li>
     <li>Chat for server admins</li>
     <li>Remote server diagnostic - at request</li>
     <li>...and many others</li>
    </ul>
    Why would you like to register NOW?
    <ul>
      <li>Requests of registered server administrators are prioritized for developers.</li>
      <li>Upon registration each server assigned uniq ID which will be used for basic server sorting. It means that servers with lesser ID (i.e. those who registered first) would be higher in server list...</li>
    </ul>
  ',
  'adm_upd_register_do' => 'Register server',
  'adm_upd_register_already' => 'You already registered your server. Write down server ID and key and store it in safe place!',
  'adm_upd_register_id' => 'Registration ID',
  'adm_upd_register_key' => 'Registration key',

  'adm_opt_stats_and_records' => 'Statistics and records',
  'adm_opt_stats_hide_admins' => 'Hide admins',
  'adm_opt_stats_hide_admins_detail' => 'Will be hidden all accounts with authlevel > 0',
  'adm_opt_stats_hide_player_list' => 'Hide players',
  'adm_opt_stats_hide_player_list_detail' => 'List of hidden players ID separated with comma',
  'adm_opt_stats_schedule' => 'Statistics update schedule',
  'adm_opt_stats_schedule_detail' => 'Format: "[YYYY:[MM:[DD:[HH:[MM:[SS]]]]]][,(...)]"<br />
    Zero left parameters is optional<br />
    Empty right parameters counts as zeros<br />
    Examples:<br />
     - "00:00:27:00" means "run every hour at 27 minutes";<br />
     - "04::" - "run at 4am every day";<br />
     - "02::,17:00" - "run at 2am every day AND each hour at 17 minutes";<br />
     - "1:4:30:00" - "run every 1st of each month at 04:30am" etc',
  'adm_opt_stats_hide_pm_link' => 'Hide PM links',

  'adm_pay' => 'Payments',
  'adm_pay_stats' => 'Payments Stats',
  'adm_pay_th_payer' => 'Payer',
  'adm_pay_th_payer_id' => 'ID',
  'adm_pay_th_payer_name' => 'Name',
  'adm_pay_th_payment' => 'Payment',
  'adm_pay_th_payment_id' => 'ID',
  'adm_pay_th_payment_date' => 'Date',
  'adm_pay_th_payment_status' => 'Status',
  'adm_pay_th_payment_amount' => 'Amount',
  'adm_pay_th_payment_currency' => 'Currency',
  'adm_pay_th_mm_paid' => 'Paid for',
  'adm_pay_th_mm_gained' => 'Gained',
  'adm_pay_th_module' => 'Payment system',
  'adm_pay_th_module_name' => 'Type',

  'adm_pay_filter_all' => '-- All --',
  'adm_pay_filter_status' => array(
    PAYMENT_STATUS_ALL => '-- All --',
    PAYMENT_STATUS_NONE => 'Not finished',
    PAYMENT_STATUS_COMPLETE => 'Finished',
  ),
  'adm_pay_filter_test' => array(
    PAYMENT_TEST_ALL => '-- All --',
    PAYMENT_TEST_REAL => 'Real',
    PAYMENT_TEST_PROBE => 'Test',
  ),
  'adm_pay_filter_stat' => array(
    PAYMENT_FILTER_STAT_NORMAL => '-- None --',
    PAYMENT_FILTER_STAT_MONTH => 'By months',
    PAYMENT_FILTER_STAT_YEAR => 'By years',
    PAYMENT_FILTER_STAT_ALL => 'All time',
  ),

  'adm_user_stat' => 'Статистика пользователей',
  'adm_user_online' => 'Онлайн с %s по %s',

  'adm_ban_unban' => 'Бан/Разбан',
  'adm_metametter_payment' => 'ММ & Платежи',

  'adm_stat_already_started' => 'Statistics already updated just now',

  'adm_dm_change_hint' => 'User IDs searched first. If not found - name search performed',

  'adm_matter_change_log_record' => 'Through admin interface for user {%1$d} %2$s by user {%3$s} %4$s reason "%5$s"',

  'adm_game_status' => 'Current game status',

  'adm_log_delete_update_info' => 'Delete info about maintenance and updates of stats, DB and engine',

  'admin_ptl_test_la_' => "Single'Double\"Zero\0End",

  'admin_title_access_denied' => 'Access denied',

  'adm_player' => 'Player',
  'adm_planets' => 'Planets',
));
