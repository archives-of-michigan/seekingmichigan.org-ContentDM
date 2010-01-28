<?php
require_once dirname(__FILE__).'/../../../seeking_michigan/lib/search.php';
 
class SearchTermsTest extends PHPUnit_Framework_TestCase {
  public function setUp() {
    $this->search = new Search();
  }

  public function testShouldReturnEmptyArrayIfSearchStringIsEmpty() {
    $this->assertEquals(0, sizeof($this->search->terms()));
  }
  
  public function testShouldReturnListOfKeywords() {
    $params = array(
      'CISOROOT' => 'all',
      'CISOOP1' => 'any',
      'CISOFIELD1' => 'CISOSEARCHALL',
      'CISOBOX1' => 'michigan lake',
      'CISOOP2' => 'all',
      'CISOFIELD2' => 'title',
      'CISOBOX2' => 'general grant',
      'media-types' => array('image','audio','map','docs'),
      'search-button.x' => '61',
      'search-button.y' => '19',
      'search-button' => ''
    );
    $this->search->set_search_string($params);
    
    $terms = $this->search->terms();
    sort($terms);
    $term_string = join(' ', $terms);
    $this->assertEquals('general grant lake michigan', $term_string);
  }
}
