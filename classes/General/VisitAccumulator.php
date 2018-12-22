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
  public $playerEntryId = 0;
  public $time = 0;
  public $length = 0;
  public $hits = 1;
//  public $deviceId = 0;
//  public $browserId = 0;
//  public $ip = 0;
//  public $proxies = 0;

  public $defaultLength = 0;
  public $defaultHits = 1;

  /**
   * @param $row
   *
   * @return self
   */
  public static function build($row) {
    $me                = new self();

    $me->counterId     = $row['counter_id'];
    $me->userId        = $row['user_id'];
    $me->playerEntryId = $row['player_entry_id'];
    $me->time          = strtotime($row['visit_time']);
    $me->length        = $row['visit_length'];
    $me->hits          = $row['hits'];
//    $me->deviceId = $row['device_id'];
//    $me->browserId = $row['browser_id'];
//    $me->ip = $row['user_ip'];
//    $me->proxies = $row['user_proxy'];

    $me->defaultLength = $me->length;
    $me->defaultHits   = $me->hits;

    return $me;
  }

  public function isChanged() {
    return $this->defaultLength != $this->length || $this->defaultHits != $this->hits;
  }

}
