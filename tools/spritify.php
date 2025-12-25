<?php

/** Created by Gorlum 08.01.2024 18:59 */

/**
 * Generate one PNG sprite file from several small files
 *
 * This CLI tool will generate one sprite PNG file from input (all files in specified directory) along with CSS file
 * Filenames would be used as a key for CSS qualifier
 * Prefix and suffix can be used to produce different types of CSS qualifiers: IDs, classes, nested qualifiers etc
 * Have support to scale all sprites to single-sized square
 *
 * @version 46d0
 */

/** @noinspection PhpRedundantOptionalArgumentInspection */

require_once __DIR__ . '/classes/Spritify.php';
require_once __DIR__ . '/../vendor/autoload.php';

use Tools\Spritify;

// Tests
//Spritify::go('', __DIR__ . '/.output/', 'DELETE', '#DELETE', '', 0, '/design/images/');
//Spritify::go(__DIR__ . '/../skins/EpicBlue/icons/', __DIR__ . '/.output/', 'menu_icons_full', '#icon_full_', '', 0, '/design/images/');
//Spritify::go(__DIR__ . '/../includes/', __DIR__ . '/.output/', 'NO_FILES', '#NO_FILES_', '', 0, '/design/images/');
//Spritify::go(__DIR__ . '/../includes/zzzzz/', __DIR__ . '/.output/', 'NOT_EXISTS', '#NOT_EXISTS', '', 0, '/design/images/');
//Spritify::go(__DIR__ . '/../skins/EpicBlue/icn/*', __DIR__ . '/.output/', 'menu_icons_full', '#icon_full_', '', 0, '/design/images/');

//Spritify::go(__DIR__ . '/../skins/EpicBlue/icons/*', __DIR__ . '/.output/', 'menu_icons_full', '#icon_full_', '', 0, '/design/images/');

// WiP
Spritify::go(
  [
    __DIR__ . '/../design/images/smileys/blink.gif',
    __DIR__ . '/../design/images/smileys/cool.gif',
    __DIR__ . '/../design/images/smileys/bomb.gif',
    __DIR__ . '/../design/images/smileys/blush.gif',
    __DIR__ . '/../design/images/smileys/maniac.gif',

//    __DIR__ . '/../design/images/smileys/accordion.gif',
//    __DIR__ . '/../design/images/smileys/aggressive.gif',
//    __DIR__ . '/../design/images/smileys/angel.gif',
//    __DIR__ . '/../design/images/smileys/bad.gif',
//    __DIR__ . '/../design/images/smileys/ban.gif',
//    __DIR__ . '/../design/images/smileys/bayan.gif',
//    __DIR__ . '/../design/images/smileys/blackeye.gif',
//    __DIR__ . '/../design/images/smileys/blink.gif',
//    __DIR__ . '/../design/images/smileys/blush.gif',
//    __DIR__ . '/../design/images/smileys/bomb.gif',
//    __DIR__ . '/../design/images/smileys/censored.gif',
//    __DIR__ . '/../design/images/smileys/clapping.gif',
//    __DIR__ . '/../design/images/smileys/coctail.gif',
//    __DIR__ . '/../design/images/smileys/coffee.gif',
//    __DIR__ . '/../design/images/smileys/contract.gif',
//    __DIR__ . '/../design/images/smileys/cool.gif',
//    __DIR__ . '/../design/images/smileys/cray.gif',
//    __DIR__ . '/../design/images/smileys/crazy.gif',
//    __DIR__ . '/../design/images/smileys/diablo.gif',
//    __DIR__ . '/../design/images/smileys/dirol.gif',
//    __DIR__ . '/../design/images/smileys/drinks.gif',
//    __DIR__ . '/../design/images/smileys/facepalm.gif',
//    __DIR__ . '/../design/images/smileys/fool.gif',
//    __DIR__ . '/../design/images/smileys/friends.gif',
//    __DIR__ . '/../design/images/smileys/give_rose.gif',
//    __DIR__ . '/../design/images/smileys/good.gif',
//    __DIR__ . '/../design/images/smileys/help.gif',
//    __DIR__ . '/../design/images/smileys/index.html',
//    __DIR__ . '/../design/images/smileys/lol.gif',
//    __DIR__ . '/../design/images/smileys/maniac.gif',
//    __DIR__ . '/../design/images/smileys/mellow.gif',
//    __DIR__ . '/../design/images/smileys/mill.gif',
//    __DIR__ . '/../design/images/smileys/nea.gif',
//    __DIR__ . '/../design/images/smileys/new_year',
//    __DIR__ . '/../design/images/smileys/panic.gif',
//    __DIR__ . '/../design/images/smileys/pardon.gif',
//    __DIR__ . '/../design/images/smileys/pleasantry.gif',
//    __DIR__ . '/../design/images/smileys/plushit.gif',
//    __DIR__ . '/../design/images/smileys/poke.gif',
//    __DIR__ . '/../design/images/smileys/popcorn.gif',
//    __DIR__ . '/../design/images/smileys/pray.gif',
//    __DIR__ . '/../design/images/smileys/rofl.gif',
//    __DIR__ . '/../design/images/smileys/sad.gif',
//    __DIR__ . '/../design/images/smileys/sarcasm.gif',
//    __DIR__ . '/../design/images/smileys/shok.gif',
//    __DIR__ . '/../design/images/smileys/shout.gif',
//    __DIR__ . '/../design/images/smileys/smile.gif',
//    __DIR__ . '/../design/images/smileys/sorry.gif',
//    __DIR__ . '/../design/images/smileys/spiteful.gif',
//    __DIR__ . '/../design/images/smileys/suicide.gif',
//    __DIR__ . '/../design/images/smileys/tease.gif',
//    __DIR__ . '/../design/images/smileys/tongue.gif',
//    __DIR__ . '/../design/images/smileys/unknw.gif',
//    __DIR__ . '/../design/images/smileys/wall.gif',
//    __DIR__ . '/../design/images/smileys/whistle.gif',
//    __DIR__ . '/../design/images/smileys/wink.gif',
//    __DIR__ . '/../design/images/smileys/yahoo.gif',
//    __DIR__ . '/../design/images/smileys/yu.gif',
//    __DIR__ . '/../design/images/smileys/hmm.gif',
//
////    Gives problem if with other files
////    __DIR__ . '/../design/images/smileys/huh.gif',
  ],
  __DIR__ . '/.output/', 'smile_icons', '#icon_smile_', '', 64, '/design/images/'
);

// Actual converts
Spritify::go(__DIR__ . '/../skins/EpicBlue/icons/menu*', __DIR__ . '/.output/', 'menu_icons', '#icon_', '', 14, '/design/images/');
Spritify::go(__DIR__ . '/../design/images/navbar*', __DIR__ . '/.output/', 'sprite_navbar_buttons', '.', '_button', 0, '/skins/EpicBlue/images/');
Spritify::go(
  [
    // By file to move small hangar at the end to maintain pixel compatibility with EpicBlue navbar
    __DIR__ . '/../skins/supernova-ivash/navbar/navbar_dark_matter_standard.png',
    __DIR__ . '/../skins/supernova-ivash/navbar/navbar_defense.png',
    __DIR__ . '/../skins/supernova-ivash/navbar/navbar_expedition_standard.png',
    __DIR__ . '/../skins/supernova-ivash/navbar/navbar_fleet_own_standard.png',
    __DIR__ . '/../skins/supernova-ivash/navbar/navbar_hangar_scaled.png',
    __DIR__ . '/../skins/supernova-ivash/navbar/navbar_mail_standard.png',
    __DIR__ . '/../skins/supernova-ivash/navbar/navbar_metamatter_standard.png',
    __DIR__ . '/../skins/supernova-ivash/navbar/navbar_quest_standard.png',
    __DIR__ . '/../skins/supernova-ivash/navbar/navbar_research_64x64.png',
    __DIR__ . '/../skins/supernova-ivash/navbar/navbar_research_standard.png',
    __DIR__ . '/../skins/supernova-ivash/navbar/navbar_hangar.png',
  ], __DIR__ . '/.output/', 'sprite_navbar_buttons_ivash', '.', '_button_ivash', 0, '/skins/supernova-ivash/images/');
