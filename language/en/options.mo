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
* @version 31a9
*
*/

/**
* DO NOT CHANGE
*/

if (!defined('INSIDE')) die();

$lang = array_merge($lang, array(
  'opt_header' => 'User options',
  'opt_messages' => 'Automatic alerts',
  'opt_msg_saved' => 'Options succesfully saved',
  'opt_msg_name_changed' => 'Username sucessfully changed.<br /><a href="login.php" target="_top">Back</a>',
  'opt_msg_pass_changed' => 'Password sucessfully changed.<br /><a href="login.php" target="_top">Back</a>',
  'opt_err_pass_wrong' => 'Wrong old password. Password was not changed',
  'opt_err_pass_unmatched' => 'New password confirmation is not identical to new password. Password was not changed',
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
  'skins_example' => 'Skin<br>(for example C:/ogame/skin/)',

  'opt_avatar' => 'Avatar',
  'opt_avatar_remove' => 'Remove avatar',
  'opt_avatar_search' => 'Seatch in Google',
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
  'show_ally_logo' => 'Show logo alliances',
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
  'opt_vacation_err_building' => 'You are building or explore on %s and therefore cannot leave on vacation',
  'opt_vacation_min' => 'a minimum of',
  'succeful_changepass' => '',
));

?>
