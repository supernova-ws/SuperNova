<?php
/*
#############################################################################
#  Filename: admin.mo
#  Create date: Wednesday, April 02, 2008	 19:18:25
#  Project: prethOgame
#  Description: RPG web based game
#
#  Copyright c 2008 Aleksandar Spasojevic <spalekg@gmail.com>
#  Copyright c 2005 - 2008 KGsystem
#  Copyright (c) 2009 Gorlum
#############################################################################
*/
if (!defined('INSIDE')) {
	die("Has attempted to break into!");
}
$lang['adm_done']               = "Successfully completed";
$lang['adm_inactive_removed']   = '<li>Remove inactive players: %d</li>';
$lang['adm_stat_title']         = "Update statistics";
$lang['adm_maintenance_title']  = "Database Services";
$lang['adm_records']            = "Processed records";
$lang['adm_cleaner_title']      = "Clean queue structures";
$lang['adm_cleaned']            = "Number of deleted tasks: ";

$lang['adm_schedule_none']      = "There are no tasks in the schedule for now";

$lang['Fix']                    = "Updated";
$lang['Welcome_to_Fix_section'] = "Section Patches";
$lang['There_is_not_need_fix']  = "Fix unnecessary!";
$lang['Fix_welldone']           = "Done!";

$lang['adm_ov_title'] = "Overview";
$lang['adm_ov_infos'] = "Information";
$lang['adm_ov_yourv'] = "The current version";
$lang['adm_ov_lastv'] = "Available version";
$lang['adm_ov_here']  = "Here";
$lang['adm_ov_onlin'] = "Online";
$lang['adm_ov_ally']  = "Alliance";
$lang['adm_ov_point'] = "Point";
$lang['adm_ov_activ'] = "Active";
$lang['adm_ov_count'] = "Online players";
$lang['adm_ov_wrtpm'] = "Write Private Message";
$lang['adm_ov_altpm'] = "[PM]";

$lang['adm_ul_title'] = "Players list";
$lang['adm_ul_ttle2'] = "Players listed";
$lang['adm_ul_id']    = "ID";
$lang['adm_ul_name']  = "Player name";
$lang['adm_ul_mail']  = "E-mail";
$lang['adm_ul_adip']  = "IP";
$lang['adm_ul_regd']  = "Registred from";
$lang['adm_ul_lconn'] = "Last login";
$lang['adm_ul_bana']  = "Ban";
$lang['adm_ul_detai'] = "Details";
$lang['adm_ul_actio'] = "Actions";
$lang['adm_ul_playe'] = " Players";
$lang['adm_ul_yes']   = "Yes";
$lang['adm_ul_no']    = "No";

$lang['adm_pl_title'] = "Active planet";
$lang['adm_pl_activ'] = "Active planet";
$lang['adm_pl_name']  = "The name of the planet";
$lang['adm_pl_posit'] = "Coordinates";
$lang['adm_pl_point'] = "Value";
$lang['adm_pl_since'] = "Is Active";
$lang['adm_pl_they']  = "Total";
$lang['adm_pl_apla']  = "Planet(s)";

$lang['adm_am_plid']  = "Planet ID";
$lang['adm_am_done']  = "Add was successful";
$lang['adm_am_ttle']  = "Add resources";
$lang['adm_am_add']   = "Confirm";
$lang['adm_am_form']  = "One-step add link form resources";

$lang['adm_bn_ttle']  = "Banned Players";
$lang['adm_bn_plto']  = "Banned Players";
$lang['adm_bn_name']  = "Player name";
$lang['adm_bn_reas']  = "Reason for ban";
$lang['adm_bn_isvc']  = "Vacation mode";
$lang['adm_bn_time']  = "Duration of Ban";
$lang['adm_bn_days']  = "Days";
$lang['adm_bn_hour']  = "Hours";
$lang['adm_bn_mins']  = "Mins";
$lang['adm_bn_secs']  = "Seconds";
$lang['adm_bn_bnbt']  = "The ban";
$lang['adm_bn_thpl']  = "Player";
$lang['adm_bn_isbn']  = "Successfully Banned!";
$lang['adm_bn_vctn']  = " Vacation mode.";
$lang['adm_bn_errr']  = "Error locking player! perhaps Name %s not found.";
$lang['adm_bn_err2']  = "Error disabing production on the planets!";
$lang['adm_bn_plnt']  = "Production on the planets is disabled.";

$lang['adm_unbn_ttle']  = "Unban";
$lang['adm_unbn_plto']  = "Unban player";
$lang['adm_unbn_name']  = "Name";
$lang['adm_unbn_bnbt']  = "Unban";
$lang['adm_unbn_thpl']  = "Player";
$lang['adm_unbn_isbn']  = "Unbanned!";

$lang['adm_rz_ttle']  = "Zeroing universe";
$lang['adm_rz_done']  = "User(s) of transfer(s)";
$lang['adm_rz_conf']  = "Confirmation";
$lang['adm_rz_text']  = "Clicking (reset) You delete all database. You did backup??? Accounts will not be removed...";
$lang['adm_rz_doit']  = "Zero out";

$lang['adm_ch_ttle']  = "Admin chat";
$lang['adm_ch_list']  = "Message list";
$lang['adm_ch_clear'] = "Clear";
$lang['adm_ch_idmsg'] = "ID";
$lang['adm_ch_delet'] = "Delete";
$lang['adm_ch_play']  = "Player";
$lang['adm_ch_time']  = "Date";
$lang['adm_ch_chat']  = "Chat";
$lang['adm_ch_nbs']   = "Total messages...";

$lang['adm_er_ttle']  = "Errors";
$lang['adm_er_list']  = "Error list in game";
$lang['adm_er_clear'] = "Clear";
$lang['adm_er_idmsg'] = "ID";
$lang['adm_er_type']  = "Type";
$lang['adm_er_play']  = "Player Id";
$lang['adm_er_time']  = "Date";
$lang['adm_er_page']  = "¿ddress of the page";
$lang['adm_er_nbs']   = "Total Errors...";
$lang['adm_er_text']  = "Error text";
$lang['adm_er_bktr']  = "Debugging information";

$lang['adm_dm_title'] = "Change the number of dark matter";
$lang['adm_dm_planet'] = "ID, Coordinates or name of the planet";
$lang['adm_dm_oruser'] = "Or";
$lang['adm_dm_user'] = "ID or username";
$lang['adm_dm_no_quant'] = 'Specify the number Dark Matter(positive-Negative-for charging, removal)';
$lang['adm_dm_no_dest'] = 'Specify the user or planet to edit Dark Matter';
$lang['adm_dm_add_err'] = 'It look like during charging Dark Matter Occured.';
$lang['adm_dm_user_none'] = 'Error: could not find user with ID or name %s';
$lang['adm_dm_user_added'] = 'Number of Dark Matter user: [%s] (ID: %d) has been successfully changed to %d Dark Matter.';
$lang['adm_dm_user_conflict'] = 'Error locating user: looks like the Database is the user and with the same name, and with the same ID';

$lang['adm_dm_planet_none'] = 'Error locating planet: Planet ID is not found, coordinates or name %s';
$lang['adm_dm_planet_added'] = 'The user ID number “Ã %1$d (owner of planet %4$s %2$s ID %3$d) successfully renamed to %5$d “Ã.';
$lang['adm_dm_planet_conflict'] = 'Non-unique data to search for the planet.<br>This means that the Database at the same time there is a ';
$lang['adm_dm_planet_conflict_id'] = 'Planet named "%1$s" and the planet with ID %1$s .<br>try using the coordinates of the planet.';
$lang['adm_dm_planet_conflict_name'] = 'Multiple planets named "%1$s".<br>try using coordinates or ID planet.';
$lang['adm_dm_planet_conflict_coords'] = 'Planet named "%1$s" and the planet coordinates %1$s.<br>try using the ID of the planet.';

$lang['adm_apply'] = "Apply";
$lang['adm_maint']    = "Servicing";
$lang['adm_backup']   = "Backup";

$lang['adm_tools']   = "Utilities";
$lang['adm_tools_reloadConfig'] = 'Recalculate configuration';

$lang['adm_reason']  = "The reason for";

$lang = array_merge($lang, array(
// Server settings page
  'adm_opt_title'             => "Configuration of the universe",
  'adm_opt_game_settings'     => "Universe Parameters",
  'adm_opt_game_name'         => "Universe name",
  
  'adm_opt_speed'             => "Speed",
  'adm_opt_game_gspeed'       => "Games",
  'adm_opt_game_fspeed'       => "Fleet",
  'adm_opt_game_pspeed'       => "Resource",

  'adm_opt_main_not_counted'  => "(Apart from home planet)",
  'adm_opt_game_speed_normal' => "(1&nbsp;-&nbsp;normal)",
  'adm_opt_game_forum'        => "Forum address",
  'adm_opt_game_dark_matter'  => "Reference &quot;Harvest Dark Matter&quot;",
  'adm_opt_game_copyrigh'     => "Copyright",
  'adm_opt_game_online'       => "Turn off the game. Users will see the following message:",
  'adm_opt_game_offreaso'     => "Turn off reason",
  'adm_opt_plan_settings'     => "Planet settings",
  'adm_opt_plan_initial'      => "Size of main planet",
  'adm_opt_plan_base_inc'     => "Basic production",
  'adm_opt_game_debugmod'     => "Enable debug mode",
  'adm_opt_game_oth_info'     => "Other options",
  'adm_opt_int_news_count'    => "news count",
  'adm_opt_int_page_imperor'  => 'On the page &quot;Emperor&quot;:',
  'adm_opt_game_zero_dsiable' => "(0&nbsp;-&nbsp;Disable)",
  
  'adm_opt_game_advertise'    => "Ad units",
  'adm_opt_game_oth_adds'     => "Enable the ad block in the left menu. Banner code:",

  'adm_opt_game_oth_gala'     => "Galaxy",
  'adm_opt_game_oth_syst'     => "System",
  'adm_opt_game_oth_plan'     => "Planet",
  'adm_opt_btn_save'          => "Save",
  'adm_opt_vacation_mode'     => "Turn off vacation",
  'adm_opt_sectors'           => "Fields",
  'adm_opt_per_hour'          => "per hour",
  'adm_opt_saved'             => "Game settings saved successfully",
  'adm_opt_players_online'    => "Players on the server",
  'adm_opt_vacation_mode_is'  => "Vacation mode",
  'adm_opt_maintenance'       => "Maintenance and debugging",
  'adm_opt_links'             => "Links and banners",
                                     
  'adm_opt_universe_size'     => "Universe size",
  'adm_opt_galaxies'          => "Galaxies",
  'adm_opt_systems'           => "Systems",
  'adm_opt_planets'           => "Planets",
  'adm_opt_build_on_research' => "Build on research",
  'adm_opt_game_rules'        => "Game rules",
  'adm_opt_max_colonies'      => "Number of colonies",
  'adm_opt_exchange'          => "Exchange resources",
  'adm_opt_game_mode'         => "Type of universe",

  'adm_opt_chat'              => "Chat settings",
  'adm_opt_chat_timeout'      => "Timeout for idle",


  'adm_opt_game_defaults'         => "Configuring default Game setting",
  'adm_opt_game_default_language' => "Default language",
  'adm_opt_game_default_skin'     => "Design/Skin",
  'adm_opt_game_default_template' => "Template",

  'adm_lm_compensate' => "Compensation",

// Planet compensate page
  'adm_pl_comp_title'   => 'Compensation for destroyed planet',
  'adm_pl_comp_src'     => 'Destroy the planet',
  'adm_pl_comp_dst'     => 'Creidt resources on the planet',
  'adm_pl_comp_bonus'   => 'Bonus player',
  'adm_pl_comp_check'   => 'Check',
  'adm_pl_comp_confirm' => 'Confirm',
  'adm_pl_comp_done'    => 'Finish',

  'adm_pl_comp_price'   => 'Cost structures',
  'adm_pl_comp_got'     => 'Be enrolled',

  'adm_pl_com_of_plr'   => 'Player',
  'adm_pl_comp_will_be' => 'will',
  'adm_pl_comp_destr'   => 'destroyed.',
  'adm_pl_comp_recieve' => 'The specified number of resources',
  'adm_pl_comp_recieve2' => 'enrolled on the planet',
  


  'adm_pl_comp_err_0' => 'Not found to be destroyed planet',
  'adm_pl_comp_err_1' => 'Planet destroyed',
  'adm_pl_comp_err_2' => 'Not found, the planet you want to enroll',
  'adm_pl_comp_err_3' => 'From the planets different owners. Credit resources can only be the same player on the planet',
  'adm_pl_comp_err_4' => 'Planet belongs to the specified player',
  'adm_pl_comp_err_5' => 'Planet for -- and for credit resources match',

  'adm_ver_versions'  => 'Version of server components',
  'adm_ver_version_sn'=> 'Version',
  'adm_ver_version_db'=> 'Database version',
));

// Add moon
$lang['addm_title']    = "Add Moon";
$lang['addm_addform']  = "Form new moon";
$lang['addm_playerid'] = "ID world accommodation";
$lang['addm_moonname'] = "The name of the Moon";
$lang['addm_moongala'] = "Specify the Galaxy";
$lang['addm_moonsyst'] = "Specify system";
$lang['addm_moonplan'] = "Specify position";
$lang['addm_moondoit'] = "Add";
$lang['addm_done']     = "The Moon formed";


//Admin panel
$lang['adm_usr_level'][0] = "Player";
$lang['adm_usr_level'][1] = "Operator";
$lang['adm_usr_level'][2] = "Moderator";
$lang['adm_usr_level'][3] = "Administrator";
$lang['adm_usr_genre']['M'] = "Male";
$lang['adm_usr_genre']['F'] = "Female";

// Admin Strings
$lang['panel_mainttl'] = "Admin Panel";
// Admin Panel A Template 1
$lang['adm_panel_mnu'] = "Search player";
$lang['adm_panel_ttl'] = "Type of search";
$lang['adm_search_pl'] = "Search by name";
$lang['adm_search_ip'] = "Search by IP";
$lang['adm_stat_play'] = "Player statistics";
$lang['adm_mod_level'] = "Access level";

$lang['adm_player_nm'] = "Player name";
$lang['adm_ip']        = "IP";
$lang['adm_plyer_wip'] = "Players with IP";
$lang['adm_frm1_id']   = "ID";
$lang['adm_frm1_name'] = "Name";
$lang['adm_frm1_ip']   = "IP";
$lang['adm_frm1_mail'] = "e-Mail";
$lang['adm_frm1_acc']  = "Rank";
$lang['adm_frm1_gen']  = "Gender";
$lang['adm_frm1_main'] = "Planet ID";
$lang['adm_frm1_gpos'] = "Coordinates";
$lang['adm_mess_lvl1'] = "Access level";
$lang['adm_mess_lvl2'] = "&quot;now&quot; ";
$lang['adm_colony']    = "Colony";
$lang['adm_planet']    = "Planet";
$lang['adm_moon']      = "Moon";
$lang['adm_technos']   = "Technology";
$lang['adm_bt_search'] = "Search";
$lang['adm_bt_change'] = "Change";

// Admin fleet
$lang['flt_id']       = "ID";
$lang['flt_fleet']    = "Fleet";
$lang['flt_mission']  = "Mission";
$lang['flt_owner']    = "Owner";
$lang['flt_planet']   = "Planet";
$lang['flt_time_st']  = "Departure time";
$lang['flt_e_owner']  = "Arrival";
$lang['flt_time_en']  = "Time of arrival";
$lang['flt_staying']  = "Stat.";
$lang['flt_action']   = "Action";
$lang['flt_title']    = "Fleets in flight";

// MD5
$lang['adm_md5']    = "MD5-hash";
$lang['md5_title']  = "Encryption utility";
$lang['md5_pswcyp'] = "Password encryption";
$lang['md5_psw']    = "Password";
$lang['md5_pswenc'] = "Encrypted password";
$lang['md5_doit']   = "[ encrypt ]";

// Message list
$lang['mlst_title']       = "Message list";
$lang['mlst_mess_del']    = "Delete messages";
$lang['mlst_hdr_page']    = "Page.";
$lang['mlst_hdr_title']   = " ) messages :";
$lang['mlst_hdr_prev']    = "[ &lt;- ]";
$lang['mlst_hdr_next']    = "[ -&gt; ]";
$lang['mlst_hdr_id']      = "ID";
$lang['mlst_hdr_type']    = "Type";
$lang['mlst_hdr_time']    = "Here";
$lang['mlst_hdr_from']    = "From";
$lang['mlst_hdr_to']      = "To";
$lang['mlst_hdr_text']    = "text";
$lang['mlst_hdr_action']  = "Action.";
$lang['mlst_del_mess']    = "Delete";
$lang['mlst_bt_delsel']   = "Delete selected";
$lang['mlst_bt_deldate']  = "Delete message date";
$lang['mlst_hdr_delfrom'] = "Remove the date";
$lang['mlst_mess_typ__0'] = "Espionage";
$lang['mlst_mess_typ__1'] = "Players";
$lang['mlst_mess_typ__2'] = "Alliances";
$lang['mlst_mess_typ__3'] = "Fights";
$lang['mlst_mess_typ__4'] = "Operational.";
$lang['mlst_mess_typ__5'] = "Transport";
$lang['mlst_mess_typ_15'] = "Expedition";
$lang['mlst_mess_typ_99'] = "List Batiment";

?>
