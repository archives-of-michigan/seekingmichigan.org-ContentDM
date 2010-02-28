<?php
require_once dirname(__FILE__).'/../../test_helper.php';
require_once dirname(__FILE__).'/../../../seeking_michigan/lib/compound_object.php';

class CompoundObjectItems extends PHPUnit_Framework_TestCase {
  public function testShouldGetListOfItems() {
    $obj = new CompoundObject('/p12233','1245');

    $items = $obj->items();
    assertEquals(5, count($items));
  }
}
