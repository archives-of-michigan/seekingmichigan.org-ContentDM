<?
class SearchStatus {
  function __construct($seek_search_params) {
    $search = Search::from_param_string($seek_search_params);
    $search->maxrecs = 3;
    $this->search_position = $search->start;
    $search->start = max(0,$search->start - 1);
    
    $this->search_params = $seek_search_params;

    $results = $search->results();
    
    if($this->search_position > 1) {
      $this->previous_item = $results[0];
    }

    if($this->search_position < $search->total) {
      $this->next_item = $results[2];
    }
  
    $this->total_items = $search->total;
  }
  
  public $search_params;
  public $search_position;
  public $previous_item;
  public $next_item;
  public $total_items;

  public function previous_view_link() {
    return $this->previous_item->view_link($this->search_params,
                                           $this->search_position - 1);
  }
  public function next_view_link() {
    return $this->next_item->view_link($this->search_params,
                                       $this->search_position + 1);
  }

  public function previous_thumbnail() {
    return $this->previous_item->thumbnail_path();
  }
  public function next_thumbnail() {
    return $this->next_item->thumbnail_path();
  }
}
