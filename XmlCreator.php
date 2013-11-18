<?php
require_once('assets/dvrFunctions.php');

$dvr = new dvrFunctions();
$data = $dvr->pullListings(25, 50);

//Attempt to loop through our data and output some stuff
		$fileTemp = '../../../tmp/DVR-XML-Temp.xml';
		$fileDest = '../XML-NewListings.xml';
		
		try {
			$fh = fopen($fileTemp, w);
			fwrite($fh, '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
');

			foreach ($data as $record) {
				$addyUrl = $dvr->niceAddressURL($record);
				$timeStamp = $record['StandardFields']['StatusChangeTimestamp'];
				$outString = '<url>
				<loc>' . $addyUrl . '</loc>';
				if ($timeStamp !== "")
				{
					$outString .= '<lastmod>' . $timeStamp . '</lastmod>';
				}
				$outString .= '</url>
				';
				fwrite($fh, $outString);
			}
			fwrite($fh, '</urlset>');
			fclose($fh);
		
			$temp = file_get_contents($fileTemp);
			file_put_contents($fileDest, $temp);
		}
		catch(Exception $e) {
		 throw new Exception('Error with file output', 0 , $e);
		}
		

		try {
		$goog = curl_init("http://www.google.com/webmasters/tools/ping?sitemap=http://www.desertviewrealty.com/XML-NewListings.xml");
		$resultGoog = curl_exec($goog);
		curl_close($goog);
		
		$bing = curl_init("http://www.bing.com/webmaster/ping.aspx?siteMap=http://www.desertviewrealty.com/XML-NewListings.xml");
		$resultBing = curl_exec($bing);
		curl_close($bing);
		
		$ask = curl_init("http://submissions.ask.com/ping?sitemap=http://www.desertviewrealty.com/XML-NewListings.xml");
		$resultAsk = curl_exec($ask);
		curl_close($ask);
		}
		catch(Exception $e) {
			throw new Exception('Error with reporting to search engines', 0 , $e);
		}

?>