<?php
/**
 * Created by Gorlum 14.08.2019 22:41
 */

namespace Pages;

use SN;

class PageAjaxIgnore extends PageAjax {
  /**
   * List of allowed actions for this page
   *
   * @var array[] $allowedActions [(string)action] => true
   */
  protected $allowedActions = [
    'ignorePlayer'   => true,
    'unIgnorePlayer' => true,
  ];

  protected $subjectPlayerId = 0;

  /**
   * Loading page data from page params
   */
  public function loadParams() {
    $this->subjectPlayerId = sys_get_param_id('subjectId', 0);
  }

  // Page Actions ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  public function ignorePlayer() {
    global $user;

    SN::$gc->ignores->ignore($user['id'], $this->subjectPlayerId);

    return [
      'player' => [
        'id' => $user['id'],
        'ignores' => [
          $this->subjectPlayerId => true,
        ],
      ],
    ];
  }

  public function unIgnorePlayer() {
    global $user;

    SN::$gc->ignores->unIgnore($user['id'], $this->subjectPlayerId);

    return [
      'player' => [
        'id' => $user['id'],
        'ignores' => [
          $this->subjectPlayerId => false,
        ],
      ],
    ];
  }

}
