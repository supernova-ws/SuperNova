<?php

/** Created by Gorlum 24.01.2026 15:43 */

namespace Refactor\PlayerNickRenderer;

use Refactor\ContextPipeline\AbstractPipeline;

/**
 * @psalm-type PlayerNickRenderOptionArray = array{
 *    color:bool,
 *    ally:bool,
 *    icons:bool,
 *    gender:bool,
 *    birthday:bool,
 *    vacancy:bool,
 *    player_rank:bool,
 *    race:bool,
 *    award:bool,
 * }
 *
 * Magick methods @see AbstractPipeline::__call()
 * Methods below would map to methods of childs of @see AbstractPlayerNickRenderStep (Ctrl+H in phpStorm for Hierarchy)
 * @method array renderToArray(PlayerNickRenderContext $context)
 * @method array renderArrayToHtml(PlayerNickRenderContextHtml $context)
 */
class PlayerNickRenderPipeline extends AbstractPipeline {
  public function __construct() {
    /** @see PlayerNickBase::renderToArray() */
    $this->registerBase(new PlayerNickBase());
  }

}
