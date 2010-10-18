<?php

// Compare function to sort fleet in time order
function tpl_assign_fleet_compare($a, $b)
{
  if($a['fleet']['OV_THIS_PLANET'] == $b['fleet']['OV_THIS_PLANET'])
  {
    if($a['fleet']['OV_LEFT'] == $b['fleet']['OV_LEFT'])
    {
      return 0;
    }
    return ($a['fleet']['OV_LEFT'] < $b['fleet']['OV_LEFT']) ? -1 : 1;
  }
  else
  {
    return $a['fleet']['OV_THIS_PLANET'] ? -1 : 1;
  }
}

function tpl_assign_fleet(&$template, $fleets, $js_name = 'fleets')
{
  if(!$fleets)
  {
    return;
  }

  usort($fleets, 'tpl_assign_fleet_compare');

  foreach($fleets as $fleet_data)
  {
    $template->assign_block_vars($js_name, $fleet_data['fleet']);

    if($fleet_data['ships'])
    {
      foreach($fleet_data['ships'] as $ship_data)
      {
        $template->assign_block_vars("{$js_name}.ships", $ship_data);
      }
    }
  }
}

?>