jQuery(document).ready(function($) {
        //localStorage.removeItem("attributes");
	$( "select" ).change(function() {

              	//localStorage.removeItem("attributes");
		var productId = $('input[name=product_id]').val();
		var selectedName = $(this).attr("name");
		var selectedValue = $(this).find('option:selected').text();
		var storedAttributes = JSON.parse(localStorage.getItem("attributes"));

		// Already saved a selection in local storage
		if(storedAttributes){
			// Only add new selections to the local storage
			var len_value = selectedValue.length;
			if(len_value > 0){
				storedAttributes[selectedName] = selectedValue;
				localStorage.setItem("attributes", JSON.stringify(storedAttributes));
			}
		} else {
			var json_attributes = new Object();
			json_attributes.productId = productId;
			json_attributes[selectedName] = selectedValue;
			localStorage.setItem("attributes", JSON.stringify(json_attributes));
		}
				
                var storedAttributes = JSON.parse(localStorage.getItem("attributes"));
		
		// Now AJAX call to save in options
		var inputdata = {
			'action': 'woosea_storedattributes_details',
			'data_to_pass': productId,
			'storedAttributes': storedAttributes,
		}
		
		$.post(frontEndAjax.ajaxurl, inputdata, function( response ) {
		}, 'json' );

		console.log(storedAttributes);
	});

	// For shop pages
	$(".add_to_cart_button").click(function(){
		var productId = $(this).attr('data-product_id');
	
		console.log(productId);

		// Ajax frontend
		var inputdata = {
			'action': 'woosea_addtocart_details',
			'data_to_pass': productId,
		}
		
		$.post(frontEndAjax.ajaxurl, inputdata, function( response ) {
        		fbq("track", "AddToCart", {
				content_ids: "['" + response.product_id + "']",
				content_name: response.product_name,
				content_category: response.product_cats,
  				content_type: "product",
				value: response.product_price,
				currency: response.product_currency,
 			});
		}, 'json' );
	});

	// For product pages
	$(".single_add_to_cart_button").click(function(){
		var productId = $('input[name=product_id]').val();

		if(!productId){
			productId = $(this).attr('value');
		}

		console.log(productId);

		// Ajax frontend
		var inputdata = {
			'action': 'woosea_addtocart_details',
			'data_to_pass': productId,
		}

		$.post(frontEndAjax.ajaxurl, inputdata, function( response ) {
	 
			fbq("track", "AddToCart", {
				content_ids: "['" + response.product_id + "']",
				content_name: response.product_name,
				content_category: response.product_cats,
  				content_type: "product",
				value: response.product_price,
				currency: response.product_currency,
 			});
		}, 'json' );
	});
});
