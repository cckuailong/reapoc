jQuery(document).ready(function() {
	jQuery('.ewd-upcp-welcome-screen-box h2').on('click', function() {
		var page = jQuery(this).parent().data('screen');
		UPCP_Toggle_Welcome_Page(page);
	});

	jQuery('.ewd-upcp-welcome-screen-next-button').on('click', function() {
		var page = jQuery(this).data('nextaction');
		UPCP_Toggle_Welcome_Page(page);
	});

	jQuery('.ewd-upcp-welcome-screen-previous-button').on('click', function() {
		var page = jQuery(this).data('previousaction');
		UPCP_Toggle_Welcome_Page(page);
	});

	jQuery('.ewd-upcp-welcome-screen-add-category-button').on('click', function() {

		jQuery('.ewd-upcp-welcome-screen-show-created-categories').show();

		var category_name = jQuery('.ewd-upcp-welcome-screen-add-category-name input').val();
		var category_description = jQuery('.ewd-upcp-welcome-screen-add-category-description textarea').val();

		jQuery('.ewd-upcp-welcome-screen-add-category-name input').val('');
		jQuery('.ewd-upcp-welcome-screen-add-category-description textarea').val('');

		var data = 'category_name=' + category_name + '&category_description=' + category_description + '&action=ewd_upcp_welcome_add_category';
		jQuery.post(ajaxurl, data, function(response) {
			var HTML = '<tr class="upcp-welcome-screen-category">';
			HTML += '<td class="upcp-welcome-screen-category-name">' + category_name + '</td>';
			HTML += '<td class="upcp-welcome-screen-category-description">' + category_description + '</td>';
			HTML += '</tr>';

			jQuery('.ewd-upcp-welcome-screen-show-created-categories').append(HTML);

			var category = JSON.parse(response); 
			jQuery('.ewd-upcp-welcome-screen-add-catalogue-categories').append('<input type="checkbox" value="' + category.category_id + '" checked /> ' + category.category_name + '<br />');
			jQuery('.ewd-upcp-welcome-screen-add-product-category select').append('<option value="' + category.category_id + '">' + category.category_name + '</option>');
		});
	});

	jQuery('.ewd-upcp-welcome-screen-add-catalog-page-button').on('click', function() {
		var catalog_name = jQuery('.ewd-upcp-welcome-screen-add-catalog-page-name input').val();

		var categories = [];
		jQuery('.ewd-upcp-welcome-screen-add-product-category select option').each(function() {
			categories.push(jQuery(this).val());
		});

		jQuery('.ewd-upcp-welcome-screen-add-catalog-page-name input').val('');

		var data = 'catalog_name=' + catalog_name + '&categories=' + JSON.stringify(categories) + '&action=ewd_upcp_welcome_add_catalog';
		jQuery.post(ajaxurl, data, function(response) {});

		UPCP_Toggle_Welcome_Page('options');
	});

	jQuery('.ewd-upcp-welcome-screen-save-options-button').on('click', function() {
		var currency_symbol = jQuery('input[name="currency_symbol"]').val();
		var color_scheme = jQuery('input[name="color_scheme"]:checked').val();
		var product_links = jQuery('input[name="product_links"]:checked').val();
		var product_search = jQuery.map( jQuery( 'input[name="product_search\\[\\]"]:checked' ), function( n, i ) { return n.value; } ).join( ',' );

		var data = 'currency_symbol=' + currency_symbol + '&color_scheme=' + color_scheme + '&product_links=' + product_links + '&product_search=' + product_search + '&action=ewd_upcp_welcome_set_options';
		jQuery.post(ajaxurl, data, function(response) {
			jQuery('.ewd-upcp-welcome-screen-save-options-button').after('<div class="ewd-upcp-save-message"><div class="ewd-upcp-save-message-inside">Options have been saved.</div></div>');
			jQuery('.ewd-upcp-save-message').delay(2000).fadeOut(400, function() {jQuery('.ewd-upcp-save-message').remove();});
		});
	});

	jQuery('.ewd-upcp-welcome-screen-add-product-button').on('click', function() {

		jQuery('.ewd-upcp-welcome-screen-show-created-products').show();

		var product_name = jQuery('.ewd-upcp-welcome-screen-add-product-name input').val();
		var product_image = jQuery('.ewd-upcp-welcome-screen-add-product-image input[name="product_image_url"]').val();
		var product_description = jQuery('.ewd-upcp-welcome-screen-add-product-description textarea').val();
		var product_category = jQuery('.ewd-upcp-welcome-screen-add-product-category select').val();
		var product_price = jQuery('.ewd-upcp-welcome-screen-add-product-price input').val();

		jQuery('.ewd-upcp-welcome-screen-add-product-name input').val('');
		jQuery('.ewd-upcp-welcome-screen-image-preview').addClass('upcp-hidden');
		jQuery('.ewd-upcp-welcome-screen-add-product-image input[name="product_image_url"]').val('');
		jQuery('.ewd-upcp-welcome-screen-add-product-description textarea').val('');
		jQuery('.ewd-upcp-welcome-screen-add-product-price input').val('');

		var data = 'product_name=' + product_name + '&product_image=' + product_image + '&product_description=' + product_description + '&product_category=' + product_category + '&product_price=' + product_price + '&action=ewd_upcp_welcome_add_product';
		jQuery.post(ajaxurl, data, function(response) {
			var HTML = '<tr class="upcp-welcome-screen-product">';
			HTML += '<td class="upcp-welcome-screen-product-name">' + product_name + '</td>';
			HTML += '<td class="upcp-welcome-screen-product-image"><img src="' + product_image + '" /></td>';
			HTML += '<td class="upcp-welcome-screen-product-price">' + product_price + '</td>';
			HTML += '<td class="upcp-welcome-screen-product-description">' + product_description + '</td>';
			HTML += '</tr>';

			jQuery('.ewd-upcp-welcome-screen-show-created-products').append(HTML);
		});
	});
});

function UPCP_Toggle_Welcome_Page( page ) {

	jQuery( '.ewd-upcp-welcome-screen-box' ).removeClass( 'ewd-upcp-welcome-screen-open' );
	jQuery( '.ewd-upcp-welcome-screen-' + page ).addClass( 'ewd-upcp-welcome-screen-open' );
}

jQuery( document ).ready(function( $ ) {
	var custom_uploader;
 
    jQuery( '#welcome_item_image_button' ).click(function(e) {
 
        e.preventDefault();
 
        //If the uploader object has already been created, reopen the dialog
        if (custom_uploader) {
            custom_uploader.open();
            return;
        }
 
        //Extend the wp.media object
        custom_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Image',
            button: {
                text: 'Choose Image'
            },
            multiple: false
        });
 
        //When a file is selected, grab the URL and set it as the text field's value
        custom_uploader.on( 'select', function() {
            attachment = custom_uploader.state().get( 'selection' ).first().toJSON();
            jQuery( 'input[name="product_image_url"]' ).val(attachment.url);
        });
 
        //Open the uploader dialog
        custom_uploader.open();
 
    });
});