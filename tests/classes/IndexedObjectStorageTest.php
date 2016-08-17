<?php

/**
 * Created by Gorlum 17.08.2016 23:30
 */

/**
 * Class IndexedObjectStorageTest
 * @coversDefaultClass \Common\IndexedObjectStorage
 */
class IndexedObjectStorageTest extends PHPUnit_Framework_TestCase {

  protected $indexesName = 'index';

  /**
   * @covers ::attach
   * @covers ::detach
   *
   * @covers ::offsetSet
   * @covers ::offsetGet
   * @covers ::offsetUnset
   * @covers ::indexGetObject
   * @covers ::setInfo
   *
   * @covers ::unserialize
   *
   * @covers ::addAll
   * @covers ::removeAll
   * @covers ::removeAllExcept
   *
   * @covers ::indexRebuild
   * @covers ::indexUnset
   * @covers ::indexSet
   */
  public function test() {
    $s = new \Common\IndexedObjectStorage();
    $o1 = new stdClass();
    $o2 = new stdClass();

    // attach
    $s->attach($o1, 'i3');
    $s->attach($o1, 'i1');
    // offsetSet
    $s[$o2] = 'i2';

    // offsetGet
    $this->assertEquals('i1', $s[$o1]);
    $this->assertEquals('i2', $s[$o2]);

    // indexGetObject
    $this->assertEquals($o1, $s->indexGetObject('i1'));
    $this->assertEquals($o2, $s->indexGetObject('i2'));
    $this->assertNull($s->indexGetObject('i3'));
    $this->assertNull($s->indexGetObject(null));

    // detach
    $s->detach($o1);
    $this->assertNull($s->indexGetObject('i1'));
    $this->assertEquals(array('i2' => $o2), getPrivatePropertyValue($s, $this->indexesName));
    // offsetUnset
    unset($s[$o2]);
    $this->assertNull($s->indexGetObject('i2'));
    $this->assertEquals(array(), getPrivatePropertyValue($s, $this->indexesName));

    // setInfo
    $s->attach($o1, 'i1');
    $this->assertEquals($o1, $s->indexGetObject('i1'));
    $s->rewind();
    $this->assertEquals($o1, $s->current());
    $s->setInfo('i3');
    $this->assertNull($s->indexGetObject('i1'));
    $this->assertEquals($o1, $s->indexGetObject('i3'));
    $this->assertEquals(array('i3' => $o1), getPrivatePropertyValue($s, $this->indexesName));


    // unserialize
    $str = $s->serialize();
    $s->detach($o1);
    $this->assertEquals(array(), getPrivatePropertyValue($s, $this->indexesName));
    $s->unserialize($str);
    $this->assertEquals($o1, $s->indexGetObject('i3'));
    $this->assertEquals(array('i3' => $o1), getPrivatePropertyValue($s, $this->indexesName));
    $s->detach($s->indexGetObject('i3'));

    // addAll
    $s->attach($o1, 'i1');
    $s1 = new SplObjectStorage();
    $s1[$o2] = 'i2';
    $s->addAll($s1);
    $this->assertTrue($s->contains($o1));
    $this->assertTrue($s->contains($o2));
    $this->assertEquals($o1, $s->indexGetObject('i1'));
    $this->assertEquals($o2, $s->indexGetObject('i2'));
    $this->assertEquals(array('i1' => $o1, 'i2' => $o2), getPrivatePropertyValue($s, $this->indexesName));

    // removeAll
    $s->removeAll($s1);
    $this->assertTrue($s->contains($o1));
    $this->assertEquals($o1, $s->indexGetObject('i1'));
    $this->assertFalse($s->contains($o2));
    $this->assertNull($s->indexGetObject('i2'));
    $this->assertEquals(array('i1' => $o1), getPrivatePropertyValue($s, $this->indexesName));

    // removeAllExcept
    $s[$o2] = 'i2';
    $s->removeAllExcept($s1);
    $this->assertFalse($s->contains($o1));
    $this->assertNull($s->indexGetObject('i1'));
    $this->assertTrue($s->contains($o2));
    $this->assertEquals($o2, $s->indexGetObject('i2'));
    $this->assertEquals(array('i2' => $o2), getPrivatePropertyValue($s, $this->indexesName));
  }

  /**
   * @covers ::indexRebuild
   * @covers ::indexUnset
   * @covers ::indexSet
   */
  public function testExceptionDuplicateIndex() {
    $s = new \Common\IndexedObjectStorage();
    $o1 = new stdClass();
    $s->attach($o1, 'i1');
    $str = $s->serialize();
    $this->setExpectedException('\Exception', 'Duplicate index [i1] in Common\IndexedObjectStorage');
    $s->unserialize($str);
  }

}



// + public function attach ($object, $data = null) {}
// + public function detach ($object) {}
// # public function contains ($object) {}
//public function addAll ($storage) {}
//public function removeAll ($storage) {}
//public function removeAllExcept ($storage) {}
// # public function getInfo () {}
// + public function setInfo ($data) {}
// # public function count () {}
// # public function rewind () {}
// # public function valid () {}
// # public function key () {}
// # public function current () {}
// # public function next () {}
// + public function unserialize ($serialized) {}
// # public function serialize () {}
// # public function offsetExists ($object) {}
// + public function offsetSet ($object, $data = null) {}
// + public function offsetUnset ($object) {}
// # public function offsetGet ($object) {}
// # public function getHash($object) {}
