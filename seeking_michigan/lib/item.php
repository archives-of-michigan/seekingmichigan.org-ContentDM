<?php

class Item {
  public $alias;
  public $itnum;
  public $index;
  public $title;
  public $file;

  private $_parent = NULL;
  private $_collection_path = NULL;

  function __construct($_alias, $_itnum, $parent_compound_object = NULL) {
    $this->alias = $_alias;
    $this->itnum = $_itnum;

    if($parent_compound_object) {
      $this->_parent = $parent_compound_object;
    }
  }

  public function parent_item() {
    if($this->_parent == NULL) {
      $parent_itnum = GetParent($this->alias, $requested_itnum, $this->collection_path);
      if($parent_itnum != -1) {
        $this->_parent = new CompoundObject($this->alias, $parent_itnum);
      }
    }
    return $this->_parent;
  }

  public function set_parent_item($parent) {
    $this->_parent = $parent;
  }

  public function collection_path() {
    if($this->_collection_path == NULL) {
      dmGetCollectionParameters($this->alias, $collection_name, $collection_path);
      $this->_collection_path = $collection_path;
    }
    return $this->_collection_path;
  }

  public function thumbnail_path() {
    return "/cgi-bin/thumbnail.exe?CISOROOT=".$this->alias."&amp;CISOPTR=".$this->itnum;
  }

  public function query_string() {
    if($this->is_child()) {
      $parent_alias = $this->parent_item()->alias;
      $parent_itnum = $this->parent_item()->itnum;
      return "CISOROOT=".$parent_alias."&amp;CISOPTR=".$parent_itnum."&amp;CISOSHOW=".$this->itnum;
    } else {
      return "CISOROOT=".$this->alias."&amp;CISOPTR=".$this->itnum;
    }
  }

  public static function extension($filename) {
    return end(explode('.',$filename));
  }
  public static function image_extensions() {
    return array('gif','jpg','tif','tiff','jp2');
  }

  public function file_type() {
    return Item::extension($this->file);
  }

  public function is_child() {
    if($this->parent_item() != -1 && $this->parent_item() != NULL) {
      return TRUE;
    } else {
      return FALSE;
    }
  }

  public function is_printable() {
    if($this->is_child()) {
      return $this->parent_item()->is_printable();
    } else if($this->file_type() == 'pdf') {
      return TRUE;
    } else {
      return FALSE;
    }
  }

  public function is_pdf() {
    $file = new SplFileInfo($this->pdf_path());
    return $file->isFile();
  }

  public function pdf_path() {
    return $this->collection_path()."/supp/".$this->itnum."/index.pdf";
  }

  public function print_link() {
    if ($this->is_pdf()) {
      return "/cgi-bin/showpdf.exe?CISOROOT=$this->alias&CISOPTR=$this->itnum";
    } else {
      return "print.php?CISOROOT=$this->alias&amp;CISOPTR=$this->itnum";
    }
  }

  public function download_link() {
    return "/cgi-bin/showfile.exe?CISOROOT=$this->alias&amp;CISOPTR=$this->itnum";
  }

  public static function from_xml($alias, $itnum, $xmlbuffer, $parent = NULL) {
    $xml = new SimpleXMLElement($xmlbuffer);
    $doc = ItemFactory::node($xml, '//xml');
    $item = new Item($alias, $itnum, $parent);
    $item->title = (string) $doc->title;
    $item->file = (string) $doc->find;

    return $item;
  }
}
