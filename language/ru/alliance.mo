<?php
if (!defined('INSIDE')) {
	die("���������� ������� ������!");
}

$lang['ali_dip_title'] = '����������';
$lang['ali_dip_negotiate'] = '����������';

$lang['ali_adm_msg_subject']   = '�������� �������';

$lang['ali_dip_offers_your']   = '���� �����������';
$lang['ali_dip_offers_to_you'] = '����������� ���';
$lang['ali_dip_offer_none']    = '��� �����������';
$lang['ali_dip_offer']         = '�����������';
$lang['ali_dip_offers']        = '�����������';
$lang['ali_dip_offer_new']     = '�������� � ����������';
$lang['ali_dip_offer_to_ally'] = '���������� �������';
$lang['ali_dip_offer_make']    = '������ ����������';
$lang['ali_dip_offer_answer']      = '������ �������� ���� �����������';
$lang['ali_dip_offer_deny_reason'] = '�� ��������� �����������'; // . ������� ������:
$lang['ali_dip_offer_to']          = '�������';
$lang['ali_dip_offer_from']        = '�� �������';

$lang['ali_dip_offer_deny']          = '��������� �����������';
$lang['ali_dip_offer_accept']        = '������� �����������';
$lang['ali_dip_offer_delete']        = '�������� �����������';

$lang['ali_dip_err_no_ally']          = '��� ������ �������';
$lang['ali_dip_err_wrong_offer']      = '������ ������� ����� �����������';
$lang['ali_dip_err_offer_none']       = '��� ������ �����������';
$lang['ali_dip_err_offer_same']       = '�� ��� ���������� � ���� �������� � ���������� %s';
$lang['ali_dip_err_offer_alien']      = '��� ����������� ������ �� ���!'; // hack
$lang['ali_dip_err_offer_accept_own'] = '������ ������� �� ������� ���� �����������!'; // hack
$lang['ali_dip_err_offer_empty']      = '�� ������� �����������'; // hack

$lang['ali_dip_relation_none']    = '��� ���������';
$lang['ali_dip_relation_change']  = '�� ������� ����������� �������';
$lang['ali_dip_relation_change_to']  = '�������� ��������� ��';
$lang['ali_dip_relation_accept']  = '������ ���� ����������� �������� ��������� ��';

$lang['ali_dip_relations'] = array(
  ALLY_DIPLOMACY_NEUTRAL       => '�����������',
  ALLY_DIPLOMACY_WAR           => '�����',
  ALLY_DIPLOMACY_PEACE         => '���',
  ALLY_DIPLOMACY_CONFEDERATION => '������������',
  ALLY_DIPLOMACY_FEDERATION    => '���������',
  ALLY_DIPLOMACY_UNION         => '�����������',
  ALLY_DIPLOMACY_MASTER        => '�������',
  ALLY_DIPLOMACY_SLAVE         => '�������'
);

$lang['ali_lessThen15min']    = '&lt; 15 �';

$lang['ali_confirm']          = '�����������';
$lang['ali_confirmation']     = '�������������';

$lang['ali_adm_disband']      = '���������� ������';
$lang['ali_adm_options']      = '��������� �������';
$lang['ali_adm_transfer']     = '�������� ������ ������';
$lang['ali_adm_return']       = '��������� � ���������� ��������';
$lang['ali_adm_kick']         = '��������� ������ �� �������';
$lang['ali_adm_kick_confirm'] = '�� �������� ��� ������ ��������� ������ �� ������?';
$lang['ali_adm_requests']     = '������';
$lang['ali_adm_newLeader']    = '�������� ������';
$lang['ali_adm_lastRank']     = '������ ������� ������������ ������!';

$lang['ali_adm_rights_title']       = '��������� ���� �������';
$lang['ali_adm_rights_rank_new']    = '����� ������';
$lang['ali_adm_rights_rank_delete'] = '������� ������';
$lang['ali_adm_rights_rank_none']   = '��� ������';
$lang['ali_adm_rights_rank_name']   = '������';
$lang['ali_adm_rights_mass_mail']   = '��������� ����� �������';
$lang['ali_adm_rights_view_online'] = '�������� on-line ������� ����������';
$lang['ali_adm_rights_helper']      = '�������� ����� (��� �������� ��������� ���� ����������)';
$lang['ali_adm_rights_legend']      = '����� �������';

$lang['ali_leaderRank']       = '����� �������';
$lang['ali_defaultRankName']  = '�������';

$lang['ali_make_title']       = '�������� �������';
$lang['ali_make_tag_length']  = '(�� 3 �� 8 ��������)';
$lang['ali_make_name_length'] = '(�� 35 ��������)';
$lang['ali_make_confirm']     = '������� ������';

$lang['ali_req_cancel']       = '������� ������';
$lang['ali_req_candidate']    = '��������';
$lang['ali_req_characters']   = '��������';
$lang['ali_req_date']         = '���� ������ ������';
$lang['ali_req_deny_msg']     = '���� ������ �� ���������� � ������ [%s] ���� ���������.<br>������� ������: "%s".<br>�� ������ ������� ������ � ����������� ����� ��� �������� � ������ ������.';
$lang['ali_req_deny_admin']   = '<font color=red>������ ��� ��������</font>. ������, ���� ������������ �� ������ ������ �� ����������, �� ������ �������� ���� �������';
$lang['ali_req_deny_reason']  = '��� ������ �� ���������� ��������';
$lang['ali_req_emptyList']    = '��� ������ ��� ������������';
$lang['ali_req_inAlly']       = '�� ��� ��������� ���������� �������.';
$lang['ali_req_make']         = '������ ������';
$lang['ali_req_not_allowed']  = '��� ������';
$lang['ali_req_otherRequest'] = '�� ��� ������ ������ � ������ ������.';
$lang['ali_req_template']     = '����� ������� ���� � ��� ������';
$lang['ali_req_text']         = '����� ������';
$lang['ali_req_title']        = '������ ������ � ������';
$lang['ali_req_waiting']      = '���� ������ �� ���������� � ������ [%s] ����� ����������� ������ �������.<br>��� ��������� � �������� �������.';
$lang['ali_req_check']        = '���������� ��������';
$lang['ali_req_requestCount'] = '������';
$lang['ali_req_admin_title']  = '����� ������';
$lang['ali_req_accept']       = '������� ������';
$lang['ali_req_deny']         = '��������� ������';

$lang['ali_search_title']       = '����� �������';
$lang['ali_search_action']      = '������';
$lang['ali_search_tip']         = '����� ����� ����������� �� ����� ����� ��� ����������� �������';
$lang['ali_search_result_none'] = '�� ������� ��������, ��������������� ������ �������.';
$lang['ali_search_result_tip']  = '�������� �� ����� ��� ����������� �������, ��� �� ���������� ���������� � ���.<br>�������� "��������", ��� �� ������� ������ � ����������.';

$lang['ali_sys_name']         = '��������';
$lang['ali_sys_tag']          = '�����������';
$lang['ali_sys_members']      = '���������';
$lang['ali_sys_notFound']     = '����� ������ �� ����������';
$lang['ali_sys_memberName']   = '���';
$lang['ali_sys_points']       = '����';
$lang['ali_sys_lastActive']   = '����������';
$lang['ali_sys_totalMembers'] = '�����';
$lang['ali_sys_clear']        = '��������';
$lang['ali_sys_main_page']    = '��������� �� ������� �������� �������';
$lang['ali_sys_joined']       = '���� ����������';

$lang['ali_frm_write']        = '������ �� �����';
$lang['ali_info_title']       = '���������� �� �������';
$lang['ali_info_internal']    = '��������� ����������';
$lang['ali_info_leave']       = '�������� ������';
$lang['ali_info_leave_success'] = '�� �������� ������ [%s].<br />������ �� ������ ������� ���� ����������� ������ ��� ������ ������ �� ���������� � ������ ������<br />';


$lang['Name']           = '��������';
$lang['Tag']            = '�����������';
$lang['Members']        = '���������';


$lang['Accept_cand']             = '�������';
$lang['alliance']             = '������';
$lang['alliances']            = '�������';
$lang['Alliance_information']       = '���������� �� �������';
$lang['Alliance_logo']        = '������� �������';
$lang['alliance_tag']         = '����������� �������';
$lang['Allow_request']        = '��������� ������';
$lang['allyance_name']        = '��� �������';
$lang['ally_admin']        = '���������� ��������';
$lang['ally_been_maked']   = '������ %s ������� ������';
$lang['ally_description']           = '�������� �������';
$lang['ally_dissolve']     = '�������� �������';
$lang['Ally_info_1']             = '���������� �� �������';
$lang['ally_maked']        = '%s ������';
$lang['Ally_nodescription']         = '� ������� ��� ��������';
$lang['ally_notexist']        = '������ ������ �� ����������';
$lang['Ally_not_exist']          = '� ��������� ��� ������� ���������� � ���� �������';
$lang['Ally_transfer']     = '�������� ������';
$lang['All_players']          = '��� ������';
$lang['always_exist']         = '%s ��� ����������';
$lang['Aplication_acepted']      = '�� �������';
$lang['Aplication_hello']           = '�����������<br>������ :';
$lang['Aplication_rejected']        = '���� ������ �� ���������� � ������ ���� ���������.<br>�������:<br>';
$lang['apply_cantbeadded']       = '������ �� ������, ���������� ��� ���!';
$lang['apply_registered']     = '���� ������ ���� ����������.<br><br><a href=alliance.php>�����</a>';
$lang['Back']           = '�����';
$lang['Canceld_req_text']  = '�� �������� ������ �� ���������� � [%s]';
$lang['Change']            = '��������';
$lang['ch_allyname']       = '�������� ��� �������';
$lang['ch_allytag']        = '�������� ����������� �������';
$lang['Circular_message']     = '��������� �������';
$lang['Circular_sended']      = '��������� ������� ����������';
$lang['Clear']             = '��������';
$lang['Click_writerequest']      = '������� ����� ����� �������� ������';
$lang['Continue']          = '����������';
$lang['Delete_apply']         = '��������� ������';
$lang['Denied_access']        = '������ ��������!';
$lang['Destiny']        = '����������';
$lang['Exit_of_this_alliance']      = '����� �� �������';
$lang['External_text']        = '������� �����';
$lang['Founder']        = '���������';
$lang['Founder_name']         = '������ ����������';
$lang['Function']          = '�������';
$lang['Go_out_welldone']      = '�� ������� �������� ������';
$lang['have_not_name']        = '������� ��� �������';
$lang['have_not_tag']         = '������� ����������� �������';
$lang['Help']           = '������';
$lang['Inactive']          = '����������';
$lang['Inner_section']        = '���������� �����';
$lang['Internal_text']        = '���������� �����';
$lang['knowed_allys']     = '������������ �������';
$lang['laws_config']          = '��������� ���� �������';
$lang['Main_Page']         = '�������� ��������';
$lang['make_alliance']        = '�������� �������';
$lang['make_alliance_owner']  = '������� ������';
$lang['max']            = '����.';
$lang['member']            = '��������';
$lang['memberlist_view']      = '�������� ������ ����������';
$lang['members']        = '���������';
$lang['members_admin']     = '���������� �����������';
$lang['Members_list']         = '������ ����������';
$lang['members_who_recived_message'] = '��������� ����� ������� �������� ���������:';
$lang['Message']        = '���������';
$lang['Motive_optional']      = '������� (�����������)';
$lang['New_name']          = '����� �����������';
$lang['New_tag']           = '����� ���';
$lang['not_allow_request']       = '��������� ������';
$lang['Novate']            = '�������';
$lang['Number']            = '�';
$lang['Off']            = 'Off-line';
$lang['Ok']             = '��';
$lang['On']             = 'On-line';
$lang['Online']            = '������';
$lang['Options']        = '�����';
$lang['Position']          = '������';
$lang['Public_text_of_alliance']    = '������� �����';
$lang['Range']             = '������';
$lang['Reject_cand']             = '���������';
$lang['Reload']            = '������';
$lang['Repel']             = 'Repel';
$lang['requests_view']        = '�������� ������';
$lang['Request_answer']       = '������ ��������';
$lang['Request_date']      = '���� ������ ������';
$lang['Request_text']         = '����� ������';
$lang['s']           = '[N/A]';
$lang['Search']               = '�����';
$lang['searchd_ally_avail']   = '������� �������:';
$lang['search_alliance']      = '�����';
$lang['Send']           = '���������';
$lang['Send_Apply']        = '������� ������';
$lang['Send_circular_mail']      = '������� ��������� ����� �������';
$lang['Set_range']            = '��������� �����';
$lang['Show_of_request_text']       = '����� ������';
$lang['Texts']             = '�������������� ������';
$lang['Text_mail']         = '�������� ��������� ����� �������';
$lang['top10alliance']        = '��� 10 ��������';
$lang['transfer']             = '��������';
$lang['transfer_ally']           = '�������� �������';
$lang['transfer_to']          = '�������� ������ ������:';
$lang['Want_go_out']          = '�� ������������� ������ �������� ������ ?';
$lang['write_apply']          = '������ ������';
$lang['your_alliance']        = '��� ������';
$lang['your_apply']        = '���� ������';

?>
