<?php

if (!defined('INSIDE'))
{
  die("������� ������!");
}

global $sn_message_groups, $sn_message_class_list;
$lang['opt_custom'] = $lang['opt_custom'] === null ? array() : $lang['opt_custom'];
foreach($sn_message_groups['switchable'] as $option_id)
{
  $option_name = $sn_message_class_list[$option_id]['name'];
  $lang['opt_custom']["opt_{$option_name}"] = &$lang['msg_class'][$option_id];
}

$lang = array_merge($lang, array(
  'opt_header'           => '��������� ������������',

  'opt_messages'         => '�������������� �����������',
  'opt_msg_saved'        => '��������� ������� ��������',
  'opt_msg_name_changed' => '��� ������������ ������� ��������.<br /><a href="login.php" target="_top">�����</a>',
  'opt_msg_pass_changed' => '������ ������� �������.<br /><a href="login.php" target="_top">�����</a>',
  'opt_err_pass_wrong'   => '������������ ������� ������. ������ �� ��� �������',
  'opt_err_pass_unmatched' => '��������� ������ �� ��������� � ������������� ������. ������ �� ��� �������',

));

//
$lang['changue_pass']        = "������� ������";
$lang['Download']            = "��������";
$lang['Search']              = "�����";
$lang['succeful_changepass'] = "������ ������� ������.<br /><a href=\"login.php\" target=\"_top\">�����</a>";

//
$lang['userdata']			= "����������";
$lang['username']			= "���";
$lang['lastpassword']			= "������ ������";
$lang['newpassword']			= "����� ������<br>(���. 8 ��������)";
$lang['newpasswordagain']		= "��������� ����� ������";
$lang['emaildir']			= "����� e-mail";
$lang['emaildir_tip']			= "���� ����� ����� ���� ������ � ����� �����. ����� ������ ��������, ���� �� �� ��������� � ������� 7 ����.";
$lang['permanentemaildir']		= "�������� ����� e-mail";

$lang['opt_lst_ord']			= "����������� ������� ��:";
$lang['opt_lst_ord0']			= "������� �����������";
$lang['opt_lst_ord1']			= "�����������";
$lang['opt_lst_ord2']			= "����������� �������";
$lang['opt_lst_ord3']			= "���������� �����";
$lang['opt_lst_cla']			= "����������� ��:";
$lang['opt_lst_cla0']			= "�����������";
$lang['opt_lst_cla1']			= "��������";
$lang['opt_chk_skin']			= "������������ ����������";

// 	
$lang['opt_adm_title']			= "����� �����������������";
$lang['opt_adm_planet_prot']		= "������ ������";

// 	
$lang['thanksforregistry']		= "������� �� �����������.<br />����� ��������� ����� �� �������� ���� ��������� � �������.";
$lang['general_settings']		= "����� ���������";
$lang['skins_example']			= "����������<br>(�������� C:/ogame/skin/)";
$lang['avatar_example']			= "������<br>(�������� /img/avatar.jpg)";
$lang['untoggleip']			= "��������� ������� �������� �� IP";
$lang['untoggleip_tip']			= "�������� IP �������� ��, ��� �� �� ������� ����� ��� ����� ������ � ���� ������ IP. �������� ��� ��� ������������ � ������������!";

// 	
$lang['galaxyvision_options']		= "��������� ���������";
$lang['spy_cant']			= "���������� ������";
$lang['spy_cant_tip']			= "���������� ������, ������� ����� ������������, ����� �� ������ �� ���-�� �������.";
$lang['tooltip_time']			= "����� ������ ���������";
$lang['mess_ammount_max']		= "���������� ������������ ��������� �����";
$lang['show_ally_logo']			= "���������� ������� ��������";
$lang['seconds']			= "������(�/�)";

//	
$lang['shortcut']			= "������� ������";
$lang['show']				= "����������";
$lang['write_a_messege']		= "�������� ���������";
$lang['spy']				= "�������";
$lang['add_to_buddylist']		= "�������� � ������";
$lang['attack_with_missile']		= "�������� �����";
$lang['show_report']			= "����������� �����";

//	
$lang['delete_vacations']		= "���������� ��������";
$lang['mode_vacations']			= "�������� ����� �������";
$lang['vacations_tip']			= "����� ������� ����� ��� ������ ������ �� ����� ������ ����������.";
$lang['deleteaccount']			= "��������� �������";
$lang['deleteaccount_tip']		= "������� ����� ����� ����� 45 ���� ������������.";
$lang['save_settings']			= "��������� ���������";
$lang['exit_vacations']			= "����� �� ������ �������";
$lang['Vaccation_mode']			= "����� ������� �������. �� ��������� ��: ";
$lang['You_cant_exit_vmode']		= "�� �� ������ ����� �� ������ �������, ���� �� ������� ����������� �����";
$lang['Error']				= "������";
$lang['cans_resource']			= "���������� ������ �������� �� ��������";
$lang['cans_reseach']                   = "���������� ����������� �� ��������";
$lang['cans_build']                     = "���������� ������������� �� ��������";
$lang['cans_fleet_build']               = "���������� ��������� ����� � �������";
$lang['cans_fly_fleet2']                 = "����� ���� ������������... �� �� ������ ���� � ������";
$lang['vacations_exit']                 = "����� ������� ��������... �����������";

$lang['select_skin_path']		= "�������";

$lang['opt_language']         = '���� ����������';

$lang['opt_compatibility']    = '������������� - ������ �����������';
$lang['opt_compat_structures']= '������ ��������� ������������� ������';

$lang['opt_vacation_err_your_fleet'] = "������ ���� � ������ ���� � ������ ��������� ���� �� ���� ��� ����";
$lang['opt_vacation_err_building']   = "�� ���-�� ������� ��� ���������� � ������� �� ������ ���� � ������";
$lang['opt_vacation_min'] = '������� ��';

?>
