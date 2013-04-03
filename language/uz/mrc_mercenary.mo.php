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
* @version 37a5.8
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
  'mrc_up_to' => 'gacha',
  'mrc_hire' => 'Yollash',
  'mrc_hire_for' => 'Yollash xaqi:',
  'mrc_msg_error_wrong_mercenary' => 'Yollanma ishchining noto`gri idendifakatori',
  'mrc_msg_error_wrong_level' => 'Yollanma ishchining noto`g`ri bosqichi',
  'mrc_msg_error_wrong_period' => 'Yolloshning noto`g`ri muddati',
  'mrc_msg_error_already_hired' => 'Yollanma ishchi allaqachon yollanngan. Yollanishning muddati tugashini kuting',
  'mrc_msg_error_no_resource' => 'Yollash uchun sizda TM yetishmaydi',
  'mrc_msg_error_requirements' => 'Talab qondirilmadi',

  'mrc_dismiss' => 'Bo`shatish',
  'mrc_dismiss_confirm' => 'Yollanma ishchini bo`shatish uchun barcha TM ingizdan ajralasiz. Siz aniq yollanma ishchini bo`shatishni xoxlaysizmi?',
  'mrc_dismiss_before_hire' => 'Siz yollanma ishchini bosqichini ko`tarishingiz uchun amaldagi ishchini bo`shatisgingiz lozim. Bu esa sizning TM laringizga zarar keltirishi mumkin',

));

?>
