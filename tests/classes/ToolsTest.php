<?php

/**
 * Created by Gorlum 01.03.2017 14:03
 */

/**
 * Class ToolsTest
 *
 * @coversDefaultClass Tools
 */
class ToolsTest extends PHPUnit_Framework_TestCase {
  public function setUp() {
    parent::setUp();
  }

  public function tearDown() {
    parent::tearDown();
  }

  public function dataFillPercentStyle() {
    return array(
      array(100, 101, 'error'),
      array(100, 100, 'warning'),
      array(100, 99, 'warning'),
      array(100, 91, 'warning'),
      array(100, 90, 'notice'),
      array(100, 89, 'notice'),
      array(100, 76, 'notice'),
      array(100, 75, 'info'),
      array(100, 74, 'info'),
      array(100, 51, 'info'),
      array(100, 50, 'ok'),
      array(100, 49, 'ok'),
      array(100, 0, 'ok'),

      array(0, 1, 'error'),
      array(0, 0, 'zero_number'),
      array(0, -1, 'zero_number'),
    );
  }

  /**
   * Test for style select in fillPercentStyle
   *
   * @covers ::fillPercentStyle
   * @dataProvider dataFillPercentStyle
   */
  public function testFillPercentStyle($number, $sample, $expected) {
    $this->assertEquals($expected, Tools::fillPercentStyle($number, $sample));
  }

  /**
   * Test for span-wrapping of fillPercentStyle
   *
   * @covers ::numberPercentSpan
   */
  public function testNumberStyleSpan() {
    $this->assertEquals("<span class=\"ok\">100</span>", Tools::numberPercentSpan(100, 10));
  }

}
