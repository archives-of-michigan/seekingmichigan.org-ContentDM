<?php

class CompoundObject extends Item {
  public $items = array();

  function __construct($_alias, $_itnum) {
    $this->alias = $_alias;
    $this->itnum = $_itnum;
  }

  public function item_by_itnum($itnum) {
    return $this->item_by_position(
      $this->item_position($itnum)
    );
  }

  public function item_by_position($index) {
    return $this->items[$index];
  }

  public function item_position($itnum) {
    foreach($this->items as $index => $item) {
      if($item->itnum == $itnum) {
        return $index;
      }
    }
    return NULL;
  }

  public function first_item() {
    return $this->items[0];
  }

  public function num_items() {
    return count($this->items);
  }

  public function previous_item($itnum) {
    $previous_position = $this->item_position($itnum) - 1;
    return $this->item_by_position($previous_position);
  }

  public function next_item($itnum) {
    $next_position = $this->item_position($itnum) + 1;
    return $this->item_by_position($next_position);
  }

  public function add_item($item) {
    $item->set_parent_item($compound_object);
    $this->items[] = $item;
  }

  public static function from_xml($alias, $itnum, $xmlbuffer) {
    $xml = new SimpleXMLElement($xmlbuffer);
    $doc = ItemFactory::node($xml, '//xml');

    $compound_object = new CompoundObject($alias, $itnum);
    $compound_object->title = (string) $doc->title;
    $compound_object->file = (string) $doc->find;

    dmGetCompoundObjectInfo($alias, $itnum, $compound_xml_buffer);
    $compound_xml = new SimpleXMLElement($compound_xml_buffer);

    $pages = $compound_xml->xpath('//cpd/page');
    foreach($pages as $page) {
      $subitnum = (string) $page->pageptr;
      $item = ItemFactory::create($alias, $subitnum, NULL); 
      $compound_object->add_item($item);
    }

    return($compound_object);
  }
}
