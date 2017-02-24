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
   * PTL tag resolved with data from template. Include params - if any - for further processing
   * Can be used as cache key
   *
   * @var string $cacheKey
   */
  public $cacheKey = '';
  /**
   * Array of tag params
   *
   * @var array $params
   */
  public $params = array();

  /**
   * Ресолвит переменные и парсит тэг
   *
   * @param string   $ptlTag - tag from PTL
   * @param template $template - template object which used to resolve tags
   * @param array    $allowedParamsAsKeys - array of param name as a key like array('paramName' => mixed)
   */
  public function __construct($ptlTag, $template, $allowedParamsAsKeys = array()) {
    $this->raw = $ptlTag;

    $image_tag_ptl_resolved = $ptlTag;


    // Is there any template variables? Template variables passed in square brackets
    if (strpos($this->raw, '[') !== false && is_object($template)) {
      // TODO - многоуровневые вложения ?! Надо ли и где их можно применить
      preg_match_all('#(\[.+?\])#', $image_tag_ptl_resolved, $matches);
      foreach ($matches[0] as &$match) {
        $var_name = str_replace(array('[', ']'), '', $match);
        if (strpos($var_name, '.') !== false) {
          // Block variable like $template->assign_block_var($block_name, array($block_var => $value));
          // Only one level supported!
          list($block_name, $block_var) = explode('.', $var_name);
          isset($template->_block_value[$block_name][$block_var]) ? $image_tag_ptl_resolved = str_replace($match, $template->_block_value[$block_name][$block_var], $image_tag_ptl_resolved) : false;
        } elseif (strpos($var_name, '$') !== false) {
          // DEFINE-d variable like <!-- DEFINE $VAR = 'value' -->
          $define_name = substr($var_name, 1);
          isset($template->_tpldata['DEFINE']['.'][$define_name]) ? $image_tag_ptl_resolved = str_replace($match, $template->_tpldata['DEFINE']['.'][$define_name], $image_tag_ptl_resolved) : false;
        } else {
          // Root variable like $template->assign_var($var_name, $value)
          isset($template->_rootref[$var_name]) ? $image_tag_ptl_resolved = str_replace($match, $template->_rootref[$var_name], $image_tag_ptl_resolved) : false;
        }
      }
    }

    // Here so we potentially can use 2nd-level params - i.e. in template's variables
    if (strpos($image_tag_ptl_resolved, '|') !== false) {
      $params = explode('|', $image_tag_ptl_resolved);
      $image_id = $params[0];
      unset($params[0]);
      $params = HelperArray::parseParamStrings($params);
      $params = array_intersect_key($params, $allowedParamsAsKeys);
      $image_tag_ptl_resolved = implode('|', array_merge(array($image_tag_ptl_resolved), $params));
    } else {
      $params = array();
      $image_id = $image_tag_ptl_resolved;
    }

    $this->resolved = $image_id;
    $this->cacheKey = $image_tag_ptl_resolved;
    $this->params = $params;
  }

}
