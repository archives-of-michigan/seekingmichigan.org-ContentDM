<?php
include("config.php");

$alias = (isset($_GET["CISOROOT"])) ? $_GET["CISOROOT"] : 0;
$itnum = (isset($_GET["CISOPTR"])) ? $_GET["CISOPTR"] : 0;

$current_item = ItemFactory::create($alias, $itnum);

// get the format of the image - 8in wide for protrait layout, 10.5in for landscape
if(get_class($current_item) == 'CompoundObject') {
  if($current_item->is_overall_layout_portrait()) {
    $size = "8.5in 11in";
  } else {
    $size = "11in 8.5in";
  }
} elseif(get_class($current_item) == 'Image') {
  if($current_item->is_portrait()) {
    $size = "8.5in 11in";
  } else {
    $size = "11in 8.5in";
  }
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
  <head>
    <script type="text/javascript" src="<?=SEEKING_MICHIGAN_HOST?>/js/jquery.js"></script>
    <script type="text/javascript">
      $(window).load(function(){
        window.print();
      });
    </script>
    <? if($size): ?>
      <style type="text/css" media="print">
        @page { size <?= $size ?>; margin: 2cm }
        img { width: 100%; height: 100%; }
      </style>
    <? endif; ?>
  </head>
  <body>
    <? if(get_class($current_item) == 'CompoundObject'): ?>
      <? foreach($current_item->items() as $item): ?>
        <img src="<?= $item->full_image_path(); ?>">
      <? endforeach; ?>
    <? elseif(get_class($current_item) == 'Image'): ?>
      <img src="<?= $current_item->full_image_path(); ?>">
    <? else: ?>
      This item is likely not printable from your web browser.  You can download the item and print 
      it using the appropriate software <a href="<?= $current_item->download_link(); ?>">here</a>.
    <? endif; ?>
  </body>
</html>
