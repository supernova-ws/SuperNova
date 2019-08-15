<?php
/**
 * Created by Gorlum 12.02.2017 8:51
 */

namespace Pages;

use \SN;

class PageQuest extends PageAjax {
  /**
   * List of allowed actions for this page
   *
   * @var array[] $allowedActions [(string)action] => true
   */
  protected $allowedActions = [
    'saveFilter' => true,
  ];

  protected $filterQuestStatus = QUEST_STATUS_ALL;

  /**
   * Loading page data from page params
   */
  public function loadParams() {
    $this->filterQuestStatus = sys_get_param_int('filterQuestStatus', QUEST_STATUS_ALL);
    $this->checkParams();
  }

  protected function checkParams() {
    static $statuses = array(
      QUEST_STATUS_ALL => '',
      QUEST_STATUS_EXCEPT_COMPLETE => '',
      QUEST_STATUS_NOT_STARTED => '',
      QUEST_STATUS_STARTED => '',
      QUEST_STATUS_COMPLETE => '',
    );

    if(!isset($statuses[$this->filterQuestStatus])) {
      $this->filterQuestStatus = QUEST_STATUS_ALL;
    }
  }

  // Page Actions ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  /**
   * Finishes current tutorial
   */
  public function saveFilter() {
    SN::$user_options[PLAYER_OPTION_QUEST_LIST_FILTER] = $this->filterQuestStatus;
    return array(
      'quest' => array(
        'filterQuestStatus' => $this->filterQuestStatus,
      ),
    );
  }

}
