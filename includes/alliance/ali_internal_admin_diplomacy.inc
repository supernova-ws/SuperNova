<?php

if(!defined('SN_IN_ALLY') || SN_IN_ALLY !== true)
{
  $debug->error("Attempt to call ALLIANCE page mode {$mode} directly - not from alliance.php", 'Forbidden', 403);
}

if (!$user_can_negotiate) {
  message($lang['Denied_access'], $lang['ali_dip_title']);
}
$template = gettemplate('ali_admin_diplomacy', true);
$page_title = $lang['ali_dip_title'];
/*
define('ALLY_DIPLOMACY_NEUTRAL',    'neutral');
define('ALLY_DIPLOMACY_WAR',        'war');
define('ALLY_DIPLOMACY_FEDERATION', 'federation');
define('ALLY_DIPLOMACY_UNION',      'union');
define('ALLY_DIPLOMACY_SLAVE',      'slave');
*/

if(sys_get_param_str('ali_dip_offer_make'))
{
  $alliance_diplomacy_contr_ally_id = sys_get_param_int('alliance_diplomacy_contr_ally_id');

  $query = doquery("SELECT `id` FROM {{alliance}} WHERE `id` = {$alliance_diplomacy_contr_ally_id} LIMIT 1;");
  if(!$query)
  {
    message($lang['ali_dip_err_no_ally'], $page_title);
  }
  $alliance_diplomacy_relation = sys_get_param_str('alliance_diplomacy_relation');

  if(!in_array($alliance_diplomacy_relation, $sn_diplomacy_relation_list))
  {
    message($lang['ali_dip_err_wrong_offer'], $page_title);
  }
  $alliance_diplomacy_propose = sys_get_param_str('alliance_diplomacy_propose');

  $query = doquery("SELECT alliance_diplomacy_id FROM {{alliance_negotiation}} WHERE alliance_diplomacy_ally_id = {$user['ally_id']} AND alliance_diplomacy_contr_ally_id = {$alliance_diplomacy_contr_ally_id} LIMIT 1;", '', true);
  if($query)
  {
    // There is already offer for this alliance. Delete it
    doquery("DELETE FROM {{alliance_negotiation}} WHERE alliance_diplomacy_id = {$query['alliance_diplomacy_id']} LIMIT 1;");
  }

  $query_alliance_contr_name = doquery("SELECT ally_name FROM {{alliance}} WHERE `id` = {$alliance_diplomacy_contr_ally_id} LIMIT 1;", '', true);

  doquery(
    "INSERT INTO {{alliance_negotiation}}
      SET
        alliance_diplomacy_ally_id = {$user['ally_id']},
        alliance_diplomacy_contr_ally_id = {$alliance_diplomacy_contr_ally_id},
        alliance_diplomacy_contr_ally_name = '{$query_alliance_contr_name['ally_name']}',
        alliance_diplomacy_relation = '{$alliance_diplomacy_relation}',
        alliance_diplomacy_time = '{$time_now}',
        alliance_diplomacy_propose = '{$alliance_diplomacy_propose}',
        alliance_diplomacy_status = " . ALLY_DIPLOMACY_PROPOSE_SEND . ";"
	);
}

$offer_id = sys_get_param_int('offer_id');
$offer_answer = sys_get_param_str('answer');
if($offer_answer)
{
  if(!$offer_id)
  {
    // TODO: Add log of hack attempt
    message($lang['ali_dip_err_offer_empty'], $page_title);
  }

  $negotiation = doquery("SELECT * FROM {{alliance_negotiation}} WHERE alliance_diplomacy_id = {$offer_id} LIMIT 1;", '', true);
  if(!$negotiation)
  {
    message($lang['ali_dip_err_offer_none'], $page_title);
  }
  elseif($negotiation['alliance_diplomacy_ally_id'] != $user['ally_id'] && $negotiation['alliance_diplomacy_contr_ally_id'] != $user['ally_id'])
  {
    // TODO: Add log of hack attempt
    message($lang['ali_dip_err_offer_alien'], $page_title);
  }
  elseif($negotiation['alliance_diplomacy_ally_id'] == $user['ally_id'])
  {
    if($offer_answer == 'accept')
    {
      // TODO: Add log of hack attempt
      message($lang['ali_dip_err_offer_accept_own'], $page_title);
    }
    elseif($offer_answer == 'deny')
    {
      doquery("DELETE FROM {{alliance_negotiation}} WHERE alliance_diplomacy_id = {$offer_id} LIMIT 1;");
    }
  }
  else
  {
    if($offer_answer == 'accept')
    {
      $negotiation['alliance_diplomacy_contr_ally_name'] = mysql_real_escape_string($negotiation['alliance_diplomacy_contr_ally_name']);
      doquery('START TRANSACTION;');
      doquery(
        "INSERT INTO {{alliance_diplomacy}}
          SET
            alliance_diplomacy_ally_id = {$negotiation['alliance_diplomacy_ally_id']},
            alliance_diplomacy_contr_ally_id = {$negotiation['alliance_diplomacy_contr_ally_id']},
            alliance_diplomacy_contr_ally_name = '{$negotiation['alliance_diplomacy_contr_ally_name']}',
            alliance_diplomacy_relation = '{$negotiation['alliance_diplomacy_relation']}',
            alliance_diplomacy_time = {$time_now};
      ");
      doquery("DELETE FROM {{alliance_negotiation}} WHERE alliance_diplomacy_id = {$offer_id} LIMIT 1;");
      msg_ali_send("{$lang['ali_dip_relation_change']} [{$negotiation['alliance_diplomacy_contr_ally_name']}] {$lang['ali_dip_relation_change_to']} {$lang['ali_dip_relations'][$negotiation['alliance_diplomacy_relation']]}", $lang['ali_dip_relation_change']);
      msg_ali_send("{$lang['sys_alliance']} [{$negotiation['alliance_diplomacy_contr_ally_name']}] {$lang['ali_dip_relation_accept']} {$lang['ali_dip_relations'][$negotiation['alliance_diplomacy_relation']]}", '', -1, $negotiation['alliance_diplomacy_contr_ally_id']);
      doquery('COMMIT;');
    }
    elseif($offer_answer == 'deny')
    {
      doquery("UPDATE {{alliance_negotiation}} SET alliance_diplomacy_status = 1 WHERE alliance_diplomacy_id = {$offer_id} LIMIT 1;");
    }
  }

}

foreach($sn_diplomacy_relation_list as $diplomacy_relation)
{
  $template->assign_block_vars('relation', array(
    'ID'   => $diplomacy_relation,
    'TEXT' => $lang['ali_dip_relations'][$diplomacy_relation],
  ));
}

$query = doquery("SELECT id, ally_name, ally_tag FROM {{alliance}} ORDER BY ally_name;");
while($alliance = mysql_fetch_assoc($query))
{
  $template->assign_block_vars('alliance', array(
    'ID'   => $alliance['id'],
    'NAME' => js_safe_string($alliance['ally_name']),
    'TAG'  => js_safe_string($alliance['ally_tag']),
  ));
}

//    if(alliance_diplomacy_ally_id = {$user['ally_id']}, alliance_diplomacy_ally_id, alliance_diplomacy_contr_ally_id) AS ally_id,
$query = doquery(
  "SELECT
    alliance_diplomacy_id,
    if(alliance_diplomacy_ally_id = {$user['ally_id']}, 1, 0) AS owner,
    if(alliance_diplomacy_contr_ally_id = {$user['ally_id']}, alliance_diplomacy_ally_id, alliance_diplomacy_contr_ally_id) AS contr_ally_id,
    alliance_diplomacy_contr_ally_name AS ally_name,
    alliance_diplomacy_relation AS relation,
    alliance_diplomacy_time AS time,
    alliance_diplomacy_propose AS propose,
    alliance_diplomacy_response AS response,
    alliance_diplomacy_status AS status
  FROM
    {{alliance_negotiation}}
  WHERE
    alliance_diplomacy_ally_id = {$user['ally_id']}
    OR alliance_diplomacy_contr_ally_id = {$user['ally_id']};"
);
while($offer = mysql_fetch_assoc($query))
{
  $template->assign_block_vars('offer', array(
    'ID'   		=> $offer['alliance_diplomacy_id'],
    'NAME'     => $offer['ally_name'],
    'RELATION' => $lang['ali_dip_relations'][$offer['relation']],
    'TIME'		=> date(FMT_DATE_TIME, $offer['time']),
    'TEXT'     => CHT_messageParse($offer['propose']),
    'RESPONSE' => CHT_messageParse($offer['response']),
    'STATUS'   => $offer['status'],
    'OWNER'		=> $offer['owner'],
  ));
}

display(parsetemplate($template), $page_title);

?>
