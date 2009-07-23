<?
class SearchStatus {
  function __construct($seek_search_params, $search_position) {
    $this->search_position = $search_position;
    
    if(is_string($seek_search_params)) {
      parse_str($seek_search_params, $query_params);
    } else {
      $query_params = $seek_search_params;
    }

    $search = Search::from_params($query_params);
    $search->maxrecs = 3;
  
    $prev_item_num = ($this->search_position > 1) ? $this->search_position - 1 : null;
    
    $current_result_index = $this->search_position - 1;
    $search->start = array(0, max(1, $current_result_index));
    $results = $search->results();
    
    if($this->search_position > 1) {
      $this->previous_item = get_item($results[0]['collection'], $results[0]['pointer']);
      $this->previous_item['query_string'] .= "&amp;search_position=".($this->search_position - 1);
    }
    if($this->search_position < $search->total) {
      $next_result_index = ($this->search_position > 1) ? 2 : 1;
      $this->next_item = get_item($results[$next_result_index]['collection'], $results[$next_result_index]['pointer']);
      $this->next_item['query_string'] .= "&amp;search_position=".($this->search_position + 1);
    }
  
    $this->total_items = $search->total;
  }
  
  public $previous_item;
  public $next_item;
  public $search_position;
  public $total_items;
}

?>