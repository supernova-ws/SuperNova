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
* @system [Spanish]
* @version 46d0
* @condition clear
*
*/

/**
* DO NOT CHANGE
*/

if (!defined('INSIDE')) die();

$a_lang_array = (array(
  'eco_mrk_title' => 'Mercado Negro',
  'eco_mrk_description' => 'Curiosamente, en la descripción de la interfaz de gestión del Imperio no había tal punto... ¿Me pregunto de dónde salió?',
  'eco_mrk_service' => 'Servicio',
  'eco_mrk_service_cost' => 'Costo del servicio',

  'eco_mrk_trader_do' => 'Intercambiar recursos',
  'eco_mrk_trader' => 'Intercambio de recursos',
  'eco_mrk_trader_cost' => 'Costo del intercambio de recursos',
  'eco_mrk_trader_exchange' => 'Cantidad de recursos a intercambiar',
  'eco_mrk_trader_to' => 'Intercambiar por',
  'eco_mrk_trader_course' => 'Tasa',
  'eco_mrk_trader_left' => 'Resultado de la operación',
  'eco_mrk_trader_resources_all' => 'Todos los recursos',
  'eco_mrk_trader_exchange_dm_confirm' => '¿Estás seguro de que deseas intercambiar {0} Materia Oscura por recursos?',

  'eco_mrk_scraper_do' => 'Vender naves al desguazador',
  'eco_mrk_scraper' => 'Desguace de naves',
  'eco_mrk_scraper_price' => 'Rendimiento de chatarra',
  'eco_mrk_scraper_perShip' => 'por nave',
  'eco_mrk_scraper_total' => 'Total',
  'eco_mrk_scraper_cost' => 'Vender naves como chatarra cuesta',
  'eco_mrk_scraper_onOrbit' => 'En órbita',
  'eco_mrk_scraper_to' => 'Enviar al desguace',
  'eco_mrk_scraper_res' => 'Se obtuvo la siguiente chatarra:',
  'eco_mrk_scraper_ships' => 'Las siguientes naves fueron desguazadas:',
  'eco_mrk_scraper_noShip' => 'No hay naves en órbita',

  'eco_mrk_stockman_do' => 'Comprar naves al desguazador',
  'eco_mrk_stockman' => 'Vendedor de naves usadas',
  'eco_mrk_stockman_price' => 'Precio',
  'eco_mrk_stockman_perShip' => 'por nave',
  'eco_mrk_stockman_onStock' => 'En stock',
  'eco_mrk_stockman_buy' => 'Comprar naves',
  'eco_mrk_stockman_res' => 'Costo de las naves compradas:',
  'eco_mrk_stockman_ships' => 'Se compraron las siguientes naves:',
  'eco_mrk_stockman_noShip' => 'El vendedor no tiene naves disponibles en este momento',

  'eco_mrk_exchange' => 'Bolsa de intercambio de recursos',
  'eco_mrk_banker'   => 'Banquero',
  'eco_mrk_pawnshop' => 'Casa de empeño',

  'eco_mrk_info_do' => 'Comprar información',
  'eco_mrk_info' => 'Vendedor de información',
  'eco_mrk_info_description' => 'En el buzón de entrada se encontró un mensaje con el siguiente contenido:',
  'eco_mrk_info_description_2' => 'Tengo acceso a mucha información interesante. Puedo compartirla contigo... por una modesta recompensa. Por una consulta, solo',
  'eco_mrk_info_buy' => 'Comprar información',

  'eco_mrk_info_player' => 'Información sobre el jugador',
  'eco_mrk_info_player_description' => 'Puedo averiguar qué mercenarios están trabajando actualmente para el jugador',
  'eco_mrk_info_player_message' => 'Según mis fuentes confiables, la lista de mercenarios del jugador ID %1$d [%2$s] es la siguiente:',

  'eco_mrk_info_not_hired' => 'no contratado',

  'eco_mrk_info_ally' => 'Información sobre la Alianza',
  'eco_mrk_info_online' => 'Actividad actual en el universo',

  'eco_mrk_info_msg_from' => 'Fuente no rastreable',

  'eco_mrk_error_title' => 'Mercado Negro - Error',
  'eco_mrk_errors' => array(
    MARKET_RESOURCES => 'Operación completada con éxito',
    MARKET_SCRAPPER => 'Intercambio de recursos realizado con éxito',
    MARKET_NOT_A_SHIP => '¡No intentes vender algo que no sea una nave!',
    MARKET_STOCKMAN => 'No hay suficiente Materia Oscura para completar la operación',
    MARKET_NO_RESOURCES => 'No hay suficientes recursos para completar la operación',
    MARKET_PAWNSHOP => 'Intentas desguazar más naves de las que hay en órbita',
    MARKET_NO_STOCK => 'Intentas comprar más naves de las que tiene el vendedor. Quizás, mientras seleccionabas, alguien más ya las compró',
    MARKET_ZERO_DEAL => 'No se especificó la cantidad de recursos para intercambiar',
    MARKET_NOTHING => 'Debes seleccionar naves para vender',
    MARKET_ZERO_RES_STOCK => 'Debes seleccionar naves para comprar',
    MARKET_NEGATIVE_SHIPS => '¡No intentes vender una cantidad negativa de naves!',

    MARKET_NO_DM => 'No hay suficiente Materia Oscura para completar la operación',
    MARKET_INFO_WRONG => 'No existe tal información',
    MARKET_INFO_PLAYER => 'Información comprada con éxito. Revisa tu buzón',
    MARKET_INFO_PLAYER_WRONG => 'Debes especificar el ID o nombre del jugador',
    MARKET_INFO_PLAYER_NOT_FOUND => 'No puedo identificar al jugador. Si el nombre del jugador consiste en números o es ilegible, intenta usar su ID',
    MARKET_INFO_PLAYER_SAME => '¿Para qué quieres información sobre ti mismo?',
  ),
));