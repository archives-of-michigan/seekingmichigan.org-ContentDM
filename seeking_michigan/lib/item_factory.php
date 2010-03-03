<?php

class ItemFactory {
  public static function create($alias, $itnum, $subitnum) {
    dmGetItemInfo($alias, $itnum, $xmlbuffer);
    return ItemFactory::create_from_xml($alias, $itnum, $subitnum, $xmlbuffer);
  }

  public static function create_from_xml($alias, $itnum, $subitnum, $xmlbuffer) {
    $xml = new SimpleXMLElement($xmlbuffer);
    $xmlnode = ItemFactory::node($xml, '//xml');
    $format = (string) $xmlnode->format;
    $extension = Item::extension((string) $xmlnode->find);  #some items have no format
    $is_image = array_search($extension, Item::image_extensions());

    if($format == 'Document' || $extension == 'cpd') {
      $compound_object = CompoundObject::from_xml($alias, $itnum, $xmlbuffer);
      if($subitnum != NULL) {
        return $compound_object->item_by_itnum($subitnum);
      } else {
        return $compound_object->first_item();
      }
    } else if($format == 'Image' || $is_image) {
      return Image::from_xml($alias, $itnum, $xmlbuffer);
    } else {
      return Item::from_xml($alias, $itnum, $xmlbuffer);
    }
  }

  public static function node($xml, $xpath) {
    $result = $xml->xpath($xpath);
    if($result) {
      return $result[0];
    }
    return NULL;
  }
}
