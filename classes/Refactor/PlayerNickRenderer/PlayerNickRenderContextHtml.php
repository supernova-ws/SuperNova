<?php

/** Created by Gorlum 26.01.2026 04:33 */

namespace Refactor\PlayerNickRenderer;

use Refactor\ContextPipeline\AbstractContext;

class PlayerNickRenderContextHtml extends AbstractContext {
  public array $nick_array = [];

  /**
   * @param $nick_array
   */
  public function __construct($nick_array) {
    $this->nick_array = $nick_array;
  }

}
