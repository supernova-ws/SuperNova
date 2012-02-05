<?php

function eco_can_build_unit($user, $planet, $unit_id)
{
  global $sn_data;

  $accessible = BUILD_ALLOWED;
  if(isset($sn_data[$unit_id]['require']))
  {
    foreach($sn_data[$unit_id]['require'] as $require_id => $require_level)
    {
      $db_name = $sn_data[$require_id]['name'];
      $data = in_array($require_id, $sn_data['groups']['mercenaries']) ? mrc_get_level($user, $planet, $require_id) : (
        isset($planet[$db_name]) ? $planet[$db_name] : (
          isset($user[$db_name]) ? $user[$db_name] : (
            $require_id == $planet['PLANET_GOVERNOR_ID'] ? $planet['PLANET_GOVERNOR_LEVEL'] : 0
          )
        )
      );

      if($data < $require_level)
      {
        $accessible = BUILD_REQUIRE_NOT_MEET;
        break;
      }
    }
  }

  return $accessible;
}

?>
