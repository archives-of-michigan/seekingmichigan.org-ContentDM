<link rel="stylesheet" type="text/css" href="<?= SEEKING_MICHIGAN_HOST ?>/css/jquery.lightbox-0.5.css" media="screen" />
<script type="text/javascript" src="<?= SEEKING_MICHIGAN_HOST ?>/js/jquery.lightbox-0.5.pack.js"></script>
<script type="text/javascript">
  function full_screen_image_url(alias, itnum, image_width, image_height, type) {
    var scaling = 100;
    var window_width = $(window).width() - 20;
    var window_height = $(window).height() - 20;
  
    var full_width = Math.min(image_width, window_width);
    var full_height = Math.min(image_height, window_height);
  
    if(full_height == window_height) {
      scaling = Math.round((window_height / image_height) * 100);
    }
  
    return('http://seekingmichigan.cdmhost.com/cgi-bin/getimage.exe?CISOROOT=' + alias + '&CISOPTR=' + itnum + '&REC=1&DMSCALE=' + scaling + '&DMWIDTH=' + full_width + '&DMHEIGHT=' + full_height + '&type=.' + type);
  }

  $(function() {
    <? if($lightbox_images): ?>
      <? foreach($lightbox_images as $id => $data): ?>
          $('#<?= $id ?>').each(function() {
            this.href = full_screen_image_url('<?=$data['alias']?>', <?=$data['itnum']?>, <?= $data['width'] ?>, <?= $data['height'] ?>, '<?=$data['type']?>');
          });
      <? endforeach; ?>
    <? endif; ?>
    
    $('.pop-full').lightBox({
      imageLoading: '<?= SEEKING_MICHIGAN_HOST ?>/images/lightbox-ico-loading.gif',
      imageBtnClose: '<?= SEEKING_MICHIGAN_HOST ?>/images/lightbox-btn-close.gif',
      imageBtnPrev: '<?= SEEKING_MICHIGAN_HOST ?>/images/lightbox-btn-prev.gif',
      imageBtnNext: '<?= SEEKING_MICHIGAN_HOST ?>/images/lightbox-btn-next.gif'
    });
  });
</script>
