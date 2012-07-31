<?php

/*
#############################################################################
#  Filename: mercenary.mo
#  Project: SuperNova.WS
#  Website: http://www.supernova.ws
#  Description: Massive Multiplayer Online Browser Space Startegy Game
#
#  Copyright © 2009 Gorlum for Project "SuperNova.WS"
#############################################################################
*/

/**
*
* @package language
* @system [Russian]
* @version 35a8.8
*
* @clean - all constants is used
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

// Officers/mercenaries
$lang = array_merge($lang, array(
  'mrc_up_to' => 'до',
  'mrc_hire' => 'Нанять',
  'mrc_hire_for' => 'Нанять за',
  'mrc_msg_error_wrong_mercenary' => 'Неправильный идентификатор наемника',
  'mrc_msg_error_wrong_level' => 'Неправильный уровень наемника',
  'mrc_msg_error_wrong_period' => 'Недопустимый срок найма',
  'mrc_msg_error_already_hired' => 'Наемник уже рекрутирован. Дождитесь окончания срока найма',
  'mrc_msg_error_no_resource' => 'Не хватает Тёмной Материи для найма',
  'mrc_msg_error_requirements' => 'Не удовлетворены требования',

  'mrc_dismiss' => 'Уволить',
  'mrc_dismiss_confirm' => 'При увольнении наемника теряется вся ТМ, раннее затраченная на его найм! Вы точно хотите уволить наемника?',
  'mrc_dismiss_before_hire' => 'Что бы изменить уровень рекрутированного наемника нужно сначала уволить текущего - с потерей потраченных на найм ТМ',

));

?>
