<?php

/*
#############################################################################
#  Filename: market.mo
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
* @version 31a22.2
* @condition clear
*
*/

/**
* DO NOT CHANGE
*/

if (!defined('INSIDE')) die();


$lang = array_merge($lang, array(
  'eco_mrk_title' => 'Чёрный Рынок',
  'eco_mrk_description' => 'Странно, но в описании к интерфейсу управления Империей не было такого пункта... Интересно, откуда он взялся?',
  'eco_mrk_service' => 'Услуга',
  'eco_mrk_service_cost' => 'Стоимость услуги',

  'eco_mrk_trader' => 'Обмен ресурсов',
  'eco_mrk_trader_cost' => 'Стоимость обмена ресурсов',
  'eco_mrk_trader_exchange' => 'Обмен',
  'eco_mrk_trader_to' => 'Обменять на',
  'eco_mrk_trader_course' => 'Курс',
  'eco_mrk_trader_left' => 'Остаток',
  'eco_mrk_trader_resources_all' => 'Все ресурсы',

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

  'eco_mrk_stockman' => 'Продавец б/у кораблей',
  'eco_mrk_stockman_price' => 'Цена',
  'eco_mrk_stockman_perShip' => 'корабля',
  'eco_mrk_stockman_onStock' => 'У продавца',
  'eco_mrk_stockman_buy' => 'Купить корабли',
  'eco_mrk_stockman_res' => 'Стоимость купленных кораблей:',
  'eco_mrk_stockman_ships' => 'Куплены следующие корабли:',
  'eco_mrk_stockman_noShip' => 'У продавца сейчас нет кораблей для продажи',

  'eco_mrk_exchange' => 'Биржа обмена ресурсов',
  'eco_mrk_banker' => 'Банкир',
  'eco_mrk_pawnshop' => 'Ломбард',

  'eco_mrk_error_title' => 'Чёрный Рынок - Ошибка',
  'eco_mrk_errors' => array(
    MARKET_RESOURCES => 'Операция прошла успешно',
    MARKET_SCRAPPER => 'Обмен ресурсов произошел успешно',
    MARKET_NOT_A_SHIP => 'Не надо пытаться продать что-нибудь, отличное от корабля!',
    MARKET_STOCKMAN => 'Не хватает Темной Материи для завершения операции',
    MARKET_NO_RESOURCES => 'Не хватает ресурсов для завершения операции',
    MARKET_PAWNSHOP => 'Вы пытаетесь пустить на лом больше кораблей, чем есть на орбите',
    MARKET_NO_STOCK => 'Вы пытаетесь купить больше кораблей, чем есть у продавца. Возможно, пока вы выбирали корабли, кто-то другой уже купил их',
    MARKET_ZERO_DEAL => 'Не указано количество ресурсов для обмена',
    MARKET_NOTHING => 'Нужно выбрать корабли для продажи',
    MARKET_ZERO_RES_STOCK => 'Нужно выбрать корабли для покупки',
    MARKET_NEGATIVE_SHIPS => 'Не надо пытаться продать отрицательное количество кораблей!',
  ),

));

?>
