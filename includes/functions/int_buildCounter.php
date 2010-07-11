<?php
function int_buildCounter($planetrow, $type, $subType = ''){
  global $lang, $user, $time_now;

  if ( $planetrow["b_{$type}_id"] ) {
    $BuildQueue = explode (';', $planetrow["b_{$type}_id"]);

    $start_prod = $time_now;
    if($type=='hangar'){
      $start_prod = $time_now - $planetrow["b_{$type}"];
    }

    $Build = "<script type='text/javascript'>sn_timers.unshift(['ov_{$type}{$subType}', 0, true, {$start_prod}, ['{$lang['Free']}',[";
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
    $Build.= "]]]);</script>";
  }

  return $Build;
}
?>