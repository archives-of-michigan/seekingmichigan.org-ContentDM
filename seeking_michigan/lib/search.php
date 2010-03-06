<?php
if(TEST_ENV != 'TEST') { require_once dirname(__FILE__)."/../../dmscripts/DMSystem.php"; }
require_once 'content_dm.php';

class Search {

  public $search_alias;
  public $sortby;
  public $field;
  public $search_string;
  public $maxrecs;
  public $start;
  public $total;
  public $document_types;
  public $media_types;
  public $search_all;

  function __construct(
      $_search_alias = array(),
      $_field = array('title','subjec','descri','creato','date','type','format'),
      $_sortby = array('title'),
      $_search_string = array(),
      $_maxrecs = 1024,
      $_start = array(1,1),
      $_document_types = array(),
      $_media_types = array()
    ) {
    $this->search_alias = $_search_alias;
    $this->sortby       = $_sortby;
    $this->field        = $_field;
    $this->search_string = $_search_string;
    $this->maxrecs      = $_maxrecs;
    $this->start        = $_start;
    $this->document_types = $_document_types;
    $this->media_types = $_media_types;
    $this->search_all = FALSE;
  }
  
  public static function from_param_string($param_string) {
    parse_str(
      urldecode($param_string),
      $params
    );
    
    return Search::from_params($params);
  }
  
  public static function from_params($params) {
    Search::complexify_simple_search_params($params);

    $search = new Search();
    $search->search_alias = array_values(ContentDM::get_alias($params));
    $search->search_all = ($params['CISOROOT'] == 'all') ? TRUE : FALSE;
    $search->maxrecs = 20;
    if(isset($params['document-types'])) {
      $search->set_document_types($params['document-types']);
    }
    if(isset($params['media-types'])) {
      $search->media_types = $params['media-types'];
    } else if(isset($query_params['media-types%5B%5D'])) {
      $search->media_types = $params['media-types%5B%5D'];
    }
    $search->set_search_string($params);

    $start = (isset($params['CISOSTART'])) ? $params['CISOSTART'] : "1,1";
    $search->start = split(',',$start);

    return($search);
  }

  public static function chunk_simple_search_string($string) {
    $chunks = array('any' => array(), 'exact' => array());
    preg_match_all('/"([^"]+)"/', $string, $exact_matches);
    $exact_matches = array_pop($exact_matches);

    foreach($exact_matches as $match) {
      $chunks['exact'][count($chunks['exact'])] = trim($match);
    }

    $any = preg_replace('/"[^"]*"/', '', $string);
    $any = preg_replace('/\s+/', ' ', $any);
    $any = trim($any);

    if(strlen($any) > 0) {
      $chunks['any'][0] = $any;
    }

    return $chunks;
  }

  public static function complexify_simple_search_params(&$params) {
    if(isset($params['s'])) {
      Search::default_for($params, 'CISOROOT', 'all');

      $chunks = Search::chunk_simple_search_string($params['s']);
      $counter = 1;
      foreach(array('any','exact') as $type) {
        foreach($chunks[$type] as $chunk) {
          $params["CISOFIELD$counter"] = 'CISOSEARCHALL';
          $params["CISOBOX$counter"] = $chunk;
          $params["CISOOP$counter"] = $type;
          $counter++;
        }
      }
    }

    return $params;
  }

  public function set_document_types($doctypes) {
    $this->document_types = $doctypes;
    if(in_array('map',$doctypes)) {
      $this->search_alias = array('/p129401coll3');
    }
  }

  public function results() {
    $results = dmQuery(
          $this->search_alias,
          $this->search_string,
          $this->field,
          $this->sortby,
          $this->maxrecs,
          $this->start[1],
          $this->total,
          1);
    $list = array();
    foreach($results as $result) {
      $list[] = Item::from_search($result);
    }
    return($list);
  }

  public function is_default_search() {
    return (count($this->search_string) == 0);
  }

  public function terms() {
    $terms = array();
    foreach($this->search_string as $box) {
      if($box['field'] != 'format') {
        $terms = array_merge($terms, explode(' ', $box['string']));
      }
    }
    return $terms;
  }
  
  public function term_search_string($term) {
    $aliases = ($this->search_all) ? 
      join(',',$this->search_string) :
      'all';
    return 'CISOROOT='.$aliases.'&amp;s='.$term;
  }

  public function form_fields($overrides = array()) {
    $fields = array();

    $fields['CISOROOT'] = join(',', $this->search_alias);
    $counter = 1;
    foreach($this->search_string as $string) {
      $fields["CISOBOX$counter"] = $string['string'];
      $fields["CISOOP$counter"] = $string['mode'];
      $fields["CISOFIELD$counter"] = $string['field'];
      $counter++;
    }
    $fields['CISOSTART'] = join(',',$this->start);

    return array_merge($fields, $overrides);
  }

  public static function default_for(&$array, $name, $default) {
    if(!isset($array[$name]) || $array[$name] == '') {
      $array[$name] = $default;
    }
  }
  
  public function set_search_string($params) {
    $this->search_string = array();
    if(isset($params["CISOPARM"])){
      $parm = explode(":",$params["CISOPARM"]);
      $this->search_string[] = array(
        'field' => $parm[1],
        'string' => $parm[2],
        'mode' => $params['CISOOP1']
      );
    } else {
      Search::default_for($params, 'CISOBOX1', '');
      Search::default_for($params, 'CISOFIELD1', 'CISOSEARCHALL');
      Search::default_for($params, 'CISOOP1', 'any');
      for($i = 1; $i <= 6; $i++) {
        if(isset($params["CISOBOX$i"]) && ($params["CISOBOX$i"] != "")){
          $this->search_string[] = array(
            'field' => $params["CISOFIELD$i"],
            'string' => $params["CISOBOX$i"],
            'mode' => $params["CISOOP$i"]);
        }
      }
      $this->generate_content_type_search_string($params);
    }
  }
  
  public function generate_content_type_search_string() {
    $type_filter = '';
    $types = array('image' => 'image', 'audio' => 'audio', 'video' => 'video', 'docs' => 'Document');
    foreach($types as $type => $filter) {
      if(in_array($type, $this->media_types)) {
        $type_filter .= $filter.' ';
      }
    }

    if(strlen($type_filter) > 0) {
      $this->search_string[] = array(
        'field' => 'format',
        'string' => $type_filter,
        'mode' => 'all'
      );
    }
  }
}
