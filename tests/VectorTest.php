<?php

/**
 * Class VectorTest
 *
 * @coversDefaultClass Vector
 */
class VectorTest extends PHPUnit_Framework_TestCase {

  /**
   * @var Vector $object
   */
  protected $object;
  /**
   * @var stdClass $config
   */
  protected $config;

  public function setUp() {
    parent::setUp();
    $this->object = new Vector();

    $this->object->type = PT_PLANET;

    $this->config = new stdClass();
    $this->config->game_maxGalaxy = 2;
    $this->config->game_maxSystem = 4;
    $this->config->game_maxPlanet = 6;
    $this->config->uni_galaxy_distance = 20000;

    Vector::_staticInit($this->config);
  }

  public function tearDown() {
    unset($this->object);
    parent::tearDown();
  }

  /**
   * @covers ::_staticInit
   */
  public function test_staticInit() {
    $this->assertTrue(getPrivateProperty('Vector', '_isStaticInit')->getValue($this->object));
    $this->assertEquals(2, getPrivateProperty('Vector', 'knownGalaxies')->getValue($this->object));
    $this->assertEquals(4, getPrivateProperty('Vector', 'knownSystems')->getValue($this->object));
    $this->assertEquals(6, getPrivateProperty('Vector', 'knownPlanets')->getValue($this->object));
    $this->assertEquals(PT_PLANET, getPrivateProperty('Vector', 'type')->getValue($this->object));
    $this->assertEquals(20000, getPrivateProperty('Vector', 'galaxyDistance')->getValue($this->object));
  }

  /**
   * @covers ::readFromVector
   */
  public function testReadFromVector() {
    $vector = new Vector(1, 3, 5, PT_MOON);
    $this->object->readFromVector($vector);

    $this->assertEquals(1, getPrivateProperty('Vector', 'galaxy')->getValue($this->object));
    $this->assertEquals(3, getPrivateProperty('Vector', 'system')->getValue($this->object));
    $this->assertEquals(5, getPrivateProperty('Vector', 'planet')->getValue($this->object));
    $this->assertEquals(PT_MOON, getPrivateProperty('Vector', 'type')->getValue($this->object));
  }

  /**
   * @covers ::getParamInt
   */
  // TODO
  public function testGetParamInt() {
  }

  /**
   * @covers ::readFromParamFleets
   */
  // TODO
  public function testReadFromParamFleets() {
  }

  public function data__construct() {
    $vector = new Vector(4, 5, 6, PT_PLANET);

    return array(
      array(1, 2, 3, PT_MOON, 1, 2, 3, PT_MOON),
      array(Vector::READ_VECTOR, $vector, 3, PT_MOON, 4, 5, 6, PT_PLANET),
      // TODO
//      array(Vector::READ_PARAMS_FLEET, array('galaxy' => 90, 'system' => 91, 'planet' => 92, 'planet_type' => 93,), 99, PT_MOON, 4, 5, 6, PT_PLANET),
    );
  }

  /**
   * @dataProvider data__construct
   *
   * @covers ::__construct
   */
  public function test__construct($galaxy, $system, $planet, $type, $g, $s, $p, $t) {
    $this->object = new Vector($galaxy, $system, $planet, $type);

    $this->assertEquals($g, getPrivateProperty('Vector', 'galaxy')->getValue($this->object));
    $this->assertEquals($s, getPrivateProperty('Vector', 'system')->getValue($this->object));
    $this->assertEquals($p, getPrivateProperty('Vector', 'planet')->getValue($this->object));
    $this->assertEquals($t, getPrivateProperty('Vector', 'type')->getValue($this->object));
  }

  public function dataDistance() {
    return array(
      // Checking that $returnZero works on same planet
      array(new Vector(2, 4, 6, PT_PLANET), new Vector(2, 4, 6, PT_PLANET), true, 0),
      array(new Vector(2, 4, 6, PT_PLANET), new Vector(2, 4, 6, PT_PLANET), false, 5),
      array(new Vector(2, 4, 6, PT_PLANET), new Vector(2, 4, 6, PT_MOON), true, 5),
      array(new Vector(2, 4, 6, PT_PLANET), new Vector(2, 4, 6, PT_MOON), false, 5),
      array(new Vector(2, 4, 6, PT_PLANET), new Vector(2, 4, 6, PT_DEBRIS), true, 5),
      array(new Vector(2, 4, 6, PT_PLANET), new Vector(2, 4, 6, PT_DEBRIS), false, 5),

      array(new Vector(2, 4, 6, PT_PLANET), new Vector(2, 4, 7, PT_PLANET), true, 1005),
      array(new Vector(2, 4, 6, PT_PLANET), new Vector(2, 4, 7, PT_PLANET), false, 1005),
      array(new Vector(2, 4, 6, PT_PLANET), new Vector(2, 4, 7, PT_MOON), true, 1005),
      array(new Vector(2, 4, 6, PT_PLANET), new Vector(2, 4, 7, PT_MOON), false, 1005),
      array(new Vector(2, 4, 6, PT_PLANET), new Vector(2, 4, 7, PT_DEBRIS), true, 1005),
      array(new Vector(2, 4, 6, PT_PLANET), new Vector(2, 4, 7, PT_DEBRIS), false, 1005),

      array(new Vector(2, 4, 6, PT_PLANET), new Vector(2, 4, 5, PT_PLANET), true, 1005),
      array(new Vector(2, 4, 6, PT_PLANET), new Vector(2, 4, 5, PT_PLANET), false, 1005),
      array(new Vector(2, 4, 6, PT_PLANET), new Vector(2, 4, 5, PT_MOON), true, 1005),
      array(new Vector(2, 4, 6, PT_PLANET), new Vector(2, 4, 5, PT_MOON), false, 1005),
      array(new Vector(2, 4, 6, PT_PLANET), new Vector(2, 4, 5, PT_DEBRIS), true, 1005),
      array(new Vector(2, 4, 6, PT_PLANET), new Vector(2, 4, 5, PT_DEBRIS), false, 1005),

      array(new Vector(2, 4, 6, PT_PLANET), new Vector(2, 4, 8, PT_PLANET), true, 1010),
      array(new Vector(2, 4, 6, PT_PLANET), new Vector(2, 4, 8, PT_PLANET), false, 1010),
      array(new Vector(2, 4, 6, PT_PLANET), new Vector(2, 4, 8, PT_MOON), true, 1010),
      array(new Vector(2, 4, 6, PT_PLANET), new Vector(2, 4, 8, PT_MOON), false, 1010),
      array(new Vector(2, 4, 6, PT_PLANET), new Vector(2, 4, 8, PT_DEBRIS), true, 1010),
      array(new Vector(2, 4, 6, PT_PLANET), new Vector(2, 4, 8, PT_DEBRIS), false, 1010),

      array(new Vector(2, 4, 6, PT_PLANET), new Vector(2, 5, 6, PT_PLANET), true, 2795),
      array(new Vector(2, 4, 6, PT_PLANET), new Vector(2, 5, 6, PT_PLANET), false, 2795),
      array(new Vector(2, 4, 6, PT_PLANET), new Vector(2, 5, 6, PT_MOON), true, 2795),
      array(new Vector(2, 4, 6, PT_PLANET), new Vector(2, 5, 6, PT_MOON), false, 2795),
      array(new Vector(2, 4, 6, PT_PLANET), new Vector(2, 5, 6, PT_DEBRIS), true, 2795),
      array(new Vector(2, 4, 6, PT_PLANET), new Vector(2, 5, 6, PT_DEBRIS), false, 2795),

      array(new Vector(2, 4, 6, PT_PLANET), new Vector(2, 3, 6, PT_PLANET), true, 2795),
      array(new Vector(2, 4, 6, PT_PLANET), new Vector(2, 3, 6, PT_PLANET), false, 2795),
      array(new Vector(2, 4, 6, PT_PLANET), new Vector(2, 3, 6, PT_MOON), true, 2795),
      array(new Vector(2, 4, 6, PT_PLANET), new Vector(2, 3, 6, PT_MOON), false, 2795),
      array(new Vector(2, 4, 6, PT_PLANET), new Vector(2, 3, 6, PT_DEBRIS), true, 2795),
      array(new Vector(2, 4, 6, PT_PLANET), new Vector(2, 3, 6, PT_DEBRIS), false, 2795),

      array(new Vector(2, 4, 6, PT_PLANET), new Vector(2, 2, 6, PT_PLANET), true, 2890),
      array(new Vector(2, 4, 6, PT_PLANET), new Vector(2, 2, 6, PT_PLANET), false, 2890),
      array(new Vector(2, 4, 6, PT_PLANET), new Vector(2, 2, 6, PT_MOON), true, 2890),
      array(new Vector(2, 4, 6, PT_PLANET), new Vector(2, 2, 6, PT_MOON), false, 2890),
      array(new Vector(2, 4, 6, PT_PLANET), new Vector(2, 2, 6, PT_DEBRIS), true, 2890),
      array(new Vector(2, 4, 6, PT_PLANET), new Vector(2, 2, 6, PT_DEBRIS), false, 2890),

      array(new Vector(2, 4, 6, PT_PLANET), new Vector(1, 4, 6, PT_PLANET), true, 20000),
      array(new Vector(2, 4, 6, PT_PLANET), new Vector(1, 4, 6, PT_PLANET), false, 20000),
      array(new Vector(2, 4, 6, PT_PLANET), new Vector(1, 4, 6, PT_MOON), true, 20000),
      array(new Vector(2, 4, 6, PT_PLANET), new Vector(1, 4, 6, PT_MOON), false, 20000),
      array(new Vector(2, 4, 6, PT_PLANET), new Vector(1, 4, 6, PT_DEBRIS), true, 20000),
      array(new Vector(2, 4, 6, PT_PLANET), new Vector(1, 4, 6, PT_DEBRIS), false, 20000),

      array(new Vector(2, 4, 6, PT_PLANET), new Vector(3, 4, 6, PT_PLANET), true, 20000),
      array(new Vector(2, 4, 6, PT_PLANET), new Vector(3, 4, 6, PT_PLANET), false, 20000),
      array(new Vector(2, 4, 6, PT_PLANET), new Vector(3, 4, 6, PT_MOON), true, 20000),
      array(new Vector(2, 4, 6, PT_PLANET), new Vector(3, 4, 6, PT_MOON), false, 20000),
      array(new Vector(2, 4, 6, PT_PLANET), new Vector(3, 4, 6, PT_DEBRIS), true, 20000),
      array(new Vector(2, 4, 6, PT_PLANET), new Vector(3, 4, 6, PT_DEBRIS), false, 20000),

      array(new Vector(2, 4, 6, PT_PLANET), new Vector(4, 4, 6, PT_PLANET), true, 40000),
      array(new Vector(2, 4, 6, PT_PLANET), new Vector(4, 4, 6, PT_PLANET), false, 40000),
      array(new Vector(2, 4, 6, PT_PLANET), new Vector(4, 4, 6, PT_MOON), true, 40000),
      array(new Vector(2, 4, 6, PT_PLANET), new Vector(4, 4, 6, PT_MOON), false, 40000),
      array(new Vector(2, 4, 6, PT_PLANET), new Vector(4, 4, 6, PT_DEBRIS), true, 40000),
      array(new Vector(2, 4, 6, PT_PLANET), new Vector(4, 4, 6, PT_DEBRIS), false, 40000),
    );
  }

  /**
   * @dataProvider dataDistance
   *
   * @covers ::distance
   *
   * @param Vector $vector1
   * @param Vector $vector2
   * @param bool   $returnZero
   * @param int    $distance
   */
  public function testDistance($vector1, $vector2, $returnZero, $distance) {
    $this->object->readFromVector($vector1);
    $this->assertEquals($distance, $this->object->distance($vector2, $returnZero));
  }

  public function dataConvertToVector() {
    return array(
      array(array('galaxy' => 1, 'system' => 3, 'planet' => 5, 'planet_type' => PT_MOON), '', 1, 3, 5, PT_MOON, 20000),
      array(array('galaxy' => 1, 'system' => 3, 'planet' => 5, 'type' => PT_MOON), '', 1, 3, 5, PT_MOON, 20000),
      array(array('pr_galaxy' => 1, 'pr_system' => 3, 'pr_planet' => 5, 'pr_planet_type' => PT_MOON), 'pr_', 1, 3, 5, PT_MOON, 40000),
      array(array('pr_galaxy' => 1, 'pr_system' => 3, 'pr_planet' => 5, 'pr_type' => PT_MOON), 'pr_', 1, 3, 5, PT_MOON, 40000),

      array(array('galaxy' => 2, 'system' => 3, 'planet' => 5, 'type' => PT_MOON), '', 2, 3, 5, PT_MOON, 2795),
      array(array('galaxy' => 2, 'system' => 4, 'planet' => 5, 'type' => PT_MOON), '', 2, 4, 5, PT_MOON, 1005),
      array(array('galaxy' => 2, 'system' => 4, 'planet' => 6, 'type' => PT_MOON), '', 2, 4, 6, PT_MOON, 5),
      array(array('galaxy' => 2, 'system' => 4, 'planet' => 6, 'type' => PT_PLANET), '', 2, 4, 6, PT_PLANET, 5),
    );
  }

  /**
   * @dataProvider dataConvertToVector
   *
   * @covers ::convertToVector
   *
   * @param array  $coordinates
   * @param string $prefix
   * @param        $g
   * @param        $s
   * @param        $p
   * @param        $t
   * @param        $d
   */
  public function testConvertToVector($coordinates, $prefix, $g, $s, $p, $t, $d) {
    $this->object = Vector::convertToVector($coordinates, $prefix);

    $this->assertEquals($g, getPrivateProperty('Vector', 'galaxy')->getValue($this->object));
    $this->assertEquals($s, getPrivateProperty('Vector', 'system')->getValue($this->object));
    $this->assertEquals($p, getPrivateProperty('Vector', 'planet')->getValue($this->object));
    $this->assertEquals($t, getPrivateProperty('Vector', 'type')->getValue($this->object));
  }

  /**
   * @dataProvider dataConvertToVector
   *
   * @covers ::distanceFromCoordinates
   *
   * @param Vector $vector
   * @param int    $distance
   */
  public function testDistanceFromCoordinates($coordinates, $prefix, $g, $s, $p, $t, $d) {
    $this->object->galaxy = 2;
    $this->object->system = 4;
    $this->object->planet = 6;
    $this->assertEquals($d, $this->object->distanceFromCoordinates($coordinates));
  }

}
