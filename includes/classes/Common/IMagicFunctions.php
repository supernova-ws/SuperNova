<?php

namespace Common;


interface IMagicFunctions {

  public function __call($name, $arguments);

}