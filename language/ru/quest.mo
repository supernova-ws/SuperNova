<?php

if (!defined('INSIDE')) 
{
  die('���������� ������� ������!');
}

$lang = array_merge($lang, array(
  'qst_quest'       => '�����',
  'qst_quest_of'    => '������',
  'qst_name'        => '��������',
  'qst_description' => '��������',
  'qst_conditions'  => '�������',
  'qst_rewards'     => '�������',
  'qst_total'       => '�������',
  'qst_status'      => '������',
  'qst_status_list' => array(
    QUEST_STATUS_NOT_STARTED => '��&nbsp;�����',
    QUEST_STATUS_STARTED     => '�����',
    QUEST_STATUS_COMPLETE    => '��������',
  ),

  'qst_add'         => '���������� ������',
  'qst_edit'        => '�������������� ������',
  'qst_copy'        => '����������� ������',

  'qst_mode_add'    => '����������',
  'qst_mode_edit'   => '��������������',
  'qst_mode_copy'   => '�����������',

  'qst_adm_err_unit_id'       => '������������ ����',
  'qst_adm_err_unit_amount'   => '������������ ���������� ������',
  'qst_adm_err_reward_amount' => '������������ ������ �������',
  'qst_adm_err_reward_type'   => '������������ ��� �������',
));

?>
