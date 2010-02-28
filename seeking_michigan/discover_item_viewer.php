<? 
include("config.php");
include('discover/meta_scr.php');

$alias = (isset($_GET["CISOROOT"])) ? $_GET["CISOROOT"] : 0;
$trimmed_alias = trim($alias,'/');
$requested_itnum = (isset($_GET["CISOPTR"])) ? $_GET["CISOPTR"] : 0;

$current_item = ItemFactory::create($alias, $requested_itnum, $_GET['CISOSHOW']);

$show_all = (isset($_GET["show_all"])) ? $_GET["show_all"] : false;

if(isset($_GET['search'])) {
  $seek_search_params = $_GET['search'];
  $encoded_seek_search_params = urlencode($seek_search_params);
} else if(isset($_POST['search'])) {
  $seek_search_params = $_POST['search'];
  $encoded_seek_search_params = urlencode($seek_search_params);
}

$search_position = isset($_GET['search_position']) ? $_GET['search_position'] : 0;

if(get_class($current_item) == 'Image') {
  include("discover/pan_scr.php");
  $js_includes = array('jquery-ui-1.7.1.custom.min', 'jquery.event.drag-1.5.min', 'dmmonocle.min');
  $css_includes = array('dmmonocle','smoothness/jquery-ui-1.7.custom');
}
define("FACEBOX",'display');

$collection_url = SEEKING_MICHIGAN_HOST.'/discover-collection?collection='.$trimmed_alias;
$breadcrumbs = array(
  'Home' => SEEKING_MICHIGAN_HOST,
  'Discover' => SEEKING_MICHIGAN_HOST.'/discover',
  'Collections' => SEEKING_MICHIGAN_HOST.'/discover',
  $collection_name => $collection_url, 
  'Item Viewer' => '');

app()->partial('header', 
  array('body_class' => 'discover',
    'breadcrumbs' => $breadcrumbs, 
    'css_includes' => $css_includes,
    'js_includes' => $js_includes,
    'title' => 'Viewer  &mdash; Seeking Michigan &mdash; '.$current_item->title,
    'current_item' => $current_item));
?>
<div id="section-header">
  <h1><a href="<?= SEEKING_MICHIGAN_HOST ?>/discover">Discover</a></h1>
</div>
<div id="main-content">
  <div class="wrapper">
    <div  id="item-header">
      <div class="wrapper">
        <h2>Collection: <a href="<?= SEEKING_MICHIGAN_HOST ?>/discover-collection?collection=<?= $trimmed_alias ?>" title="texthere"><?= $collection_name ?></a></h2>
        <h3>Item Viewer: <?= $current_item->title ?></h3>
        <ul class="page-actions">
          <? if($seek_search_params): ?>
            <li class="action-back"><a href="seek_results.php?<?= $seek_search_params ?>">Back to results</a></li>
          <? endif; ?>
          <li class="action-url">
            <a href="#permalink" rel="facebox">Copy Item URL</a>
            <div id="permalink" style="display:none;"> 
              The URL for this item is <span class="citation"><?= SEEKING_MICHIGAN_HOST ?>/u?<?= $_GET['CISOROOT'] ?>,<?= $_GET['CISOPTR'] ?></span>
            </div>
          </li>
          <? if($current_item->is_printable()): ?>
            <li class="action-print"><a href="<?= $current_item->print_link(); ?>">Printable Version</a></li>
          <? endif; ?>
          <li class="share-this"><!-- AddThis Button BEGIN --><script type="text/javascript">addthis_pub  = 'seekingmichigan'; addthis_offset_top = -10; addthis_offset_left = 5; addthis_options = 'delicious, email, digg, facebook, google, technorati, twitter, myspace,  more';</script><a href="http://www.addthis.com/bookmark.php" onmouseover="return addthis_open(this, '', '[URL]', '[TITLE]')" onmouseout="addthis_close()" onclick="return addthis_sendto()">Share This</a><script type="text/javascript" src="http://s7.addthis.com/js/152/addthis_widget.js"></script><!-- AddThis Button END --></li>
          <li class="view-collection"><a href="seek_results.php?CISOROOT=<?= $current_item->alias ?>">View Collection</a></li>
        </ul>
      </div>
    </div>
    <? if($show_all) {
      foreach($current_item->parent_item()->items() as $subitem) {
        app()->partial('basic_view', 
          array(
            'current_item' => $subitem, 
            'seek_search_params' => $seek_search_params, 
            'search_position' => $search_position, 
            'seek_search_params' => $seek_search_params));
      }
    } else if(get_class($current_item) == 'Image'){
      app()->partial('pan_view', array('current_item' => $current_item));
    } else {
        app()->partial('basic_view', 
          array(
            'current_item' => $current_item, 
            'seek_search_params' => $seek_search_params, 
            'search_position' => $search_position, 
            'seek_search_params' => $seek_search_params));
    } ?>
  </div>
</div>
<div id="main-whitebox-left"></div>
<div id="main-whitebox-right"></div>
<? app()->partial('footer'); ?>
