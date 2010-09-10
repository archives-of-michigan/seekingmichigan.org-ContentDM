<div id="close_button">Close</div>
<ul>
  <? $count = 0; ?>
  <? foreach($parent_item->items as $item): ?>
    <?
      $class = array();
      if($count % 3 == 0) {
        $class[] = 'clear_before';
      }
      if($item->itnum == $current_itnum) {
        $class[] = 'current_page';
      }
    ?>
    <li <? if(count($class) > 0): ?>class="<?= join($class, ' '); ?>"<? endif; ?>>
      <a href="<?= $item->search_view_link($search_status); ?>" title="Previous">
        <img src="<?= $item->thumbnail_path(); ?>" />
        <span><?= $item->alt_title(); ?></span>
      </a>
    </li>
    <? $count++; ?>
  <? endforeach; ?>
</ul>
