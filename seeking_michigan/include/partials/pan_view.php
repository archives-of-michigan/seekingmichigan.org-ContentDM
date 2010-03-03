<div id="item-view">
  <div class="wrapper mod">
    <div class="item-preview">
      <div id="dmMonocle"></div>
    </div>
    <h2>Metadata</h2>
    <? display_metadata($current_item); ?>
  </div>
</div>
<div id="sidebar">
  <? if($current_item->is_child()): ?>
    <a id="compound_object_pages" href="/seeking_michigan/compound_object_pages.php">All items</a>
  <? endif; ?>
  <? #prev_next_search($seek_search_params,$search_position); ?>
</div>
