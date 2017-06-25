<?php

/**
 * Created by Gorlum 10.08.2016 21:29
 */

use Common\Invoker;

/**
 * Class InvokerTest
 * @coversDefaultClass \Common\Invoker
 */
class InvokerTest extends PHPUnit_Framework_TestCase {

  public function publicFunction() {
    return 'public';
  }

  protected function protectedFunction() {
    return 'protected';
  }

  /**
   * @covers ::build
   * @covers ::__construct
   * @covers ::__invoke
   */
  public function testBuild() {
    $t = Invoker::build(12345);
    $this->assertNull($t());

    $t = Invoker::build(array($this, 'protectedFunction'));
    $this->assertNull($t());


    $t = Invoker::build(function () { return 'lambda';});
    $this->assertEquals('lambda', $t());

    $t = Invoker::build(array($this, 'publicFunction'));
    $this->assertEquals('public', $t());

    $t = Invoker::build('mt_rand');
    $this->assertTrue(is_integer($t()));

//    $t = Invoker::build('array_unshift');
//    $array = array('q');
//    $this->assertEquals(2, $t($array, 'w'));
//    // TODO - uncomment for PHP > 5.3
//    $this->assertEquals(array('w', 'q'), $array);
  }

}
