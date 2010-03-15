<div  id="item-view">
  <div class="wrapper mod">
    <? if($current_item->is_pdf()): ?>
      <ul id="item-actions">
        <li class="zoom-pop">
          Full/Pop:
          <a href="<?= $current_item->print_link(); ?>" class="pop-full" 
             title="Fullscreen">
            <img src="<?= SEEKING_MICHIGAN_HOST ?>/images/icon-zoom-full.gif" 
                 alt="rotate left" />
          </a> 
          <a href="<?= $current_item->print_link(); ?>" class="pop-new"
             title="New Window" target="_new">
            <img src="<?= SEEKING_MICHIGAN_HOST ?>/images/icon-zoom-new.gif" 
                 alt="rotate left" /></a>
        </li>
      </ul>
    <? endif; ?>
    <div class="item-preview">
      <? if($current_item->is_pdf()): ?>
        <a href="<?= $current_item->print_link(); ?>">Download PDF</a>
        <iframe src="<?= $current_item->download_link(); ?>" 
                style="width: 100%; height: 6in"></iframe>
      <? elseif($current_item->is_audio()): ?>
        <audio src="<?= $current_item->download_link(); ?>" autoplay="true"
               controls="true" style="display: block; margin: 1em 30%;">
          <script language="JavaScript" 
                  src="<?= SEEKING_MICHIGAN_HOST ?>/js/audio-player.js"></script>
          <object type="application/x-shockwave-flash" 
                  data="<?= SEEKING_MICHIGAN_HOST ?>/wp-content/themes/airbag/swf/player.swf" 
                  style="display: block; margin: 10px auto;" height="24" width="290">
            <param name="movie" 
                   value="<?= SEEKING_MICHIGAN_HOST ?>/wp-content/themes/airbag/swf/player.swf">
            <param name="FlashVars" 
                   value="playerID=1&amp;soundFile=<?= urlencode($current_item->download_link()); ?>">
            <param name="quality" value="high">
            <param name="menu" value="true">
            <param name="wmode" value="transparent">
          </object>
        </audio>
        <a href="<?= $current_item->download_link(); ?>">Download audio file</a>
      <? elseif(get_class($current_item) == 'Image'): ?>
        <a href="<?= $current_item->search_view_link($search_status); ?>">
          <img src="<?= $current_item->view_link(); ?>" />
        </a>
      <? else: ?>
      <? var_dump($current_item); ?>
        <a href="<?= $current_item->download_link(); ?>">Download file</a>
      <? endif; ?>
    </div>
    <h2>Metadata</h2>
    <? app()->partial('metadata', array('item' => $current_item)); ?>
  </div><!-- end .wrapper -->
</div><!-- end .item-view -->
