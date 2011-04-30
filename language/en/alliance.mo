<?php
if (!defined('INSIDE')) {
	die("attempt hacking!");
}

$lang['ali_dip_title'] = 'Diplomacy';
$lang['ali_dip_negotiate'] = 'Negotiations';

$lang['ali_adm_msg_subject']   = 'Alliance Maillist';

$lang['ali_dip_offers_your']   = 'Your offers';
$lang['ali_dip_offers_to_you'] = 'Offers to you';
$lang['ali_dip_offer_none']    = 'No offers';
$lang['ali_dip_offer']         = 'Offer';
$lang['ali_dip_offers']        = 'Offers';
$lang['ali_dip_offer_new']     = 'Start negotiations';
$lang['ali_dip_offer_to_ally'] = 'Offer to Alliance';
$lang['ali_dip_offer_make']    = 'Start negotiations';
$lang['ali_dip_offer_answer']      = 'Alliance declined your offer';
$lang['ali_dip_offer_deny_reason'] = 'You declared offer'; // . Причина отказа:
$lang['ali_dip_offer_to']          = 'To Alliance';
$lang['ali_dip_offer_from']        = 'From Alliance';

$lang['ali_dip_offer_deny']          = 'Declare offer';
$lang['ali_dip_offer_accept']        = 'Accept offer';
$lang['ali_dip_offer_delete']        = 'Withdraw offer';

$lang['ali_dip_err_no_ally']       = 'There is no such Alliance';
$lang['ali_dip_err_wrong_offer']   = 'You can not make THIS offer';
$lang['ali_dip_err_offer_none']    = 'No such offer';
$lang['ali_dip_err_offer_alien']   = 'This offer was made not for you!'; // hack
$lang['ali_dip_err_offer_accept_own'] = 'You can not accept own offer!'; // hack
$lang['ali_dip_err_offer_empty']      = 'Offer not defined'; // hack

$lang['ali_dip_relation_none']    = 'No relations';
$lang['ali_dip_relation_change']  = 'We accept offer of Alliance';
$lang['ali_dip_relation_change_to']  = 'change relations to';
$lang['ali_dip_relation_accept']  = 'accepted your offer to change relations to';

$lang['ali_dip_relations'] = array(
  ALLY_DIPLOMACY_NEUTRAL    => 'Neutral',
  ALLY_DIPLOMACY_WAR        => 'War',
  ALLY_DIPLOMACY_FEDERATION => 'Federation',
  ALLY_DIPLOMACY_UNION      => 'Union',
  ALLY_DIPLOMACY_SLAVE      => 'Slave'
);

$lang['ali_lessThen15min']    = '&lt; 15 min';

$lang['ali_confirm']          = 'Confirm';
$lang['ali_confirmation']     = 'Confirmation';

$lang['ali_adm_disband']      = 'To Dissolve The Alliance';
$lang['ali_adm_options']      = 'Configuring Alliance';
$lang['ali_adm_transfer']     = 'Send Alliance player';
$lang['ali_adm_return']       = 'Return to management Alliance';
$lang['ali_adm_kick']         = 'Exclude the player from Alliance';
$lang['ali_adm_kick_confirm'] = 'Are you sure you want to exclude the player from Alliance?';
$lang['ali_adm_requests']     = 'Applications';
$lang['ali_adm_newLeader']    = 'SELECT PLAYER';
$lang['ali_adm_lastRank']     = 'You cannot delete the only rank!';

$lang['ali_adm_rights_title']       = 'Setting permissions';
$lang['ali_adm_rights_rank_new']    = 'New rank';
$lang['ali_adm_rights_rank_delete'] = 'Delete rank';
$lang['ali_adm_rights_rank_none']   = 'No ranks';
$lang['ali_adm_rights_rank_name']   = 'Rank';
$lang['ali_adm_rights_mass_mail']   = 'Message to the entire Alliance';
$lang['ali_adm_rights_view_online'] = 'View the online status of Members';
$lang['ali_adm_rights_helper']      = 'Assistant Head (to pass the required rank founder)';
$lang['ali_adm_rights_legend']      = 'Law Alliance';

$lang['ali_leaderRank']       = 'Leader';
$lang['ali_defaultRankName']  = 'Rookie';

$lang['ali_make_title']       = 'Establishment Of The Alliance';
$lang['ali_make_tag_length']  = '(from 3 to 8 characters)';
$lang['ali_make_name_length'] = '(up to 35 characters)';
$lang['ali_make_confirm']     = 'Create Alliance';

$lang['ali_req_cancel']       = 'Remove application';
$lang['ali_req_candidate']    = 'Candidate';
$lang['ali_req_characters']   = 'Characters';
$lang['ali_req_date']         = 'Filing date';
$lang['ali_req_deny_msg']     = 'Your application for membership in the Alliance [%s] was rejected by.<br>Reason for refusal: "%s".<br>You can remove the application and try again later or enter into another Alliance.';
$lang['ali_req_deny_admin']   = '<font color=red>Request already rejected</font>. However, until you have deleted an entry, you may change your mind';
$lang['ali_req_deny_reason']  = 'Your request for membership is rejected';
$lang['ali_req_emptyList']    = 'No requests for review';
$lang['ali_req_inAlly']       = 'You are already a member of the Alliance.';
$lang['ali_req_make']         = 'Apply now';
$lang['ali_req_not_allowed']  = 'NO RECEPTION';
$lang['ali_req_otherRequest'] = 'You have already submitted an application to the other Alliance.';
$lang['ali_req_template']     = 'Please accept me in your Alliance';
$lang['ali_req_text']         = 'Application text';
$lang['ali_req_title']        = 'Enroll in the Alliance';
$lang['ali_req_waiting']      = 'Your application for membership in the Alliance [%s] will be sent to the head of the Alliance.<br>You will be automatically notified of the decision.';
$lang['ali_req_check']        = 'Application management';
$lang['ali_req_requestCount'] = 'Applications';
$lang['ali_req_admin_title']  = 'Review of applications';
$lang['ali_req_accept']       = 'Accept the application';
$lang['ali_req_deny']         = 'Reject the application';

$lang['ali_search_title']       = 'Search Alliance';
$lang['ali_search_action']      = 'Search';
$lang['ali_search_tip']         = 'Search can be performed on the part of the name or tag of the Alliance';
$lang['ali_search_result_none'] = 'No items found matching your search for Alliances.';
$lang['ali_search_result_tip']  = 'Click on the name or tag of the Alliance that would see information about it.<br>Click "Enter" to send a request to join.';

$lang['ali_sys_name']         = 'The Name';
$lang['ali_sys_tag']          = 'Tag';
$lang['ali_sys_members']      = 'Members';
$lang['ali_sys_notFound']     = 'The Alliance does not exist';
$lang['ali_sys_memberName']   = 'Member Name';
$lang['ali_sys_points']       = 'Points';
$lang['ali_sys_lastActive']   = 'Activity';
$lang['ali_sys_totalMembers'] = 'Total';
$lang['ali_sys_clear']        = 'Reset';
$lang['ali_sys_main_page']    = 'Return to the home page of the Alliance';
$lang['ali_sys_joined']       = 'Date of entry';

$lang['ali_frm_write']        = 'Write in the Forum';

$lang['Name']           = 'The Name';
$lang['Tag']            = 'Tag';
$lang['Members']        = 'Members';


$lang['Accept_cand']             = 'Accept';
$lang['alliance']             = 'Alliance';
$lang['alliances']            = 'Alliances';
$lang['Alliance_information']       = 'Information about the Alliance';
$lang['Alliance_logo']        = 'Alliance Logo';
$lang['alliance_tag']         = 'Alliance Tag';
$lang['Allow_request']        = 'Accept applications';
$lang['allyance_name']        = 'Alliance Name';
$lang['ally_admin']        = 'Alliance Management';
$lang['ally_been_maked']   = 'Alliance %s successfully created';
$lang['ally_description']           = 'Alliance Description';
$lang['ally_dissolve']     = 'Delete Alliance';
$lang['Ally_info_1']             = 'Information about the Alliance';
$lang['ally_maked']        = '%s created';
$lang['Ally_nodescription']         = 'The Alliance has no description';
$lang['ally_notexist']        = 'Alliance no longer exists';
$lang['Ally_not_exist']          = 'Unfortunately there is no information about the Alliance';
$lang['Ally_transfer']     = 'Transfer Alliance';
$lang['All_players']          = 'All players';
$lang['always_exist']         = '%s already exists';
$lang['Aplication_acepted']      = 'You have taken';
$lang['Aplication_hello']           = 'Welcome<br>Alliance:';
$lang['Aplication_rejected']        = 'Your application for membership in the Alliance was rejected.<br>The Reason:<br>';
$lang['apply_cantbeadded']       = 'Request failed, try again!';
$lang['apply_registered']     = 'Your request has been sent.<br><br><a href=alliance.php>Back</a>';
$lang['Back']           = 'Back';
$lang['Canceld_req_text']  = 'You have cancelled for [%s]';
$lang['Change']            = 'Change';
$lang['ch_allyname']       = 'Change Alliance Name';
$lang['ch_allytag']        = 'Change Alliance Tag';
$lang['Circular_message']     = 'Circular Alliance';
$lang['Circular_sended']      = 'Message sent successfully';
$lang['Clear']             = 'Clear';
$lang['Click_writerequest']      = 'Click here to write an application';
$lang['Continue']          = 'Continue';
$lang['Delete_apply']         = 'Reject the application';
$lang['Denied_access']        = 'Access forbidden!';
$lang['Destiny']        = 'Recipient';
$lang['Exit_of_this_alliance']      = 'Exit Alliance';
$lang['External_text']        = 'External text';
$lang['Founder']        = 'Founder';
$lang['Founder_name']         = 'Founder Name';
$lang['Function']          = 'Function';
$lang['Go_out_welldone']      = 'You have successfully left the Alliance';
$lang['have_not_name']        = 'Type a name for the Alliance';
$lang['have_not_tag']         = 'Type the Tag of the Alliance';
$lang['Help']           = 'Assistance';
$lang['Inactive']          = 'Inactive';
$lang['Inner_section']        = 'Inner text';
$lang['Internal_text']        = 'Inner text';
$lang['knowed_allys']     = 'Current Alliances';
$lang['laws_config']          = 'Setting permissions';
$lang['Main_Page']         = 'Home page';
$lang['make_alliance']        = 'Establishment Of The Alliance';
$lang['make_alliance_owner']  = 'Create Alliance';
$lang['max']            = 'Max.';
$lang['member']            = 'Member';
$lang['memberlist_view']      = 'View a list of members';
$lang['members']        = 'Participants';
$lang['members_admin']     = 'Managing your members';
$lang['Members_list']         = 'List of members';
$lang['members_who_recived_message'] = 'The following members of the Alliance received a message:';
$lang['Message']        = 'Message';
$lang['Motive_optional']      = 'Reason (optional)';
$lang['New_name']          = 'New Alliance Name';
$lang['New_tag']           = 'New Tag';
$lang['not_allow_request']       = 'Reject the application';
$lang['Novate']            = 'Rookie';
$lang['Number']            = 'Number';
$lang['Off']            = 'Offline';
$lang['Ok']             = 'Ok';
$lang['On']             = 'Online';
$lang['Online']            = 'Status';
$lang['Options']        = 'Options';
$lang['Position']          = 'Status';
$lang['Public_text_of_alliance']    = 'External text';
$lang['Range']             = 'Rank';
$lang['Reject_cand']             = 'Reject';
$lang['Reload']            = 'Example';
$lang['Repel']             = 'Repel';
$lang['requests_view']        = 'Viewing applications';
$lang['Request_answer']       = 'Request rejected';
$lang['Request_date']      = 'Request date';
$lang['Request_text']         = 'Application text';
$lang['s']           = '[N/A]';
$lang['Search']               = 'Search';
$lang['searchd_ally_avail']   = 'Found Alliances:';
$lang['search_alliance']      = 'Search';
$lang['Send']           = 'Send';
$lang['Send_Apply']        = 'To accept the application';
$lang['Send_circular_mail']      = 'Send a message to the entire Alliance';
$lang['Set_range']            = 'Change in rank';
$lang['Show_of_request_text']       = 'Application text';
$lang['Texts']             = 'Editing text';
$lang['Text_mail']         = 'Send a message to the entire Alliance';
$lang['top10alliance']        = 'Top 10 Alliances';
$lang['transfer']             = 'Transfer';
$lang['transfer_ally']           = 'Transfer Alliance';
$lang['transfer_to']          = 'Send Alliance player:';
$lang['Want_go_out']          = 'Are you sure you want to leave the Alliance?';
$lang['write_apply']          = 'Apply now';
$lang['your_alliance']        = 'Your Alliance';
$lang['your_apply']        = 'Your application';

?>
