<?php

class ItemFactory {
  public static function create($alias, $itnum, $subitnum) {
    dmGetItemInfo($alias, $itnum, $xmlbuffer);
    return ItemFactory::create_from_xml($alias, $itnum, $subitnum, $xmlbuffer);
  }

  public static function create_from_xml($alias, $itnum, $subitnum, $xmlbuffer) {
    $xml = new SimpleXMLElement($xmlbuffer);
    $format = (string) ItemFactory::node($xml, '//xml')->format;

    if($format == 'Document') {
      $compound_object = CompoundObject::from_xml($alias, $itnum, $xmlbuffer);
      if($subitnum != NULL) {
        return $compound_object->item_by_itnum($subitnum);
      } else {
        return $compound_object->first_item();
      }
    } else if($format == 'Image') {
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
