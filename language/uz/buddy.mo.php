<?php

/*
#############################################################################
#  Filename: buddy.mo
#  Project: SuperNova.WS
#  Website: http://www.supernova.ws
#  Description: Massive Multiplayer Online Browser Space Startegy Game
#
#  Copyright © 2009-2012 Gorlum for Project "SuperNova.WS"
#############################################################################
*/

/**
*
* @package language
* @system [Uzbekin]
* @version 37a5.8
*
*/

/**
* DO NOT CHANGE
*/

if (!defined('INSIDE')) die();


$lang = array_merge($lang, array(
  'buddy_buddies' => 'Do`stlar',
  'buddy_request_text' => 'So\'rovnoma matni',
  'buddy_request_text_default' => 'Do`stlar ro`yxatiga qo`shishingizni so`rayman',
  'buddy_request_none' => 'Do`stlar yo`q, do`stlik uchun so`rovlar ham yo`q',
  'buddy_request_write_header' => 'Do`stlar safiga qo`shish uchun so`rov yuborish',
  'buddy_request_player_name' => 'o`yinchining nomi',
  'buddy_request_accept' => 'Do`stlar safiga o`yinchini qo`shish',

  'buddy_status' => 'Holati',
  'buddy_status_active' => 'Bu sizning o`zaro do`stingiz',
  'buddy_status_incoming_waiting' => 'Sziga dost sifatida qo`shish haqida so`rov kledi',
  'buddy_status_incoming_denied' => 'Siz do`stlik taklifini bekor qildingiz',
  'buddy_status_outcoming_waiting' => 'Sizning so`rovingiz jo`natildi. Javobini kuting',
  'buddy_status_outcoming_denied' => 'Sizning so`rovingiz bekor qilindi',

  // Result messages
  'buddy_err_not_exist' => 'So`rov mavjud emas. Siz uni o`chirgan yoki bekor qilgan bo`lishingiz mumkin',

  'buddy_err_accept_own' => 'Siz bu so`rovni `qabul qilashingiz mumkin emas',
  'buddy_err_accept_alien' => 'Siz bu so`rovni `qabul qilashingiz mumkin emas. Bu sizga yo`llangan emas.',
  'buddy_err_accept_already' => 'Siz bu so`rovni qabul qabul qilgansiz, siz allaqachon o`yinchining do`sti hisoblanasiz',
  'buddy_err_accept_denied' => 'Siz allaqachon so`rovni bekor qilgansiz va uni hozir qabul qila olmaysiz',
  'buddy_err_accept_internal' => 'So`rovni qabul qilayotganingizda xa`tolik ro`y berdi. Bir necha marotaba urunib ko`ring.Agar xatolik yana davom etsa server admistratsiyasiga bog`laning',
  'buddy_err_accept_none' => 'So`rov muvaffaqiyatli qabul qilindi',

  'buddy_err_delete_alien' => 'Bu so`rov siz tamonongizdan yaratilmagan va bu siz uchun emas!',
  'buddy_err_unfriend_none' => 'Siz do`stlik aloqasini bekor qildingiz',
  'buddy_err_delete_own' => 'So`rov muvaffaqiyatli o`chirildi',

  'buddy_err_deny_none' => 'Siz boshqa o`yinchilar beilan do`stlashishni bekor qildingiz. Nimaga?',

  'buddy_err_adding_exists' => 'Siz ushbu o`yinchiga so`rov yubora olmaysiz - siz allaqachon do`st hisoblanasiz ',
  'buddy_err_adding_none' => 'Sizning taklifingiz do`stlarga jo`natildi',
  'buddy_err_adding_self' => 'Siz o`zingizni do`st sifatida qabul qila olmaysiz',

  // PM messages
  'buddy_msg_accept_title' => 'Sizda yangi do`st paydo bo`ldi!',
  'buddy_msg_accept_text' => 'o`yinchi %s sizni o`zining do`stlar safiga qo`shdi!',
  'buddy_msg_unfriend_title' => 'Siz do`stingizni yo`qotdingiz!',
  'buddy_msg_unfriend_text' => 'o`yinchi %s sizning do`stlik aloqangizni yo`q qildi',
  'buddy_msg_deny_title' => 'Yangi do\'st orttirilmadi',
  'buddy_msg_deny_text' => 'o`yinchi  %s siz bilan do`stlashishni xoxlamadi',
  'buddy_msg_adding_title' => 'Do`stlashish taklifi',
  'buddy_msg_adding_text' => 'o`yinchi %s sizni do`st sifatida qabul qildi',

  'buddy_hint' => '
    <li>Do`stlashish taklifini menyu bo`limi orqali yuborish <a href="search.php">Qidiruv</a></li>
    <li>Siz do`stingizni xolatini ko`ra olasiz ya`ni uni onlineda yoki offlineda ekanligini. .</li>
    <li>Agar siz do`stlikni bekor qilsangiz, o`yinchi bilan do`st sifatida davom ettira olmaysiz </li>',

));

?>
