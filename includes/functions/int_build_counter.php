<?php

function int_buildCounter($planetrow, $type, $subType = '', $que = false)
{
  global $lang, $user, $time_now;

  if ( $planetrow["b_{$type}_id"] )
  {
    $BuildQueue = explode (';', $planetrow["b_{$type}_id"]);

    $start_prod = $time_now;
    if($type=='hangar'){
      $start_prod = $time_now - $planetrow["b_{$type}"];
    }

    $Build = "<script type='text/javascript'>sn_timers.unshift({id: 'ov_{$type}{$subType}', type: 0, active: true, start_time: {$start_prod}, options: { msg_done: '{$lang['Free']}', que: [";
    foreach($BuildQueue as $queItem){
      $CurrBuild  = explode (',', $queItem);
      if($type=='hangar'){
        $RestTime   = GetBuildingTime( $user, $planetrow, $CurrBuild[0] );
        $buildCount = $CurrBuild[1];
      }else{
        $RestTime   = $planetrow["b_{$type}"] - time();
        $buildCount = 1;
      }
      if($type=='building')
        $b1 .= ' (' . ($CurrBuild[1]) .')';

      $Build.= "['{$CurrBuild[0]}', '{$lang['tech'][$CurrBuild[0]]}{$b1}', {$RestTime}, '{$buildCount}'],";
    }
    $Build.= "]}});</script>";
  }
  elseif ($que)
  {
    $que_item = $que['que'][QUE_STRUCTURES][0];
    if(!empty($que_item))
    {
      $start_prod = $time_now - $que_item['TIME'];

      $Build = "<script type='text/javascript'>sn_timers.unshift({id: 'ov_{$type}{$subType}', type: 0, active: true, start_time: {$time_now}, options: { msg_done: '{$lang['Free']}', que: [";
      $RestTime   = $que_item['TIME'];
      $buildCount = $que_item['AMOUNT'];

      $Build.= "['{$que_item['ID']}', '{$lang['tech'][$que_item['ID']]} ({$que_item['LEVEL']})', {$RestTime}, '{$buildCount}'],";
      $Build.= "]}});</script>";
    }
  }

  return $Build;
}

?>
