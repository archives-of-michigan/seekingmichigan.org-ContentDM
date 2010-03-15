<ul class="meta-data">
  <? foreach($item->metadata() as $field => $value): ?>
    <li>
      <h4><?= $field; ?></h4>
      <p><?= $value; ?></p>
    </li>
  <? endforeach ?>
</ul>
