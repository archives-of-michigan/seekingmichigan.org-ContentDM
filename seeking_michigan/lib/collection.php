<?php

class Collection {
  public $alias;
  public $name;
  public $path;

  public static function from_alias($alias) {
    $collections = dmGetCollectionlist();
    foreach($collections as $collection) {
      if($collection['alias'] == $alias) {
        $obj = new Collection($alias, $collection['name'], $collection['path']);
        return $obj;
      }
    }
    return NULL;
  }

  function __construct($alias, $name, $path) {
    $this->alias = $alias;
    $this->name = $name;
    $this->path = $path;
  }

  public function url() {
    $collection_url = SEEKING_MICHIGAN_HOST.'/discover-collection?collection=';
    $collection_url = $collection_url.trim($this->alias,'/');
    return $collection_url;
  }
}
