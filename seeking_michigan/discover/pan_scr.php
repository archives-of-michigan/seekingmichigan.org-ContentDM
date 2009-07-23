<?
parse_str($statusCookie, $stat);
if(isset($stat['DMSCALE'])){
$dmScale = $stat['DMSCALE'];
} else {
$dmScale = (isset($_GET["DMSCALE"]))?$_GET["DMSCALE"]:'';
}

$defaultWidth = (isset($stat['DMMENU']) && ($stat['DMMENU'] == "0"))?968:750;
$defaultHeight = (isset($stat['DMMENU']) && ($stat['DMMENU'] == "0"))?2000:1600;

$boundingText = "";
$fulltextfield = "nullfield";

/* Viewer constants */
define("DEFAULTWIDTH",$defaultWidth);    /* Default width of viewer window */
define("DEFAULTHEIGHT",$defaultHeight);   /* Default height of viewer window */
define("MAXWIDTH",640);       /* Maximum width of viewer window */
define("MAXHEIGHT",2000);      /* Maximum height of viewer window */
define("MAXSCALE",100);        /* Maximum scale value */
define("DEFAULTPANPCT",0.7);   /* Pan overlap factor */
define("MAXTHUMBDIM",150);     /* Maximum thumbnail dimension */

/* Formulate input values */
$inputarray = array();
$inputarray["CISOROOT"] = $alias;
$inputarray["CISOPTR"] = $itnum;

if($_GET['CISOSHOW']) {
  $inputarray['CISOSHOW'] = $_GET['CISOSHOW'];
}

$tool_width = 28;

$inputarray["DMWIDTH"] = (isset($_GET["DMWIDTH"]))?$_GET["DMWIDTH"]:'';
$inputarray["DMHEIGHT"] = (isset($_GET["DMHEIGHT"]))?$_GET["DMHEIGHT"]:'';
$inputarray["DMSCALE"] = $dmScale;
$inputarray["DMOLDSCALE"] = (isset($_GET["DMOLDSCALE"]))?$_GET["DMOLDSCALE"]:'';
$inputarray["DMFULL"] = (isset($_GET["DMFULL"]))?$_GET["DMFULL"]:'';
$inputarray["DMTEXT"] = (isset($_GET["DMTEXT"]))?urldecode($_GET["DMTEXT"]):$boundingText;
$inputarray["DMX"] = (isset($_GET["DMX"]))?$_GET["DMX"]:'';
$inputarray["DMY"] = (isset($_GET["DMY"]))?$_GET["DMY"]:'';
$inputarray["DMROTATE"] = (isset($_GET["DMROTATE"]))?$_GET["DMROTATE"]:'0';
$inputarray["x"] = (isset($_GET["x"]))?$_GET["x"]:'';
$inputarray["y"] = (isset($_GET["y"]))?$_GET["y"]:'';

$res = GetImageParameters($inputarray);

if ($res["image_src"] == -1) {
  $isthisImage = false;
}
else {
  $isthisImage = true;
  /* Write out all of the variables */
  $image_cisoroot = $res["image_cisoroot"];
  $image_cisoptr = $res["image_cisoptr"];
  $image_zoomin_link = $res["image_zoomin_link"];
  $image_zoomout_link = $res["image_zoomout_link"];
  $image_pct_display = $res["image_pct_display"];
  $image_full_link = $res["image_full_link"];
  $image_fit_link = $res["image_fit_link"];
  $image_width_link = $res["image_width_link"];
  $thumbnail_link = $res["thumbnail_link"];

  $image_scale = $res["image_scale"];
  $image_width = $res["image_width"];
  $image_height = $res["image_height"];
  $image_full = $res["image_full"];
  $image_x = $res["image_x"];
  $image_y = $res["image_y"];
  $image_text = $res["image_text"];

  $image_src = $res["image_src"];
  $image_oldscale = $res["image_oldscale"];
  $image_currentscale = $res["image_currentscale"];
  $image_guide_src = $res["image_guide_src"];
  $image_thumbnail_width = $res["image_thumbnail_width"];

  $image_rotateleft_link = $res["image_rotateleft_link"];
  $image_rotateright_link = $res["image_rotateright_link"];
}

/* Calculate the parameters for the image viewer */
function &GetImageParameters($inputarray) {
  
  $base_url = "CISOROOT=".$inputarray["CISOROOT"]."&amp;CISOPTR=".$inputarray["CISOPTR"];
  if(isset($inputarray['CISOSHOW'])) {
    $base_url .= "&amp;CISOSHOW=".$inputarray['CISOSHOW'];
  }
  
  global $dmrec,$querystr,$stat;
  
	$dmrec = (isset($_GET["REC"]))?$_GET["REC"]:1;
	$dmrotate = (isset($_GET["DMROTATE"]))?$_GET["DMROTATE"]:"0";

  $res = array();

  $res["image_cisoroot"] = $inputarray["CISOROOT"];
  $res["image_cisoptr"] = $inputarray["CISOPTR"];
  $res["image_cisoshow"] = $inputarray["CISOSHOW"];

  $rc = dmGetImageInfo($inputarray["CISOROOT"],$inputarray["CISOPTR"],$absfn,$ext,$imgwidth,$imgheight);
  if ($rc == -1) {
    print("<!-- No permission to access this item -->\n");
    $res["image_src"] = -1;
    return($res);
  }

  $irc = 0;

	$types = explode(',', S_ALLOWED_ZOOM_IMAGE_TYPES);
  for($i=0;$i<count($types);$i++){
    if ($ext == trim($types[$i])){
      $irc = 1;
      break;
    }
  }

  if ($irc == 1) {
    /* Read the collection default parameters */
    dmGetCollectionImageSettings($inputarray["CISOROOT"],$enabled,$minjpegdim,$step,$maxderivedimg,$viewer,$docviewer,$compareviewer,$slideshowviewer);
    $numsteps = count($step);
    $dmwidth = ($inputarray["DMWIDTH"] != "") ? $inputarray["DMWIDTH"] : $viewer["width"];
    $dmheight = ($inputarray["DMHEIGHT"] != "") ? $inputarray["DMHEIGHT"] : $viewer["height"];
    $dmscale = ($inputarray["DMSCALE"] != "") ? $inputarray["DMSCALE"] : $viewer["scale"];

    $width = $dmwidth;
    if ($width <= 0) {
      $width = DEFAULTWIDTH;
    } elseif ($width > MAXWIDTH) {
      $width = MAXWIDTH;
    }
    if (($dmheight == "A") && ($dmscale == "W")) {
      $height = $imgheight * $width / $imgwidth;
    } else {
      $height = $dmheight;
      if ($height <= 0) {
        $height = DEFAULTHEIGHT;
      } elseif ($height > MAXHEIGHT) {
        $height = MAXHEIGHT;
      }
    }

    if ($inputarray["DMX"] == "") {
      $oldx = 0;
    } else {
      $oldx = $inputarray["DMX"];
      if ($oldx < 0) {
        $oldx = 0;
      }
    }

    if ($inputarray["DMY"] == "") {
      $oldy = 0;
    } else {
      $oldy = $inputarray["DMY"];
      if ($oldy < 0) {
        $oldy = 0;
      }
    }

    if ($dmscale == "F") {
      for ($k = 0; $k < $numsteps; $k++) {
        if ((($step[$k]*$imgwidth/100) > $width) || (($step[$k]*$imgheight/100) > $height)) {
          break;
        }
      }
      $k--;
      if ($k < 0) {
        $k = 0;
      }
      $scale = $step[$k];
    } elseif ($dmscale == "W") {
      $scale = ($width * 100.0) / $imgwidth;
    } else {
      $scale = $dmscale;
      if ($scale <= 0) {
        $scale = 100;
      }
    }
    if ($scale > MAXSCALE) {
      $scale = MAXSCALE;
    }

    $nextscaleup = NextScale($scale,$step,$numsteps,1);
    $nextscaledown = NextScale($scale,$step,$numsteps,0);

    if ($inputarray["DMROTATE"] == 90) {
      $clickx = $width - $inputarray["y"];
      $clicky = $inputarray["x"];
    } elseif ($inputarray["DMROTATE"] == 180) {
      $clickx = $width - $inputarray["x"];
      $clicky = $height - $inputarray["y"];
    } elseif ($inputarray["DMROTATE"] == 270) {
      $clickx = $inputarray["y"];
      $clicky = $width - $inputarray["x"];
    } else {
      $clickx = $inputarray["x"];
      $clicky = $inputarray["y"];
    }

    $dmoldscale = $inputarray["DMOLDSCALE"];
    $dmfull = $inputarray["DMFULL"];
    $dmtext = $inputarray["DMTEXT"];
    if ($dmtext != "") {
      $dmtext = str_replace(" ","%20",$dmtext);
      $dmtext = str_replace("'","%27",$dmtext);
    }

    if ($clickx != "") {
      if ($dmoldscale == "") {
        $oldscale = $nextscaledown;
      } else {
        $oldscale = $dmoldscale;
      }
      $offsetx = $clickx;
      if ($offsetx < 0) {
        $offsetx = 0;
      }
      $offsety = $clicky;
      if ($offsety < 0) {
        $offsety = 0;
      }
      if ($dmfull == "1") {
        $offsetx = $offsetx - ($width/2);
        $offsety = $offsety - ($height/2);
      } else {
        if ((isset($dmoldscale[0])) && ($dmoldscale[0] != "")) {
          $offsetx = (int)(($scale/$oldscale) * $offsetx) - ($width/2);
          $offsety = (int)(($scale/$oldscale) * $offsety) - ($height/2);
        } else {
          $offsetx = (int)(($scale/$oldscale) * ($offsetx - ($width/2)));
          $offsety = (int)(($scale/$oldscale) * ($offsety - ($height/2)));
        }
      }
      $oldx = $oldx + $offsetx;
      if (($oldx + $width) > (($scale*$imgwidth)/100))
        $oldx = (int)(($scale*$imgwidth)/100) - $width;
	    
	    if ($oldx < 0) {
        $oldx = 0;
      }
      $oldy = $oldy + $offsety;
      if (($oldy + $height) > (($scale*$imgheight)/100))
        $oldy = (int)(($scale*$imgheight)/100) - $height;
	    if ($oldy < 0)
          $oldy = 0;
    }

    /* Zoom in */
    if ($nextscaleup > $scale) {
      if ((($scale*$imgwidth)/100) < $width) {
        $min = (int)($scale*$imgwidth)/100;
      } else {
        $min = $width;
      }
      $tempx = (int)(($nextscaleup / $scale) * ($oldx + ($min/2))) - ($width/2);
      if (($tempx + $width) > (($nextscaleup * $imgwidth)/100)) {
        $tempx = (int)(($nextscaleup * $imgwidth)/100) - $width;
      }
      if ($tempx < 0) {
        $tempx = 0;
      }
      
      if ((($scale*$imgheight)/100) < $height) {
        $min = (int)($scale*$imgheight)/100;
      } else {
        $min = $height;
      }
      $tempy = (int)(($nextscaleup / $scale) * ($oldy + ($min/2))) - ($height/2);
      if (($tempy + $height) > (($nextscaleup * $imgheight)/100)) {
        $tempy = (int)(($nextscaleup * $imgheight)/100) - $width;
      }
      if ($tempy < 0) {
        $tempy = 0;
      }
      
      $res["image_zoomin_link"] = zoom_link(
        $inputarray["CISOROOT"], $inputarray["CISOPTR"], $nextscaleup, 
        $width, $height, $tempx, $tempy, $dmtext, $dmrec, $dmrotate
      );
    } else {
      $res["image_zoomin_link"] = "";
    }

    /* Zoom out */

    if ((($scale*$imgwidth)/100) < $width)
      $min = (int)($scale*$imgwidth)/100;
    else
      $min = $width;
    $tempx = (int)(($nextscaledown / $scale) * ($oldx + ($min/2))) - ($width/2);
    if (($tempx + $width) > (($nextscaledown * $imgwidth)/100))
      $tempx = (int)(($nextscaledown * $imgwidth)/100) - $width;
    if ($tempx < 0)
      $tempx = 0;
    if ((($scale*$imgheight)/100) < $height)
      $min = (int)($scale*$imgheight)/100;
    else
      $min = $height;
    $tempy = (int)(($nextscaledown / $scale) * ($oldy + ($min/2))) - ($height/2);
    if (($tempy + $height) > (($nextscaledown * $imgheight)/100))
      $tempy = (int)(($nextscaledown * $imgheight)/100) - $height;
    if ($tempy < 0)
      $tempy = 0;
    if ($nextscaledown < $scale) {
      $res["image_zoomout_link"] = zoom_link(
        $inputarray["CISOROOT"], $inputarray["CISOPTR"], $nextscaledown, 
        $width, $height, $tempx, $tempy, $dmtext, $dmrec, $dmrotate
      );
    } else {
      $res["image_zoomout_link"] = "";
    }

    /* Scale percentage box */

    $temppct = sprintf("%.1f",$scale);
    $res["image_pct_display"] = $temppct;
    
    /* Zoom steps */
    $steps = array();
    foreach($step as $current_step) {
      $steps[$current_step] = array(
        'level' => $current_step,
        'url' => zoom_link(
          $inputarray["CISOROOT"], $inputarray["CISOPTR"], $current_step, 
          $width, $height, $tempx, $tempy, $dmtext, $dmrec, $dmrotate
      ));
    }
    ksort($steps, SORT_NUMERIC);
    $res['steps'] = $steps;
    
    $res["current_zoom"] = $scale;

    /* Full res */

    if ((($scale*$imgwidth)/100) < $width)
      $min = (int)($scale*$imgwidth)/100;
    else
      $min = $width;
    $tempx = (int)((100.0 / $scale) * ($oldx + ($min/2))) - ($width/2);
    if (($tempx + $width) > ((100 * $imgwidth)/100))
      $tempx = (int)((100 * $imgwidth)/100) - $width;
    if ($tempx < 0)
      $tempx = 0;

    if ((($scale*$imgheight)/100) < $height)
      $min = (int)($scale*$imgheight)/100;
    else
      $min = $height;
    $tempy = (int)((100.0 / $scale) * ($oldy + ($min/2))) - ($height/2);
    if (($tempy + $height) > ((100.0 * $imgheight)/100))
      $tempy = (int)((100 * $imgheight)/100) - $width;
    if ($tempy < 0)
      $tempy = 0;
    $res["image_full_link"] = $base_url."&amp;DMSCALE=100&amp;DMWIDTH=" . $width . "&amp;DMHEIGHT=" . $height . "&amp;DMX=" . $tempx . "&amp;DMY=" . $tempy."&amp;DMTEXT=" . str_replace('%2520','%20',urlencode($dmtext)) . "&REC=" . $dmrec."&amp;DMROTATE=" . $dmrotate;

    /* Fit in window */
    for ($k = 0; $k < $numsteps; $k++) {
      if ((($step[$k]*$imgwidth/100) > $width) || (($step[$k]*$imgheight/100) > $height))
        break;
    }
    $k--;
    if ($k < 0)
      $k = 0;
    $res["image_fit_link"] = $base_url."&amp;DMSCALE=" . $step[$k] . "&amp;DMWIDTH=" . $width . "&amp;DMHEIGHT=" . $height."&amp;DMTEXT=" . str_replace('%2520','%20',urlencode($dmtext)) . "&REC=" . $dmrec ."&amp;DMROTATE=" . $dmrotate;

    /* Full width */

    $tempscale = ($width * 100.0) / $imgwidth;
    $temppct = sprintf("%.5f",$tempscale);
    $res["image_width_link"] = $base_url."&amp;DMSCALE=" . $temppct . "&amp;DMWIDTH=" . $width . "&amp;DMHEIGHT=" . $height."&amp;DMTEXT=" . str_replace('%2520','%20',urlencode($dmtext)) . "&REC=" . $dmrec ."&amp;DMROTATE=" . $dmrotate;

    $res["image_scale"] = $nextscaleup;
    $res["image_width"] = $width;
    $res["image_height"] = $height;

    if ($scale == $nextscaleup)
      $res["image_full"] = "1";
    else
      $res["image_full"] = "0";

    if ((($scale*$imgwidth)/100) < $width)
      $min = (int)($scale*$imgwidth)/100;
    else
      $min = $width;
    $tempx = (int)(($nextscaleup / $scale) * ($oldx + ($min/2))) - ($width/2);
    if (($tempx + $width) > (($nextscaleup * $imgwidth)/100))
      $tempx = (int)(($nextscaleup * $imgwidth)/100) - $width;
    if ($tempx < 0)
      $tempx = 0;
    if ((($scale*$imgheight)/100) < $height)
      $min = (int)($scale*$imgheight)/100;
    else
      $min = $height;
    $tempy = (int)(($nextscaleup / $scale) * ($oldy + ($min/2))) - ($height/2);
    if ($tempy < 0)
      $tempy = 0;
    $res["image_x"] = $tempx;
    $res["image_y"] = $tempy;
    $res["image_text"] = $dmtext;

    $temppct = sprintf("%.5f",$scale);
    $res["image_src"] = "/cgi-bin/getimage.exe?".$base_url."&amp;DMSCALE=" . $temppct . "&amp;DMWIDTH=" . $width . "&amp;DMHEIGHT=" . $height . "&amp;DMX=" . $oldx . "&amp;DMY=" . $oldy . "&amp;DMTEXT=" . str_replace('%2520','%20',urlencode($dmtext)) . "&REC=" . $dmrec."&amp;DMROTATE=" . $dmrotate;

    if ($imgwidth < $imgheight)
      $scalet = (MAXTHUMBDIM * 100.0) / $imgheight;
    else
      $scalet = (MAXTHUMBDIM * 100.0) / $imgwidth;

    $thumbw = (int) ($scalet * $imgwidth / 100.0);
    $thumbh = (int) ($scalet * $imgheight / 100.0);
    $tx = (int) (($scalet / $scale) * $oldx);
    $ty = (int) (($scalet / $scale) * $oldy);
    $tw = (int) (($scalet / $scale) * $width);
    $th = (int) (($scalet / $scale) * $height);

    if ($tw > $thumbw)
      $tw = $thumbw;
    if ($th > $thumbh)
      $th = $thumbh;

    $temppct = sprintf("%.5f",$scalet);
    $res["image_oldscale"] = $temppct;

    $temppct = sprintf("%.5f",$scale);
    $res["image_currentscale"] = $temppct;

    $temppct = sprintf("%.5f",$scalet);
    $res["image_guide_src"] = "/cgi-bin/getimage.exe?".$base_url."&amp;DMSCALE=" . $temppct . "&amp;DMWIDTH=" . $thumbw . "&amp;DMHEIGHT=" . $thumbh . "&amp;DMX=0&amp;DMY=0" . "&amp;DMBOUND=" . $tx . "," . $ty . "," . $tw . "," . $th . "&REC=" . $dmrec."&amp;DMROTATE=" . $dmrotate;

    $res["image_thumbnail_width"] = $thumbw;

    /* Rotate functions */
    if($dmrotate == "360"){
      $dmrotate = "0";
    }
    $rleft = ($dmrotate + 90);
    $rright = (($dmrotate + 270) % 360);
    
    $res["image_rotateleft_link"] = stripUrlVar($querystr, 'DMROTATE'). "&amp;DMROTATE=" .$rleft;
    $res["image_rotateright_link"] = stripUrlVar($querystr, 'DMROTATE'). "&amp;DMROTATE=" .$rright;
  } else {
    $res["image_src"] = -1;
  }

  return($res);
}

/* Return the next scale value */
function NextScale($scale,$step,$numsteps,$p) {
  $match = -1;
  if ($p == 0) {    /* Next smaller */
    for ($i = 0; $i < $numsteps; $i++) {
      if ($scale <= $step[$i]) {
        $match = $i;
        break;
      }
    }
    $match--;
    if ($match < 0) {
      return($step[0]);
    }
    else {
      return($step[$match]);
    }
  }
  else {           /* Next bigger */
    for ($i = $numsteps-1; $i >= 0; $i--) {
      if ($scale >= $step[$i]) {
        $match = $i;
        break;
      }
    }
    $match++;
    if ($match == $numsteps) {
      return($step[$numsteps-1]);
    }
    else {
      return($step[$match]);
    }
  }
}

function zoom_link($alias, $ptr, $step, $width, $height, $x, $y, $dmtext, $dmrec, $dmrotate) {
  $base_url = "CISOROOT=".$alias."&amp;CISOPTR=".$ptr;
  $dmtext = str_replace('%2520','%20',urlencode($dmtext));
  return($base_url."&amp;DMSCALE=".$step. "&amp;DMWIDTH=".$width."&amp;DMHEIGHT=".$height."&amp;DMX=".$x."&amp;DMY=".$y."&amp;DMTEXT=".$dmtext."&amp;REC=".$dmrec."&amp;DMROTATE=".$dmrotate);
}
?>