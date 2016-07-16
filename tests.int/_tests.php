<?php

function _testProcess($function, $caseList) {
  $caseList = array_reverse($caseList);
  foreach ($caseList as $testCase) {
    call_user_func_array($function, $testCase);
  }
}
