<form id="browse-search" action="seek_results.php">
  <? foreach($search_fields as $field_name => $field_value): ?>
    <input type="hidden" name="<?= $field_name ?>" value="any" />
  <? endforeach; ?>
  <p>
    Browsing <strong><?= $num_records_this_page; ?></strong> items in 
    <select name="CISOROOT">
      <option value="all" <? if($_GET['CISOROOT'] == 'all'): ?>selected="selected"<? endif; ?>>
        All Collections</option>
      <? foreach($collections as $collection): ?>
        <option value="<?= $collection['alias']; ?>" <? if($_GET['CISOROOT'] == $collection['alias']): ?>selected="selected"<? endif; ?>>
          <?= $collection['name']; ?></option>
      <? endforeach; ?>
    </select>
    <input type="image" src="<?= SEEKING_MICHIGAN_HOST ?>/images/search-button.gif" value=" " />  
    Or use <a href="seek_advanced.php">Advanced Search &raquo; </a>
  </p>
</form>
