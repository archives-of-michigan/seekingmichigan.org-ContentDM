<?
$month = getdate();
$month = $month['mon'];
if($month == 12 || ($month >= 1 && $month < 3)) { // winter
  $scenes = array('ice','snow-trees');
} elseif($month == 3 || $month == 4) {
  $scenes = array('summer-fern');
} elseif($month > 4 && $month <= 6) {  // spring, summer
  $scenes = array('ship','summer-fern','summer-treeline','wheatgrass');
} else {  // fall
  $scenes = array('fall-treeline','ship','snow-sun');
}

$scene = $scenes[rand(0,count($scenes) - 1)];
?>
<style type="text/css" media="screen">
#callout { background: url(http://seekingmichigan.org/images/scenes-<?= $scene; ?>-top.jpg) no-repeat 50% -0px; }
#footer { background:  url(http://seekingmichigan.org/images/scenes-<?= $scene; ?>-bottom.jpg) no-repeat 50% 15%; }
</style>