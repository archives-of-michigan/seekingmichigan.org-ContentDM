<?php
require_once dirname(__FILE__).'/../../../seeking_michigan/lib/search.php';

class SearchFromParamsTest extends PHPUnit_Framework_TestCase {
  public function testShouldParseSearchString() {
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
  }
}