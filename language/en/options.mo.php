<?php

/*
#############################################################################
#  Filename: options.mo
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
* @version 39a6.17
*
*/

/**
* DO NOT CHANGE
*/

if (!defined('INSIDE')) die();

//$lang = array_merge($lang,
//$lang->merge(
$a_lang_array = (array(
  'opt_birthday' => 'Birthday',

  'opt_header' => 'User options',
  'opt_messages' => 'Automatic alerts',
  'opt_msg_saved' => 'Options succesfully saved',
  'opt_msg_name_changed' => 'Username sucessfully changed.<br /><a href="login.php" target="_top">Back</a>',
  'opt_msg_pass_changed' => 'Password sucessfully changed.<br /><a href="login.php" target="_top">Back</a>',
  'opt_err_pass_wrong' => 'Wrong old password. Password was not changed',
  'opt_err_pass_unmatched' => 'New password confirmation is not identical to new password. Password was not changed',

  'opt_msg_name_change_err_used_name' => 'Someone else already owns this name',
  'opt_msg_name_change_err_no_dm' => 'There is not enough DM to change name',

  'username_old' => 'Old name',
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
  'opt_lst_ord' => 'View of the universe:',
  'opt_lst_ord0' => 'Time of colonization',
  'opt_lst_ord1' => 'Coordinates',
  'opt_lst_ord2' => 'Alphabetical order',
  'opt_lst_ord3' => 'Maximum fields',
  'opt_lst_cla' => 'Arrange by:',
  'opt_lst_cla0' => 'Ascending Order',
  'opt_lst_cla1' => 'Descending Order',
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
  'galaxyvision_options' => 'Configuring Galaxy',
  'spy_cant' => 'Number of probes',
  'spy_cant_tip' => 'Number of probes to be sent when you follow someone for.',
  'tooltip_time' => 'Show ToolTips',
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

  'opt_int_options' => 'Interface Settings',

  'opt_time_diff_clear' => 'Measure difference between local (client) and server time',

  'opt_custom' => array(
    'opt_uni_avatar_user' => 'Show user avatar',
    'opt_uni_avatar_ally' => 'Show Ally logo',
    'opt_int_struc_vertical' => 'Vertical structures que',
    'opt_int_navbar_resource_force' => 'Always show planet navbar',
    'opt_int_overview_planet_columns' => 'Column count in planet list',
    'opt_int_overview_planet_columns_hint' => '0 - calculate by maximum row count',
    'opt_int_overview_planet_rows' => 'Maximum row count in planet list',
    'opt_int_overview_planet_rows_hint' => 'Ignored if there is column count',
  ),
));
