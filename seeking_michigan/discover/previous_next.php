<div class="wrapper">
  <h3><?= $heading ?></h3>
  <ul class="prev-next">
    <li class="previous">
      <? if($previous_item): ?>
        <a href="/seeking_michigan/discover_item_viewer.php?<?= $previous_item['query_string'] ?><? if($encoded_seek_search_params): ?>&amp;search=<?= $encoded_seek_search_params ?>&amp;search_position=<?= $previous_position ?><? endif; ?>" title="Previous">
          <? if($previous_item['thumbnail']): ?>
            <img src="<?= $previous_item['thumbnail'] ?>" alt="previous item" />
          <? endif; ?>
          Previous
        </a>
      <? else: ?>
        <span class="nav-boundary"><img src="<?= SEEKING_MICHIGAN_HOST ?>/images/first_item.png" alt="first item" /></span>
      <? endif; ?>
    </li>
    <li class="next">
      <? if($next_item): ?>
        <a href="/seeking_michigan/discover_item_viewer.php?<?= $next_item['query_string'] ?><? if($encoded_seek_search_params): ?>&amp;search=<?= $encoded_seek_search_params ?>&amp;search_position=<?= $next_position ?><? endif; ?>" title="Next">
          <? if($next_item['thumbnail']): ?>
            <img src="<?= $next_item['thumbnail'] ?>" alt="next item" />
          <? endif; ?>
          Next
        </a>
      <? else: ?>
        <span class="nav-boundary"><img src="<?= SEEKING_MICHIGAN_HOST ?>/images/last_item.png" alt="last item" /></span>
      <? endif; ?>
    </li>
  </ul>
</div>
<div class="wrapper">
  <p class="collection-meta">
    Item <strong><?= $current_item_num ?></strong> of <strong><?= $totalitems ?></strong>
    <? if($type == 'compound'): ?>
      <a href="/seeking_michigan/discover_item_viewer.php?CISOROOT=<?= $alias ?>&amp;CISOPTR=<?= $parent_itnum ?>&amp;search=<?= $encoded_seek_search_params ?>&amp;show_all=true">Show All</a>
    <? endif; ?>
  </p>
</div>