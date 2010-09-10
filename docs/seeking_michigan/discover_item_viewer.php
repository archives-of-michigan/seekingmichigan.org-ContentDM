<? 
include("config.php");

$collection = Collection::from_alias($_GET['CISOROOT']);
$current_item = ItemFactory::create($_GET['CISOROOT'], $_GET['CISOPTR'],
                                    $_GET['CISOSHOW']);
$show_all = (isset($_GET["show_all"])) ? $_GET["show_all"] : false;

if(isset($_GET['search'])) {
  $search_status = new SearchStatus($_GET['search']);
}

$css_includes = array('screen/viewer');
if(get_class($current_item) == 'Image') {
  $js_includes = array('jquery-ui-1.7.1.custom.min', 
    'jquery.event.drag-1.5.min', 'dmmonocle.min',
    'compound_object_browser');
  $css_includes = array_merge($css_includes, array('dmmonocle','smoothness/jquery-ui-1.7.custom'));
}
define("FACEBOX",'display');

$breadcrumbs = array(
  'Home' => SEEKING_MICHIGAN_HOST,
  'Discover' => SEEKING_MICHIGAN_HOST.'/discover',
  'Collections' => SEEKING_MICHIGAN_HOST.'/discover',
  $collection->name => $collection->url(), 
  'Item Viewer' => '');

app()->partial('header', 
  array('body_class' => 'discover',
    'breadcrumbs' => $breadcrumbs, 
    'css_includes' => $css_includes,
    'js_includes' => $js_includes,
    'title' => 'Viewer &mdash; Seeking Michigan &mdash; '.$current_item->title(),
    'current_item' => $current_item));
?>
<div id="section-header">
  <h1><a href="<?= SEEKING_MICHIGAN_HOST ?>/discover">Discover</a></h1>
</div>
<div id="main-content">
  <div class="wrapper">
    <div  id="item-header">
      <div class="wrapper">
        <h2>
          Collection: 
          <a href="<?= $collection->url(); ?>" title="texthere">
            <?= $collection->name; ?>
          </a>
        </h2>
        <h3><?= $current_item->title() ?></h3>
        <ul class="page-actions">
          <? if($search_status): ?>
            <li class="action-back">
              <a href="<?= $search_status->all_results_link(); ?>">
                Back to results
              </a>
            </li>
          <? endif; ?>
          <li class="action-url">
            <a href="#permalink" rel="facebox">Copy Item URL</a>
            <div id="permalink" style="display:none;"> 
              The URL for this item is 
              <span class="citation">
                <?= SEEKING_MICHIGAN_HOST ?>/u?<?= $current_item->alias ?>,<?= $current_item->itnum ?>
              </span>
            </div>
          </li>
          <? if($current_item->is_printable()): ?>
            <li class="action-print">
              <a href="<?= $current_item->print_link(); ?>">Printable Version</a>
            </li>
          <? endif; ?>
          <li class="share-this"><!-- AddThis Button BEGIN --><script type="text/javascript">addthis_pub  = 'seekingmichigan'; addthis_offset_top = -10; addthis_offset_left = 5; addthis_options = 'delicious, email, digg, facebook, google, technorati, twitter, myspace,  more';</script><a href="http://www.addthis.com/bookmark.php" onmouseover="return addthis_open(this, '', '[URL]', '[TITLE]')" onmouseout="addthis_close()" onclick="return addthis_sendto()">Share This</a><script type="text/javascript" src="http://s7.addthis.com/js/152/addthis_widget.js"></script><!-- AddThis Button END --></li>
          <li class="view-collection">
            <a href="seek_results.php?CISOROOT=<?= $current_item->alias ?>">
              View Collection
            </a>
          </li>
          <? if($current_item->is_child()): ?>
            <li class="browse-document">
              <a id="compound_object_pages"
                 href="<?= $current_item->parent_item()->browse_link($search_status); ?>">
                Browse Document
              </a>
            </li>
          <? endif; ?>
        </ul>
      </div>
    </div>
    <? if(get_class($current_item) == 'Image'){
      app()->partial('pan_view', array('current_item' => $current_item,
                                       'search_status' => $search_status));
    } else {
        app()->partial('basic_view', 
          array('current_item' => $current_item, 
                'search_status' => $search_status,
                'show_all' => $show_all));
    } ?>
    <div id="sidebar">
      <? if($current_item->is_child()): ?>
        <ul class="sidenav">
          <li>
          </li>
        </ul>
      <? endif; ?>
      <? 
         if($search_status) {
            app()->partial('previous_next', 
              array('search_status' => $search_status));
         }
      ?>
    </div>
  </div>
</div>
<div id="main-whitebox-left"></div>
<div id="main-whitebox-right"></div>
<? app()->partial('footer', 
                  array(
                    'hidden_partials' => array(
                      'compound_object_list' => array(
                        'parent_item' => $current_item->parent_item(),
                        'search_status' => $search_status,
                        'current_itnum' => $current_item->itnum)))); ?>
