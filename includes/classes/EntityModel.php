<?php

/**
 * Class EntityModel
 *
 * @property int|float $dbId Buddy record DB ID
 */
class EntityModel implements \Common\IEntity {
  /**
   * Name of exception class that would be thrown
   *
   * Uses for calling when you don't know which exact exception should be called
   * On EntityModel's children should be used exception class name
   *
   * @var string $exceptionClass
   */
  protected static $exceptionClass = 'EntityException';

  /**
   * EntityModel constructor.
   *
   * @param \Common\GlobalContainer $gc
   */
  public function __construct($gc) {

  }

}
