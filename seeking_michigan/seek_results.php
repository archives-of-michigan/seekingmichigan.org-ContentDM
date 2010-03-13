<?
include("config.php");
include('lib/paginator.php');

$search = Search::from_params($_GET);
$collections = dmGetCollectionList();
$results = $search->results();
$search_url = $_SERVER['QUERY_STRING'];

$breadcrumbs = array('Home' => SEEKING_MICHIGAN_HOST, 
                     'Seek' => 'seek_advanced.php', 'Search Results' => '');
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
          <a href="seek_results.php?<?= $search->term_search_string($term) ?>">
            <?= $term; ?>
          </a>
        <? endforeach; ?>
      </h1>
    <? endif; ?>
    <div class="search-results">
      <div class="wrapper">
        <h2>Collection Results</h2>
        <p class="intro">
          The results for your search are listed below.  You can narrow your 
          search results by following the links listed for each category.</p>
        <? app()->partial('search_category', 
                          array('search' => $search, 
                          'collections' => $collections)); ?>
        <div class="paginate">
          <? app()->partial('seek_pagination', 
                            array('search' => $search)); ?>
        </div>
        <? if(count($results) > 0): ?>
          <ol class="search-results mod" start="<?= $search->start; ?>">
            <? $counter = 0; ?>
            <? foreach($results as $item): ?>
              <? $item_num = $search->start + $counter; ?>
              <li>
                <? if($item->alias == '/p129401coll7'): ?>
                  <h3>
                    <a href="<?= $item->view_link($search_url, $item_num); ?>"
                       title="<?= $item->alt_title(); ?>">
                      <img src="<?= $item->thumbnail_path(); ?>"
                           alt="<?= $item->alt_title(); ?>"
                           title="<?= $item->alt_title(); ?>" />
                      <?= $item->description;  #first name ?>
                      <?= $item->creator;  #last name ?>
                    </a>
                  </h3>
                  <p class="byline">
                    <?= $item->type; ?> <?= $item->date; ?>, 
                    <?= $item->format; ?></p>
                  <p>
                    <? if($item->subject): ?>
                      <?= $item->subject;  #city/village/township ?>,
                    <? endif; ?>
                    <? if($item->title): ?>
                      <?= $item->title;  #county ?> County
                    <? endif; ?>
                  </p>
                <? else: ?>
                  <h3>
                    <a href="<?= $item->view_link($search_url, $item_num); ?>"
                       title="<?= $item->alt_title(); ?>">
                      <img src="<?= $item->thumbnail_path(); ?>"
                           alt="<?= $item->alt_title(); ?>"
                           title="<?= $item->alt_title(); ?>" />
                      <?= $item->title(); ?>
                    </a>
                  </h3>
                  <p class="byline"><?= $item->subject; ?></p>
                  <p><?= $item->description; ?></p>
                <? endif; ?>
              </li>
              <? $counter += 1; ?>
            <? endforeach; ?>
          </ol>
        <? else: ?>
          No Items matched your criteria
        <? endif; ?>
      </div>
      <div class="paginate">
         <? app()->partial('seek_pagination', 
                           array('search' => $search)); ?>
      </div>
    </div>
  </div>
  </div>
</div>
<? app()->partial('footer'); ?>
