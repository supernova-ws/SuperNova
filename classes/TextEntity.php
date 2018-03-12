<?php

/**
 * Created by Gorlum 07.02.2017 10:20
 */

/**
 * Class TextEntity
 *
 * Represents text in DB
 *
 * @property int    $id
 * @property int    $parent
 * @property int    $context
 * @property int    $prev
 * @property int    $next
 * @property int    $next_alt
 * @property string $title
 * @property string $content
 *
 */
class TextEntity extends \Common\ContainerPlus {
  const _class = 'TextEntity';

  /**
   * @var TextModel $model
   */
  protected $model;

  /**
   * @inheritdoc
   */
  public function __construct(array $values = array()) {
    parent::__construct($values);

    // TODO
    $this->model = new TextModel(SN::$gc);
  }

  /**
   * @return array
   */
  public function toArray() {
    $tutorial = array();
    foreach($this->keys() as $key) {
      $tutorial[$key] = $this->$key;
    }

    return $tutorial;
  }

  /**
   * Return HTML-ized array of elements (if any)
   *
   * @param int $encodeOptions - HTML_ENCODE_xxx constants
   *
   * @return array
   */
  public function toArrayHtml($encodeOptions = HTML_ENCODE_MULTILINE) {
    $result = $this->toArray();

    if (!empty($result)) {
      $result['title'] = HelperString::htmlEncode($result['title'], $encodeOptions);
      $result['content'] = HelperString::htmlEncode($result['content'], $encodeOptions);
    }

    return $result;
  }

  /**
   * Return HTML-ized array of elements (if any)
   *
   * @param int $encodeOptions - HTML_ENCODE_xxx constants
   *
   * @return array
   */
  public function toArrayParsedBBC($encodeOptions = HTML_ENCODE_MULTILINE) {
    $result = $this->toArray();

    if (!empty($result)) {
      $result['title'] = SN::$gc->bbCodeParser->expandBbCode($result['title'], AUTH_LEVEL_SYSTEM, $encodeOptions);
      $result['content'] = SN::$gc->bbCodeParser->expandBbCode($result['content'], AUTH_LEVEL_SYSTEM, $encodeOptions);
    }

    return $result;
  }

//  public function get($textId) {
//    $this->clear();
//    $this->id = $textId;
//
//    return $this->repository->get($this);
//  }
//
//  public function next($textId) {
//    $current = new static();
//    $static->get($textId);
//
//    if ($static->next) {
//      $next = new static();
//      $next->get($static->next);
//      if ($next->isEmpty() && $current->alternative) {
//
//      }
//    }
//
//
//  }
//
//

}
