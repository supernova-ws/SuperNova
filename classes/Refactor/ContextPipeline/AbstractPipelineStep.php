<?php

/** Created by Gorlum 24.01.2026 17:30 */

namespace Refactor\ContextPipeline;

abstract class AbstractPipelineStep {

  abstract public function apply(AbstractContext $context);
  // TODO abstract public function applyCompat(...$args);

}
