<?php 
$metadata_item = ($item->is_child()) ? $item->parent_item() : $item;
$metadata = $metadata_item->metadata();
?>
<ul class="meta-data">
  <? foreach($metadata as $field => $value): ?>
    <li>
      <h4><?= $field; ?></h4>
      <p><?= $value; ?></p>
    </li>
  <? endforeach ?>
</ul>
