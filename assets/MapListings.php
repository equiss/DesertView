<?php
header("Content-type: text/xml"); 
header('Access-Control-Allow-Origin: http://desertviewrealty.com');

require_once("dvrFunctions.php");

$dvr = new dvrFunctions();

$lat = (isset($_GET['lat']) ? $_GET['lat'] : 33.386733);
$lng = (isset($_GET['lng']) ? $_GET['lng'] : -111.891631);
$north = (isset($_GET['north']) ? $_GET['north'] : '33.408230');
$south = (isset($_GET['south']) ? $_GET['south'] : '33.365230');
$east = (isset($_GET['east']) ? $_GET['east'] : '-111.840991');
$west = (isset($_GET['west']) ? $_GET['west'] : '-111.942271');


$data = $dvr->areaSearch($north, $south, $east, $west);

echo '<markers>';
if ($data) {
foreach($data as $record){
	$sf = $record['StandardFields'];
	$price = $dvr->nicePrice($sf['ListPrice']);
	$addyUrl = $dvr->niceAddressURL($record);
	echo '<marker ';
	echo 'name="' . htmlspecialchars(preg_replace('/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', ' ', $sf['UnparsedAddress'])) . '" ';
	echo 'lat="' . $sf['Latitude'] . '" ';
	echo 'lng="' . $sf['Longitude'] . '" ';
	echo 'photo="' . $sf['Photos'][0]['Uri300'] . '" ';
	echo 'price="' . $price . '" ';
	echo 'beds="' . $sf['BedsTotal'] . '" ';
	echo 'bath="' . $sf['BathsTotal'] . '" ';
	echo 'remarks="' . htmlspecialchars(preg_replace('/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', ' ', $sf['PublicRemarks'])) . '" ';
	echo 'url="' . $addyUrl . '" ';
	echo '/>';
		}
}		
echo '</markers>';

?>