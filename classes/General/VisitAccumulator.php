<?php
/**
 * Created by Gorlum 17.10.2017 7:28
 */

namespace General;

/**
 * Class VisitAccumulator
 *
 * Class used for `counter` table recalculation
 *
 * @package General
 */

class VisitAccumulator {
  public $counterId = 0;
  public $userId = 0;
  public $time = 0;
  public $length = 0;
  public $deviceId = 0;
  public $browserId = 0;
  public $ip = 0;
  public $proxies = 0;

  /**
   * @param $row
   *
   * @return self
   */
  public static function build($row) {
    $me = new self();
    $me->userId = $row['user_id'];
    $me->counterId = $row['counter_id'];
    $me->time = strtotime($row['visit_time']);
    $me->length = $row['visit_length'];
    $me->deviceId = $row['device_id'];
    $me->browserId = $row['browser_id'];
    $me->ip = $row['user_ip'];
    $me->proxies = $row['user_proxy'];

    return $me;
  }
}
