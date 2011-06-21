<?php
/**
*
* system [Russian]
*
* @package language
* @version $Id$
*
*/

/**
* DO NOT CHANGE
*/
if (!defined('INSIDE'))
{
  exit;
}

if (empty($lang) || !is_array($lang))
{
  $lang = array();
}

// System-wide localization

$lang = array_merge($lang, array(
  'sys_empire'          => '�������',
  'VacationMode'			=> "���� ������������ �������, ��� ��� �� � �������",
  'sys_moon_destruction_report' => "������ ���������� ����",
  'sys_moon_destroyed' => "���� ����� ������ ��������� ������ �������������� �����, ������� ��������� ����! ",
  'sys_rips_destroyed' => "���� ����� ������ ��������� ������ �������������� �����, �� � �������� ��������� �� ���������� ��� ����������� ���� ������ �������. �� �������������� ����� ���������� �� ������ ����������� � ��������� ��� ����.",
  'sys_rips_come_back' => "���� ����� ������ �� ����� ���������� �������, ���� ������� ����� ���� ����. ��� ���� ������������ �� ��������� ����.",
  'sys_chance_moon_destroy' => "��������� ������� �����������: ",
  'sys_chance_rips_destroy' => "��������� ���������� �����������: ",

  'sys_day' => "����",
  'sys_hrs' => "�����",
  'sys_min' => "�����",
  'sys_sec' => "������",
  'sys_day_short' => "�",
  'sys_hrs_short' => "�",
  'sys_min_short' => "�",
  'sys_sec_short' => "�",

  'sys_ask_admin' => '������� � ����������� ���������� �� ������',

  'sys_wait'      => '������ �����������. ����������, ���������.',

  'sys_fleets'       => '�����',
  'sys_expeditions'  => '����������',
  'sys_fleet'        => '����',
  'sys_expedition'   => '����������',
  'sys_event_next'   => '��������� �������:',
  'sys_event_arrive' => '��������',
  'sys_event_stay'   => '�������� �������',
  'sys_event_return' => '��������',

  'sys_total'           => "�����",
  'sys_need'				=> '�����',
  'sys_register_date'   => '���� �����������',

  'sys_attacker' 		=> "���������",
  'sys_defender' 		=> "�������������",

  'COE_combatSimulator' => "��������� ���",
  'COE_simulate'        => "������ ����������",
  'COE_fleet'           => "����",
  'COE_defense'         => "�������",
  'sys_coe_combat_start'=> "����� ���������� �����������",
  'sys_coe_combat_end'  => "���������� ���",
  'sys_coe_round'       => "�����",

  'sys_coe_attacker_turn'=> '��������� ������ �������� ����� ��������� %1$s. ���� �������������� ��������� %2$s ���������<br />',
  'sys_coe_defender_turn'=> '������������� ������ �������� ����� ��������� %1$s. ���� ���������� ��������� %2$s ���������<br /><br /><br />',
  'sys_coe_outcome_win'  => '������������� ������� �����!<br />',
  'sys_coe_outcome_loss' => '��������� ������� �����!<br />',
  'sys_coe_outcome_loot' => '�� �������� %1$s �������, %1$s ����������, %2$s ��������<br />',
  'sys_coe_outcome_draw' => '��� ���������� ������.<br />',
  'sys_coe_attacker_lost'=> '��������� ������� %1$s ������.<br />',
  'sys_coe_defender_lost'=> '������������� ������� %1$s ������.<br />',
  'sys_coe_debris_left'  => '������ �� ���� ���������������� ����������� ��������� %1$s ������� � %2$s ����������.<br /><br />',
  'sys_coe_moon_chance'  => '���� ��������� ���� ���������� %1$s%%<br />',
  'sys_coe_rw_time'      => '����� ��������� �������� %1$s ������<br />',

  'sys_resources'       => "�������",
  'sys_ships'           => "�������",
  'sys_metal'          => "������",
  'sys_metal_sh'       => "�",
  'sys_crystal'        => "��������",
  'sys_crystal_sh'     => "�",
  'sys_deuterium'      => "��������",
  'sys_deuterium_sh'   => "�",
  'sys_energy'         => "�������",
  'sys_energy_sh'      => "�",
  'sys_dark_matter'    => "������ �������",
  'sys_dark_matter_sh' => "��",

  'sys_reset'           => "��������",
  'sys_send'            => "���������",
  'sys_characters'      => "��������",
  'sys_back'            => "�����",
  'sys_return'          => "���������",
  'sys_delete'          => "�������",
  'sys_writeMessage'    => "�������� ���������",
  'sys_hint'            => "���������",

  'sys_alliance'        => "������",
  'sys_player'          => "�����",
  'sys_coordinates'     => "����������",

  'sys_online'          => "������",
  'sys_offline'         => "�������",
  'sys_status'          => "������",

  'sys_universe'        => "���������",
  'sys_goto'            => "�������",

  'sys_time'            => "�����",
  'sys_temperature'		=> '�����������',

  'sys_no_task'         => "��� �������",

  'sys_affilates'       => "������������ ������",

  'sys_fleet_arrived'   => "���� ������",

  'sys_planet_type' => array(
    PT_PLANET => '�������', 
    PT_DEBRIS => '���� ��������', 
    PT_MOON   => '����',
  ),

  'sys_planet_type_sh' => array(
    PT_PLANET => '(�)',
    PT_DEBRIS => '(�)',
    PT_MOON   => '(�)',
  ),

  'sys_capacity' 			=> '����������������',
  'sys_cargo_bays' 		=> '�����',

  'sys_supernova' 		=> '����������',
  'sys_server' 			=> '������',

  'sys_unbanned'			=> '�������������',

  'sys_date_time'			=> '���� � �����',
  'sys_from_person'	   => '�� ����',

  'sys_from'		  => '�',

// Resource page
  'res_planet_production' => '������������ �������� �� �������',
  'res_basic_income' => '������������ ������������',
  'res_total' => '�����',
  'res_calculate' => '����������',
  'res_hourly' => '� ���',
  'res_daily' => '�� ����',
  'res_weekly' => '�� ������',
  'res_monthly' => '�� �����',
  'res_storage_fill' => '������������� ���������',
  'res_hint' => '<ul><li>������������ �������� <100% �������� �������� �������. ��������� �������������� �������������� ��� ��������� ������������ ��������<li>���� ���� ������������ ����� 0% ������ ����� �� ����� �� ������� � ��� ����� �������� ��� ������<li>��� �� ��������� ������ ��� ���� ������� ����� ����������� ����-���� � �������� �������. �������� ������ ������������ ��� ����� ������ �� �������</ul>',

// Build page
  'bld_destroy' => '����������',
  'bld_create'  => '���������',

// Imperium page
  'imp_imperator' => "���������",
  'imp_overview' => "����� �������",
  'imp_fleets' => "����� � ������",
  'imp_production' => "������������",
  'imp_name' => "��������",
  'sys_fields' => "�������",

// Cookies
  'err_cookie' => "������! ���������� �������������� ������������ �� ���������� � cookie. <a href='login." . PHP_EX . "'>�������</a> � ���� ��� <a href='reg." . PHP_EX . "'>�����������������</a>.",

// Supported languages
  'ru'              	  => '�������',
  'en'              	  => '����������',

  'sys_vacation'        => '�� �� � ������� ��',
  'sys_vacation_leave'  => '� ��� �������� - ����� �� �������!',
  'sys_level'           => '�������',

  'sys_yes'             => '��',
  'sys_no'              => '���',

  'sys_on'              => '�������',
  'sys_off'             => '��������',

  'sys_confirm'         => '�����������',
  'sys_save'            => '���������',
  'sys_create'          => '�������',
  'sys_write_message'   => '�������� ���������',

// top bar
  'top_of_year' => '����',
  'top_online'			=> '������ on-line',

  'sys_first_round_crash_1'	=> '������� � ����������� ������ �������.',
  'sys_first_round_crash_2'	=> '��� �������� ��� �� ��� ��������� � ������ ������ ���.',

  'sys_ques' => array(
    QUE_STRUCTURES => '������',
    QUE_HANGAR     => '�����',
    QUE_RESEARCH   => '������������',
  ),

  'eco_que_empty' => '������� �����',
  'eco_que_clear' => '�������� �������',
  'eco_que_trim'  => '�������� ���������',

  'sys_overview'			=> '�����',
  'mod_marchand'			=> '��������',
  'sys_moon'			=> '����',
  'sys_planet'			=> '�������',
  'sys_error'			=> '������',
  'sys_done'				=> '������',
  'sys_no_vars'			=> '������ ������������� ����������, ���������� � �������������!',
  'sys_attacker_lostunits'		=> '��������� ������� %s ������.',
  'sys_defender_lostunits'		=> '������������� ������� %s ������.',
  'sys_gcdrunits' 			=> '������ �� ���� ���������������� ����������� ��������� %s %s � %s %s.',
  'sys_moonproba' 			=> '���� ��������� ���� ����������: %d %% ',
  'sys_moonbuilt' 			=> '��������� �������� ������� �������� ����� ������� � ��������� ����������� � ���������� ����� ���� %s [%d:%d:%d] !',
  'sys_attack_title'    		=> '%s. ��������� ��� ����� ���������� �������::',
  'sys_attack_attacker_pos'      	=> '��������� %s [%s:%s:%s]',
  'sys_attack_techologies' 	=> '����������: %d %% ����: %d %% �����: %d %% ',
  'sys_attack_defender_pos' 	=> '������������� %s [%s:%s:%s]',
  'sys_ship_type' 			=> '���',
  'sys_ship_count' 		=> '���-��',
  'sys_ship_weapon' 		=> '����������',
  'sys_ship_shield' 		=> '����',
  'sys_ship_armour' 		=> '�����',
  'sys_destroyed' 			=> '���������',
  'sys_attack_attack_wave' 	=> '��������� ������ �������� ����� ��������� %s �� ��������������. ���� �������������� ��������� %s ���������.',
  'sys_attack_defend_wave'		=> '������������� ������ �������� ����� ��������� %s �� ����������. ���� ���������� ��������� %s ���������.',
  'sys_attacker_won' 		=> '��������� ������� �����!',
  'sys_defender_won' 		=> '������������� ������� �����!',
  'sys_both_won' 			=> '��� ���������� ������!',
  'sys_stealed_ressources' 	=> '�� �������� %s ������� %s %s ��������� %s � %s ��������.',
  'sys_rapport_build_time' 	=> '����� ��������� �������� %s ������',
  'sys_mess_tower' 		=> '���������',
  'sys_mess_attack_report' 	=> '������ ������',
  'sys_spy_maretials' 		=> '����� ��',
  'sys_spy_fleet' 			=> '����',
  'sys_spy_defenses' 		=> '�������',
  'sys_mess_qg' 			=> '������������ ������',
  'sys_mess_spy_report' 		=> '��������� ������',
  'sys_mess_spy_lostproba' 	=> '����������� ����������, ���������� ��������� %d %% ',
  'sys_mess_spy_control' 		=> '�������������',
  'sys_mess_spy_activity' 		=> '��������� ����������',
  'sys_mess_spy_ennemyfleet' 	=> '����� ���� � �������',
  'sys_mess_spy_seen_at'		=> '��� ��������� ����� �������',
  'sys_mess_spy_destroyed'		=> '��������� ������� ��� ���������',
  'sys_object_arrival'		=> '������ �� �������',
  'sys_stay_mess_stay' => '�������� ����',
  'sys_stay_mess_start' 		=> '��� ���� ������ �� �������',
  'sys_stay_mess_back'		=> '��� ���� �������� ',
  'sys_stay_mess_end'		=> ' � ��������:',
  'sys_stay_mess_bend'		=> ' � �������� ��������� �������:',
  'sys_adress_planet' 		=> '[%s:%s:%s]',
  'sys_stay_mess_goods' 		=> '%s : %s, %s : %s, %s : %s',
  'sys_colo_mess_from' 		=> '�����������',
  'sys_colo_mess_report' 		=> '����� � �����������',
  'sys_colo_defaultname' 		=> '�������',
  'sys_colo_arrival' 		=> '���� ��������� ��������� ',
  'sys_colo_maxcolo' 		=> ', �� �������������� ������� ������, ���������� ������������ ����� ������� ��� ������ ������ �����������',
  'sys_colo_allisok' 		=> ', � ��������� �������� ��������� ����� �������.',
  'sys_colo_badpos'  			=> ', � ��������� ����� ����� ���� �������� ��� ����� �������. ������ ����������� ������������ ������� �� ������� ��������.',
  'sys_colo_notfree' 			=> ', � ��������� �� ����� ������� � ���� �����������. ��� ��������� ��������� ������ ������� ��������� ���������������.',
  'sys_colo_no_colonizer'     => '�� ����� ��� ������������',
  'sys_colo_planet'  		=> ' ������� ��������������!',
  'sys_expe_report' 		=> '����� ����������',
  'sys_recy_report' 		=> '��������� ����������',
  'sys_expe_blackholl_1' 		=> '��� ���� ����� � ������ ���� � �������� �������!',
  'sys_expe_blackholl_2' 		=> '��� ���� ����� � ������ ���� � ��������� �������!',
  'sys_expe_nothing_1' 		=> '��� ������������� ����� ����������� ���������� ������! � ���� ���������� ������ ������� ����� ��������������� �������.',
  'sys_expe_nothing_2' 		=> '��� ������������� ������ �� ����������!',
  'sys_expe_found_goods' 		=> '��� ������������� ����� �������, ������� ������!<br>�� �������� %s %s, %s %s � %s %s',
  'sys_expe_found_ships' 		=> '��� ������������� ����� ���������� ����� ����!<br>�� ��������: ',
  'sys_expe_back_home' 		=> '��� ���� ������������ �������.',
  'sys_mess_transport' 		=> '���������',
  'sys_tran_mess_owner' 		=> '���� �� ����� ������ ��������� ������� %s %s � ���������� %s %s, %s  %s � %s %s.',
  'sys_tran_mess_user'  		=> '��� ���� ������������ � ������� %s %s ������ �� %s %s � �������� %s %s, %s  %s � %s %s.',
  'sys_mess_fleetback' 		=> '�����������',
  'sys_tran_mess_back' 		=> '���� �� ����� ������ ������������ �� ������� %s %s.',
  'sys_recy_gotten' 		=> '���� �� ����� ������ ����� %s %s � %s %s ������������ �� �������.',
  'sys_notenough_money' 		=> '��� �� ������� ��������, ����� ���������: %s. � ��� ������: %s %s , %s %s � %s %s. ��� ������������� ����������: %s %s , %s %s � %s %s.',
  'sys_nomore_level'		=> '�� ������ �� ������ ���������������� ���. ��� �������� ����. ������ ( %s ).',
  'sys_buildlist' 			=> '������ ��������',
  'sys_buildlist_fail' 		=> '��� ��������',
  'sys_gain' 			=> '������: ',
  'sys_perte_attaquant' 		=> '��������� �������',
  'sys_perte_defenseur' 		=> '������������� �������',
  'sys_debris' 			=> '�������: ',
  'sys_noaccess' 			=> '� ������� ��������',
  'sys_noalloaw' 			=> '��� ������ ������ � ��� ����!',
  'sys_governor'        => '����������',

  // News page & a bit of imperator page
  'news_title'      => '�������',
  'news_none'       => '��� ��������',
  'news_new'        => '�����',
  'news_future'     => '�����',
  'news_more'       => '���������...',
                    
  'news_date'       => '����',
  'news_announce'   => '����������',
  'news_detail_url' => '������ �� �����������',
  'news_mass_mail'  => '��������� ������� ���� �������',

  'news_total'      => '����� ��������: ',
                    
  'news_add'        => '�������� �������',
  'news_edit'       => '������������� �������',
  'news_copy'       => '����������� �������',
  'news_mode_new'   => '�����',
  'news_mode_edit'  => '��������������',
  'news_mode_copy'  => '�����',

  'sys_administration' => '������������� �������',

  // Shortcuts
  'shortcut_title'     => '��������',
  'shortcut_none'      => '��� ��������',
  'shortcut_new'       => '�����',
  'shortcut_text'      => '�����',

  'shortcut_add'       => '�������� ��������',
  'shortcut_edit'      => '������������� ��������',
  'shortcut_copy'      => '����������� ��������',
  'shortcut_mode_new'  => '�����',
  'shortcut_mode_edit' => '��������������',
  'shortcut_mode_copy' => '�����',

  // Missile-related
  'mip_h_launched'			=> '������ ������������ �����',
  'mip_launched'				=> '�������� ������������ �����: <b>%s</b>!',

  'mip_no_silo'				=> '������������ ������� �������� ���� �� ������� <b>%s</b>.',
  'mip_no_impulse'			=> '���������� ����������� ���������� ���������.',
  'mip_too_far'				=> '������ �� ����� ������ ��� ������.',
  'mip_planet_error'			=> '������ - ������ ����� ������� �� ����� ����������',
  'mip_no_rocket'				=> '������������ ����� � ����� ��� ���������� �����.',
  'mip_hack_attempt'			=> ' �� �� �����? ��� ���� ����� ������ � ������ �������. ip ����� � ����� � �������.',

  'mip_all_destroyed' 		=> '��� ������������ ������ ���� ���������� ��������-��������������<br>',
  'mip_destroyed'				=> '%s ����������� ����� ���� ���������� ��������-��������������.<br>',
  'mip_defense_destroyed'	=> '���������� ��������� �������������� ����������:<br />',
  'mip_recycled'				=> '������������ �� �������� �������� ����������: ',
  'mip_no_defense'			=> '�� ��������� ������� �� ���� ������!',

  'mip_sender_amd'			=> '�������-����������� ������',
  'mip_subject_amd'			=> '�������� �����',
  'mip_body_attack'			=> '����� ������������� �������� (%1$s ��.) � ������� %2$s <a href="galaxy.php?mode=3&galaxy=%3$d&system=%4$d&planet=%5$d">[%3$d:%4$d:%5$d]</a> �� ������� %6$s <a href="galaxy.php?mode=3&galaxy=%7$d&system=%8$d&planet=%9$d">[%7$d:%8$d:%9$d]</a><br><br>',
  
  // Misc
  'sys_game_rules' => '������� ����',
  'sys_max' => '����',
  'sys_banned_msg' => '�� ��������. ��� ��������� ���������� ������� <a href="banned.php">����</a>. ���� ��������� ���������� ��������: ',
  'sys_total_time' => '����� �����',

  // Universe
  'uni_moon_of_planet' => '�������',

  // Combat reports
  'cr_view_title'  => "�������� ������ �������",
  'cr_view_button' => "����������� �����",
  'cr_view_prompt' => "������� ���",
  'cr_view_my'     => "��� ������ ������",
  'cr_view_hint'   => '<ul><li>���� ������ ������ ����� ����������, ������� �� ������ "��� ������ ������" � ���������</li><li>��� ������� ������ ����������� � ��� ��������� ������ � �������� ������������������� 32 ���� � �������� ���������� ��������</li></ul>',

  // Dark Matter
  'sys_dark_matter_text' => '<h2>��� ����� ������ �������?</h2>
    ������ ������� - ��� ������� ������, �� ���� ������� � ���� �� ������ ��������� ��������� ��������:
    <ul><li>���������� ���� ��� �������� �� ������</li>
    <li>�������� �������� �����</li>
    <li>������� �������� �/� ��������</li>
    <li>�������� ��������</li></ul>
    <h2>��� ����� ������ �������?</h2>
    �� ��������� ������ ������� � �������� ����: ������� ���� �� ����� �� ����� ������� � ��������� ������.
    ��� �� ������ ����������������� ���������� ����� �������� ��.',
  'sys_dark_matter_purchase' => '����� ���� �� ������ ���������� �� �� WebMoney.',
  'sys_dark_matter_get'  => '�������� ��� ������, ��� �� ������ �����������.',

  // Officers
  'off_no_points'        => '� ��� ������������ ����� �������!',
  'off_recruited'        => '������ ��� �����! <a href="officer.php">�����</a>',
  'off_tx_lvl'           => '������� �������: ',
  'off_points'           => '�������� ����� �������: ',
  'off_maxed_out'        => '������������ �������',
  'off_not_available'    => '������ ��� ��� �� ��������!',
  'off_hire'             => '������ ��',
  'off_dark_matter_desc' => 'Ҹ���� ������� - ������������ ������������ �������� ����������� �������, �� ������� ���������� 23% ����� ���������. �� �� ����� �������� ����������� ���������� �������. ��-�� �����, � ��� �� ��-�� ����������, ��������� � � �������, ������ ������� ������� ����� ������.',
  'off_dark_matter_hint' => '��� ������ ���� ���������� ����� ������ �������� � ����������.',

  // Fleet
  'flt_gather_all'    => '������ �������',

  // Ban system
  'ban_title'      => '׸���� ������',
  'ban_name'       => '���',
  'ban_reason'     => '������� ����������',
  'ban_from'       => '���� ����������',
  'ban_to'         => '���� ����������',
  'ban_by'         => '�����',
  'ban_no'         => '��� ��������������� �������',
  'ban_thereare'   => '�����',
  'ban_players'    => '�������������',
  'ban_banned'     => '������� �������������: ',

  // Contacts
  'ctc_title' => '�������������',
  'ctc_intro' => '����� �� ������ ������ ���� ��������������� � ���������� ���� ��� �������� �����',
  'ctc_name'  => '���',
  'ctc_rank'  => '������',
  'ctc_mail'  => 'eMail',

  // Records page
  'rec_title'  => '������� ���������',
  'rec_build'  => '���������',
  'rec_specb'  => '����������� ���������',
  'rec_playe'  => '�����',
  'rec_defes'  => '�������',
  'rec_fleet'  => '����',
  'rec_techn'  => '����������',
  'rec_level'  => '�������',
  'rec_nbre'   => '����������',
  'rec_rien'   => '-',

  // Credits page
  'cred_link'    => '��������',
  'cred_site'    => '����',
  'cred_forum'   => '�����',
  'cred_credit'  => '������',
  'cred_creat'   => '��������',
  'cred_prog'    => '�����������',
  'cred_master'  => '�������',
  'cred_design'  => '��������',
  'cred_web'     => '���������',
  'cred_thx'     => '�������������',
  'cred_based'   => '������ ��� �������� XNova',
  'cred_start'   => '����� ������ XNova',

  // Built-in chat
  'chat_common'   => '����� ���',
  'chat_ally'     => '��� �������',
  'chat_history'  => '�������',
  'chat_message'  => '���������',
  'chat_send'     => '���������',
  'chat_page'     => '��������',
  'chat_timeout'  => '��� �������� ��-�� ����� ������������. �������� ��������.',

  // quests
  'qst_quests'               => '������',
  'qst_msg_complete_subject' => '����� ��������',
  'qst_msg_complete_body'    => '�� ��������� ����� "%s".',
  'qst_msg_your_reward'      => '���� �������:',

  // Messages
  'msg_from_admin' => '������������� ���������',
  'msg_class' => array(
    MSG_TYPE_OUTBOX => '������������ ���������',
    MSG_TYPE_SPY => '��������� ������',
    MSG_TYPE_PLAYER => '��������� �� �������',
    MSG_TYPE_ALLIANCE => '��������� �������',
    MSG_TYPE_COMBAT => '������� ������',
    MSG_TYPE_RECYCLE => '������ �����������',
    MSG_TYPE_TRANSPORT => '�������� �����',
    MSG_TYPE_ADMIN => '��������� �������������',
    MSG_TYPE_EXPLORE => '������ ����������',
    MSG_TYPE_QUE => '��������� ������� ��������',
    MSG_TYPE_NEW => '��� ���������',
  ),

  'msg_que_research_from'    => '������-����������������� ��������',
  'msg_que_research_subject' => '����� ����������',
  'msg_que_research_message' => '����������� ����� ���������� \'%s\'. ����� ������� - %d',

  'msg_que_planet_from'    => '����������',

  'msg_que_hangar_subject' => '������ �� ����� ���������',
  'msg_que_hangar_message' => "����� �� %s ��������� ������",

  'msg_que_built_subject'   => '����������� ������ ���������',
  'msg_que_built_message'   => "��������� ������������� ������ '%2\$s' �� %1\$s. ��������� �������: %3\$d\r\n",
  'msg_que_destroy_message' => "��������� ���������� ������ '%2\$s' �� %1\$s. ��������� �������: %3\$d\r\n",

  // Arrays
  'sys_game_mode' => array(
    GAME_SUPERNOVA => '����������',
    GAME_OGAME     => '�����',
  ),

  'months' => array(
    '01'=>'������',
    '02'=>'�������',
    '03'=>'�����',
    '04'=>'������',
    '05'=>'���',
    '06'=>'����',
    '07'=>'����',
    '08'=>'�������',
    '09'=>'��������',
    '10'=>'�������',
    '11'=>'������',
    '12'=>'�������'
  ),

  'weekdays' => array(
    0 => '�����������',
    1 => '�����������',
    2 => '�������',
    3 => '�����',
    4 => '�������',
    5 => '�������',
    6 => '�������'
  ),

  'user_level' => array(
    0 => '�����',
    1 => '���������',
    2 => '��������',
    3 => '�������������',
  ),

));

// You CAN NOT merge those array_merge with previous one!
$lang = array_merge($lang, array(
  'sys_lessThen15min'   => '&lt; 15 ' . $lang['sys_min_short'],

  'user_level_shortcut' => array(
    0 => $lang['user_level'][0][0],
    1 => $lang['user_level'][1][0],
    2 => $lang['user_level'][2][0],
    3 => $lang['user_level'][3][0],
  ),

// Compatibility layer - to work with old files
/*
  'sys_resource' => array(
    1 => $lang['sys_metal'],
    2 => $lang['sys_crystal'],
    3 => $lang['sys_deuterium'],
    4 => $lang['sys_dark_matter'],
    5 => $lang['sys_energy'],
  ),

  'sys_planet_type1' => $lang['sys_planet_type'][1],
  'sys_planet_type2' => $lang['sys_planet_type'][2],
  'sys_planet_type3' => $lang['sys_planet_type'][3],

  'sys_planet_type_sh1' => $lang['sys_planet_type_sh'][1],
  'sys_planet_type_sh2' => $lang['sys_planet_type_sh'][2],
  'sys_planet_type_sh3' => $lang['sys_planet_type_sh'][3],
*/
));

?>
