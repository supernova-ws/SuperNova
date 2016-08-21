<?php

/**
 * Created by Gorlum 17.08.2016 23:30
 */

/**
 * Class ObjectCollectionTest
 * @coversDefaultClass \Common\ObjectCollection
 */
class ObjectCollectionTest extends PHPUnit_Framework_TestCase {

  protected $indexesName = 'index';

  /**
   * @covers ::offsetExists
   * @covers ::offsetSet
   * @covers ::offsetGet
   * @covers ::offsetUnset
   */
  public function test() {
    $s = new \Common\ObjectCollection();
    $o1 = new stdClass();

    $this->assertFalse(isset($s['i1']));
    $this->assertNull($s['i1']);

    $s['i1'] = $o1;
    $this->assertTrue(isset($s['i1']));
    $this->assertEquals($o1, $s['i1']);

    unset($s['i1']);
    $this->assertFalse(isset($s['i1']));
    $this->assertNull($s['i1']);
  }

}
