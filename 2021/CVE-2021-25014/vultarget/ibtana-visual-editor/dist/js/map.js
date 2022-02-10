jQuery(function ($) {

    if (typeof loadScriptAsync === 'undefined') {
        function loadScriptAsync(src) {
            return new Promise((resolve, reject) => {
                const tag = document.createElement('script');
                tag.src = src;
                tag.async = true;
                tag.onload = () => {
                    resolve();
                };
                const firstScriptTag = document.getElementsByTagName('script')[0];
                firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
            });
        }
    }

    //GOOGLE MAP BLOCK
    $('.ive-google-map:not(.ive-google-map-ready)').each(function () {
        const $mapItem = $(this);
        $mapItem.addClass('ive-google-maps-ready');
        const apiKey = $mapItem.attr('data-apiKey');
        const apiURL = 'https://maps.googleapis.com/maps/api/js?v=3&sensor=true&libraries=places&key=' + apiKey;

        if (typeof google === 'object' && typeof google.maps === 'object') {
            initIveGoogleMap($mapItem);
        } else {
            loadScriptAsync(apiURL).then(() => {
                initIveGoogleMap($mapItem);
            });
        }
    });

    //INIT GOOGLE MAP
    function initIveGoogleMap($mapItem) {
        let styles = '';
        try {
            styles = JSON.parse($mapItem.attr('data-styles'));
        } catch (e) { }
        const mapOptions = {
            zoom: parseInt($mapItem.attr('data-zoom'), 10),
            zoomControl: 'true' === $mapItem.attr('data-show-zoom-buttons'),
            zoomControlOpt: {
                style: 'DEFAULT',
                position: 'RIGHT_BOTTOM',
            },
            mapTypeControl: 'true' === $mapItem.attr('data-show-map-type-buttons'),
            streetViewControl: 'true' === $mapItem.attr('data-show-street-view-button'),
            fullscreenControl: 'true' === $mapItem.attr('data-show-fullscreen-button'),
            draggable: 'true' === $mapItem.attr('data-option-draggable'),
            styles: styles,
        }
        const map = new google.maps.Map($mapItem[0], mapOptions);
        var request = {
            placeId: $mapItem.attr('data-placeID'),
            fields: ['place_id', 'geometry', 'name', 'formatted_address', 'adr_address', 'website']
        };

        const service = new google.maps.places.PlacesService(map);
        service.getDetails(request, (place, status) => {
            if (status === google.maps.places.PlacesServiceStatus.OK) {

                if (place.geometry.viewport) {
                    map.fitBounds(place.geometry.viewport);
                } else {
                    map.setCenter(place.geometry.location);
                }
                let markerOption = { map: map }
                const iconPointer = $mapItem.attr('data-icon-pointer');
                if (iconPointer) markerOption.icon = iconPointer;
                const marker = new google.maps.Marker(markerOption);
                // Set the position of the marker using the place ID and location.
                marker.setPlace({
                    placeId: place.place_id,
                    location: place.geometry.location
                });
                ('true' === $mapItem.attr('data-show-marker')) ? marker.setVisible(true) : marker.setVisible(false);

                const contentString = '<div class="ive-gmap-marker-window"><div class="ive-gmap-marker-place">' + place.name + '</div><div class="ive-gmap-marker-address">' +
                    place.adr_address + '</div>' +
                    '<div class="ive-gmap-marker-url"><a href="' + place.website + '" target="_blank">' + place.website + '</a></div></div>';

                const infowindow = new google.maps.InfoWindow({
                    content: contentString
                });
                marker.addListener('click', () => {
                    infowindow.open(map, marker);
                });
            }
        });
    }

});
