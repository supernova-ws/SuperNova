<?php
/**
 * Created by Gorlum 12.02.2017 8:51
 */

namespace Pages;

use \classSupernova;
use \userOptions;

class PageTutorial extends PageAjax {

  protected $allowedActions = array(
    'ajax' => true,
    'ajaxNext' => true,
    'ajaxPrev' => true,
    'ajaxFinish' => true,
  );

  protected $id = 1;

  /**
   * @var userOptions $userOptions
   */
  protected $userOptions;

  /**
   * @inheritdoc
   */
  public function loadParams() {
    $this->id = sys_get_param_id('id', 1);
  }

  protected function ajaxRender($method = 'getById') {
    /**
     * @var \TextEntity $text
     */
    $text = classSupernova::$gc->textModel->$method($this->id);
    $result = $text->toArrayParsedBBC(HTML_ENCODE_MULTILINE_JS);
    if (!$text->isEmpty()) {
      classSupernova::$user_options[PLAYER_OPTION_TUTORIAL_CURRENT] = $text->id;
    }

    return array(
      'tutorial' => $result,
    );
  }

  /**
   * @return array
   */
  protected function block2ValueList() {
    $result = array();

    $text = classSupernova::$gc->textModel->getById($this->id);
    $array = $text->toArrayParsedBBC(HTML_ENCODE_MULTILINE_JS);
    foreach ($array as $key => $value) {
      $result[] = array(
        'KEY'   => $key,
        'VALUE' => $value,
      );
    }

    return $result;
  }

  /**
   * @return bool
   */
  protected function isBlockEnabled() {
    if ($this->userOptions[PLAYER_OPTION_TUTORIAL_DISABLED]) {
      return false;
    }

    if (classSupernova::$gc->textModel->getById($this->userOptions[PLAYER_OPTION_TUTORIAL_CURRENT])->isEmpty()) {
      return false;
    }

    // Checking if there is new tutorial appears after user finished old one
    if ($this->userOptions[PLAYER_OPTION_TUTORIAL_FINISHED]) {
      $next = classSupernova::$gc->textModel->next($this->userOptions[PLAYER_OPTION_TUTORIAL_CURRENT]);
      if (!$next->isEmpty()) {
        $this->userOptions[PLAYER_OPTION_TUTORIAL_FINISHED] = 0;
      } else {
        return false;
      }
    }

    return true;
  }

  // Page Actions ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  /**
   * Action 'ajax'
   *
   * @return array
   */
  public function ajax() {
    return $this->ajaxRender('getById');
  }

  /**
   * Returns ajax-ready next element in chain
   *
   * @return array
   */
  public function ajaxNext() {
    return $this->ajaxRender('next');
  }

  /**
   * Returns ajax-ready prev element in chain
   *
   * @return array
   */
  public function ajaxPrev() {
    return $this->ajaxRender('prev');
  }

  /**
   * Finishes current tutorial
   */
  public function ajaxFinish() {
    classSupernova::$user_options[PLAYER_OPTION_TUTORIAL_FINISHED] = 1;
  }

  // Renderers +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  /**
   * @param \template $template
   *
   * @return bool - should tutorial block be included
   */
  public static function renderNavBar($template) {
    $block = new self();

    if ($result = $block
      ->setUserOptions(classSupernova::$user_options)
      ->setId(classSupernova::$user_options[PLAYER_OPTION_TUTORIAL_CURRENT])
      ->isBlockEnabled()
    ) {
      $template->assign_recursive(array(
        '.' => array(
          'tutorial' => $block->block2ValueList())
      ));
    }

//    $template->assign_var('PLAYER_OPTION_TUTORIAL_WINDOWED', classSupernova::$user_options[PLAYER_OPTION_TUTORIAL_WINDOWED]);

    return $result;
  }

  // Getters/Setters +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  /**
   * @param int|string $id
   *
   * @return $this
   */
  public function setId($id) {
    $this->id = $id;

    return $this;
  }

  /**
   * @param userOptions $userOptions
   *
   * @return $this
   */
  public function setUserOptions($userOptions) {
    $this->userOptions = $userOptions;

    return $this;
  }

}
