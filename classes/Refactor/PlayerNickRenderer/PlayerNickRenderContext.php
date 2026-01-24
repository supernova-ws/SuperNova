<?php
/** Created by Gorlum 24.01.2026 17:04 */

namespace Refactor\PlayerNickRenderer;

use Refactor\ContextPipeline\AbstractContext;

class PlayerNickRenderContext extends AbstractContext {
  public array $user = [];
  public array $options = [];
  // TODO public ?PlayerNickRenderOption $optionsObject = null;
  public bool $allOptions = false;

  public $result = [];

  /**
   * @param array      $user
   * @param array|bool $options
   */
  public function __construct(array $user, $options) {
    $this->user = $user;

    // Expanding $options = true
    if ($options === true) {
      $this->allOptions = true;
      $this->options    = [
        'color' => true,
        'ally'  => true,
        'icons' => true,
      ];
    } else {
      $this->options = $options;
    }

    // Expanding $options['icons'] = true
    if (!empty($this->options['icons'])) {
      $this->options['gender']      = true;
      $this->options['player_rank'] = true;
      $this->options['birthday']    = true;
      $this->options['vacancy']     = true;
      // TODO - move somehow to module
      $this->options['race'] = true;
      // TODO - move somehow to module
      $this->options['award'] = true;
    }
  }

}
