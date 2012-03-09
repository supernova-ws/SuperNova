<?php

/*
#############################################################################
#  Filename: system.mo
#  Project: SuperNova.WS
#  Website: http://www.supernova.ws
#  Description: Massive Multiplayer Online Browser Space Startegy Game
#
#  Copyright © 2011 madmax1991 for Project "SuperNova.WS"
#  Copyright © 2009 Gorlum for Project "SuperNova.WS"
#  Copyright © 2008 Aleksandar Spasojevic <spalekg@gmail.com>
#  Copyright © 2005 - 2008 KGsystem
#############################################################################
*/

/**
*
* @package language
* @system [English]
* @version 31a9
*
*/

/**
* DO NOT CHANGE
*/

if (!defined('INSIDE')) 
{
	die('Hack attempt!');
}

if (empty($lang) || !is_array($lang))
{
  $lang = array();
}

// System-wide localization

$lang = array_merge($lang, array(
  'adm_err_denied' => 'Access denied. You do not have enough rights to use this admin page',

  'sys_empire'          => 'Empire',
  'VacationMode'			=> "Your production stopped because you are on vacation",
  'sys_moon_destruction_report' => "Report of destruction of the Moon",
  'sys_moon_destroyed' => "Your Deathstar shot a powerful gravitational wave, which destroyed the Moon! ",
  'sys_rips_destroyed' => "Your Deathstar shot a  powerful gravitational wave, but it had not enough power to destroy the Moon due to its size. But the gravitational wave reflected from the lunar surface and ruined your fleet.",
  'sys_rips_come_back' => "Your Deathstar did not have enough power to defeat this moon. Your fleet is not destroying the Moon.",
  'sys_chance_moon_destroy' => "Chance of Moon destruction: ",
  'sys_chance_rips_destroy' => "Modify burst destruction: ",

  'sys_impersonate' => 'Impersonate',
  'sys_impersonate_done' => 'Unimpersonate',
  'sys_impersonated_as' => 'WARNING! Currently you impersonating player %1$s. Don\'t forget that you are really %2$s! To unimpersonate select appropriate menu item.',

  'sys_day' => "Days",
  'sys_hrs' => "Hours",
  'sys_min' => "Minutes",
  'sys_sec' => "Seconds",
  'sys_day_short' => "D",
  'sys_hrs_short' => "H",
  'sys_min_short' => "M",
  'sys_sec_short' => "S",

  'sys_ask_admin' => 'Questions and suggestions sent to',

  'sys_wait' => 'The query is executed. Please wait.',

  'sys_fleets'       => 'Fleets',
  'sys_expeditions'  => 'Expeditions',
  'sys_fleet'        => 'fleet',
  'sys_expedition'   => 'expedition',
  'sys_event_next'   => 'Next event:',
  'sys_event_arrive' => 'will arrive',
  'sys_event_stay'   => 'will end task',
  'sys_event_return' => 'will return',

  'sys_total'           => "Total",
  'sys_need'				=> 'Need',
  'sys_register_date'   => 'Registration date',

  'sys_attacker' 		=> "Attacker",
  'sys_defender' 		=> "Defender",

  'COE_combatSimulator' => "Battle simulator",
  'COE_simulate'        => "Run the Simulator",
  'COE_fleet'           => "Fleet",
  'COE_defense'         => "Defence",
  'sys_coe_combat_start'=> "Combat begins",
  'sys_coe_combat_end'  => "Combat outcome",
  'sys_coe_round'       => "Round",

  'sys_coe_attacker_turn'=> 'Attacker make shots for %1$s. Defender\'s shield absorbs %2$s<br />',
  'sys_coe_defender_turn'=> 'Defender make shots for %1$s. Attacker\'s shield absorbs %2$s<br /><br /><br />',
  'sys_coe_outcome_win'  => 'Defender wons combat!<br />',
  'sys_coe_outcome_loss' => 'Attacker wons combat!<br />',
  'sys_coe_outcome_loot' => 'He\'s lootin %1$s metal, %2$s crystal, %3$s deuterium<br />',
  'sys_coe_outcome_draw' => 'Combat end with draw...<br />',
  'sys_coe_attacker_lost'=> 'Attacker lost %1$s units<br />',
  'sys_coe_defender_lost'=> 'Defender lost %1$s units<br />',
  'sys_coe_debris_left'  => 'There is %1$s metal and %2$s crystal floating in debris around planet.<br /><br />',
  'sys_coe_moon_chance'  => 'Moon creation chance is %1$s%%<br />',
  'sys_coe_rw_time'      => 'Reprot generated in %1$s seconds<br />',

  'sys_resources'       => "Resources",
  'sys_ships'           => "Ships",
  'sys_metal'          => "Metal",
  'sys_metal_sh'       => "M",
  'sys_crystal'        => "Crystal",
  'sys_crystal_sh'     => "C",
  'sys_deuterium'      => "Deuterium",
  'sys_deuterium_sh'   => "D",
  'sys_energy'         => "Energy",
  'sys_energy_sh'      => "E",
  'sys_dark_matter'    => "Dark Matter",
  'sys_dark_matter_sh' => "DM",

  'sys_reset'           => "Reset",
  'sys_send'            => "Send",
  'sys_characters'      => "Characters",
  'sys_back'            => "Back",
  'sys_return'          => "Return",
  'sys_delete'          => "Delete",
  'sys_writeMessage'    => "Write a message",
  'sys_hint'            => "Tip",

  'sys_alliance'        => "Alliance",
  'sys_player'          => "Player",
  'sys_coordinates'     => "Coordinates",

  'sys_online'          => "Online",
  'sys_offline'         => "Offline",
  'sys_status'          => "Status",

  'sys_universe'        => "Universe",
  'sys_goto'            => "Go",

  'sys_time'            => "Time",
  'sys_temperature'     => 'Temperature',

  'sys_no_task'         => "No task",

  'sys_affilates'       => "Invited players",

  'sys_fleet_arrived'   => "Fleet arrived",

  'sys_planet_type' => array(
    PT_PLANET => 'Planet', 
    PT_DEBRIS => 'Debris Field', 
    PT_MOON   => 'Moon',
  ),

  'sys_planet_type_sh' => array(
    PT_PLANET => '(P)', 
    PT_DEBRIS => '(D)', 
    PT_MOON   => '(M)',
  ),

  'sys_capacity' 			=> 'Load Capacity',
  'sys_cargo_bays' 			=> 'Holds',

  'sys_supernova' 			=> 'Supernova',
  'sys_server' 			=> 'Server',

  'sys_unbanned'			=> 'Unbanned',

  'sys_date_time'			=> 'Date and time',
  'sys_from_person'	   => 'From',
  'sys_from_speed'	   => 'from',

  'sys_from'		  => 'from',

// Resource page
  'res_planet_production' => 'Planet Production',
  'res_basic_income' => 'Basic Income',
  'res_total' => 'Total',
  'res_calculate' => 'Calculate',
  'res_hourly' => 'Hourly',
  'res_daily' => 'Daily',
  'res_weekly' => 'Weekly',
  'res_monthly' => 'Monthly',
  'res_storage_fill' => 'Storage occupancy',
  'res_hint' => '<ul><li>Production resources <100% means a shortage of energy. Build more power stations or reduce production resources<li>If your production is 0% likely you came from vacation mode and you want to include all plants<li>What would make the extraction for all plants immediately use the drop-down in the resource table. Especially convenient to use it after the vacation mode</ul>',

// Build page
  'bld_destroy' => 'Destroy',
  'bld_create'  => 'Build',

// Imperium page
  'imp_imperator' => "Emperor",
  'imp_overview' => "Empire Overview",
  'imp_fleets' => "Fleets in flight",
  'imp_production' => "Production",
  'imp_name' => "Name",
  'imp_research' => "Research",
  'sys_fields' => "Fields",

// Cookies
  'err_cookie' => "Error! You cannot authenticate the user on information in a cookie.<br />Clear cookies in you browser then <a href='login." . PHP_EX . "'>log in</a> in a game or <a href='reg." . PHP_EX . "'>register new account again</a>.",

// Supported languages
  'ru'              	  => 'Russian',
  'en'              	  => 'English',

  'sys_vacation'        => 'Your are on vacation until',
  'sys_vacation_leave'  => 'I have got rest - break holiday!',
  'sys_level'           => 'Level',
  'sys_level_short'     => 'Lvl',

  'sys_yes'             => 'Yes',
  'sys_no'              => 'No',

  'sys_on'              => 'Enable',
  'sys_off'             => 'Disable',

  'sys_confirm'         => 'Confirm',
  'sys_save'            => 'Save',
  'sys_create'          => 'Create',
  'sys_write_message'   => 'Write a message',

// top bar
  'top_of_year' => 'Year',
  'top_online'			=> 'Players online',
  
  'sys_first_round_crash_1'	=> 'Contact with the affected fleet lost.',
  'sys_first_round_crash_2'	=> 'This means that it was destroyed in the first round of the battle.',

  'sys_ques' => array(
    QUE_STRUCTURES => 'Building',
    QUE_HANGAR     => 'Shipyard',
    QUE_RESEARCH   => 'Research',
  ),

  'eco_que'       => 'Queue',
  'eco_que_empty' => 'Queue is empty',
  'eco_que_clear' => 'Clear queue',
  'eco_que_trim'  => 'Undo last queue',

  'sys_overview'			=> 'Overview',
  'mod_marchand'			=> 'Trader',
  'sys_galaxy'			=> 'Galaxy',
  'sys_system'			=> 'System',
  'sys_planet'			=> 'Planet',
  'sys_planet_title'			=> 'Planet Type',
  'sys_planet_title_short'			=> 'Type',
  'sys_moon'			=> 'Moon',
  'sys_error'			=> 'Error',
  'sys_done'				=> 'Finish',
  'sys_no_vars'			=> 'Initialization of variables, see the Administration!',
  'sys_attacker_lostunits'		=> 'Attacker lost %s units.',
  'sys_defender_lostunits'		=> 'Defender lost %s units.',
  'sys_gcdrunits' 			=> 'Now at these coordinates are %s %s and %s %s.',
  'sys_moonproba' 			=> 'Chance of Moon is: %d %% ',
  'sys_moonbuilt' 			=> 'Thanks to the huge energy huge chunks of metal and Crystal are joined together and formed new moon %s %s!',
  'sys_attack_title'    		=> '%s. Battle occurred between the following fleets::',
  'sys_attack_attacker_pos'      	=> 'Attacker %s [%s:%s:%s]',
  'sys_attack_techologies' 	=> 'Weapons: %d %% Shields: %d %% Armor: %d %% ',
  'sys_attack_defender_pos' 	=> 'Defender %s [%s:%s:%s]',
  'sys_ship_type' 			=> 'Type',
  'sys_ship_count' 		=> 'Count',
  'sys_ship_weapon' 		=> 'Weapon',
  'sys_ship_shield' 		=> 'Shield',
  'sys_ship_armour' 		=> 'Armor',
  'sys_destroyed' 			=> 'destroyed',
  'sys_attack_attack_wave' 	=> 'The Attacker is doing shots with a total capacity of %s on the defender. Shields absorb %s of the shots.',
  'sys_attack_defend_wave'		=> 'The Defender is doing shots with a total capacity of %s on the attacker. Shields absorb %s of the shots.',
  'sys_attacker_won' 		=> 'The Attacker won the battle!',
  'sys_defender_won' 		=> 'The Defender won the battle!',
  'sys_both_won' 			=> 'The battle ended in a draw!',
  'sys_stealed_ressources' 	=> 'The Attacker gets %s Metal %s %s Crystal %s and %s Deuterium.',
  'sys_rapport_build_time' 	=> 'Report generation time %s seconds',
  'sys_mess_tower' 		=> 'Transport',
  'sys_coe_lost_contact' 		=> 'You lost contact with your fleet',
  'sys_mess_attack_report' 	=> 'Battle Report',
  'sys_spy_maretials' 		=> 'Raw material',
  'sys_spy_fleet' 			=> 'Fleet',
  'sys_spy_defenses' 		=> 'Defence',
  'sys_mess_qg' 			=> 'Fleet command',
  'sys_mess_spy_report' 		=> 'Spy Report',
  'sys_mess_spy_lostproba' 	=> 'Accuracy of information received by the Spy probe %d %% ',
  'sys_mess_spy_detect_chance' 	=> 'Detection chance %d%%',
  'sys_mess_spy_control' 		=> 'Counter-intelligence',
  'sys_mess_spy_activity' 		=> 'Spy activity',
  'sys_mess_spy_ennemyfleet' 	=> 'Alien fleet with planet',
  'sys_mess_spy_seen_at'		=> 'was discovered near the planet',
  'sys_mess_spy_destroyed'		=> 'Spy fleet was destroyed',
  'sys_mess_spy_destroyed_enemy'		=> 'Enemy spy fleet was destroyed',
  'sys_object_arrival'		=> 'Arrived on the planet',
  'sys_stay_mess_stay' => 'Leave Fleet',
  'sys_stay_mess_start' 		=> 'Your fleet arrived at the planet',
  'sys_stay_mess_back'		=> 'Your fleet is back ',
  'sys_stay_mess_end'		=> ' and delivered:',
  'sys_stay_mess_bend'		=> ' and delivered the following resources:',
  'sys_adress_planet' 		=> '[%s:%s:%s]',
  'sys_stay_mess_goods' 		=> '%s : %s, %s : %s, %s : %s',
  'sys_colo_mess_from' 		=> 'Colonization',
  'sys_colo_mess_report' 		=> 'Report about colonization',
  'sys_colo_defaultname' 		=> 'Colony',
  'sys_colo_arrival' 		=> 'The fleet reaches the coordinates ',
  'sys_colo_maxcolo' 		=> ', but you cannot colonize the planet has reached the maximum number of colonies for your level of colonization',
  'sys_colo_allisok' 		=> ', and colonists are beginning to a new planet.',
  'sys_colo_badpos'  			=> ', and the colonists found little benefit for the environment of your Empire. The mission colonization back to planet submit.',
  'sys_colo_notfree' 			=> ', the colonists did not find the planet in these coordinates. They have to pave the way back completely discouraged.',
  'sys_colo_no_colonizer'     => 'In the fleet not colonizer',
  'sys_colo_planet'  		=> ' Planet colonized by!',
  'sys_expe_report' 		=> 'Expedition Report',
  'sys_recy_report' 		=> 'Recycler information',
  'sys_expe_blackholl_1' 		=> 'Your fleet hit the black hole and you lost part of your fleet!',
  'sys_expe_blackholl_2' 		=> 'Your fleet hit the black hole and your fleet was completely sucked in!',
  'sys_expe_nothing_1' 		=> 'Your researchers witnessed a Supernova! And your drives are able to take part of the absorption of energy.',
  'sys_expe_nothing_2' 		=> 'Your researchers found nothing!',
  'sys_expe_found_goods' 		=> 'Your researchers found a planet rich in raw materials!<br>You got %s %s, %s %s and %s %s',
  'sys_expe_found_ships' 		=> 'Your researchers found flawlessly new fleet!<br>You got: ',
  'sys_expe_back_home' 		=> 'Your fleet is back.',
  'sys_mess_transport' 		=> 'Transport',
  'sys_tran_mess_owner' 		=> 'One of your fleet reaches the planet %s %s and delivers %s %s, %s  %s and %s %s.',
  'sys_tran_mess_user'  		=> 'Your fleet sent to the planet %s %s arrived at %s %s and delivered %s %s, %s  %s and %s %s.',
  'sys_mess_fleetback' 		=> 'Return',
  'sys_tran_mess_back' 		=> 'One of your fleet returned to planet %s %s.',
  'sys_recy_gotten' 		=> 'One of your fleets, Nancy a %s %s and %s %s Return to planet.',
  'sys_notenough_money' 		=> 'You do not have enough resources to build: %s. You now: %s %s , %s %s and %s %s. For construction: %s %s , %s %s and %s %s.',
  'sys_nomore_level'		=> 'You no longer can improve it. It reached Max. level ( %s ).',
  'sys_buildlist' 			=> 'Building list',
  'sys_buildlist_fail' 		=> 'no buildings',
  'sys_gain' 			=> 'Extraction: ',
  'sys_perte_attaquant' 		=> 'The Attacker lost',
  'sys_perte_defenseur' 		=> 'The Defender lost',
  'sys_debris' 			=> 'Debris: ',
  'sys_noaccess' 			=> 'Access Denied',
  'sys_noalloaw' 			=> 'You have access to this zone!',
  'sys_governor'        => 'Governor',

  // News page & a bit of imperator page
  'news_title'      => 'News',
  'news_none'       => 'No news',
  'news_new'        => 'New',
  'news_future'     => 'Announcement',
  'news_more'       => 'Read More...',
                    
  'news_date'       => 'Date',
  'news_announce'   => 'Table of Contents',
  'news_detail_url' => 'Link to more info',
  'news_mass_mail'  => 'Send news to all players',
                    
  'news_total'      => 'Total news: ',
                    
  'news_add'        => 'Submit news',
  'news_edit'       => 'Edit news',
  'news_copy'       => 'Copy the news',
  'news_mode_new'   => 'New',
  'news_mode_edit'  => 'Editing',
  'news_mode_copy'  => 'Copying',

  'sys_administration' => 'Server Administration',

  // Shortcuts
  'shortcut_title'     => 'Shortcuts',
  'shortcut_none'      => 'No shortcuts',
  'shortcut_new'       => 'NEW',
  'shortcut_text'      => 'Text',

  'shortcut_add'       => 'Add shortcut',
  'shortcut_edit'      => 'Edit shortcut',
  'shortcut_copy'      => 'Copy shortcut',
  'shortcut_mode_new'  => 'New',
  'shortcut_mode_edit' => 'Editing',
  'shortcut_mode_copy' => 'Copying',

  // Missile-related
  'mip_h_launched'			=> 'Launch of interplanetary missiles',
  'mip_launched'				=> 'Launching interplanetary missiles: <b>%s</b>!',

  'mip_no_silo'				=> 'Insufficient level of silos on the planet <b>%s</b>.',
  'mip_no_impulse'			=> 'You want to investigate pulse motor.',
  'mip_too_far'				=> 'Rocket cannot fly that far.',
  'mip_planet_error'			=> 'Error - more than one planet one coordinate',
  'mip_no_rocket'				=> 'Not enough missiles in the shaft to carry out the attack.',
  'mip_hack_attempt'			=> ' You an hacker? Another joke and you will be banned. IP address and login Is recorded.',

  'mip_all_destroyed' 		=> 'All interplanetary missiles were destroyed missile intercepted<br>',
  'mip_destroyed'				=> '%s interplanetary missiles were destroyed by intercept missiles.<br>',
  'mip_defense_destroyed'	=> 'Destroyed following defences:<br />',
  'mip_recycled'				=> 'Repaired from the debris of defence equipment: ',
  'mip_no_defense'			=> 'On an affected planet protection!',

  'mip_sender_amd'			=> 'Rocket and space forces',
  'mip_subject_amd'			=> 'Missile attack',
  'mip_body_attack'			=> 'Attack of the interplanetary missiles (%1$s PCs.) with the planet %2$s <a href="galaxy.php?mode=3&galaxy=%3$d&system=%4$d&planet=%5$d">[%3$d:%4$d:%5$d]</a> on the planet %6$s <a href="galaxy.php?mode=3&galaxy=%7$d&system=%8$d&planet=%9$d">[%7$d:%8$d:%9$d]</a><br><br>',
  
  // Misc
  'sys_game_rules' => 'Rules of the game',
  'sys_max' => 'Max',
  'sys_banned_msg' => 'You are banned. For more information please visit <a href="banned.php">here</a>. Time of account ban: ',
  'sys_total_time' => 'Total time',

  // Univers
  'uni_moon_of_planet' => 'planet',

  // Combat reports
  'cr_view_title'  => "View Combat Reports",
  'cr_view_button' => "View Report",
  'cr_view_prompt' => "Enter the code",
  'cr_view_my'     => "My Combat Records",
  'cr_view_hint'   => "This page allows you to view shared Combat Reports. All Combat Reports will have a code at the bottom. To share a Combat Report simply give them that code. Then they can enter it here and view your Combat Report.",

  // Dark Matter
  'sys_dark_matter_text' => '<h2>What is Dark Matter?</h2>
    Dark Matter - it is currency, which in the game you can make a variety of operations:
    <ul><li>Swapping one resource for another</li>
    <li>Call charge fleet</li>
    <li>Call seller of used vehicles</li>
    <li>Hiring officers</li></ul>
    <h2>Where to get Dark Matter?</h2>
    You get Dark Matter during the game: gaining experience for raids on other planets and construction of buildings.
    Also sometimes research cruises can bring Dark Matter.',
  'sys_dark_matter_purchase' => 'In addition you can purchase Dark Matter for WebMoney.',
  'sys_dark_matter_get'  => 'Click here to read details.',

  // Fleet
  'flt_gather_all'    => 'Gather resources',
  
  // Ban system
  'ban_title'      => 'Black list',
  'ban_name'       => 'Name',
  'ban_reason'     => 'The reason for the ban',
  'ban_from'       => 'Ban data',
  'ban_to'         => 'Term of Ban',
  'ban_by'         => 'Issued',
  'ban_no'         => 'No Banned players',
  'ban_thereare'   => 'Total',
  'ban_players'    => 'Banned',
  'ban_banned'     => 'Players banned: ',

  // Contacts
  'ctc_title' => 'Administration',
  'ctc_intro' => 'Here you will find the addresses of all administrators and operators of the games for feedback',
  'ctc_name'  => 'Name',
  'ctc_rank'  => 'Rank',
  'ctc_mail'  => 'E-Mail',

  // Records page
  'rec_title'  => 'Universe Records',
  'rec_build'  => 'Building',
  'rec_specb'  => 'Special Building',
  'rec_playe'  => 'Player',
  'rec_defes'  => 'Defence',
  'rec_fleet'  => 'Fleet',
  'rec_techn'  => 'Technology',
  'rec_level'  => 'Level',
  'rec_nbre'   => 'Number',
  'rec_rien'   => '-',

  // Credits page
  'cred_link'    => 'Internet',
  'cred_site'    => 'Site',
  'cred_forum'   => 'Forum',
  'cred_credit'  => 'Authors',
  'cred_creat'   => 'Director',
  'cred_prog'    => 'Programmer',
  'cred_master'  => 'Moderator',
  'cred_design'  => 'Designerр',
  'cred_web'     => 'Webmaster',
  'cred_thx'     => 'Thanks',
  'cred_based'   => 'Basis for establishing XNova',
  'cred_start'   => 'Place debut XNova',

  // Built-in chat
  'chat_common'  => 'Common chat',
  'chat_ally'    => 'Ally chat',
  'chat_history' => 'History',
  'chat_message' => 'Message',
  'chat_send'    => 'Send',
  'chat_page'    => 'Page',
  'chat_timeout' => 'Chat is disabled from your inactivity. Refresh the page.',

  // ----------------------------------------------------------------------------------------------------------
  // Interface of Jump Gate
  'gate_start_moon' => 'Home Moon',
  'gate_dest_moon'  => 'Destination Moon',
  'gate_use_gate'   => 'Use Gate',
  'gate_ship_sel'   => 'Select ships',
  'gate_ship_dispo' => 'photos',
  'gate_jump_btn'   => 'jump!!',
  'gate_jump_done'  => 'Gates are in the process of reloading!<br>Gates will be ready for use through: ',
  'gate_wait_dest'  => 'points of destination Gate is in preparations! gate will be ready for use: ',
  'gate_no_dest_g'  => 'The ultimate destination did not open the gate to move the fleet',
  'gate_no_src_ga'  => 'There is no gates on current moon',
  'gate_wait_star'  => 'Gates are in the process of reloading!<br>Gates will be ready for use: ',
  'gate_wait_data'  => 'error, no data to make jump!',
  'gate_vacation'   => 'Error, you cannot leap because you are in Vacation Mode!',
  'gate_ready'      => 'Gate ready to jump',

  // quests
  'qst_quests'               => 'Quests',
  'qst_msg_complete_subject' => 'You completed quest!',
  'qst_msg_complete_body'    => 'You completed quest "%s".',
  'qst_msg_your_reward'      => 'Your reward: ',

  // Messages
  'msg_from_admin' => 'Universe Administration',
  'msg_class' => array(
    MSG_TYPE_OUTBOX => 'Sent messages',
    MSG_TYPE_SPY => 'Spy reports',
    MSG_TYPE_PLAYER => 'Message by players',
    MSG_TYPE_ALLIANCE => 'Alliance Communications',
    MSG_TYPE_COMBAT => 'Military reports',
    MSG_TYPE_RECYCLE => 'Records processing',
    MSG_TYPE_TRANSPORT => 'The arrival of the fleet',
    MSG_TYPE_ADMIN => 'Administrative messages',
    MSG_TYPE_EXPLORE => 'Reports for expeditions',
    MSG_TYPE_QUE => 'Message queue structures',
    MSG_TYPE_NEW => 'All messages',
  ),

  'msg_que_research_from'    => 'Scientists',
  'msg_que_research_subject' => 'Scientific discovery',
  'msg_que_research_message' => 'New technology "%s" level %d was discovered',

  'msg_que_planet_from'    => 'Governor',

  'msg_que_hangar_subject' => 'Building on hangar complete',
  'msg_que_hangar_message' => "Hangar on %s complete his work",

  'msg_que_built_subject'   => 'Planetary build work complete',
  'msg_que_built_message'   => "Building of '%2\$s' on %1\$s complete. Levels built: %3\$d",
  'msg_que_destroy_message' => "Demolition of '%2\$s' on %1\$s complete. Levels demolished: %3\$d",

  'msg_personal_messages' => 'Personal Messages',

  'sys_opt_bash_info'    => 'Antibashing settings',
  'sys_opt_bash_attacks' => 'Attacks per wave',
  'sys_opt_bash_interval' => 'Interval between waves',
  'sys_opt_bash_scope' => 'Bashing calculate period',
  'sys_opt_bash_war_delay' => 'Moratory after declaring war',
  'sys_opt_bash_waves' => 'Waves per period',
  'sys_opt_bash_disabled'    => 'Antibashing system disabled',

  'sys_id' => 'ID',
  'sys_identifier' => 'Identifier',

  'sys_email'   => 'E-Mail',
  'sys_ip' => 'IP',

  'sys_max' => 'Max',
  'sys_maximum' => 'Maximum',
  'sys_maximum_level' => 'Max level',

  'sys_user_name' => 'User name',
  'sys_player_name' => 'Player name',
  'sys_user_name_short' => 'Name',

  'sys_planets' => 'Planets',
  'sys_moons' => 'Moons',

  'sys_no_governor' => 'Hire governor',

  'sys_quantity' => 'Quantity',
  'sys_quantity_maximum' => 'Maximum quantity',
  'sys_qty' => 'Qty',

  'sys_buy_for' => 'Buy for',

  'sys_eco_lack_dark_matter' => 'Not enough Dark Matter',

  // Arrays
  'sys_build_result' => array(
    BUILD_ALLOWED => 'Can be built',
    BUILD_REQUIRE_NOT_MEET => 'Requirements not met',
    BUILD_AMOUNT_WRONG => 'Too much',
    BUILD_QUE_WRONG => 'Queue not exists',
    BUILD_QUE_UNIT_WRONG => 'Wrong queue',
    BUILD_INDESTRUCTABLE => 'Can not be destroyed',
    BUILD_NO_RESOURCES => 'Not enough resources',
    BUILD_NO_UNITS => 'No units',
  ),

  'sys_game_mode' => array(
    GAME_SUPERNOVA => 'SuperNova',
    GAME_OGAME     => 'oGame',
  ),

  'months' => array(
    '01'=>'January',
    '02'=>'February',
    '03'=>'March',
    '04'=>'April',
    '05'=>'May',
    '06'=>'June',
    '07'=>'July',
    '08'=>'August',
    '09'=>'September',
    '10'=>'October',
    '11'=>'November',
    '12'=>'December'
  ),

  'weekdays' => array(
    0 => 'Sunday',
    1 => 'Monday',
    2 => 'Tuesday',
    3 => 'Wednesday',
    4 => 'Thursday',
    5 => 'Friday',
    6 => 'Saturday'
  ),

  'user_level' => array(
    0 => 'Player',
    1 => 'Moderator',
    2 => 'Operator',
    3 => 'Administrator',
  ),

  'user_level_shortcut' => array(
    0 => 'P',
    1 => 'M',
    2 => 'O',
    3 => 'A',
  ),

  'sys_lessThen15min'   => '&lt; 15 min',

  'sys_no_points'        => 'You do not have enough Dark Matter!',
  'sys_dark_matter_desc' => 'Dark matter - using the standard methods of  fabric, which accounts for 23% mass of the universe. From there you can obtain an incredible amount of energy. Because of this, and because of the complexities associated with its extraction, Dark Matter is valued very highly.',
  'sys_dark_matter_hint' => 'With the help of this substance you can hire officers and commanders.',

  'sys_msg_err_update_dm' => 'Error updating DM quantity!',

  'sys_na' => 'Not available',
  'sys_na_short' => 'N/A',

  'sys_ali_res_title' => 'Alliance\'s resources',

  'sys_bonus' => 'Bonus',

  'sys_of_ally' => 'of Alliance',

  'sys_hint_player_name' => 'You can search player by his ID or name. If player name consists from strange symbols or only from numbers - you should use player ID for search.',
  'sys_hint_ally_name' => 'You can search Alliance by his ID, tag or name. If Alliance\'s tag or name consists from strange symbols or only from numbers - you should use ally ID for search.',

  'sys_fleet_and' => '+ fleets',
  'sys_on_planet' => 'On planet',
  'fl_on_stores' => 'In stock',

  'sys_ali_members_bonus' => 'Minimum Alliance size for Ally bonus ',

));

?>
