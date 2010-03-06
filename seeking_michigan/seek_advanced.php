<?
include("config.php");

$breadcrumbs = array('Home' => SEEKING_MICHIGAN_HOST, 'Seek' => 'seek_advanced.php', 'Advanced Search' => '');
app()->partial('header', 
  array('body_class' => 'seek',
    'breadcrumbs' => $breadcrumbs, 
    'js_includes' => array('advanced_search'),
    'title' => 'Advanced: Seek &mdash; Seeking Michigan'));

$collections = dmGetCollectionList();
?>
<div id="section-header">
  <h1><a href="seek_advanced.php">Seek</a></h1>
</div>
<div id="main-content">
  <div class="wrapper">
    <div id="advanced-search">
      <div class="wrapper">
        <h2>Advanced Search</h2>
        <p class="intro">
          You can construct a search by combining up to four search criteria and by selecting a list of collections that 
          will be searched.  Any criteria left blank will not be used in the search.  To select which collections to search, 
          highlight one or more collections in the left-hand box (on Windows and Linux you can CTRL+Click to select multiple 
          collections, Command+Click on Mac OS). Then click the "&gt;&gt;" button to add the selected collections to the 
          right-hand box.
        </p>
        <h3>Build Your Advanced Search</h3>
        <form action="seek_results.php" class="mod">
          <? for($i = 1; $i <= 4; $i++) { 
            app()->partial('criterion', array('num' => $i));
          } ?>
          <fieldset class="special mod">
            <legend>Add Collections:</legend>
            <div id="select-source" class="mod">
              <select name="excluded_collections" id="excluded_collections" multiple="multiple" size="6">
                <? foreach($collections as $collection): ?>
                  <option id="option_<?= trim($collection['alias'],'/') ?>" value="<?= $collection['alias']; ?>"><?= $collection['name']; ?></option>
                <? endforeach; ?>
              </select>
              <input type="button" id="select-all" value="Select All" />
            </div>
            
            <label for="add_collections" class="hidden">Add to</label>
            <input type="button" id="add_collections" name="add_collections" class="mod" value=" &raquo; " />
          
            <label for="remove_collections" class="hidden">Take from</label>
            <input type="button" id="remove_collections" name="remove_collections" class="mod" value=" &laquo; " />
          
            <div id="select-pool" class="mod">
              <select name="CISOROOT" id="included_collections" multiple="multiple" size="6">
                <option value="all"></option>
              </select>
              <input type="button" id="clear-all" value="Clear All" />
            </div>
            <input type="image" id="advanced-search-button" src="<?= SEEKING_MICHIGAN_HOST ?>/images/search-button.png" value="" />
          </fieldset>
        </form>
      </div>
    </div>
  </div>
</div>
<? app()->partial('footer'); ?>
