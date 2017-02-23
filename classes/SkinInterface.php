<?php

/**
 * Created by Gorlum 23.02.2017 13:10
 */
interface SkinInterface {

  /**
   * Возвращает строку для вывода в компилированном темплейте PTL
   *
   * @param string   $image_tag
   * @param template $template
   *
   * @return string
   */
  public function compile_image($image_tag, $template);

}