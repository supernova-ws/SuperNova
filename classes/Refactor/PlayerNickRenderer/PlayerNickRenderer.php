<?php

/** Created by Gorlum 24.01.2026 15:43 */

namespace Refactor\PlayerNickRenderer;

use Refactor\ContextPipeline\Pipeline;

class PlayerNickRenderer {
  private static ?PlayerNickRenderer $instance = null;

  private ?Pipeline $pipeline = null;

  /**
   * @return static
   */
  public static function singleton(): PlayerNickRenderer {
    return static::$instance ?: (static::$instance = new static());
  }

  public function __construct() {
    $this->registerBase(new PlayerNickBase());
  }


  public function registerBase($step): PlayerNickRenderer {
    $this->getPipeline()->registerBase($step);

    return $this;
  }

  public function registerModifier($step): PlayerNickRenderer {
    $this->getPipeline()->registerModifier($step);

    return $this;
  }

  public function getPipeline(): Pipeline {
    return $this->pipeline ?: ($this->pipeline = new Pipeline());
  }

  /**
   * @param array      $user
   * @param array|bool $options
   *
   * @return array
   */
  public function render(array $user, $options): array {
    return $this->pipeline->calculate(new PlayerNickRenderContext($user, $options))->getResult();
  }

}
