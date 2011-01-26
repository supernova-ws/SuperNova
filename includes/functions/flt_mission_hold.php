<?php

function flt_mission_hold($mission_data)
{
  $fleet_row = $mission_data['fleet'];

  if ($fleet_row['fleet_end_stay'] <= $GLOBALS['time_now'])
  {
    doquery("UPDATE {{fleets}} SET `fleet_mess` = 1 WHERE `fleet_id` = '{$fleet_row['fleet_id']}' LIMIT 1;");
    return CACHE_FLEET;
  }

  return CACHE_NOTHING;
}

?>
