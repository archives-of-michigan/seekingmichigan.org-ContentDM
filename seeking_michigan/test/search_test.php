<?
require_once('../lib/search.php');

$search = Search::from_param_string("CISOOP1%3Dany%26CISOFIELD1%3DCISOSEARCHALL%26CISOROOT%3Dall%26CISOBOX1%3Dmichigan%26media-types%255B%255D%3Dimage%26search-button.x%3D31%26search-button.y%3D11%26search-button%3D%2B");
$record = $search->results();
echo "search: <br />";
var_dump($search);
echo "<br />";

echo "records: <br />";
var_dump($record);

?>
