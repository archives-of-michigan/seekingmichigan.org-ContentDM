<?php
require_once dirname(__FILE__).'/../test_helper.php';
require_once dirname(__FILE__).'/../../docs/seeking_michigan/lib/item.php';
require_once dirname(__FILE__).'/../../docs/seeking_michigan/lib/compound_object.php';
require_once dirname(__FILE__).'/../../docs/seeking_michigan/lib/item_factory.php';

class ItemTest extends PHPUnit_Framework_TestCase {
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
    $this->item = Item::from_xml('abc123', '3333', $this->xmlbuffer);
  }

  #thumbnail_path
  public function testThumbnailPathShouldReturnPathToThumbnail() {
    $this->assertEquals(
      "/cgi-bin/thumbnail.exe?CISOROOT=abc123&amp;CISOPTR=3333",
      $this->item->thumbnail_path());
  }

  #file_type
  public function testFileTypeShoudlReturnFileExtension() {
    $this->assertEquals('jp2', $this->item->file_type());
  }

  #is_child
  public function testIsChildShouldReturnTrueIfItemHasParent() {
    $this->item->set_parent_item(new Item('foo', 'bar'));
    $this->assertTrue($this->item->is_child());
  }
  public function testIsChildShouldReturnFalseIfItemHasNoParent() {
    $this->assertFalse($this->item->is_child());
  }

  #collection_path
  public function testCollectionPathShouldReturnCollectionPath() {
    $this->assertEquals('D:/foo/bar', $this->item->collection_path());
  }

  #query_string
  public function testQueryStringShouldReturnStringWithSubitemIfItemIsAChild() {
    $co = new CompoundObject('zzzz', '4444');
    $this->item->set_parent_item($co);
    $this->assertEquals('CISOROOT=zzzz&amp;CISOPTR=4444&amp;CISOSHOW=3333', $this->item->query_string());
  }
  public function testQueryStringShouldReturnStringIfItemIsNotAChild() {
    $this->assertEquals('CISOROOT=abc123&amp;CISOPTR=3333', $this->item->query_string());
  }

# #is_printable
# public function testIsPrintableShouldReturnTrueIfParentIsPrintable() {
#   $co = new CompoundObject('zzzz', '4444');
#   $co->file = "foobar.pdf";
#   $this->item->set_parent_item($co);
#   $this->assertTrue($this->item->is_printable());
# }
# public function testIsPrintableShouldReturnFalseIfParentIsNotPrintable() {
#   $co = new CompoundObject('zzzz', '4444');
#   $co->file = "foobar.dat";
#   $this->item->set_parent_item($co);
#   $this->assertFalse($this->item->is_printable());
# }
# public function testIsPrintableShouldReturnTrueIfPDF() {
#   $this->item->file = "foobar.pdf";
#   $this->assertTrue($this->item->is_printable());
# }
# public function testIsPrintableShouldReturnFalse() {
#   $this->item->file = "foobar.xml";
#   $this->assertFalse($this->item->is_printable());
# }

  #print_link
  public function testPrintLinkShouldReturnPDFLink() {
    $file = $this->getMock('SplFileInfo', array('isFile'), array(), '', false);
    $file->expects($this->once())->method('isFile')->will($this->returnValue(TRUE));
    $this->assertEquals(
      "/cgi-bin/showfile.exe?CISOROOT=abc123&amp;CISOPTR=3333&amp;CISOMODE=print",
      $this->item->print_link());
  }
  public function testPrintLinkShouldReturnURL() {
    $file = $this->getMock('SplFileInfo', array('isFile'), array(), '', false);
    $file->expects($this->once())->method('isFile')->will($this->returnValue(FALSE));
    $this->assertEquals(
      "print.php?CISOROOT=abc123&amp;CISOPTR=3333",
      $this->item->print_link());
  }

  #pdf_path
  public function testPDFPathShouldReturnPathToPDF() {
    $this->assertEquals(
      "D:/foo/bar/supp/3333/index.pdf",
      $this->item->pdf_path());
  }

  #from_xml
  public function testShouldLoadXml() {
    $item = Item::from_xml('p123456', '1608', $this->xmlbuffer);
    $this->assertEquals('p123456', $item->alias);
    $this->assertEquals('1608', $item->itnum);
    $this->assertEquals('Adrian (Mich.)', $item->title);
    $this->assertEquals('1608.jp2', $item->file);
  }

  public function testShouldLoadXmlWithParent() {
    $item = Item::from_xml('p123456', '1607', $this->xmlbuffer, 'something crazy');
    $this->assertEquals('something crazy', $item->parent_item());
  }

  #view_link
  public function testViewLinkForCompoundItem() {
    $this->assertEquals(
      "discover_item_viewer.php?CISOROOT=abc123&amp;CISOPTR=1607&amp;CISOSHOW=3333",
      $this->item->view_link());
  }
  public function testViewLinkForSingleItem() {
    $this->item->set_parent_item(FALSE);
    $this->assertEquals(
      "discover_item_viewer.php?CISOROOT=abc123&amp;CISOPTR=3333",
      $this->item->view_link());
  }
  public function testViewLinkWithSearch() {
    $this->assertEquals(
      "discover_item_viewer.php?CISOROOT=abc123&amp;CISOPTR=1607&amp;CISOSHOW=3333&amp;search=search_params&amp;search_position=3",
      $this->item->view_link('search_params', 3));
  }
}
