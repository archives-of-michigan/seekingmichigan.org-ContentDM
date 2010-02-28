<?
include("config.php");

$entries = array();

$search = Search::from_params($_GET);
$field = $search->field;
$sortby = $search->sortby;
$maxrecs = $search->maxrecs;
$start = $search->start;

$record = $search->results();

$totalpages = ceil($search->total / $maxrecs);
$i=0;
while($i<=$totalpages){
  $entries[]=$i;
  $i++;
}
(count($record) > 0)?$isRes = true:$isRes = false;

$collections = dmGetCollectionList();

$breadcrumbs = array('Home' => SEEKING_MICHIGAN_HOST, 'Seek' => 'seek_advanced.php', 'Search Results' => '');
app()->partial('header', 
  array('body_class' => 'seek',
    'breadcrumbs' => $breadcrumbs, 
    'title' => 'Results : Seek &mdash; Seeking Michigan'));
?>
<div id="section-header">
  <h1><a href="seek_advanced.php">Seek</a></h1>
</div>
<div id="main-content">
  <div class="wrapper">
    <? $search->terms; ?>
    <? if(!$search->is_default_search()): ?>
      <h1>
        Search Results for:
        <? foreach($search->terms() as $term): ?>
          <a href="seek_results.php?<?= $search->term_search_string($term) ?>"><?= $term; ?></a>
        <? endforeach; ?>
      </h1>
    <? endif; ?>
    <div class="search-results">
      <div class="wrapper">
    <h2>Collection Results</h2>
    <p class="intro">The results for your search are listed below.  You can narrow your search results by following the links listed for each category.</p>
    <? app()->partial('search_category', 
                      array('search' => $search, 'collections' => $collections)); ?>
    <div class="paginate">
      <? include('seek/results_sub.php'); ?>
    </div>
    <? include('seek/results_view.php'); ?>
    <div class="paginate">
      <? include('seek/results_sub.php'); ?>
    </div>
    </div>
  </div>
  </div>
</div>
<? app()->partial('footer'); ?>
