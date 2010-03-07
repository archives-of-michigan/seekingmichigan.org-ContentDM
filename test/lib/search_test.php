<?php
require_once dirname(__FILE__).'/../test_helper.php';
require_once dirname(__FILE__).'/../../seeking_michigan/lib/search.php';
require_once dirname(__FILE__).'/../../seeking_michigan/lib/item.php';
require_once dirname(__FILE__).'/../../seeking_michigan/lib/compound_object.php';
require_once dirname(__FILE__).'/../../seeking_michigan/lib/image.php';
require_once dirname(__FILE__).'/../../seeking_michigan/lib/item_factory.php';

class SearchTest extends PHPUnit_Framework_TestCase {
  #results
  public function testResultsShouldReturnListOfItems() {
    $search = new Search();
    $list = $search->results();
    $this->assertEquals(3, count($list));
    foreach($list as $item) {
      $this->assertEquals(
        'Item',
        get_class($item));
    }
  }
}
