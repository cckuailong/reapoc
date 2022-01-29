function rm_show_map_widget(container_id, addresses,zoom) { 
    container_id = container_id || "map";
    addresses = addresses || false;
    zoom= zoom || 17;
    /*
     * To store all the marker object for further operations
     * @type Array
     */
    var allMarkers = [];

    /*
     * Map object with default location and zoom level
     * @type google.maps.Map
     */
    var map = new google.maps.Map(document.getElementById(container_id), {
        center: {lat: -34.397, lng: 150.644},
        zoom: zoom,       
       
    });

    var geocoder = new google.maps.Geocoder;
    var infowindow = new google.maps.InfoWindow;
    var infowindow = new google.maps.InfoWindow();
    var marker = new google.maps.Marker({
        map: map,
        anchorPoint: new google.maps.Point(0, -29)
    });

    // Adding marker on map for multiple addresses
    if (addresses) {
        geocodeAddress(geocoder, map,addresses);
    }


    // Pusging marker object into array for further operations
    allMarkers.push(marker);
    // Sets the map on all markers in the array.
    function setMapOnAll(map) {
        for (var i = 0; i < allMarkers.length; i++) {
            allMarkers[i].setMap(map);
        }
    }

    /**
     * @summary Add markers from array of addresses.
     * @param {google.maps.Geocoder} geocoder
     * @param {google.maps.Map} resultsMap
     * @param {String Array} addresses
     * 
     */
    function geocodeAddress(geocoder, resultsMap,addresses) { 
        var infowindow = new google.maps.InfoWindow;   
        for(var i=0;i<addresses.length;i++)
        {  
           
            var address= addresses[i];
            if(address){
              
             geocoder.geocode({'address': address}, function (results, status) {
                if (status === google.maps.GeocoderStatus.OK) {
                    resultsMap.setCenter(results[0].geometry.location);
                    var marker = new google.maps.Marker({
                        map: resultsMap,
                        position: results[0].geometry.location/*,
                        icon: em_map_info.gmarker*/
                    });
                    allMarkers.push(marker);
                    console.log(results[0]);
                    infowindow.setContent(results[0].formatted_address);
                    infowindow.open(map, marker);
                } 
                 else if (status === google.maps.GeocoderStatus.OVER_QUERY_LIMIT) {
                            setTimeout( 1000);
                }  
                else {
                   alert('Geocode was not successful for the following reason: ' + status);
                }
            });
        
            }
    }

}
}