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
* @system [German]
* @version 46d0
* @condition clear
*
*/

/**
* DO NOT CHANGE
*/

if (!defined('INSIDE')) die();

$a_lang_array = (array(
  'eco_mrk_title' => 'Schwarzmarkt',
  'eco_mrk_description' => 'Seltsam, aber in der Beschreibung der Imperiumsverwaltung gab es keinen solchen Punkt... Ich frage mich, woher er kommt?',
  'eco_mrk_service' => 'Dienstleistung',
  'eco_mrk_service_cost' => 'Kosten der Dienstleistung',

  'eco_mrk_trader_do' => 'Ressourcen tauschen',
  'eco_mrk_trader' => 'Ressourcentausch',
  'eco_mrk_trader_cost' => 'Kosten des Ressourcentauschs',
  'eco_mrk_trader_exchange' => 'Menge der zu tauschenden Ressourcen',
  'eco_mrk_trader_to' => 'Tauschen gegen',
  'eco_mrk_trader_course' => 'Kurs',
  'eco_mrk_trader_left' => 'Ergebnis der Operation',
  'eco_mrk_trader_resources_all' => 'Alle Ressourcen',
  'eco_mrk_trader_exchange_dm_confirm' => 'Möchten Sie wirklich {0} Dunkle Materie gegen Ressourcen tauschen?',

  'eco_mrk_scraper_do' => 'Schiffe an Schrotthändler verkaufen',
  'eco_mrk_scraper' => 'Schiffsverwertung',
  'eco_mrk_scraper_price' => 'Schrottwert',
  'eco_mrk_scraper_perShip' => 'pro Schiff',
  'eco_mrk_scraper_total' => 'Gesamt',
  'eco_mrk_scraper_cost' => 'Kosten für den Verkauf von Schiffen als Schrott',
  'eco_mrk_scraper_onOrbit' => 'Im Orbit',
  'eco_mrk_scraper_to' => 'Verschrotten',
  'eco_mrk_scraper_res' => 'Folgender Schrott wurde erhalten:',
  'eco_mrk_scraper_ships' => 'Folgende Schiffe wurden verschrottet:',
  'eco_mrk_scraper_noShip' => 'Keine Schiffe im Orbit',

  'eco_mrk_stockman_do' => 'Schiffe vom Schrotthändler kaufen',
  'eco_mrk_stockman' => 'Gebrauchtschiffhändler',
  'eco_mrk_stockman_price' => 'Preis',
  'eco_mrk_stockman_perShip' => 'pro Schiff',
  'eco_mrk_stockman_onStock' => 'Beim Händler',
  'eco_mrk_stockman_buy' => 'Schiffe kaufen',
  'eco_mrk_stockman_res' => 'Kosten der gekauften Schiffe:',
  'eco_mrk_stockman_ships' => 'Folgende Schiffe wurden gekauft:',
  'eco_mrk_stockman_noShip' => 'Der Händler hat derzeit keine Schiffe zum Verkauf',

  'eco_mrk_exchange' => 'Ressourcenbörse',
  'eco_mrk_banker'   => 'Bankier',
  'eco_mrk_pawnshop' => 'Pfandhaus',

  'eco_mrk_info_do' => 'Information kaufen',
  'eco_mrk_info' => 'Informationshändler',
  'eco_mrk_info_description' => 'Im Posteingang wurde eine Nachricht mit folgendem Inhalt gefunden:',
  'eco_mrk_info_description_2' => 'Ich habe Zugang zu vielen interessanten Informationen. Ich kann sie mit Ihnen teilen... gegen eine bescheidene Gegenleistung. Für eine Anfrage nur',
  'eco_mrk_info_buy' => 'Information kaufen',

  'eco_mrk_info_player' => 'Spielerinformationen',
  'eco_mrk_info_player_description' => 'Ich kann herausfinden, welche Söldner derzeit für einen Spieler arbeiten',
  'eco_mrk_info_player_message' => 'Nach meinen zuverlässigen Quellen sieht die Liste der Söldner für Spieler ID %1$d [%2$s] wie folgt aus:',

  'eco_mrk_info_not_hired' => 'nicht angeheuert',

  'eco_mrk_info_ally' => 'Allianzinformationen',
  'eco_mrk_info_online' => 'Aktuelle Aktivität im Universum',

  'eco_mrk_info_msg_from' => 'Nicht nachverfolgbare Quelle',

  'eco_mrk_error_title' => 'Schwarzmarkt - Fehler',
  'eco_mrk_errors' => array(
    MARKET_RESOURCES => 'Operation erfolgreich abgeschlossen',
    MARKET_SCRAPPER => 'Ressourcentausch erfolgreich durchgeführt',
    MARKET_NOT_A_SHIP => 'Versuchen Sie nicht, etwas anderes als Schiffe zu verkaufen!',
    MARKET_STOCKMAN => 'Nicht genug Dunkle Materie, um die Operation abzuschließen',
    MARKET_NO_RESOURCES => 'Nicht genug Ressourcen, um die Operation abzuschließen',
    MARKET_PAWNSHOP => 'Sie versuchen, mehr Schiffe zu verschrotten, als im Orbit vorhanden sind',
    MARKET_NO_STOCK => 'Sie versuchen, mehr Schiffe zu kaufen, als der Händler hat. Möglicherweise hat jemand anderes die Schiffe bereits gekauft, während Sie ausgewählt haben',
    MARKET_ZERO_DEAL => 'Keine Ressourcenmenge für den Tausch angegeben',
    MARKET_NOTHING => 'Sie müssen Schiffe zum Verkauf auswählen',
    MARKET_ZERO_RES_STOCK => 'Sie müssen Schiffe zum Kauf auswählen',
    MARKET_NEGATIVE_SHIPS => 'Versuchen Sie nicht, eine negative Anzahl von Schiffen zu verkaufen!',

    MARKET_NO_DM => 'Nicht genug Dunkle Materie, um die Operation abzuschließen',
    MARKET_INFO_WRONG => 'Keine solche Information verfügbar',
    MARKET_INFO_PLAYER => 'Information erfolgreich gekauft. Überprüfen Sie Ihren Posteingang',
    MARKET_INFO_PLAYER_WRONG => 'Sie müssen die ID oder den Namen des Spielers angeben',
    MARKET_INFO_PLAYER_NOT_FOUND => 'Spieler konnte nicht identifiziert werden. Wenn der Spielername aus Zahlen besteht oder nicht lesbar ist, versuchen Sie, die ID zu verwenden',
    MARKET_INFO_PLAYER_SAME => 'Warum Informationen über sich selbst erfragen?',
  ),
));