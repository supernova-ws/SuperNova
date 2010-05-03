<?php
function INT_myPrettyNumber($number, $limit = 1000){
  if($number > $limit){
    $number = $number / 1000;
    $suffix = 'k';
    if($number > $limit * 1000){
      $number = $number / 1000;
      $suffix = 'm';
    }
  }
  return pretty_number($number) . $suffix;
}
?>