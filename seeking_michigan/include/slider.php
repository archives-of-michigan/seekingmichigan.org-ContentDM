<link href="<?= SEEKING_MICHIGAN_HOST ?>/css/ui.slider.extras.css" media="screen" rel="stylesheet" type="text/css"/>
<link href="<?= SEEKING_MICHIGAN_HOST ?>/css/smoothness/jquery-ui-1.7.custom.css" media="screen" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?= SEEKING_MICHIGAN_HOST ?>/js/jquery-ui-1.7.custom.min.js"></script>
<script type="text/javascript">
  $(document).ready(function(){
    $('.slider').selectToUISlider({ labelSrc: 'text', sliderOptions: { change: slider_changed }, labels: 0 });
  });
  
  function slider_changed(e, ui) {
    window.location = $('.slider').find('option').eq(ui.value).attr('value');
  }
</script>
<style type="text/css">
  .slider { display: none; }
</style>