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
* @version 34a15
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
  'mrc_period_list' => array(
    PERIOD_MINUTE    => '1 минута',
    PERIOD_MINUTE_3  => '3 минуты',
    PERIOD_MINUTE_5  => '5 минут',
    PERIOD_MINUTE_10 => '10 минут',
    PERIOD_DAY       => '1 день',
    PERIOD_DAY_3     => '3 дня',
    PERIOD_WEEK      => '1 неделя',
    PERIOD_WEEK_2    => '2 недели',
    PERIOD_MONTH     => '30 дней',
    PERIOD_MONTH_2   => '60 дней',
    PERIOD_MONTH_3   => '90 дней',
  ),

  'mrc_up_to' => 'до',
  'mrc_hire' => 'Нанять',
  'mrc_hire_for' => 'Нанять за',
  'mrc_msg_error_wrong_mercenary' => 'Неправильный идентификатор наемника',
  'mrc_msg_error_wrong_level' => 'Неправильный уровень наемника',
  'mrc_msg_error_wrong_period' => 'Недопустимый срок найма',
  'mrc_msg_error_already_hired' => 'Наемник уже рекрутирован. Дождитесь окончания срока найма',
  'mrc_msg_error_no_resource' => 'Не хватает Темной Материи для найма',
  'mrc_msg_error_requirements' => 'Не удовлетворены требования',

  'mrc_dismiss' => 'Уволить',
  'mrc_dismiss_confirm' => 'При увольнении наемника теряется вся ТМ, раннее затраченная на его найм! Вы точно хотите уволить наемника?',
  'mrc_dismiss_before_hire' => 'Что бы изменить уровень рекрутированного наемника нужно сначала уволить текущего - с потерей потраченных на найм ТМ',

));

?>
