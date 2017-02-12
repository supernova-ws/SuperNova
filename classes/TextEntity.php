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
//    $this->model = classSupernova::$gc->TextModel;
    $this->model = new TextModel();
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
