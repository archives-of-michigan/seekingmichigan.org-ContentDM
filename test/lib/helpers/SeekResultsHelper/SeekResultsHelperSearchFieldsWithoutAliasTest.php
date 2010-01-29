<?php
require dirname(__FILE__).'/../../../test_helper.php';
require dirname(__FILE__).'/../../../../seeking_michigan/lib/search.php';
require dirname(__FILE__).'/../../../../seeking_michigan/lib/helpers/seek_results.php';

class SeekResultsHelperSearchFieldsWithoutAliasTest extends PHPUnit_Framework_TestCase {
  public function setUp() {
    $this->helper = new SeekResultsHelper();
  }

  public function testSholdReturnListOfFieldsSansCISOROOT() {
    $search = Search::from_params(array('s' => 'crumbly apple cobbler'));

    $results = $this->helper->search_fields_without_alias($search);
    $this->assertEquals(array(
      'CISOBOX1' => 'crumbly apple cobbler',
      'CISOOP1' => 'any',
      'CISOFIELD1' => 'CISOSEARCHALL',
      'CISOSTART' => '1,1'
    ), $results);
  }
}
