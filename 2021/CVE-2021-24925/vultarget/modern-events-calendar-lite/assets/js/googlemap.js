// MEC GOOGLE MAPS PLUGIN
(function ($) {
    $.fn.mecGoogleMaps = function (options) {
        // Default Options
        var settings = $.extend({
            // These are the defaults.
            latitude: 0,
            longitude: 0,
            autoinit: true,
            fullscreen_button: false,
            zoom: 14,
            icon: '../img/m-01.png',
            markers: {},
            sf: {},
            geolocation: 0,
            getDirection: 0,
            directionOptions: {
                form: '#mec_get_direction_form',
                reset: '.mec-map-get-direction-reset',
                addr: '#mec_get_direction_addr',
                destination: {}
            }
        }, options);

        var bounds;
        var map;
        var infowindow;
        var loadedMarkers = [];
        var markerCluster;

        var canvas = this;
        var DOM = canvas[0];

        // Init the Map
        if (settings.autoinit) init();

        function init() {
            // Search Widget
            if (settings.sf.container !== '') {
                $(settings.sf.container).mecSearchForm({
                    id: settings.id,
                    atts: settings.atts,
                    callback: function (atts) {
                        settings.atts = atts;
                        getMarkers();
                    }
                });
            }

            // Create the options
            bounds = new google.maps.LatLngBounds();
            var center = new google.maps.LatLng(settings.latitude, settings.longitude);

            var mapOptions = {
                scrollwheel: false,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                center: center,
                zoom: settings.zoom,
                styles: settings.styles,
                fullscreenControl: settings.fullscreen_button
            };

            // Init map
            map = new google.maps.Map(DOM, mapOptions);

            // Init Infowindow
            infowindow = new google.maps.InfoWindow({
                pixelOffset: new google.maps.Size(0, -37)
            });

            // Load Markers
            loadMarkers(settings.markers);

            var clusterCalculator = function (markers, numStyles) {
                var weight = 0;

                for (var i = 0; i < markers.length; ++i) {
                    weight += markers[i].weight;
                }

                return {
                    text: weight,
                    index: Math.min(String(weight).length, numStyles)
                };
            };

            var markerClusterOptions = {
                styles: [{
                        height: 53,
                        url: settings.clustering_images + '1.png',
                        width: 53,
                        textColor: '#fff'
                    },
                    {
                        height: 56,
                        url: settings.clustering_images + '2.png',
                        width: 56,
                        textColor: '#000'
                    },
                    {
                        height: 66,
                        url: settings.clustering_images + '3.png',
                        width: 66,
                        textColor: '#fff'
                    },
                    {
                        height: 78,
                        url: settings.clustering_images + '4.png',
                        width: 78,
                        textColor: '#fff'
                    },
                    {
                        height: 90,
                        url: settings.clustering_images + '5.png',
                        width: 90,
                        textColor: '#fff'
                    }
                ]
            };

            markerCluster = new MarkerClusterer(map, null, markerClusterOptions);

            markerCluster.setCalculator(clusterCalculator);
            markerCluster.addMarkers(loadedMarkers);

            // Initialize get direction feature
            if (settings.getDirection === 1) initSimpleGetDirection();
            else if (settings.getDirection === 2) initAdvancedGetDirection();

            // Geolocation focus.
            var permission = false;

            if(typeof navigator.permissions !== 'undefined')
            {
                navigator.permissions.query({
                    name : 'geolocation'
                }).then(function(result)
                {
                    if(!settings.geolocation_focus) permission = true;

                    result.onchange = function()
                    {
                        if(result.state === 'granted') permission = true;
                    }
                });
            }

            // Geolocation
            if((settings.geolocation !== 'undefined' && settings.geolocation) && navigator.geolocation)
            {
                navigator.geolocation.getCurrentPosition(function (position)
                {
                    if(permission)
                    {
                        var center = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
                        var zoom = map.getZoom();

                        if (zoom <= 6) zoom = zoom + 5;
                        else if (zoom <= 10) zoom = zoom + 3;
                        else if (zoom <= 14) zoom = zoom + 2;
                        else if (zoom <= 18) zoom = zoom + 1;

                        map.panTo(center);
                        map.setZoom(zoom);
                    }
                });
            }
        }

        function loadMarkers(markers) {
            var f = 0;
            for (var i in markers) {
                f++;
                var dataMarker = markers[i];

                var marker = new RichMarker({
                    position: new google.maps.LatLng(dataMarker.latitude, dataMarker.longitude),
                    map: map,
                    event_ids: dataMarker.event_ids,
                    infowindow: dataMarker.infowindow,
                    lightbox: dataMarker.lightbox,
                    icon: (dataMarker.icon ? dataMarker.icon : settings.icon),
                    content: '<div class="mec-marker-container"><span class="mec-marker-wrap"><span class="mec-marker">' + dataMarker.count + '</span><span class="mec-marker-pulse-wrap"><span class="mec-marker-pulse"></span></span></span></div>',
                    shadow: 'none',
                    weight: dataMarker.count
                });

                // Marker Info-Window
                if (Math.max(document.documentElement.clientWidth, window.innerWidth || 0) > 960) {
                google.maps.event.addListener(marker, 'mouseover', function (event) {
                    infowindow.close();
                    infowindow.setContent(this.infowindow);
                    infowindow.open(map, this);
                });

                // Marker Lightbox
                google.maps.event.addListener(marker, 'click', function (event) {
                    lity(this.lightbox);
                });
                } else if (Math.max(document.documentElement.clientWidth, window.innerWidth || 0) <= 960){
                    google.maps.event.addListener(marker, 'click', function (event) {
                        infowindow.close();
                        infowindow.setContent(this.infowindow);
                        infowindow.open(map, this);
                        lity(this.lightbox);
                    }); 
                }

                // extend the bounds to include each marker's position
                bounds.extend(marker.position);

                // Added to Markers
                loadedMarkers.push(marker);
            }

            if (f > 1) map.fitBounds(bounds);

            // Set map center if only 1 marker found
            if (f === 1) {
                map.setCenter(new google.maps.LatLng(dataMarker.latitude, dataMarker.longitude));
            }
        }

        function getMarkers() {
            // Add loader
            $("#mec_googlemap_canvas" + settings.id).addClass("mec-loading");

            $.ajax({
                url: settings.ajax_url,
                data: "action=mec_map_get_markers&" + settings.atts,
                dataType: "json",
                type: "post",
                success: function (response) {
                    // Remove Markers
                    removeMarkers();

                    // Load Markers
                    loadMarkers(response.markers);

                    markerCluster.clearMarkers();
                    markerCluster.addMarkers(loadedMarkers, false);
                    markerCluster.redraw();

                    // Remove loader
                    $("#mec_googlemap_canvas" + settings.id).removeClass("mec-loading");
                },
                error: function () {
                    // Remove loader
                    $("#mec_googlemap_canvas" + settings.id).removeClass("mec-loading");
                }
            });
        }

        function removeMarkers() {
            bounds = new google.maps.LatLngBounds();

            if (loadedMarkers) {
                for (i = 0; i < loadedMarkers.length; i++) loadedMarkers[i].setMap(null);
                loadedMarkers.length = 0;
            }
        }

        var directionsDisplay;
        var directionsService;
        var startMarker;
        var endMarker;

        function initSimpleGetDirection() {
            $(settings.directionOptions.form).on('submit', function (event) {
                event.preventDefault();

                var from = $(settings.directionOptions.addr).val();
                var dest = new google.maps.LatLng(settings.directionOptions.destination.latitude, settings.directionOptions.destination.longitude);

                // Reset the direction
                if (typeof directionsDisplay !== 'undefined') {
                    directionsDisplay.setMap(null);
                    startMarker.setMap(null);
                    endMarker.setMap(null);
                }

                // Fade Google Maps canvas
                $(canvas).fadeTo(300, .4);

                directionsDisplay = new google.maps.DirectionsRenderer({
                    suppressMarkers: true
                });
                directionsService = new google.maps.DirectionsService();

                var request = {
                    origin: from,
                    destination: dest,
                    travelMode: google.maps.DirectionsTravelMode.DRIVING
                };

                directionsService.route(request, function (response, status) {
                    if (status === google.maps.DirectionsStatus.OK) {
                        directionsDisplay.setDirections(response);
                        directionsDisplay.setMap(map);

                        var leg = response.routes[0].legs[0];
                        startMarker = new google.maps.Marker({
                            position: leg.start_location,
                            map: map,
                            icon: settings.directionOptions.startMarker,
                        });

                        endMarker = new google.maps.Marker({
                            position: leg.end_location,
                            map: map,
                            icon: settings.directionOptions.endMarker,
                        });
                    }

                    // Fade Google Maps canvas
                    $(canvas).fadeTo(300, 1);
                });

                // Show reset button
                $(settings.directionOptions.reset).removeClass('mec-util-hidden');
            });

            $(settings.directionOptions.reset).on('click', function (event) {
                $(settings.directionOptions.addr).val('');
                $(settings.directionOptions.form).submit();

                // Hide reset button
                $(settings.directionOptions.reset).addClass('mec-util-hidden');
            });
        }

        function initAdvancedGetDirection() {
            $(settings.directionOptions.form).on('submit', function (event) {
                event.preventDefault();

                var from = $(settings.directionOptions.addr).val();
                var url = 'https://maps.google.com/?saddr=' + encodeURIComponent(from) + '&daddr=' + settings.directionOptions.destination.latitude + ',' + settings.directionOptions.destination.longitude;

                window.open(url);
            });
        }

        return {
            init: function () {
                init();
            }
        };
    };

}(jQuery));
