<div  id="item-view">
  <div class="wrapper mod">
    <? if($file_extension == 'pdf'): ?>
      <ul id="item-actions">
        <li class="zoom-pop">
          Full/Pop:
          <a href="<?= $file_url ?>" class="pop-full" title="Fullscreen"><img src="<?= SEEKING_MICHIGAN_HOST ?>/images/icon-zoom-full.gif" alt="rotate left" /></a> 
          <a href="<?= $file_url ?>" class="pop-new" title="New Window" target="_new"><img src="<?= SEEKING_MICHIGAN_HOST ?>/images/icon-zoom-new.gif" alt="rotate left" /></a>
        </li>
      </ul>
    <? endif; ?>
    <div class="item-preview">
      <? if($file_extension == 'pdf'): ?>
        <a href="<?= $file_url ?>">Download PDF</a>
        <iframe src="<?= $file_url ?>" style="width: 100%; height: 6in"></iframe>
      <? elseif($file_extension == 'mp3'): ?>
        <script language="JavaScript" src="<?= SEEKING_MICHIGAN_HOST ?>/js/audio-player.js"></script>
        <object type="application/x-shockwave-flash" data="<?= SEEKING_MICHIGAN_HOST ?>/wp-content/themes/airbag/swf/player.swf" 
          style="display: block; margin: 10px auto;" height="24" width="290">
          <param name="movie" value="<?= SEEKING_MICHIGAN_HOST ?>/wp-content/themes/airbag/swf/player.swf">
          <param name="FlashVars" value="playerID=1&amp;soundFile=<?= $encoded_file_url ?>">
          <param name="quality" value="high">
          <param name="menu" value="true">
          <param name="wmode" value="transparent">
        </object>
        <a href="<?= $file_url ?>">Download audio file</a>
      <? elseif($is_image): ?>
        <a href="/seeking_michigan/discover_item_viewer.php?CISOROOT=<?= $alias ?>&amp;CISOPTR=<?= $itnum ?>&amp;CISOSHOW=<?= $itnum ?>&amp;search=<?= $seek_search_params ?>">
          <img src="<?= $file_url ?>" />
        </a>
      <? elseif(isset($pageptr) && $pageptr != 0): ?>
        <b><a href="<?= $file_url ?>&amp;CISOPAGE=<?= $pageptr + isSearchHit($pageptr) ?>">View Document</a>
      <? else: ?>
        <a href="<?= $file_url ?>">Download <?= $filename ?></a>
      <? endif; ?>
    </div>
    <? if(!$_GET['show_all'] || $i == 1): ?>
      <h2>Meta Data</h2>
      <? if($isthisCompoundObject): ?>
        <? display_metadata($alias, $parent_item, $conf); ?>
      <? else: ?>
        <? display_metadata($alias, $display_item, $conf); ?>
      <? endif; ?>
    <? endif; ?>
    <? if($isthisCompoundObject): ?>
      <h2>Current Item's Meta Data</h2>
      <? display_metadata($alias, $display_item, $conf); ?>
    <? endif; ?>
  </div><!-- end .wrapper -->
</div><!-- end .item-view -->
<? if(!$_GET['show_all']): ?>
  <div  id="sidebar">
   <? prev_next_search($seek_search_params,$search_position); ?>
  </div>
<? endif; ?>