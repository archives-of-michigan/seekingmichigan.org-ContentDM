<?php

class Item {
  public $alias;
  public $itnum;
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
  private $_xml = NULL;
  private $_position;
 
# public static
 
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
    $item->set_xml($xmlbuffer);

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

  public static function extension($filename) {
    return end(explode('.',$filename));
  }
  public static function image_extensions() {
    return array('gif','jpg','tif','tiff','jp2');
  }


# constructors

  function __construct($_alias, $_itnum, $parent_compound_object = NULL) {
    $this->alias = $_alias;
    $this->itnum = $_itnum;

    if($parent_compound_object) {
      $this->_parent = $parent_compound_object;
    }
  }


# public instance
 
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
    if($this->is_child()) {
      return $this->parent_item()->itnum;
    } else {
      return NULL;
    }
  }

  public function set_xml($val) {
    $this->_xml = $val;
  }
  public function xml() {
    $xml = $this->_xml;
    if($xml == NULL) {
      dmGetItemInfo($this->alias, $this->itnum, $xml);
      $this->_xml = $xml;
    }

    return $xml;
  }

  public function title() {
    if($this->is_child()) {
      $title = $this->parent_item()->title();
      return $title.' &mdash; '.$this->title;
    } else {
      return $this->title;
    }
  }

  public function position() {
    $this->_position ? $this->_position : NULL;
  }
  public function set_position($num) {
    $this->_position = $num;
  }

  public function alt_title() {
    $text = $this->title;
    if(strlen($text) > 100){
      $text = $this->truncate($text,100);
    }
    return str_replace("<br />","\n", str_replace("'","&#39;",str_replace("\"","&#34;",$this->charReplace($text))));
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
    } else if($this->is_pdf()) {
      return TRUE;
    } else {
      return FALSE;
    }
  }

  public function is_audio() {
    return ($this->file_type() == 'mp3');
  }

  public function is_pdf() {
    $file = new SplFileInfo($this->pdf_path());
    return ($file->isFile() || $this->file_type() == 'pdf' || $this->is_pdf_page());
  }

  public function is_pdf_page() {
    return ($this->file_type() == 'pdfpage');
  }

  public function pdf_path() {
    return $this->collection_path()."/supp/".$this->itnum."/index.pdf";
  }

  public function print_link() {
    if ($this->is_pdf()) {
      return "/cgi-bin/showfile.exe?CISOROOT=$this->alias&amp;CISOPTR=$this->itnum&amp;CISOMODE=print";
    } else {
      return "print.php?CISOROOT=$this->alias&amp;CISOPTR=$this->itnum";
    }
  }

  public function view_link($search_url, $search_position) {
    return "discover_item_viewer.php".$this->query_params($search_url, $search_position);
  }

  protected function query_params($search_url, $search_position) {
    $params = '';
    if($this->is_child()) {
      $parent_itnum = $this->parent_itnum();
      $params = "?CISOROOT=$this->alias";
      $params .= "&amp;CISOPTR=$parent_itnum&amp;CISOSHOW=$this->itnum";
    } else {
      $params = "?CISOROOT=$this->alias&amp;CISOPTR=$this->itnum";
    }

    if($search_url) {
      $search_url = preg_replace('/&CISOSTART=\d+/','',$search_url);
      $search_url .= "&CISOSTART=$search_position";
      $search_url = urlencode($search_url);

      $params .= "&amp;search=$search_url";
    }

    return $params;
  }

  public function search_view_link($search_status = NULL) {
    if($search_status) {
      return $this->view_link($search_status->search_params,
                              $search_status->search_position);
    } else {
      return $this->view_link(NULL, NULL);
    }
  }

  public function download_link() {
    return "/cgi-bin/showfile.exe?CISOROOT=$this->alias&amp;CISOPTR=$this->itnum";
  }

  public function metadata() {
    $confs = &dmGetCollectionFieldInfo($this->alias);
    $std_fields = array();

    $parser = xml_parser_create();
    xml_parse_into_struct($parser, $this->xml(), $structure, $index);
    xml_parser_free($parser);
 
    foreach($confs as $conf) {
      $tag = strtoupper($conf["nick"]);
      if($conf["type"] != "FTS" && array_key_exists($tag,$index) && 
          array_key_exists("value",$structure[$index[$tag][0]]) &&
          $conf['hide'] != 1) {
        $value = '';
        if($conf["type"] == "DATE") {
          $value = $this->linkDate($structure[$index[$tag][0]]["value"],
                            $this->alias, $conf["nick"]);
        } else {
          if(($conf["search"] == "1") && ($conf["vocab"] == "1")) {
            $value = $this->vocabLink(
              $this->charReplace($structure[$index[$tag][0]]["value"]), 
                                 $this->alias, $conf["nick"]);
          } elseif(($conf["search"] == "1") && ($conf["vocab"] == "0")) {
            $value = $this->makeLinks(
              $this->isHyperlink(
                $this->charReplace($structure[$index[$tag][0]]["value"]),
                                   $conf["type"],$conf["nick"], $this->alias));
          } else {
            $value = $this->makeLinks(
              $this->charReplace($structure[$index[$tag][0]]["value"]));
          }
        }
        $std_fields[$conf['name']] = $value;
      }
    }

    return $std_fields;
  }

#private instance
  
  private function linkDate($date,$alias,$field){
    $str = $this->formatDate($date);
    $fieldType = (S_HYPERLINK_CISOFIELD == "0")?"CISOSEARCHALL":$field;
    $aliasType = (S_HYPERLINK_CISOROOT == "0")?"all":$alias;
    $str = '<a href="seek_results.php?CISOOP1=any&amp;CISOFIELD1='.$fieldType.'&amp;CISOROOT='.$aliasType.'&amp;CISOBOX1='.str_replace('-','',$date).'" target="_top">'.$str.'</a>';
    return $str;
  }

  private function charReplace($text){
    $bchars = array('“','”','’','â€','«','»',' ');
    $gchars = array('"','"','\'','','','','&nbsp;');
    $n = count($gchars);
    for($i=0;$i<$n;$i++){
      $text = str_replace($bchars[$i],$gchars[$i],$text);
    }   
    $text = str_replace('à','//acute//',$text);     
    $text = preg_replace('/\s+/', ' ', $text);    
    $text = preg_replace("/(<br>|<BR>|<br \/>|<BR \/>)/"," //br// ",$text)." ";
    $text = preg_replace("/(<|>)/"," ",$text);
    $text = str_replace(' //br// ','<br />',str_replace('//acute//','à',$text));
    return $text;
  }

  private function isHyperlink($text, $type, $field, $alias){
    $isFts = ($type == "FTS") ? true : false;
    if($isFts){
      switch(S_FTS_DISPLAY){
        case "1": return $this->hyperLink($text, $field, $alias);break;
        default: return $text;break;
      }
    } else {
      return $this->hyperLink($text, $field, $alias);
    }
  }

  private function makeLinks($text){
    $text = eregi_replace('(((http|ftp|mms|https)://)[-a-zA-Z0-9\,@!():%_\+.~#\?&//='.L_A_CHARACTER_LIST.']+)', '<a href="\\1" target="_top">\\1</a>', $text);
    $text = eregi_replace('([[:space:]()[{}])(www.[-a-zA-Z0-9\,@!():%_\+.~#\?&//='.L_A_CHARACTER_LIST.']+)','<a href="'.S_PROTOCOL.'://\\2" target="_top">\\2</a>', $text);
    $text = eregi_replace('()(mailto:[-a-zA-Z0-9@!():%_\+.~#?&//='.L_A_CHARACTER_LIST.']+)','<a href="
    \\2" target="_top">\\2</a>', $text);
    $text = str_replace(',"', '"', str_replace(',</a>', '</a>,', $text));
    return $text;
  }

  private function truncate($text, $limit, $break=" "){
    if(false !== ($breakpoint = strpos($text, $break, $limit))){
      if($breakpoint < strlen($text) - 1) {
        $text = substr($text, 0, $breakpoint);
      }
    }
    return $text;
  }

  private function hyperLink($text, $field, $alias){
  $text = str_replace("&nbsp;"," //nobr// ",$text);
  $text = preg_replace("/(<br>|<BR>|<br \/>|<BR \/>)/"," //br// ",$text)." ";
  $textLength = strlen($text);
  $str = '';
  $fieldType = (S_HYPERLINK_CISOFIELD == "0")?"CISOSEARCHALL":$field;
  $aliasType = (S_HYPERLINK_CISOROOT == "0")?"all":$alias;
  $pattern = '([a-zA-Z0-9\''.L_A_CHARACTER_LIST.']*)';
  $replace = '<a href="seek_results.php?CISOOP1=any&amp;CISOFIELD1='.$fieldType.'&amp;CISOROOT='.$aliasType.'&amp;CISOBOX1=\\1" target="_top">\\1</a>';

  $fp = fopen("../stopwords.txt", "r");
      if((!$fp) || (S_ALLOW_HYPERLINKING == "0") || ($textLength > S_HYPERLINK_LIMIT)){
      $str = $text;
      } else {
      $lines = file('../stopwords.txt');
      $text = str_replace(' .','.',$text);
      $words = explode(" ", $text);
          for($i = 0; $i < count($words); $i++){
          $j = $i + 1;
            if(strpos($words[$i],'&nbsp;') > 0){
            $w = explode("&nbsp;", $words[$i]);
            $str .= ereg_replace($pattern, $replace, $w[0])."&nbsp".ereg_replace($pattern, $replace, $w[1]);
            } else {
                if((preg_match("/\b(http|https|ftp|mms|mailto|br)\b/i",$words[$i])) || ($words[$i] == "à") || ($words[$i] == "//nobr//") || (substr($words[$i],0,2) == "&#") || (!$this->isReadable($words[$i]))){
                $str .= $words[$i]." ";
                } else {
                $str .= ereg_replace($pattern, $replace, $words[$i]);
                    if($i != count($words) - 1){
                      $str .= " ";
                      }
                }
              }
          }
          $str = str_replace('<a href="seek_results.php?CISOOP1=any&amp;CISOFIELD1='.$fieldType.'&amp;CISOROOT='.$aliasType.'&amp;CISOBOX1=" target="_top"></a>', '', $str);
          foreach($lines as $sd){
          $str = str_replace('<a href="seek_results.php?CISOOP1=any&amp;CISOFIELD1='.$fieldType.'&amp;CISOROOT='.$aliasType.'&amp;CISOBOX1='.trim($sd).'" target="_top">'.trim($sd).'</a>', trim($sd), $str);
          $str = str_replace('<a href="seek_results.php?CISOOP1=any&amp;CISOFIELD1='.$fieldType.'&amp;CISOROOT='.$aliasType.'&amp;CISOBOX1='.trim(ucfirst($sd)).'" target="_top">'.trim(ucfirst($sd)).'</a>', trim(ucfirst($sd)), $str);
          $str = str_replace('<a href="seek_results.php?CISOOP1=any&amp;CISOFIELD1='.$fieldType.'&amp;CISOROOT='.$aliasType.'&amp;CISOBOX1='.trim(strtoupper($sd)).'" target="_top">'.trim(strtoupper($sd)).'</a>', trim(strtoupper($sd)), $str);
          }
      }
    $str = str_replace(' //nobr// ','&nbsp;',$str);    
    $str = str_replace(' //br// ','<br />',$str);    
    return $str;
  }

  public function vocabLink($text, $alias, $field){
    $text = str_replace("&nbsp;","//nobr//",$text);
    $str = '';
    $fieldType = (S_HYPERLINK_CISOFIELD == "0")?"CISOSEARCHALL":$field;
    $aliasType = (S_HYPERLINK_CISOROOT == "0")?"all":$alias;
        if(S_ALLOW_HYPERLINKING == "0"){
        $str = $text;
        } else {
        $s = explode(';',$text);
            for($i=0;$i<count($s);$i++){
            $s[$i] = trim($s[$i]);
                if(!empty($s[$i])){
                $str .= '<a href="seek_results.php?CISOOP1=exact&amp;CISOFIELD1='.$fieldType.'&amp;CISOROOT='.$aliasType.'&amp;CISOBOX1='.urlencode(str_replace('(',' ', str_replace(')',' ',$s[$i]))).'" target="_top">'.$s[$i].'</a><br />';
                }
            }
        }
    $str = str_replace('//nobr//','&nbsp;',$str);
    return $str;
  }

  private function isReadable($str){
    $t = 0;
    for ($i = 0; $i < strlen($str); $i++){
      $chr = $str[$i];
      $ord = ord($chr);
      if(($ord<32) || ($ord>206)){
        return false;
      }
    }
    return true;
  }

  private function formatDate($date){
    $pattern = '([a-zA-Z])';
    if(preg_match($pattern,$date)){
    return $date;
    } else {
    $a = S_DATE_FORMAT;
    $ms = array();
    $ms[0] = "";
    $ms[1] = "January";
    $ms[2] = "February";
    $ms[3] = "March";
    $ms[4] = "April";
    $ms[5] = "May";
    $ms[6] = "June";
    $ms[7] = "July";
    $ms[8] = "August";
    $ms[9] = "September";
    $ms[10] = "October";
    $ms[11] = "November";
    $ms[12] = "December";

    $dt = explode('-',$date);
        if((isset($dt[0]) && strlen($dt[0]) != 4) || (isset($dt[1]) && $dt[1] > 12) || (isset($dt[1]) && $dt[1] < 1)){
        $ret = $date;
        } else {
        $y = (isset($dt[0]))?$dt[0]:"";
        $m = (isset($dt[1]))?$dt[1]:"";
        $d = (isset($dt[2]))?$dt[2]:"";

        $md = (isset($dt[1]))?"-":"";
        $mh = (isset($dt[1]))?"/":"";

        $dd = (isset($dt[2]))?"-":"";
        $ds = (isset($dt[2]))?"/":"";

            if(strlen($m) == 1){
            $lm = "0".$m;
            } else {
            $lm = $m;
            }
            if($m<10){
            $m = str_replace('0','',$m);
            }
            $sm = (isset($dt[1]))?$ms[$m]:"";
            if(strlen($d) == 1){
            $ld = "0".$d;
            } else {
            $ld = $d;
            }
        $i=0;
        $f = array();
        $f[$i++] = $y.$md.$lm.$dd.$ld;
        $f[$i++] = $ld.$dd.$lm.$md.$y;
        $f[$i++] = $lm.$md.$ld.$dd.$y;
        $f[$i++] = $sm." ".$ld." ".$y;
        $f[$i++] = $ld." ".$sm." ".$y;
        $f[$i++] = $m.$mh.$d.$ds.$y;
        $f[$i++] = $d.$ds.$m.$mh.$y;
        $ret = $f[$a];
        }
    }
    return "<nobr>".$ret."</nobr>";
  }
}
