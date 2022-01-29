<?php
if (!defined('WPINC')) {
    die('Closed');
}
$form = new RM_PFBC_Form("add-widget");
$form->configure(array(
    "prevent" => array("bootstrap", "jQuery"),
    "action" => ""
));
$service= new RM_Services();
$gmap_api_key= $service->get_setting('google_map_key');
wp_enqueue_script ('google_map_key', 'https://maps.googleapis.com/maps/api/js?key='.$gmap_api_key.'&libraries=places');
?>

<div class="rmagic">

    <!--Dialogue Box Starts-->
    <div class="rmcontent">
        <?php
        if (isset($data->model->field_id))
            $form->addElement(new Element_Hidden("field_id", $data->model->field_id));

        $form->addElement(new Element_HTML('<div class="rmheader">' . RM_UI_Strings::get("TITLE_MAP_WIDGET_PAGE") . '</div>'));
        
        $form->addElement(new Element_Hidden("field_type",$data->selected_field));
        $form->addElement(new Element_Hidden("form_id", $data->form_id));
        $form->addElement(new Element_HTML('<div>'));
        $form->addElement(new Element_Textbox("<b>" . RM_UI_Strings::get('LABEL_LABEL') . ":</b>", "field_label", array("class" => "rm_static_field rm_required", "required" => "1", "value" => $data->model->field_label, "longDesc"=>RM_UI_Strings::get('HELP_ADD_WIDGET_LINK'))));
        $form->addElement(new Element_HTML('</div>'));
 $form->addElement(new Element_HTML('<div class="rmrow"><div class="rmfield">&nbsp;</div><div class="rminput">'));
        $form->addElement(new Element_HTML('<div class="rm_map_container"><input id="rm-pac-input" name="field_value" value="'.$data->model->get_field_value().'" type="text" ><div id="map"></div></div></div></div>'));
        $form->addElement(new Element_Textbox("<b>" . RM_UI_Strings::get('LABEL_LAT') . ":</b>", "lat", array("id" => "rm_lat", "class" => "rm_static_field rm_latlng", "value" => $data->model->field_options->lat, "longDesc"=>RM_UI_Strings::get('HELP_LAT'))));
        $form->addElement(new Element_Textbox("<b>" . RM_UI_Strings::get('LABEL_LONG') . ":</b>", "long", array("id" => "rm_long", "class" => "rm_static_field rm_latlng", "value" => $data->model->field_options->long, "longDesc"=>RM_UI_Strings::get('HELP_LONG'))));
        $form->addElement(new Element_Number("<b>" . RM_UI_Strings::get('LABEL_ZOOM') . ":</b>", "zoom", array("id" => "rm_zoom", "class" => "rm_static_field", "value" => $data->model->field_options->zoom, "longDesc"=>RM_UI_Strings::get('HELP_ZOOM'))));
        $form->addElement(new Element_Number("<b>" . RM_UI_Strings::get('LABEL_MAP_WIDTH') . ":</b>", "width", array("id" => "rm_width", "class" => "rm_static_field", "value" => $data->model->field_options->width, "longDesc"=>RM_UI_Strings::get('HELP_MAP_WIDTH'))));
        $form->addElement(new Element_Textbox("<b>" . RM_UI_Strings::get('LABEL_CSS_CLASS') . ":</b>", "field_css_class", array("id" => "rm_field_class", "class" => "rm_static_field rm_required", "value" => $data->model->field_options->field_css_class, "longDesc"=>RM_UI_Strings::get('HELP_ADD_FIELD_CSS_CLASS'))));
        
        //Button Area
        $form->addElement(new Element_HTMLL('&#8592; &nbsp; '.__('Cancel','custom-registration-form-builder-with-submission-manager'), '?page=rm_field_manage&rm_form_id='.$data->form_id, array('class' => 'cancel')));

        $save_buttton_label = RM_UI_Strings::get('LABEL_FIELD_SAVE');

        if (isset($data->model->field_id))
            $save_buttton_label = RM_UI_Strings::get('LABEL_SAVE');

        $form->addElement(new Element_Button($save_buttton_label, "submit", array("id" => "rm_submit_btn",  "onClick" => "jQuery.prevent_field_add(event, '".RM_UI_Strings::get('MSG_REQUIRED_FIELD') ."')", "class" => "rm_btn", "name" => "submit")));


        $form->render();
        ?>
    </div>
</div>
<style>
    #map{
    height: 400px;
    width: 100%;
    }
</style>
<script>
    jQuery(document).ready(function(){
        rm_initMap('map', 'rm-pac-input', 'type-selector',["<?php echo $data->model->get_field_value(); ?>"]);
    });
    
    function rm_initMap(container_id, element_id, type_selector_id, addresses) { 
    container_id = container_id || "map";
    element_id = element_id || "rm-pac-input";
    type_selector_id = type_selector_id || "type-selector";
    addresses = addresses || false;
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
        zoom: 17,       
       
    });

    /*
     * Textbox to contain formatted address. Same input box can be used to search location either 
     * by lat long or by address.
     * @type Element
     */
    var input = /** @type {!HTMLInputElement} */(
            document.getElementById(element_id));

    var geocoder = new google.maps.Geocoder;
    var infowindow = new google.maps.InfoWindow;

    /*
     * Options to select map search criterian (Address,Establishment and LatLong)
     * As of now we don't need such options hence it is hidden on Map
     * @type Element
     */
    var types = document.getElementById(type_selector_id);
    map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
    map.controls[google.maps.ControlPosition.TOP_LEFT].push(types);

    var autocomplete = new google.maps.places.Autocomplete(input);
    autocomplete.bindTo('bounds', map);

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
    

    autocomplete.addListener('place_changed', function () {
        allMarkers= [];
        allMarkers.push(marker);
        infowindow.close();
        marker.setVisible(false);
        var place = autocomplete.getPlace();
        if (!place.geometry) {
            window.alert("<?php _e('Autocomplete\'s returned place contains no geometry','custom-registration-form-builder-with-submission-manager'); ?>");
            return;
        }

        // If the place has a geometry, then present it on a map.
        if (place.geometry.viewport) {
            map.fitBounds(place.geometry.viewport);
        } else {
            map.setCenter(place.geometry.location);
            map.setZoom(17);  // Why 17? Because it looks good.
        }
        updateLatLongFormFields(place.geometry.location);
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
        //document.getElementById(element_id).value=place.name + " " + address;
        rm_event_dispatcher('change',element_id);
        
    });

    // Sets a listener on a radio button to change the filter type on Places
    // Autocomplete.
    function setupClickListener(id, types) {
        var radioButton = document.getElementById(id);
       // console.log(radioButton);
        if(radioButton!=null){
            radioButton.addEventListener('click', function () {
            autocomplete.setTypes(types);
         }); 
            clearInterval(geocode_listner);
        }
        
    }

    //setupClickListener('changetype-all', []);
   // setupClickListener('changetype-address', ['address']);
    //setupClickListener('changetype-establishment', ['establishment']);
    var geocode_listner= setInterval(function(){setupClickListener('changetype-geocode', ['geocode']);},3000);

    /*
     * Listener to handle  click event on Map. 
     */
    map.addListener('click', function (e) {
        // Removing all the previous markers
        setMapOnAll(null);

        // Andding new marker on Map
        placeMarkerAndPanTo(e.latLng, map);
    }


    );
    
    function updateLatLongFormFields(latLng){
        jQuery("#rm_lat").val(parseFloat(latLng.lat()));
        jQuery("#rm_long").val(parseFloat(latLng.lng()));
    }
    /*
     * Function to add marker whenever user clicks on Map. Function also sets 
     * formatted address into the search box
     */
    function placeMarkerAndPanTo(latLng, map) {
        updateLatLongFormFields(latLng);
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
                    /*
                     * Updating the address location in textbox. 
                     * Dispatching on change event on the element for Angular module to identify the model changes.
                     */
                    document.getElementById(element_id).value = results[1].formatted_address;
                    rm_event_dispatcher('change', 'rm-pac-input');


                    infowindow.setContent(results[1].formatted_address);
                    infowindow.open(map, marker);
                } else {
                    window.alert("<?php _e('No results found','custom-registration-form-builder-with-submission-manager'); ?>");
                }
            } else {
                window.alert("<?php _e('Google failed due to: ','custom-registration-form-builder-with-submission-manager'); ?>" + status);
            }
        });
    }

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
                    updateLatLongFormFields(results[0].geometry.location);
                    var marker = new google.maps.Marker({
                        map: resultsMap,
                        position: results[0].geometry.location/*,
                        icon: em_map_info.gmarker*/
                    });
                    allMarkers.push(marker);
                    infowindow.setContent(results[0].formatted_address);
                    infowindow.open(map, marker);
                } 
                 else if (status === google.maps.GeocoderStatus.OVER_QUERY_LIMIT) {
                            setTimeout( 1000);
                }  
                else {
                   alert("<?php _e('Geocode was not successful for the following reason: ','custom-registration-form-builder-with-submission-manager'); ?>" + status);
                }
            });
        
            }
           

    }

}
}

function rm_event_dispatcher(event_name,id){
    
 // Create the event.
var event = document.createEvent('Event');

// Define that the event name is 'build'.
event.initEvent('change', true, true);
var elem= document.getElementById(id);
// Listen for the event.
elem.addEventListener('change', function (e) {
  // e.target matches elem
}, false);

// target can be any Element or other EventTarget.
elem.dispatchEvent(event);
}

jQuery(document).ready(function(){
   var lat_element=  jQuery("#rm_lat");
   var long_element=  jQuery("#rm_long");
   
   jQuery(".rm_latlng").change(function(){
       if(lat_element.val().trim()!="" && long_element.val().trim()!=""){
       var latLng=  lat_element.val() + "," + long_element.val(); 
       rm_initMap('map', 'rm-pac-input', 'type-selector',[latLng]);
        }
   }); 
});
</script>

