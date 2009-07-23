<?php
  include("../dmscripts/DMSystem.php");

  if (isset($_SERVER["QUERY_STRING"])) {
    $arglist = $_SERVER["QUERY_STRING"];
    $p = strpos($arglist,",",0);
    if ($p > 0) {
      $alias = substr($arglist,0,$p);
      $ptr = substr($arglist,$p+1);

      $rc = dmGetCollectionParameters($alias,$name,$path);
      if ($rc >= 0) {
        $rc2 = dmGetItemInfo($alias,$ptr,$buf);
        if ($rc2 > 0) {
          $find = GetXMLField("find",$buf);
          $ext = GetFileExt($find);
          if ($ext == "cpd") {
            $link = "/seeking_michigan/discover_item_viewer.php?CISOROOT=" . $alias . "&amp;CISOPTR=" . $ptr;
          }
          else {
            $rc3 = GetParent($alias,$ptr,$path);
            if ($rc3 >= 0) {
              $link = "/seeking_michigan/discover_item_viewer.php?CISOROOT=" . $alias . "&amp;CISOPTR=" . $rc3 . "&amp;CISOSHOW=" . $ptr;
            }
            else {
              $link = "/seeking_michigan/discover_item_viewer.php?CISOROOT=" . $alias . "&amp;CISOPTR=" . $ptr;
            }
          }
          print("<html>\n");
          print("<head>\n");
          print("<title>Redirect URL</title>\n");
          $line = '<META HTTP-EQUIV="Refresh" CONTENT="0; URL=' . $link . '">' . "\n";
          print("$line");
          print("</head>\n");
          print("<body>\n");
          print("</body>\n");
          print("</html>\n");
        }
        else {
          print("Error, invalid item specified.\n");
        }
      }
      else {
        print("Error, invalid item specified.\n");
      }
    }
    else {
      print("Error, invalid item specified.\n");
    }
  }
  else {
    print("Error, no item specified.\n");
  }
?>
