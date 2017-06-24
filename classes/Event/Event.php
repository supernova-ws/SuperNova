<?php
/**
 * Created by Gorlum 22.06.2017 17:00
 */

namespace Event;


class Event {

  protected static $eventTypeId = EVENT_NONE;

  public function getType() {
    return static::$eventTypeId;
  }

}
