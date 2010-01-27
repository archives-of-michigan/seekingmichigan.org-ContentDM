<?php
require_once dirname(__FILE__).'/../../test_helper.php';
require_once dirname(__FILE__).'/../../../seeking_michigan/lib/search.php';

class SearchDefaultForTest extends PHPUnit_Framework_TestCase {
  public function testShouldSetDefaultValueForUnsetKey() {
    $array = array('foo' => 'bar');
    Search::default_for($array, 'Missouri', 'Kansas City');
    $this->assertEquals('Kansas City', $array['Missouri']);
  }

  public function testShouldSetDefaultValueForBlankValue() {
    $array = array('foo' => 'bar', 'Missouri' => '');
    Search::default_for($array, 'Missouri', 'Kansas City');
    $this->assertEquals('Kansas City', $array['Missouri']);
  }

  public function testShouldNotSetDefaultValueForExisting() {
    $array = array('foo' => 'bar', 'Missouri' => 'Kansas City');
    Search::default_for($array, 'Missouri', 'Joplin');
    $this->assertEquals('Kansas City', $array['Missouri']);
  }
}
