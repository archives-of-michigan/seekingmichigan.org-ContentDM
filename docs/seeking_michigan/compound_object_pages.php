<html>
  <head></head>
  <body>
    <div class="wrapper">
      <ul>
        <? foreach($pages as $page): ?>
          <li>
            <a href="/seeking_michigan/discover_item_viewer.php?<?= $page->query_string ?><? if($encoded_seek_search_params): ?>&amp;search=<?= $encoded_seek_search_params ?>&amp;search_position=<?= $previous_position ?><? endif; ?>" title="Previous">
          </li>
        <? endforeach; ?>
    </div>
  </body>
</html>
