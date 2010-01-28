<?php
require_once dirname(__FILE__).'/../../../seeking_michigan/lib/search.php';
 
class SearchSetSearchStringTest extends PHPUnit_Framework_TestCase {
  public function testShouldGenerateSearchStringArray() {
    $params = array(
      'CISOROOT' => 'all',
      'CISOOP1' => 'any',
      'CISOFIELD1' => 'creato',
      'CISOBOX1' => 'michigan lake',
      'CISOOP2' => 'all',
      'CISOFIELD2' => 'title',
      'CISOBOX2' => 'general grant',
      'media-types' => array('image','audio','map','docs'),
      'search-button.x' => '61',
      'search-button.y' => '19',
      'search-button' => ''
    );
    $search = new Search();
    $search->set_search_string($params);
    
    $this->assertEquals('creato', $search->search_string[0]['field']);
  }
}
