<?php

function ECO_getPlanetCaps($CurrentUser, &$CurrentPlanet)
{
  global $sn_data, $resource, $reslist, $config;

  $Caps = array( 'planet' => array(
    'metal' => $CurrentPlanet['metal'],
    'crystal' => $CurrentPlanet['crystal'],
    'deuterium' => $CurrentPlanet['deuterium']
  ));

  // Mise a jour de l'espace de stockage
  $Caps['planet']['metal_max']     = floor (BASE_STORAGE_SIZE * pow (1.5, $CurrentPlanet[ $resource[22] ] ) * (1 + $CurrentUser['rpg_stockeur'] * 0.5) * MAX_OVERFLOW);
  $Caps['planet']['crystal_max']   = floor (BASE_STORAGE_SIZE * pow (1.5, $CurrentPlanet[ $resource[23] ] ) * (1 + $CurrentUser['rpg_stockeur'] * 0.5) * MAX_OVERFLOW);
  $Caps['planet']['deuterium_max'] = floor (BASE_STORAGE_SIZE * pow (1.5, $CurrentPlanet[ $resource[24] ] ) * (1 + $CurrentUser['rpg_stockeur'] * 0.5) * MAX_OVERFLOW);

  if ($CurrentPlanet['planet_type'] == 3)
  {
    return $Caps;
  }

  // Calcul de production linéaire des divers types
  $BuildTemp = $CurrentPlanet['temp_max'];

  $Caps['energy'][0]            = $config->energy_basic_income    *  $config->resource_multiplier;
  $Caps['metal_perhour'][0]     = $config->metal_basic_income     *  $config->resource_multiplier;
  $Caps['crystal_perhour'][0]   = $config->crystal_basic_income   *  $config->resource_multiplier;
  $Caps['deuterium_perhour'][0] = $config->deuterium_basic_income *  $config->resource_multiplier;
  $Caps['planet']['energy_max'] = $Caps['energy']['0'];

  foreach($reslist['prod'] as $ProdID)
  {
    $BuildLevel       = $CurrentPlanet[ $resource[$ProdID] ];
    $BuildLevelFactor = $CurrentPlanet[ $resource[$ProdID]."_porcent" ];

    $Caps['energy'][$ProdID] = floor( eval ( $sn_data[$ProdID]['energy_perhour'] ) );
    if ($ProdID == 12)
    {
      if ($CurrentPlanet['deuterium'] > 0)
      {
        $Caps['deuterium_perhour'][$ProdID] = floor( eval ( $sn_data[$ProdID]['deuterium_perhour'] ));
      }
      else
      {
        $Caps['energy'][$ProdID] = 0;
      };
    }
    else
    {
      $rpgGeologue = 1;
      if (in_array( $ProdID, $reslist['build']))
      {
        $rpgGeologue += $CurrentUser['rpg_geologue'] * 0.05;
      };
      $Caps['metal_perhour'][$ProdID]     +=  floor( eval ( $sn_data[$ProdID]['metal_perhour']     ) * $rpgGeologue *  $config->resource_multiplier);
      $Caps['crystal_perhour'][$ProdID]   +=  floor( eval ( $sn_data[$ProdID]['crystal_perhour']   ) * $rpgGeologue *  $config->resource_multiplier);
      $Caps['deuterium_perhour'][$ProdID] +=  floor( eval ( $sn_data[$ProdID]['deuterium_perhour'] ) * $rpgGeologue *  $config->resource_multiplier);
    };

    if ($Caps['energy'][$ProdID]>0)
    {
      $Caps['energy'][$ProdID] = floor ( $Caps['energy'][$ProdID] * ( 1 + $CurrentUser['rpg_ingenieur'] * 0.05 ) * ( 1 + $CurrentUser['energy_tech'] * 0.1 ) * $config->resource_multiplier);
      $Caps['planet']['energy_max'] += $Caps['energy'][$ProdID];
    }
    else
    {
      $Caps['planet']['energy_used'] -= $Caps['energy'][$ProdID];
    };

    $Caps['planet']['metal_perhour']     += $Caps['metal_perhour'][$ProdID];
    $Caps['planet']['crystal_perhour']   += $Caps['crystal_perhour'][$ProdID];
    $Caps['planet']['deuterium_perhour'] += $Caps['deuterium_perhour'][$ProdID];
  };

  if ($Caps['planet']['energy_used'])
  {
    $Caps['production'] = min(1, $Caps['planet']['energy_max'] / $Caps['planet']['energy_used']);
  }
  else
  {
    $Caps['production'] = 1;
  }

  $Caps['real']['metal_perhour']     = floor($Caps['planet']['metal_perhour']     * $Caps['production'] + $Caps['metal_perhour'][0]);
  $Caps['real']['crystal_perhour']   = floor($Caps['planet']['crystal_perhour']   * $Caps['production'] + $Caps['crystal_perhour'][0]);
  $Caps['real']['deuterium_perhour'] = floor($Caps['planet']['deuterium_perhour'] * $Caps['production'] + $Caps['deuterium_perhour'][0]);

  foreach($Caps['energy'] as $element => $production)
  {
    $Caps['real']['units']['metal_perhour'][$element]     = floor($Caps['metal_perhour'][$element] * $Caps['production']);
    $Caps['real']['units']['crystal_perhour'][$element]   = floor($Caps['crystal_perhour'][$element] * $Caps['production']);
    $Caps['real']['units']['deuterium_perhour'][$element] = floor($Caps['deuterium_perhour'][$element] * $Caps['production']);
  }

  return $Caps;
}

?>