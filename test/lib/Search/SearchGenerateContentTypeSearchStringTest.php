<?php
require_once dirname(__FILE__).'/../../../seeking_michigan/lib/search.php';
 
class SearchGenerateContentTypeSearchStringTest extends PHPUnit_Framework_TestCase {
  public function testShouldCreateSearchTerms() {
    $search = new Search();
    $search->media_types = array('image','audio','video','docs');
    $search->generate_content_type_search_string();
    $this->assertEquals('format',$search->search_string[0]['field']);
    $this->assertEquals('image audio video Document ',$search->search_string[0]['string']);
    $this->assertEquals('all',$search->search_string[0]['mode']);
  }
}
