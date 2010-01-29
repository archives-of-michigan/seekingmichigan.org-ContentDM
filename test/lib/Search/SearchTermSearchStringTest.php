<?php
require_once dirname(__FILE__).'/../../../seeking_michigan/lib/search.php';
 
class SearchTermSearchStringTest extends PHPUnit_Framework_TestCase {
  public function setUp() {
    $this->search = new Search();
  }

  public function testShouldReturnSearchStringForTerm() {
    $search->search_all = TRUE;
    $this->assertEquals(
      'CISOROOT=all&amp;CISOOP1=any&amp;CISOFIELD1=CISOSEARCHALL&amp;CISOBOX1=michigan', 
      $this->search->term_search_string('michigan'));
  }
}
