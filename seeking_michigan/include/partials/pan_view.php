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
    <a id="compound_object_pages"
       href="/seeking_michigan/compound_object_pages.php">
      Browse Document
    </a>
    <a href="<?= $current_item->parent_item()->
                  view_link($seek_search_params); ?>">
      Show All Items in Document
    </a>
  <? endif; ?>
  <? 
     if($search_status) {
        app()->partial('previous_next', 
          array('search_status' => $search_status));
     }
  ?>
</div>
