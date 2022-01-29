
function rmAutocomplete(curr_id) {

    this.componentForm = {
        street_number: 'short_name',
        route: 'long_name',
        locality: 'long_name',
        administrative_area_level_1: 'short_name',
        country: 'long_name',
        postal_code: 'short_name'
    };
    var self = this;


    this.rmInitAutocomplete = function () {
        /* Create the autocomplete object, restricting the search to geographical*/
        /* location types.*/
        this.autocomplete = new google.maps.places.Autocomplete(
                /** @type {!HTMLInputElement} */(document.getElementById(curr_id)),
                {types: ['geocode']});

        /* When the user selects an address from the dropdown, populate the address*/
        /* fields in the form.*/
        this.autocomplete.addListener('place_changed', this.fillInAddress);
    };

    this.fillInAddress = function () {
        /* Get the place details from the autocomplete object.*/
        var place = self.autocomplete.getPlace();

        for (var component in self.componentForm) {
            document.getElementById(curr_id + '_' + component).value = '';
            document.getElementById(curr_id + '_' + component).disabled = false;
        }

        /* Get each component of the address from the place details*/
        /* and fill the corresponding field on the form.*/
        for (var i = 0; i < place.address_components.length; i++) {
            var addressType = place.address_components[i].types[0];
            if (self.componentForm[addressType]) {
                var val = place.address_components[i][self.componentForm[addressType]];
                document.getElementById(curr_id + '_' + addressType).value = val;
            }
        }
    };

/* Bias the autocomplete object to the user's geographical location,*/
/* as supplied by the browser's 'navigator.geolocation' object.*/
    this.geolocate = function () {

        if (this.autocomplete === undefined)
            this.rmInitAutocomplete();
        /*console.log(this);*/

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (position) {
                var geolocation = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };
                var circle = new google.maps.Circle({
                    center: geolocation,
                    radius: position.coords.accuracy
                });
                self.autocomplete.setBounds(circle.getBounds());
            });
        }
    };

    this.callback = function (position) {
        var geolocation = {
            lat: position.coords.latitude,
            lng: position.coords.longitude
        };
        var circle = new google.maps.Circle({
            center: geolocation,
            radius: position.coords.accuracy
        });
        this.autocomplete.setBounds(circle.getBounds());
    };

}

/* To prevent submission of the form when 'enter' is pressed in autocomplete textobox*/
if (typeof rm_prevent_submission !== 'function') {
    function rm_prevent_submission(event) {
        if (event.which == 13 || event.keyCode == 13) {
            event.preventDefault();
        }
    }
}


function rm_validate_zipcode(name){
    if(rm_ajax.gmap_api=="" || rm_ajax.gmap_api==null)
        return;
    
    var zip_element = jQuery("#" + name + "_postal_code");
               
    var country= jQuery("#" + name + "_country");
    if(country.length==0 || country.val()=="")
        return;
        
    var url= "https://maps.googleapis.com/maps/api/geocode/json?components=country:" + country.val() + "|postal_code:";
    jQuery.get(url + zip_element.val() + "&key=" + rm_ajax.gmap_api,
                function(resp){
                    if(resp.results.length>=1){
                        var validator = zip_element.closest("form").validate();
                        zip_element.removeClass('error').next('label.error').remove();
                    } else {
                        var validator = zip_element.closest("form").validate();
                        var error= {};
                        error[name + '[zip]']= rm_ajax.invalid_zip;
                        validator.showErrors(error);
                    }
                     
                });
}


function rm_load_states(country,target_element_id,code,default_value){
    var target_element= jQuery("#" + target_element_id);
    var data = {
                    'action': 'rm_load_states',
                    'country': country
                    
               };
    if(country=="")
    {
        target_element.find("option:gt(0)").remove();
        //target_element.find("option").slice(0).remove();
        return;
    }
    jQuery.post(rm_ajax_url,
                data,
                function(resp){
                    resp = JSON.parse(resp);
                    if(typeof resp === 'object') {
                        target_element.find("option:gt(0)").remove();
                        //target_element.find("option").slice(0).remove();
                        jQuery.each(resp, function(i, item) {
                           
                            if(code!=1)
                             target_element.append(jQuery('<option>').text(resp[i]).attr('value', resp[i]));
                            else
                             target_element.append(jQuery('<option>').text(i).attr('value', i));   
                        });
                        if(default_value)
                            target_element.val(default_value);
                    }                    
                });
                
}