<h3>
  <a href="<?= $directTo; ?>" title="<?=altText($item[$field[0]]);?>">
    <img src="/cgi-bin/thumbnail.exe?CISOROOT=<?=$item['collection'];?>&amp;CISOPTR=<?=$item['pointer'];?>" alt="<?=altText($item[$field[0]]);?>" title="<?=altText($item[$field[0]]);?>" />
    <?= $item['descri'];  #first name ?>
    <?= $item['creato'];  #last name ?>
  </a>
</h3>
<p class="byline"><?= $item['type']; ?> <?= $item['date']; ?>, <?= $item['format']; ?></p>
<p>
  <? if($item['subjec']): ?><?= $item['subjec'];  #city/village/township ?>, <? endif; ?>
  <? if($item['title']): ?><?= $item['title'];  #county ?> County<? endif; ?>
</p>