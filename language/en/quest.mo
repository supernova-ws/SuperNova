<?php

if (!defined('INSIDE')) 
{
  die('Hack attempt!');
}

$lang = array_merge($lang, array(
  'qst_quest'       => 'Quest',
  'qst_quest_of'    => 'quest',
  'qst_name'        => 'Name',
  'qst_description' => 'Description',
  'qst_conditions'  => 'Condition',
  'qst_rewards'     => 'Reward',
  'qst_total'       => 'Quests',
  'qst_status'      => 'Status',
  'qst_status_list' => array(
    QUEST_STATUS_NOT_STARTED => 'Not&nbsp;started',
    QUEST_STATUS_STARTED     => 'Started',
    QUEST_STATUS_COMPLETE    => 'Complete',
  ),

  'qst_add'         => 'Add quest',
  'qst_edit'        => 'Edit quest',
  'qst_copy'        => 'Copy quest',

  'qst_mode_add'    => 'Add',
  'qst_mode_edit'   => 'Edit',
  'qst_mode_copy'   => 'Copy',

  'qst_adm_err_unit_id'       => 'Unsupported unit',
  'qst_adm_err_unit_amount'   => 'Wrong unit amount',
  'qst_adm_err_reward_amount' => 'Wrong reward amount',
  'qst_adm_err_reward_type'   => 'Wrong reward type',
));

?>
