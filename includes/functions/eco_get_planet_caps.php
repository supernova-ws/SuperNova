<?php

function ECO_getPlanetCaps($user, &$planet_row)
{
  global $sn_data, $resource, $config;

  $sn_groups = $sn_data['groups'];
  $sn_group_structures = $sn_groups['structures'];

  $storage_overflowed_size = BASE_STORAGE_SIZE * MAX_OVERFLOW;
  $Caps = array( 'planet' => array(
    'metal'         => $planet_row['metal'],
    'crystal'       => $planet_row['crystal'],
    'deuterium'     => $planet_row['deuterium'],
    'metal_max'     => floor(mrc_modify_value($user, $planet_row, MRC_STOCKMAN, $storage_overflowed_size * pow (1.5, $planet_row[$resource[22]]))),
    'crystal_max'   => floor(mrc_modify_value($user, $planet_row, MRC_STOCKMAN, $storage_overflowed_size * pow (1.5, $planet_row[$resource[23]]))),
    'deuterium_max' => floor(mrc_modify_value($user, $planet_row, MRC_STOCKMAN, $storage_overflowed_size * pow (1.5, $planet_row[$resource[24]]))),
  ));

  if ($planet_row['planet_type'] == 3)
  {
    return $Caps;
  }

  $config_resource_multiplier = $config->resource_multiplier;

  // Calcul de production lin�aire des divers types
  $BuildTemp = $planet_row['temp_max'];
  $BuildEnergyTech = $user['energy_tech'];

  $Caps['metal_perhour'][0]     = $config->metal_basic_income     * $config_resource_multiplier;
  $Caps['crystal_perhour'][0]   = $config->crystal_basic_income   * $config_resource_multiplier;
  $Caps['deuterium_perhour'][0] = $config->deuterium_basic_income * $config_resource_multiplier;
  $Caps['energy'][0]            = $config->energy_basic_income;
  $Caps['planet']['energy_max'] = $Caps['energy'][0];

  foreach($sn_groups['prod'] as $ProdID)
  {
    $unit_data = $sn_data[$ProdID];

    $BuildLevel       = $planet_row[ $resource[$ProdID] ];
    $BuildLevelFactor = $planet_row[ "{$resource[$ProdID]}_porcent" ];

    $Caps['energy'][$ProdID] = floor(eval($unit_data['energy_perhour']));
    if ($ProdID == 12)
    {
      if ($planet_row['deuterium'] > 0)
      {
        $Caps['deuterium_perhour'][$ProdID] = floor( eval ( $unit_data['deuterium_perhour'] ));
      }
      else
      {
        $Caps['energy'][$ProdID] = 0;
      }
    }
    else
    {
      if (in_array($ProdID, $sn_group_structures))
      {
        $Caps['metal_perhour'][$ProdID]     += floor(mrc_modify_value($user, $planet_row, MRC_GEOLOGIST, eval($unit_data['metal_perhour']) * $config_resource_multiplier));
        $Caps['crystal_perhour'][$ProdID]   += floor(mrc_modify_value($user, $planet_row, MRC_GEOLOGIST, eval($unit_data['crystal_perhour']) * $config_resource_multiplier));
        $Caps['deuterium_perhour'][$ProdID] += floor(mrc_modify_value($user, $planet_row, MRC_GEOLOGIST, eval($unit_data['deuterium_perhour']) * $config_resource_multiplier));
      }
    };

    if ($Caps['energy'][$ProdID]>0)
    {
      $Caps['energy'][$ProdID] = floor(mrc_modify_value($user, $planet_row, array(MRC_POWERMAN), $Caps['energy'][$ProdID]));

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
    $Caps['production'] = max(0, min(1, $Caps['planet']['energy_max'] / $Caps['planet']['energy_used']));
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
