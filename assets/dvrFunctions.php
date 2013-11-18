<?php

require_once('/home/equiss/public_html/desertviewrealty/wp-content/plugins/flexmls-idx/lib/flexmlsAPI/Core.php');

//Establishes a new connection to the flex/Spark API service (maybe hide the key? lulz)
	$api = new flexmlsAPI_APIAuth("xxxxxxx", "xxxxxxxxxxx");
	$api->SetApplicationName("DVR");

class dvrFunctions {

// testing functions to see output
	function searchableRes() {
		global $api;
		$result = $api->GetStandardFields();
		$standard_fields = $result[0];
		
		$searchable_fields = array();
		foreach ($standard_fields as $k => $v) {
			if ($v['Searchable']) {
				$searchable_fields[] = $k;
			}
		}
		return ($searchable_fields);
	}

// test

function test(){
global $api;
$response = $api->MakeAPICall("POST", "session");
return ($response);
}


//Return all available fields
	function fields(){
		global $api;
		$result = $api->GetStandardFields();
		return ($result);
	}

//OnMarket Date test
	static function onMarket($itemsPerPage, $pagesTotal) {
		global $api;
		$result_fields = array();
		
		for ($i = 1; $i <= $pagesTotal; $i++) {
			$result = array();
			
			$result = $api->GetListings(
				array(
				'_select' => 'MlsId,ListingId,ListPrice,PropertyType,BedsTotal,BathsTotal,BuildingAreaTotal,YearBuilt,SubdivisionName,PublicRemarks,StreetNumber,StreetDirPrefix,StreetName,StreetSuffix,StreetDirSuffix,StreetAdditionalInfo,City,StateOrProvince,PostalCode,CountyOrParish,StreetAddress,UnparsedAddress,StatusChangeTimestamp,Latitude,Longitude,Photos,OffMarketDate,ListOfficeName',
				'_orderby' => '-OnMarketDate',
				'_pagination' => 1,
				'_limit' => $itemsPerPage,
				'_page' => $i
				)
				);
				

			$result_fields = array_merge((array)$result_fields, (array)$result);
		}	
		return $result_fields;
	}
	
	

//Items per page max is 25, loops through pages collecting all the listings up to the given number of pages
//_orderby -statuschangetimestamp gives the newest listings.
	static function pullListings($itemsPerPage, $pagesTotal) {
		global $api;
		$result_fields = array();
		
		for ($i = 1; $i <= $pagesTotal; $i++) {
			$result = array();
			
			$result = $api->GetListings(
				array(
				'_select' => 'MlsId,ListingId,ListPrice,PropertyType,BedsTotal,BathsTotal,BuildingAreaTotal,YearBuilt,SubdivisionName,PublicRemarks,StreetNumber,StreetDirPrefix,StreetName,StreetSuffix,StreetDirSuffix,StreetAdditionalInfo,City,StateOrProvince,PostalCode,CountyOrParish,StreetAddress,UnparsedAddress,StatusChangeTimestamp,Latitude,Longitude,Photos,ListOfficeName,PoolFeatures',
				'_orderby' => '-StatusChangeTimestamp',
				'_pagination' => 1,
				'_limit' => $itemsPerPage,
				'_page' => $i
				)
				);
				

			$result_fields = array_merge((array)$result_fields, (array)$result);
		}	
		return $result_fields;
	}
	

// Get a single listing
 function getListing($mls) {
 	global $api;
 	
 	$result = $api->GetListings(
 		array(
 		'_filter' => "ListingId Eq '".$mls."'",
 		'_select' => 'MlsId,ListingId,ListPrice,PropertyType,BedsTotal,BathsTotal,BuildingAreaTotal,YearBuilt,SubdivisionName,PublicRemarks,StreetNumber,StreetDirPrefix,StreetName,StreetSuffix,StreetDirSuffix,StreetAdditionalInfo,City,StateOrProvince,PostalCode,CountyOrParish,StreetAddress,UnparsedAddress,StatusChangeTimestamp,Latitude,Longitude,Photos,ListOfficeName,PoolFeatures,LotSizeArea,LotSizeDimensions'
 		));
 		
 	return $result;
 }
	
//Location search for map
	function areaSearch($n, $s, $e, $w) {
		global $api;
		$poly = ''.$n.' '.$w.','.$s.' '.$w.','.$s.' '.$e.','.$n.' '.$e;
			
		$result = $api->GetListings(
			array(
			'_filter' => "Location Eq polygon('" . $poly . "')",
			'_select' => 'MlsId,ListingId,ListPrice,PropertyType,BedsTotal,BathsTotal,BuildingAreaTotal,YearBuilt,SubdivisionName,PublicRemarks,StreetNumber,StreetDirPrefix,StreetName,StreetSuffix,StreetDirSuffix,StreetAdditionalInfo,City,StateOrProvince,PostalCode,CountyOrParish,StreetAddress,UnparsedAddress,StatusChangeTimestamp,Latitude,Longitude,Photos,ListOfficeName',
			'_limit' => 50,
			));
					
		return $result;
	}
	
	
	
//	static function areaSearch($lat, $lng) {
//		global $api;
//		
//		$result = $api->GetNearbyListings(
//			array(
//			'_select' => 'ListingId,ListPrice,BedsTotal,BathsTotal,PublicRemarks,UnparsedAddress,Latitude,Longitude,Photos',
//			'_lat' => $lat,
//			'_lon' => $lng,
//			'_distance' => 1,
//			'_limit' => 50
//			));
//			
//		//$output = json_encode($result);
//		
//		return ($result);
//	}



//Make an address URL (mls only for the moment)
	static function niceAddressURL($data){
		$stanField = $data['StandardFields'];
		
		$one_line_address = "{$stanField['StreetNumber']} {$stanField['StreetDirPrefix']} {$stanField['StreetName']} ";
		$one_line_address .= "{$stanField['StreetSuffix']} {$stanField['City']} {$stanField['StateOrProvince']} {$stanField['PostalCode']}";
		$one_line_address = str_replace("********", "", $one_line_address);
		
		$return = $one_line_address .'-mls_'. $stanField['ListingId'];
		$return = preg_replace('/[^\w]/', '-', $return);
		
		return 'http://www.desertviewrealty.com/idx/'. $return;
	}
	
	//Price setup
	static  function nicePrice($val) {

    if (empty($val) or $val == "********" )
      return "";
	if ( strpos($val, '.') !== false ) {
		// has a decimal
		$places = explode(".", $val);
			if ($places[1] != "00") {
				return number_format($val, 2);
			}
		}

		return number_format($val, 0);
	}
	
	
}
?>