<?php

/**
 * messages.php
 * Handles internal message system
 *
 * @package messages
 * @version 3.0
 *
 * Revision History
 * ================
 *
 * 3.0 - copyright (c) 2010-2011 by Gorlum for http://supernova.ws
 *   [!] Full rewrite
 *
 * 2.0 - copyright (c) 2010 by Gorlum for http://supernova.ws
 *   [!] Fully rewrote MessPageMode = 'show' part
 *   [~] All HTML code from 'show' part moved to messages.tpl
 *   [~] Tweaks and optimizations
 *
 * 1.5 - copyright (c) 2010 by Gorlum for http://supernova.ws
 *   [~] Replaced table 'galaxy' with table 'planets'
 *
 * 1.4 - copyright (c) 2010 by Gorlum for http://supernova.ws
 *   [~] Security checked & verified for SQL-injection by Gorlum for http://supernova.ws
 *
 * 1.3 - copyright (c) 2010 by Gorlum for http://supernova.ws
 *   [+] "Outbox" added
 *
 * 1.2 - copyright 2008 by Chlorel for XNova
 *   [+] Regroupage des 2 fichiers vers 1 seul plus simple a mettre en oeuvre et a gerer !
 *
 * 1.1 - Mise a plat, linearisation, suppression des doublons / triplons / 'n'gnions dans le code (Chlorel)
 *
 * 1.0 - Version originelle (Tom1991)
 *
 */

include('common.' . substr(strrchr(__FILE__, '.'), 1));

global $sn_message_class_list, $user, $lang;

lng_include('messages');

$messagePage = new \Pages\Deprecated\PageMessage();
$messagePage->route();
