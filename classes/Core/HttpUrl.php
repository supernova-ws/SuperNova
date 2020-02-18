<?php
/**
 * Created by Gorlum 13.02.2020 10:46
 */

namespace Core;


class HttpUrl {
  const SCHEME_HTTPS = 'https';
  const SCHEME_HTTP = 'http';

  const FIELD_SIGNATURE = 'sign';
  const FIELD_TIMESTAMP_MICRO = '_sn_ts_micro';

  /**
   * @var GlobalContainer $gc
   */
  public $gc;

  /**
   * Scheme - `http` or `https`
   *
   * @var string $scheme
   */
  public $scheme = '';
  /**
   * Host address
   *
   * @var string $host
   */
  public $host = '';
  /**
   * Path from server root WITH starting slash
   *
   * @var string $path
   */
  public $path = '';
  /**
   * [(str)$paramName => [(mixed)$paramValue, ...], ...]
   *
   * @var string[][]
   */
  public $params = [];
  /**
   * Secure string known only to server for internal call exchange
   *
   * @var string $cypher
   */
  public $cypher = '';

  /**
   * A bit of syntax sugar
   *
   * @param GlobalContainer $gc
   *
   * @return static
   */
  public static function spawn(GlobalContainer $gc) {
    return new static($gc);
  }

  public function __construct(GlobalContainer $gc) {
    $this->gc = $gc;
  }

  public function parseUrl($url) {
    $parsed = explode('://', $url);

    if (is_array($parsed) && count($parsed) > 1 && strtolower($parsed[0]) === self::SCHEME_HTTPS) {
      $this->scheme = self::SCHEME_HTTPS;
    } else {
      $this->scheme = self::SCHEME_HTTP;
    }

    if (is_array($parsed)) {
      array_shift($parsed);
    } else {
      $parsed = [];
    }

    $server = explode('/', implode('://', $parsed));
    if (is_array($server) && !empty($server)) {
      $this->host = $server[0];
      array_shift($server);
    } else {
      $this->host = [];
    }

    return $this->explodeUri(implode('/', $server));
  }

  /**
   * Fills URI from current URL
   *
   * @return $this
   */
  public function fillCurrent() {
    if (
      (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) === 'on')
      ||
      (!empty($_SERVER['REQUEST_SCHEME']) && strtolower($_SERVER['REQUEST_SCHEME']) === self::SCHEME_HTTPS)
    ) {
      $this->scheme = self::SCHEME_HTTPS;
    } else {
      $this->scheme = self::SCHEME_HTTP;
    }

    $this->host = !empty($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';

    $this->path   = '';
    $this->params = [];

    return $this->explodeUri($_SERVER['REQUEST_URI']);
  }

  /**
   * Return server root - i.e. host with scheme like http://localhost/
   * DO NOT adds trailing slash
   *
   * @return string
   */
  public function serverRoot() {
    return "{$this->scheme}://{$this->host}";
  }

  public function url() {
    return $this->serverRoot() . $this->uri();
  }

  public function uri() {
    return $this->path . $this->renderGetParams();
  }

  /**
   * @param bool $timeStamp
   *
   * @return string
   */
  public function urlSigned($timeStamp = true) {
    if ($timeStamp) {
      $this->params[self::FIELD_TIMESTAMP_MICRO] = SN_TIME_MICRO;
    }

    $this->removeSignature();

    $signature = $this->gc->crypto->sign($this->url() . $this->cypher);

    $this->params[self::FIELD_SIGNATURE] = $signature;

    return $this->url();
  }

  /**
   * Checks signature in URL
   *
   * @return bool
   */
  public function isSigned() {
    $isMatched = false;

    $signatureExists = $this->isSignaturePresent();
    if ($signatureExists) {
      $signature = $this->removeSignature();
      // Checking signature
      $isMatched = $this->gc->crypto->signCheck($this->url() . $this->cypher, $signature);
      // Re-adding signature to URL
      $this->params[self::FIELD_SIGNATURE] = $signature;
    }

    return $isMatched;
  }


  /**
   * Explodes provided URI to path and params
   *
   * @param string $uri URI part of URL - i.e. path from server root with params
   *
   * @return $this
   */
  protected function explodeUri($uri) {
    if (!is_string($uri) || empty($uri)) {
      return $this;
    }

    $exploded   = explode('?', $uri);
    $this->path = array_shift($exploded);
    if (substr($this->path, 0, 1) !== '/') {
      $this->path = '/' . $this->path;
    }

    // Re-imploding left URI parts for a case of malformed request
    $this->params = $this->parseGetParams(implode('?', $exploded));

    return $this;
  }

  /**
   * @param string $paramString
   *
   * @return array
   */
  protected function parseGetParams($paramString) {
    $result = [];

    if (!is_string($paramString) || empty($paramString)) {
      return $result;
    }

    $params = explode('&', $paramString);
    if (!is_array($params) || empty($params)) {
      return $result;
    }

    foreach ($params as $paramString) {
      $parsed = explode('=', $paramString);

      $paramName  = array_shift($parsed);
      $paramValue = implode('&', $parsed);

      // TODO - indexed params support
      $result[$paramName] = $paramValue;
    }

    return $result;
  }

  protected function renderGetParams() {
    $result = '';
    if (empty($this->params) || !is_array($this->params)) {
      return $result;
    }

    $query = [];
    foreach ($this->params as $paramName => $paramValue) {
      $query[] = urlencode($paramName) . '=' . urlencode($paramValue);
    }

    return '?' . implode('&', $query);
  }

  public function removeSignature() {
    $result = '';
    if ($this->isSignaturePresent()) {
      $result = $this->params[self::FIELD_SIGNATURE];
      unset($this->params[self::FIELD_SIGNATURE]);
    }

    return $result;
  }

  public function setCypher($cypher) {
    $this->cypher = $cypher;

    return $this;
  }

  /**
   * @param array|string $params
   *
   * @return HttpUrl
   */
  public function addParams($params) {
    if (is_string($params)) {
      $params = $this->parseGetParams($params);
    }

    if (is_array($params) && !empty($params)) {
      foreach ($params as $paramName => $value) {
        $this->params[$paramName] = $value;
      }
    }

    return $this;
  }

  public function addPath($path) {
    if (substr($this->path, -1) !== '/') {
      $this->path .= '/';
    }
    $this->path .= $path;

    return $this;
  }

  /**
   * @return bool
   */
  public function isSignaturePresent() {
    return array_key_exists(self::FIELD_SIGNATURE, $this->params);
  }

}
