<?php

class Paginator {
  public $offset;
  public $params;
  public $max_records;
  public $total_records;
  public $nav_size;

  function __construct($query_string, $search, $nav_size = 10) {
    $this->offset = $search->start;
    $this->params = htmlentities($this->stripUrlVar($query_string, 'CISOSTART'));
    $this->max_records = $search->maxrecs;
    $this->total_records = $search->total;
    $this->nav_size = $nav_size;
  }

  public function current_page() {
    return ceil($this->offset / $this->max_records);
  }

  public function total_pages() {
    return ceil($this->total_records / $this->max_records);
  }

  public function next_link() {
    $next_offset = ($this->offset + $this->max_records);
    return $this->page_link($next_offset);
  }

  public function previous_link() {
    $previous_offset = ($this->offset - $this->max_records);
    return $this->page_link($previous_offset);
  }

  public function pages() {
    $results = array();

    $page_num = $this->start_page();
    $nav_num = $this->start_page() + $this->nav_size - 1;

    $end = min($nav_num, $this->total_pages());
    while($page_num <= $end) {
      $page_base_count = (($page_num * $this->max_records) - ($this->max_records - 1));
      $results[$page_num] = $page_base_count;
      $page_num++;
    }

    return $results;
  }

  public function page_link($offset) {
    return 'seek_results.php?'.$this->params.'&amp;CISOSTART='.$offset;
  }

  public function start_page() {
    return (floor(($this->current_page() - 1) / $this->nav_size) * $this->nav_size) + 1;
  }

  public function is_last_page() {
    return $this->current_page() == $this->total_pages();
  }

  public function is_first_page() {
    return $this->current_page() == 1;
  }


#private
  
  private function stripUrlVar($u,$a){
    $p = strpos($u,"$a=");
        if($p){
            if($u[$p-1] == "&"){
            $p--;
            }
        $ep = strpos($u,"&",$p+1);
            if ($ep === false){
            $u = substr($u,0,$p);
            } else {
            $u = str_replace(substr($u,$p,$ep-$p),'',$u);
            }
        }
    return $u;
  }
}
