<?php
require_once dirname(__FILE__).'/../../test_helper.php';
require_once dirname(__FILE__).'/../../../seeking_michigan/lib/search.php';

class SearchFromParamsTest extends PHPUnit_Framework_TestCase {
  public function testShouldSetAllAliases() {
    $get = array(
      "CISOOP1" => "any",
      "CISOFIELD1" => "CISOSEARCHALL",
      "CISOROOT" => "all",
      "CISOBOX1" => "michigan",
      "media-types" => array("image"),
      "search-button_x" => "31",
      "search-button_y" => "11",
      "search-button" => " "
    );
    $search = Search::from_params($get);
    $this->assertEquals('/p4006coll2',$search->search_alias[0]);
    $this->assertEquals('/p4006coll3',$search->search_alias[1]);
  }
  
  public function testShouldSetSpecificAlias() {
    $params = array(
      'CISOROOT' => '/p4006coll2'
    );
    $search = Search::from_params($params);
    
    $this->assertEquals('/p4006coll2',$search->search_alias[0]);
    $this->assertEquals(1,count($search->search_alias));
  }
  
  public function testShouldSetMapAlias() {
    $params = array(
      'CISOROOT' => 'all',
      'document-types' => array('map')
    );
    $search = Search::from_params($params);
    
    $this->assertEquals(1,count($search->search_alias));
    $this->assertEquals('/p129401coll3',$search->search_alias[0]);
  }
  
  public function testShouldSetSortby() {
    $params = array(
      'CISOROOT' => 'all',
      'document-types' => array('map')
    );
    $search = Search::from_params($params);
    
    $this->assertEquals(array('title'),$search->sortby);
  }

  public function testShouldParseSimpleSearch() {
    $params = array(
      's' => 'Lansing mayor',
    );
    $search = Search::from_params($params);
    
    $this->assertEquals(13,count($search->search_alias));
    $this->assertEquals('Lansing mayor', $search->search_string[0]['string']);
    $this->assertEquals('CISOSEARCHALL', $search->search_string[0]['field']);
    $this->assertEquals('any', $search->search_string[0]['mode']);
  }
}
