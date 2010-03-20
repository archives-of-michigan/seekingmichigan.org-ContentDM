<? $paginator = new Paginator($_SERVER['QUERY_STRING'], $search); ?>
<? if($_GET['show_all'] != 'true'): ?>
<ul>
  <li class="previous">
    <? if($paginator->is_first_page()): ?>
      Previous
    <? else: ?>
      <a href="<?= $paginator->previous_link(); ?>">Previous</a>
    <? endif; ?>
  </li>
  <? foreach($paginator->pages() as $page_num => $page_base_count): ?>
    <li>
      <? if($paginator->current_page() == $page_num): ?>
        <?= $page_num ?>
      <? else: ?>
        <a href="<?= $paginator->page_link($page_base_count); ?>"><?=trim($page_num);?></a>
      <? endif; ?>
    </li>
  <? endforeach; ?>
  <li class="next">
    <? if($paginator->is_last_page()): ?>
      Next
    <? else: ?>
      <a href="<?= $paginator->next_link(); ?>">Next</a>
    <? endif; ?>
  </li>
</ul>
<? endif ?>
