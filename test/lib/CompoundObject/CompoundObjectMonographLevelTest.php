<?php
require_once dirname(__FILE__).'/../../test_helper.php';
require_once dirname(__FILE__).'/../../../seeking_michigan/lib/compound_object.php';

class CompoundObjectMonographLevelTest extends PHPUnit_Framework_TestCase {
  public function testShouldReturnZeroInBaseCase() {
    $obj = new CompoundObject('/p12233','1245');

    $level = $obj->monograph_level(array(0));
    $this->assertEquals('0', $level);
  }
  public function testShouldReturnSingle() {
    $obj = new CompoundObject('/p12233','1245');

    $level = $obj->monograph_level(array(1, 0));
    $this->assertEquals(1, $level);
  }
  public function testShouldReturnDouble() {
    $obj = new CompoundObject('/p12233','1245');

    $level = $obj->monograph_level(array(1, 2, 0));
    $this->assertEquals('1.2', $level);
  }
  public function testShouldReturnTriple() {
    $obj = new CompoundObject('/p12233','1245');

    $level = $obj->monograph_level(array(1, 2, 3, 0));
    $this->assertEquals('1.2.3', $level);
  }
  public function testShouldReturnQuadruple() {
    $obj = new CompoundObject('/p12233','1245');

    $level = $obj->monograph_level(array(1, 2, 3, 4, 0));
    $this->assertEquals('1.2.3.4', $level);
  }
  public function testShouldReturnQuintuple() {
    $obj = new CompoundObject('/p12233','1245');

    $level = $obj->monograph_level(array(1, 2, 3, 4, 5, 0));
    $this->assertEquals('1.2.3.4.5', $level);
  }
  public function testShouldReturnSextuple() {
    $obj = new CompoundObject('/p12233','1245');

    $level = $obj->monograph_level(array(1, 2, 3, 4, 5, 6, 0));
    $this->assertEquals('1.2.3.4.5.6', $level);
  }
  public function testShouldReturnSeptuple() {
    $obj = new CompoundObject('/p12233','1245');

    $level = $obj->monograph_level(array(1, 2, 3, 4, 5, 6, 7, 0));
    $this->assertEquals('1.2.3.4.5.6.7', $level);
  }
  public function testShouldReturnOctuple() {
    $obj = new CompoundObject('/p12233','1245');

    $level = $obj->monograph_level(array(1, 2, 3, 4, 5, 6, 7, 8, 0));
    $this->assertEquals('1.2.3.4.5.6.7.8', $level);
  }
  public function testShouldReturnNonuple() {
    $obj = new CompoundObject('/p12233','1245');

    $level = $obj->monograph_level(array(1, 2, 3, 4, 5, 6, 7, 8, 9, 0));
    $this->assertEquals('1.2.3.4.5.6.7.8.9', $level);
  }
}
