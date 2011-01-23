<?php

/**
 * This file is under the GPL liscence, which must be included with the file under distrobution (license.txt)
 * This file was made by Anthony (MadnessRed) [http://madnessred.co.cc/]
 * This file return acs fleets as missioncaseattack will ont return attack fleets.
 * Do not edit this comment block
 */

function MissionCaseACS ( $FleetRow)
{
  if ($FleetRow['fleet_mess'] == 0 && $FleetRow['fleet_start_time'] > time())
  {
    //Well... acs in dealt with in misioncaseattack.php, so all we need to do is make the fleet return
    doquery( "UPDATE {{fleets}} SET `fleet_mess` = '1' WHERE `fleet_id` = '{$FleetRow['fleet_id']}' LIMIT 1;");
  }
  elseif ($FleetRow['fleet_end_time'] <= time())
  {
    RestoreFleetToPlanet($FleetRow);
  }
}

// MadnessRed 2008
?>