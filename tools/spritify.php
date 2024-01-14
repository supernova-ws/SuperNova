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
 * @version 46a88
 */

require_once __DIR__ . '/classes/Spritify.php';

use Tools\Spritify;

Spritify::go(__DIR__ . '/../skins/EpicBlue/icons/menu*', __DIR__ . '/_output/', 'menu_icons', '#icon_', '', 14, '/design/images/');
//Spritify::go(__DIR__ . '/../skins/EpicBlue/icons/menu*', __DIR__ . '/_output/', 'menu_icons_full', '#icon_full_', '', 0, '/design/images/');
Spritify::go(__DIR__ . '/../design/images/navbar*', __DIR__ . '/_output/', 'navbar', '#icon_', '', 64, '/design/images/');
