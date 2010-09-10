<?php
require_once dirname(__FILE__).'/../test_helper.php';
require_once dirname(__FILE__).'/../../docs/seeking_michigan/lib/item.php';
require_once dirname(__FILE__).'/../../docs/seeking_michigan/lib/compound_object.php';
require_once dirname(__FILE__).'/../../docs/seeking_michigan/lib/image.php';
require_once dirname(__FILE__).'/../../docs/seeking_michigan/lib/item_factory.php';

class CompoundObjectTest extends PHPUnit_Framework_TestCase {
  function setUp() {
    $this->co = new CompoundObject('abc123','3333');
    $this->co->items = array(new Item('abc123','3334'), new Item('abc123','3335'), new Item('abc123', '3336'));
  }

  # item_by_itnum
  public function testItemByItnumShouldFetchAnItem() {
    $item = $this->co->item_by_itnum('3335');
    $this->assertEquals('3335', $item->itnum);
  }
  public function testItemByItnumShouldReturnNull() {
    $item = $this->co->item_by_itnum('3338');
    $this->assertEquals(NULL, $item);
  }

  #first_item
  public function testFirstItemShouldReturnFirstItem() {
    $item = $this->co->first_item();
    $this->assertEquals('3334', $item->itnum);
  }
  public function testFirstItemShouldReturnNULL() {
    $this->co->items = array();
    $item = $this->co->first_item();
    $this->assertEquals(NULL, $item);
  }

  #previous_item
  public function testPreviousItemShouldReturnPreviousItem() {
    $this->assertEquals('3334', $this->co->previous_item('3335')->itnum);
  }
  public function testPreviousItemShouldReturnNullForFirstItem() {
    $this->assertEquals(NULL, $this->co->previous_item('3334')->itnum);
  }

  #next_item
  public function testNextItemShouldReturnNextItem() {
    $this->assertEquals('3336', $this->co->next_item('3335')->itnum);
  }
  public function testNextItemShouldReturnNullForLastItem() {
    $this->assertEquals(NULL, $this->co->next_item('3336')->itnum);
  }

  #num_items
  public function testNumItemsShouldReturnNumberOfItems() {
    $this->assertEquals(3,$this->co->num_items());
  }


  #from_xml
  public function testFromXml() {
    $buf = <<<XML
    <xml>
      <title>Adrian (Mich.)</title>
      <subjec>Adrian High School (Adrian, Mich.); schools</subjec>
      <descri>High School Building in Adrian (Mich.); c. 1920.</descri>
      <format>Document</format>
      <fullrs></fullrs>
      <find>9876.cpd</find>
      <dmaccess></dmaccess>
      <dmimage></dmimage>
      <dmcreated>2009-08-28</dmcreated>
      <dmmodified>2009-08-28</dmmodified>
      <dmoclcno></dmoclcno>
      <dmrecord>9876</dmrecord>
    </xml>
XML;
    $co = CompoundObject::from_xml('aaa111','9876',$buf);
    $this->assertEquals('aaa111',$co->alias);
    $this->assertEquals('9876',$co->itnum);
    $this->assertEquals(2,$co->num_items());
  }

  #browse_link
  public function testBrowseLinkWithoutSearch() {
    $this->assertEquals('compound_object_pages.php?CISOROOT=abc123&amp;CISOPTR=1607&amp;CISOSHOW=3333', 
      $this->co->browse_link(NULL));
  }
}
