<?php

/**
 * Created by Gorlum 08.01.2018 14:24
 */

namespace Planet;

use DBAL\ActiveRecord;

/**
 * Class RecordPlanet
 * @package Planet
 *
 * @property string    $name
 * @property int|float $id_owner
 * @property int       $galaxy
 * @property int       $system
 * @property int       $planet
 * @property int       $planet_type
 * @property int|float $metal
 * @property int|float $crystal
 * @property int|float $deuterium
 * @property int|float $energy_max
 * @property int|float $energy_used
 * @property int       $last_jump_time
 * @property int       $metal_perhour
 * @property int       $crystal_perhour
 * @property int       $deuterium_perhour
 * @property int       $metal_mine_porcent
 * @property int       $crystal_mine_porcent
 * @property int       $deuterium_sintetizer_porcent
 * @property int       $solar_plant_porcent
 * @property int       $fusion_plant_porcent
 * @property int       $solar_satelit_porcent
 * @property int       $last_update
 * @property int       $que_processed
 * @property string    $image
 * @property int|float $points
 * @property int|float $ranks
 * @property int       $id_level
 * @property int       $destruyed
 * @property int       $diameter
 * @property int       $field_max
 * @property int       $field_current
 * @property int       $temp_min
 * @property int       $temp_max
 * @property int|float $metal_max
 * @property int|float $crystal_max
 * @property int|float $deuterium_max
 * @property int|float $parent_planet
 * @property int|float $debris_metal
 * @property int|float $debris_crystal
 * @property int       $PLANET_GOVERNOR_ID
 * @property int       $PLANET_GOVERNOR_LEVEL
 * @property int       $planet_teleport_next
 * @property int       $ship_sattelite_sloth_porcent
 * @property int       $density
 * @property int       $density_index
 * @property int       $position_original
 * @property int       $field_max_original
 * @property int       $temp_min_original
 * @property int       $temp_max_original
 *
 */
class RecordPlanet extends ActiveRecord {
  protected static $_tableName = 'planets';

}
