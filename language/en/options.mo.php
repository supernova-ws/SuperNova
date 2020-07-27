<?php

/*
#############################################################################
#  Filename: options.mo
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

$a_lang_array = [
  'opt_account' => 'Account',
  'opt_int_options' => 'Interface',
  'opt_settings_statistics' => 'Player\'s statistics',
  'opt_settings_info' => 'Player\'s info',
  'opt_alerts' => 'Alerts',
  'opt_common' => 'Common',
  'opt_tutorial' => 'Tutorial',

  'opt_birthday' => 'Birthday',

  'opt_header' => 'User options',
  'opt_messages' => 'Automatic alerts',
  'opt_msg_saved' => 'Options succesfully saved',
  'opt_msg_name_changed' => 'Username sucessfully changed',
  'opt_msg_pass_changed' => 'Password sucessfully changed',
  'opt_err_pass_wrong' => 'Wrong old password. Password was not changed',
  'opt_err_pass_unmatched' => 'New password confirmation is not identical to new password. Password was not changed',

  'opt_msg_name_change_err_used_name' => 'Someone else already owns this name',
  'opt_msg_name_change_err_no_dm' => 'There is not enough DM to change name',

  'username_old' => 'Current name',
  'username_new' => 'New name',
  'username_change_confirm' => 'Change name',
  'username_change_confirm_payed' => 'for',

  'changue_pass' => 'Change password',
  'Download' => 'Download',
  'userdata' => 'Information',
  'username' => 'Username',
  'lastpassword' => 'Old password',
  'newpassword' => 'New password<br>(min. 8 characters)',
  'newpasswordagain' => 'Repeat new password',
  'emaildir' => 'E-mail address',
  'emaildir_tip' => 'This address can be changed at any time. Address will be the main, if it has not been modified within 7 days.',
  'permanentemaildir' => 'Main e-mail address',
  'opt_planet_sort_title' => 'Order planets by',
  'opt_planet_sort_options' => [
    SORT_ID       => 'Colonization Time',
    SORT_LOCATION => 'Coordinates',
    SORT_NAME     => 'Name',
    SORT_SIZE     => 'Size',
  ],
  'opt_planet_sort_ascending' => [
    SORT_ASCENDING  => 'Ascending',
    SORT_DESCENDING => 'Descending',
  ],

  'opt_navbar_title' => 'Navigation Panel',
  'opt_navbar_description' => 'The navigation bar (or simply "navbar") is located at the very top of the screen. This section allows you to customize the look of the navbar.',
  'opt_navbar_resourcebar_description' => 'Resourcebar - resource panel',
  'opt_navbar_buttons_title' => 'Setup navbar buttons',
  'opt_player_options' => [
    PLAYER_OPTION_NAVBAR_PLANET_VERTICAL        => 'Vertical resourcebar',
    PLAYER_OPTION_NAVBAR_PLANET_DISABLE_STORAGE => 'Disable storage capacity in resourcebar',
    PLAYER_OPTION_NAVBAR_PLANET_OLD             => 'Use old tabled resource view',

    PLAYER_OPTION_NAVBAR_RESEARCH_WIDE          => 'Wide Research button (old look)',
    PLAYER_OPTION_NAVBAR_DISABLE_RESEARCH       => 'Disable Research button',
    PLAYER_OPTION_NAVBAR_DISABLE_PLANET         => 'Disable Planet button',
    PLAYER_OPTION_NAVBAR_DISABLE_HANGAR         => 'Disable Hangar button',
    PLAYER_OPTION_NAVBAR_DISABLE_DEFENSE        => 'Disable Defense button',
    PLAYER_OPTION_NAVBAR_DISABLE_EXPEDITIONS    => 'Disable Expeditions button',
    PLAYER_OPTION_NAVBAR_DISABLE_FLYING_FLEETS  => 'Disable Flying Fleets button',
    PLAYER_OPTION_NAVBAR_DISABLE_QUESTS         => 'Disable Quest button',
    PLAYER_OPTION_NAVBAR_DISABLE_META_MATTER    => 'Disable MetaMatter button',

    PLAYER_OPTION_UNIVERSE_OLD                  => 'Use the old view of the "Survey of the Universe"',
    PLAYER_OPTION_UNIVERSE_DISABLE_COLONIZE     => 'Disable Colonization Button',
    PLAYER_OPTION_DESIGN_DISABLE_BORDERS        => 'Disable table borders (if any)',
    PLAYER_OPTION_TECH_TREE_TABLE               => 'View Technology Tree as table (old view)',
    PLAYER_OPTION_FLEET_SHIP_SELECT_OLD         => 'Use old fleet selection view',
    PLAYER_OPTION_FLEET_SHIP_HIDE_SPEED         => 'Do not show ship speed',
    PLAYER_OPTION_FLEET_SHIP_HIDE_CAPACITY      => 'Do not show ship capacity',
    PLAYER_OPTION_FLEET_SHIP_HIDE_CONSUMPTION   => 'Do not show ship fuel consumption',
    PLAYER_OPTION_TUTORIAL_DISABLED             => 'Disable tutorial',
    PLAYER_OPTION_TUTORIAL_WINDOWED             => 'Show tutorial in popup window',
    PLAYER_OPTION_TUTORIAL_CURRENT              => 'Reset tutorial - tutorial will starts from begin',

    PLAYER_OPTION_PLANET_SORT_INVERSE           => 'Reverse order',
    PLAYER_OPTION_BUILD_AUTOCONVERT_HIDE        => 'Hide autoconvert button',

    PLAYER_OPTION_SOUND_ENABLED                 => 'Enable game sounds',
    PLAYER_OPTION_ANIMATION_DISABLED            => 'Disable animation effects',
    PLAYER_OPTION_PROGRESS_BARS_DISABLED        => 'Disable progress bars',
  ],

  'opt_chk_skin' => 'Use skin',
  'opt_adm_title' => 'Administration options',
  'opt_adm_planet_prot' => 'Planetary protection',
  'thanksforregistry' => 'Thanks for registering.<br />After a few minutes you will receive your message with a password.',
  'general_settings' => 'General settings',
  'skins_example' => 'Skin',

  'opt_avatar' => 'Avatar',
  'opt_avatar_remove' => 'Remove avatar',
  'opt_avatar_search' => 'Search in Google',
  'opt_upload' => 'Upload',

  'opt_msg_avatar_removed' => 'Avatar succesfully removed',
  'opt_msg_avatar_uploaded' => 'Avatar succesfully changed',
  'opt_msg_avatar_error_delete' => 'Error deleting avatar file. Please, contact server Administration',
  'opt_msg_avatar_error_writing' => 'Error saving avatar file. Please, contact server Administration',
  'opt_msg_avatar_error_upload' => 'Error loading avatar image %1. Please, contact server Administration',
  'opt_msg_avatar_error_unsupported' => 'Uploaded image format not supported. Only supported JPG, GIF, PNG up to 200KB',

  'untoggleip' => 'Disable IP check',
  'untoggleip_tip' => 'Check IP means that you will not be able to log in under his own name with two different IP. Testing gives you the advantage in security!',
  'galaxyvision_options' => 'Universe',
  'spy_cant' => 'Number of probes',
  'spy_cant_tip' => 'Number of probes to be sent when you follow someone for.',
  'tooltip_time' => 'Delay before show tooltip',
  'mess_ammount_max' => 'The number of maximum fleet communications',
  'seconds' => 'Second(s)',
  'shortcut' => 'Quick access',
  'show' => 'Show',
  'write_a_messege' => 'Write a message',
  'spy' => 'Espionage',
  'add_to_buddylist' => 'Add as friend',
  'attack_with_missile' => 'Missile attack',
  'show_report' => 'View report',
  'delete_vacations' => 'Account management',
  'mode_vacations' => 'Turn vacation',
  'vacations_tip' => 'Vacation mode is to protect the planet while you\'re away.',
  'deleteaccount' => 'Disable Account',
  'deleteaccount_tip' => 'Account will be deleted after 45 days of no login.',
  'deleteaccount_on' => 'If no activity this profile would be deleted on',
  'save_settings' => 'Save the changes',
  'exit_vacations' => 'Exit leave',
  'Vaccation_mode' => 'Vacation mode is enabled. It runs until: ',
  'You_cant_exit_vmode' => 'You can not exit leaves until time expires',
  'Error' => 'Error',
  'cans_resource' => 'Stop resource extraction on planets',
  'cans_reseach' => 'Stop research on planets',
  'cans_build' => 'Stop construction on the planets',
  'cans_fleet_build' => 'Stop the construction of Ships and Defenses',
  'cans_fly_fleet2' => 'Alien fleet approaches ... You can go on vacation',
  'vacations_exit' => 'Vacation mode is disabled',
  'select_skin_path' => 'SELECT',
  'opt_language' => 'Interface language',
  'opt_compatibility' => 'Compatibility - old interfaces',
  'opt_compat_structures' => 'The old interface construction',
  'opt_vacation_err_your_fleet' => 'Not to leave until the flight is at least one of your fleet',
  'opt_vacation_err_building' => 'You are building or explore on %s and therefore you cannot leave on vacation',
  'opt_vacation_err_research' => 'Your scientists do some research and therefore you cannot leave on vacation',
  'opt_vacation_err_que' => 'There are research ongoing and/or some planet ques is not empty so you can not leave to vacation. Use Empire overview to find what happening',
  'opt_vacation_err_timeout' => 'Vacancy timeout not reached',
  'opt_vacation_next' => 'Next vacancy would be available after',
  'opt_vacation_min' => 'a minimum of',
  'succeful_changepass' => '',

  'opt_time_diff_clear' => 'Measure difference between time on player side and time on server',
  'opt_time_diff_manual' => 'Set time difference manually',
  'opt_time_diff_explain' => 'When time difference set right clocks "Time on player" in navbar should click second to second with clocks on player\'s device<br />
  Usually game automatically detects time difference right. However when time zone is worng on player\'s device or player used several devices for playing or when
  internet connection is bad you should set time difference manually',

  'opt_custom' => [
    'opt_uni_avatar_user' => 'Show user avatar',
    'opt_uni_avatar_ally' => 'Show Ally logo',
    'opt_int_struc_vertical' => 'Vertical structures que',
    'opt_int_navbar_resource_force' => 'Always show resourcebar',
    'opt_int_overview_planet_columns' => 'Column count in planet list',
    'opt_int_overview_planet_columns_hint' => '0 - calculate by maximum row count',
    'opt_int_overview_planet_rows' => 'Maximum row count in planet list',
    'opt_int_overview_planet_rows_hint' => 'Ignored if there is column count',
  ],

  'opt_mail_optional_description' => 'Personal messages from other players and notifications about internal game events (like combat reports, expedition reports etc) will be sent to this e-mail',
  'opt_mail_permanent_description' => 'Your game account linked permanently to this e-mail. All system notices (like password change confirmation) will be sent to this address. You can enter this email only once',

  'opt_account_name' => 'You login<br />Login is used to enter game. Usually this is email you entered on registration',
  'opt_game_user_name' => 'Name in the game (nickname)<br />Other players in game will see your nickname - not your login',

  'opt_universe_title' => 'Universe',

  'option_fleets' => 'Fleets',
  'option_fleet_send' => 'Fleet send',

  'option_change_nick_disabled' => 'Player nickname change forbidden by server settings',

  'opt_ignores' => 'Игнор-лист',
  'opt_unignore_do' => 'Удалить из игнор-листа',
  'opt_ignore_list_empty' => 'Ваш игнор-лист пуст',

];
