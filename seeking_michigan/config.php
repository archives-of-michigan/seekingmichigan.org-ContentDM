<?
if(preg_match('/haldigitalcollections/',$_SERVER['HTTP_HOST'])) {
  $redirect_string = 'http://seekingmichigan.cdmhost.com'.$_SERVER['PHP_SELF'];
  if($_SERVER['QUERY_STRING']) {
    $redirect_string .= '?'.@$_SERVER['QUERY_STRING'];
  }
  header('Location: '.$redirect_string);
  exit();
}

define("SEEKING_MICHIGAN_HOST","http://seekingmichigan.org");

include('vendor/framework/lib/application.php');
$SM_APP = new Application;

function app() {
  global $SM_APP;
  return $SM_APP;
}

$isImage = array('gif','jpg','tif','tiff','jp2');
$isBasicImage = array('gif','jpg');
$isPopup = array('slideshow.php','compare.php','clip.php','clipped.php','page_text.php','side_side.php','cliparticle.php','subset.php');
$login = false;
$protocol = ((isset($_SERVER["HTTPS"])) && ($_SERVER["HTTPS"] == 'on'))?"https":"http";
$url = $protocol."://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.@$_SERVER['QUERY_STRING'];
$u = parse_url($url);
$thisfile = ltrim(strtok(strrchr($u['path'],'/'), '?'),'/');

if(strpos(urldecode($url),"<") !== false){header("Location: {$protocol}://{$_SERVER['HTTP_HOST']}");}

$dmdir = (isset($extPath))?"/cdm4/":($protocol.'://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'] == $protocol.'://'.$_SERVER['HTTP_HOST'].'/index.php')?"/cdm4/":"";
$dmdiag = (dirname($_SERVER['PHP_SELF']) != 'cdm-diagnostics')?'/index.php':'#';

$bchars = array('“','”','’','â€','«','»',' ');
$gchars = array('"','"','\'','','','','&nbsp;');


define("INCLUDE_PATH","includes/"); 
define("DMSCRIPTS_PATH", ($dmdir == "/cdm4/")?"dmscripts/":"../dmscripts/");
define("CLIENT_PATH", "../cdm4/client/");
define("LANG", "en");
define("CHARSET", "utf-8");
define("DEF_CISOGRID", "thumbnail,A,1;title,A,1;subjec,A,0;descri,200,0;none,A,0;20;title,none,none,none,none");

if(isset($_GET["CISOCUST"])){
$ci = substr(urldecode($_GET["CISOCUST"]),1);
} else if(isset($_GET["CISOROOT"])){
$crt = explode(',',$_GET["CISOROOT"]);
	if(count($crt) > 1){
		if(file_exists(CLIENT_PATH."STY_".trim(substr($crt[0],1))."_style.php")){
		$ci = trim(substr($crt[0],1));
		} else {
		$ci = "";
		}
	} else {
	$ci = trim(substr($_GET["CISOROOT"],1));
	}
} else if(isset($_GET["CISOPARM"])){
$parm = explode(":",urldecode($_GET["CISOPARM"]));
$cr = explode(" ",$parm[0]);
$ci = (count($cr) == 2)?trim(substr($cr[0],1)):"";
} else {
$ci = "";
}
$isCiso = (strpos($ci,',') > 0)?0:$ci;

if(file_exists(CLIENT_PATH."STY_".$isCiso."_style.php")){
include(CLIENT_PATH."STY_".$isCiso."_style.php");
$isCustom = true;
} else {
include(CLIENT_PATH."STY_global_style.php");
$isCustom = false;
}

if((isset($_COOKIE['DMLANG'])) && (S_DMLANG != "")){
$dmlang = ($_COOKIE['DMLANG'] != "")?$_COOKIE['DMLANG']:0;
} else {
$dmlang = (S_DMLANG != "")?substr(trim(S_DMLANG),0,2):0;
}
$dmlang = (file_exists("../cdm4/client/LOC_global.php"))?$dmlang:"";

define("CLIENT_LOC_PATH", CLIENT_PATH.(($dmlang != "")?$dmlang."/":"")); 

include(CLIENT_LOC_PATH."LOC_global.php");

switch($thisfile){
    case 'seek_results.php':
      require(DMSCRIPTS_PATH."DMSystem.php");
      require(DMSCRIPTS_PATH."DMImage.php");
      break;
    case 'seek_advanced.php':
      require(DMSCRIPTS_PATH."DMSystem.php");
      break;
    case "viewer.php":
    case "discover_doc_viewer.php":    
    case "pdf_viewer.php": 
    case "subset_viewer.php": 
    case "subset.php":
    case "subset_obj.php":       
    case "pdftext_viewer.php":
    case "wtf.php":
    case "discover_item_viewer.php":
        require(DMSCRIPTS_PATH."DMSystem.php");
        require(DMSCRIPTS_PATH."DMImage.php");
        break;
    case "discover_document.php":
    case "page_text.php":
    case "pdf_text.php":
    case "side_side.php":
    case "menu_open.php":
    case "fulltext.php":
        require(DMSCRIPTS_PATH."DMSystem.php");
        require(DMSCRIPTS_PATH."DMImage.php");
        break;
    case 'print.php':
      require(DMSCRIPTS_PATH."DMSystem.php");
      require(DMSCRIPTS_PATH."DMImage.php");
    break;
}

require_once 'lib/content_dm.php';
require_once 'lib/search.php';
require_once 'lib/search_status.php';

$self = $_SERVER["PHP_SELF"];
$querystr = (isset($_SERVER['QUERY_STRING']))?$_SERVER['QUERY_STRING']:'';
$setrefer = $protocol.'://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$querystr;
$refer = (isset($_COOKIE['refer']))?substr($_COOKIE['refer'],0,strpos($_COOKIE['refer'],'&QUY')):'javascript:history.back(0)';

if((isset($_COOKIE["DMID"])) && ($_COOKIE["DMID"] != "")){
$login = true;
}

$thislang = (($dmlang != substr(S_DMLANG,0,2)) && (file_exists($slash."dc_".$dmlang.".txt")))?$dmlang:"";

/********************************************/

function stripUrlVar($u,$a){
$p = strpos($u,"$a=");
    if($p){
        if($u[$p-1] == "&"){
        $p--;
        }
    $ep = strpos($u,"&",$p+1);
        if ($ep === false){
        $u = substr($u,0,$p);
        } else {
        $u = str_replace(substr($u,$p,$ep-$p),'',$u);
        }
    }
return $u;
}

function formatDate($date){
$pattern = '([a-zA-Z])';
if(preg_match($pattern,$date)){
return $date;
} else {
$a = S_DATE_FORMAT;
$ms = array();
$ms[0] = "";
$ms[1] = "January";
$ms[2] = "February";
$ms[3] = "March";
$ms[4] = "April";
$ms[5] = "May";
$ms[6] = "June";
$ms[7] = "July";
$ms[8] = "August";
$ms[9] = "September";
$ms[10] = "October";
$ms[11] = "November";
$ms[12] = "December";

$dt = explode('-',$date);
    if((isset($dt[0]) && strlen($dt[0]) != 4) || (isset($dt[1]) && $dt[1] > 12) || (isset($dt[1]) && $dt[1] < 1)){
    $ret = $date;
    } else {
    $y = (isset($dt[0]))?$dt[0]:"";
    $m = (isset($dt[1]))?$dt[1]:"";
    $d = (isset($dt[2]))?$dt[2]:"";

    $md = (isset($dt[1]))?"-":"";
    $mh = (isset($dt[1]))?"/":"";

    $dd = (isset($dt[2]))?"-":"";
    $ds = (isset($dt[2]))?"/":"";

        if(strlen($m) == 1){
        $lm = "0".$m;
        } else {
        $lm = $m;
        }
        if($m<10){
        $m = str_replace('0','',$m);
        }
        $sm = (isset($dt[1]))?$ms[$m]:"";
        if(strlen($d) == 1){
        $ld = "0".$d;
        } else {
        $ld = $d;
        }
    $i=0;
    $f = array();
    $f[$i++] = $y.$md.$lm.$dd.$ld;
    $f[$i++] = $ld.$dd.$lm.$md.$y;
    $f[$i++] = $lm.$md.$ld.$dd.$y;
    $f[$i++] = $sm." ".$ld." ".$y;
    $f[$i++] = $ld." ".$sm." ".$y;
    $f[$i++] = $m.$mh.$d.$ds.$y;
    $f[$i++] = $d.$ds.$m.$mh.$y;
    $ret = $f[$a];
    }
}
return "<nobr>".$ret."</nobr>";
}

function linkDate($date,$alias,$field){
$str = formatDate($date);
	if(S_ALLOW_HYPERLINKING == "1"){
	$fieldType = (S_HYPERLINK_CISOFIELD == "0")?"CISOSEARCHALL":$field;
	$aliasType = (S_HYPERLINK_CISOROOT == "0")?"all":$alias;
	$str = '<a href="seek_results.php?CISOOP1=any&amp;CISOFIELD1='.$fieldType.'&amp;CISOROOT='.$aliasType.'&amp;CISOBOX1='.str_replace('-','',$date).'" target="_top">'.$str.'</a>';
	}
return $str;
}

function makeLinks($text){
$text = eregi_replace('(((http|ftp|mms|https)://)[-a-zA-Z0-9\,@!():%_\+.~#\?&//='.L_A_CHARACTER_LIST.']+)', '<a href="\\1" target="_top">\\1</a>', $text);
$text = eregi_replace('([[:space:]()[{}])(www.[-a-zA-Z0-9\,@!():%_\+.~#\?&//='.L_A_CHARACTER_LIST.']+)','<a href="'.S_PROTOCOL.'://\\2" target="_top">\\2</a>', $text);
$text = eregi_replace('()(mailto:[-a-zA-Z0-9@!():%_\+.~#?&//='.L_A_CHARACTER_LIST.']+)','<a href="
\\2" target="_top">\\2</a>', $text);
$text = str_replace(',"', '"', str_replace(',</a>', '</a>,', $text));
return $text;
}

function truncate($text, $limit, $break=" "){
    if(false !== ($breakpoint = strpos($text, $break, $limit))){
        if($breakpoint < strlen($text) - 1) {
        $text = substr($text, 0, $breakpoint);
        }
    }
return $text;
}

function isHyperlink($text, $type, $field, $alias){
$isFts = ($type == "FTS")?true:false;
    if($isFts){
        switch(S_FTS_DISPLAY){
        case "1":return hyperLink($text, $field, $alias);break;
        case "2":
        	if(strlen($text) > S_TRUNCATE_FTS_LIMIT){
        		if(S_TRUNCATE_FTS_LIMIT == "0"){
        		return "<b><a href=\"fulltext.php?CISOROOT=".$_GET['CISOROOT']."&amp;CISOPTR=".$_GET['CISOPTR']."&amp;OBJ=".$field."\">".L_HYPERLINK_VIEW."</a></b>";
				} else {
				return hyperLink(truncate($text,S_TRUNCATE_FTS_LIMIT), $field, $alias)."...<b><a href=\"fulltext.php?CISOROOT=".$_GET['CISOROOT']."&amp;CISOPTR=".$_GET['CISOPTR']."&amp;OBJ=".$field."\">".L_HYPERLINK_VIEW_ALL."</a></b>";
				}
			} else {
			return hyperLink($text, $field, $alias);
			}
		break;
		case "3":return $text;break;
		default:return $text;break;
		}
	} else {
	return hyperLink($text, $field, $alias);
	}
}

function isReadable($str){
$t = 0;
	for ($i = 0; $i < strlen($str); $i++){
	$chr = $str[$i];
	$ord = ord($chr);
		if(($ord<32) || ($ord>206)){
		$t = $t + 1;
		}
	}
	if($t > 0){
	return false;
	} else {
	return true;
	}
}

function hyperLink($text, $field, $alias){
$text = str_replace("&nbsp;"," //nobr// ",$text);
$text = preg_replace("/(<br>|<BR>|<br \/>|<BR \/>)/"," //br// ",$text)." ";
$textLength = strlen($text);
$str = '';
$fieldType = (S_HYPERLINK_CISOFIELD == "0")?"CISOSEARCHALL":$field;
$aliasType = (S_HYPERLINK_CISOROOT == "0")?"all":$alias;
$pattern = '([a-zA-Z0-9\''.L_A_CHARACTER_LIST.']*)';
$replace = '<a href="seek_results.php?CISOOP1=any&amp;CISOFIELD1='.$fieldType.'&amp;CISOROOT='.$aliasType.'&amp;CISOBOX1=\\1" target="_top">\\1</a>';

$fp = fopen("../stopwords.txt", "r");
    if((!$fp) || (S_ALLOW_HYPERLINKING == "0") || ($textLength > S_HYPERLINK_LIMIT)){
    $str = $text;
    } else {
    $lines = file('../stopwords.txt');
    $text = str_replace(' .','.',$text);
    $words = explode(" ", $text);
        for($i = 0; $i < count($words); $i++){
        $j = $i + 1;
        	if(strpos($words[$i],'&nbsp;') > 0){
        	$w = explode("&nbsp;", $words[$i]);
       		$str .= ereg_replace($pattern, $replace, $w[0])."&nbsp".ereg_replace($pattern, $replace, $w[1]);
        	} else {
            	if((preg_match("/\b(http|https|ftp|mms|mailto|br)\b/i",$words[$i])) || ($words[$i] == "à") || ($words[$i] == "//nobr//") || (substr($words[$i],0,2) == "&#") || (!isReadable($words[$i]))){
            	$str .= $words[$i]." ";
            	} else {
            	$str .= ereg_replace($pattern, $replace, $words[$i]);
               		if($i != count($words) - 1){
                    $str .= " ";
                    }
            	}
            }
        }
        $str = str_replace('<a href="seek_results.php?CISOOP1=any&amp;CISOFIELD1='.$fieldType.'&amp;CISOROOT='.$aliasType.'&amp;CISOBOX1=" target="_top"></a>', '', $str);
        foreach($lines as $sd){
        $str = str_replace('<a href="seek_results.php?CISOOP1=any&amp;CISOFIELD1='.$fieldType.'&amp;CISOROOT='.$aliasType.'&amp;CISOBOX1='.trim($sd).'" target="_top">'.trim($sd).'</a>', trim($sd), $str);
        $str = str_replace('<a href="seek_results.php?CISOOP1=any&amp;CISOFIELD1='.$fieldType.'&amp;CISOROOT='.$aliasType.'&amp;CISOBOX1='.trim(ucfirst($sd)).'" target="_top">'.trim(ucfirst($sd)).'</a>', trim(ucfirst($sd)), $str);
        $str = str_replace('<a href="seek_results.php?CISOOP1=any&amp;CISOFIELD1='.$fieldType.'&amp;CISOROOT='.$aliasType.'&amp;CISOBOX1='.trim(strtoupper($sd)).'" target="_top">'.trim(strtoupper($sd)).'</a>', trim(strtoupper($sd)), $str);
        }
    }
$str = str_replace(' //nobr// ','&nbsp;',$str);    
$str = str_replace(' //br// ','<br />',$str);    
return $str;
}

function vocabLink($text, $alias, $field){
$text = str_replace("&nbsp;","//nobr//",$text);
$str = '';
$fieldType = (S_HYPERLINK_CISOFIELD == "0")?"CISOSEARCHALL":$field;
$aliasType = (S_HYPERLINK_CISOROOT == "0")?"all":$alias;
    if(S_ALLOW_HYPERLINKING == "0"){
    $str = $text;
    } else {
    $s = explode(';',$text);
        for($i=0;$i<count($s);$i++){
        $s[$i] = trim($s[$i]);
            if(!empty($s[$i])){
            $str .= '<a href="seek_results.php?CISOOP1=exact&amp;CISOFIELD1='.$fieldType.'&amp;CISOROOT='.$aliasType.'&amp;CISOBOX1='.urlencode(str_replace('(',' ', str_replace(')',' ',$s[$i]))).'" target="_top">'.$s[$i].'</a><br />';
            }
        }
    }
$str = str_replace('//nobr//','&nbsp;',$str);
return $str;
}

function highlight_words($str,$keywords,$style){
$str = preg_replace("/(<br>|<BR>|<br \/>|<BR \/>)/"," //br// ",$str)." ";
	if($style == "red"){
		if($keywords == 1){
		$scriteria = str_replace('>','|',str_replace('<','+',@$_COOKIE['SEARCH']));
		$scriteria = explode('|',substr($scriteria,strpos($scriteria,'+')+3));
			for($i=0;$i<count($scriteria);$i++){
			$sc[$i] = trim(substr($scriteria[$i],strpos($scriteria[$i],'+')+1));
			}
		$sc = array_values($sc);
			for($i=0;$i<count($sc)-1;$i++){
				if(substr($sc[$i],0,1) == '"'){
				$keywords = str_replace('"','',$sc[$i]);
				$isProx = explode(" ",$keywords);
					if((count($isProx) == 3) && (strlen($isProx[1]) > 4) && (strpos($isProx[1],'near') !== false)){
						foreach($isProx as $word){
						$str = preg_replace("/\W$word\W/i"," <span style=\"color:".S_HIGHLIGHT_COLOR.";font-weight:bold;\">".$word."</span> ",$str);
						}	
					} else {
						if(substr($keywords,-1) == '*'){
						$keywords = trim(str_replace('*','',$keywords));
						$str = preg_replace("/\W$keywords\W/i"," <span style=\"color:".S_HIGHLIGHT_COLOR.";font-weight:bold;\">".$keywords."</span> ",$str);
						} else {
						$str = preg_replace("/\W$keywords\W/i"," <span style=\"color:".S_HIGHLIGHT_COLOR.";font-weight:bold;\">".$keywords."</span> ",$str);
						}
					}
				} else if(substr($sc[$i],-1) == '*'){
				$keywords = trim(str_replace('*','',$sc[$i]));
				$keywords = explode(' ',$keywords);
					foreach($keywords as $word){
					$str = preg_replace("/\W$word\W/i"," <span style=\"color:".S_HIGHLIGHT_COLOR.";font-weight:bold;\">".$word."</span> ",$str);
					}
				} else {
				$keywords = explode(' ',$sc[$i]);
					foreach($keywords as $word){
					$str = preg_replace("/\W$word\W/i"," <span style=\"color:".S_HIGHLIGHT_COLOR.";font-weight:bold;\">".$word."</span> ",$str);
					}
				}				
    		}
		} else {
		$str = $str;
		}  
    } elseif($style == "bold"){
        if(isset($keywords) && $keywords != ''){
            foreach($keywords as $word){
            $str = preg_replace("/$word/i","<b>".$word."</b>", substr($str, (strpos($str, $word) < 50)?0:strpos($str, $word)-50, 200));
            }
        } else {
        $str = substr($str,0,200);
        }
    } else {
    $str = $str;
    }
$str = str_replace(' //br// ','<br />',$str);
return $str;
}

function charReplace($text){
global $bchars,$gchars;
$n = count($gchars);
    for($i=0;$i<$n;$i++){
    $text = str_replace($bchars[$i],$gchars[$i],$text);
    }   
$text = str_replace('à','//acute//',$text);     
$text = preg_replace('/\s+/', ' ', $text);    
$text = preg_replace("/(<br>|<BR>|<br \/>|<BR \/>)/"," //br// ",$text)." ";
$text = preg_replace("/(<|>)/"," ",$text);
$text = str_replace(' //br// ','<br />',str_replace('//acute//','à',$text));
return $text;
}

function linkText($text){
return str_replace("'","&#39;",str_replace("\"","&#34;",$text));
}

function linkItem($str,$b){
global $record,$setrefer,$directTo,$field,$z,$r;
$nstr = ((isset($b)) && ($b == 1))?'<a href="'.$directTo.'" title="'.$record[$r][$field[0]].'">'.$str.'</a>':$str;
return $nstr;
}


function newLine($a){
	switch($a){
	case "P":$br = ". ";break;
	case "C":$br = ", ";break;	
	case "S":$br = " &nbsp;";break;	
	default:$br = "<br />";break;	
	}
return $br;
}


function sortCarrot($f,$g){
$ret = '';
global $sortby,$self,$querystr;
    if(isset($_GET["CISOSORT"])){
    $so = explode('|',$_GET["CISOSORT"]);
        if(isset($so[1])){
            if(($so[0] == $f) && ($so[1] == "f")){
            $ret = ' <a href="'.$self.'?'.stripUrlVar($querystr, 'CISOSORT').'&amp;CISOSORT='.$f.'|r" title="'.L_ALT_REVERSE_SORT.$g.'"><img src="/cdm4/images/up_arrow.gif" width="9" height="9" border="0" alt="" /> '.$g.':</a>';
            }
            if(($so[0] == $f) && ($so[1] == "r")){
            $ret = ' <a href="'.$self.'?'.stripUrlVar($querystr, 'CISOSORT').'&amp;CISOSORT='.$f.'|f" title="'.L_ALT_FORWARD_SORT.$g.'"><img src="/cdm4/images/down_arrow.gif" width="9" height="9" border="0" alt="" /> '.$g.':</a>';
            }
            elseif($so[0] != $f){
            $ret = ' <a href="'.$self.'?'.stripUrlVar($querystr, 'CISOSORT').'&amp;CISOSORT='.$f.'|f" title="'.L_ALT_FORWARD_SORT.$g.'"> '.$g.':</a>';
            }
        }
    } else {
        if(count($sortby) != 0){
            if($sortby[0] == $f){
            $ret = ' <a href="'.$self.'?'.stripUrlVar($querystr, 'CISOSORT').'&amp;CISOSORT='.$f.'|r" title="'.L_ALT_REVERSE_SORT.$g.'"><img src="/cdm4/images/up_arrow.gif" width="9" height="9" border="0" alt="" /> '.$g.':</a>';
            }
            elseif($sortby[0] != $f){
            $ret = ' <a href="'.$self.'?'.stripUrlVar($querystr, 'CISOSORT').'&amp;CISOSORT='.$f.'|f" title="'.L_ALT_FORWARD_SORT.$g.'"> '.$g.':</a>';
            }
        }
    }
return $ret;
}

function getSearchCookie($s){
$str = '';
$a = substr($s, 5);
$b = explode('<',$a);
for($i=0;$i<count($b);$i++){
$c = strpos($b[$i],'>');
$str .= substr($b[$i],0,$c).' ';
}
$str = trim($str);
return $str;
}

function getFieldName($a){
$fname = '';
global $conf;
    for($i=0;$i<count($conf);$i++){
        if($conf[$i]["nick"] == $a){
        $fname = $conf[$i]["name"];
        }
    }
return $fname;
}

function getDublinFieldName($a){
global $thislang;
$fname = '';
$conf = &dmGetDublinCoreFieldInfo($thislang);
    for($i=0;$i<count($conf);$i++){
        if($conf[$i]["nick"] == $a){
        $fname = $conf[$i]["name"];
        }
    }
return $fname;
}

function isHide($c,$a){
$hide = false;
$conf = &dmGetCollectionFieldInfo($c);
    for($i=0;$i<count($conf);$i++){
        if($conf[$i]["nick"] == $a){
        $hide = ($conf[$i]["hide"] == "1")?true:0;
        }
    }
return $hide;
}

function searchStr($a){
parse_str($a, $output);
$endstr = "";
if(isset($output['CISOPARM'])){
$parm = explode(':',$output['CISOPARM']);
    if($output['CISOOP1'] == "exact"){
    $endstr .= '"'.trim($parm[2]).'" ';
    } elseif($output['CISOOP1'] == "any"){
    $pstr = "";
    $pex = explode(' ', $parm[2]);
    $pstr = $pex[0];
        if(count($pex) > 1){
            for($i=1;$i<count($pex);$i++){
            $pstr = $pstr." OR ".$pex[$i];
            }
        }
    $endstr .= $pstr." ";
    } elseif($output['CISOOP1'] == "all"){
    $endstr .= $parm[2]." ";
    } else {
    $endstr .= " NOT(".$parm[2].") ";
    }
} else {
    if((isset($output['CISOBOX1'])) &&($output['CISOBOX1'] != "")){
    $tr1 = trim($output['CISOBOX1']);
        if($output['CISOOP1'] == "exact"){
        $endstr .= '"'.$tr1.'" ';
        } elseif($output['CISOOP1'] == "any"){
        $sstr = "";
        $ex = explode(' ', $tr1);
        $sstr = $ex[0];
            if(count($ex) > 1){
                for($i=1;$i<count($ex);$i++){
                $sstr = $sstr." OR ".$ex[$i];
                }
            }
        $endstr .= $sstr." ";
        } elseif($output['CISOOP1'] == "all"){
        $endstr .= $tr1." ";
        } else {
        $endstr .= " NOT(".$tr1.") ";
        }
    }
    if((isset($output['CISOBOX2'])) &&($output['CISOBOX2'] != "")){
    $tr2 = trim($output['CISOBOX2']);
        if($output['CISOOP2'] == "exact"){
        $endstr .= '"'.$tr2.'" ';
        } elseif($output['CISOOP2'] == "any"){
        $sstr = "";
        $ex = explode(' ', $tr2);
        $sstr = $ex[0];
            if(count($ex) > 1){
                for($i=1;$i<count($ex);$i++){
                $sstr = $sstr." OR ".$ex[$i];
                }
            }
        $endstr .= $sstr." ";
        } elseif($output['CISOOP2'] == "all"){
        $endstr .= $tr2." ";
        } else {
        $endstr .= " NOT(".$tr2.") ";
        }
    }
    if((isset($output['CISOBOX3'])) &&($output['CISOBOX3'] != "")){
    $tr3 = trim($output['CISOBOX3']);
        if($output['CISOOP3'] == "exact"){
        $endstr .= '"'.$tr3.'" ';
        } elseif($output['CISOOP3'] == "any"){
        $sstr = "";
        $ex = explode(' ', $tr3);
        $sstr = $ex[0];
            if(count($ex) > 1){
                for($i=1;$i<count($ex);$i++){
                $sstr = $sstr." OR ".$ex[$i];
                }
            }
        $endstr .= $sstr." ";
        } elseif($output['CISOOP3'] == "all"){
        $endstr .= $tr3." ";
        } else {
        $endstr .= " NOT(".$tr3.") ";
        }
    }
    if((isset($output['CISOBOX4'])) &&($output['CISOBOX4'] != "")){
    $tr4 = trim($output['CISOBOX4']);
        if($output['CISOOP4'] == "exact"){
        $endstr .= '"'.$tr4.'" ';
        } elseif($output['CISOOP4'] == "any"){
        $sstr = "";
        $ex = explode(' ', $tr4);
        $sstr = $ex[0];
            if(count($ex) > 1){
                for($i=1;$i<count($ex);$i++){
                $sstr = $sstr." OR ".$ex[$i];
                }
            }
        $endstr .= $sstr." ";
        } elseif($output['CISOOP4'] == "all"){
        $endstr .= $tr4." ";
        } else {
        $endstr .= " NOT(".$tr4.") ";
        }
    }
}
return $endstr;
}


function getAlias(){
  return ContentDM::get_alias($_GET);
}

function getSettings(){
    if(isset($_COOKIE['resPrefs']) && $_COOKIE['resPrefs'] != ""){
    $set = 'cook';
    } else if((!isset($_COOKIE['resPrefs']) || ($_COOKIE['resPrefs'] == "")) && (isset($_GET["CISOMODE"]))){
    $set = 'cqr';
    } else {
    $set = 'def';
    }
return($set);
}

function getSearchStr() {
  return(Search::generate_search_string($_GET));
}

function IsTagged($alias, $ptr){
$rc = dmGetCollectionParameters($alias, $name, $path);
return(file_exists($path.'/supp/'.$ptr.'/articles.xml'))?true:false;
}


function parseImageSrc($a,$b){
parse_str($a, $c);
return $c[$b];
}

function isSingleColl(){
global $protocol,$isCiso,$isCustom;
$s = "all";
	if($protocol."://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'] != $protocol."://".$_SERVER['HTTP_HOST']."/index.php"){
		if($isCustom){
		$thisdir = "/".$isCiso;
		} else {
		$thisdir = str_replace("cdm-", "", dirname($_SERVER["PHP_SELF"]));
		}
	$catlist = &dmGetCollectionList();
		for ($i = 0; $i < count($catlist); $i++){
			if($catlist[$i]['alias'] == $thisdir){
			$s = $thisdir;
			break;
			}
		}
	}
return $s;
}

$specifyCollection = isSingleColl();

switch(S_CUST_DIR_ALIAS_LIST){
	case "none":$custCollList = $specifyCollection;break;
	case "all":$custCollList = "all";break;
	default:
	$a = (($specifyCollection != "all")?$specifyCollection.','.S_CUST_DIR_ALIAS_LIST:S_CUST_DIR_ALIAS_LIST);
	$a = explode(",",$a);
	for($i=0;$i<count($a);$i++){
	$a[$i] = trim($a[$i]);
	}
	$a = array_unique($a);
	$a = array_values($a);
	$custCollList = implode(",",$a);
	break;
}

function custcoll(){
global $isCustom,$isCiso;
$coll = "";
	if($isCustom){
	$coll = "?CISOROOT="."/".$isCiso;	
	}
return $coll;
}

function custcollPOP(){
global $isCustom,$isCiso;
$coll = "";
	if($isCustom){
	$coll = "CISOROOT="."/".$isCiso."&amp;";	
	}
return $coll;
}

switch($dmlang){
	case "fr":$langwidth = 30;$langtwidth = 30;break;
	case "es":$langwidth = 32;$langtwidth = 10;break;
	case "de":$langwidth = 0;$langtwidth = 20;break;
	case "nl":$langwidth = 10;$langtwidth = 15;break;
	default:$langwidth = 0;$langtwidth = 0;break;
}

/* Read the image size */
function &GetThumbnailDim($alias,$ptr){
$rc = dmGetCollectionParameters($alias,$name,$path);
	if ($rc < 0) {
	return(-1);  /* no collection permission */
	}
	
	$rc = ReadItemDesc($path,$ptr,$data);
	if ($rc == -1) {
	return(-1);
	}
	
	$findval = str_replace("&amp;","&",GetXMLField("find",$data));
	
	$f = explode(".",$findval);
	$thumbfile = $path . "/image/" . "icon" . $f[0] . ".jpg";
	
	if (file_exists($thumbfile)) {
	$dim = GetImageDimensions($thumbfile);
	} else {
	$dim = array();
	$dim["width"] = 0;
	$dim["height"] = 0;
	}
return($dim);
}

function print_link($print_item) {
  $rc = dmGetCollectionParameters($print_item['alias'], $name, $path);
  if ($rc < 0) {
    return "#";
  }
  
  if (file_exists($path."/supp/".$print_item['ptr']."/index.pdf")) {
    return "/cgi-bin/showfile.exe?CISOROOT=".$print_item['alias']."&amp;CISOPTR=".$print_item['ptr']."&amp;CISOMODE=print";
  } else {
    return "print.php?CISOROOT=".$print_item['alias']."&amp;CISOPTR=".$print_item['ptr'];
  }
}

function get_item($alias, $itnum) {
  $rc = dmGetItemInfo($alias, $itnum, $xmlbuffer);
  if($rc == -1) {
    echo "This file is restricted.";
    exit();
  }
  
  $pageptr = "CISOROOT=".$alias."&amp;CISOPTR=".$itnum;
  
  $parser = xml_parser_create();
  xml_parse_into_struct($parser, $xmlbuffer, $structure, $index);
  xml_parser_free($parser);
  
  $filetype = GetFileExt($structure[$index["FIND"][0]]["value"]);
  return array(
    'alias' => $alias, 
    'ptr' => $itnum,
    'structure' => $structure, 
    'index' => $index,
    'title' => $structure[$index["TITLE"][0]]["value"],
    'query_string' => $pageptr,
    'settings' => get_item_settings($alias, $itnum, $filetype),
    'filetype' => $filetype,
    'thumbnail' => "/cgi-bin/thumbnail.exe?CISOROOT=".$alias."&amp;CISOPTR=".$itnum
  );
}

function get_sub_item($alias, $compound_items, $num, $itnum) {
  $item = $compound_items[$num];
  
  $stitle = linkText($item["title"]);
  
  if($thisdoc == "PDFdoc"){
    $pageptr = "CISOROOT=".$alias."&amp;CISOPTR=".$itnum."&amp;CISOPAGE=".$i."&amp;CISOSHOW=".$item["ptr"];
    $ptr = $itnum;
  } else {
    $pageptr = "CISOROOT=".$alias."&amp;CISOPTR=".$itnum."&amp;CISOSHOW=".$item["ptr"];
    $ptr = $item["ptr"];
  }
  
  $item_data = get_item($alias, $item["ptr"]);
  $filetype = GetFileExt($item_data["structure"][$item_data["index"]["FIND"][0]]["value"]);
  return array(
    'alias' => $alias, 
    'ptr' => $ptr, 
    'structure' => $item_data["structure"], 
    'index' => $item_data["index"], 
    'title' => $stitle,
    'query_string' => $pageptr,
    'res' => $compound_items, 
    'settings' => get_item_settings($alias, $ptr, $filetype),
    'filetype' => $item_data['filetype'],
    'thumbnail' => "/cgi-bin/thumbnail.exe?CISOROOT=".$alias."&amp;CISOPTR=".$ptr
  );
}

function get_item_settings($alias, $itnum, $filetype) {
  global $isImage;   // arrrgh! I know!
  
  $isthisImage = in_array($filetype,$isImage);

  if($isthisImage){
    dmGetCollectionImageSettings($alias, $pan_enabled, $minjpegdim, $zoomlevels, $maxderivedimg, $viewer, $docviewer, $compareviewer, $slideshowviewer);
    dmGetImageInfo($alias, $itnum, $filename, $type, $width, $height);

    return array(
      'pan_enabled' => $pan_enabled,
      'width' => $width,
      'height' => $height,
      'thumbnail' => "/cgi-bin/thumbnail.exe?CISOROOT=".$alias."&amp;CISOPTR=".$itnum,
      'full_image' => "/cgi-bin/getimage.exe?CISOROOT=".$alias."&amp;CISOPTR=".$itnum."&amp;DMWIDTH=".$width."&amp;DMHEIGHT=".$height."&amp;DMSCALE=100"
    );
  } else {
    return NULL;
  }
}

function prev_next_compound($isthisCompoundObject, $show_all, $previous_item, $next_item, $current_item_num, 
                            $totalitems, $encoded_seek_search_params, $search_position, $alias, $parent_itnum) {
  if($isthisCompoundObject && !$show_all) {
    $heading = "Document Pages";
    $previous_position = $search_position;
    $next_position = $search_position;
    include('discover/previous_next.php');
  }
}

function prev_next_search($seek_search_params, $search_position) {
  if(isset($seek_search_params) && $seek_search_params != '') {
    $encoded_seek_search_params = urlencode($seek_search_params);
    $search_status = new SearchStatus($seek_search_params, $search_position);
  
    $previous_item = $search_status->previous_item;
    if($previous_item) { $previous_position = $search_position - 1; }
    $next_item = $search_status->next_item;
    if($next_item) { $next_position = $search_position + 1; }
    $current_item_num = $search_position;
  
    $heading = "Search Results";
    $totalitems = $search_status->total_items;
    include('./discover/previous_next.php');
  }
}
?>
