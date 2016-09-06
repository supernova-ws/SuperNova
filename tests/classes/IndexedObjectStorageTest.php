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
   * @covers ::indexIsSet
   * @covers ::onObjectIndexEmpty
   */
  public function test() {
    $s = new \Common\IndexedObjectStorage();
    $o1 = new stdClass();
    $o2 = new stdClass();

    // indexGetObject - null value
    $this->assertNull($s->indexGetObject(null));
    $this->assertEquals(0, $s->count());

    // attach
    $s->attach($o1, 'i1');
    $this->assertTrue($s->indexIsSet('i1'));
    $this->assertEquals(1, $s->count());
    $this->assertEquals('i1', $s[$o1]);
    $this->assertEquals('i1', $s->offsetGet($o1));
    $this->assertEquals($o1, $s->indexGetObject('i1'));
    // detach
    $s->detach($o1);
    $this->assertEquals(0, $s->count());
    $this->assertNull( $s->indexGetObject('i1'));

    // attach
    $s->attach($o1, 'i3');
    // Index replacement on reattach
    $s->attach($o1, 'i1');
    $this->assertEquals(1, $s->count());
    $this->assertEquals('i1', $s[$o1]);
    $this->assertEquals($o1, $s->indexGetObject('i1'));
    $this->assertNull($s->indexGetObject('i3'));

    // offsetSet
    $s[$o2] = 'i2';
    $this->assertEquals(2, $s->count());
    // offsetGet
    $this->assertEquals('i2', $s[$o2]);
    // indexGetObject
    $this->assertEquals($o2, $s->indexGetObject('i2'));

    // detach
    $s->detach($o1);
    $this->assertEquals(1, $s->count());
    $this->assertNull($s->indexGetObject('i1'));
    $this->assertEquals(array('i2' => $o2), getPrivatePropertyValue($s, $this->indexesName));
    // offsetUnset
    unset($s[$o2]);
    $this->assertEquals(0, $s->count());
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
    $this->assertEquals(1, $s->count());

    // unserialize
    $str = $s->serialize();
    $s->detach($o1);
    $this->assertEquals(array(), getPrivatePropertyValue($s, $this->indexesName));
    $s->unserialize($str);
    $this->assertEquals($o1, $s->indexGetObject('i3'));
    $this->assertEquals(array('i3' => $o1), getPrivatePropertyValue($s, $this->indexesName));
    $this->assertEquals(1, $s->count());
    $s->detach($s->indexGetObject('i3'));
    $this->assertEquals(0, $s->count());

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


    $o3 = new stdClass();
    $s->attach($o3);
  }

  /**
   * @covers ::indexRebuild
   * @covers ::indexUnset
   * @covers ::indexSet
   * @covers ::onObjectIndexDuplicated
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
