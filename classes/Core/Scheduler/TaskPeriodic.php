<?php
/**
 * Created by Gorlum 08.02.2020 15:44
 */

namespace Core\Scheduler;

use classConfig;

/**
 * Periodic task - task which runs in time intervals, i.e. each 10 minutes
 *
 * @package Core
 */
class TaskPeriodic extends TaskConditional {
  /**
   * Interval to run task
   *
   * @var int $interval
   */
  protected $interval = 0;

  /**
   * @return bool
   */
  protected function isTaskAllowed() {
    return $this->interval > 0 && parent::isTaskAllowed();
  }

  /**
   * @return bool
   */
  public function condition() {
    $this->forceConfigLoad ? $this->config->pass() : false;
    $configValue = $this->config->dateRead($this->configName, classConfig::DATE_TYPE_UNIX);

    return ($this->interval > 0) && (SN_TIME_NOW - $configValue > $this->interval);
  }

}
