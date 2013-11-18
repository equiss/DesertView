<?php
require_once('assets/dvrFunctions.php');
require_once('twitteroauth/twitteroauth.php');

// Twitter Connection Info
$twitter_access_token = 'xxxxxxxxxxxxxxx';
$twitter_access_token_secret = 'xxxxxxxxxxxxxxx';
$twitter_consumer_key = 'xxxxxxxxxxxxxxx';
$twitter_consumer_secret = 'xxxxxxxxxxxxxxx';

// Connect to Twitter
$connection = new TwitterOAuth($twitter_consumer_key, $twitter_consumer_secret, $twitter_access_token, $twitter_access_token_secret);

// Pull some listings
$dvr = new dvrFunctions();
$data = $dvr->pullListings(9, 1);

// Loop through listings and output some tweets
 foreach($data as $record) {
	$sf = $record['StandardFields'];
	$listTime = strtotime($sf['StatusChangeTimestamp']);
	
	// Only tweet listings that are newer than our last round of tweets
	if (((time() - $listTime)/60) > 28.0){
		continue;
		}
		
	$addyUrl = $dvr->niceAddressURL($record);
	$remarks = substr($sf['PublicRemarks'], 0, 117);
	$tweet = $remarks . ' ' . $addyUrl;
	
	// Post Update
	$content = $connection->post('statuses/update', array('status' => $tweet, 'lat' => $sf['Latitude'], 'long' => $sf['Longitude']));
	file_put_contents('../../../tmp/twitlog.log', print_r($content), FILE_APPEND);
	sleep(90);
	}
?>