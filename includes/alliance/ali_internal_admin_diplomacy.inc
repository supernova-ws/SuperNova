<?php

$classLocale = classLocale::$lang;

if(!defined('SN_IN_ALLY') || SN_IN_ALLY !== true) {
  classSupernova::$debug->error("Attempt to call ALLIANCE page mode {$mode} directly - not from alliance.php", 'Forbidden', 403);
}

if(!$user_can_negotiate) {
  message(classLocale::$lang['Denied_access'], classLocale::$lang['ali_dip_title']);
}

$template = gettemplate('ali_admin_diplomacy', true);
$page_title = classLocale::$lang['ali_dip_title'];

$ally_name_safe = db_escape($user['ally_name']);



if(sys_get_param_str('ali_dip_offer_make')) {
  $alliance_negotiation_relation = sys_get_param_str('alliance_negotiation_relation');
  if(!array_key_exists($alliance_negotiation_relation, $sn_diplomacy_relation_list)) {
    message(classLocale::$lang['ali_dip_err_wrong_offer'], $page_title);
  }

  $alliance_negotiation_contr_ally_id = sys_get_param_id('alliance_negotiation_contr_ally_id');
  if($alliance_negotiation_contr_ally_id == $user['ally_id']) {
    message(classLocale::$lang['ali_dip_err_same_ally'], $page_title);
  }

  $contr_ally_row = DBStaticAlly::db_ally_get_by_id($alliance_negotiation_contr_ally_id);
  if(!$contr_ally_row) {
    message(classLocale::$lang['ali_dip_err_no_ally'], $page_title);
  }

  $relation_current_id = ali_relation($user['ally_id'], $alliance_negotiation_contr_ally_id);
  if($alliance_negotiation_relation == $relation_current_id) {
    message(sprintf(classLocale::$lang['ali_dip_err_offer_same'], classLocale::$lang['ali_dip_relations'][$alliance_negotiation_relation]), $page_title);
  }

  $alliance_negotiation_propose = sys_get_param_str('alliance_negotiation_propose');

  // If there is already offer for this alliance - delete it
  DBStaticAlly::db_ally_negotiation_delete($user['ally_id'], $alliance_negotiation_contr_ally_id);

  $relation_new = $sn_diplomacy_relation_list[$alliance_negotiation_relation];
  $relation_current = $sn_diplomacy_relation_list[$relation_current_id];
  if($relation_new['enter_delay'] == -1 || $relation_current['exit_delay'] == -1) {
    classSupernova::$db->doInsertSet(
      TABLE_ALLIANCE_NEGOTIATION,
      array(
        'alliance_negotiation_ally_id'         => $user['ally_id'],
        'alliance_negotiation_ally_name'       => $user['ally_name'],
        'alliance_negotiation_contr_ally_id'   => $alliance_negotiation_contr_ally_id,
        'alliance_negotiation_contr_ally_name' => $contr_ally_row['ally_name'],
        'alliance_negotiation_relation'        => $alliance_negotiation_relation,
        'alliance_negotiation_time'            => SN_TIME_NOW,
        'alliance_negotiation_propose'         => $alliance_negotiation_propose,
        'alliance_negotiation_status'          => ALLY_PROPOSE_SENT
      )
    );
    $accept_offer = false;
  } else {
    $negotiation = array(
      'alliance_negotiation_ally_id'         => $alliance_negotiation_contr_ally_id,
      'alliance_negotiation_ally_name'       => $contr_ally_row['ally_name'],
      'alliance_negotiation_contr_ally_id'   => $user['ally_id'],
      'alliance_negotiation_contr_ally_name' => $user['ally_name'],
      'alliance_negotiation_relation'        => $alliance_negotiation_relation,
    );
    $accept_offer = true;
  }
} else {
  $offer_id = sys_get_param_id('offer_id');
  if($offer_id) {
    $offer_answer = sys_get_param_str('answer');

    $negotiation = DBStaticAlly::db_ally_negotiation_get_by_offer_id($offer_id);
    if(!$negotiation) {
      message(classLocale::$lang['ali_dip_err_offer_none'], $page_title);
    } elseif($negotiation['alliance_negotiation_ally_id'] != $user['ally_id'] && $negotiation['alliance_negotiation_contr_ally_id'] != $user['ally_id']) {
      // TODO: Add log of hack attempt
      message(classLocale::$lang['ali_dip_err_offer_alien'], $page_title);
    } elseif($negotiation['alliance_negotiation_ally_id'] == $user['ally_id']) {
      if($offer_answer == 'accept') {
        // TODO: Add log of hack attempt
        message(classLocale::$lang['ali_dip_err_offer_accept_own'], $page_title);
      } elseif($offer_answer == 'deny') {
        DBStaticAlly::db_ally_negotiation_delete_by_offer_id($offer_id);
      }
    } else {
      if($offer_answer == 'accept') {
        $accept_offer = true;
      } elseif($offer_answer == 'deny') {
        DBStaticAlly::db_ally_negotiation_deny($offer_id);
      }
    }
  }
}

if($accept_offer) {
  sn_db_transaction_start();

  classSupernova::$db->doInsertSet(
    TABLE_ALLIANCE_DIPLOMACY,
    array(
      'alliance_diplomacy_ally_id'         => $user['ally_id'],
      'alliance_diplomacy_contr_ally_id'   => $negotiation['alliance_negotiation_ally_id'],
      'alliance_diplomacy_contr_ally_name' => $negotiation['alliance_negotiation_ally_name'],
      'alliance_diplomacy_relation'        => $negotiation['alliance_negotiation_relation'],
      'alliance_diplomacy_time'            => SN_TIME_NOW
    )
  );
  classSupernova::$db->doInsertSet(
    TABLE_ALLIANCE_DIPLOMACY,
    array(
      'alliance_diplomacy_ally_id'         => $negotiation['alliance_negotiation_ally_id'],
      'alliance_diplomacy_contr_ally_id'   => $user['ally_id'],
      'alliance_diplomacy_contr_ally_name' => $user['ally_name'],
      'alliance_diplomacy_relation'        => $negotiation['alliance_negotiation_relation'],
      'alliance_diplomacy_time'            => SN_TIME_NOW
    )
  );


  DBStaticAlly::db_ally_negotiation_delete_extended($negotiation['alliance_negotiation_ally_id'], $user['ally_id']);

  DBStaticMessages::msg_ali_send("{$classLocale['ali_dip_relation_change']} [{$negotiation['alliance_negotiation_ally_name']}] {$classLocale['ali_dip_relation_change_to']} {$classLocale['ali_dip_relations'][$negotiation['alliance_negotiation_relation']]}", "{$classLocale['sys_alliance']} [{$negotiation['alliance_negotiation_ally_name']}]");
  DBStaticMessages::msg_ali_send("{$classLocale['sys_alliance']} [{$negotiation['alliance_negotiation_contr_ally_name']}] {$classLocale['ali_dip_relation_accept']} {$classLocale['ali_dip_relations'][$negotiation['alliance_negotiation_relation']]}", "{$classLocale['sys_alliance']} [{$negotiation['alliance_negotiation_contr_ally_name']}]", -1, $negotiation['alliance_negotiation_ally_id']);
  sn_db_transaction_commit();
}

foreach($sn_diplomacy_relation_list as $diplomacy_relation_id => $diplomacy_relation) {
  $template->assign_block_vars('relation', array(
    'ID'   => $diplomacy_relation_id,
    'TEXT' => classLocale::$lang['ali_dip_relations'][$diplomacy_relation_id],
  ));
}


$query = DBStaticAlly::db_ally_list_get_by_not_user_ally($user);
while($alliance = db_fetch($query)) {
  $template->assign_block_vars('alliance', array(
    'ID'   => $alliance['id'],
    'NAME' => js_safe_string($alliance['ally_name']),
    'TAG'  => js_safe_string($alliance['ally_tag']),
  ));
}

$query = DBStaticAlly::db_ally_negotiation_list($user);
while($offer = db_fetch($query)) {
  $template->assign_block_vars('offer', array(
    'ID'       => $offer['alliance_negotiation_id'],
    'NAME'     => $offer['ally_name'],
    'RELATION' => classLocale::$lang['ali_dip_relations'][$offer['alliance_negotiation_relation']],
    'TIME'     => date(FMT_DATE_TIME, $offer['alliance_negotiation_time']),
    'TEXT'     => cht_message_parse($offer['alliance_negotiation_propose']),
    'RESPONSE' => cht_message_parse($offer['alliance_negotiation_response']),
    'STATUS'   => $offer['alliance_negotiation_status'],
    'OWNER'    => $offer['owner'],
  ));
}

display(parsetemplate($template), $page_title);
