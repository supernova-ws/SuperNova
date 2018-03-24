<?php

/*
#############################################################################
#  Filename: buddy.mo
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
*/

/**
* DO NOT CHANGE
*/

if (!defined('INSIDE')) die();


$a_lang_array = (array(
  'buddy_buddies' => 'Друзья',
  'buddy_request_text' => 'Текст запроса',
  'buddy_request_text_default' => 'Прошу добавить меня в список друзей',
  'buddy_request_none' => 'Нет ни друзей, ни заявок на дружбу',
  'buddy_request_write_header' => 'Отправить запрос на добавление в друзья',
  'buddy_request_player_name' => 'Имя игрока',
  'buddy_request_accept' => 'Добавить игрока в список друзей',

  'buddy_status' => 'Статус',
  'buddy_status_active' => 'Это ваш взаимный друг',
  'buddy_status_incoming_waiting' => 'Вам пришел запрос на добавление вас в друзья',
  'buddy_status_incoming_denied' => 'Вы отклонили предложение дружбы',
  'buddy_status_outcoming_waiting' => 'Ваш запрос отправлен. Ждите ответа',
  'buddy_status_outcoming_denied' => 'Ваш запрос отклонен',

  // Result messages
  'buddy_err_not_exist' => 'Указанная заявка не существует. Возможно, вы её удалили или отвергли, либо она была отозвана её автором',

  'buddy_err_accept_own' => 'Вы не можете принять свою же заявку',
  'buddy_err_accept_alien' => 'Вы не можете принять заявку, которая направлена не вам',
  'buddy_err_accept_already' => 'Вы уже приняли эту заявку раньше и являетесь другом этого игрока',
  'buddy_err_accept_denied' => 'Вы уже отклонили эту заявку и теперь не можете её принять',
  'buddy_err_accept_internal' => 'Во время принятия заявки возникла ошибка. Попробуйте еще раз через некоторое время. Если ошибка не пропала - обратитесь к администрации сервера',
  'buddy_err_accept_none' => 'Заявка успешно принята',

  'buddy_err_delete_alien' => 'Эта заявка создана не вами и не для вас! Не стоит вмешиваться в отошения других людей! Поищите лучше себе друзей!',
  'buddy_err_unfriend_none' => 'Вы разорвали дружеские отношения',
  'buddy_err_delete_own' => 'Ваша заявка успешно удалена',

  'buddy_err_deny_none' => 'Вы отказались дружить с другим игроком. Почему?',

  'buddy_err_adding_exists' => 'Нельзя отправить запрос этому игроку - вы уже являетесь друзьями или существуют какие-то предложения дружбы между вами',
  'buddy_err_adding_none' => 'Ваше предложение дружбы отправлено',
  'buddy_err_adding_self' => 'Нельзя добавить себя в друзья',

  // PM messages
  'buddy_msg_accept_title' => 'У вас появился новый друг!',
  'buddy_msg_accept_text' => 'Игрок %s добавил вас в свой список друзей!',
  'buddy_msg_unfriend_title' => 'Вы потеряли друга!',
  'buddy_msg_unfriend_text' => 'Игрок %s разорвал с вами дружеские отношения и вычеркнул вас из списка друзей. Как это грустно...',
  'buddy_msg_deny_title' => 'Не удалось завести нового друга',
  'buddy_msg_deny_text' => 'Игрок %s не захотел с вами дружить',
  'buddy_msg_adding_title' => 'Предложение дружбы',
  'buddy_msg_adding_text' => 'Игрок %s предлагает вам дружить',

  'buddy_hint' => '
    <li>Послать предложение дружбы можно через пункт меню <a href="search.php">Поиск</a></li>
    <li>Вы можете видеть статус ваших друзей - находятся ли они онлайн или оффлайн. Однако и друзья могут видеть ваш статус. Учитывайте данный факт перед принятием предложения о дружбе.</li>
    <li>Если вы отклонили предложение дружбы, то вы не сможете начать дружеские отношения с этим игроком, пока он не удалит свой запрос</li>',

));
