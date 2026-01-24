<?php

/** Created by Gorlum 24.01.2026 17:26 */

namespace Refactor\ContextPipeline;

abstract class AbstractContext {
  /** @noinspection PhpMissingFieldTypeInspection */
  public $result;

  public function getResult() {
    return $this->result;
  }

}
