<?php

class Image extends Item {
  public $_settings;

  public static function from_xml($alias, $itnum, $xmlbuffer, $parent = NULL) {
    $xml = new SimpleXMLElement($xmlbuffer);
    $doc = ItemFactory::node($xml, '//xml');
    $image = new Image($alias, $itnum, $parent);
    $image->title = (string) $doc->title;
    $image->file = (string) $doc->find;

    return $image;
  }

  private function load_settings() {
    if($this->_settings == NULL) {
      dmGetCollectionImageSettings($this->alias, $pan_enabled, $minjpegdim, $zoomlevels, $maxderivedimg, $viewer, $docviewer, $compareviewer, $slideshowviewer);
      dmGetImageInfo($this->alias, $this->itnum, $this->file, $type, $width, $height);
      $this->_settings = array('pan_enabled' => $pan_enabled, 'minjpegdim' => $minjpegdim, 
        'zoomlevels' => $zoomlevels, 'maxderivedimg' => $maxderivedimg, 'viewer' => $viewer, 
        'docviewer' => $docviewer, 'compareviewer' => $compareviewer, 'slideshowviewer' => $slideshowviewer,
        'width' => $width, 'height' => $height);
    }
  }

  public function width() {
    $this->load_settings();
    return $this->_settings['width'];
  }
  public function height() {
    $this->load_settings();
    return $this->_settings['height'];
  }
  public function pan_enabled() {
    $this->load_settings();
    return $this->_settings['pan_enabled'];
  }

  public function is_printable() {
    return TRUE;
  }

  public function full_image_path() {
    $str = "/cgi-bin/getimage.exe?CISOROOT=".$this->alias;
    $str = $str."&amp;CISOPTR=".$this->itnum;
    $str = $str."&amp;DMWIDTH=".$this->width();
    $str = $str."&amp;DMHEIGHT=".$this->height();
    $str = $str."&amp;DMSCALE=100";
    return $str;
  }
}
