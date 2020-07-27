<?php

/*
#############################################################################
#  Filename: system.mo
#  Project: SuperNova.WS
#  Website: http://www.supernova.ws
#  Description: Massive Multiplayer Online Browser Space Strategy Game
#
#  Copyright © 2009-2018 Gorlum for Project "SuperNova.WS"
#  Copyright В© 2008 Aleksandar Spasojevic <spalekg@gmail.com>
#  Copyright В© 2005 - 2008 KGsystem
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

if (!defined('INSIDE')) {
	die('Hack attempt!');
}

global $config;

$a_lang_array = (array(
  'sys_administration' => 'SuperNova Administration',
  'sys_birthday' => 'Birthday',
  'sys_birthday_message' => '%1$s! SuperNova Administration warmly greats you with your birthday on %2$s and gives to you s small gift - %3$d %4$s!',

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

  'menu_admin_mining'          => 'Mining stats',
  'menu_admin_units'          => 'Units',
  'menu_admin_ube_balance'          => 'UBE Balance',

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
  'sys_characters'      => "characters",
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

  'sys_planet_expedition' => 'unexplored space',

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
  'res_basic_starting_resources' => 'Planet starting resources',
  'res_basic_income' => 'Basic Income',
  'res_basic_storage_size' => 'Planet storage size',
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
  'bld_research' => 'Research',
  'bld_hire' => 'Hire',

// Imperium page
  'imp_imperator' => "Emperor",
  'imp_overview' => "Empire Overview",
  'imp_fleets' => "Fleets in flight",
  'imp_production' => "Production",
  'imp_name' => "Name",
  'imp_research' => "Research",
  'imp_exploration' => "Exploration",
  'imp_imperator_none' => "There is no such Emperor in this Universe!",
  'sys_fields' => "Fields",

// Cookies
  'err_cookie' => "Error! You cannot authenticate the user on information in a cookie.<br />Clear cookies in you browser then <a href='login" . DOT_PHP_EX . "'>log in</a> in a game or <a href='reg" . DOT_PHP_EX . "'>register new account again</a>.",

// Supported languages
  'ru'              	  => 'Russian',
  'en'              	  => 'English',

  'sys_vacation'        => 'Your are on vacation until',
  'sys_vacation_leave'  => 'I have got rest - break holiday!',
  'sys_vacation_in'     => 'On vacation',
  'sys_level'           => 'Level',
  'sys_level_short'     => 'Lvl',
  'sys_level_max'       => 'Max level',

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
    SUBQUE_DEFENSE => 'Defense',
    QUE_RESEARCH   => 'Research',
  ),

  'navbar_button_expeditions_short' => 'Exp',
  'navbar_button_fleets' => 'Fleets',
  'navbar_button_quests' => 'Quests',
  'navbar_font' => 'Font',
  'navbar_font_normal' => 'Normal',
  'sys_que_structures' => 'Buildings',
  'sys_que_hangar' => 'Hangar',
  'sys_que_defense' => 'Defense',
  'sys_que_research' => 'Research',
  'sys_que_research_short' => 'Science',

  'eco_que'          => 'Queue',
  'eco_que_empty'    => 'Queue is empty',
  'eco_que_clear'    => 'Clear queue',
  'eco_que_trim'     => 'Undo last queue',
  'eco_que_artifact' => 'Use Artifact',

  'sys_cancel' => 'Cancel',

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
  'sys_ship_speed' 		=> 'Speed',
  'sys_ship_consumption' 		=> 'Consumption',
  'sys_ship_capacity' 		=> 'Capacity/Tanks',
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
  'sys_spy_activity' => 'There is some spy activity around your planets',
  'sys_spy_materials' 		=> 'Raw material',
  'sys_spy_fleet' 			=> 'Fleet',
  'sys_spy_defenses' 		=> 'Defence',
  'sys_mess_qg' 			=> 'Fleet command',
  'sys_mess_spy_report' 		=> 'Spy Report',
  'sys_mess_spy_lostproba' 	=> 'Accuracy of information received by the Spy probe %d %% ',
  'sys_mess_spy_detect_chance' 	=> 'Detection chance %d%%',
  'sys_mess_spy_detect_chance_no_percent' 	=> 'Detection chance',
  'sys_mess_spy_control' 		=> 'Counter-intelligence',
  'sys_mess_spy_activity' 		=> 'Spy activity',
  'sys_mess_spy_enemy_fleet' 	=> 'Alien fleet with planet',
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
  // 'sys_tran_mess_owner' 		=> 'One of your fleet reaches the planet %s %s and delivers %s %s, %s  %s and %s %s.',
  'sys_tran_mess_user'  		=> 'Your fleet sent to the planet %s %s arrived at %s %s and delivered %s %s, %s  %s and %s %s.',
  'sys_relocate_mess_user'  		=> 'Also following units where relocated:<br />',
  'sys_mess_fleetback' 		=> 'Return',
  'sys_tran_mess_back' 		=> 'One of your fleet returned to planet %s %s.',
  'sys_recy_gotten' 		=> 'One of your fleets, Nancy a %s %s and %s %s Return to planet.',
  'sys_notenough_money' 		=> 'You do not have enough resources to build: %s. You now: %s %s , %s %s and %s %s. For construction: %s %s , %s %s and %s %s.',
  'sys_nomore_level'		=> 'You no longer can improve it. It reached Max. level ( %s ).',
  'sys_buildlist' 			=> 'Building list',
  'sys_buildlist_fail' 		=> 'no buildings',
  'sys_gain' 			=> 'Extraction: ',
  'sys_debris' 			=> 'Debris: ',
  'sys_noaccess' 			=> 'Access Denied',
  'sys_noalloaw' 			=> 'You have access to this zone!',
  'sys_governor'        => 'Governor',

  'flt_error_duration_wrong' => 'Невозможно отправить флот - нет доступных интервалов для задержки. Изучите еще уровни Астрокартографии',
  'flt_stay_duration' => 'Time',

  'flt_mission_expedition' => array(
    'msg_sender' => 'Отчет экспедиции',
    'msg_title' => 'Отчет экспедиции',

    'found_dark_matter_new' => 'Получена ТМ:',
    'found_resources_new' => "Найдены ресурсы:",
    'found_fleet_new' => "Найдены корабли:",
    'lost_fleet_new' => "Потеряны следующие корабли:",

    'found_dark_matter' => 'Получено %1$d единиц ТМ',
    'found_resources' => "Найдены ресурсы:\r\n",
    'found_fleet' => "Найдены корабли:\r\n",
    'lost_fleet' => "Потеряны следующие корабли:\r\n",
    'outcomes' => array(
      FLT_EXPEDITION_OUTCOME_NONE => array(
        'messages' => array(
          'Ваши исследователи ничего не обнаружили',
        ),
      ),

      FLT_EXPEDITION_OUTCOME_LOST_FLEET => array(
        'messages' => array(
          'Флот попал в черную дыру и частично утерян',
        ),
      ),

      FLT_EXPEDITION_OUTCOME_LOST_FLEET_ALL => array(
        'messages' => array(
          'Если бы вы только это видели! Оно такое красивое... Оно зовёт к себе... (связь с флотом утеряна)',
          // 'Отчёт флота %1$s. Мы завершили исследование сектора. Команда недовольна Эй, ты что делаешь на мостике?! (связь с флотом утеряна)',
          'Отчёт флота %1$s. Всё спокойно (помехи) (связь с флотом утеряна)',
          'АААААА! ЧТО ЭТО?! ОТКУДА ОНО ВЗЯ (связь с флотом утеряна)',
          'Обнаружен неизвестный объект. Он не отвечает на запросы стандартных протоколов. Высылаем зонд для проведения исследований (связь с флотом утеряна)',
        ),
      ),

      FLT_EXPEDITION_OUTCOME_FOUND_FLEET => array(
        'no_result' => 'К сожалению, совокупной мощности всех компьютеров флота не хватило даже на контроль самого мелкого корабля. Попробуйте отправлять больше кораблей и/или более крупные корабли',
        'messages' => array(
          0 => array(
            'Вы нашли абсолютно новый флот',
          ),
          1 => array(
            'Вы нашли флот',
          ),
          2 => array(
            'Вы нашли б/у флот',
          ),
        ),
      ),

      FLT_EXPEDITION_OUTCOME_FOUND_RESOURCES => array(
        'no_result' => 'Трюмы вашего флота оказались неспособны вместить хоть один контейнер с ресурсами. Попробуйте отправлять флот с большим количеством транспортников',
        'messages' => array(
          0 => array(
            'Вы нашли пиратский клад с ресурсами. Сколько же кораблей было уничтожено, что бы собрать столько добра?',
          ),
          1 => array(
            'Вы нашли заброшенную астероидную базу. Интересно, куда делись её обитатели? Исследовав руины, вы нашли несколько уцелевших хранилищ',
          ),
          2 => array(
            'Вы наткнулись на уничтоженный транспортный конвой. Обыскав трюмы разбитых кораблей, вы обнаружили немного ресурсов',
          ),
        ),
      ),

      FLT_EXPEDITION_OUTCOME_FOUND_DM => array(
        'no_result' => 'К сожалению, всех накопителей флота не хватило что бы собрать одну-единственую ТМ. Попробуйте отправлять флот побольше',
        'messages' => 'Ваш флот стал свидетелем рождения СуперНовы',
        /*
        'messages' => array(
          'Ваш флот стал свидетелем рождения СуперНовы 1',
          'Ваш флот стал свидетелем рождения СуперНовы 2',
          'Ваш флот стал свидетелем рождения СуперНовы 3',
        ),
        */
      ),

    ),
  ),

  // News page & a bit of imperator page
  'news_fresh'      => 'Fresh news',
  'news_all'        => 'All news',
  'news_title'      => 'News',
  'news_none'       => 'No news',
  'news_new'        => 'New',
  'news_future'     => 'Announcement',
  'news_more'       => 'Read More...',
  'news_hint'       => 'To close fresh news list - read them all by clicking on header "[ News ]"',

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
  'sys_game_documentation' => 'Documentation',
  'sys_max' => 'Max',
  'sys_banned_msg' => 'You are banned. For more information please visit <a href="banned.php">here</a>. Time of account ban: ',
  'sys_total_time' => 'Total time',
  'sys_total_time_short' => 'Que time',

  // Univers
  'uni_moon_of_planet' => 'planet',

  // Combat reports
  'cr_view_title'  => "View Combat Reports",
  'cr_view_button' => "View Report",
  'cr_view_prompt' => "Enter the code",
  'cr_view_my'     => "My Combat Records",
  'cr_view_hint'   => "This page allows you to view shared Combat Reports. All Combat Reports will have a code at the bottom. To share a Combat Report simply give them that code. Then they can enter it here and view your Combat Report.",

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
  'cred_design'  => 'DesignerСЂ',
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

  'sys_quantity' => 'Quantity',
  'sys_quantity_maximum' => 'Maximum quantity',
  'sys_qty' => 'Qty',
  'sys_quantity_total' => 'Total',

  'sys_buy_for' => 'Buy for',
  'sys_buy' => 'Buy',

  'sys_eco_lack_dark_matter' => 'Not enough Dark Matter',

  'time_local' => 'Time on player',
  'time_server' => 'Time on server',

  'topnav_imp_attack' => 'Your Empire is attacked!',

  'sys_result' => array(
    'error_dark_matter_not_enough' => 'Не хватает Тёмной Материи для завершения операции',
    'error_dark_matter_change' => 'Ошибка изменения количества Тёмной Материи! Повторите операцию еще раз. Если ошибка повторится - сообщите Администрации сервера',
  ),

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
    BUILD_UNIT_BUSY => array(
      0 => 'Busy',
      STRUC_LABORATORY => 'Research ongoing',
      STRUC_LABORATORY_NANO => 'Research ongoing',
    ),
    BUILD_QUE_FULL => 'Que is full',
    BUILD_SILO_FULL => 'Silo is full',
    BUILD_MAX_REACHED => 'You already build and/or enqued maximum numbers of this type units',
    BUILD_SECTORS_NONE => 'No free sectors',
    BUILD_AUTOCONVERT_AVAILABLE => 'Autoconvert available',
    BUILD_HIGHSPOT_NOT_ACTIVE => 'Festival highspot is not active',
  ),

  'sys_game_mode' => array(
    GAME_SUPERNOVA => 'SuperNova',
    GAME_OGAME     => 'oGame',
    GAME_BLITZ     => 'Blitz',
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
    4 => 'Developer',
  ),

  'user_level_shortcut' => array(
    0 => 'P',
    1 => 'M',
    2 => 'O',
    3 => 'A',
    4 => 'D',
  ),

  'sys_lessThen15min'   => '&lt; 15 min',

  'sys_no_points'        => 'You do not have enough Dark Matter!',
  'sys_dark_matter_obtain_header' => 'How to obtain Dark Matter',
  'sys_dark_matter_desc' => 'Dark matter - using the standard methods of  fabric, which accounts for 23% mass of the universe. From there you can obtain an incredible amount of energy. Because of this, and because of the complexities associated with its extraction, Dark Matter is valued very highly.',
  'sys_dark_matter_hint' => 'With the help of this substance you can hire officers and commanders.',

  'sys_dark_matter_what_header' => 'What is <span class="dark_matter">Dark Matter</span>',
  'sys_dark_matter_description_header' => 'Why do you need <span class="dark_matter">Dark Matter</span>',
  'sys_dark_matter_description_text' => '<span class="dark_matter">Dark Matter</span> is ingame currency, which in the game you can make a variety of operations:
    <ul>
      <li>Buy <a href="index.php?page=premium"><span class="link">Premium account</span></a></li>
      <li>Recruit <a href="officer.php?mode=600"><span class="link">Mercenaries</span></a> for Empire</li>
      <li>Hire Governors and but additional sectors <a href="overview.php?mode=manage"><span class="link">for planets</span></a></li>
      <li>Buy <a href="officer.php?mode=1100"><span class="link">Schematics</span></a></li>
      <li>Buy <a href="artifacts.php"><span class="link">Artefacts</span></a></li>
      <li>Use <a href="market.php"><span class="link">Black Market</span></a>: exchange resources; sell ships; buy ships; buy intelligence etc</li>
      <li>...and many other things</li>
    </ul>',
  'sys_dark_matter_obtain_text' => 'You acquring <span class="dark_matter">Dark Matter</span> in game process: while gained levels for raids to enemy planets, researching technologies, building and destroying buildings.
    Also sometimes expeditions can gain you some <span class="dark_matter">DM</span>.',

//  'sys_dark_matter_obtain_text_convert' => '<br />Besides, you can convert Metamatter to Dark Matter. <a href="metamatter.php" class="link">More about Metamatter</a>',
  'sys_dark_matter_obtain_text_convert' => '<br />If you lack <span class="dark_matter">Dark Matter</span> - purchase the <span class="metamatter">Metamatter</span>. If you have not enough <span class="dark_matter">DM</span> needed amount of <span class="metamatter">Metamatter</span> would be used instead of <span class="dark_matter">DM</span>',

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

  'sys_ali_bonus_members' => 'Minimum Alliance size for Ally bonus ',

  'sys_premium' => 'Premium account',

  'mrc_period_list' => array(
    PERIOD_MINUTE    => '1 minute',
    PERIOD_MINUTE_3  => '3 minutes',
    PERIOD_MINUTE_5  => '5 minutes',
    PERIOD_MINUTE_10 => '10 minutes',
    PERIOD_DAY       => '1 day',
    PERIOD_DAY_3     => '3 days',
    PERIOD_WEEK      => '1 week',
    PERIOD_WEEK_2    => '2 weeks',
    PERIOD_MONTH     => '30 days',
    PERIOD_MONTH_2   => '60 days',
    PERIOD_MONTH_3   => '90 days',
  ),

  'sys_sector_buy' => 'Buy 1 sector',

  'sys_select_confirm' => 'Confirm selection',

  'sys_capital' => 'Capital',

  'sys_result_operation' => 'Outcome',

  'sys_password' => 'Password',
  'sys_password_length' => 'Password length',
  'sys_password_seed' => 'Used characters',

  'sys_msg_ube_report_err_not_found' => 'Battle report not found - check cypher key. It is possible that battle report was deleted as outdated',

  'sys_mess_attack_report' 	=> 'Battle Report',
  'sys_perte_attaquant' 		=> 'The Attacker lost',
  'sys_perte_defenseur' 		=> 'The Defender lost',


  'ube_report_info_page_header' => 'Battle report',
  'ube_report_info_page_header_cypher' => 'Access code',
  'ube_report_info_main' => 'Main battle info',
  'ube_report_info_date' => 'Date and time',
  'ube_report_info_location' => 'Location',
  'ube_report_info_rounds_number' => 'Round number',
  'ube_report_info_outcome' => 'Battle outcome',
  'ube_report_info_outcome_win' => 'Attacker win',
  'ube_report_info_outcome_loss' => 'Attacker lost',
  'ube_report_info_outcome_draw' => 'Draw',
  'ube_report_info_link' => 'Link to battle report',
  'ube_report_info_bbcode' => 'BBCode for posting in chat',
  'ube_report_info_sfr' => 'Battle finshed in one round by attacker loss<br />Possible SFR',
  'ube_report_info_debris' => 'Debris on orbit',
  'ube_report_info_debris_simulator' => '(does not counting moon)',
  'ube_report_info_loot' => 'Loot',
  'ube_report_info_loss' => 'Battle losses',
  'ube_report_info_generate' => 'Page generation time',

  'ube_report_moon_was' => 'This planet already had moon',
  'ube_report_moon_chance' => 'Moon chance',
  'ube_report_moon_created' => 'On planet orbit appears new moon diameter',

  'ube_report_moon_reapers_none' => 'All ships with graviton engines was destroyed during fight',
  'ube_report_moon_reapers_wave' => 'Attacker\'s ship created focused gravitation wave',
  'ube_report_moon_reapers_chance' => 'Moon destruction chance',
  'ube_report_moon_reapers_success' => 'Moon destroyed',
  'ube_report_moon_reapers_failure' => 'Graviton wave power was not enough to destroy moon',

  'ube_report_moon_reapers_outcome' => 'Graviton engines self-desctruction chance',
  'ube_report_moon_reapers_survive' => 'Graviton engines was succesfully synchronized and compensated graviton recoil',
  'ube_report_moon_reapers_died' => 'Graviton engines self-destructs and destroy all your fleet',

  'ube_report_side_attacker' => 'Attacker',
  'ube_report_side_defender' => 'Defender',

  'ube_report_round' => 'Round',
  'ube_report_unit' => 'Unit',
  'ube_report_attack' => 'Attack',
  'ube_report_shields' => 'Shields',
  'ube_report_shields_passed' => 'Puncture',
  'ube_report_armor' => 'Armor',
  'ube_report_damage' => 'Damage',
  'ube_report_loss' => 'Losses',

  'ube_report_info_restored' => 'Defense unit recovered',
  'ube_report_info_loss_final' => 'Total unit loss',
  'ube_report_info_loss_resources' => 'Loss in resources',
  'ube_report_info_loss_dropped' => 'Resource loss due reduced cargo capacity',
  'ube_report_info_loot_lost' => 'Resources looted from planet',
  'ube_report_info_loss_gained' => 'Resource loss due planet loot',
  'ube_report_info_loss_in_metal' => 'Total resources lost in metal',

  'ube_report_msg_body_common' => 'Battle on %s on orbit of %s [%d:%d:%d] %s<br />%s<br /><br />',
  'ube_report_msg_body_debris' => 'There are debris appears on planet orbit:<br />',
  'ube_report_msg_body_sfr' => 'You lost contact with your fleet',

  'ube_report_capture' => 'Planet capture',
  'ube_report_capture_result' => array(
    UBE_CAPTURE_DISABLED => 'Захват планет отключён',
    UBE_CAPTURE_NON_PLANET => 'Захватывать можно только планеты',
    UBE_CAPTURE_NOT_A_WIN_IN_1_ROUND => 'Для захвата планеты бой должен закончиться победой в первом раунде',
    UBE_CAPTURE_TOO_MUCH_FLEETS => 'При захвате планеты в бою должен участвовать только флот-захватчик и планетарный флот',
    UBE_CAPTURE_NO_ATTACKER_USER_ID => 'ВНУТРЕННЯЯ ОШИБКА - Нет ИД атакующего! Сообщите разработчику!',
    UBE_CAPTURE_NO_DEFENDER_USER_ID => 'ВНУТРЕННЯЯ ОШИБКА - Нет ИД защитника! Сообщите разработчику!',
    UBE_CAPTURE_CAPITAL => 'Нельзя захватывать столицу',
    UBE_CAPTURE_TOO_LOW_POINTS => 'Можно захватывать планеты только у игроков, чье общее количество очков не менее чем в 2 раза больше количество очков у атакующего',
    UBE_CAPTURE_NOT_ENOUGH_SLOTS => 'Больше нет слотов захвата планеты',
    UBE_CAPTURE_SUCCESSFUL => 'Планета захвачена атакующим игроком',
  ),

  'sys_kilometers_short' => 'km',

  'ube_simulation' => 'Simulation',

  'sys_hire_do' => 'Hire',

  'sys_captains' => 'Captains',

  'sys_fleet_composition' => 'Fleet composition',

  'sys_continue' => 'Continue',

  'uni_planet_density_types' => array(
    PLANET_DENSITY_NONE => 'Never happens',
    PLANET_DENSITY_ICE_HYDROGEN => 'Hydrogen ice',
    PLANET_DENSITY_ICE_METHANE => 'Methane ice',
    PLANET_DENSITY_ICE_WATER => 'Water ice',
    PLANET_DENSITY_CRYSTAL_RAW => 'Crystal',
    PLANET_DENSITY_CRYSTAL_SILICATE => 'Silicate',
    PLANET_DENSITY_CRYSTAL_STONE => 'Stone',
    PLANET_DENSITY_STANDARD => 'Standard',
    PLANET_DENSITY_METAL_ORE => 'Ore',
    PLANET_DENSITY_METAL_PERIDOT => 'Peridot',
    PLANET_DENSITY_METAL_RAW => 'Metal',
  ),

  'sys_planet_density' => 'Density',
  'sys_planet_density_units' => 'kg/m&sup3;',
  'sys_planet_density_core' => 'Core type',

  'sys_change' => 'Change',
  'sys_show' => 'Show',
  'sys_hide' => 'Hide',
  'sys_close' => 'Close',
  'sys_unlimited' => 'Unlimited',

  'ov_core_type_current' => 'Current core type',
  'ov_core_change_to' => 'Change to',
  'ov_core_err_none' => 'Planet core type succesfully changed from "%s" to "%s".<br />New average planet density %d kg/m3',
  'ov_core_err_not_a_planet' => 'Only planet core type could be changed',
  'ov_core_err_denisty_type_wrong' => 'Wrong core type',
  'ov_core_err_same_density' => 'New core type does not differ from current one - nothing to change',
  'ov_core_err_no_dark_matter' => 'There are not enough Dark Matter to change core type',

  'sys_color'  => "Color",
  'topnav_imp_attack' => 'Your Empire is attacked',
  'topnav_user_rank' => 'Your current place in statistic',
  'topnav_users' => 'Number of registered players',
  'topnav_users_online' => 'Now online',

  'topnav_refresh_page' => 'Reload page',

  'sys_colonies' => 'Colonies',
  'sys_radio' => '"Space" radio',

  'sys_auth_provider_list' => array(
    ACCOUNT_PROVIDER_NONE => 'Таблица USERS',
    ACCOUNT_PROVIDER_LOCAL => 'Таблица ACCOUNT',
    ACCOUNT_PROVIDER_CENTRAL => 'Центральная таблица ACCOUNT',
  ),

  'sys_login_messages' => array(
    LOGIN_UNDEFINED => 'Login process does not started',
    LOGIN_SUCCESS => 'Login succesfull',
    LOGIN_ERROR_USERNAME_EMPTY => 'Имя игрока не может быть пустым',
    LOGIN_ERROR_USERNAME_RESTRICTED_CHARACTERS => 'В имени игрока и логине не допускаются символы ',
    LOGIN_ERROR_USERNAME => 'There is no player with such name',
    LOGIN_ERROR_USERNAME_ALLY_OR_BOT => 'Это имя принадлежит Альянсу или боту. Под ним нельзя логиниться... по крайней мере пока',
    LOGIN_ERROR_PASSWORD_EMPTY => 'Пароль не может быть пустым',
    LOGIN_ERROR_PASSWORD_TRIMMED => 'Пароль не может начинаться или заканчиваться пробелом, табуляцией или символом перевода строки',
    LOGIN_ERROR_PASSWORD => 'Wrong password',
    //    LOGIN_ERROR_COOKIE => '',

    REGISTER_SUCCESS => 'Registration succesfully complete',
    REGISTER_ERROR_BLITZ_MODE => 'Регистрация новых игроков в режиме Блиц-сервера отключена',
    REGISTER_ERROR_USERNAME_WRONG => 'Wrong player name',
    REGISTER_ERROR_ACCOUNT_NAME_EXISTS => 'Account name already registered',
    REGISTER_ERROR_PASSWORD_INSECURE => 'Insecure or wrong password. Password should be at least ' . PASSWORD_LENGTH_MIN . ' characters long and cannot start or end with spaces',
    REGISTER_ERROR_USERNAME_SHORT => 'Слишком короткое имя. Имя должно состоять минимум из ' . LOGIN_LENGTH_MIN. ' символов',
    REGISTER_ERROR_PASSWORD_DIFFERENT => 'Password does not match confirmation password',
    REGISTER_ERROR_EMAIL_EMPTY => 'Е-Мейл не может быть пустым',
    REGISTER_ERROR_EMAIL_WRONG => 'Введенный Е-Мейл не является адресом электронной почты',
    REGISTER_ERROR_EMAIL_EXISTS => 'This email already registered. If you already registered try password reset option. Otherwise use other email address',

    PASSWORD_RESTORE_ERROR_EMAIL_NOT_EXISTS => 'There is no player with such base email',
    PASSWORD_RESTORE_ERROR_TOO_OFTEN => 'You can request password restoration code only once per 10 minutes. Check your SPAM folder for restoration code or contact server administration via email <span class="ok">' . $config->server_email . '</span> from your main email (email which you used for registration)',
    PASSWORD_RESTORE_ERROR_SENDING => 'There is error sending email with restore code. Contact server administration via email <span class="ok">' . $config->server_email . '</span>',
    PASSWORD_RESTORE_SUCCESS_CODE_SENT => 'Restoration code successfully sent',

    PASSWORD_RESTORE_ERROR_CODE_EMPTY => 'Restoration code can not be empty',
    PASSWORD_RESTORE_ERROR_CODE_WRONG => 'Wrong restoration code',
    PASSWORD_RESTORE_ERROR_CODE_TOO_OLD => 'Restoration code is too old. Get new one',
    PASSWORD_RESTORE_ERROR_CODE_OK_BUT_NO_ACCOUNT_FOR_EMAIL => 'Код восстановления указан верно, однако не найдено ни одного аккаунта с таким емейлом. Возможно, он был удалён или произошла внутренняя ошибка. Обратитесь к Администрации сервера',
    PASSWORD_RESTORE_SUCCESS_PASSWORD_SENT => 'Email with new password successfully sent to your email',
    PASSWORD_RESTORE_SUCCESS_PASSWORD_SEND_ERROR => 'Error sending new password. Get new restoration code and try again',


    REGISTER_ERROR_PLAYER_NAME_TRIMMED => 'Player name can not starts or ends with "empty characters" (characters like "Space", "Tabulation", "New line" etc)',
    REGISTER_ERROR_PLAYER_NAME_EMPTY => 'Player name can not be empty',
    REGISTER_ERROR_PLAYER_NAME_RESTRICTED_CHARACTERS => 'Player name contains forbidden characters',
    REGISTER_ERROR_PLAYER_NAME_SHORT => 'Player name should have ' . LOGIN_LENGTH_MIN . ' characters at least',
    REGISTER_ERROR_PLAYER_NAME_EXISTS => 'This player name is already owned by someone. Please choose another name',


    // Внутренние ошибки
    AUTH_ERROR_INTERNAL_PASSWORD_CHANGE_ON_RESTORE => 'Password change error. Contact server administration',
    PASSWORD_RESTORE_ERROR_ADMIN_ACCOUNT => 'Forbidden to restore password for member of Server Team. Contact Administrator directly',
    REGISTER_ERROR_ACCOUNT_CREATE => 'Error creating account! Please, message Administration about this error!',
    LOGIN_ERROR_SYSTEM_ACCOUNT_TRANSLATION => 'СИСТЕМНАЯ ОШИБКА - СБОЙ В ТАБЛИЦЕ ТРАНСЛЯЦИИ ПРОВАЙДЕРОВ! Сообщите администрации сервера!',
    PASSWORD_RESTORE_ERROR_ACCOUNT_NOT_EXISTS => 'Account not found! Contact server administration!',
    AUTH_PASSWORD_RESET_INSIDE_ERROR_NO_ACCOUNT_FOR_CONFIRMATION => 'INTERNAL ERROR! No account to change password on confirmation code. Please, report to Universe Administration!',
    LOGIN_ERROR_NO_ACCOUNT_FOR_COOKIE_SET => 'INTERNAL ERROR! No account for cookie_set()! Please, report to Universe Administration!',
  ),

  'log_reg_email_title' => "Your registration on SuperNova game server %1\$s",
  'log_reg_email_text' => "Registration confirmation for account %3\$s\r\n\r\n
  This letter contains registration data on SuperNova game server %1\$s
  Store this data in safe place\r\n\r\n
  Server address: %2\$s\r\n
  Your login: %3\$s\r\n
  Your password: %4\$s\r\n\r\n
  Thank you for registering on our server! We wish you luck in game!\r\n
  Server administration %1\$s %2\$s\r\n\r\n
  Powered by OpenSource engine 'Project SuperNova.WS'. Light your SuperNova http://supernova.ws/",

  'log_lost_email_title' => 'Supernova, Universe %s: Password reset',
  'log_lost_email_code' => "Someone (possibly you) has requested a reset password on SuperNova Universe %4\$s . If you did not request reset password-then just ignore this email.\r\n\r\nFor password reset, go to the address \r\n%1\$s?password_reset_confirm=1&password_reset_code=%2\$s#tab_password_reset\r\nor enter the confirmation code \"%2\$s\" (WITHOUT THE DOUBLE QUOTES!) on the page %1\$s#tab_password_reset This code will be valid up to %3\$s. After the password reset you will need to request a new confirmation code",
  'log_lost_email_pass' => "You changed your password on the SuperNova Universe %1\$s.\r\n\r\nYou login:\r\n%2\$s\r\n\r\nYour new password:\r\n%3\$s\r\n\r\nRemember it!\r\n\r\nYou can enter into game following link " . SN_ROOT_VIRTUAL . "login.php and using provided login and password",

  'sys_password_reset_message_body' => "Your password to accessing this Universe was reset.\r\n\r\nYour new password is:\r\n\r\n%1\$s\r\n\r\nRemember your password!\r\n\r\nYou can change it any time to one that suits you on 'Settings' menu",

  'sys_login_password_show' => 'Show password',
  'sys_login_password_hide' => 'Hide password',
  'sys_password_repeat' => 'Confirm password',

  'sys_game_disable_reason' => array(
    GAME_DISABLE_NONE => 'Game is enabled',
    GAME_DISABLE_REASON => 'Game is disabled. Players will see reason',
    GAME_DISABLE_UPDATE => 'Game is updating',
    GAME_DISABLE_STAT => 'Statistics update in progress',
    GAME_DISABLE_INSTALL => 'Game is not configured yet',
    GAME_DISABLE_EVENT_BLACK_MOON => 'Black Moon Rising!',
  ),

  'sys_sector_purchase_log' => 'User {%2$d} {%1$s} purchased 1 sector on planet {%5$d} {%3$s} type "%4$s" for %6$d DM',

  'sys_notes' => 'Notes',
  'sys_notes_priorities' => array(
    0 => 'Low priority',
    1 => 'Below normal',
    2 => 'Normal',
    3 => 'Important',
    4 => 'Very important',
  ),

  'sys_milliseconds' => 'milliseconds',

  'sys_gender' => 'Gender',
  'sys_gender_list' => array(
    GENDER_UNKNOWN => 'Will decide - when grew up',
    GENDER_MALE => 'Male',
    GENDER_FEMALE => 'Female',
  ),

  'imp_stat_header' => 'График изменений',
  'imp_stat_types' => array(
    'TOTAL_RANK' => 'Место в общей статистике',
    'TOTAL_POINTS' => 'Общее количество очков',
    // 'TOTAL_COUNT' => 'Общее количество ресурсов',
    'TECH_RANK' => 'Место в статистике по Исследованиям',
    'TECH_POINTS' => 'Количество очков за Исследования',
    // 'TECH_COUNT' => 'Количество уровней',
    'BUILD_RANK' => 'Место в статистике по Постройкам',
    'BUILD_POINTS' => 'Количество очков за Постройки',
    // 'BUILD_COUNT' => '',
    'DEFS_RANK' => 'Место в статистике по Обороне',
    'DEFS_POINTS' => 'Количество очков за Оборону',
    //'DEFS_COUNT' => '',
    'FLEET_RANK' => 'Место в статистике по Кораблям',
    'FLEET_POINTS' => 'Количество очков за Корабли',
    //'FLEET_COUNT' => '',
    'RES_RANK' => 'Место в статистике по свободным ресурсам',
    'RES_POINTS' => 'Количество очков за свободные ресурсы',
    //'RES_COUNT' => '',
  ),

  'sys_date' => 'Date',

  'sys_blitz_global_button' => 'Блиц-сервер',
  'sys_blitz_page_disabled' => 'В режиме Блиц-сервера эта страница недоступна',
  'sys_blitz_registration_disabled' => 'Регистрация на игру в Блиц-сервер отключена',
  'sys_blitz_registration_no_users' => 'Нет зарегестрированных игроков',
  'sys_blitz_registration_player_register' => 'Зарегестрироваться для игры',
  'sys_blitz_registration_player_register_un' => 'Отозвать регистрацию',
  'sys_blitz_registration_closed' => 'Регистрация пока закрыта. Попробуйте зайти позже',
  'sys_blitz_registration_player_generate' => 'Сгенерировать логины и пароли',
  'sys_blitz_registration_player_import_generated' => 'Импортировать сгенерированную строку',
  'sys_blitz_registration_player_name' => 'Ваш логин для Блиц-сервера:',
  'sys_blitz_registration_player_password' => 'Ваш пароль для Блиц-сервера:',
  'sys_blitz_registration_server_link' => 'Ссылка на Блиц-сервер',
  'sys_blitz_registration_player_blitz_name' => 'Имя на Блиц-сервере',
  'sys_blitz_registration_price' => 'Стоимость подачи заявки',
  'sys_blitz_registration_mode_list' => array(
    BLITZ_REGISTER_DISABLED => 'Регистрация отключена',
    BLITZ_REGISTER_OPEN => 'Регистрация открыта',
    BLITZ_REGISTER_CLOSED => 'Регистрация закрыта',
    BLITZ_REGISTER_SHOW_LOGIN => 'Открыты логины и пароли',
    BLITZ_REGISTER_DISCLOSURE_NAMES => 'Подведение итогов',
  ),

  'survey' => 'Опрос',
  'survey_questions' => 'Варианты для выбора',
  'survey_questions_hint' => '1 вариант на строку',
  'survey_questions_hint_edit' => 'Редактированние опроса обнулит его результаты',
  'survey_until' => 'Длительность опроса (1 сутки по умолчанию)',

  'survey_votes_total_none' => 'Еще никто не проголосовал... Проголосуй первым!',
  'survey_votes_total_voted' => 'Уже проголосовало:',
  'survey_votes_total_voted_join' => 'Голосуй - или проиграешь!',
  'survey_votes_total_voted_has_answer' => 'Вы уже проголосовали. Вместе с вами проголосовавших',

  'survey_lasts_until' => 'Опрос продлится до',

  'survey_select_one' => 'Выберите один вариант ответа и нажмите',
  'survey_confirm' => 'Проголосовать!',
  'survey_result_sent' => 'Ваш голос учтен. Обновите страницу или воспользуйтесь ссылкой <a class="link" href="announce.php">Новости</a> что бы увидеть текущие результаты опроса',
  'survey_complete' => 'Опрос завершен',

  'player_option_fleet_ship_sort' => array(
    PLAYER_OPTION_SORT_DEFAULT => 'Standard',
    PLAYER_OPTION_SORT_NAME => 'By name',
    PLAYER_OPTION_SORT_SPEED => 'By ship speed',
    PLAYER_OPTION_SORT_COUNT => 'By ship quantity',
    PLAYER_OPTION_SORT_ID => 'By ID',
  ),

  'player_option_building_sort' => array(
    PLAYER_OPTION_SORT_DEFAULT => 'Standard',
    PLAYER_OPTION_SORT_NAME => 'By name',
    PLAYER_OPTION_SORT_ID => 'By ID',
    PLAYER_OPTION_SORT_CREATE_TIME_LENGTH => 'By building time',
  ),

  'sys_sort' => 'Sort',
  'sys_sort_inverse' => 'Reverse order',

  'sys_blitz_reward_log_message' => 'Блиц-сервер %1$d призовое место блиц-имя "%2$s"',
  'sys_blitz_registration_view_stat' => 'Посмотреть статистику Блиц-сервера',

  'sys_login_register_message_title' => "Ваше имя и пароль для входа в игру",
  'sys_login_register_message_body' => "Ваше имя для входа в игру (логин)\r\n%1\$s\r\n\r\nВаш пароль для входа в игру\r\n%2\$s\r\n\r\nЗапишите или запомните эти данные!",

  'auth_provider_list' => array(
    ACCOUNT_PROVIDER_NONE => 'Таблица users',
    ACCOUNT_PROVIDER_LOCAL => 'Таблица account',
    ACCOUNT_PROVIDER_CENTRAL => 'Центральное хранилище',
  ),

  'bld_autoconvert' => 'Автоматическая конвертация при создании юнита {%1$d} "%4$s" в количесте %2$d на планете %3$s',

  'news_show_rest' => 'Показать текст новости',

  'wiki_requrements' => 'Requires',
  'wiki_grants' => 'Grants',

  'que_slot_length' => 'Slots',

  'sys_confirm_action_title' => 'Подтвердите ваше действие',
  'sys_confirm_action' => 'Вы действительно хотите сделать это?',

  'sys_system_speed_original' => 'Original speed',
  'sys_system_speed_for_action' => 'Speed while action/event',

  'menu_info_best_battles' => 'Best battles',

  'sys_cost' => 'Cost',
  'sys_price' => 'Price',

  'sys_governor_none' => 'Governor not hired',
  'sys_governor_hire' => 'Hire Governor',
  'sys_governor_upgrade_or_change' => 'Upgrade or change Governor',

  'tutorial_prev' => '<< Next',
  'tutorial_next' => 'Prev >>',
  'tutorial_finish' => 'Finish',
  'tutorial_window' => 'Open in window',
  'tutorial_window_off' => 'Return to page',

  'tutorial_error_load' => "Error loading tutorial - try again. If error persists - please contact game Administration",
  'tutorial_error_next' => "Error - there is no next page in tutorial - please contact game Administration",
  'tutorial_error_prev' => "Error - there is no previous page in tutorial - please contact game Administration",

  'sys_click_here_to_continue' => 'Click here to continue',

  'sys_module_error_not_found' => 'Award module "%1$s" not found or disabled!',

  'rank_page_title' => 'Ranks list',
  'rank' => 'Military Rank',
  'ranks' => [
    0  => 'Cadet',
    1  => 'Recruit',
    2  => 'Private',
    3  => 'Lance Corporal',
    4  => 'Corporal',
    5  => 'Sergeant',
    6  => 'Sergeant Major',
    7  => 'Midshipman',
    8  => 'Warrant Officer',
    9  => 'Ensign',
    10 => 'Lieutenant',
    11 => 'Captain',
    12 => 'Major',
    13 => 'Lieutenant Colonel',
    14 => 'Colonel',
    15 => 'Rear Admiral',
    16 => 'Vice Admiral',
    17 => 'Admiral',
    18 => 'Fleet Admiral',
    19 => 'Marshal',
    20 => 'Generalissimo',
  ],

));
