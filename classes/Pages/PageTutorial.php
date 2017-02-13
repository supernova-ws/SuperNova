<?php
/**
 * Created by Gorlum 12.02.2017 8:51
 */

namespace Pages;

use \classSupernova;
use \HelperString;
use \userOptions;

class PageTutorial {

  protected $id = 1;
  /**
   * @var userOptions $userOptions
   */
  protected $userOptions;

  /**
   * Loading page data from page params
   */
  public function loadParams() {
    $this->id = sys_get_param_id('id', 1);
  }

  protected function ajaxRender($method = 'getById') {
    /**
     * @var \TextEntity $text
     */
    $text = classSupernova::$gc->textModel->$method($this->id);
    if (!$text->isEmpty()) {
      classSupernova::$user_options[PLAYER_OPTION_TUTORIAL_CURRENT] = $text->id;
    }
    $result = $text->toArray();

    return array(
      'tutorial' => $result,
    );
  }

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

  public function ajaxPrev() {
    return $this->ajaxRender('prev');
  }

  public function ajaxFinish() {
    classSupernova::$user_options[PLAYER_OPTION_TUTORIAL_FINISHED] = 1;
  }

  public function htmlize($jsSafe = false) {
    $result = classSupernova::$gc->textModel->getById($this->id)->toArray();

    if (!empty($result)) {
      $result['title'] = HelperString::htmlEncode($result['title'], HTML_ENCODE_PREFORM | HTML_ENCODE_NL2BR | ($jsSafe ? HTML_ENCODE_JS_SAFE : 0));
      $result['content'] = HelperString::htmlEncode($result['content'], HTML_ENCODE_PREFORM | HTML_ENCODE_NL2BR | ($jsSafe ? HTML_ENCODE_JS_SAFE : 0));
    }

    return $result;
  }

  /**
   * @return array
   */
  public function renderBlock() {
    $result = array();
    foreach ($this->htmlize(true) as $key => $value) {
      $result[] = array(
        'KEY'   => $key,
        'VALUE' => $value,
      );
    }

    return $result;
  }

  public function renderNavBar() {
    if ($this->userOptions[PLAYER_OPTION_TUTORIAL_DISABLED]) {
      return false;
    }

    // Checking if there is new tutorial appears after user finished old one
    if ($this->userOptions[PLAYER_OPTION_TUTORIAL_FINISHED]) {
      $next = classSupernova::$gc->textModel->next($this->userOptions[PLAYER_OPTION_TUTORIAL_CURRENT]);
      if (!$next->isEmpty()) {
        $this->userOptions[PLAYER_OPTION_TUTORIAL_CURRENT] = $next->id;
        $this->userOptions[PLAYER_OPTION_TUTORIAL_FINISHED] = 0;
      } else {
        return false;
      }
    }

    return $this->renderBlock();
  }

  /**
   * @param \template $template
   *
   * @return bool - should tutorial block be included
   */
  public static function renderTemplate($template) {
    $block = new self();

    $htmlized = $block
      ->setId(classSupernova::$user_options[PLAYER_OPTION_TUTORIAL_CURRENT])
      ->setUserOptions(classSupernova::$user_options)
      ->renderNavBar();

    if (empty($htmlized)) {
      return false;
    }

    $template->assign_recursive(array('.' => array('tutorial' => $htmlized)));
    $template->assign_var('PLAYER_OPTION_TUTORIAL_WINDOWED', classSupernova::$user_options[PLAYER_OPTION_TUTORIAL_WINDOWED]);

    return true;
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
