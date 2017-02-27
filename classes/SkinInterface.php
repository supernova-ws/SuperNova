<?php

/**
 * Created by Gorlum 23.02.2017 13:10
 */
interface SkinInterface {

  /**
   * Get skin name
   *
   * @return string
   */
  public function getName();

  /**
   * Возвращает строку для вывода в компилированном темплейте PTL
   *
   * @param PTLTag $ptlTag
   *
   * @return string
   */
  public function imageFromPTLTag($ptlTag);

  /**
   * Compiles image string from string
   *
   * @param string   $stringTag
   * @param template $template
   *
   * @return string
   */
  public function imageFromStringTag($stringTag, $template = null);

}
