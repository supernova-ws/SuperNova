<?php

/*
#############################################################################
#  Filename: market.mo
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
* @condition clear
*
*/

/**
* DO NOT CHANGE
*/

if (!defined('INSIDE')) die();

$a_lang_array = (array(
  'eco_mrk_title' => 'Чёрный Рынок',
  'eco_mrk_description' => 'Странно, но в описании к интерфейсу управления Империей не было такого пункта... Интересно, откуда он взялся?',
  'eco_mrk_service' => 'Услуга',
  'eco_mrk_service_cost' => 'Стоимость услуги',

  'eco_mrk_trader_do' => 'Обменять ресурсы',
  'eco_mrk_trader' => 'Обмен ресурсов',
  'eco_mrk_trader_cost' => 'Стоимость обмена ресурсов',
  'eco_mrk_trader_exchange' => 'Количество обмениваемых ресурсов',
  'eco_mrk_trader_to' => 'Обменять на',
  'eco_mrk_trader_course' => 'Курс',
  'eco_mrk_trader_left' => 'Итог операции',
  'eco_mrk_trader_resources_all' => 'Все ресурсы',
  'eco_mrk_trader_exchange_dm_confirm' => 'Вы точно хотите обменять {0} Тёмной Материи на ресурсы?',

  'eco_mrk_scraper_do' => 'Продать корабли скупщику',
  'eco_mrk_scraper' => 'Скупка кораблей',
  'eco_mrk_scraper_price' => 'Выход лома',
  'eco_mrk_scraper_perShip' => 'с корабля',
  'eco_mrk_scraper_total' => 'Всего',
  'eco_mrk_scraper_cost' => 'Продать корабли на лом стоит',
  'eco_mrk_scraper_onOrbit' => 'На орбите',
  'eco_mrk_scraper_to' => 'Пустить на слом',
  'eco_mrk_scraper_res' => 'Получен следующий лом:',
  'eco_mrk_scraper_ships' => 'Пущены на лом следующие корабли:',
  'eco_mrk_scraper_noShip' => 'На орбите нет кораблей',

  'eco_mrk_stockman_do' => 'Купить корабли у скупщика',
  'eco_mrk_stockman' => 'Продавец б/у кораблей',
  'eco_mrk_stockman_price' => 'Цена',
  'eco_mrk_stockman_perShip' => 'корабля',
  'eco_mrk_stockman_onStock' => 'У продавца',
  'eco_mrk_stockman_buy' => 'Купить корабли',
  'eco_mrk_stockman_res' => 'Стоимость купленных кораблей:',
  'eco_mrk_stockman_ships' => 'Куплены следующие корабли:',
  'eco_mrk_stockman_noShip' => 'У продавца сейчас нет кораблей для продажи',

  'eco_mrk_exchange' => 'Биржа обмена ресурсов',
  'eco_mrk_banker'   => 'Банкир',
  'eco_mrk_pawnshop' => 'Ломбард',

  'eco_mrk_info_do' => 'Купить информацию',
  'eco_mrk_info' => 'Продавец информации',
  'eco_mrk_info_description' => 'Во входящем почтовом ящике обнаружилось письмо со следующим содержанием:',
  'eco_mrk_info_description_2' => 'У меня есть доступ ко множеству интересной информации. Я могу поделиться ею с вами... за скромное вознаграждение. За один запрос - всего',
  'eco_mrk_info_buy' => 'Купить информацию',

  'eco_mrk_info_player' => 'Сведения об игроке',
  'eco_mrk_info_player_description' => 'Я могу узнать, какие наемники сейчас работают у игрока',
  'eco_mrk_info_player_message' => 'По моим достоверным сведениям, список наемников у игрока ID %1$d [%2$s] выглядит следующим образом:',

  'eco_mrk_info_not_hired' => 'не нанят',

  'eco_mrk_info_ally' => 'Сведения об Альянсе',
  'eco_mrk_info_online' => 'Текущая активность во Вселенной',

  'eco_mrk_info_msg_from' => 'Неотслеживаемый источник',

  'eco_mrk_error_title' => 'Чёрный Рынок - Ошибка',
  'eco_mrk_errors' => array(
    MARKET_RESOURCES => 'Операция прошла успешно',
    MARKET_SCRAPPER => 'Обмен ресурсов произошел успешно',
    MARKET_NOT_A_SHIP => 'Не надо пытаться продать что-нибудь, отличное от корабля!',
    MARKET_STOCKMAN => 'Не хватает Тёмной Материи для завершения операции',
    MARKET_NO_RESOURCES => 'Не хватает ресурсов для завершения операции',
    MARKET_PAWNSHOP => 'Вы пытаетесь пустить на лом больше кораблей, чем есть на орбите',
    MARKET_NO_STOCK => 'Вы пытаетесь купить больше кораблей, чем есть у продавца. Возможно, пока вы выбирали корабли, кто-то другой уже купил их',
    MARKET_ZERO_DEAL => 'Не указано количество ресурсов для обмена',
    MARKET_NOTHING => 'Нужно выбрать корабли для продажи',
    MARKET_ZERO_RES_STOCK => 'Нужно выбрать корабли для покупки',
    MARKET_NEGATIVE_SHIPS => 'Не надо пытаться продать отрицательное количество кораблей!',

    MARKET_NO_DM => 'Не хватает Тёмной Материи для завершения операции',
    MARKET_INFO_WRONG => 'Нет такой информации',
    MARKET_INFO_PLAYER => 'Информация куплена успешно. Проверьте свой почтовый ящик',
    MARKET_INFO_PLAYER_WRONG => 'Нужно указать ID или имя игрока',
    MARKET_INFO_PLAYER_NOT_FOUND => 'Не могу идентифицировать игрока. Если имя игрока состоит из цифр или нечитаемо - попробуйте использовать его ID',
    MARKET_INFO_PLAYER_SAME => 'Зачем узнавать информацию о самом себе?',
  ),

));
