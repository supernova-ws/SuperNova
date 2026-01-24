<?php

/** Created by Gorlum 24.01.2026 16:31 */

namespace Refactor\ContextPipeline;

class Pipeline {
  /** @var AbstractPipelineStep|null $baseStep Base value provider that should be launched first */
  protected ?AbstractPipelineStep $baseStep = null;

  /** @var AbstractPipelineStep[] $pipeline List of steps that can be executed independently of each other but after base one */
  protected array $pipeline = [];

  /**
   * @param AbstractPipelineStep $step
   * @param bool                 $overwrite
   *
   * @return $this
   */
  public function registerBase(AbstractPipelineStep $step, bool $overwrite = false): Pipeline {
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
  public function registerModifier(AbstractPipelineStep $step): Pipeline {
    $this->pipeline[] = $step;

    return $this;
  }

  public function calculate(AbstractContext $context): AbstractContext {
    if (!empty($this->baseStep)) {
      $this->baseStep->apply($context);
    }

    foreach ($this->pipeline as $step) {
      $step->apply($context);
    }

    return $context;
  }

}
