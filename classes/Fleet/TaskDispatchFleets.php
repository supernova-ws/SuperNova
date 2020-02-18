<?php
/**
 * Created by Gorlum 08.02.2020 18:54
 */

namespace Fleet;

use Core\GlobalContainer;
use Core\HttpUrl;
use SN;
use classConfig;
use Core\Scheduler\TaskPeriodic;
use Core\Scheduler\Lock;

class TaskDispatchFleets extends TaskPeriodic {
  /**
   * Name of config field to monitor
   *
   * @var string $configName
   */
  protected $configName = 'fleet_update_last';

  /**
   * @param GlobalContainer $gc
   *
   * @return Lock
   */
  public static function getLock($gc) {
    return new Lock($gc, 'fleet_update_lock', PERIOD_MINUTE, 10, classConfig::DATE_TYPE_UNIX);
//    return new Lock($gc, 'fleet_update_lock', 10, 0, classConfig::DATE_TYPE_UNIX);
  }

  /**
   * TaskDispatchFleets constructor.
   *
   * @param GlobalContainer|null $gc
   */
  public function __construct($gc = null) {
    parent::__construct($gc);

    $this->interval = $this->config->fleet_update_interval;
    $this->lock     = $this::getLock($this->gc);
  }

  protected function isTaskAllowed() {
    return !SN::$options[PAGE_OPTION_FLEET_UPDATE_SKIP] && !SN::gameIsDisabled() && parent::isTaskAllowed();
  }

  /**
   * This function called if task have a lock and lock grace period expired
   * Here can be added some checks and even recovery procedures for expired lock
   *
   * @return bool True if task can proceed even on lock expiration time
   */
  protected function proceedLockExpiration() {
    $this->gc->debug->warning('Fleet dispatcher was locked too long - unlocked by watchdog', 'FFH Error', 504);

    return Lock::LOCK_EXPIRED_IGNORE;
  }


  protected function task() {
    $url = HttpUrl::spawn($this->gc)
      ->parseUrl(SN_ROOT_VIRTUAL)
      ->addPath('index.php')
      ->addParams(['page' => 'worker', 'mode' => 'dispatchFleets',]);

    sn_get_url_contents($url->urlSigned());

//    invokeUrl($url);

    return true;
  }

}
