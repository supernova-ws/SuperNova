<?php
/**
 * Created by Gorlum 22.06.2017 17:00
 */

namespace Event;


interface IObserver {

  /**
   * @param Event $event
   *
   * @return mixed
   */
  public function _update(Event $event);

}
