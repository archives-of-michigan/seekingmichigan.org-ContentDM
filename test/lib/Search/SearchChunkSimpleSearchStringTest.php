<?php
require_once dirname(__FILE__).'/../../test_helper.php';
require_once dirname(__FILE__).'/../../../seeking_michigan/lib/search.php';

class SearchChunkSimpleSearchString extends PHPUnit_Framework_TestCase {
  public function testShouldChunkNonQuotedString() {
    $array = Search::chunk_simple_search_string("Hello I am Error");
    $this->assertEquals(1, count($array['any']));
    $this->assertEquals(0, count($array['exact']));
    $this->assertEquals('Hello I am Error', $array['any'][0]);
  }

  public function testShouldChunkQuotedString() {
    $array = Search::chunk_simple_search_string("Hello \"I am Error\"");
    $this->assertEquals(1, count($array['any']));
    $this->assertEquals(1, count($array['exact']));
    $this->assertEquals('Hello', $array['any'][0]);
    $this->assertEquals('I am Error', $array['exact'][0]);
  }

  public function testShouldChunkStringWithMultipleQuotes() {
    $array = Search::chunk_simple_search_string("\"Hello I\" am \"not Error\"");
    $this->assertEquals(1, count($array['any']));
    $this->assertEquals(2, count($array['exact']));
    $this->assertEquals('am', $array['any'][0]);
    $this->assertEquals('Hello I', $array['exact'][0]);
    $this->assertEquals('not Error', $array['exact'][1]);
  }

  public function testShouldChunkStringWithQuotesAndNoAnys() {
    $array = Search::chunk_simple_search_string("\"Hello I am Error\"");
    $this->assertEquals(0, count($array['any']));
    $this->assertEquals(1, count($array['exact']));
    $this->assertEquals('Hello I am Error', $array['exact'][0]);
  }
}
