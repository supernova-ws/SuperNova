<?php
/**
 * Created by Gorlum 04.10.2017 6:56
 */

namespace Unit;

use DBAL\ActiveRecord;

/**
 * Class RecordUnit
 * @package Unit
 *
 * @property int|string $unit_player_id     - bigint   -
 * @property int        $unit_location_type - tinyint  -
 * @property int|string $unit_location_id   - bigint   -
 * @property int        $unit_type          - bigint   -
 * @property int        $unit_snid          - bigint   -
 * @property int|string $unit_level         - decimal  -
 * @property string     $unit_time_start    - datetime -
 * @property string     $unit_time_finish   - datetime -
 *
 */
class RecordUnit extends ActiveRecord {
  protected static $_primaryIndexField = 'unit_id';

}
