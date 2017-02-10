<?php

/**
 * Created by Gorlum 10.02.2017 01:31
 */

use Common\AccessAccessors;
use Common\AccessorsV2;

/**
 * Class AccessAccessorsTest
 * @coversDefaultClass \Common\AccessAccessors
 */
class AccessAccessorsTest extends PHPUnit_Framework_TestCase {

  /**
   * @var AccessAccessors $object
   */
  protected $object;

  /**
   * @var AccessorsV2 $accessors
   */
  protected $accessors;

  public function setUp() {
    parent::setUp();

    $this->object = new AccessAccessors();
    $this->accessors = new AccessorsV2();

    $this->accessors->__setConstant = function ($that, $varName) {
      $that[$varName] = '__setConstant';
    };

    $this->accessors->__setVar = function ($that, $varName, $value) {
      $that[$varName] = $value;
    };

    $this->accessors->__getVar1 = function ($that, $varName) {
      return $that[$varName];
    };

    $this->accessors->__setVar2 = function ($that, $varName, $value) {
      $that[$varName] = $value;
    };
    $this->accessors->__getVar2 = function ($that, $varName) {
      return $that[$varName];
    };

    $this->accessors->__setVarModifySet = function ($that, $varName, $value) {
      $that[$varName] = $value . 'Set';
    };

    $this->accessors->__getVarModifyGet = function ($that, $varName) {
      return $that[$varName] . 'Get';
    };

    $this->accessors->__setVarModifySetGet = function ($that, $varName, $value) {
      $that[$varName] = $value . 'Set';
    };
    $this->accessors->__getVarModifySetGet = function ($that, $varName) {
      return $that[$varName] . 'Get';
    };

    $this->accessors->call = function ($that) {
      return 'Called' . $that->VarModifySet;
    };

    $this->accessors->share('__setShareSet', function ($that, $varName, $value) {
      $that[$varName] = $value . 'ShareSet';
    });
    $this->accessors->share('__getShareGet', function ($that, $varName) {
      return $that[$varName] . 'ShareGet';
    });
    $this->accessors->share('__setShareSetGet', function ($that, $varName, $value) {
      $that[$varName] = $value . 'ShareSet';
    });
    $this->accessors->share('__getShareSetGet', function ($that, $varName) {
      return $that[$varName] . 'ShareGet';
    });
  }

  public function tearDown() {
    unset($this->object);
    parent::tearDown();
  }

  /**
   * @covers ::__construct
   * @covers ::isEmpty
   * @covers ::clear
   */
  public function test__get() {
    $object = new AccessAccessors();

    $this->assertTrue($object->isEmpty());
    $this->assertFalse(isset($object->test));
    $this->assertNull($object->test);
  }

  public function dataSetterGetter() {
    return array(
      // Testing constant setter
      array('Constant', 'test', '__setConstant'),
      // Testing simple setter w/o getter
      array('Var', ' ', ' '),
      // Testing simple getter w/o setter
      array('Var1', 1, 1),
      // Testing simple setter and simple getter
      array('Var2', 2, 2),
      // Testing setting/getting w/o accessors
      array('Var3', 3, 3),
      // Value modification on set
      array('VarModifySet', 4, '4Set'),
      // Value modification on get
      array('VarModifyGet', 5, '5Get'),
      // Value modification on set and get
      array('VarModifySetGet', 6, '6SetGet'),
    );
  }

  /**
   * @covers ::setAccessors
   * @covers ::__get
   * @covers ::__isset
   * @covers ::__set
   * @covers ::__unset
   * @covers ::__call
   * @covers ::isEmpty
   *
   * @dataProvider dataSetterGetter
   */
  public function testSetterGetter($varName, $setValue, $expectedValue) {
    $this->object->setAccessors($this->accessors);

    $this->assertTrue($this->object->isEmpty());

    $this->assertFalse(isset($this->object->$varName));
    $this->object->$varName = $setValue;
    $this->assertEquals($expectedValue, $this->object->$varName);
    $this->assertTrue(isset($this->object->$varName));
    unset($this->object->$varName);
    $this->assertFalse(isset($this->object->$varName));

    $this->assertTrue($this->object->isEmpty());
  }


  public function dataShared() {
    return array(
      array('ShareSet', '1ShareSet'),
      array('ShareGet', '1ShareGet'),
      array('ShareSetGet', '1ShareSetShareGet'),
    );
  }

  /**
   * Testing shared accessors
   *
   * @covers ::setAccessors
   * @covers ::__get
   * @covers ::__set
   * @covers ::__call
   *
   * @dataProvider dataShared
   */
  public function testShared($varName, $expected) {
    $this->object->setAccessors($this->accessors);

    $this->object->$varName = 1;
    $this->assertEquals($expected, $this->object->$varName);
    $this->object->$varName = 2;
    $this->assertEquals($expected, $this->object->$varName);
  }


  /**
   * @covers ::isEmpty
   * @covers ::clear
   * @covers ::__get
   * @covers ::__set
   * @covers ::__call
   */
  public function testCall() {
    $this->object->setAccessors($this->accessors);

    $this->assertTrue($this->object->isEmpty());

    $this->object->VarModifySet = 1;
    $this->assertEquals('Called1Set', $this->object->call());

    $this->object->clear();
    $this->assertTrue($this->object->isEmpty());
  }

  /**
   * @covers ::isEmpty
   * @covers ::clear
   * @covers ::__get
   * @covers ::__set
   * @covers ::__call
   * @covers ::offsetGet
   * @covers ::offsetSet
   * @covers ::offsetExists
   * @covers ::offsetUnset
   */
  public function testArrayAccess() {
    $this->object->setAccessors($this->accessors);

    $this->assertTrue($this->object->isEmpty());

    // Bypassing getter
    $this->object->VarModifyGet = 1;
    $this->assertEquals('1Get', $this->object->VarModifyGet);
    $this->assertEquals(1, $this->object['VarModifyGet']);

    // Bypassing setter
    $this->object['VarModifySet'] = 2;
    $this->assertEquals(2, $this->object->VarModifySet);
    $this->assertEquals(2, $this->object['VarModifySet']);

    // Checking unset/isset
    $this->assertTrue(isset($this->object['VarModifySet']));
    unset($this->object['VarModifySet']);
    $this->assertFalse(isset($this->object['VarModifySet']));
  }

}
