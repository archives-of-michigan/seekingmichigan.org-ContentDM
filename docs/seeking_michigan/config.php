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

require_once 'lib/collection.php';
require_once 'lib/item.php';
require_once 'lib/image.php';
require_once 'lib/compound_object.php';
require_once 'lib/item_factory.php';
require_once 'lib/content_dm.php';
require_once 'lib/search.php';
require_once 'lib/search_status.php';
