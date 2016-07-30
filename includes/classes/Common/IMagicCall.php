<?php

namespace Common;


interface IMagicCall {

  public function __call($name, $arguments);

}