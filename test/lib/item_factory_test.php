<?php
require_once dirname(__FILE__).'/../test_helper.php';
require_once dirname(__FILE__).'/../../seeking_michigan/lib/item.php';
require_once dirname(__FILE__).'/../../seeking_michigan/lib/image.php';
require_once dirname(__FILE__).'/../../seeking_michigan/lib/compound_object.php';
require_once dirname(__FILE__).'/../../seeking_michigan/lib/item_factory.php';

class ItemFactoryTest extends PHPUnit_Framework_TestCase {
  #create_from_xml
  public function testCreateFromXmlShouldCreateImage() {
    $xmlbuffer = "
<xml>
  <title>Adrian (Mich.)</title>
  <subjec>Adrian High School (Adrian, Mich.); schools</subjec>
  <descri>High School Building in Adrian (Mich.); c. 1920.</descri>
  <format>Image</format>
  <find>1608.jp2</find>
</xml>
    ";

    $image = ItemFactory::create_from_xml('p1234','1608',NULL,$xmlbuffer);
    $this->assertEquals('Image',get_class($image));
  }

  public function testCreateFromXmlShouldCreateItem() {
    $xmlbuffer = "
<xml>
  <title>Adrian (Mich.)</title>
  <subjec>Adrian High School (Adrian, Mich.); schools</subjec>
  <descri>High School Building in Adrian (Mich.); c. 1920.</descri>
  <format>PDF</format>
  <find>1608.pdf</find>
</xml>
    ";

    $item = ItemFactory::create_from_xml('p1234','1608',NULL,$xmlbuffer);
    $this->assertEquals('Item',get_class($item));
  }

  public function testCreateFromXmlShouldCreateCompoundObject() {
    $xmlbuffer = "
<xml>
  <title>Adrian (Mich.)</title>
  <subjec>Adrian High School (Adrian, Mich.); schools</subjec>
  <descri>High School Building in Adrian (Mich.); c. 1920.</descri>
  <format>Document</format>
  <find>1608.cpd</find>
</xml>
    ";

    $item = ItemFactory::create_from_xml('p1234','1608',NULL,$xmlbuffer);
    $this->assertEquals('Image', get_class($item)); //image fetched from stubbed getItemInfo
  }
}
