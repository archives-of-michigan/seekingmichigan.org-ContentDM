<?
error_reporting(E_ALL);

if(preg_match('/haldigitalcollections/',$_SERVER['HTTP_HOST'])) {
  $redirect_string = 'http://seekingmichigan.cdmhost.com'.$_SERVER['PHP_SELF'];
  if($_SERVER['QUERY_STRING']) {
    $redirect_string .= '?'.@$_SERVER['QUERY_STRING'];
  }
  header('Location: '.$redirect_string);
  exit();
}

define("SEEKING_MICHIGAN_HOST","http://seekingmichigan.org");
include('lib/item.php');
include('lib/image.php');
include('lib/compound_object.php');
include('lib/item_factory.php');

include('vendor/framework/lib/application.php');
$SM_APP = new Application;

function app() {
  global $SM_APP;
  return $SM_APP;
}

app()->add_partial_root('cdm', realpath('./include/partials'));
app()->add_helpers(array(
  realpath('./lib/helpers/seek_results.php')
));

$protocol = 'http';

define("CLIENT_PATH", "../cdm4/client/");
define("LANG", "en");

require("../dmscripts/DMSystem.php");
require("../dmscripts/DMImage.php");

$isCiso = TRUE;

require_once 'lib/content_dm.php';
require_once 'lib/search.php';
require_once 'lib/search_status.php';


$self = $_SERVER["PHP_SELF"];
$querystr = (isset($_SERVER['QUERY_STRING']))?$_SERVER['QUERY_STRING']:'';
$setrefer = $protocol.'://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$querystr;

$thislang = (($dmlang != substr(S_DMLANG,0,2)) && (file_exists($slash."dc_".$dmlang.".txt")))?$dmlang:"";

/********************************************/


$langwidth = 0;
$langtwidth = 0;
