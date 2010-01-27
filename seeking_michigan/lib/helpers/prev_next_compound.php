<?php
if($isthisCompoundObject && !$show_all) {
  $heading = "Document Pages";
  $previous_position = $search_position;
  $next_position = $search_position;
  include('discover/previous_next.php');
}
