<?php
/** Created by Gorlum 09.05.2025 22:39 */

namespace Fleet;

/**
 * Class FleetRowObject
 * @package Fleet
 *
 * @property int|string $id                       - bigint     -
 * property int|string $ownerId                  - bigint     - Fleet player owner ID
 * @property int        $fleet_mission            - int        -
 * @property int|string $fleet_amount             - bigint     -
 * @property string     $fleet_array              - mediumtext -
 * property int        $timeLaunch               - int        - Fleet launched from source planet (unix)
 * property int        $timeArrive               - int        - Time fleet arrive to destination (unix)
 * property int        $timeEndStay              - int        - Time when fleet operation on destination complete (if any) (unix)
 * property int        $timeReturn               - int        - Time fleet would return to source planet (unix)
 * @property int|string $fleet_start_planet_id    - bigint     -
 * @property int        $fleet_start_galaxy       - int        -
 * @property int        $fleet_start_system       - int        -
 * @property int        $fleet_start_planet       - int        -
 * @property int        $fleet_start_type         - int        -
 * @property int|string $fleet_end_planet_id      - bigint     -
 * @property int        $fleet_end_galaxy         - int        -
 * @property int        $fleet_end_system         - int        -
 * @property int        $fleet_end_planet         - int        -
 * @property int        $fleet_end_type           - int        -
 * @property int|string $fleet_resource_metal     - decimal    -
 * @property int|string $fleet_resource_crystal   - decimal    -
 * @property int|string $fleet_resource_deuterium - decimal    -
 * @property int|string $fleet_target_owner       - int        -
 * @property int|string $fleet_group              - varchar    -
 * property int        $status                   - int        - Current fleet status: flying to destination; returning
 *
 * Old fields for direct access
 * @property int        $fleet_id
 * @property int        $fleet_owner
 * @property int        $start_time               Time when fleet launched from source
 * @property int        $fleet_start_time         Time when fleet will arrive to destination point. Wrong name - should be `fleet_dst_arrive`
 * @property int        $fleet_end_stay           Time when fleet will end its mission on destination point. Should be `fleet_dst_stay_until`
 * @property int        $fleet_end_time           Time when fleet will return to source point. Should be `fleet_return_to_src`
 * @property int        $fleet_mess
 *
 */
class FleetRowObject {

}
