<?php
require_once dirname(__FILE__).'/../test_helper.php';
require_once dirname(__FILE__).'/../../seeking_michigan/lib/item.php';
require_once dirname(__FILE__).'/../../seeking_michigan/lib/image.php';
require_once dirname(__FILE__).'/../../seeking_michigan/lib/item_factory.php';

class ImageTest extends PHPUnit_Framework_TestCase {
  function setUp() {
    $this->xmlbuffer = "
<xml>
  <title>Adrian (Mich.)</title>
  <subjec>Adrian High School (Adrian, Mich.); schools</subjec>
  <descri>High School Building in Adrian (Mich.); c. 1920.</descri>
  <creato></creato>
  <date>c. 1920</date>
  <format>Image</format>
  <type>Postcard - Color</type>
  <identi>PH.10458; Town and City Scenes-Adrian-PF.2104</identi>
  <negati>21568</negati>
  <source>Part of the Souvenir Folder of Adrian (Mich.)</source>
  <rights>Use of this image requires the permission of the Archives of Michigan</rights>
  <order>To order please email archives@mi.gov</order>
  <featur></featur>
  <locati></locati>
  <oclc></oclc>
  <answer></answer>
  <ead></ead>
  <fullrs></fullrs>
  <find>1608.jp2</find>
  <dmaccess></dmaccess>
  <dmimage></dmimage>
  <dmcreated>2009-08-28</dmcreated>
  <dmmodified>2009-08-28</dmmodified>
  <dmoclcno></dmoclcno>
  <dmrecord>1607</dmrecord>
</xml>
    ";
    $this->image = Image::from_xml('p232323', '1608', $this->xmlbuffer);
  }

  #full_image_path
  public function testFullImagePathShouldReturnPathToFullResImage() {
    $this->image->_settings = array('width' => 640, 'height' => 480);

    $this->assertEquals(
      "/cgi-bin/getimage.exe?CISOROOT=p232323&amp;CISOPTR=1608&amp;DMWIDTH=640&amp;DMHEIGHT=480&amp;DMSCALE=100",
      $this->image->full_image_path());
  }

  #is_portrait
  public function testIsPortrait() {
    $this->assertEquals(FALSE, $this->image->is_portrait());
  }


  #from_xml
  public function testShouldLoadXml() {
    $image = Image::from_xml('p123456', '1607', $this->xmlbuffer);
    $this->assertEquals('p123456', $image->alias);
    $this->assertEquals('1607', $image->itnum);
    $this->assertEquals('Adrian (Mich.)', $image->title);
    $this->assertEquals('1608.jp2', $image->file);
  }

  public function testShouldLoadXmlWithParent() {
    $image = Image::from_xml('p123456', '1607', $this->xmlbuffer, 'something crazy');
    $this->assertEquals('something crazy', $image->parent_item());
  }
}
