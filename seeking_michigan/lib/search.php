<?
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
    $this->sortby       = $_sortby;
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
    $search = new Search();

    $alias = array_values(ContentDM::get_alias($params));

    $search->maxrecs = 20;
    $search->sortby = array('title');

    $search->searchstring = Search::generate_search_string($params);
    
    if(isset($params['document-types'])) {
      if(in_array('map',$params['document-types'])) {
        $search->search_alias = array('/p129401coll3');
      }
    }

    $start = (isset($params['CISOSTART'])) ? $params['CISOSTART'] : "1,1";
    $search->start = split(',',$start);

    $search->search_alias = $alias;

    return($search);
  }

  public function results() {
    $results = dmQuery(
          $this->search_alias,
          $this->searchstring,
          $this->field,
          $this->sortby,
          $this->maxrecs,
          $this->start[1],
          $this->total);
    
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
  
  public static function generate_search_string($query_params) {
    global $thisfile;
    $s = array();
    if(isset($query_params["CISOPARM"])){
      $parm = explode(":",$query_params["CISOPARM"]);
      $s[0]["field"] = $parm[1];
      $s[0]["string"] = $parm[2];
      $s[0]["mode"] = $query_params["CISOOP1"];
      $s = array_values($s);
      return($s);
    } else if((!isset($query_params["CISOPARM"])) && (isset($query_params["CISOROOT"]))){
      if(!isset($query_params["CISOBOX1"]) || $query_params["CISOBOX1"] == '') { $query_params["CISOBOX1"] = ' '; }
      if(!isset($query_params["CISOFIELD1"]) || $query_params["CISOFIELD1"] == '') { $query_params["CISOFIELD1"] = 'CISOSEARCHALL'; }
      if(!isset($query_params["CISOOP1"]) || $query_params["CISOOP1"] == '') { $query_params["CISOOP1"] = 'any'; }
    
      if(isset($query_params["CISOBOX1"]) && ($query_params["CISOBOX1"] != "")){
        $s[0]["field"] = $query_params["CISOFIELD1"];
        $s[0]["string"] = $query_params["CISOBOX1"];
        $s[0]["mode"] = $query_params["CISOOP1"];
      }
      if(isset($query_params["CISOBOX2"]) && ($query_params["CISOBOX2"] != "")){
        $s[1]["field"] = $query_params["CISOFIELD2"];
        $s[1]["string"] = $query_params["CISOBOX2"];
        $s[1]["mode"] = $query_params["CISOOP2"];
      }
      if(isset($query_params["CISOBOX3"]) && ($query_params["CISOBOX3"] != "")){
        $s[2]["field"] = $query_params["CISOFIELD3"];
        $s[2]["string"] = $query_params["CISOBOX3"];
        $s[2]["mode"] = $query_params["CISOOP3"];
      }
      if(isset($query_params["CISOBOX4"]) && ($query_params["CISOBOX4"] != "")){
        $s[3]["field"] = $query_params["CISOFIELD4"];
        $s[3]["string"] = $query_params["CISOBOX4"];
        $s[3]["mode"] = $query_params["CISOOP4"];
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
?>