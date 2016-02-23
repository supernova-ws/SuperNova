<?php

/**
 * Class UBERoundList
 *
 * @method UBERound offsetGet($offset)
 * @property UBERound[] $_container
 */
class UBERoundList extends ArrayAccessV2 {

  /**
   * @return UBERound
   */
  public function get_last_element() {
    return end($this->_container);
  }



  // REPORT RENDER *****************************************************************************************************
  // Генерируем отчет по флотам
  /**
   * @param     $template_result
   * @param UBE $ube
   */
  public function report_render_rounds(&$template_result, UBE $ube) {
    $round_count = $this->count();
    for($round = 1; $round <= $round_count - 1; $round++) {
      $template_result['.']['round'][] = array(
        'NUMBER' => $round,
        '.'      => array(
          'fleet' => $this[$round]->report_render_round($ube, $this[$round - 1]),
        ),
      );
    }
  }

}
