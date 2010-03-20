<div class="wrapper">
  <h3>Search Results</h3>
  <ul class="prev-next">
    <li class="previous">
      <? if($search_status->previous_item): ?>
        <a href="<?= $search_status->previous_view_link(); ?>" title="Previous">
          <? if($search_status->previous_thumbnail()): ?>
            <img src="<?= $search_status->previous_thumbnail(); ?>"
                 alt="previous item" />
          <? endif; ?>
          Previous
        </a>
      <? else: ?>
        <span class="nav-boundary">
          <img src="<?= SEEKING_MICHIGAN_HOST ?>/images/first_item.png"
               alt="first item" />
        </span>
      <? endif; ?>
    </li>
    <li class="next">
      <? if($search_status->next_item): ?>
        <a href="<?= $search_status->next_view_link(); ?>" title="Next">
          <? if($search_status->next_thumbnail()): ?>
            <img src="<?= $search_status->next_thumbnail(); ?>"
                 alt="previous item" />
          <? endif; ?>
          Next
        </a>
      <? else: ?>
        <span class="nav-boundary">
          <img src="<?= SEEKING_MICHIGAN_HOST ?>/images/last_item.png"
               alt="last item" />
        </span>
      <? endif; ?>
    </li>
  </ul>
</div>
<div class="wrapper">
  <p class="collection-meta">
    Item <strong><?= $search_status->search_position ?></strong>
    of <strong><?= $search_status->total_items ?></strong>
  </p>
</div>
