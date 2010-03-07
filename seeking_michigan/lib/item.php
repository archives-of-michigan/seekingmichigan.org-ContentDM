<?php

class Item {
  public $alias;
  public $itnum;
  public $index;
  public $title;
  public $file;
  public $description;
  public $subject;
  public $creator;
  public $type;
  public $date;
  public $format;

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
    if($this->_parent === NULL) {
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

  public function parent_itnum() {
    var_dump($this->parent_item());
    if($this->is_child()) {
      return $this->parent_item()->itnum;
    } else {
      return NULL;
    }
  }

  public function alt_tile() {
    $text = $this->title;
    if(strlen($text) > 100){
      $text = truncate($text,100);
    }
    return str_replace("<br />","\n", str_replace("'","&#39;",str_replace("\"","&#34;",charReplace($text))));
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
      return "CISOROOT=".$parent_alias."&amp;CISOPTR=".$this->parent_itnum()."&amp;CISOSHOW=".$this->itnum;
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

  public function view_link($search_url = NULL, $search_position = NULL) {
    $path = '';
    if($this->is_child()) {
      $parent_itnum = $this->parent_itnum();
      $path = "discover_item_viewer.php?CISOROOT=$this->alias&amp;CISOPTR=$parent_itnum&amp;CISOSHOW=$this->itnum";
    } else {
      $path = "discover_item_viewer.php?CISOROOT=$this->alias&amp;CISOPTR=$this->itnum";
    }

    if($search_url) {
      $search_url = urlencode($search_url);
      $path = "$path&amp;search=$search_url&amp;search_position=$search_position";
    }

    return $path;
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
    $item->description = (string) $doc->descri;
    $item->subject = (string) $doc->subjec;
    $item->creator = (string) $doc->creato;
    $item->type = (string) $doc->type;
    $item->date = (string) $doc->date;
    $item->format = (string) $doc->format;

    return $item;
  }

  public static function from_search($result) {
    $item = new Item($result['collection'], $result["pointer"]);
    $item->title = $result['title'];
    $item->subject = $result['subjec'];
    $item->description = $result['descri'];
    $item->creator = $result['creato'];
    $item->date = $result['date'];
    $item->type = $result['type'];
    $item->format = $result['format'];

    return $item;
  }
}
