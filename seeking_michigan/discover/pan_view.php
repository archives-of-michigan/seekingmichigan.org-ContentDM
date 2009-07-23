<?
$conf = &dmGetCollectionFieldInfo($alias);
$rc = dmGetItemInfo($alias, $itnum, $data);
$parser = xml_parser_create();
xml_parse_into_struct($parser, $data, $structure, $index);
xml_parser_free($parser);
$dmrotate = (isset($_GET["DMROTATE"])) ? $_GET["DMROTATE"] : '0';
if(isset($structure[$index['TITLE'][0]]["value"])) {
  $item_title = $structure[$index['TITLE'][0]]["value"];
}
?>
<div id="item-view">
  <div class="wrapper mod">
    <ul id="item-actions">
      <? if($pan_enabled): ?>
        <li class="zoom">
          ZOOM:
          <? if($image_zoomout_link != ''): ?>
            <a href="<?= $self ?>?<?= $image_zoomout_link ?>" class="zoom-out" title="Zoom Out"><img src="<?= SEEKING_MICHIGAN_HOST ?>/images/icon-item-zoom-out.gif" alt="zoom out" /></a>
          <? else: ?>
            <img src="<?= SEEKING_MICHIGAN_HOST ?>/images/icon-item-zoom-in.gif" alt="zoom in" />
          <? endif; ?>
          <? if($res['steps']): ?>
            <div id="zoom_slider_container">
              <select id="zoom" name="zoom" class="slider">
                <? foreach($res['steps'] as $level => $step): ?>
                  <option value="<?= $self ?>?<?= $step['url']; ?>" <? if($level == $res["current_zoom"]): ?>selected="selected"<? endif; ?>><?= sprintf("%.2f%%",$level); ?></option>
                <? endforeach; ?>
              </select>
            </div>
          <? endif; ?>
          <? if($image_zoomin_link != ''): ?>
            <a href="<?= $self ?>?<?= $image_zoomin_link ?>" class="zoom-in" title="Zoom In"><img src="<?= SEEKING_MICHIGAN_HOST ?>/images/icon-item-zoom-in.gif" alt="zoom in" /></a>
          <? else: ?>
            <img src="<?= SEEKING_MICHIGAN_HOST ?>/images/icon-item-zoom-in.gif" alt="zoom in" />
          <? endif; ?>
          <?= $image_pct_display ?>
        </li>
        <li class="fit">
          Fit:
          <a href="<?= $self ?>?<?= $image_full_link ?>" class="zoom-fit-normal" title="100%"><img src="<?= SEEKING_MICHIGAN_HOST ?>/images/icon-page-fit-normal.gif" alt="zoom fit normal" /><span class="action-tooltip">100%</span></a> 
          <a href="<?= $self ?>?<?= $image_fit_link ?>" class="zoom-fit-window" title="Window"><img src="<?= SEEKING_MICHIGAN_HOST ?>/images/icon-page-fit-window.gif" alt="zoom fit window" /><span class="action-tooltip">Fit Window</span></a> 
          <a href="<?= $self ?>?<?= $image_width_link ?>" class="zoom-fit-width" title="Width"><img src="<?= SEEKING_MICHIGAN_HOST ?>/images/icon-page-fit-width.gif" alt="zoom fit width" /><span class="action-tooltip">Fit Width</span></a>
        </li>
        <li class="rotate">
          Rotate: 
          <a href="<?= $self ?>?<?= $image_rotateleft_link ?>" class="rotate-left" title="Left"> <img src="<?= SEEKING_MICHIGAN_HOST ?>/images/icon-item-rotate-ccw.gif" alt="rotate left" /><span class="action-tooltip">Left</span></a> 
          <a href="<?= $self ?>?<?= $image_rotateright_link ?>" class="rotate-right" title="Right"> <img src="<?= SEEKING_MICHIGAN_HOST ?>/images/icon-item-rotate-cw.gif" alt="rotate right" /><span class="action-tooltip">Right</span></a>
        </li>
      <? endif; ?>
      <li class="zoom-pop">
        Full/Pop: 
        <a id="fullscreen_popup" href="/cgi-bin/getimage.exe?CISOROOT=<?=$alias?>&amp;CISOPTR=<?=$itnum?>&amp;DMSCALE=100" class="pop-full" title="<?= $doctitle ?> - full screen view">
          <img src="<?= SEEKING_MICHIGAN_HOST ?>/images/icon-zoom-full.gif" alt="rotate left" />
        </a> 
        <? $popout_url = '/cgi-bin/getimage.exe?CISOROOT='.$alias.'&amp;CISOPTR='.$itnum.'&amp;DMWIDTH='.$facebox_images['fullscreen_popup']['width'].'&amp;DMHEIGHT='.$facebox_images['fullscreen_popup']['height']; ?>
        <a href="<?= $popout_url ?>" class="pop-new" title="New Window" target="_new">
          <img src="<?= SEEKING_MICHIGAN_HOST ?>/images/icon-zoom-new.gif" alt="rotate left" />
        </a>
      </li>
    </ul>
    <div class="item-preview">
      <form name="mainimage" action="">
        <? if($seek_search_params): ?>
          <input type="hidden" name="search" value="<?= $seek_search_params ?>">
        <? endif ?>
        <input type="hidden" name="CISOROOT" value="<?=$image_cisoroot?>">
        <input type="hidden" name="CISOPTR" value="<?=$image_cisoptr?>">
        <input type="hidden" name="CISOSHOW" value="<?=$image_cisoptr?>">
        <input type="hidden" name="DMSCALE" value="<?=$image_scale?>">
        <input type="hidden" name="DMWIDTH" value="<?=$image_width?>">
        <input type="hidden" name="DMHEIGHT" value="<?=$image_height?>">
        <input type="hidden" name="DMFULL" value="<?=$image_full?>">
        <input type="hidden" name="DMX" value="<?=$image_x?>">
        <input type="hidden" name="DMY" value="<?=$image_y?>">
        <input type="hidden" name="DMTEXT" value="<?=$image_text?>">
        <input type="hidden" name="REC" value="<?=$dmrec?>">
        <input type="hidden" name="DMROTATE" value="<?=$dmrotate?>">
        <input type="image" id="item-preview-input" src="<?= $image_src ?>" border="0" alt="<?=htmlspecialchars($structure[$index['TITLE'][0]]['value'],ENT_QUOTES)?>" title="<?=htmlspecialchars($structure[$index['TITLE'][0]]['value'],ENT_QUOTES)?>">
      </form>
    </div>
    <? if($rc != -1): ?>
      <h2>Metadata</h2>
      <? display_metadata($alias, $parent_item, $conf); ?>
    <? endif; ?>
  </div>
</div>
<div id="sidebar">
  <? if($pan_enabled): ?>
    <div class="wrapper">
      <h3>Navigation</h3>
      <div id="item-nav">
        <form name="smallimage" action="">
          <? if($seek_search_params): ?><input type="hidden" name="search" value="<?= $seek_search_params ?>"><? endif ?>
          <input type="hidden" name="CISOROOT" value="<?=$image_cisoroot?>">
          <input type="hidden" name="CISOPTR" value="<?=$image_cisoptr?>">
          <input type="hidden" name="CISOSHOW" value="<?=$image_cisoptr?>">
          <input type="hidden" name="DMSCALE" value="<?=$image_currentscale?>">
          <input type="hidden" name="DMWIDTH" value="<?=$image_width?>">
          <input type="hidden" name="DMHEIGHT" value="<?=$image_height?>">
          <input type="hidden" name="DMFULL" value="0">
          <input type="hidden" name="DMOLDSCALE" value="<?=$image_oldscale?>">
          <input type="hidden" name="DMX" value="0">
          <input type="hidden" name="DMY" value="0">
          <input type="hidden" name="DMTEXT" value="<?=$image_text?>">
          <input type="hidden" name="REC" value="<?=$dmrec?>">
          <input type="hidden" name="DMROTATE" value="<?=$dmrotate?>">
          <input type="image" src="<?= $image_guide_src ?>" width="<?=$image_thumbnail_width?>" border="0" alt="<?=htmlspecialchars($structure[$index['TITLE'][0]]['value'],ENT_QUOTES)?>">
        </form>
      </div>
    </div>
  <? endif; ?>
  <? prev_next_compound($isthisCompoundObject, $show_all, $previous_item, $next_item, $current_item_num, 
                        $totalitems, $encoded_seek_search_params, $search_position, $alias, $parent_itnum); ?>
  <? prev_next_search($seek_search_params,$search_position); ?>
</div>