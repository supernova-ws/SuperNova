<?php
/** Created by Gorlum 26.01.2026 02:24 */

namespace Refactor\ContextPipeline;

abstract class AbstractPipeline {
  /** @var AbstractPipeline|null $instance */
  protected static ?AbstractPipeline $instance = null;

  /** @var AbstractPipelineStep|null $baseStep Base value provider that should be ABSOLUTELY launched first */
  protected ?AbstractPipelineStep $baseStep = null;

  /** @var AbstractPipelineStep[] $pipeline List of steps that can be executed independently of each other but after base one */
  protected array $pipeline = [];

  /**
   * @return static
   */
  public static function singleton(): AbstractPipeline {
    return static::$instance ?: (static::$instance = new static());
  }

  /**
   * @param AbstractPipelineStep $step
   * @param bool                 $overwrite
   *
   * @return $this
   */
  public function registerBase(AbstractPipelineStep $step, bool $overwrite = false): AbstractPipeline {
    if ($overwrite || empty($this->baseStep)) {
      $this->baseStep = $step;
    }

    return $this;
  }

  /**
   * @param AbstractPipelineStep $step
   *
   * @return $this
   */
  public function registerModifier(AbstractPipelineStep $step): AbstractPipeline {
    $this->pipeline[] = $step;

    return $this;
  }

  public function __call($name, $arguments) {
    if (!empty($this->baseStep) && method_exists($this->baseStep, $name)) {
      $this->baseStep->$name(...$arguments);
    }

    foreach ($this->pipeline as $step) {
      if (method_exists($step, $name)) {
        $step->$name(...$arguments);
      }
    }

    // Context always should bew first argument
    $context = reset($arguments);

    return $context->getResult();
  }

}
