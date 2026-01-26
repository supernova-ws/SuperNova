<?php

/** Created by Gorlum 26.01.2026 03:20 */

namespace Refactor\PlayerNickRenderer;

use Refactor\ContextPipeline\AbstractPipelineStep;

abstract class AbstractPlayerNickRenderStep extends AbstractPipelineStep {
  /**
   * @param PlayerNickRenderContext $context
   *
   * @return array
   */
  abstract public function renderToArray(PlayerNickRenderContext $context): array;

  abstract public function renderArrayToHtml(PlayerNickRenderContextHtml $context): array;
}
