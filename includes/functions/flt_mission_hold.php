<?php

function flt_mission_hold( $fleet_row)
{
  if ($fleet_row['fleet_end_stay'] <= $GLOBALS['time_now'])
  {
    doquery("UPDATE {{fleets}} SET `fleet_mess` = 1 WHERE `fleet_id` = '{$fleet_row['fleet_id']}' LIMIT 1;");
    return CACHE_FLEET;
  }

  return CACHE_NOTHING;
}

?>
