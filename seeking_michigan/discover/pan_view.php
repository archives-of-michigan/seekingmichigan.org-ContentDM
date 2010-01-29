<?
$conf = &dmGetCollectionFieldInfo($alias);
$rc = dmGetItemInfo($alias, $itnum, $data);
$parser = xml_parser_create();
xml_parse_into_struct($parser, $data, $structure, $index);
xml_parser_free($parser);
?>
<div id="item-view">
  <div class="wrapper mod">
    <div class="item-preview">
      <div id="dmMonocle"></div>
    </div>
    <? if($rc != -1): ?>
      <h2>Metadata</h2>
      <? display_metadata($alias, $parent_item, $conf); ?>
    <? endif; ?>
    <? if($_GET['show_comments'] == 1): ?>
      <? app()->partial('comments', array('alias' => $alias, 'idnum' => $itnum)); ?>
    <? endif; ?>
  </div>
</div>
<div id="sidebar">
  <? prev_next_compound($isthisCompoundObject, $show_all, $previous_item, $next_item, $current_item_num, 
                        $totalitems, $encoded_seek_search_params, $search_position, $alias, $parent_itnum); ?>
  <? prev_next_search($seek_search_params,$search_position); ?>
  <? # partial('buy', $alias, $parent_item); ?>
</div>
