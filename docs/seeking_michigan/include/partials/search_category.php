<form id="browse-search" action="seek_results.php">
  <? foreach(app()->helper('seek_results')->search_fields_without_alias($search) as $field_name => $field_value): ?>
    <input type="hidden" name="<?= $field_name ?>" value="<?= $field_value ?>" />
  <? endforeach; ?>
  <p>
    Browsing <strong><?= $search->total; ?></strong> items in 
    <select name="CISOROOT">
      <option value="all" <? if($search->search_all): ?>selected="selected"<? endif; ?>>
        All Collections</option>
      <? foreach($collections as $collection): ?>
        <option value="<?= $collection['alias']; ?>" <? if(!$search->search_all && in_array($collection['alias'], $search->search_alias)): ?>selected="selected"<? endif; ?>>
          <?= $collection['name']; ?></option>
      <? endforeach; ?>
    </select>
    <input type="image" src="<?= SEEKING_MICHIGAN_HOST ?>/images/search-button.gif" value=" " />  
    Or use <a href="seek_advanced.php">Advanced Search &raquo; </a>
  </p>
</form>
