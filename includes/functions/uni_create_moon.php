<?php

/**
 * uni_create_moon.php
 *
 * UNI: Create moon record
 *
 * V2.1  - copyright (c) 2010-2011 by Gorlum for http://supernova.ws
 *   [~] Renamed CreateOneMoonRecord to uni_create_moon
 *   [-] Removed unsed $MoonID parameter from call
 *   [~] PCG1 compliant
 * V2.0  - copyright (c) 2010 by Gorlum for http://supernova.ws
 *   [+] Deep rewrite to rid of using `galaxy` and `lunas` tables greatly reduce numbers of SQL-queries
 * @version 1.1
 * @copyright 2008
*/

function uni_create_moon($pos_galaxy, $pos_system, $pos_planet, $user_id, $moon_chance, $moon_name = '')
{
  global $lang, $time_now;

  $planet_name = '';

  $moon = doquery("SELECT * FROM `{{planets}}` WHERE `galaxy` = '{$pos_galaxy}' AND `system` = '{$pos_system}' AND `planet` = '{$pos_planet}' AND `planet_type` = 3 LIMIT 1;", '', true);

  if (!$moon['id'])
  {
    $moon_planet = doquery ("SELECT * FROM `{{planets}}` WHERE `galaxy` = '{$pos_galaxy}' AND `system` = '{$pos_system}' AND `planet` = '{$pos_planet}' AND `planet_type` = 1 LIMIT 1;", '', true);

    if ($moon_planet['id'])
    {
      $planet_name = $moon_planet['name'];

      $base_storage_size = BASE_STORAGE_SIZE;

      $size      = rand ( $moon_chance * 100 + 1000, $moon_chance * 200 + 2999 );
      $temp_min  = $moon_planet['temp_min'] - rand(10, 45);
      $temp_max  = $moon_planet['temp_max'] - rand(10, 45);
      $moon_name = $moon_name ? $moon_name : "{$lang['sys_moon']} {$lang['uni_moon_of_planet']} {$planet_name}";

      doquery(
        "INSERT INTO `{{planets}}` SET
          `id_owner` = '{$user_id}', `name` = '{$moon_name}', `last_update` = '{$time_now}',
          `galaxy` = '{$pos_galaxy}', `system` = '{$pos_system}', `planet` = '{$pos_planet}', `planet_type` = '3', `parent_planet` = '{$moon_planet['id']}',
          `image` = 'mond', `diameter` = '{$size}', `temp_min` = '{$temp_max}', `temp_max` = '{$temp_min}', `field_max` = '1',
          `metal` = '0', `metal_perhour` = '0', `metal_max` = '{$base_storage_size}',
          `crystal` = '0', `crystal_perhour` = '0', `crystal_max` = '{$base_storage_size}',
          `deuterium` = '0', `deuterium_perhour` = '0', `deuterium_max` = '{$base_storage_size}';"
      );
    }
  }

  return $planet_name;
}

?>
