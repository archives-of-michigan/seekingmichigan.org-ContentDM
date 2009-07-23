<? 
function altText($text){
  if(strlen($text) > 100){
    $text = truncate($text,100);
  }
  return str_replace("<br />","\n", str_replace("'","&#39;",str_replace("\"","&#34;",charReplace($text))));
}

$z = '';
for($i = 0; $i < count($record); $i++){
$z .= "|".$record[$i]['collection']." ".$record[$i]['pointer']." ".$record[$i]['filetype']." ".$record[$i]['parentobject'];
}
?>
<? if($isRes): ?>
  <ol class="search-results mod" start="<?= $start[1]; ?>">
    <? for($r = 0; $r < count($record); $r++): ?>
      <? 
        $item = $record[$r];
        $search_url = urlencode($_SERVER['QUERY_STRING']);
        $item_num = $start[1] + $r;
        if($record[$r]['parentobject'] >= 0){
          $directTo = 'discover_item_viewer.php?CISOROOT='.$record[$r]['collection'].'&amp;CISOPTR='.$record[$r]['parentobject'].'&amp;CISOSHOW='.$record[$r]['pointer']."&amp;search=".$search_url."&amp;search_position=".$item_num;
        } else {
          $directTo = 'discover_item_viewer.php?CISOROOT='.$record[$r]['collection'].'&amp;CISOPTR='.$record[$r]['pointer']."&amp;search=".$search_url."&amp;search_position=".$item_num;
        }
      ?>
      <li>
        <? if($item['collection'] == '/p129401coll7'): ?>
          <? include('results_view_death_record.php'); ?>
        <? else: ?>
          <h3>
            <a href="<?= $directTo; ?>" title="<?=altText($item[$field[0]]);?>">
              <img src="/cgi-bin/thumbnail.exe?CISOROOT=<?=$item['collection'];?>&amp;CISOPTR=<?=$item['pointer'];?>" alt="<?=altText($item[$field[0]]);?>" title="<?=altText($item[$field[0]]);?>" />
              <?= $item['title']; ?>
            </a>
          </h3>
          <p class="byline"><?= $item['subjec']; ?></p>
          <p><?= $item['descri']; ?></p>
        <? endif; ?>
      </li>
      <? $hr = ($j/$maxrecs); ?>
    <? endfor; ?>
  </ol>
<? else: ?>
  No Items matched your criteria
<? endif; ?>