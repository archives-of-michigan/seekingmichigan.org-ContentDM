<?
/* Parse the CPD document format and return the particulars */
function &parseCPD($alias,$itnum,$xmlbuffer,&$type) {
  $parser = xml_parser_create();
  xml_parse_into_struct($parser, $xmlbuffer, $structure);
  xml_parser_free($parser);

  $compound_itemsult = array();
  $type = "";
  foreach($structure as $s){
    if ($s["tag"] == "TYPE") {
      $type = $s["value"];
      break;
    }
  }

  /* Read the document title from the metadata */
  $rc = dmGetItemInfo($alias,$itnum,$data2);
  if ($rc == -1) {
    $doctitle = "";
  } else {
    $parser2 = xml_parser_create();
    xml_parse_into_struct($parser2, $data2, $structure2, $index2);
    xml_parser_free($parser2);

    $doctitle = $structure2[$index2["TITLE"][0]]["value"];
  }

  global $thisdoc;
  switch($type) {
    case "Document": $thisdoc = "document"; break;
    case "Document-PDF": $thisdoc = "PDFdoc"; break;
    case "Postcard": $thisdoc = "postcard"; break;
    case "Picture Cube": $thisdoc = "picturecube"; break;
    case "Monograph": $thisdoc = "monograph"; break;
  }

  $n = 0;
  if (($type == "Document") || ($type == "Document-PDF") || ($type == "Postcard") || ($type == "Picture Cube")) {
    $compound_itemsult[$n]["index"] = $n;
    $compound_itemsult[$n]["title"] = $doctitle;
    $compound_itemsult[$n]["ptr"] = "";
    $compound_itemsult[$n]["file"] = "";
    $n++;

    foreach($structure as $s){
      if ($s["tag"] == "PAGETITLE") {
        $title = $s["value"];
      } elseif ($s["tag"] == "PAGEFILE") {
        $file = $s["value"];
      } elseif ($s["tag"] == "PAGEPTR") {
        $ptr = $s["value"];

        $compound_itemsult[$n]["index"] = $n;
        $compound_itemsult[$n]["title"] = $title;
        $compound_itemsult[$n]["ptr"] = $ptr;
        $compound_itemsult[$n]["file"] = $file;
        $n++;
      }
    }
  } elseif ($type == "Monograph") {
    $monolvl = array();
    for ($i = 0; $i < 9; $i++)
      $monolvl[$i] = 0;
    $level = 0;
    $firsttime = 1;
    $leveladjust = 0;

    foreach($structure as $s)
    {
      if ($s["tag"] == "PAGETITLE") {
        $title = $s["value"];
      } elseif ($s["tag"] == "PAGEPTR") {
        $ptr = $s["value"];

        for ($i = $level+1; $i < 9; $i++) {
          $monolvl[$i] = 0;
        }
        $monolvl[$level] = $monolvl[$level] + 1;
        $compound_itemsult[$n]["index"] = ComputeLevel($monolvl);
        $compound_itemsult[$n]["title"] = $title;
        $compound_itemsult[$n]["ptr"] = $ptr;
        $n++;
      } elseif ($s["tag"] == "NODETITLE") {
        $title = (isset($s["value"]))?$s["value"]:'';

        $level = $s["level"] - 3 + $leveladjust;
        if ($level < 0) {
          $level = 0;
        } elseif ($level > 9) {
          $level = 9;
        }

        if (($level == 0) && ($monolvl[0] == 0) && ($firsttime == 1)) {
          $firsttime = 0;
          $compound_itemsult[$n]["index"] = "0";
          $title = $doctitle;
        } else {
          if ($level == 0) {
            $leveladjust = 1;
            $level = $level + 1;
          }
          if ($level < 1) {
            $level = 1;
          }
          $monolvl[$level-1] = $monolvl[$level-1] + 1;
          for ($i = $level; $i < 9; $i++) {
            $monolvl[$i] = 0;
          }
          $compound_itemsult[$n]["index"] = ComputeLevel($monolvl);
        }
        $compound_itemsult[$n]["title"] = $title;
        $compound_itemsult[$n]["ptr"] = "";
        $n++;
      }
    }
  }
  return($compound_itemsult);
}

// this is so freakin' stupid...
function ComputeLevel($monolvl) {
  if ($monolvl[0] == 0) {
    $s = "0";
  }
  elseif ($monolvl[1] == 0) {
    $s = $monolvl[0];
  }
  elseif ($monolvl[2] == 0) {
    $s = $monolvl[0] . "." . $monolvl[1];
  }
  elseif ($monolvl[3] == 0) {
    $s = $monolvl[0] . "." . $monolvl[1] . "." . $monolvl[2];
  }
  elseif ($monolvl[4] == 0) {
    $s = $monolvl[0] . "." . $monolvl[1] . "." . $monolvl[2] . "." . $monolvl[3];
  }
  elseif ($monolvl[5] == 0) {
    $s = $monolvl[0] . "." . $monolvl[1] . "." . $monolvl[2] . "." . $monolvl[3] . "." . $monolvl[4];
  }
  elseif ($monolvl[6] == 0) {
    $s = $monolvl[0] . "." . $monolvl[1] . "." . $monolvl[2] . "." . $monolvl[3] . "." . $monolvl[4] . "." . $monolvl[5];
  }
  elseif ($monolvl[7] == 0) {
    $s = $monolvl[0] . "." . $monolvl[1] . "." . $monolvl[2] . "." . $monolvl[3] . "." . $monolvl[4] . "." . $monolvl[5] . "." . $monolvl[6];
  }
  else {
    $s = $monolvl[0] . "." . $monolvl[1] . "." . $monolvl[2] . "." . $monolvl[3] . "." . $monolvl[4] . "." . $monolvl[5] . "." . $monolvl[6] . "." . $monolvl[7];
  }
  return($s);
}



$conf = &dmGetCollectionFieldInfo($alias);
$rc = dmGetCompoundObjectInfo($alias,$parent_itnum,$data);

if ($rc == -1) {
  print("No permission to access this item<br>\n");
  exit();
}

$compound_items = parseCPD($alias,$parent_itnum,$data,$type);

$current_item_index_num = 0;
$current_item_num = 0;
if(isset($_GET["CISOSHOW"])){
  for ($n = 0; $n < count($compound_items); $n++){
    if($_GET["CISOSHOW"] == $compound_items[$n]["ptr"]){
      $current_item_index_num = $compound_items[$n]["index"];
      $current_item_num = $n;
      break;
    }
  }
} else {
  for ($n = 0; $n < count($compound_items); $n++){
    if($compound_items[$n]["ptr"] != ""){
      $current_item_index_num = $compound_items[$n]["index"];
      $current_item_num = $n;
      break;
    }
  }
}
$previous_item_num = $current_item_num - 1;
$next_item_num = $current_item_num + 1;
$totalitems = count($compound_items) - 1;

$current_item = get_sub_item($alias, $compound_items, $current_item_num, $requested_itnum);
$previous_item = ($previous_item_num >= 1) ? get_sub_item($alias, $compound_items, $previous_item_num, $parent_itnum) : null;
$next_item = ($next_item_num <= $totalitems) ? get_sub_item($alias, $compound_items, $next_item_num, $parent_itnum) : null;
?>