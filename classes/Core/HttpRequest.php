<?php
/**
 * Created by Gorlum 13.02.2020 10:28
 */

namespace Core;

class HttpRequest {
  const REQUEST_GET = 'GET';
  const REQUEST_POST = 'POST';

  /**
   * @var GlobalContainer $gc
   */
  protected $gc;

  public $method = '';
  /**
   * @var HttpUrl $url
   */
  public $url;

  public function __construct(GlobalContainer $gc) {
    $this->gc = $gc;

    $this->url = new HttpUrl($this->gc);
  }

  public function fillCurrent() {
    $this->url->fillCurrent();

    $this->method = strtoupper($_SERVER['REQUEST_METHOD']);

  }

}
