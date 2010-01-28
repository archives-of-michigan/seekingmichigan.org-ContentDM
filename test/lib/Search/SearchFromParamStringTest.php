<?php
require_once dirname(__FILE__).'/../../test_helper.php';
require_once dirname(__FILE__).'/../../../seeking_michigan/lib/search.php';

class SearchFromParamStringTest extends PHPUnit_Framework_TestCase {
  public function testShouldParseSearchString() {
    $search = Search::from_param_string("CISOOP1%3Dany%26CISOFIELD1%3DCISOSEARCHALL%26CISOROOT%3Dall%26CISOBOX1%3Dmichigan%26media-types%255B%255D%3Dimage%26search-button.x%3D31%26search-button.y%3D11%26search-button%3D%2B");
    
    $this->assertEquals(13, count($search->search_alias));
    $this->assertEquals('/p4006coll2',$search->search_alias[0]);
  }

  public function testSHouldParseSimpleSearchString() {
    $search = Search::from_param_string("s%3Di+seek+\"gold+ingots\"%26media-types%255B%255D%3Dimage%26");
    
    $this->assertEquals(13, count($search->search_alias));
    $this->assertEquals('/p4006coll2',$search->search_alias[0]);
  }
}
