<?php
$compound_objects = array(
  '/uw/9876' => <<<XML
<cpd>
  <page>
    <pageptr>1609</pageptr>
  </page>
  <page>
    <pageptr>1610</pageptr>
  </page>
</cpd>
XML
);

$items = array(
  '/uw/1609' => <<<XML
<xml>
  <title>Adrian (Mich.)</title>
  <subjec>Adrian High School (Adrian, Mich.); schools</subjec>
  <descri>High School Building in Adrian (Mich.); c. 1920.</descri>
  <creato></creato>
  <date>c. 1920</date>
  <format>Image</format>
  <type>Postcard - Color</type>
  <identi>PH.10458; Town and City Scenes-Adrian-PF.2104</identi>
  <negati>21568</negati>
  <source>Part of the Souvenir Folder of Adrian (Mich.)</source>
  <rights>Use of this image requires the permission of the Archives of Michigan</rights>
  <order>To order please email archives@mi.gov</order>
  <featur></featur>
  <locati></locati>
  <oclc></oclc>
  <answer></answer>
  <ead></ead>
  <fullrs></fullrs>
  <find>1609.jp2</find>
  <dmaccess></dmaccess>
  <dmimage></dmimage>
  <dmcreated>2009-08-28</dmcreated>
  <dmmodified>2009-08-28</dmmodified>
  <dmoclcno></dmoclcno>
  <dmrecord>1607</dmrecord>
</xml>
XML,

  '/uw/1610' => <<<XML
<xml>
  <title>Adrian (Mich.)</title>
  <subjec>Adrian High School (Adrian, Mich.); schools</subjec>
  <descri>High School Building in Adrian (Mich.); c. 1920.</descri>
  <creato></creato>
  <date>c. 1920</date>
  <format>Image</format>
  <type>Postcard - Color</type>
  <identi>PH.10458; Town and City Scenes-Adrian-PF.2104</identi>
  <negati>21568</negati>
  <source>Part of the Souvenir Folder of Adrian (Mich.)</source>
  <rights>Use of this image requires the permission of the Archives of Michigan</rights>
  <order>To order please email archives@mi.gov</order>
  <featur></featur>
  <locati></locati>
  <oclc></oclc>
  <answer></answer>
  <ead></ead>
  <fullrs></fullrs>
  <find>1610.jp2</find>
  <dmaccess></dmaccess>
  <dmimage></dmimage>
  <dmcreated>2009-08-28</dmcreated>
  <dmmodified>2009-08-28</dmmodified>
  <dmoclcno></dmoclcno>
  <dmrecord>1607</dmrecord>
</xml>
XML
);

  $slash = getPath();
  include($slash."dmscripts/DMUser.php");
  define("CATALOG_FILE",$slash."catalog.txt");
  define("DC_FILE",$slash."dc.txt");
  define("IMAGE_FILE",$slash."../conf/imageconf.txt");
  define("PUB_FILE",$slash."../conf/dbpriv.txt");
  define("CONF_DIR",$slash."../conf");
  define("FIND_DIR",$slash."../find");
  define("FINDCONF_FILE",$slash."../conf/findconf.txt");
  define("MAX_FAVORITES",100);
  define("WORDSIZE",32);

  function getPath() {
    $slash = '';
    if ( array_key_exists( "HTTP_HOST", $_SERVER ) ) {
      $num = substr_count($_SERVER["HTTP_HOST"].$_SERVER["PHP_SELF"],'/');
    }
    else {
      $path = str_replace ( '\\', '/', getcwd() );
      unset ($m);
      if ( preg_match( "#.*(/docs/.*)$#is",  $path, $m ) ) {
        $num = substr_count($m[1],'/');
      }
      else {
        die ("Error" . " File: " . __FILE__ . " on line: " . __LINE__);
      }
    }
    for($i=1;$i<$num;$i++) {
      $slash .= '../';
    }
    return $slash;
  }

  /* Read list of collections available on the Server */
  function &dmGetCollectionList() {
    if ((file_exists(PUB_FILE)) && (filesize(PUB_FILE) > 0)) {
      $handle = fopen(PUB_FILE,"r");
      $dbprivlist = fread($handle,filesize(PUB_FILE));
      fclose($handle);
    }
    else
      $dbprivlist = "";

    /* Open the catalog.txt file */
    $catalogFile = fopen(CATALOG_FILE,"r");
    if (!($catalogFile)) {
      print("Error opening catalog file");
      exit;
    }

    $cat = array();
    $n = 0;

    /* Read the collections from the catalog line by line */
    while (!feof($catalogFile)) {
      $s = fgets($catalogFile,512);
      if (substr($s,0,1) == "/") {
        $s = str_replace("\r","",$s);
        $s = str_replace("\n","",$s);
        $alias = strtok($s,"\t");
        $name = strtok("\t");
        $path = strtok("\t");

        $allow = 0;
        $privfile = $path . "/index/etc/priv.txt";
        $fd = fopen($privfile,"r");
        if ($fd) {
          $t = trim(fgets($fd,2048)," \r\n");
          if ($t != "") {
            $rc = CheckUser($t);
            if ($rc > 0) {
              $allow = 1;
            }
          }
          else {
            $allow = 1;
          }
        }
        else {
          $allow = 1;
        }

        if ($allow == 1) {
          $temp = $alias . "\n";
          if ((isset($_COOKIE['DMID'])) || (!strstr($dbprivlist,$temp))) {
            $cat[$n]["alias"] = $alias;
            $cat[$n]["name"] = $name;
            $cat[$n]["path"] = $path;
            $n++;
          }
        }
      }
    }
    fclose($catalogFile);

    return($cat);
  }

  /* Check the user against the permission string */
  /* Returns: 0 = no access, 1 = full access, 2 = metadata only */
  function CheckUser($t) {
    $user = dmGetUser();
    if (isset($_SERVER["REMOTE_ADDR"]))
      $ip = $_SERVER["REMOTE_ADDR"];
    else
      $ip = "";

    $rc = 0;
    $deny = 0;
    $p = trim(strtok($t,";"));
    while ($p != "") {
      if ($p == "deny:file") {
        $deny = 1;
      }
      elseif (substr($p,0,3) == "ip:") {  /* ip address restriction */
        $ipstring = substr($p,3,strlen($p)-3);
        if (CheckIPString($ipstring,$ip)) {
//      $ipstem = explode("*",$ipstring);
//      if ((trim($ipstem[0]) != "") && (strncmp($ip,trim($ipstem[0]),strlen(trim($ipstem[0]))) == 0)) {
          $rc = 1;
          break;
        }
      }
      else {  /* user restriction */
        $luser = strtolower($user);
        $lp = strtolower($p);
        if (strcmp($luser,$lp) == 0) {
          $rc = 1;
          break;
        }
      }
      $p = trim(strtok(";"));
    }
    if (($rc == 0) && ($deny == 1)) {
      $rc = 2;
    }
    return($rc);
  }

  /* Check the IP string */
  function CheckIPString($s,$ip) {
    $rc = 0;
    if (!strstr($s,"*") && !strstr($s,"-")) {
      if (trim($s) == trim($ip))
        $rc = 1;
    }
    else {
      $sbuf = explode(".",$s,4);
      $ipbuf = explode(".",$ip,4);
      $j = count($sbuf);
      if ($j < 4) {
        for ($i = $j; $i < 4; $i++)
          $sbuf[$i] = "";
      }
      $j = count($ipbuf);
      if ($j < 4) {
        for ($i = $j; $i < 4; $i++)
          $ipbuf[$i] = "";
      }

      $j = 0;
      for ($i = 0; $i < 4; $i++) {
        if (($sbuf[$i] == "*") || ($sbuf[$i] == ""))
          $j++;
        elseif (strstr($sbuf[$i],"-")) {
          $range = explode("-",$sbuf[$i],2);
          if (count($range) < 2)
            break;
          if (($ipbuf[$i] >= $range[0]) && ($ipbuf[$i] <= $range[1]))
            $j++;          
        }
        else {
          if ($sbuf[$i] == $ipbuf[$i])
            $j++;
          else
            break;
        }
      }
      if ($j == 4)
        $rc = 1;
    }
    return($rc);
  }

  /* Read the collection parameters for a given collection */
  /* Returns:  -2 = error, -1 = no permission, 1 = full access, 2 = metadata only */
  function dmGetCollectionParameters($alias,&$name,&$path) {
    /* Open the catalog.txt file */
    $catalogFile = fopen(CATALOG_FILE,"r");
    if (!($catalogFile)) {
      print("Error opening catalog file<br>\n");
      exit;
    }

    $rc = -2;

    /* Read the collections from the catalog line by line */
    while (!feof($catalogFile)) {
      $s = fgets($catalogFile,512);
      if (substr($s,0,1) == "/") {
        $s = str_replace("\r","",$s);
        $s = str_replace("\n","",$s);
        $temp = strtok($s,"\t");
        if ($temp == $alias) {
          $name = strtok("\t");
          $path = strtok("\t");
          $rc = 0;
          break;
        }
      }
    }
    fclose($catalogFile);
    if ($rc == 0) {
      $allow = 0;
      $privfile = $path . "/index/etc/priv.txt";

      if (file_exists($privfile)) {
        $fd = fopen($privfile,"r");
        if ($fd) {
          $t = trim(fgets($fd,2048)," \r\n");
          fclose($fd);

          if ($t != "") {
            $rc = CheckUser($t);
            if ($rc > 0) {
              $allow = 1;
            }
          }
          else {
            $allow = 1;
          }
        }
        else {
          $allow = 1;
        }
        if ($allow == 0) {
          $rc = -1;
        }
      }
      else {
        $rc = -2;
      }
    }
    return($rc);
  }

  /* Read the collection field properties */
  function &dmGetCollectionFieldInfo($alias) {
    $rc = dmGetCollectionParameters($alias,$name,$path);
    if ($rc < 0) {
      print("Error looking up collection $alias<br>\n");
      if ($rc == -1) {
        print("No permission to access this collection<br>\n");
      }
      exit;
    }

    $conf = array();
    $n = 0;

    $fn = $path . "/index/etc/config.txt";

    $configFile = fopen($fn,"r");
    if (!($configFile)) {
      print("Error opening configuration file $fn<br>\n");
      exit;
    }

    while (!feof($configFile)) {
      $s = fgets($configFile,512);
      $s = str_replace("\r","",$s);
      $s = str_replace("\n","",$s);

      if (strlen($s) > 0) {
        $t = substr($s,0,1);

        if (($t != "*") && ($t != ">") && ($t != " ")) {
          $conf[$n]["name"] = strtok($s,":");
          $conf[$n]["nick"] = strtok(":");
          $conf[$n]["type"] = strtok(":");
          $temp = strtok(":");
          if ($temp == "BIG") {
            $conf[$n]["size"] = 1;
          }
          else {
            $conf[$n]["size"] = 0;
          }
          $temp = strtok(":");
          $conf[$n]["find"] = $temp;

          $temp = strtok(":");
          if (($temp == "REQ") || ($conf[$n]["nick"] == "title")) {
            $conf[$n]["req"] = 1;
          }
          else {
            $conf[$n]["req"] = 0;
          }
          $temp = strtok(":");
          if ($temp == "SEARCH") {
            $conf[$n]["search"] = 1;
          }
          else {
            $conf[$n]["search"] = 0;
          }
          $temp = strtok(":");
          if ($temp == "HIDE") {
            $conf[$n]["hide"] = 1;
          }
          else {
            $conf[$n]["hide"] = 0;
          }
          $conf[$n]["vocdb"] = "";
          $temp = strtok(":");
          if (substr($temp,0,5) == "VOCAB") {
            $conf[$n]["vocab"] = 1;
            if ((strlen($temp) > 5) && (substr($temp,5,1) == "-")) {
              $tsdb = trim(substr($temp,6));
              $conf[$n]["vocdb"] = $tsdb;
            }
          }
          else {
            $conf[$n]["vocab"] = 0;
          }
          $conf[$n]["dc"] = strtok(":");
          $conf[$n]["admin"] = 0;
          $conf[$n]["readonly"] = 0;
          $n++;
        }
      }
    }

    fclose($configFile);

    $fn = $path . "/index/etc/configadmin.txt";
    if (file_exists($fn)) {
      $configFile = fopen($fn,"r");
      if (!($configFile)) {
        print("Error opening configuration file $fn<br>\n");
        exit;
      }

      while (!feof($configFile)) {
        $s = fgets($configFile,512);
        $s = str_replace("\r","",$s);
        $s = str_replace("\n","",$s);

        if (strlen($s) > 0) {
          $t = substr($s,0,1);
  
          if (($t != "*") && ($t != ">") && ($t != " ")) {
            $conf[$n]["name"] = strtok($s,":");
            $conf[$n]["nick"] = strtok(":");

            $conf[$n]["type"] = strtok(":");
            if ($conf[$n]["nick"] == "fullrs")
              $conf[$n]["type"] = "FULLRES";

            $temp = strtok(":");
            if ($temp == "BIG") {
              $conf[$n]["size"] = 1;
            }
            else {
              $conf[$n]["size"] = 0;
            }
            $temp = strtok(":");
            $conf[$n]["find"] = $temp;

            $temp = strtok(":");
            $conf[$n]["req"] = 1;
            $temp = strtok(":");
            if ($temp == "SEARCH") {
              $conf[$n]["search"] = 1;
            }
            else {
              $conf[$n]["search"] = 0;
            }
            $temp = strtok(":");
            if ($temp == "HIDE") {
              $conf[$n]["hide"] = 1;
            }
            else {
              $conf[$n]["hide"] = 0;
            }
            $temp = strtok(":");
            if ($temp == "VOCAB") {
              $conf[$n]["vocab"] = 1;
            }
            else {
              $conf[$n]["vocab"] = 0;
            }
            $conf[$n]["vocdb"] = "";
            $conf[$n]["dc"] = strtok(":");
            $conf[$n]["admin"] = 1;
            if (($conf[$n]["nick"] == "dmoclcno") || ($conf[$n]["nick"] == "fullrs")) {
              $conf[$n]["readonly"] = 0;
              $conf[$n]["req"] = 0;
            }
            else
              $conf[$n]["readonly"] = 1;

            $n++;
          }
        }
      }
      fclose($configFile);
    }
    else {
      $conf[$n]["name"] = "Full resolution";
      $conf[$n]["nick"] = "fullrs";
      $conf[$n]["type"] = "FULLRES";
      $conf[$n]["size"] = 0;
      $conf[$n]["find"] = "";
      $conf[$n]["req"] = 0;
      $conf[$n]["search"] = 0;
      $conf[$n]["hide"] = 1;
      $conf[$n]["vocab"] = 0;
      $conf[$n]["vocdb"] = "";
      $conf[$n]["dc"] = "";
      $conf[$n]["admin"] = 1;
      $conf[$n]["readonly"] = 0;
      $n++;
      $conf[$n]["name"] = "OCLC number";
      $conf[$n]["nick"] = "dmoclcno";
      $conf[$n]["type"] = "TEXT";
      $conf[$n]["size"] = 0;
      $conf[$n]["find"] = "";
      $conf[$n]["req"] = 0;
      $conf[$n]["search"] = 0;
      $conf[$n]["hide"] = 1;
      $conf[$n]["vocab"] = 0;
      $conf[$n]["vocdb"] = "";
      $conf[$n]["dc"] = "";
      $conf[$n]["admin"] = 1;
      $conf[$n]["readonly"] = 0;
      $n++;
      $conf[$n]["name"] = "Date created";
      $conf[$n]["nick"] = "dmcreated";
      $conf[$n]["type"] = "DATE";
      $conf[$n]["size"] = 0;
      $conf[$n]["find"] = "";
      $conf[$n]["req"] = 1;
      $conf[$n]["search"] = 0;
      $conf[$n]["hide"] = 1;
      $conf[$n]["vocab"] = 0;
      $conf[$n]["vocdb"] = "";
      $conf[$n]["dc"] = "";
      $conf[$n]["admin"] = 1;
      $conf[$n]["readonly"] = 1;
      $n++;
      $conf[$n]["name"] = "Date modified";
      $conf[$n]["nick"] = "dmmodified";
      $conf[$n]["type"] = "DATE";
      $conf[$n]["size"] = 0;
      $conf[$n]["find"] = "";
      $conf[$n]["req"] = 1;
      $conf[$n]["search"] = 0;
      $conf[$n]["hide"] = 1;
      $conf[$n]["vocab"] = 0;
      $conf[$n]["vocdb"] = "";
      $conf[$n]["dc"] = "";
      $conf[$n]["admin"] = 1;
      $conf[$n]["readonly"] = 1;
      $n++;
      $conf[$n]["name"] = "CONTENTdm number";
      $conf[$n]["nick"] = "dmrecord";
      $conf[$n]["type"] = "TEXT";
      $conf[$n]["size"] = 0;
      $conf[$n]["find"] = "";
      $conf[$n]["req"] = 1;
      $conf[$n]["search"] = 0;
      $conf[$n]["hide"] = 1;
      $conf[$n]["vocab"] = 0;
      $conf[$n]["vocdb"] = "";
      $conf[$n]["dc"] = "";
      $conf[$n]["admin"] = 1;
      $conf[$n]["readonly"] = 1;
      $n++;
      $conf[$n]["name"] = "CONTENTdm file name";
      $conf[$n]["nick"] = "find";
      $conf[$n]["type"] = "TEXT";
      $conf[$n]["size"] = 0;
      $conf[$n]["find"] = "";
      $conf[$n]["req"] = 1;
      $conf[$n]["search"] = 0;
      $conf[$n]["hide"] = 1;
      $conf[$n]["vocab"] = 0;
      $conf[$n]["vocdb"] = "";
      $conf[$n]["dc"] = "";
      $conf[$n]["admin"] = 1;
      $conf[$n]["readonly"] = 1;
      $n++;
    }
    return($conf);
  }

  /* Read the Dublin Core field properties */
  function &dmGetDublinCoreFieldInfo($lang="") {
    $conf = array();
    $n = 0;

    if ($lang == "")
      $dcfile = DC_FILE;
    else {
      $slash = getPath();
      $dcfile = $slash . "dc_" . $lang . ".txt";
    }
    $configFile = fopen($dcfile,"r");
    if (!($configFile)) {
      print("Error opening Dublin Core configuration file<br>\n");
      exit;
    }

    while (!feof($configFile)) {
      $s = fgets($configFile,512);

      if (strlen($s) > 0) {
        $t = substr($s,0,1);

        if (($t != "*") && ($t != ">") && ($t != " ")) {
          $conf[$n]["name"] = strtok($s,":");
          $conf[$n]["nick"] = strtok(":");
          $conf[$n]["type"] = strtok(":");
          $n++;
        }
      }
    }

    fclose($configFile);
    return($conf);
  }

  /* Read the collection full resolution settings for a given collection */
  function dmGetCollectionArchivalInfo($alias,&$enabled,&$public,&$volprefix,&$volsize,&$oclcsym) {
    $rc = dmGetCollectionParameters($alias,$name,$path);
    if ($rc < 0) {
      print("Error looking up collection $alias<br>\n");
      if ($rc == -1) {
        print("No permission to access this collection<br>\n");
      }
      exit;
    }

    $fn = $path . "/index/etc/fullconf.txt";
    if (file_exists($fn)) {
      $configFile = fopen($fn,"r");
      if (!($configFile)) {
        $enabled = 0;
      }
      else {
        $s = fgets($configFile,1024);
        $s = str_replace("\r","",$s);
        $s = str_replace("\n","",$s);
        $temp = strtok($s,":");
        if ($temp == "YES") {
          $enabled = 1;
          $temp = strtok(":");
/*
          if ($temp == "PRIVATE") {
            $public = 0;
          }
          else {
            $public = 1;
          }
*/
          $volprefix = strtok(":");
          $volsize = strtok(":");
          $oclcsym = strtok(":");
          if ($oclcsym == "160x120")
            $oclcsym = "";
        }
        else {
          $enabled = 0;
        }
        fclose($configFile);
      }
    }
    else {
      $enabled = 0;
    }

    $conf = &dmGetCollectionFieldInfo($alias);
    $private = 1;
    for ($i = 0; $i < count($conf); $i++) {
      if ($conf[$i]["nick"] == "fullrs")
        $private = $conf[$i]["hide"];
    }
    if ($private == 1)
      $public = 0;
    else
      $public = 1;
  }

  /* Read the collection display image settings for a given collection */
  function dmGetCollectionDisplayImageSettings($alias,&$enabled,&$format,&$lossy,&$comptype,&$ratio,&$quality,&$tile,&$levels,&$layers,&$jpgdim,&$jpgquality) {
    $rc = dmGetCollectionParameters($alias,$name,$path);
    if ($rc < 0) {
      print("Error looking up collection $alias<br>\n");
      if ($rc == -1) {
        print("No permission to access this collection<br>\n");
      }
      exit;
    }

    $fn = $path . "/index/etc/fileconf.txt";
    if (file_exists($fn)) {
      $configFile = fopen($fn,"r");
      if (!($configFile)) {
        $enabled = 0;
      }
      else {
        $s = fgets($configFile,1024);
        $s = str_replace("\r","",$s);
        $s = str_replace("\n","",$s);
        $temp = strtok($s,":");
        if ($temp == "YES") {
          $enabled = 1;
          $temp = strtok(":");  /* format */
          if ($temp == "JPEG2000") {
            $format = "jp2";
            $temp = strtok(":");  /* lossy */
            if ($temp == "LOSSY") {
              $lossy = 1;
              $temp = strtok(":");
              if ($temp == "RATIO") {
                $comptype = "ratio";
                $ratio = strtok(":");
                $temp = strtok(":");
                $quality = "";
              }
              else {
                $comptype = "quality";
                $temp = strtok(":");
                $ratio = "";
                $temp = strtok(":");
                if ($temp == "MIN")
                  $quality = "Minimum";
                elseif ($temp == "LOW")
                  $quality = "Low";
                elseif ($temp == "MED")
                  $quality = "Medium";
                elseif ($temp == "HIGH")
                  $quality = "High";
                else
                  $quality = "Maximum";
              }
            }
            else {
              $lossy = 0;
              $temp = strtok(":");
              $comptype = "";
              $temp = strtok(":");
              $ratio = "";
              $temp = strtok(":");
              $quality = "";
            }
            $tile = strtok(":");
            $levels = strtok(":");
            if ($levels == "AUTO")
              $levels = 0;
            $layers = strtok(":");
            $jpgdim = "";
            $jpgquality = "";
          }
          else {   /* JPEG */
            $format = "jpg";
            $jpgdim = strtok(":");
            $jpgquality = strtok(":");
            $lossy = "";
            $comptype = "";
            $ratio = "";
            $quality = "";
            $tile = "";
            $levels = "";
            $layers = "";
          }
        }
        else {
          $enabled = 0;
        }
        fclose($configFile);
      }
    }
    else {
      $enabled = 0;
    }
  }

  /* Read the collection full resolution settings for a given collection */
  function dmGetCollectionPDFInfo($alias,&$enabled,&$type,&$pagetext,&$start) {
    $rc = dmGetCollectionParameters($alias,$name,$path);
    if ($rc < 0) {
      print("Error looking up collection $alias<br>\n");
      if ($rc == -1) {
        print("No permission to access this collection<br>\n");
      }
      exit;
    }

    $fn = $path . "/index/etc/pdfconf.txt";
    if (file_exists($fn)) {
      $configFile = fopen($fn,"r");
      if (!($configFile)) {
        $enabled = 0;
      }
      else {
        $s = fgets($configFile,1024);
        $s = str_replace("\r","",$s);
        $s = str_replace("\n","",$s);

        if ($s == "YES") {
          $enabled = 1;
          $type = "page";
          $pagetext = "Page";
          $start = 1;
        }
        elseif ($s == "NO") {
          $enabled = 0;
        }
        else {
          $temp = strtok($s,":");
          if ($temp == "YES") {
            $enabled = 1;
            $temp = strtok(":");
            if ($temp == "FILE") {
              $type = "file";
            }
            else {
              $type = "page";
            }
            $pagetext = strtok(":");
            if ($type == "file")
              $pagetext = "";
            $start1 = strtok(":");
            $start2 = strtok(":");
            if ($type == "page")
              $start = $start1;
            else
              $start = $start2;
          }
          else {
            $enabled = 0;
          }
        }
        fclose($configFile);
      }
    }
    else {
      $enabled = 0;
    }
  }

  /* Read the collection full resolution settings for a given collection */
  function dmGetCollectionFullResInfo($alias,&$enabled,&$public,&$volprefix,&$volsize,&$displaysize,&$archivesize) {
    $rc = dmGetCollectionParameters($alias,$name,$path);
    if ($rc < 0) {
      print("Error looking up collection $alias<br>\n");
      if ($rc == -1) {
        print("No permission to access this collection<br>\n");
      }
      exit;
    }

    $fn = $path . "/index/etc/fullconf.txt";
    if (file_exists($fn)) {
      $configFile = fopen($fn,"r");
      if (!($configFile)) {
        $enabled = 0;
      }
      else {
        /* Read the collections from the catalog line by line */
        $s = fgets($configFile,512);
        $s = str_replace("\r","",$s);
        $s = str_replace("\n","",$s);
        $temp = strtok($s,":");
        if ($temp == "YES") {
          $enabled = 1;
          $temp = strtok(":");
/*
          if ($temp == "PRIVATE") {
            $public = 0;
          }
          else {
            $public = 1;
          }
*/
          $volprefix = strtok(":");
          $volsize = strtok(":");
          $temp = strtok(":");
          $displaysize = strtok(":");
          $archivesize = strtok(":");
        }
        else {
          $enabled = 0;
        }
        fclose($configFile);
      }
    }
    else {
      $enabled = 0;
    }

    $conf = &dmGetCollectionFieldInfo($alias);
    $private = 1;
    for ($i = 0; $i < count($conf); $i++) {
      if ($conf[$i]["nick"] == "fullrs")
        $private = $conf[$i]["hide"];
    }
    if ($private == 1)
      $public = 0;
    else
      $public = 1;
  }

  /* Read the collection parameters for a given collection */
  function dmGetCollectionFullResVolumeInfo($alias,$volname,&$location) {
    $rc = dmGetCollectionParameters($alias,$name,$path);
    if ($rc < 0) {
      print("Error looking up collection $alias<br>\n");
      if ($rc == -1) {
        print("No permission to access this collection<br>\n");
      }
      exit;
    }
    $fn = $path . "/index/etc/fullvol.txt";

    $volFile = fopen($fn,"r");
    if (!($volFile)) {
      $location = "";
    }
    else {
      $location = "";
      /* Read the volume info from the file line by line */
      while (!feof($volFile)) {
        $s = fgets($volFile,512);
        $temp = strtok($s,"\t");
        if ($temp == $volname) {
          $temp = strtok("\t");    /* skip volsize */
          $location = strtok("\t");
          $location = str_replace("\r","",$location);
          $location = str_replace("\n","",$location);
          break;
        }
      }
      fclose($volFile);
    }
  }

  /* Read the field vocabulary */
  function &dmGetCollectionFieldVocabulary($alias,$nick,$forcedict,$forcefullvoc) {
    $rc = dmGetCollectionParameters($alias,$name,$path);
    if ($rc < 0) {
      print("Error looking up collection $alias<br>\n");
      if ($rc == -1) {
        print("No permission to access this collection<br>\n");
      }
      exit;
    }

    if ($forcedict) {
      $vocabsetting = 0;
    }
    else {
      $conf = &dmGetCollectionFieldInfo($alias);
      $vocabsetting = 0;
      for ($i = 0; $i < count($conf); $i++) {
        if ($conf[$i]["nick"] == $nick) {
          $vocabsetting = $conf[$i]["vocab"];
          break;
        }
      }
    }

    $voc = array();
    $n = 0;

    if ($vocabsetting == 1) {   /* return the controlled vocabulary */
      if ($forcefullvoc) {
        $mapfn = $path . "/index/vocab/" . $nick . ".map";
        if (file_exists($mapfn)) {
          $mapFile = fopen($mapfn,"r");
          if (!($mapFile)) {
            $fn = $path . "/index/vocab/" . $nick . ".txt";
          }
          else {
            $s = fgets($mapFile,10);
            fclose($mapFile);
            $id = trim($s);
            $fn = CONF_DIR . "/vocab/" . $id . ".txt";
          }
        }
        else
          $fn = $path . "/index/vocab/" . $nick . ".txt";
      }
      else {
        $fn = $path . "/index/text_search/voc." . $nick;
      }
      $vocFile = fopen($fn,"r");
      if ($vocFile) {
        while (!feof($vocFile)) {
          $s = fgets($vocFile,512);
          $s = str_replace("\r","",$s);
          $s = str_replace("\n","",$s);
          $base = explode("\t",$s);
          $s = trim($base[0]);
          if (strlen($s) > 0) {
            $voc[$n] = $s;
            $n++;
          }
        }
        fclose($vocFile);
      }
    }
    else {   /* return the field dictionary */
      $fn = $path . "/index/text_search/words." . $nick;
      if (file_exists($fn)) {
        $wordFile = fopen($fn,"r");
        if ($wordFile) {
          while (!feof($wordFile)) {
            $s = fgets($wordFile,512);
            $s = str_replace("\r","",$s);
            $s = str_replace("\n","",$s);
            $base = explode("\t",$s);
            $s = trim($base[0]);
            if (strlen($s) > 0) {
              $voc[$n] = $s;
              $n++;
            }
          }
        }
      }
      else {
        $fn = $path . "/index/text_search/word." . $nick;
        $t = "";
        if (file_exists($fn)) {
          $wordFile = fopen($fn,"r");
          if ($wordFile) {
            while (!feof($wordFile)) {
              $s = fgetc($wordFile);
              if ($s == " ") {
                $voc[$n] = $t;
                $n++;
                for ($i = 0; $i < 4; $i++) {
                  $s = fgetc($wordFile);
                }
                $t = "";
              }
              else {
                $t = $t . $s;
              }
            }
            fclose($wordFile);
          }
        }
      }
    }
    return($voc);
  }

  /* Read the image settings */
  function dmGetCollectionImageSettings($alias,&$enabled,&$minjpegdim,&$zoomlevels,&$maxderivedimg,&$viewer,&$docviewer,&$compareviewer,&$slideshowviewer) {
    $rc = dmGetCollectionParameters($alias,$name,$path);
    if ($rc < 0) {
      print("Error looking up collection $alias<br>\n");
      if ($rc == -1) {
        print("No permission to access this collection<br>\n");
      }
      exit;
    }

    $fn = $path . "/index/etc/imageconf.txt";
    if (file_exists($fn)) {   /* try local collection file first */
      $configFile = fopen($fn,"r");
    }
    else {    /* open global file */
      $configFile = fopen(IMAGE_FILE,"r");
    }
    if (!($configFile)) {
      $enabled = 0;
    }
    else {
      $enabled = 0;
      $maxderivedimg = array();
      $viewer = array();
      $docviewer = array();
      $compareviewer = array();
      $slideshowviewer = array();
      $zoomlevels = array();

      $viewer["thumbnail"] = 0;
      $docviewer["thumbnail"] = 0;
      $compareviewer["thumbnail"] = 0;
      $slideshowviewer["thumbnail"] = 0;

      while (!feof($configFile)) {
        $s = fgets($configFile,512);
        $s = str_replace("\r","",$s);
        $s = str_replace("\n","",$s);

        if (strlen($s) > 0) {
          if (substr($s,0,1) != "#") {
            if (substr($s,0,17) == "EnableImageViewer") {
              $temp = TrimValue($s,18);
              if (strcasecmp($temp,"Yes") == 0) {
                $enabled = 1;
              }
            }
            elseif (substr($s,0,16) == "MinJPEGDimension") {
              $minjpegdim = TrimValue($s,17);
            }
            elseif (substr($s,0,20) == "MaxDerivedImageWidth") {
              $maxderivedimg["width"] = TrimValue($s,21);
            }
            elseif (substr($s,0,21) == "MaxDerivedImageHeight") {
              $maxderivedimg["height"] = TrimValue($s,22);
            }
            elseif (substr($s,0,10) == "ZoomLevels") {
              $temp = TrimValue($s,11);
              $temp = $temp . " ";
              $len = strlen($temp);
              $start = 0;
              $n = 0;
              while ($start < $len) {   /* parse out the zoom levels */
                $p = strpos($temp," ",$start);
                if ($p > 0) {
                  $val = substr($temp,$start,$p-$start);
                  if (strlen($val) > 0) {
                    $zoomlevels[$n] = $val;
                    $n++;
                  }
                }
                else {
                  break;
                }
                $start = $p + 1;
              }
            }
            elseif (substr($s,0,11) == "ViewerWidth") {
              $viewer["width"] = TrimValue($s,12);
            }
            elseif (substr($s,0,12) == "ViewerHeight") {
              $viewer["height"] = TrimValue($s,13);
            }
            elseif (substr($s,0,18) == "ViewerDefaultScale") {
              $viewer["scale"] = TrimValue($s,19);
            }
            elseif (substr($s,0,22) == "ViewerDefaultThumbnail") {
              $temp = TrimValue($s,23);
              if (($temp == "On") || ($temp == "ON"))
                $viewer["thumbnail"] = 1;
            }
            elseif (substr($s,0,14) == "DocViewerWidth") {
              $docviewer["width"] = TrimValue($s,15);
            }
            elseif (substr($s,0,15) == "DocViewerHeight") {
              $docviewer["height"] = TrimValue($s,16);
            }
            elseif (substr($s,0,21) == "DocViewerDefaultScale") {
              $docviewer["scale"] = TrimValue($s,22);
            }
            elseif (substr($s,0,25) == "DocViewerDefaultThumbnail") {
              $temp = TrimValue($s,26);
              if (($temp == "On") || ($temp == "ON"))
                $docviewer["thumbnail"] = 1;
            }
            elseif (substr($s,0,20) == "DocViewerNoMenuWidth") {
              $docviewer["nomenuwidth"] = TrimValue($s,21);
            }
            elseif (substr($s,0,21) == "DocViewerNoMenuHeight") {
              $docviewer["nomenuheight"] = TrimValue($s,22);
            }
            elseif (substr($s,0,27) == "DocViewerNoMenuDefaultScale") {
              $docviewer["nomenuscale"] = TrimValue($s,28);
            }
            elseif (substr($s,0,18) == "CompareViewerWidth") {
              $compareviewer["width"] = TrimValue($s,19);
            }
            elseif (substr($s,0,19) == "CompareViewerHeight") {
              $compareviewer["height"] = TrimValue($s,20);
            }
            elseif (substr($s,0,25) == "CompareViewerDefaultScale") {
              $compareviewer["scale"] = TrimValue($s,26);
            }
            elseif (substr($s,0,29) == "CompareViewerDefaultThumbnail") {
              $temp = TrimValue($s,30);
              if (($temp == "On") || ($temp == "ON"))
                $compareviewer["thumbnail"] = 1;
            }
            elseif (substr($s,0,20) == "SlideshowViewerWidth") {
              $slideshowviewer["width"] = TrimValue($s,21);
            }
            elseif (substr($s,0,21) == "SlideshowViewerHeight") {
              $slideshowviewer["height"] = TrimValue($s,22);
            }
            elseif (substr($s,0,27) == "SlideshowViewerDefaultScale") {
              $slideshowviewer["scale"] = TrimValue($s,28);
            }
            elseif (substr($s,0,31) == "SlideshowViewerDefaultThumbnail") {
              $temp = TrimValue($s,32);
              if (($temp == "On") || ($temp == "ON"))
                $slideshowviewer["thumbnail"] = 1;
            }
          }
        }
      }
      if (!array_key_exists("nomenuwidth",$docviewer))
        $docviewer['nomenuwidth'] = $docviewer['width'];
      if (!array_key_exists("nomenuheight",$docviewer))
        $docviewer['nomenuheight'] = $docviewer['height'];
      if (!array_key_exists("nomenuscale",$docviewer))
        $docviewer['nomenuscale'] = $docviewer['scale'];

      fclose($configFile);
    }
  }

  /* Strip off leading and trailing whitespace and trailing comment */
  function TrimValue($s,$i) {
    $temp = trim(substr($s,$i));
    $p = strpos($temp,";");
    if ($p > 0) {
      $temp = trim(substr($temp,0,$p-1));
    }
    return($temp);
  }

  /* Read the item metadata */
  /* Returns: -1 = no permission, 1 = full access, 2 = metadata only */
  function dmGetItemInfo($alias,$ptr,&$xmlbuffer) {
    global $items;
    $xmlbuffer = $items["$alias/$ptr"];
    return($rc);
  }

  /* Read the metadata file */
  /* Returns: -1 = no permission, 1 = full access, 2 = metadata only */
  function ReadItemDesc($path,$ptr,&$xmlbuffer) {
    if (!extension_loaded('dmopr')) {
      if (strtoupper(substr(PHP_OS, 0, 3) == 'WIN')) {
        dl('php_dmoprmod.dll');
      }
      else {
        dl('dmopr.so');
      }
    }
    $oprarg = "getdesc " . $path . "|" . $ptr;
    $buf2 = dmopr($oprarg);
    $xmlbuffer = $buf2;

    $itemperm = GetXMLField("dmaccess",$xmlbuffer);
    if ($itemperm != "") {
      $rc = CheckUser($itemperm);
      if ($rc == 0) {
        $xmlbuffer = "";
      }
    }
    else {
      $rc = 1;
    }
    if ($xmlbuffer == "") {
      return(-1);
    }
    else {
      return($rc);
    }
  }

  /* Read the compound object structure information */
  function dmGetCompoundObjectInfo($alias,$ptr,&$xmlbuffer) {
    global $compound_objects;
    $xmlbuffer = $compound_objects["$alias/$ptr"];
    return(0);
  }

  /* Read the image size */
  function dmGetImageInfo($alias,$ptr,&$filename,&$type,&$width,&$height) {
    $rc = dmGetCollectionParameters($alias,$name,$path);
    if ($rc < 0) {
      return(-1);  /* no collection permission */
    }

    $rc = ReadItemDesc($path,$ptr,$data);
    if ($rc == -1) {
      return(-1);
    }

/*  FIX to decode ampersands in filenames */
    $findval = str_replace("&amp;","&",GetXMLField("find",$data));
    $filename = trim($path . "/image/" . $findval);
    $ext = GetFileExt($filename);
    
    if (($ext == "jpg") || ($ext == "gif") || ($ext == "png")  || ($ext == "jp2") || ($ext == "tif") || ($ext == "tiff")) {
      $type = $ext;
      $size = GetImageDimensions($filename);
      $width = $size["width"];
      $height = $size["height"];
    }
    elseif ($ext == "url") {
      $type = "";
      $width = 0;
      $height = 0;

      if (file_exists($filename)) {
        $urlFile = fopen($filename,"r");
        $n = filesize($filename);
        $urlbuffer = fread($urlFile,$n);
        fclose($urlFile);
        if (strstr($urlbuffer,"about:blank"))
          $type = "null";
      }
    }
    else {
      $type = "";
      $width = 0;
      $height = 0;
    }
  }

  /* Read My Favorites saved items out of the cookie */
  function &dmGetFavorites($field) {
    $record = array();
    $pathmap = array();
    $n = 0;

    if (isset($_COOKIE['BUF'])) {
      $temp = $_COOKIE['BUF'];

      $catlist = &dmGetCollectionList();
      for ($i = 0; $i < count($catlist); $i++) {
        $pathmap[$catlist[$i]["alias"]] = $catlist[$i]["path"];
      }

      /* Parse the cookie data */
      $len = strlen($temp);
      $start = 0;
      $n = 0;
      while ($start < $len) {   /* parse out the zoom levels */
        $p = strpos($temp,">",$start);
        if ($p > 0) {
          $val = substr($temp,$start,$p-$start);
          $l = strlen($val);
          if ($l > 4) {
            $q = strpos($val,"<",0);
            if ($q > 0) {
              $ptr = substr($val,$q+1,$l-$q);
              $alias = substr($val,0,$q);

              if (array_key_exists($alias,$pathmap)) {
                $rc = dmGetItemInfo($alias,$ptr,$data);
                if ($rc >= 0) {
                  $record[$n]["collection"] = $alias;
                  $record[$n]["pointer"] = $ptr;

                  if(!($parser = xml_parser_create()))
                  {
                    print("Error creating XML parser<br>");
                    exit();
                  }
                  xml_parse_into_struct($parser, $data, $structure, $index);
                  xml_parser_free($parser);

                  /* Read the filetype */
                  if (array_key_exists("value",$structure[$index["FIND"][0]]))
                    $record[$n]["filetype"] = GetFileExt($structure[$index["FIND"][0]]["value"]);
                  else
                    $record[$n]["filetype"] = "";

                  $record[$n]["parentobject"] = GetParent($record[$n]["collection"],$record[$n]["pointer"],$pathmap[$record[$n]["collection"]]);

                  /* Read any metadata fields to pass back */
                  for ($j = 0; $j < count($field); $j++) {
                    $tag = strtoupper($field[$j]);
                    if (array_key_exists($tag,$index)) {
                      if (array_key_exists("value",$structure[$index[$tag][0]]))
                        $record[$n][$field[$j]] = $structure[$index[$tag][0]]["value"];
                      else
                        $record[$n][$field[$j]] = "";
                    }
                    else {
                      $record[$n][$field[$j]] = "";
                    }
                  }
                  $n++;
                }
              }
            }
          }
        }
        else {
          break;
        }
        $start = $p + 1;
      }
    }
    return($record);
  }

  /* Parse the favorites out of the cookie string */
  function ParseFavorites($str,&$record) {
    /* Parse the cookie data */
    $len = strlen($str);
    $start = 0;
    $n = 0;
    while ($start < $len) {   /* parse out the records */
      $p = strpos($str,">",$start);
      if ($p > 0) {
        $val = substr($str,$start,$p-$start);
        $l = strlen($val);
        if ($l > 4) {
          $q = strpos($val,"<",0);
          if ($q > 0) {
            $ptr = substr($val,$q+1,$l-$q);
            $alias = substr($val,0,$q);
            $record[$n]["collection"] = $alias;
            $record[$n]["pointer"] = $ptr;
            if ($n < MAX_FAVORITES) {
              $n++;
            }
          }
        }
      }
      else {
        break;
      }
      $start = $p + 1;
    }
  }


  /* Add item(s) to My Favorites */
  function dmAddFavorite($record) {
    if (isset($_COOKIE['BUF'])) {
      $s = $_COOKIE['BUF'];
    }
    else {
      $s = "";
    }

    $n = 0;
    for ($i = 0; $i < strlen($s); $i++) {
      if ($s{$i} == '>') {
        $n++;
      }
    }
    for ($i = 0; $i < count($record); $i++) {
      $t = $record[$i]["collection"] . "<" . $record[$i]["pointer"] . ">";
      if (strstr($s,$t) == "") {
        if ($n < MAX_FAVORITES) {
          $s = $s . $t;
        }
      }
    }
    /* Set the cookie */

    header("Set-Cookie: BUF=$s; path=/; expires=Saturday, 19-Nov-2011 12:00:00 GMT");
  }

  /* Delete item(s) from My Favorites */
  function dmDeleteFavorite($record) {
    if (isset($_COOKIE['BUF'])) {
      $s = $_COOKIE['BUF'];
    }
    else {
      $s = "";
    }

    $buf = array();
    ParseFavorites($s,$buf);

    $cookieval = "";

    for ($i = 0; $i < count($buf); $i++) {
      $found = 0;
      for ($j = 0; $j < count($record); $j++) {
        if ($i == ($record[$j]-1)) {
          $found = 1;
          break;
        }
      }
      if ($found == 0) {
        $cookieval = $cookieval . $buf[$i]["collection"] . "<" . $buf[$i]["pointer"] . ">";
      }
    }

    /* Set the cookie */
    header("Set-Cookie: BUF=$cookieval; path=/; expires=Saturday, 19-Nov-2011 12:00:00 GMT");
  }

  /* Move items in My Favorites */
  function dmMoveFavorite($from,$to) {
    if (isset($_COOKIE['BUF'])) {
      $s = $_COOKIE['BUF'];
    }
    else {
      $s = "";
    }

    $buf = array();
    ParseFavorites($s,$buf);
    $n = count($buf);

    /* Check that the arguments are legal */
    if (($from > 0) && ($from <= $n) && ($to > 0) && ($to <= $n) && ($from != $to)) {
      $from = $from - 1;
      $to = $to - 1;

      if ($from < $to) {
        $tempcol = $buf[$from]["collection"];
        $tempptr = $buf[$from]["pointer"];
        for ($i = $from; $i < $to; $i++) {
          $buf[$i]["collection"] = $buf[$i+1]["collection"];
          $buf[$i]["pointer"] = $buf[$i+1]["pointer"];
        }
        $buf[$to]["collection"] = $tempcol;
        $buf[$to]["pointer"] = $tempptr;
      }
      else {
        $tempcol = $buf[$from]["collection"];
        $tempptr = $buf[$from]["pointer"];
        for ($i = $from; $i > $to; $i--) {
          $buf[$i]["collection"] = $buf[$i-1]["collection"];
          $buf[$i]["pointer"] = $buf[$i-1]["pointer"];
        }
        $buf[$to]["collection"] = $tempcol;
        $buf[$to]["pointer"] = $tempptr;
      }

      $cookieval = "";
      for ($i = 0; $i < $n; $i++) {
        $cookieval = $cookieval . $buf[$i]["collection"] . "<" . $buf[$i]["pointer"] . ">";
      }
      /* Set the cookie */
      header("Set-Cookie: BUF=$cookieval; path=/; expires=Saturday, 19-Nov-2011 12:00:00 GMT");
    }
  }

  /* Perform a text query */
  function &dmQuery($alias,$searchstring,$field,$sortby,$maxrecs,$start,&$total,$suppress=0,$docptr=-1,&$suggest=0,&$facet="") {
    $record = array();
    $pathmap = array();
    $n = 0;
    $numsearchfields = count($searchstring);

    if (($numsearchfields == 0) && (count($alias) != 1)) {  /* can only browse one collection */
      $total = 0;
    }
    else {
      if (count($alias) == 0) {
        print("Error, no collections specified<br>\n");
        exit;
      }
      $catlist = &dmGetCollectionList();
      for ($i = 0; $i < count($catlist); $i++) {
        $pathmap[$catlist[$i]["alias"]] = $catlist[$i]["path"];
      }
      if (($alias[0] == "all") || ((count($alias) == count($catlist)) && (count($catlist) > 1))) {
        $n = GetCatalogCount();
        if (count($catlist) == $n)
          $dblist = "/";
        else {
          $dblist = $catlist[0]["alias"];
          for ($i = 1; $i < count($catlist); $i++) {
            $dblist = $dblist . "," . $catlist[$i]["alias"];
          }
        }
      }
      else {
        $dblist = $alias[0];
        for ($i = 1; $i < count($alias); $i++) {
          $dblist = $dblist . "," . $alias[$i];
        }
      }
      if (count($sortby) == 0) {
        $sortlist = "";
      }
      else {
        $sortlist = $sortby[0];
        for ($i = 1; $i < count($sortby); $i++) {
          $sortlist = $sortlist . " " . $sortby[$i];
        }
      }

      if (($alias[0] == "all") || (count($alias) > 1))
        $temp = "dc";
      else
        $temp = $alias[0];
      $findmap = dmGetFindNick($temp);

      for ($i = 0; $i < 6; $i++) {
        $fieldmap[$i] = -1;
      }
      $multimode = 0;
      for ($i = 0; $i < $numsearchfields; $i++) {
        if (trim($searchstring[$i]["string"]) != "") {
          $searchstring[$i]["string"] = str_replace('"',"",$searchstring[$i]["string"]);
//        $searchstring[$i]["string"] = str_replace(':'," ",$searchstring[$i]["string"]);
          if ($searchstring[$i]["mode"] == "exact") {
            $searchstring[$i]["string"] = str_replace("--"," -- ",$searchstring[$i]["string"]);
            $fieldmap[1] = $i;
          }
          else {
            $searchstring[$i]["string"] = str_replace("--"," - ",$searchstring[$i]["string"]);

            if ($searchstring[$i]["mode"] == "all") {
              $fieldmap[0] = $i;
            }
            elseif ($searchstring[$i]["mode"] == "any") {
              $fieldmap[2] = $i;
            }
            elseif ($searchstring[$i]["mode"] == "none") {
              $fieldmap[3] = $i;
            }
          }
        }
      }
      $searchlist = "";
      if ($multimode == 1) {     /* Search across all fields */
        if ($fieldmap[1] >= 0) {
          if (!strstr($searchstring[$fieldmap[1]]["string"]," near"))
            $searchlist = '"' . $searchstring[$fieldmap[1]]["string"] . '"';
          else
            $searchlist = $searchstring[$fieldmap[1]]["string"];
        }
        if ($fieldmap[0] >= 0) {
          if ($searchlist != "")
            $searchlist = $searchlist . " and ";
          $str = str_replace('('," ",$searchstring[$fieldmap[0]]["string"]);
          $str = str_replace(')'," ",$str);
          $searchlist = $searchlist . AddOperator($str,"and");
        }
        if ($fieldmap[2] >= 0) {
          $str = str_replace('('," ",$searchstring[$fieldmap[2]]["string"]);
          $str = str_replace(')'," ",$str);
          if ($searchlist != "") {
            $searchlist = $searchlist . " and ";
            $searchlist = $searchlist . "(" . AddOperator($str,"or") . ")";
          }
          else
            $searchlist = $searchlist . AddOperator($str,"or");
        }
        if ($fieldmap[3] >= 0) {
          if ($searchlist != "")
            $searchlist = $searchlist . " not ";
          else
            $searchlist = "not ";
          $searchlist = $searchlist . "(" . AddOperator($searchstring[$fieldmap[3]]["string"],"or") . ")";
        }
      }
      else {  /* Selected fields */
        $notclause = "";
        for ($i = 0; $i < $numsearchfields; $i++) {
          if (trim($searchstring[$i]["string"]) != "") {
            if ($i > 0)
              $searchlist = $searchlist . " ";
            $code = "ft";
            $isdate = 0;
            for ($j = 0; $j < count($findmap); $j++) {
              if ($searchstring[$i]["field"] == "CISOSEARCHALL") {
                $code = "ft";
                $isdate = 0;
                break;
              }
              if ($searchstring[$i]["field"] == $findmap[$j]["cdmnick"]) {
                $code = $findmap[$j]["findnick"];
                $isdate = $findmap[$j]["date"];
                break;
              }
            }
            if (($isdate) && (!strstr($searchstring[$i]["string"]," "))) {
              $searchlist = $searchlist . "(" . $code . ":" . FormatDateString($searchstring[$i]["string"]) . ")";
            }
            elseif ($searchstring[$i]["mode"] == "all") {
              $str = str_replace('('," ",$searchstring[$i]["string"]);
              $str = str_replace(')'," ",$str);
              $searchlist = $searchlist . $code . ":" . AddOperator($str,"and");
            }
            elseif ($searchstring[$i]["mode"] == "exact") {
              if (!strstr($searchstring[$i]["string"]," near"))
                $searchlist = $searchlist . $code . ":" . '"' . $searchstring[$i]["string"] . '"';
              else
                $searchlist = $searchlist . $code . ":" . $searchstring[$i]["string"];
            }
            elseif ($searchstring[$i]["mode"] == "any") {
              $str = str_replace('('," ",$searchstring[$i]["string"]);
              $str = str_replace(')'," ",$str);
              $searchlist = $searchlist . "(" . $code . ":" . AddOperator($str,"or") . ")";
            }
            elseif ($searchstring[$i]["mode"] == "none") {
              $notclause = $notclause . " not (" . $code . ":" . AddOperator($searchstring[$i]["string"],"or") . ")";
//            $searchlist = $searchlist . "not (" . $code . ":" . AddOperator($searchstring[$i]["string"],"or") . ")";
            }
          }
        }
        if ($notclause != "") {
          $searchlist = trim($searchlist) . $notclause;
        }
      }

      if (!extension_loaded('DmSearch')) {
        if (strtoupper(substr(PHP_OS, 0, 3) == 'WIN')) {
          dl('php_dmsearchmod.dll');
        }
        else {
          dl('dmsearch.so');
        }
      }
      $dmid = dmGetUser();

      if (isset($_SERVER["REMOTE_ADDR"]))
        $remote_addr = $_SERVER["REMOTE_ADDR"];
      else
        $remote_addr = "";

      if ($suppress == 1)
        $multimode = $multimode + 20;

      /* browse mode */
      if ($numsearchfields == 0) {
        $result = dmsearch($dblist,$searchlist,$multimode,$docptr,$start,$maxrecs,$sortlist,$dmid,$remote_addr);

        if (substr($result,0,1) == "0") {
          $total = 0;
          $n = 0;
          $temp = strtok($result,"\n");
          $p1 = strpos($temp," ");
          if ($p1 != FALSE) {
            $p2 = strpos($temp," ",$p1+1);
            if ($p2 != FALSE) {
              $total = (int) trim(substr($temp,$p2+1));
            }
          }
          while (($temp = strtok("\n")) != FALSE) {
            $p1 = strpos($temp," ");
            $p2 = strpos($temp," ",$p1+1);

            $record[$n]["collection"] = trim(substr($temp,0,$p1));
            $record[$n]["pointer"] = trim(substr($temp,$p1+1,$p2-$p1-1));
            $record[$n]["filetype"] = trim(substr($temp,$p2+1));
            $record[$n]["parentobject"] = GetParent($record[$n]["collection"],$record[$n]["pointer"],$pathmap[$record[$n]["collection"]]);
            $n++;
          }
        }
        else {   /* Error */
          $total = 0;
        }
      }
      else {   /* Run the FIND search */
        $searchstr = $searchlist;

        $searchstr = str_replace("*","\\*",$searchstr);     /* wildcard */
        $searchstr = str_replace(" near"," n",$searchstr);  /* proximity */
//      $searchstr = str_replace('"',"",$searchstr);
//      $searchstr = str_replace(" -- "," - ",$searchstr);
//      $searchstr = str_replace(" --"," -",$searchstr);

        if ($dblist != "/")
          $dblist = str_replace(",","/,",$dblist) . "/";

        $collstr = $dblist;

        /* Handle the sortby, hardcoded 2-character nicknames */
        if (count($sortby) == 0) {
          $sortstr = "ti";
        }
        else {
          $scount = count($sortby);
          $sortstr = "";
          if ($sortby[$scount-1] == "reverse")
            $sortstr = "-";
          $scount--;
          if ($scount == 0)
            $sortstr = $sortstr . "ti";
          else {
            $sortstr = $sortstr . substr($sortby[0],0,2);
            for ($i = 1; $i < $scount; $i++)
              $sortstr = $sortstr . ":" . substr($sortby[$i],0,2);
          }
        }

        $fstart = $start - 1;

        if (isset($_SERVER["REMOTE_ADDR"]))
          $remote_addr = $_SERVER["REMOTE_ADDR"];
        else
          $remote_addr = "";

        $access_str = " (za:f3";
        if ($dmid != "")
          $access_str = $access_str . " or \"" . $dmid . "\"";
        if ($remote_addr != "")
          $access_str = $access_str . " or " . $remote_addr . ")";

        /* compound object search */
        if ($docptr > -1) {
          $searchstr = $searchstr . " cp:" . $docptr;
        }

        if ($suppress == 1) {
          $sortstr = "";
          $scount = count($sortby);
          if ($scount == 0)
            $sortstr = "-\$max.\$d";
          else {
            if ($sortby[$scount-1] == "reverse") {
              $sortstr = "-";
              $scount = $scount - 1;
            }
            for ($i = 0; $i < $scount; $i++) {
              $code = LookupFindNick($findmap,$sortby[$i]);
              if (($code != "") && ($code != "BLANK"))
                $sortstr = $sortstr . "\$" . $code;
            }
          }
          if (($sortstr == "") || ($sortstr == "-") || ($sortstr == "$"))
            $sortstr = "-\$max.\$d";

          $displaystr = "&group=\$group.pa.cp<ITEM><DB>\$pa</DB><KEY>\$cp</KEY><SORT>\$sort" . $sortstr . "</SORT></ITEM>";
        }
        else {
          $sortstr = "";
          $scount = count($sortby);
          if ($scount == 0)
            $sortstr = "\$density";
          else {
            if ($sortby[$scount-1] == "reverse") {
              $sortstr = "-";
              $scount = $scount - 1;
            }
            for ($i = 0; $i < $scount; $i++) {
              $code = LookupFindNick($findmap,$sortby[$i]);
              if ($i > 0)
                $sortstr = $sortstr . ":";
              $sortstr = $sortstr . $code;
            }
          }
          $displaystr = "&sort=" . $sortstr . "&display=<ITEM><DB>\$\$DM_pa\$\$</DB><KEY>\$\$DM_ci\$\$</KEY></ITEM>";
        }

        $facetstr = "";
        if ($facet != "") {
          $facetnick = explode(":",$facet);
          $facetcodestr = "";
          for ($i = 0; $i < count($facetnick); $i++) {
            $code = LookupFindNick($findmap,$facetnick[$i]);
            if (($code != "") && ($code != "BLANK"))
              $facetcodestr = $facetcodestr . $code . ":";
          }
          $facetstr = "&facet=1&maxfacet=10&rsum=" . $facetcodestr . "&facetinitial=" . $facetcodestr;
        }


        $findurl = ReadFindURL();
        $url = $findurl . "/!/search?query=" . urlencode($searchstr) . " and" . $access_str . $displaystr . "&collection=" . $collstr . "&suggest=1" . $facetstr . "&rankboost=&proximity=strict&priority=normal&unanchoredphrases=1&maxres=" . $maxrecs  . "&firstres=" . $fstart . "&rform=/!/null.htm";

        print("<!-- $url //-->\n");

        $result = file_get_contents($url);
        $result = str_replace("&lt;","<",$result);

        $istart = 0;
        $n = 0;
        $rc = GetField("count",$result,$buf,0);
        if ($rc < -1)
          $total = 0;
        else
          $total = $buf + 0;

        while ($istart >= 0) {
          $istart = GetField("ITEM",$result,$buf,$istart);
          if ($istart >= 0) {
            $rc = GetField("DB",$buf,$db,0);
            $rc = GetField("KEY",$buf,$ptr,0);
            if (($db != "") && ($ptr != "")) {
              $record[$n]["collection"] = rtrim($db,"/ ");
              $record[$n]["pointer"] = $ptr;
              $record[$n]["filetype"] = "jpg";  /* hardcoded */
              $record[$n]["parentobject"] = GetParent($record[$n]["collection"],$record[$n]["pointer"],$pathmap[$record[$n]["collection"]]);
              $n++;
              if ($n == $maxrecs)
                break;
            }
          }
        }
        /* spelling suggestion */
        if (($total == 0) && ($suggest == 1)) {
          if ((!strstr($searchstr," not ")) && (!strstr($searchstr,":not "))) {
            $url = $findurl . "/!/search?query=" . urlencode($searchstr) . $displaystr . "&collection=" . $collstr . "&suggest=1" . $facetstr . "&rankboost=&proximity=strict&priority=normal&unanchoredphrases=0&maxres=" . $maxrecs  . "&firstres=" . $fstart . "&rform=/!/null.htm";
            $result = file_get_contents($url);
            $rc = GetField("alternate",$result,$temp,0);
            if ($rc < -1)
              $temp = "";
            $suggest = trim($temp);
            if ($suggest != "") {
              if (substr($suggest,2,1) == ":")
                $suggest = substr($suggest,3);
              $p = strpos($suggest,":",0);
              if ($p) {
                if ($p > 1)
                  $suggest = trim(substr($suggest,0,$p-2));
              }
            }
          }
          else
            $suggest = "";
        }

        /* facets */
        if ($facet != "") {
          $istart = 0;
          $facet = "<facet>\n";

          $k = 0;
          $facetmap = array();
          while ($istart >= 0) {
            $istart = GetField("FacetItem",$result,$buf,$istart);
            if ($istart >= 0) {
              $rc = GetField("label",$buf,$facetlabel,0);
              $rc = GetField("name",$buf,$facetname,0);
              $rc = GetField("count",$buf,$facetcount,0);
              if (($facetlabel != "") && ($facetname != "") && ($facetcount != "")) {
                $code = substr(trim($facetlabel),0,2);
                $label = "";
                for ($j = 0; $j < count($findmap); $j++) {
                  if ($code == $findmap[$j]["findnick"]) {
                    $label = $findmap[$j]["cdmnick"];
                    break;
                  }
                }
                $facetmap[$k]["label"] = $label;
                $facetmap[$k]["name"] = $facetname;
                $facetmap[$k]["count"] = (int) $facetcount;
                $k++;
              }
            }
          }

          for ($i = 0; $i < $k-1; $i++) {
            for ($j = $i+1; $j < $k; $j++) {
              if (($facetmap[$i]["label"] == $facetmap[$j]["label"]) && ($facetmap[$i]["name"] == $facetmap[$j]["name"])) {
                $facetmap[$i]["count"] = $facetmap[$i]["count"] + $facetmap[$j]["count"];
                $facetmap[$j]["count"] = 0;
              }
            }
          }
          for ($i = 0; $i < $k-1; $i++) {
            for ($j = $i+1; $j < $k; $j++) {
              if ($facetmap[$i]["count"] < $facetmap[$j]["count"]) {
                $label = $facetmap[$i]["label"];
                $facetname = $facetmap[$i]["name"];
                $facetcount = $facetmap[$i]["count"];
                $facetmap[$i]["label"] = $facetmap[$j]["label"];
                $facetmap[$i]["name"] = $facetmap[$j]["name"];
                $facetmap[$i]["count"] = $facetmap[$j]["count"];
                $facetmap[$j]["label"] = $label;
                $facetmap[$j]["name"] = $facetname;
                $facetmap[$j]["count"] = $facetcount;
              }
            }
          }
          for ($j = 0; $j < $k; $j++) {
            if ($facetmap[$j]["count"] > 0)
              $facet = $facet . "  <label>" . $facetmap[$j]["label"] . "</label><name>" . $facetmap[$j]["name"] . "</name><count>" . $facetmap[$j]["count"] . "</count>\n";
          }

          $facet = $facet . "</facet>\n";
        }
      }
    }

    /* Check if there are any metadata fields to return */
    if (count($field) > 0) {
      for ($i = 0; $i < count($record); $i++) {
        dmGetItemInfo($record[$i]["collection"],$record[$i]["pointer"],$data);
        if(!($parser = xml_parser_create()))
        {
          print("Error creating XML parser<br>");
          exit();
        }
        xml_parse_into_struct($parser, $data, $structure, $index);
        xml_parser_free($parser);

        for ($j = 0; $j < count($field); $j++) {
          $tag = strtoupper($field[$j]);
          if (array_key_exists($tag,$index)) {
            if (array_key_exists("value",$structure[$index[$tag][0]])) {
              $record[$i][$field[$j]] = $structure[$index[$tag][0]]["value"];
            }
            else {
              $record[$i][$field[$j]] = "";
            }
          }
          else {
            $record[$i][$field[$j]] = "";
          }
        }

        /* Get the file extension for Find */
        if (array_key_exists("FIND",$index)) {
          if (array_key_exists("value",$structure[$index["FIND"][0]])) {
            $temp = $structure[$index["FIND"][0]]["value"];
            $record[$i]["filetype"] = GetFileExt($temp);
          }
        }

      }
    }
    return($record);
  }

  /* Read the Find nicknames */
  function &dmGetFindNick($alias) {
    $res = array();
    $n = 0;
    if ($alias == "dc") {
      $infile = FIND_DIR . "/config/finddc.txt";
      if (file_exists($infile)) {
        $fd = fopen($infile,"r");
        if ($fd) {
          while (!feof($fd)) {
            $t = fgets($fd,2048);
            $s = explode(" ",$t);
            $j = count($s);
            if ($j > 1) {
              $left = strtolower(trim($s[0]));
              $right = trim($s[$j-1]);
              if ($left == "collection")
                continue;
              if ($left == "cdmid")
                break;
  
              $res[$n]["findnick"] = $left;
              $res[$n]["cdmnick"] = $right;
              if (substr($right,0,4) == "date")
                $res[$n]["date"] = 1;
              else 
                $res[$n]["date"] = 0;
              $n++;
            }
          }
        }
      }
      fclose($fd);
    }
    else {
      $conf = &dmGetCollectionFieldInfo($alias);
      for ($i = 0; $i < count($conf); $i++) {
        if ($conf[$i]["search"] == 1) {
          $res[$n]["findnick"] = $conf[$i]["find"];
          $res[$n]["cdmnick"] = $conf[$i]["nick"];
          if ($conf[$i]["type"] == "DATE")
            $res[$n]["date"] = 1;
          else 
            $res[$n]["date"] = 0;
          $n++;
        }
      }
    }
    return($res);
  }

  function LookupFindNick($findmap,$field) {
    $code = "";
    for ($i = 0; $i < count($findmap); $i++) {
      if ($findmap[$i]["cdmnick"] == $field) {
        $code = $findmap[$i]["findnick"];
        break;
      }
    }
    return($code);
  }

  /* Return the XML field */
  function GetField($tag,$xmlbuffer,&$s,$start) {
    $tagstart = "<" . $tag . ">";
    $tagend = "</" . $tag . ">";
    $p1 = strpos($xmlbuffer,$tagstart,$start);
    $p2 = strpos($xmlbuffer,$tagend,$start);
    if (($p1 === FALSE) || ($p2 === FALSE) || ($p2 < $p1)) {
      $s = "";
      return(-1);
    }
    $l = strlen($tagstart);
    if (($p1 + $l) == $p2) {
      $s = "";
      return(-1);
    }
    $s = substr($xmlbuffer,$p1+$l,$p2-$p1-$l);
    return($p2+strlen($tagend));
  }

  /* Perform a text query */
  function &dmQuery43($alias,$searchstring,$field,$sortby,$maxrecs,$start,&$total,$suppress=0,$docptr=-1) {
    $record = array();
    $pathmap = array();
    $n = 0;
    $numsearchfields = count($searchstring);

    if (($numsearchfields == 0) && (count($alias) != 1)) {  /* can only browse one collection */
      $total = 0;
    }
    else {
      if (count($alias) == 0) {
        print("Error, no collections specified<br>\n");
        exit;
      }
      $catlist = &dmGetCollectionList();
      for ($i = 0; $i < count($catlist); $i++) {
        $pathmap[$catlist[$i]["alias"]] = $catlist[$i]["path"];
      }
      if ($alias[0] == "all") {      
        $dblist = $catlist[0]["alias"];
        for ($i = 1; $i < count($catlist); $i++) {
          $dblist = $dblist . " " . $catlist[$i]["alias"];
        }
      }
      else {
        $dblist = $alias[0];
        for ($i = 1; $i < count($alias); $i++) {
          $dblist = $dblist . " " . $alias[$i];
        }
      }
      if (count($sortby) == 0) {
        $sortlist = "";
      }
      else {
        $sortlist = $sortby[0];
        for ($i = 1; $i < count($sortby); $i++) {
          $sortlist = $sortlist . " " . $sortby[$i];
        }
      }
      for ($i = 0; $i < 4; $i++) {
        $fieldmap[$i] = -1;
      }
      $multimode = 0;
      for ($i = 0; $i < $numsearchfields; $i++) {
        $searchstring[$i]["string"] = strtr($searchstring[$i]["string"],"`~!@#$%^&-_+={}[]|;:<>,.?/","                          ");
        if ($searchstring[$i]["field"] == "CISOSEARCHALL") {
          $multimode = 1;
//        $searchstring[$i]["string"] = strtolower($searchstring[$i]["string"]);
        }
        if (trim($searchstring[$i]["string"]) != "") {
          if ($searchstring[$i]["mode"] == "all") {
            $fieldmap[0] = $i;
          }
          elseif ($searchstring[$i]["mode"] == "exact") {
            $fieldmap[1] = $i;
          }
          elseif ($searchstring[$i]["mode"] == "any") {
            $fieldmap[2] = $i;
          }
          elseif ($searchstring[$i]["mode"] == "none") {
            $fieldmap[3] = $i;
          }
        }
      }
      $searchlist = "";
      if ($multimode == 1) {     /* Search across all fields */
        if ($fieldmap[1] >= 0) {
          $searchlist = "(" . $searchstring[$fieldmap[1]]["string"] . ")";
        }
        if ($fieldmap[0] >= 0) {
          if ($searchlist != "")
            $searchlist = $searchlist . " and ";
          $searchlist = $searchlist . "(" . AddOperator($searchstring[$fieldmap[0]]["string"],"and") . ")";
        }
        if ($fieldmap[2] >= 0) {
          if ($searchlist != "")
            $searchlist = $searchlist . " and ";
          $searchlist = $searchlist . "(" . AddOperator($searchstring[$fieldmap[2]]["string"],"or") . ")";
        }
        if ($fieldmap[3] >= 0) {
          if ($searchlist != "")
            $searchlist = $searchlist . " not ";
          else
            $searchlist = "not ";
          $searchlist = $searchlist . "(" . AddOperator($searchstring[$fieldmap[3]]["string"],"or") . ")";
        }
      }
      else {  /* Selected fields */
        for ($i = 0; $i < $numsearchfields; $i++) {
          if (trim($searchstring[$i]["string"]) != "") {
            if ($searchstring[$i]["mode"] == "all") {
              $searchlist = $searchlist . $searchstring[$i]["field"] . "=" . AddOperator($searchstring[$i]["string"],"and") . "\n";
            }
            elseif ($searchstring[$i]["mode"] == "exact") {
              $searchlist = $searchlist . $searchstring[$i]["field"] . "=" . $searchstring[$i]["string"] . "\n";
            }
            elseif ($searchstring[$i]["mode"] == "any") {
              $searchlist = $searchlist . $searchstring[$i]["field"] . "=" . AddOperator($searchstring[$i]["string"],"or") . "\n";
            }
            elseif ($searchstring[$i]["mode"] == "none") {
              $searchlist = $searchlist . $searchstring[$i]["field"] . "=not " . AddOperator($searchstring[$i]["string"],"or") . "\n";
            }
          }
        }
      }

      if (!extension_loaded('DmSearch')) {
        if (strtoupper(substr(PHP_OS, 0, 3) == 'WIN')) {
          dl('php_dmsearchmod.dll');
        }
        else {
          dl('dmsearch.so');
        }
      }
      $dmid = dmGetUser();

      if (isset($_SERVER["REMOTE_ADDR"]))
        $remote_addr = $_SERVER["REMOTE_ADDR"];
      else
        $remote_addr = "";

      if ($suppress == 1)
        $multimode = $multimode + 20;

      $result = dmsearch($dblist,$searchlist,$multimode,$docptr,$start,$maxrecs,$sortlist,$dmid,$remote_addr);

      if (substr($result,0,1) == "0") {
        $total = 0;
        $n = 0;
        $temp = strtok($result,"\n");
        $p1 = strpos($temp," ");
        if ($p1 != FALSE) {
          $p2 = strpos($temp," ",$p1+1);
          if ($p2 != FALSE) {
            $total = (int) trim(substr($temp,$p2+1));
          }
        }
        while (($temp = strtok("\n")) != FALSE) {
          $p1 = strpos($temp," ");
          $p2 = strpos($temp," ",$p1+1);

          $record[$n]["collection"] = trim(substr($temp,0,$p1));
          $record[$n]["pointer"] = trim(substr($temp,$p1+1,$p2-$p1-1));
          $record[$n]["filetype"] = trim(substr($temp,$p2+1));
          $record[$n]["parentobject"] = GetParent($record[$n]["collection"],$record[$n]["pointer"],$pathmap[$record[$n]["collection"]]);
          $n++;
        }
      }
      else {   /* Error */
        $total = 0;
      }
    }

    /* Check if there are any metadata fields to return */
    if (count($field) > 0) {
      for ($i = 0; $i < count($record); $i++) {
        dmGetItemInfo($record[$i]["collection"],$record[$i]["pointer"],$data);
        if(!($parser = xml_parser_create()))
        {
          print("Error creating XML parser<br>");
          exit();
        }
        xml_parse_into_struct($parser, $data, $structure, $index);
        xml_parser_free($parser);

        for ($j = 0; $j < count($field); $j++) {
          $tag = strtoupper($field[$j]);
          if (array_key_exists($tag,$index)) {
            if (array_key_exists("value",$structure[$index[$tag][0]])) {
              $record[$i][$field[$j]] = $structure[$index[$tag][0]]["value"];
            }
            else {
              $record[$i][$field[$j]] = "";
            }
          }
          else {
            $record[$i][$field[$j]] = "";
          }
        }
      }
    }
    return($record);
  }

  /* Get the parent compound object pointer, -1 if the item is not part of a compound object */
  function GetParent($alias,$ptr,$path) {
    $rc = CheckSuppFile($path,$ptr,"index.xml",$suppfn);
    if (file_exists($suppfn)) {       /* Check for compound object */
      $xmlFile = fopen($suppfn,"r");
      if (!($xmlFile)) {
        return(-1);
      }
      $n = filesize($suppfn);
      $xmlbuffer = fread($xmlFile,$n);
      fclose($xmlFile);

      $s = GetXMLField("parent",$xmlbuffer);
      return($s);
    }
    else {
      $rc = CheckSuppFile($path,$ptr,"newsindex.xml",$suppfn2);
      if (file_exists($suppfn2)) {       /* Check for newspaper */
        $xmlFile = fopen($suppfn2,"r");
        if (!($xmlFile)) {
          return(-1);
        }
        $n = filesize($suppfn2);
        $xmlbuffer = fread($xmlFile,$n);
        fclose($xmlFile);

        $s = GetXMLField("itemtype",$xmlbuffer);

        if ($s == "Page") {
          $issue = GetXMLField("issue",$xmlbuffer);
          return($issue);
        }
        elseif ($s == "Article") {
          $page = GetXMLField("page",$xmlbuffer);
          $rc = CheckSuppFile($path,$page,"newsindex.xml",$suppfn3);
          if (file_exists($suppfn3)) {       /* Check for newspaper */
            $xmlFile = fopen($suppfn3,"r");
            if (!($xmlFile)) {
              return(-1);
            }
            $n = filesize($suppfn3);
            $xmlbuffer = fread($xmlFile,$n);
            fclose($xmlFile);
            $issue = GetXMLField("issue",$xmlbuffer);
            return($issue);
          }
        }
        else {
          return(-1);
        }
      }
    }
    return(-1);
  }

  /* Check for existence of supp file */
  function CheckSuppFile($loc,$ptr,$relfn,&$fn) {
    $fn = $loc . "/supp/" . $ptr ."/" . $relfn;
    if (!file_exists($fn)) {
      if ($ptr >= 10000) {
        $k = ((int)((int)$ptr / 10000)) * 10000;
        $fn = $loc . "/supp/D" . $k . "/" . $ptr ."/" . $relfn;
        if (!file_exists($fn))
	  $rc = -1;  /* file does not exist */
	else
	  $rc = 0;   /* file exists */
      }
      else
	$rc = -1;  /* file does not exist */
    }
    else
      $rc = 0;  /* file exists */
    return($rc);
  }

  /* Return the XML field */
  function GetXMLField($tag,$xmlbuffer) {
    $tagstart = "<" . $tag . ">";
    $tagend = "</" . $tag . ">";
    $p1 = strpos($xmlbuffer,$tagstart);
    $p2 = strpos($xmlbuffer,$tagend);
    if (($p1 === FALSE) || ($p2 === FALSE) || ($p2 < $p1)) {
      $s = "";
      return($s);
    }
    $l = strlen($tagstart);
    if (($p1 + $l) == $p2) {
      $s = "";
      return($s);
    }
    $s = substr($xmlbuffer,$p1+$l,$p2-$p1-$l);
    return($s);
  }

  function ReadFindURL() {
    $fd = fopen(FINDCONF_FILE,"r");
    if (!($fd)) {
      print("Error opening conf/findconf.txt file<br>\n");
      exit;
    }

    $findurl = "";
    while (!feof($fd)) {
      $s = fgets($fd,1024);
      if (substr($s,0,7) == "FINDURL") {
        $findurl = trim(substr($s,7));
        break;
      }
    }
    fclose($fd);

    return($findurl);
  }

  /* Add the search operator to the search string */
  function AddOperator($s,$op) {
    $t = "";
    $string = trim($s);
    if ($string == "")
      return($t);
    $t = strtok($s," ");
    if (strlen($t) >= WORDSIZE)
      $t = substr($t,0,WORDSIZE);

    $lt  = strtolower($t);
    if (($lt == "and") || ($lt == "or") || ($lt == "not"))
      $t = "";
    
    while (($word = strtok(" ")) != FALSE) {
      $u = trim($word);
      if (strlen($u) >= WORDSIZE)
        $u = substr($u,0,WORDSIZE);
      $lu = strtolower($u);

      if (($u != "") && ($lu != "and") && ($lu != "or") && ($lu != "not")) {
        $t = $t . " " . $op . " " . $u;
      }
    }
    return($t);  
  }

  /* Return file extension */
  function GetFileEXt($filename) {
    $p = strrpos($filename,".");
    if ($p > 0) {
      $ext = trim(strtolower(substr($filename,$p+1,strlen($filename)-$p-1)));
    }
    else {
      $ext = "";
    }
    return($ext);
  }

  /* Return the print file link */
  /* Returns: -1 = no print file, 0 = print file exists */
  function dmGetPrintFileInfo($alias,$ptr,&$link) {
    $rc = dmGetCollectionParameters($alias,$name,$path);
    if ($rc < 0) {
      if ($rc == -1) {
        return(-1);  /* no collection permission */
      }
      print("Error looking up collection $alias<br>\n");
      exit;
    }

    $rc2 = CheckSuppFile($path,$ptr,"index.pdf",$printlink);
    if ($rc2 == -1)
      $rc2 = CheckSuppFile($path,$ptr,"index.html",$printlink);

    if (file_exists($printlink)) {
      $link = "/cgi-bin/showfile.exe?CISOROOT=" . $alias . "&CISOPTR=" . $ptr . "&CISOMODE=print";
      $rc = 0;
    }
    else
      $rc = -1;

    return($rc);
  }

  /* Get the Server locale */
  function dmGetLocale() {
    $locale = "en_US";  /* default */
    $localefile = CONF_DIR . "/locale.txt";
    if (file_exists($localefile)) {
      $fd = fopen($localefile,"r");
      if ($fd) {
        $s = fgets($fd,1024);
        $locale = trim($s);
      }
    }
    return($locale);
  }

  /* Format the date search string */
  function FormatDateString($str) {
    $result = "";
    $s = str_replace("*","",$str);
    $t = explode("-",$s);
    if (count($t) == 1) {
      /* single date case */
      $l = strlen($s);
      if ($l == 4)
        $result = $s . "*";
      elseif ($l == 6)
        $result = substr($s,0,4) . "-" . substr($s,4,2) . "*";
      elseif ($l == 8)
        $result = substr($s,0,4) . "-" . substr($s,4,2) . "-" . substr($s,6,2);
      else
        $result = $str;
    }
    elseif (count($t) == 2) {
      if ($t[0] == "00000000")
        $t[0] = "1000";
      $k = strlen($t[0]);
      $l = strlen($t[1]);
      if ($l == 2) {
        $result = $s . "*";
      }
      elseif ((($k == 4) || ($k == 6) || ($k == 8)) && (($l == 4) || ($l == 6) || ($l == 8))) {
        /* date range */
        if ($k == 4) {
          $startyear = (int) $t[0];
          $smonth = 0;
          $sday = 0;
        }
        elseif ($k == 6) {
          $syear = (int) substr($t[0],0,4);
          $result = $syear;
          $startyear = $syear + 1;
          $smonth = (int) $t[0];
          $sday = 0;
        }
        elseif ($k == 8) {
          $syear = (int) substr($t[0],0,4);
          $startyear = $syear + 1;
          $startmonth = (int) substr($t[0],0,6);
          $smonth = $startmonth + 1;
          $sday = (int) $t[0];
          $result = $syear . " or " . substr($t[0],0,4) . "-" . substr($t[0],4,2);
        }
        if ($l == 4) {
          $endyear = (int) $t[1];
          $emonth = 999999;
          $eday = 99999999;
        }
        elseif ($l == 6) {
          $eyear = (int) substr($t[1],0,4);
          $endyear = $eyear - 1;
          $emonth = (int) $t[1];
          $eday = 99999999;
        }
        elseif ($l == 8) {
          $eyear = (int) substr($t[1],0,4);
          $endyear = $eyear - 1;
          $emonth = (int) substr($t[1],0,6);
          $eday = (int) $t[1];
        }

        /* Handle a day count up to the month */
        $end10 = (int) ($eday / 10);
        if ($k == 8) {
          if ($sday < $eday) {
            $i = $sday;
            while ($i < $eday) {
              $j = $i % 100;
              if ($j > 31)
                break;
              $m = $i % 10;

              if (($j == 1) || ($m == 0)) {
                $start10 = (int) ($i / 10);
                if ($start10 < $end10) {
                  $u = (string) $i;
                  $result = $result . " or " . substr($u,0,4) . "-" . substr($u,4,2) . "-" . substr($u,6,1) . "*";
                  $i = $i + 10;
                  if ($j == 1)
                    $i--;
                }
                else {
                  $u = (string) $i;
                  $result = $result . " or " . substr($u,0,4) . "-" . substr($u,4,2) . "-" . substr($u,6,2);
                  $i++;
                }
              }
              else {
                $u = (string) $i;
                $result = $result . " or " . substr($u,0,4) . "-" . substr($u,4,2) . "-" . substr($u,6,2);
                $i++;
              }
            }
          }
        }

        /* Handle a month count up to the year */
        if (($k == 6) || ($k == 8)) {
          if ($smonth < $emonth) {
            $i = $smonth;
            while ($i < $emonth) {
              $j = $i % 100;
              if ($j > 12)
                break;
              $u = (string) $i;
              $result = $result . " or " . substr($u,0,4) . "-" . substr($u,4,2) . "*";
              $i++;
            }
          }
        }

        /* Handle the intervening years */
        if ($startyear <= $endyear) {
          if (($startyear < 0) || ($endyear < 0))
            return "";
          $i = $startyear;
          if ($result == "")
            $op = "";
          else
            $op = " or ";
          $enddecade = (int) ($endyear / 10);
          $endcentury = (int) ($endyear / 100);
          while ($i <= $endyear) {
            if (($i % 100) == 0) {
              $century = (int) ($i / 100);
              if ($century < $endcentury) {
                $result = $result . $op . $century . "*";
                $i = $i + 100;
                $op = " or ";
                continue;
              }
            }
            if (($i % 10) == 0) {
              $decade = (int) ($i / 10);
              if ($decade < $enddecade) {
                $result = $result . $op . $decade . "*";
                $i = $i + 10;
              }
              else {
                $result = $result . $op . $i . "*";
                $i++;
              }
            }
            else {
              $result = $result . $op . $i . "*";
              $i++;
            }
            $op = " or ";
          }
        }

        /* Handle a month count up to the end month */
        if (($l == 6) || ($l == 8)) {
          $i = ($eyear * 100) + 1;
          while ($i < $emonth) {
            $j = $i % 100;
            if ($j > 12)
              break;
            if ($i > $smonth) {
              $u = (string) $i;
              $result = $result . " or " . substr($u,0,4) . "-" . substr($u,4,2) . "*";
            }
            $i++;
          }
          $u = (string) $emonth;
          $result = $result . " or " . substr($u,0,4) . "-" . substr($u,4,2);
          if ($l == 6)
            $result = $result . "*";
        }

        /* Handle a day count up to the day */
        $end10 = (int) ($eday / 10);
        if ($l == 8) {
          if (($k == 8) && ($startmonth == $emonth)) {
            $u = (string) $eday;
            $result = $result . " or " . substr($u,0,4) . "-" . substr($u,4,2) . "-" . substr($u,6,2);
          }
          else {
          $i = ($emonth * 100) + 1;
          while ($i <= $eday) {
            $j = $i % 100;
            if ($j > 31)
              break;
            if ($i > $smonth) {
              $m = $i % 10;

              if (($j == 1) || ($m == 0)) {
                $start10 = (int) ($i / 10);
                if ($start10 < $end10) {
                  $u = (string) $i;
                  $result = $result . " or " . substr($u,0,4) . "-" . substr($u,4,2) . "-" . substr($u,6,1) . "*";
                  $i = $i + 10;
                  if ($j == 1)
                    $i--;
                }
                else {
                  $u = (string) $i;
                  $result = $result . " or " . substr($u,0,4) . "-" . substr($u,4,2) . "-" . substr($u,6,2);
                  $i++;
                }
              }
              else {
                $u = (string) $i;
                $result = $result . " or " . substr($u,0,4) . "-" . substr($u,4,2) . "-" . substr($u,6,2);
                $i++;
              }
            }
            else
              $i++;
          }
          }
        }
      }
      else
        $result = "";
    }
    else {
      /* single date case */
      if (strlen($s) < 10)
        $result = $s . "*";
      else
        $result = $s;
    }

    return $result;
  }

  function GetCatalogCount() {
    /* Open the catalog.txt file */
    $catalogFile = fopen(CATALOG_FILE,"r");
    if (!($catalogFile)) {
      print("Error opening catalog file");
      exit;
    }

    $n = 0;

    /* Read the collections from the catalog line by line */
    while (!feof($catalogFile)) {
      $s = fgets($catalogFile,512);
      if (substr($s,0,1) == "/") {
        $n++;
      }
    }
    fclose($catalogFile);

    return($n);
  }
?>
