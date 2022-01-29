/* Script to load Map field on Venu,Event pages */
function rmInitMap(curr_id) {

    /*
     * To store all the marker object for further operations
     * @type Array
     */
    var allMarkers = [];

    /*
     * Map object with default location and zoom level
     * @type google.maps.Map
     */
    var map = new google.maps.Map(document.getElementById('map' + curr_id), {
        center: {lat: -33.8688, lng: 151.2195},
        zoom: 13
    });

    /*
     * Textbox to contain formatted address. Same input box can be used to search location either 
     * by lat long or by address.
     * @type Element
     */
    var input = /** @type {!HTMLInputElement} */(
            document.getElementById(curr_id));

    var geocoder = new google.maps.Geocoder;
    var infowindow = new google.maps.InfoWindow;
    
    /* Try HTML5 geolocation.*/
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(function(position) {
        var pos = {
          lat: position.coords.latitude,
          lng: position.coords.longitude
        };
        map.setCenter(pos);
      });
    }
    
    /*
     * Options to select map search criterian (Address,Establishment and LatLong)
     * As of now we don't need such options hence it is hidden on Map
     * @type Element
     */
    /*        var types = document.getElementById('type-selector'+curr_id);
     */        map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
    /*        map.controls[google.maps.ControlPosition.TOP_LEFT].push(types);
     */
    var autocomplete = new google.maps.places.Autocomplete(input);
    autocomplete.bindTo('bounds', map);

    var infowindow = new google.maps.InfoWindow();
    var marker = new google.maps.Marker({
        map: map,
        anchorPoint: new google.maps.Point(0, -29)
    });

    /* Pushing marker object into array for further operations*/
    allMarkers.push(marker);

    autocomplete.addListener('place_changed', function () {
        allMarkers.push(marker);
        infowindow.close();
        marker.setVisible(false);
        var place = autocomplete.getPlace();
        if (!place.geometry) {
            //window.alert("Autocomplete's returned place contains no geometry");
            return;
        }

        /* If the place has a geometry, then present it on a map.*/
        if (place.geometry.viewport) {
            map.fitBounds(place.geometry.viewport);
        } else {
            map.setCenter(place.geometry.location);
            map.setZoom(17);  /* Why 17? Because it looks good.*/
        }
        marker.setIcon(/** @type {google.maps.Icon} */({
            url: place.icon,
            size: new google.maps.Size(71, 71),
            origin: new google.maps.Point(0, 0),
            anchor: new google.maps.Point(17, 34),
            scaledSize: new google.maps.Size(35, 35)
        }));
        marker.setPosition(place.geometry.location);
        marker.setVisible(true);

        var address = '';
        if (place.address_components) {
            address = [
                (place.address_components[0] && place.address_components[0].short_name || ''),
                (place.address_components[1] && place.address_components[1].short_name || ''),
                (place.address_components[2] && place.address_components[2].short_name || '')
            ].join(' ');
        }

        infowindow.setContent('<div><strong>' + place.name + '</strong><br>' + address);
        infowindow.open(map, marker);
    });

    /* Sets a listener on a radio button to change the filter type on Places*/
    
    /* Autocomplete.*/
    function setupClickListener(id, types) {
        var radioButton = document.getElementById(id);
        radioButton.addEventListener('click', function () {
            autocomplete.setTypes(types);
        });
    }

    /*setupClickListener('changetype-all'+curr_id, []);
     setupClickListener('changetype-address'+curr_id, ['address']);
     setupClickListener('changetype-establishment'+curr_id, ['establishment']);
     setupClickListener('changetype-geocode'+curr_id, ['geocode']);*/

    /*
     * Listener to handle  click event on Map. 
     */
    map.addListener('click', function (e) {
        /* Removing all the previous markers*/
        marker.setMap(null);

        /* Andding new marker on Map*/
        placeMarkerAndPanTo(e.latLng, map);
    }


    );

    /*
     * Function to add marker whenever user clicks on Map. Function also sets 
     * formatted address into the search box
     */
    function placeMarkerAndPanTo(latLng, map) {
        setMapOnAll(null);
        map.panTo(latLng);
        var latlng = {lat: parseFloat(latLng.lat()), lng: parseFloat(latLng.lng())};
        geocoder.geocode({'location': latlng}, function (results, status) {
            if (status === google.maps.GeocoderStatus.OK) {
                if (results[1]) {
                    map.setZoom(13);
                    var marker = new google.maps.Marker({
                        position: latlng,
                        map: map
                    });
                    allMarkers.push(marker);
                    document.getElementById(curr_id).value = results[1].formatted_address;
                    infowindow.setContent(results[1].formatted_address);
                    infowindow.open(map, marker);
                } else {
                    if(rm_ajax && rm_ajax.hasOwnProperty('no_results')){
                           window.alert(rm_ajax.no_results);
                    }
                }
            } else {
                //window.alert('Geocoder failed due to: ' + status);
            }
        });
    }

    /* Sets the map on all markers in the array.*/
    function setMapOnAll(map) {
        for (var i = 0; i < allMarkers.length; i++) {
            allMarkers[i].setMap(map);
        }
    }

}

if (typeof rm_prevent_submission !== 'function') {
    function rm_prevent_submission(event) {
        if (event.which == 13 || event.keyCode == 13) {
            event.preventDefault();
        }
    }
}


