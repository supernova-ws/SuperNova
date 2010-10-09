<?php

function sys_combatDataPack($combat, $strArray)
{
  global $reslist;

  foreach($combat as $fleetID => $fleetCompress)
  {
    $strPackedEnd = '';
    if($strArray == 'def')
      $strPacked .= 'D';
    else
      $strPacked .= 'A';

    $strPacked .= '.';

    foreach($fleetCompress['user'] as $key => $techLevel)
      $strPacked .= $key  . ',' . (empty($techLevel) ? 0 : $techLevel) . ';';

    $strPacked .= '.';

    foreach($fleetCompress[$strArray] as $shipID => $shipCount)
      $strPacked .= $shipCount ? ($shipID . ',' . $shipCount . ';') : '';
  }
  $strPacked .= '.';

  foreach($reslist['resources'] as $resource)
  {
     $strPacked .= intval($combat[0]['resources'][$resource]) . ',';
  }
  $strPacked .= '!';

  return $strPacked;
}

function sys_combatDataUnPack($strData)
{
  global $reslist;

  $unpacked = array (
    'detail' => array(),
    'def' => array()
  );

  $fleetList = explode('!', $strData);

  foreach($fleetList as $fleet)
  {
    $t = explode('.', $fleet);

    if(!$t[0]) continue;

    if($t[0] == 'A' ){
      $strArray = 'detail';
    }else{
      $strArray = 'def';
    };

    $combat = array();

    $t[1] = explode(';', $t[1]);
    foreach($t[1] as $techInfo)
    {
      if($techInfo)
      {
        $techInfo = explode(',', $techInfo);
        $combat['user'][$techInfo[0]] = $techInfo[1];
      }
    }

    $t[2] = explode(';', $t[2]);
    foreach($t[2] as $shipInfo)
    {
      if($shipInfo)
      {
        $shipInfo = explode(',', $shipInfo);
        $combat[$strArray][$shipInfo[0]] = $shipInfo[1];
      }
    }

    $t[3] = explode(',', $t[3]);
    foreach($t[3] as $resourceID => $resource)
    {
      if($resource)
      {
        $combat['resources'][$reslist['resources'][$resourceID]] = intval($resource);
      }
    }

    $unpacked[$strArray][] = $combat;
  }

  return $unpacked;
}

function coe_simulatorHTMLMake($resToLook)
{
  global $lang, $resource, $user, $unpacked;

  foreach($resToLook as $unitID)
  {
    if($unitID<200 || $unitID>600 )
    {
      $parse['fieldNameAtt']  = 'user';
      $parse['fieldNameDef']  = 'user';
    }
    else
    {
      $parse['fieldNameAtt']  = 'detail';
      $parse['fieldNameDef']  = 'def';
    }
    $parse['fieldValueAtt'] = intval($unpacked['detail'][0][$parse['fieldNameAtt']][$unitID]);
    $parse['fieldValueAtt'] = intval($parse['fieldValueAtt'] ? $parse['fieldValueAtt'] : $user[$resource[$unitID]]);

    $parse['fieldValueDef'] = intval($unpacked['def'][0][$parse['fieldNameDef']][$unitID]);
    $parse['unitID'] = $unitID;
    $parse['unitName'] = $lang['tech'][$unitID];
    $parse['hideAttacker'] = $unitID < 400 ? '' : 'class="hide"';

    $tmp = parsetemplate(gettemplate('simulator_row'), $parse);
    $input[floor($unitID/100) * 100] .= $tmp;
  }

  return $input;
}

?>