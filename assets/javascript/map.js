//JavaScript for our map
    var overlays = [];
    var infoWindow = new google.maps.InfoWindow;
    var seenCoordinates = {};
    var geocoder;
    
    function initialize() {
        var latLng = new google.maps.LatLng(33.386733, -111.891631);
        var mapOptions = {
          zoom: 13,
          center: latLng,
          mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
        geocoder = new google.maps.Geocoder();
        
        centerMark = new google.maps.Marker({
        	map: map,
        	position: map.getCenter(),
        	visible: false
        	});
        
        	
        google.maps.event.addListenerOnce(map, 'idle', function() {
        	if (document.getElementById('address').value == 'Enter a City or Address') {
        	var north = map.getBounds().getNorthEast().lat();
        	var south = map.getBounds().getSouthWest().lat();
        	var east = map.getBounds().getNorthEast().lng();
        	var west = map.getBounds().getSouthWest().lng();
        	downloadUrl('http://www.desertviewrealty.com/XmlRss/assets/MapListings.php', '?north=' + north + '&south=' + south + '&east=' + east + '&west=' + west);
        	}
        	else {
        	codeAddress();
        	}
        	}); 
        
        	
        google.maps.event.addListener(map, 'center_changed', function() {
        	window.setTimeout(function(){
        	var north = map.getBounds().getNorthEast().lat();
        	var south = map.getBounds().getSouthWest().lat();
        	var east = map.getBounds().getNorthEast().lng();
        	var west = map.getBounds().getSouthWest().lng();
        	centerMark.setPosition(map.getCenter());
        	downloadUrl('http://www.desertviewrealty.com/XmlRss/assets/MapListings.php', '?north=' + north + '&south=' + south + '&east=' + east + '&west=' + west);
        	removeOutsideMarkers();
        	}, 100);
        	});
        
        google.maps.event.addListener(map, 'zoom_changed', function(){
        	map.setCenter(centerMark.getPosition());
        	});
   
    }
        function codeAddress() {
         var address = document.getElementById('address').value;
         geocoder.geocode( { 'address': address}, function(results, status) {
           if (status == google.maps.GeocoderStatus.OK) {
             map.setCenter(results[0].geometry.location);
           }
    });
}

	function downloadUrl(url, params) {
	 var request = window.ActiveXObject ?
	     new ActiveXObject('Microsoft.XMLHTTP') :
	     new XMLHttpRequest;
	
	 request.onreadystatechange = function() {
	   if (request.readyState == 4) {
	    listingsToMarkers(request); 
	  }
	 };
	
	 request.open("GET", url + params, true);
	 request.send();
	}
	
	function listingsToMarkers(request) {
	 var xml = request.responseXML;
            var markers = xml.documentElement.getElementsByTagName("marker");
              for (var i = 0; i < markers.length; i++) {
               var name = markers[i].getAttribute("name");
               var price = markers[i].getAttribute("price");
               var remarks = markers[i].getAttribute("remarks");
               var photo = markers[i].getAttribute("photo");
               var beds = markers[i].getAttribute("beds");
               var bath = markers[i].getAttribute("bath");
               var url = markers[i].getAttribute("url");
               var point = new google.maps.LatLng(
                parseFloat(markers[i].getAttribute("lat")),
                parseFloat(markers[i].getAttribute("lng")));
               var html = "<p style=\"color:sienna; margin-left:1px;\">" + name + "</p><img style=\"float:left\" src=\"" + photo + "\" height=\"70\" width=\"70\"><span style=\"float:right\"><b>$"+price+"</b></span><br><span style=\"margin-left:5px\"><b>Beds: </b>"+beds+"</span><br><span style=\"margin-left:5px\"><b>Baths: </b>"+bath+"<br><span style=\"margin-left:40px\"><a href=\""+url+"\">Listing Details</a></span>";
          
          var marker = new google.maps.Marker({
            position: point,
            title: name
          });
          coordHash = markHash(marker);
          if(seenCoordinates[coordHash] == null) {
	   seenCoordinates[coordHash] = 1;
	   overlays.push(marker);
	   marker.setMap(map);
	  }
          
         bindInfoWindow(marker, map, infoWindow, html);
         
         } 
	}
	
	function bindInfoWindow(marker, map, infoWindow, html) {
         google.maps.event.addListener(marker, 'click', function() {
         infoWindow.close();
         infoWindow.setContent(html);
         infoWindow.open(map, marker);
      });
    }
    	function markHash(marker) {
    	 var coordinatesHash = [ marker.getPosition().lat(), marker.getPosition().lng() ].join('');
	  return coordinatesHash.replace(".","").replace(",", "").replace("-","");
	}
	
	function removeOutsideMarkers() {
	 for (var i = 0; i < overlays.length; i++){
	  if (!map.getBounds().contains(overlays[i].getPosition())){
	   overlays[i].setMap(null);
	   hash = markHash(overlays[i]);
	    if (seenCoordinates[hash]) {
	     seenCoordinates[hash] = null;
	    }
	   overlays.splice(i, 1);
	  }
	 }
	}


      google.maps.event.addDomListener(window, 'load', initialize);