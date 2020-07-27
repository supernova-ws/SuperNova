<?php

use Alliance\DBStaticAlly;
use \DBAL\DbQuery;

if(!defined('SN_IN_ALLY') || SN_IN_ALLY !== true)
{
  $debug->error("Attempt to call ALLIANCE page mode {$mode} directly - not from alliance.php", 'Forbidden', 403);
}

if (!$user_can_negotiate) {
  SnTemplate::messageBox($lang['Denied_access'], $lang['ali_dip_title']);
}

$template = SnTemplate::gettemplate('ali_admin_diplomacy', true);
$page_title = $lang['ali_dip_title'];

$ally_name_safe = db_escape($user['ally_name']);

$autoAccept = false;
if(sys_get_param_str('ali_dip_offer_make'))
{
  $alliance_negotiation_relation = sys_get_param_str('alliance_negotiation_relation');
  if(!array_key_exists($alliance_negotiation_relation, $sn_diplomacy_relation_list))
  {
    SnTemplate::messageBox($lang['ali_dip_err_wrong_offer'], $page_title);
  }

  $alliance_negotiation_contr_ally_id = sys_get_param_id('alliance_negotiation_contr_ally_id');
  if($alliance_negotiation_contr_ally_id == $user['ally_id'])
  {
    SnTemplate::messageBox($lang['ali_dip_err_same_ally'], $page_title);
  }

  $contr_ally_row = doquery("SELECT `ally_name` FROM {{alliance}} WHERE `id` = {$alliance_negotiation_contr_ally_id} LIMIT 1;", '', true);
  if(!$contr_ally_row)
  {
    SnTemplate::messageBox($lang['ali_dip_err_no_ally'], $page_title);
  }

  $relation_current_id = ali_relation($user['ally_id'], $alliance_negotiation_contr_ally_id);
  if($alliance_negotiation_relation == $relation_current_id)
  {
    SnTemplate::messageBox(sprintf($lang['ali_dip_err_offer_same'], $lang['ali_dip_relations'][$alliance_negotiation_relation]), $page_title);
  }

  $alliance_negotiation_propose = sys_get_param_str('alliance_negotiation_propose');

  // If there is already offer for this alliance - delete it
  doquery("DELETE FROM {{alliance_negotiation}} WHERE alliance_negotiation_ally_id = {$user['ally_id']} AND alliance_negotiation_contr_ally_id = {$alliance_negotiation_contr_ally_id} LIMIT 1;");

  $relation_new = $sn_diplomacy_relation_list[$alliance_negotiation_relation];
  $relation_current = $sn_diplomacy_relation_list[$relation_current_id];
  if($relation_new['enter_delay'] == -1 || $relation_current['exit_delay'] == -1)
  {
    DbQuery::build()
      ->setTable('alliance_negotiation')
      ->setValues(array(
        'alliance_negotiation_ally_id'         => $user['ally_id'],
        'alliance_negotiation_ally_name'       => $user['ally_name'],
        'alliance_negotiation_contr_ally_id'   => $alliance_negotiation_contr_ally_id,
        'alliance_negotiation_contr_ally_name' => $contr_ally_row['ally_name'],
        'alliance_negotiation_relation'        => $alliance_negotiation_relation,
        'alliance_negotiation_time'            => SN_TIME_NOW,
        'alliance_negotiation_propose'         => $alliance_negotiation_propose,
        'alliance_negotiation_status'          => ALLY_PROPOSE_SEND
      ))
      ->doInsert();

    $accept_offer = false;
  }
  else
  {
    $accept_offer = true;
    $autoAccept = true;
    $negotiation = array(
      'alliance_negotiation_ally_id' => $alliance_negotiation_contr_ally_id,
      'alliance_negotiation_ally_name' => $contr_ally_row['ally_name'],
      'alliance_negotiation_contr_ally_id' => $user['ally_id'],
      'alliance_negotiation_contr_ally_name' => $user['ally_name'],
      'alliance_negotiation_relation' => $alliance_negotiation_relation,
    );
  }
}
else
{
  $offer_id = sys_get_param_id('offer_id');
  if($offer_id)
  {
    $offer_answer = sys_get_param_str('answer');

    $negotiation = doquery("SELECT * FROM {{alliance_negotiation}} WHERE alliance_negotiation_id = {$offer_id} LIMIT 1;", '', true);
    if(!$negotiation)
    {
      SnTemplate::messageBox($lang['ali_dip_err_offer_none'], $page_title);
    }
    elseif($negotiation['alliance_negotiation_ally_id'] != $user['ally_id'] && $negotiation['alliance_negotiation_contr_ally_id'] != $user['ally_id'])
    {
      // TODO: Add log of hack attempt
      SnTemplate::messageBox($lang['ali_dip_err_offer_alien'], $page_title);
    }
    elseif($negotiation['alliance_negotiation_ally_id'] == $user['ally_id'])
    {
      if($offer_answer == 'accept')
      {
        // TODO: Add log of hack attempt
        SnTemplate::messageBox($lang['ali_dip_err_offer_accept_own'], $page_title);
      }
      elseif($offer_answer == 'deny')
      {
        doquery("DELETE FROM {{alliance_negotiation}} WHERE alliance_negotiation_id = {$offer_id} LIMIT 1;");
      }
    }
    else
    {
      if($offer_answer == 'accept')
      {
        $accept_offer = true;
      }
      elseif($offer_answer == 'deny')
      {
        DBStaticAlly::db_ally_negotiation_update_status_1($offer_id);
      }
    }
  }
}

if($accept_offer)
{
  sn_db_transaction_start();

  DbQuery::build()
    ->setTable('alliance_diplomacy')
    ->setValues(array(
      'alliance_diplomacy_ally_id'         => $user['ally_id'],
      'alliance_diplomacy_contr_ally_id'   => $negotiation['alliance_negotiation_ally_id'],
      'alliance_diplomacy_contr_ally_name' => $negotiation['alliance_negotiation_ally_name'],
      'alliance_diplomacy_relation'        => $negotiation['alliance_negotiation_relation'],
      'alliance_diplomacy_time'            => SN_TIME_NOW,
    ))
    ->doInsert();

  DbQuery::build()
    ->setTable('alliance_diplomacy')
    ->setValues(array(
      'alliance_diplomacy_ally_id'         => $negotiation['alliance_negotiation_ally_id'],
      'alliance_diplomacy_contr_ally_id'   => $user['ally_id'],
      'alliance_diplomacy_contr_ally_name' => $user['ally_name'],
      'alliance_diplomacy_relation'        => $negotiation['alliance_negotiation_relation'],
      'alliance_diplomacy_time'            => SN_TIME_NOW,
    ))
    ->doInsert();

  doquery(
    "DELETE FROM {{alliance_negotiation}}
  	 WHERE
        (alliance_negotiation_ally_id = {$negotiation['alliance_negotiation_ally_id']} AND alliance_negotiation_contr_ally_id = {$user['ally_id']})
        OR
        (alliance_negotiation_ally_id = {$user['ally_id']} AND alliance_negotiation_contr_ally_id = {$negotiation['alliance_negotiation_ally_id']});"
  );

  if ($autoAccept) {
    $messages = [
      'own'   => 'ali_dip_relation_change_auto_accept',
      'other' => 'ali_dip_relation_change_auto_accept',
    ];
  } else {
    $messages = [
      'own'   => 'ali_dip_relation_change_own',
      'other' => 'ali_dip_relation_change_other',
    ];
  }

  foreach ($messages as &$theMessage) {
    $theMessage = vsprintf(SN::$lang[$theMessage], [
      $negotiation['alliance_negotiation_contr_ally_name'],
      $negotiation['alliance_negotiation_ally_name'],
      $lang['ali_dip_relations'][$negotiation['alliance_negotiation_relation']],
    ]);
  }

  msg_ali_send($messages['own'], "{$lang['sys_alliance']} [{$negotiation['alliance_negotiation_ally_name']}]");
  msg_ali_send($messages['other'], "{$lang['sys_alliance']} [{$negotiation['alliance_negotiation_contr_ally_name']}]", -1, $negotiation['alliance_negotiation_ally_id']);

  sn_db_transaction_commit();
}

foreach($sn_diplomacy_relation_list as $diplomacy_relation_id => $diplomacy_relation)
{
  $template->assign_block_vars('relation', array(
    'ID'   => $diplomacy_relation_id,
    'TEXT' => $lang['ali_dip_relations'][$diplomacy_relation_id],
  ));
}

$query = doquery("SELECT id, ally_name, ally_tag FROM {{alliance}} WHERE `id` != {$user['ally_id']} ORDER BY ally_name;");
while($alliance = db_fetch($query))
{
  $template->assign_block_vars('alliance', array(
    'ID'   => $alliance['id'],
    'NAME' => js_safe_string($alliance['ally_name']),
    'TAG'  => js_safe_string($alliance['ally_tag']),
  ));
}

$query = doquery(
  "SELECT
    *,
    if(alliance_negotiation_ally_id = {$user['ally_id']}, 1, 0) AS owner,
    if(alliance_negotiation_ally_id = {$user['ally_id']}, alliance_negotiation_contr_ally_name, alliance_negotiation_ally_name) AS ally_name
  FROM
    {{alliance_negotiation}}
  WHERE
    alliance_negotiation_ally_id = {$user['ally_id']} OR alliance_negotiation_contr_ally_id = {$user['ally_id']};"
);
while($offer = db_fetch($query))
{
  $template->assign_block_vars('offer', array(
    'ID'       => $offer['alliance_negotiation_id'],
    'NAME'     => $offer['ally_name'],
    'RELATION' => $lang['ali_dip_relations'][$offer['alliance_negotiation_relation']],
    'TIME'     => date(FMT_DATE_TIME, $offer['alliance_negotiation_time']),
    'TEXT'     => SN::$gc->bbCodeParser->expandBbCode($offer['alliance_negotiation_propose'], AUTH_LEVEL_REGISTERED, HTML_ENCODE_MULTILINE),
    'RESPONSE' => SN::$gc->bbCodeParser->expandBbCode($offer['alliance_negotiation_response'], AUTH_LEVEL_REGISTERED, HTML_ENCODE_MULTILINE),
    'STATUS'   => $offer['alliance_negotiation_status'],
    'OWNER'    => $offer['owner'],
  ));
}

SnTemplate::display($template, $page_title);
