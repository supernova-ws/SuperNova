<?php

  /**
   * This file is under the GPL liscence, which must be included with the file under distrobution (license.txt)
   * this file was made by Xnova, edited to support Toms combat engine by Anthony (MadnessReD) [http://madnessred.co.cc/]
   * Do not edit this comment block
   */

  /*
   * BE_CalculateMoon.php
   * Battle Engine File
   * Calculate moon creation chance with simulation support
   */

  /*
  *
  * Partial copyright (c) 2010 by Gorlum for oGame.triolan.com.ua
  */

function BE_calculateMoonChance($result){
  $FleetDebris = $result['debree']['att'][0] + $result['debree']['def'][0] + $result['debree']['att'][1] + $result['debree']['def'][1];

  $MoonChance = $FleetDebris / 1000000;
  return ($MoonChance<1) ? 0 : ($MoonChance>30 ? 30 : $MoonChance);
}
?>