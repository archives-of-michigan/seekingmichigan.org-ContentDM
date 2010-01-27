<?php
require_once dirname(__FILE__).'/../../test_helper.php';
require_once dirname(__FILE__).'/../../../seeking_michigan/lib/search.php';

class SearchComplexifySimpleSearchParamsTest extends PHPUnit_Framework_TestCase {
  public function testShouldConvertStringToParams() {
    $get = array('s' => "John's suitcase");

    Search::complexify_simple_search_params($get);

    $this->assertEquals('any', $get['CISOROOT']);
    $this->assertEquals('CISOSEARCHALL', $get['CISOFIELD1']);
    $this->assertEquals("John's suitcase", $get['CISOBOX1']);
    $this->assertEquals('any', $get['CISOOP1']);
  }

  public function testShouldConvertStringWithQuotedPhrase() {
    $get = array('s' => "lansing \"streetcar station\"");

    Search::complexify_simple_search_params($get);

    $this->assertEquals('any', $get['CISOROOT']);

    $this->assertEquals('CISOSEARCHALL', $get['CISOFIELD1']);
    $this->assertEquals("lansing", $get['CISOBOX1']);
    $this->assertEquals('any', $get['CISOOP1']);

    $this->assertEquals('CISOSEARCHALL', $get['CISOFIELD2']);
    $this->assertEquals("streetcar station", $get['CISOBOX2']);
    $this->assertEquals('exact', $get['CISOOP2']);
  }

  public function testShouldConvertStringWithMultipleQuotedPhrases() {
    $get = array('s' => "computer virus \"apple mac\" \"dell pc\" vulnerability");

    Search::complexify_simple_search_params($get);

    $this->assertEquals('any', $get['CISOROOT']);

    $this->assertEquals('CISOSEARCHALL', $get['CISOFIELD1']);
    $this->assertEquals("computer virus vulnerability", $get['CISOBOX1']);
    $this->assertEquals('any', $get['CISOOP1']);

    $this->assertEquals('CISOSEARCHALL', $get['CISOFIELD2']);
    $this->assertEquals("apple mac", $get['CISOBOX2']);
    $this->assertEquals('exact', $get['CISOOP2']);

    $this->assertEquals('CISOSEARCHALL', $get['CISOFIELD3']);
    $this->assertEquals("dell pc", $get['CISOBOX3']);
    $this->assertEquals('exact', $get['CISOOP3']);
  }

  public function testShouldNotConvertAComplexQuery() {
    $get = array('CISOFIELD1' => 'CISOSEARCHALL', 'CISOBOX1' => 'gabba hey');

    Search::complexify_simple_search_params($get);

    $this->assertEquals('any', $get['CISOROOT']);
    $this->assertEquals('CISOSEARCHALL', $get['CISOFIELD1']);
    $this->assertEquals("gabba hey", $get['CISOBOX1']);
  }
}
