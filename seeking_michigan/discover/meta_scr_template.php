<ul class="meta-data">
<? foreach($std_fields as $item): ?><li><h4><?= $item["field"] ?></h4><p><?= $item['value'] ?></p></li><? endforeach ?>
<? if ($enabled && $public == 1 && isset($item['structure'][$item['index']["FULLRS"][0]])): ?>
  <? if(array_key_exists("value",$item['structure'][$item['index']["FULLRS"][0]])): ?>
    <? $fullres = $item['structure'][$item['index']["FULLRS"][0]]["value"]; ?>
  <? endif; ?>
  <? if ($fullres != ""): ?>
    <? $p = strpos($fullres,"\\"); ?>
    <? if (($p != false) && ($p > 0)): ?>
      <? dmGetCollectionFullResVolumeInfo($alias,substr($fullres,0,$p),$location); ?>
      <li><h4>Full resolution</h4><?= $fullres ?>
      <p><? if (substr($location,0,4) == "http"): ?>
        <? $fullurl = $location . "/" . substr($fullres,$p+1); ?>
        <br /><a href="<?= $fullurl ?>"><?= $fullurl ?></a>
      <? endif; ?></p></li>
    <? endif; ?>
  <? endif; ?>
<? endif; ?>
</ul>