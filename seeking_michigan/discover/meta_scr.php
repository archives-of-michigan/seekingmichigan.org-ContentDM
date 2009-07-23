<?
function display_metadata($alias, $item, $conf) {
  dmGetCollectionFullResInfo($alias,$enabled,$public,$volprefix,$volsize,$displaysize,$archivesize);
  $fullres = "";
  $std_fields = array();
  for($i = 0; $i < count($conf); $i++) {
    $tag = strtoupper($conf[$i]["nick"]);
    if($conf[$i]["type"] != "FTS" && array_key_exists($tag,$item['index']) && 
       array_key_exists("value",$item['structure'][$item['index'][$tag][0]])) {
       $value = '';
       if($conf[$i]["type"] == "DATE") {
         $value = linkDate($item['structure'][$item['index'][$tag][0]]["value"],$alias,$conf[$i]["nick"]);
       } else {
         if(($conf[$i]["search"] == "1") && ($conf[$i]["vocab"] == "1")) {
           $value = vocabLink(charReplace($item['structure'][$item['index'][$tag][0]]["value"]), $alias, $conf[$i]["nick"]);
         } elseif(($conf[$i]["search"] == "1") && ($conf[$i]["vocab"] == "0")) {
           $value = makeLinks(isHyperlink(charReplace($item['structure'][$item['index'][$tag][0]]["value"]),$conf[$i]["type"],$conf[$i]["nick"], $alias));
         } else {
           $value = makeLinks(charReplace($item['structure'][$item['index'][$tag][0]]["value"]));
         }
       }
       $std_fields[] = array('field' => $conf[$i]["name"], 'value' => $value);
     }
  }
  include('meta_scr_template.php');
}
?>