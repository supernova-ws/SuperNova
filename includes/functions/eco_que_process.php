<?php

function eco_que_process($user, &$planet, $time_passed)
{
  global $sn_data;

  $que_types = array(
    QUE_STRUCTURES => array(
      'id' => QUE_STRUCTURES,
      'time_left' => $time_passed,
      'unit_list' => $sn_data['groups']['build_allow'][$planet['planet_type']],
      'length' => 5
    ),

    QUE_HANGAR => array(
      'id' => QUE_HANGAR,
      'time_left' => $time_passed,
      'unit_list' => array_merge($sn_data['groups']['fleet'], $sn_data['groups']['defense']),
      'length' => 10
    ),

    QUE_RESEACH => array(
      'id' => QUE_RESEACH,
      'time_left' => $time_passed,
      'unit_list' => $sn_data['groups']['tech'],
      'length' => 1
    )
  );

  if(!$planet['que'])
  {
    return false;
  }

  $que_strings = explode(';', $planet['que']);
  foreach($que_strings as $que_item_string)
  {
    // skipping empty que lines
    if(!$que_item_string)
    {
      continue;
    }

    $que_item = explode(',', $que_item_string);
    $que_item = array(
      'ID'         => $que_item[0], // unit ID
      'AMOUNT'     => $que_item[1], // unit amount
      'BUILD_TIME' => $que_item[2], // build time left (in seconds)
      'BUILD_TYPE' => $que_item[3], // build/destroy
      'QUE'        => $que_item[4], // que ID
      'STRING'     => "{$que_item_string};",
    );

    $unit_id = $que_item['ID'];
    $unit_data = $sn_data[$unit_id];

    $time_passed = false;
    foreach($que_types as $que_id => $que_data)
    {
      if(in_array($unit_id, $que_data['unit_list']))
      {
        $time_passed = $que_data['time_left'];
        $unit_list   = $que_data['unit_list'];
        break;
      }
    }

    if($time_passed === false)
    {
      // This is not queable item. Remove it from que
      continue;
    }

    if($time_passed === 0)
    {
      // There is no time left in this que. Skipping
      $query_string .= $que_item['STRING'];
      $que[$que_id][] = $que_item;

      continue;
    }

    $change = 0;
    switch($que_item['BUILD_TYPE'])
    {
      case BUILD_CREATE:
        $change = +1;
      break;

      case BUILD_DESTROY:
        $change = -1;
      break;
    }

    $build_time = $que_item['BUILD_TIME'];
    $build_amount = min($que_item['AMOUNT'], floor($time_passed / $build_time));

    if($build_amount)
    {
      $unit_db_name = $unit_data['name'];

      $time_passed -= $build_amount * $build_time;

      $que_item['AMOUNT'] -= $build_amount;

      $change *= $build_amount;
      $built[$unit_id] += $change;
      $planet[$unit_db_name] += $change;
      $query .= "`{$unit_db_name}` = `{$unit_db_name}` + '{$change}',";
    }

    if($que_item['AMOUNT'] > 0)
    {
      $que_item['BUILD_TIME'] -= $time_passed;
      $time_passed = 0;
      $que_item['STRING'] = "{$unit_id},{$que_item['AMOUNT']},{$que_item['BUILD_TIME']},{$que_item['BUILD_TYPE']},{$que_item['QUE']};";

      $query_string .= $que_item['STRING'];
      $que[$que_id][] = $que_item;
    }

    // now placing rest of time back to que element
    $que_types[$que_id]['time_left'] = $time_passed;
  }

  if($query)
  {
    $query .= "`que` = '{$query_string}'";
  }

  return array(
    'que' => $que,
    'built' => $built,
    'query' => $query
  );
}

?>
