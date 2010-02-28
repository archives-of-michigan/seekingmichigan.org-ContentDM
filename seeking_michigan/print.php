<?php
include("config.php");

$alias = (isset($_GET["CISOROOT"])) ? $_GET["CISOROOT"] : 0;
$itnum = (isset($_GET["CISOPTR"])) ? $_GET["CISOPTR"] : 0;

$display_item = get_item($alias, $itnum);
$compound_object = $current_item->parent_item();

if (in_array($display_item['filetype'],$isImage)) {
  $base_type = 'image';
} else if($display_item['filetype'] == 'cpd') {
  $base_type = 'compound';
  $parent_itnum = $display_item['ptr'];
} else if($display_item['filetype'] == 'pdf') {
  $base_type = 'pdf';
} else {
  $base_type = 'non-printable';
}

// get the format of the image - 8in wide for protrait layout, 10.5in for landscape
if($base_type == 'image') {
  if($display_item['settings']['height'] > $display_item['settings']['width']) {
    $size = "8.5in 11in";
  } else {
    $size = "11in 8.5in";
  }
} elseif($base_type == 'compound') {
  $max_width = 0;
  $max_height = 0;
  foreach($compound_items as $item) {
    $max_width = max($max_width, $item['settings']['width']);
    $max_height = max($max_height, $item['settings']['height']);
  }
  if($max_height > $max_width) {
    $size = "8.5in 11in";
  } else {
    $size = "11in 8.5in";
  }
} else {
  $img_width = "0";
}
?>
<? if($base_type == 'pdf'): ?>
  <? header("Location: http://seekingmichigan.cdmhost.com/cgi-bin/showpdf.exe?CISOROOT=".$alias."&CISOPTR=".$itnum); ?>
<? else: ?>
  <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
  <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
      <script type="text/javascript" src="<?=SEEKING_MICHIGAN_HOST?>/js/jquery.js"></script>
      <script type="text/javascript">
        $(window).load(function(){
          window.print();
        });
      </script>
      <style type="text/css" media="print">
        @page { size <?= $size ?>; margin: 2cm }
        img { width: 100%; height: 100%; }
      </style>
    </head>
    <body>
      <? if($base_type == 'image'): ?>
        <img src="<?= $display_item['settings']['full_image'] ?>" />
      <? elseif($base_type == 'compound'): ?>
        <? for($i = 1; $i < count($compound_items); $i++): ?>
          <? $item = get_sub_item($alias, $compound_items, $i, $compound_items[$i]['ptr']); ?>
          <? if($item['settings']): ?>
            <img src="<?= $item['settings']['full_image'] ?>" />
          <? endif; ?>
        <? endfor; ?>
      <? elseif($base_type == 'non-printable'): ?>
        This item is not printable in a web browser.  You can download the item and print 
        it using the appropriate software <a href="/cgi-bin/showfile.exe?CISOROOT=<?=$alias?>&amp;CISOPTR=<?=$itnum?>">here</a>.
      <? endif; ?>
    </body>
  </html>
<? endif; ?>
