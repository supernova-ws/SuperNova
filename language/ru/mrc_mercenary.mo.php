<?php

/*
#############################################################################
#  Filename: mercenary.mo
#  Project: SuperNova.WS
#  Website: http://www.supernova.ws
#  Description: Massive Multiplayer Online Browser Space Strategy Game
#
#  Copyright © 2009-2018 Gorlum for Project "SuperNova.WS"
#############################################################################
*/

/**
*
* @package language
* @system [Russian]
* @version 43a16.13
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

$a_lang_array = (array(
  'mrc_up_to' => 'до',
  'mrc_hire' => 'Нанять',
  'mrc_hire_for' => 'Нанять за',
  'mrc_allowed' => 'Доступные',
  'mrc_msg_error_wrong_mercenary' => 'Неправильный идентификатор Наёмника',
  'mrc_msg_error_wrong_level' => 'Неправильный уровень Наёмника',
  'mrc_msg_error_wrong_period' => 'Недопустимый срок найма',
  'mrc_msg_error_already_hired' => 'Наёмник уже рекрутирован. Увольте его или дождитесь окончания срока найма',
  'mrc_msg_error_no_resource' => 'Не хватает Тёмной Материи для найма',
  'mrc_msg_error_requirements' => 'Не удовлетворены требования для найма',

  'mrc_dismiss' => 'Уволить',
  'mrc_dismiss_confirm' => 'При увольнении Наёмника теряется вся ТМ, раннее затраченная на его найм! Вы точно хотите уволить Наёмника?',
  'mrc_dismiss_before_hire' => 'Что бы изменить уровень рекрутированного Наёмника нужно сначала уволить текущего - с потерей потраченных на найм ТМ',

  'mrc_mercenary_hired_log' => 'Куплен Наёмник "%1$s" ID %2$d за %3$d ТМ на срок %4$d дней',
  'mrc_mercenary_dismissed_log' => 'ПОТЕРЯНО %7$d дней найма и %8$d ТМ по текущим ценам! Уволен Наёмник "%1$s" ID %2$d, нанятый на срок %4$d дней (с %5$s по %6$s) и стоящий сейчас %3$d ТМ',
  'mrc_plan_bought_log' => 'Куплен "%1$s" ID %2$d за %3$d ТМ',
));
