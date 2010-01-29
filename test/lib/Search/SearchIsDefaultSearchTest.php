<?php
require_once dirname(__FILE__).'/../../test_helper.php';
require_once dirname(__FILE__).'/../../../seeking_michigan/lib/search.php';

class SearchIsDefaultSearchTest extends PHPUnit_Framework_TestCase {
  public function testShouldReturnTrueIfDefaultSearch() {
    $search = Search::from_params(array('CISOROOT' => '/p1234'));
    $this->assertTrue($search->is_default_search());
  }

  public function testShouldReturnFalseIfSpecificSearch() {
    $search = Search::from_params(array('CISOROOT' => 'all', 's' => 'smokey'));
    $this->assertFalse($search->is_default_search());
  }
}
