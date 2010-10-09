<?php
function INT_myPrettyNumber($number, $limit = 1000){
  if($number < 0)
  {
    $negative = '-';
    $number = -$number;
  }

  while($number > $limit)
  {
    $suffix .= 'k';
    $number = round($number / 1000);
  }
/*
  if($number > $limit){
    $number = round($number / 1000);
    $suffix = 'k';
    if($number > $limit * 1000){
      $number = round($number / 1000);
      $suffix = 'm';
    }
  }
*/
  return $negative . pretty_number($number) . $suffix;
}
?>