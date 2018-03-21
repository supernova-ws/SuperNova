<?php
/**
 * Created by Gorlum 01.10.2017 12:56
 */

namespace Meta\Economic;

use Core\GlobalContainer;
use \classConfig;

/**
 * Class EcoHelperTest
 * @coversDefaultClass \Meta\Economic\EconomicHelper
 *
 * @package Meta\Economic
 *
 *
 * Testing cost calculations
 */
class EcoHelperTest extends \PHPUnit_Framework_TestCase {

  /**
   * @var EconomicHelper $object
   */
  protected $object;

  /**
   * @var classConfig $config
   */
  protected $config;

  /**
   * @var GlobalContainer $gc
   */
  protected $gc;

  public function setUp() {
    parent::setUp();

    $this->config = $this->createMock(classConfig::class);
    $this->config->method('__get')
      ->will($this->returnValueMap(
        [
          ['rpg_exchange_metal', 1],
          ['rpg_exchange_crystal', 2],
          ['rpg_exchange_deuterium', 4],
          ['rpg_exchange_darkMatter', 4000],
        ]
      ));

    $this->gc = $this->createMock(GlobalContainer::class);
    $this->gc->method('__get')
      ->willReturn($this->config);

    $this->object = new EconomicHelper($this->gc);
  }

  public function tearDown() {
    parent::tearDown();

    unset($this->object);
    unset($this->config);
    unset($this->gc);
  }

  /**
   * @covers ::__construct
   */
  public function test___construct() {
    $this->object = new EconomicHelper($this->gc);
  }

  /**
   * @covers ::getResourcesExchange
   * @covers ::resetResourcesExchange
   */
  public function test_getResourcesExchange() {
    // First call
    $this->assertEquals([
      RES_METAL => 1,
      RES_CRYSTAL => 2,
      RES_DEUTERIUM => 4,
      RES_DARK_MATTER => 4000,
    ], $this->object->getResourcesExchange());

    // Replacing internal $config property with new mock
    $this->config = $this->createMock(classConfig::class);
    $this->config->method('__get')
      ->will($this->returnValue(0
      ));
    setProtectedProperty($this->object, 'config', $this->config);

    // Without reset 2nd should return cached values
    $this->assertEquals([
      RES_METAL => 1,
      RES_CRYSTAL => 2,
      RES_DEUTERIUM => 4,
      RES_DARK_MATTER => 4000,
    ], $this->object->getResourcesExchange());

    // Resetting internal cache
    $this->object->resetResourcesExchange();

    // Now $config should return 0 for all rates and Helper would replace them with 1-s
    $this->assertEquals([
      RES_METAL => 1,
      RES_CRYSTAL => 1,
      RES_DEUTERIUM => 1,
      RES_DARK_MATTER => 1,
    ], $this->object->getResourcesExchange());
  }

  /**
   * @covers ::getResourceExchangeIn
   */
  public function test_getResourceExchangeIn() {
    $this->assertEquals([
      RES_METAL => 0.25,
      RES_CRYSTAL => 0.5,
      RES_DEUTERIUM => 1,
      RES_DARK_MATTER => 1000,
    ], $this->object->getResourceExchangeIn(RES_DEUTERIUM));

    $this->assertEquals([
      RES_METAL => 1,
      RES_CRYSTAL => 2,
      RES_DEUTERIUM => 4,
      RES_DARK_MATTER => 4000,
    ], $this->object->getResourceExchangeIn(RES_METAL));
  }

}
