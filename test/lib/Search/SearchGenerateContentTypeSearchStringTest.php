<?php
require_once dirname(__FILE__).'/../../../seeking_michigan/lib/search.php';
 
class SearchGenerateContentTypeSearchStringTest extends PHPUnit_Framework_TestCase {
  public function setUp() {
    $this->params = array(
      'media-types' => array('image','audio','video','docs')
    );
  }
  
  public function testShouldCreateSearchTerms() {
    $string = Search::generate_content_type_search_string($this->params);
    $this->assertEquals('format',$string[0]['field']);
    $this->assertEquals('image audio video Document ',$string[0]['string']);
    $this->assertEquals('all',$string[0]['mode']);
  }
}