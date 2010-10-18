<?php

/**
 * eco_bld_handle_que.php
 * Handles building in hangar
 *
 * @oldname HandleElementBuildingQueue.php
 * @package economic
 * @version 2
 *
 * Revision History
 * ================
 *    2 - copyright (c) 2010 by Gorlum for http://supernova.ws
 *      [!] Full rewrite
 *      [%] Fixed stupid bug that allows to build several fast-build
 *          units utilizing build-time of slow-build units upper in que
 *      [~] Some optimizations and speedups
 *      [~] Complies with PCG1
 *
 *    1 - copyright 2008 By Chlorel for XNova
 */

function eco_bld_handle_que($user, &$planet, $production_time)
{
  global $resource;

  if ($planet['b_hangar_id'] != 0)
  {
    $hangar_time = $planet['b_hangar'] + $production_time;
    $que = explode(';', $planet['b_hangar_id']);

    $built = array();
    $new_hangar = '';
    $skip_rest = false;
    foreach ($que as $que_string)
    {
      if ($que_string)
      {
        $que_data = explode(',', $que_string);

        $unit  = $que_data[0];
        $count = $que_data[1];
        $build_time = GetBuildingTime($user, $planet, $unit);

        if(!$skip_rest)
        {
          $planet_unit = $planet[$resource[$unit]];
          while ($hangar_time >= $build_time && $count > 0)
          {
            $hangar_time -= $build_time;
            $count--;
            $built[$unit]++;
            $planet_unit++;
          }
          $planet[$resource[$unit]] = $planet_unit;

          if($count)
          {
            $skip_rest = true;
          }
        }
        if($count > 0)
        {
          $new_hangar .= "{$unit},{$count};";
        }
      }
    }
    if(!$new_hangar)
    {
      $hangar_time = 0;
    }
    $planet['b_hangar']    = $hangar_time;
    $planet['b_hangar_id'] = $new_hangar;
  } else {
    $built = '';
    $planet['b_hangar'] = 0;
  }

  return $built;
}

?>
