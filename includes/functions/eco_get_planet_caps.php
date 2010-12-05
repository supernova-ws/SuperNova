<?php

function ECO_getPlanetCaps($CurrentUser, &$CurrentPlanet)
{
  global $sn_data, $resource, $config;

  $sn_groups = $sn_data['groups'];
  $sn_group_structures = $sn_groups['structures'];

  $storage_overflowed_size = BASE_STORAGE_SIZE * MAX_OVERFLOW;
  $Caps = array( 'planet' => array(
    'metal'         => $CurrentPlanet['metal'],
    'crystal'       => $CurrentPlanet['crystal'],
    'deuterium'     => $CurrentPlanet['deuterium'],
    'metal_max'     => floor(mrc_modify_value($CurrentUser, $CurrentPlanet, MRC_STOCKMAN, $storage_overflowed_size * pow (1.5, $CurrentPlanet[$resource[22]]))),
    'crystal_max'   => floor(mrc_modify_value($CurrentUser, $CurrentPlanet, MRC_STOCKMAN, $storage_overflowed_size * pow (1.5, $CurrentPlanet[$resource[23]]))),
    'deuterium_max' => floor(mrc_modify_value($CurrentUser, $CurrentPlanet, MRC_STOCKMAN, $storage_overflowed_size * pow (1.5, $CurrentPlanet[$resource[24]]))),
  ));

  if ($CurrentPlanet['planet_type'] == 3)
  {
    return $Caps;
  }

  $config_resource_multiplier = $config->resource_multiplier;

  // Calcul de production linéaire des divers types
  $BuildTemp = $CurrentPlanet['temp_max'];

  $Caps['metal_perhour'][0]     = $config->metal_basic_income     * $config_resource_multiplier;
  $Caps['crystal_perhour'][0]   = $config->crystal_basic_income   * $config_resource_multiplier;
  $Caps['deuterium_perhour'][0] = $config->deuterium_basic_income * $config_resource_multiplier;
  $Caps['energy'][0]            = $config->energy_basic_income    * $config_resource_multiplier;
  $Caps['planet']['energy_max'] = $Caps['energy'][0];

  foreach($sn_groups['prod'] as $ProdID)
  {
    $unit_data = $sn_data[$ProdID];

    $BuildLevel       = $CurrentPlanet[ $resource[$ProdID] ];
    //$BuildLevelFactor = $CurrentPlanet[ $resource[$ProdID]."_porcent" ];
    $BuildLevelFactor = $CurrentPlanet[ "{$resource[$ProdID]}_porcent" ];

    $Caps['energy'][$ProdID] = eval($unit_data['energy_perhour']);
    if ($ProdID == 12)
    {
      if ($CurrentPlanet['deuterium'] > 0)
      {
        $Caps['deuterium_perhour'][$ProdID] = floor( eval ( $unit_data['deuterium_perhour'] ));
      }
      else
      {
        $Caps['energy'][$ProdID] = 0;
      };
    }
    else
    {
      if (in_array($ProdID, $sn_group_structures))
      {
        $Caps['metal_perhour'][$ProdID]     += floor(mrc_modify_value($CurrentUser, $CurrentPlanet, MRC_GEOLOGIST, eval($unit_data['metal_perhour']) * $config_resource_multiplier));
        $Caps['crystal_perhour'][$ProdID]   += floor(mrc_modify_value($CurrentUser, $CurrentPlanet, MRC_GEOLOGIST, eval($unit_data['crystal_perhour']) * $config_resource_multiplier));
        $Caps['deuterium_perhour'][$ProdID] += floor(mrc_modify_value($CurrentUser, $CurrentPlanet, MRC_GEOLOGIST, eval($unit_data['deuterium_perhour']) * $config_resource_multiplier));
      }
    };

    if ($Caps['energy'][$ProdID]>0)
    {
//      $Caps['energy'][$ProdID] = floor ( $Caps['energy'][$ProdID] * $config_resource_multiplier * ( 1 + $CurrentUser['rpg_ingenieur'] * 0.05 ) * ( 1 + $CurrentUser['energy_tech'] * 0.1 ));
      $Caps['energy'][$ProdID] = mrc_modify_value($CurrentUser, $CurrentPlanet, array(TECH_ENERGY, MRC_POWERMAN), $Caps['energy'][$ProdID] * $config_resource_multiplier);

      $Caps['planet']['energy_max'] += floor($Caps['energy'][$ProdID]);
    }
    else
    {
      $Caps['planet']['energy_used'] -= floor($Caps['energy'][$ProdID]);
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
