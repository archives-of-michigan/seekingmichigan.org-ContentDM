<?
class ContentDM {
  public static function get_alias($params) {
    global $thisfile,$specifyCollection;
    $a = array();
    if(isset($params["CISOPARM"])){
      $parm = explode(":",urldecode($params["CISOPARM"]));
      $cr = explode(" ",$parm[0]);
      if($cr[0] == "all"){
        $catlist = &dmGetCollectionLIst();
        for ($i = 0; $i < count($catlist); $i++){
          $a[$i] = trim($catlist[$i]['alias']);
        }
      } else {
        for($i = 0; $i < count($cr)-1;$i++){
          $a[$i] = (isset($cr[$i]))?trim($cr[$i]):0;
        }
      }
    } else if(isset($params["CISOROOT"])){
      if($params["CISOROOT"] == "all"){
        $catlist = &dmGetCollectionList();
        for ($i = 0; $i < count($catlist); $i++){
          $a[$i] = trim($catlist[$i]['alias']);
        }
      } else {
        $cisostr = explode(',',urldecode($params["CISOROOT"]));
        for ($i = 0; $i < count($cisostr); $i++){
          $a[$i] = (isset($cisostr[$i]))?trim($cisostr[$i]):0;
        }
      }
    }
    return($a);
  }
}
?>