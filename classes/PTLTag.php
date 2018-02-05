<?php

/**
 * Created by Gorlum 23.02.2017 14:34
 */
class PTLTag {

  /**Raw tag
   *
   * @var string $raw
   */
  public $raw = '';
  /**
   * Fully resolved tag without params
   *
   * @var mixed|string $resolved
   */
  public $resolved = '';
  /**
   * Namespace for block refs
   *
   * @var string $namespace
   */
  public $namespace = '';
  /**
   * Array of tag params
   *
   * @var array $params
   */
  public $params = array();

  /**
   * Template via which this tag is compiled
   *
   * @var template $template
   */
  public $template;

  /**
   * Ресолвит переменные и парсит тэг
   *
   * @param string        $stringTag - tag from PTL
   * @param template|null $template - template object which used to resolve tags
   * @param array         $allowedParamsAsKeys - array of param name as a key like array('paramName' => mixed)
   */
  public function __construct($stringTag, $template = null, $allowedParamsAsKeys = array()) {
    $this->template = $template;
    $this->raw = $stringTag;

    // Separating params - so there are will be no false-positives for template's variable names
    if (strpos($this->raw, '|') !== false) {
      $this->params = explode('|', $this->raw);
      $this->resolved = $this->params[0];
      unset($this->params[0]);
    } else {
      $this->params = array();
      $this->resolved = $this->raw;
    }

    // Is there any template variables? Template variables passed in square brackets
    if (strpos($this->resolved, '[') !== false && is_object($this->template)) {
      $this->resolved = $this->resolveTemplateVars($this->resolved);
    }

    // Is there any namespaces in tag?
    if (strpos($this->resolved, '.') !== false) {
      $this->namespace = substr($this->resolved, 0, strrpos($this->resolved, '.'));
    }

    // Here so we potentially can use 2nd-level params - i.e. in template's variables
    if (count($this->params)) {
      $this->params = HelperArray::parseParamStrings($this->params);
      $this->params = array_intersect_key($this->params, $allowedParamsAsKeys);
      ksort($this->params);
    }
  }

  /**
   * Resolves template's variables in tag
   *
   * Template's variables defined in square brackets:
   *    [$DEFINE] - Defines is a DEFINE-d variable like <!-- DEFINE $VAR = 'value' -->
   *    [VAR_NAME] - Root variable like $template->assign_var($var_name, $value)
   *    [block_name.BLOCK_VAR_NAME] - Block variable like $template->assign_block_var($block_name, array($block_var => $value));
   *                                  Use last level block name for access multilevel blocks - i.e. [e.E1] for accessing current value of q.w.e.E1
   *
   * Other constructions (like {D_xxx}, {C_xxx} etc) is not supported
   *
   * @param string $rawTag
   *
   * @return mixed|string
   */
  protected function resolveTemplateVars($rawTag) {
    // Многоуровневые вложения ?! Надо ли и где их можно применить
    preg_match_all('#(\[.+?\])#', $rawTag, $matches);

    foreach ($matches[0] as &$match) {
      $var_name = str_replace(array('[', ']'), '', $match);

      if (strpos($var_name, '.') !== false) {
        // Block variables
        list($block_name, $block_var) = explode('.', $var_name);
        isset($this->template->_block_value[$block_name][$block_var])
          ? $rawTag = str_replace($match, $this->template->_block_value[$block_name][$block_var], $rawTag) : false;
      } elseif (strpos($var_name, '$') !== false) {
        // Defines
        // Only one $VAR can be used per square bracket
        $define_name = substr($var_name, 1);
        isset($this->template->_tpldata['DEFINE']['.'][$define_name])
          ? $rawTag = str_replace($match, $this->template->_tpldata['DEFINE']['.'][$define_name], $rawTag) : false;
      } else {
        // Root variable
        isset($this->template->_rootref[$var_name])
          ? $rawTag = str_replace($match, $this->template->_rootref[$var_name], $rawTag) : false;
      }
    }

    return $rawTag;
  }


  public function removeParam($paramName) {
    if (!array_key_exists($paramName, $this->params)) {
      return;
    }

    unset($this->params[$paramName]);
  }

  /**
   * Generates unique cache key for tag
   *
   * @return string
   */
  public function getCacheKey() {
    $result = [$this->resolved];

    foreach ($this->params as $key => $value) {
      $result[] = $key . '=' . $value;
    }

    return implode('|', $result);
  }

}
