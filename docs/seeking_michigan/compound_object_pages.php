<html>
  <head></head>
  <body>
    <div class="wrapper">
      <? app()->partial('compound_object_list', 
          array('parent_item' => $current_item->parent_item(),
                'search_status' => $search_status)); ?>
    </div>
  </body>
</html>
