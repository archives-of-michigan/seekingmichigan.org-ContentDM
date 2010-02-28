<?
function display_metadata($item) {
  dmGetItemInfo($item->alias,$item->itnum,$data);
  $parser = xml_parser_create();
  xml_parse_into_struct($parser, $data, $structure, $index);
  xml_parser_free($parser);
  $confs = &dmGetCollectionFieldInfo($item->alias);
  dmGetCollectionFullResInfo($item->alias,$enabled,$public,$volprefix,$volsize,$displaysize,$archivesize);
  $fullres = "";
  $std_fields = array();
  
  
  foreach($confs as $conf) {
    $tag = strtoupper($conf["nick"]);
    if($conf["type"] != "FTS" && array_key_exists($tag,$index) && 
      array_key_exists("value",$structure[$index[$tag][0]]) &&
      $conf['hide'] != 1) {
      $value = '';
      if($conf["type"] == "DATE") {
        $value = linkDate($structure[$index[$tag][0]]["value"],$item->alias,$conf["nick"]);
      } else {
        if(($conf["search"] == "1") && ($conf["vocab"] == "1")) {
          $value = vocabLink(charReplace($structure[$index[$tag][0]]["value"]), $item->alias, $conf["nick"]);
        } elseif(($conf["search"] == "1") && ($conf["vocab"] == "0")) {
          $value = makeLinks(isHyperlink(charReplace($structure[$index[$tag][0]]["value"]),$conf["type"],$conf["nick"], $item->alias));
        } else {
          $value = makeLinks(charReplace($structure[$index[$tag][0]]["value"]));
        }
      }
      $std_fields[] = array('field' => $conf["name"], 'value' => $value);
    }
  }
  
  include('meta_scr_template.php');
}
?>

