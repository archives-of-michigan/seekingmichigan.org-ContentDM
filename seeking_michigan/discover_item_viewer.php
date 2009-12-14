<? 
include('discover/partial.php');
include("config.php");
include('discover/meta_scr.php');

$alias = (isset($_GET["CISOROOT"])) ? $_GET["CISOROOT"] : 0;
$trimmed_alias = trim($alias,'/');
$requested_itnum = (isset($_GET["CISOPTR"])) ? $_GET["CISOPTR"] : 0;
$show_all = (isset($_GET["show_all"])) ? $_GET["show_all"] : false;
$printable = false;

if(isset($_GET['search'])) {
  $seek_search_params = $_GET['search'];
  $encoded_seek_search_params = urlencode($seek_search_params);
} else if(isset($_POST['search'])) {
  $seek_search_params = $_POST['search'];
  $encoded_seek_search_params = urlencode($seek_search_params);
}

$search_position = isset($_GET['search_position']) ? $_GET['search_position'] : 0;
$parent_item = get_item($alias, $requested_itnum);
$parent_filetype = GetFileExt($parent_item['structure'][$parent_item['index']["FIND"][0]]["value"]);

dmGetCollectionParameters($alias, $collection_name, $collection_path);
$parent_object_ptr = GetParent($alias, $requested_itnum, $collection_path);
if($parent_object_ptr != -1) {
  $parent_itnum = $parent_object_ptr;
  include("discover/comp_obj_scr.php");
  $isthisCompoundObject = true;
  $display_item = $current_item;
  $itnum = $display_item['ptr'];
  $print_item = $display_item;
} else if($parent_filetype == 'cpd') {
  $parent_itnum = $requested_itnum;
  include("discover/comp_obj_scr.php");
  $isthisCompoundObject = true;
  $display_item = $current_item;
  $itnum = $display_item['ptr'];
  $printable = true;
  if($show_all) { $print_item = $parent_item; } else { $print_item = $display_item; }
} else if($parent_filetype == 'pdf') {
  $isthisCompoundObject = false;
  $display_item = $parent_item;
  $itnum = $requested_itnum;
  $printable = true;
  $print_item = $display_item;
} else {
  $isthisCompoundObject = false;
  $display_item = $parent_item;
  $itnum = $requested_itnum;
  $print_item = $display_item;
}

$filetype = GetFileExt($display_item['structure'][$display_item['index']["FIND"][0]]["value"]);
$doctitle = $display_item['structure'][$display_item['index']["TITLE"][0]]["value"];

$isthisImage = in_array($filetype,$isImage);
if($isthisImage){
  dmGetCollectionImageSettings($alias, $pan_enabled, $minjpegdim, $zoomlevels, $maxderivedimg, $viewer, $docviewer, $compareviewer, $slideshowviewer);
  $filename = $type = $width = $height = '';
  $image_info = dmGetImageInfo($alias, $itnum, $filename, $type, $width, $height);
  include("discover/pan_scr.php");
  define("FACEBOX",'display');
  define("LIGHTBOX",'display');
  define('DMMONOCLE','display');
  $js_includes = array('jquery-ui-1.7.1.custom.min', 'jquery.event.drag-1.5.min', 'dmmonocle.min');
  $css_includes = array('dmmonocle','smoothness/jquery-ui-1.7.custom');
  
  $lightbox_images = array(
    'fullscreen_popup' => array(
      'alias' => $alias,
      'itnum' => $itnum
    )
  );
  
  // full-screen view settings
  if($lightbox_images) {
    foreach($lightbox_images as $id => $data) {
      $lightbox_images[$id]['filename'] = $filename;
      $lightbox_images[$id]['type'] = $type;
      $lightbox_images[$id]['width'] = $width;
      $lightbox_images[$id]['height'] = $height;
    }
  }
  $printable = true;
}

$title = 'Viewer  &mdash; Seeking Michigan &mdash; '.$doctitle;
$collection_url = SEEKING_MICHIGAN_HOST.'/discover-collection?collection='.$trimmed_alias;
$breadcrumbs = array(
  'Home' => SEEKING_MICHIGAN_HOST,
  'Discover' => SEEKING_MICHIGAN_HOST.'/discover',
  'Collections' => SEEKING_MICHIGAN_HOST.'/discover',
  $collection_name => $collection_url, 
  'Item Viewer' => '');
define("BODY_CLASS","discover");
include('header.php');
?>
<div id="section-header">
  <h1><a href="<?= SEEKING_MICHIGAN_HOST ?>/discover">Discover</a></h1>
</div>
<div id="main-content">
  <div class="wrapper">
    <div  id="item-header">
      <div class="wrapper">
        <h2>Collection: <a href="<?= SEEKING_MICHIGAN_HOST ?>/discover-collection?collection=<?= $trimmed_alias ?>" title="texthere"><?= $collection_name ?></a></h2>
        <h3>Item Viewer: <?= $doctitle ?></h3>
        <ul class="page-actions">
          <? if($seek_search_params): ?>
            <li class="action-back"><a href="/seeking_michigan/seek_results.php?<?= $seek_search_params ?>">Back to results</a></li>
          <? endif; ?>
          <li class="action-url">
            <a href="#permalink" rel="facebox">Copy Item URL</a>
            <div id="permalink" style="display:none;"> 
              The URL for this item is <span class="citation"><?= SEEKING_MICHIGAN_HOST ?>/u?<?= $_GET['CISOROOT'] ?>,<?= $_GET['CISOPTR'] ?></span>
            </div>
          </li>
          <? if($printable): ?>
            <li class="action-print"><a href="<?= print_link($print_item) ?>">Printable Version</a></li>
          <? endif; ?>
          <li class="share-this"><!-- AddThis Button BEGIN --><script type="text/javascript">addthis_pub  = 'seekingmichigan'; addthis_offset_top = -10; addthis_offset_left = 5; addthis_options = 'delicious, email, digg, facebook, google, technorati, twitter, myspace,  more';</script><a href="http://www.addthis.com/bookmark.php" onmouseover="return addthis_open(this, '', '[URL]', '[TITLE]')" onmouseout="addthis_close()" onclick="return addthis_sendto()">Share This</a><script type="text/javascript" src="http://s7.addthis.com/js/152/addthis_widget.js"></script><!-- AddThis Button END --></li>
          <li class="view-collection"><a href="/seeking_michigan/seek_results.php?CISOROOT=<?= $alias ?>">View Collection</a></li>
        </ul>
      </div>
    </div>
    <? if($_GET['show_all']) {
      for($i = 1; $i < count($compound_items); $i++){
        $current_item = get_sub_item($alias, $compound_items, $i, $requested_itnum);
        Partial::basic_view($alias, $current_item, $parent_item, $isImage, $seek_search_params, $search_position, $seek_search_params);
      }
    } else if($isthisImage){
      include("discover/pan_view.php");
    } else {
      Partial::basic_view($alias, $display_item, $parent_item, $isImage, $seek_search_params, $search_position, $seek_search_params);
    } ?>
  </div>
</div>
<div id="main-whitebox-left"></div>
<div id="main-whitebox-right"></div>
<? include('footer.php'); ?>
