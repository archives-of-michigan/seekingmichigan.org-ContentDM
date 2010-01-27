<?php
if(TEST_ENV != 'TEST') { require_once dirname(__FILE__)."/../../dmscripts/DMSystem.php"; }
require_once 'content_dm.php';

class Search {
  function __construct(
      $_search_alias = array(),
      $_field = array('title','subjec','descri','creato','date','type','format'),
      $_sortby = array('title'),
      $_searchstring = array(),
      $_maxrecs = 1024,
      $_start = array(1,1)
    ) {
    $this->search_alias = $_search_alias;
    $this->sortby       = $_sortby;
    $this->field        = $_field;
    $this->searchstring = $_searchstring;
    $this->maxrecs      = $_maxrecs;
    $this->start        = $_start;
  }
  
  public $search_alias;
  public $sortby;
  public $field;
  public $searchstring;
  public $maxrecs;
  public $start;
  public $total;
  
  public static function from_param_string($param_string) {
    parse_str(
      urldecode($param_string),
      $params
    );
    
    return Search::from_params($params);
  }
  
  public static function from_params($params) {
    Search::complexify_simple_search_params($params);

    $alias = array_values(ContentDM::get_alias($params));
    $search = new Search();
    $search->search_alias = $alias;
    $search->maxrecs = 20;
    $search->searchstring = Search::generate_search_string($params);
    
    if(isset($params['document-types'])) {
      if(in_array('map',$params['document-types'])) {
        $search->search_alias = array('/p129401coll3');
      }
    }

    $start = (isset($params['CISOSTART'])) ? $params['CISOSTART'] : "1,1";
    $search->start = split(',',$start);

    return($search);
  }

  public static function chunk_simple_search_string($string) {
    $chunks = array('any' => array(), 'exact' => array());
    preg_match('/"([^"])"/', $string, $exact_matches);

    foreach($exact_matches => $match) {
      
    }

    $string = preg_replace('/"[^"]"/', '', $string);

    return $chunks;
  }

  public static function complexify_simple_search_params(&$params) {
    if(isset($params['s'])) {
      Search::default_for($params, 'CISOROOT', 'any');

      $chunks = Search::chunk_simple_search_string($params['s']);
      $counter = 0;
      foreach(array('any','exact') => $type) {
        foreach($chunks[$type] => $chunk) {
          $params["CISOFIELD$counter"] = 'CISOSEARCHALL';
          $params["CISOBOX$counter"] = $chunk;
          $params["CISOOP$counter"] = $type;
          $counter++;
        }
      }
    }

    return $params;
  }

  public function results() {
    $results = dmQuery(
          $this->search_alias,
          $this->searchstring,
          $this->field,
          $this->sortby,
          $this->maxrecs,
          $this->start[1],
          $this->total,
          1);
    return($results);
  }
  
  public function terms() {
    if(count($this->searchstring) > 0) {
      $terms = array();
      
      foreach($this->searchstring as $box) {
        if($box['field'] == 'format') { continue; }
        
        $terms = array_merge($terms, explode(' ', $box['string']));
      }
      
      return $terms;
    } else {
      return array();
    }
  }
  
  public function term_search_string($alias, $term) {
    return 'CISOROOT='.$alias.'&amp;CISOOP1=any&amp;CISOFIELD1=CISOSEARCHALL&amp;CISOBOX1='.$term;
  }

  public static function default_for(&$array, $name, $default) {
    if(!isset($array[$name]) || $array[$name] == '') {
      $array[$name] = $default;
    }
  }
  
  public static function generate_search_string($query_params) {
    $s = array();
    if(isset($query_params["CISOPARM"])){
      $parm = explode(":",$query_params["CISOPARM"]);
      $s[0]["field"] = $parm[1];
      $s[0]["string"] = $parm[2];
      $s[0]["mode"] = $query_params["CISOOP1"];
      $s = array_values($s);
      return($s);
    } else if((!isset($query_params["CISOPARM"])) && (isset($query_params["CISOROOT"]))){
      Search::default_for($query_params, 'CISOBOX1', ' ');
      Search::default_for($query_params, 'CISOFIELD1', 'CISOSEARCHALL');
      Search::default_for($query_params, 'CISOOP1', 'any');
      for($i = 1; $i <= 4; $i++) {
        $idx = $i - 1;
        if(isset($query_params["CISOBOX$i"]) && ($query_params["CISOBOX$i"] != "")){
          $s[$idx]["field"] = $query_params["CISOFIELD$i"];
          $s[$idx]["string"] = $query_params["CISOBOX$i"];
          $s[$idx]["mode"] = $query_params["CISOOP$i"];
        }
      }
      
      $s = array_merge($s, Search::generate_content_type_search_string($query_params));
      $s = array_values($s);
      return($s);
    }
  }
  
  public static function generate_content_type_search_string($query_params) {
    $search = array();
    
    if(isset($query_params['media-types%5B%5D'])) { $query_params['media-types'] = $query_params['media-types%5B%5D']; }
    if(isset($query_params['media-types[]'])) { $query_params['media-types'] = $query_params['media-types[]']; }
    
    if(isset($query_params['media-types']) && is_array($query_params['media-types'])) {
      $type_filter = '';
      
      $types = array('image' => 'image', 'audio' => 'audio', 'video' => 'video', 'docs' => 'Document');
      foreach($types as $type => $filter) {
        if(in_array($type,$query_params['media-types'])) {
          $type_filter .= $filter.' ';
        }
      }

      if(strlen($type_filter) > 0) {
        $search[] = array(
          'field' => 'format',
          'string' => $type_filter,
          'mode' => 'all'
        );
      }
    }
    
    return $search;
  }
}
