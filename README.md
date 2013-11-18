DesertView
==========

PHP and JavaScript for DVR

----------------------------

assets/DVRfunctions.php
- creates a connection to the MLS API to retrieve listing data.  It also contains functions for editing the output for the address and price of the listing.

assets/MapListings.php 
- outputs an XML listing of the necessary elements for interacting with our map

assets/javascript/map.js 
- creates and updates asynchronously a Google Map with all(with a limit based on the MLS API) of the listings within the viewable pane.  It removes elements from the listing array as they go outside the viewable area.

twitterposter.php 
- Outputs recently-updated listings to the DVR twitter account.  Sends the public-text of the listing with enough room to include the link back to DVR.  Also includes the lat/log such that each tweet is searchable by city.

XmlCreator.php
- Pulls the most recent 1,250 listings and creates an XML document with them.  Then pushes this to Google/Bing/Ask webmaster sites.
