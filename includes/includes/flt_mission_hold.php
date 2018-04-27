<?php

use Fleet\DbFleetStatic;

function flt_mission_hold(&$mission_data)
{
  if($mission_data['fleet']['fleet_end_stay'] < SN_TIME_NOW)
  {
    DbFleetStatic::fleet_send_back($mission_data['fleet']);
    // doquery("UPDATE {{fleets}} SET `fleet_mess` = 1 WHERE `fleet_id` = '{$fleet_row['fleet_id']}' LIMIT 1;");
    return CACHE_FLEET;
  }

  return CACHE_NOTHING;
}
