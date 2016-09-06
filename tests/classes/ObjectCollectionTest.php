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
   * @covers ::count
   */
  public function test() {
    $s = new \Common\ObjectCollection();
    $o1 = new stdClass();

    $this->assertEquals(0, count($s));
    $this->assertFalse(isset($s['i1']));
    $this->assertNull($s['i1']);

    $s['i1'] = $o1;
    $this->assertEquals(1, count($s));
    $this->assertTrue(isset($s['i1']));
    $this->assertEquals($o1, $s['i1']);

    unset($s['i1']);
    $this->assertEquals(0, count($s));
    $this->assertFalse(isset($s['i1']));
    $this->assertNull($s['i1']);
  }

}
