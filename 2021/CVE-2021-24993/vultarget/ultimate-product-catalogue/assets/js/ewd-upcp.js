var Filtering_Running = "No";

jQuery(document).ready(function(){
	
	ewd_upcp_thumbnail_height();

	jQuery('.ewd-upcp-sidebar-toggle').click(function(){
		jQuery('.ewd-upcp-catalog-sidebar').toggleClass('ewd-upcp-catalog-sidebar-hidden');
	});

	var thumbContainerWidth = 0,thumbContainerHeight = 0,thumbHolderWidth = 0,thumbImageWidth = 0,thumbImageHeight = 0,numberOfImages = 0;
	var thumbnailHolderContainer,thumbnailControls;

	jQuery(".jquery-prod-cat-value").change(function(){
		var CatValues = [];
		jQuery('.jquery-prod-cat-value').each(function() {if (jQuery(this).prop('checked')) {CatValues.push(jQuery(this).val());}});
		jQuery('#upcp-selected-categories').val(CatValues);
		UPCP_Dynamic_Disabling(CatValues);
	});
	jQuery(".upcp-jquery-cat-dropdown").change(function() {
		var CatValues = [];
		if (jQuery(this).val() != 'All') {CatValues.push(jQuery(this).val());}
		jQuery('#upcp-selected-categories').val(CatValues);
		UPCP_Dynamic_Disabling(CatValues);
	});
	jQuery(".jquery-prod-sub-cat-value").change(function(){
		var SubCatValues = [];
		jQuery('.jquery-prod-sub-cat-value').each(function() {if (jQuery(this).prop('checked')) {SubCatValues.push(jQuery(this).val());}});
		jQuery('#upcp-selected-subcategories').val(SubCatValues);
	});
	jQuery(".jquery-prod-tag-value").change(function(){
		var TagValues = [];
		jQuery('.jquery-prod-tag-value').each(function() {if (jQuery(this).prop('checked')) {TagValues.push(jQuery(this).val());}});
		jQuery('#upcp-selected-tags').val(TagValues);
	});
	jQuery(".jquery-prod-name-text").keyup(function(){
		var prod_name = jQuery(this).val();
		jQuery('#upcp-selected-prod-name').val(prod_name);
	});

	screenshotThumbHolderWidth();
	jQuery('.upcp-catalogue-link ').hover(
		function(){jQuery(this).children('.upcp-prod-desc-custom-fields').fadeIn(400);},
		function(){jQuery(this).children('.upcp-prod-desc-custom-fields').fadeOut(400);}
	);
	jQuery('.upcp-minimal-img-div').hover(
		function(){jQuery(this).children('.upcp-prod-desc-custom-fields').fadeIn(400);},
		function(){jQuery(this).children('.upcp-prod-desc-custom-fields').fadeOut(400);}
	);

	var heights = jQuery('.upcp-minimal-product-listing').map(function ()
	{
	    return jQuery(this).height();
	}).get(),
	maxWidgetHeight = Math.max.apply(null, heights);

	jQuery('.upcp-minimal-product-listing').each(function (index, value) {
	jQuery(this).height(maxWidgetHeight);
	});

	jQuery('.upcp-tab-slide').on('click', function(event) {
		jQuery('.upcp-tabbed-tab').each(function() {jQuery(this).addClass('upcp-Hide-Item');});
		jQuery('.upcp-tabbed-layout-tab').each(function() {jQuery(this).addClass('upcp-tab-layout-tab-unclicked');});
		var TabClass = jQuery(this).data('class');
		jQuery('.'+TabClass).removeClass('upcp-Hide-Item');
		jQuery('.'+TabClass+'-menu').removeClass('upcp-tab-layout-tab-unclicked');
		event.preventDefault;
	});

	jQuery('.upcp-tabbed-button-left').on('click', function() {
		jQuery('.upcp-scroll-list li:first').before(jQuery('.upcp-scroll-list li:last'));
		jQuery('.upcp-scroll-list').animate({left:'-=117px'}, 0);
		jQuery('.upcp-scroll-list').animate({left:'+=117px'}, 600);
	});
	jQuery('.upcp-tabbed-button-right').on('click', function() {
		jQuery('.upcp-scroll-list').animate({left:'-=117px'}, 600, function() {
			jQuery('.upcp-scroll-list li:last').after(jQuery('.upcp-scroll-list li:first'));
			jQuery('.upcp-scroll-list').animate({left:'+=117px'}, 0);
		});
	});

	jQuery('#upcp-name-search').on('keyup', function() {jQuery('.upcp-filtering-clear-all').removeClass('upcp-Hide-Item');});
	jQuery('.jquery-prod-cat-value, .jquery-prod-sub-cat-value, .jquery-prod-tag-value, .jquery-prod-cf-value').on('change', function() {jQuery('.upcp-filtering-clear-all').removeClass('upcp-Hide-Item');});
	jQuery('.upcp-jquery-cat-dropdown, .upcp-jquery-subcat-dropdown, .upcp-jquery-tags-dropdown, .jquery-prod-cf-select').on('change', function() {jQuery('.upcp-filtering-clear-all').removeClass('upcp-Hide-Item');});
	

	jQuery(window).resize( function() {

		adjustCatalogueHeight();
		ewd_upcp_thumbnail_height();
	});
	
});

function UPCP_Dynamic_Disabling(CatValues) {
	if (CatValues.length === 0) {jQuery('.jquery-prod-sub-cat-value').prop('disabled', false);}
	else {
		jQuery('.jquery-prod-sub-cat-value').prop('disabled', true);
		jQuery('.jquery-prod-sub-cat-value').each(function() {
			if (jQuery.inArray(jQuery(this).data('parent') + "", CatValues) !== -1) {jQuery(this).prop('disabled', false);}
			else {jQuery(this).parent().removeClass('highlightBlack');}
		});
		jQuery('.jquery-prod-sub-cat-value').each(function() {
			if (jQuery(this).prop('disabled')) {jQuery(this).prop('checked', false);}
		});
		UPCP_Ajax_Filter();
	}
}

function screenshotThumbHolderWidth(){
	var screenshotImage = jQuery('.prod-cat-addt-details-thumbs-div img:first-child');
	var thumbnailHolderContainer = jQuery('.game-thumbnail-holder');

	thumbImageWidth = screenshotImage.width();
	thumbImageHeight = screenshotImage.height();
	numberOfImages = jQuery('.prod-cat-addt-details-thumb').length;
	thumbContainerWidth = (thumbImageWidth+20)*numberOfImages;
	thumbnailHolderContainerW = thumbnailHolderContainer.width();
	thumbnailControls = jQuery('.thumbnail-control');
	//jQuery('.prod-cat-addt-details-thumbs-div').css({width:thumbContainerWidth,height:thumbImageHeight+20,position:"absolute",top:0,left:0});
	//jQuery(thumbnailHolderContainer).css({minHeight:thumbImageHeight+20,width:thumbContainerWidth});

	if(thumbContainerWidth > thumbnailHolderContainerW){
		thumbnailControls.show();
		var tnScrollerW = jQuery(".thumbnail-scroller").width();
		var tnHolderDiv = jQuery(".prod-cat-addt-details-thumbs-div").width();
		var tnScrollLimit = -tnHolderDiv + tnScrollerW + thumbImageWidth;
		jQuery('.thumbnail-nav-left').click(function(){
			var tnContainerPos = thumbnailHolderContainer.position();
			var tnContainerXPos = tnContainerPos.left;
			if(tnContainerXPos >= tnScrollLimit){
				var scrollThumbnails = tnContainerXPos - (thumbImageWidth+20);
				jQuery(thumbnailHolderContainer).animate({left:scrollThumbnails});
				jQuery('.thumbnail-nav-right').show();
			}else if(tnContainerXPos <= tnScrollLimit){
				jQuery(this).hide();
			};
		});
		jQuery('.thumbnail-nav-right').click(function(){
			var tnContainerPos = thumbnailHolderContainer.position();
			var tnContainerXPos = tnContainerPos.left;
			if(tnContainerXPos != 0){
				var scrollThumbnails = tnContainerXPos + (thumbImageWidth+20);
				jQuery(thumbnailHolderContainer).animate({left:scrollThumbnails});
				jQuery('.thumbnail-nav-left').show();
			}else if(tnContainerXPos == 0){
				jQuery(this).hide();
			}
		});
	};
};

function additionalThemeJS() {
	try{
		upcp_style_hover();
	}
	catch(e) {
	}
}

function addClickHandlers() {
	if (typeof maintain_filtering === 'undefined' || maintain_filtering === null) {maintain_filtering = "Yes";}

	if (maintain_filtering != "No") {
		jQuery(".upcp-catalogue-link").click(function(event){
			event.preventDefault();
    		var link = jQuery(this).attr('href');
    		jQuery("#upcp-hidden-filtering-form").attr('action', link);

    		if (jQuery('.upcp-lightbox-mode').length) {return;}

    		jQuery("#upcp-hidden-filtering-form").submit();
		});
	}
	additionalThemeJS();
}

function FieldFocus (Field) {
		if (Field.value == Field.defaultValue){
			  Field.value = '';
		}
}

function FieldBlur(Field) {
		if (Field.value == '') {
			  Field.value = Field.defaultValue;
		}
}

function UPCPHighlight(Field, Color) {
	var inputType = jQuery(Field).attr('name');
	jQuery('input[name="' + inputType + '"][type="radio"]').each(function(){jQuery(this).parent().removeClass('highlight' + Color)});

	if (jQuery(Field.parentNode).hasClass('highlight'+Color)) {
		  jQuery(Field.parentNode).removeClass('highlight'+Color);
	}
	else {
			jQuery(Field.parentNode).addClass('highlight'+Color);
	}
}

function UPCP_DisplayPage(PageNum) {
	jQuery('#upcp-selected-current-page').val(PageNum);
	jQuery('#upcp-current-page').html(PageNum);
	UPCP_Ajax_Filter();
}

function UPCP_Show_Hide_CF(cf_title) {
	var CFID = jQuery(cf_title).data('cfid');

	jQuery('.prod-cat-sidebar-cf-content').each(function() {
		if (jQuery(this).data('cfid') == CFID) {
			jQuery(this).slideToggle('1000', 'swing');
		}
	});
}

function UPCP_Show_Hide_Sidebar(sidebar_title) {
	var TITLE = jQuery(sidebar_title).data('title');

	jQuery('.prod-cat-sidebar-content').each(function() {
	if(jQuery(this).data('title') == TITLE) {
		jQuery(this).slideToggle('1000', 'swing');
	}
	});
}

function UPCP_Show_Hide_Subcat(sidebar_category) {
	jQuery('#subcat-collapsible-'+sidebar_category).slideToggle('1000', 'swing');
	  jQuery('#cat-collapsible-'+sidebar_category).toggleClass("clicked");
        if ( jQuery('#cat-collapsible-'+sidebar_category).hasClass("clicked") ) {
            jQuery('#cat-collapsible-'+sidebar_category).text("-");
        }
        else {
            jQuery('#cat-collapsible-'+sidebar_category).text("+");
        }
}


/* Used to track the number of times that a product is clicked in all catalogues */
function RecordView(Item_ID) {
		var data = 'Item_ID=' + Item_ID + '&action=record_view';
		jQuery.post(ajaxurl, data, function(response) {});
}

function adjustCatalogueHeight() {
 	var objHeight = 0;

    var thumbOuterHeight = 0;
    var listOuterHeight = 0;
    var detailOuterHeight = 0;
    jQuery('.prod-cat.thumb-display').each(function() {thumbOuterHeight += jQuery(this).outerHeight();});
	jQuery('.prod-cat.list-display').each(function() {listOuterHeight += jQuery(this).outerHeight();});
	jQuery('.prod-cat.detail-display').each(function() {detailOuterHeight += jQuery(this).outerHeight();});
	objHeight = Math.max(thumbOuterHeight, listOuterHeight, detailOuterHeight);

	objHeight = objHeight + 120;
    jQuery('.prod-cat-inner').height(objHeight);

    if (jQuery(window).width() <= 715) {
    	objHeight = jQuery('.prod-cat-inner').height() + jQuery('.prod-cat-sidebar').height();
    	jQuery('.prod-cat-container').height(objHeight);
	}
	else {
		objHeight = Math.max(jQuery('.prod-cat-inner').height(), jQuery('.prod-cat-sidebar').height());
		jQuery('.prod-cat-container').height(objHeight);
	}
}

function ewd_upcp_thumbnail_height() {
	jQuery( '.ewd-upcp-catalog-fixed-thumbnail .ewd-upcp-catalog-product-thumbnail-image-div' ).each( function() {

		var this_thumbnail = jQuery(this);
		var this_thumbnail_width = this_thumbnail.width();
		var this_thumbnail_height = this_thumbnail_width * .8;
		this_thumbnail.css( 'height', this_thumbnail_height+'px' );
	});
}

var infinite_scroll_loading = false;
jQuery( document ).ready( function() {

	// Filtering triggers
	jQuery( 'input[name="ewd-upcp-search"]' ).on( 'keyup', ewd_upcp_display_clear_all_reset_page_and_update_catalog );

	jQuery( 'input[name="ewd-upcp-price-slider-min"], input[name="ewd-upcp-price-slider-max"]' ).on( 'keyup', ewd_upcp_display_clear_all_reset_page_and_update_catalog );

	jQuery( '.ewd-upcp-catalog-sidebar input[type="checkbox"], .ewd-upcp-catalog-sidebar input[type="radio"]' ).on( 'click', ewd_upcp_display_clear_all_reset_page_and_update_catalog );

	jQuery( '.ewd-upcp-catalog-sidebar select' ).on( 'change', ewd_upcp_display_clear_all_reset_page_and_update_catalog );

	// View switching
	jQuery( '.ewd-upcp-toggle-icon' ).on( 'click', function() { 

		jQuery( '.ewd-upcp-catalog-view' ).addClass( 'ewd-upcp-hidden' );

		jQuery( '.ewd-upcp-' + jQuery( this ).data( 'view' ) + '-view' ).removeClass( 'ewd-upcp-hidden' );

		ewd_upcp_thumbnail_height();

		ewd_upcp_adjust_thumbnail_heights();
	} );

	// Pagination
	jQuery( '.pagination-links a' ).on( 'click', function() {

		if ( jQuery( this ).hasClass( 'first-page' ) ) { jQuery( 'input[name="catalog-current-page"]' ).val( 1 ); }
		if ( jQuery( this ).hasClass( 'prev-page' ) ) { jQuery( 'input[name="catalog-current-page"]' ).val( Math.max( 1, jQuery( 'input[name="catalog-current-page"]' ).val() - 1 ) ); }
		if ( jQuery( this ).hasClass( 'next-page' ) ) { jQuery( 'input[name="catalog-current-page"]' ).val( Math.min( jQuery( '.ewd-upcp-pagination' ).data( 'max_pages' ), +jQuery( 'input[name="catalog-current-page"]' ).val() + 1 ) ); }
		if ( jQuery( this ).hasClass( 'last-page' ) ) { jQuery( 'input[name="catalog-current-page"]' ).val( jQuery( '.ewd-upcp-pagination' ).data( 'max_pages' ) ); }

		jQuery( '.pagination-links a' ).removeClass( 'disabled' );

		if ( jQuery( 'input[name="catalog-current-page"]' ).val() == 1 ) {

			jQuery( '.pagination-links a.first-page, .pagination-links a.prev-page' ).addClass( 'disabled' );

			jQuery( '.pagination-links a.next-page, .pagination-links a.last-page' ).removeClass( 'disabled' );
		}

		if ( jQuery( 'input[name="catalog-current-page"]' ).val() == jQuery( '.ewd-upcp-pagination' ).data( 'max_pages' ) ) {

			jQuery( '.pagination-links a.next-page, .pagination-links a.last-page' ).addClass( 'disabled' );

			jQuery( '.pagination-links a.first-page, .pagination-links a.prev-page' ).removeClass( 'disabled' );
		}

		jQuery( '.ewd-upcp-pagination .current-page' ).html( jQuery( 'input[name="catalog-current-page"]' ).val() );

		ewd_upcp_update_catalog();
	});

	// Infinite scroll pagination
	jQuery( window ).scroll( function() {

		if ( ! ewd_upcp_php_data.infinite_scroll ) { return; }

		if ( infinite_scroll_loading ) { return; }

		if ( jQuery( 'input[name="catalog-current-page"]' ).val() == jQuery( 'input[name="catalog-max-page"]' ).val() ) { return; }

		if ( jQuery( '.ewd-upcp-catalog-display' ).position() == undefined ) { return; }

		if ( ( jQuery( '.ewd-upcp-catalog-display' ).position().top + jQuery( '.ewd-upcp-catalog-display' ).outerHeight( true ) ) >= ( jQuery( window ).height() + jQuery( window ).scrollTop() )  ) { return; }
				
		jQuery( 'input[name="catalog-current-page"]' ).val( Math.min( jQuery( 'input[name="catalog-max-page"]' ).val(), +jQuery( 'input[name="catalog-current-page"]' ).val() + 1 ) );
					
		infinite_scroll_loading = true;

		ewd_upcp_update_catalog();
	});

	// Clear all button
	jQuery( '.ewd-upcp-catalog-sidebar-clear-all' ).on( 'click', function() {

		jQuery( '.ewd-upcp-catalog-sidebar-clear-all' ).addClass( 'ewd-upcp-hidden' );

		jQuery( 'input[name="ewd-upcp-search"], input[name="ewd-upcp-search-mobile"]' ).val( '' );

		jQuery( '#ewd-upcp-price-range input[name="ewd-upcp-price-slider-min"]' ).val( jQuery( '#ewd-upcp-price-range input[name="ewd-upcp-price-slider-min"]' ).data( 'min_price' ) );
		jQuery( '#ewd-upcp-price-range input[name="ewd-upcp-price-slider-max"]' ).val( jQuery( '#ewd-upcp-price-range input[name="ewd-upcp-price-slider-max"]' ).data( 'max_price' ) );

		jQuery( '.ewd-upcp-catalog-sidebar-category input, .ewd-upcp-catalog-sidebar-subcategory input, .ewd-upcp-catalog-sidebar-tag input, .ewd-upcp-catalog-sidebar-custom-field input' ).prop( 'checked', false );
		jQuery( '.ewd-upcp-catalog-sidebar-categories select, .ewd-upcp-catalog-sidebar-subcategories select, .ewd-upcp-catalog-sidebar-tags select, .ewd-upcp-catalog-sidebar-custom-field select' ).val( 'all' );
		
		jQuery( '.ewd-upcp-catalog-sidebar-custom-field-slider' ).each( function() {

			var custom_field_id = jQuery( this ).data( 'custom_field_id' );

			var min_value = jQuery( 'input[name="ewd-upcp-custom-field-slider-min"][data-custom_field_id="' + custom_field_id + '"]' ).data( 'custom_field_minimum' );
			var max_value = jQuery( 'input[name="ewd-upcp-custom-field-slider-max"][data-custom_field_id="' + custom_field_id + '"]' ).data( 'custom_field_maximum' );

			jQuery( '.ewd-upcp-catalog-sidebar-custom-field-slider[data-custom_field_id="' + custom_field_id + '"]' ).slider( 'values', [min_value, max_value] );
		});

		ewd_upcp_reset_page_and_update_catalog();

	} );

	// Collapsible sidebar content 
	jQuery( '.ewd-upcp-catalog-sidebar-collapsible' ).on( 'click', function() {

		jQuery( this ).toggleClass( 'ewd-upcp-sidebar-content-hidden' );
	} );

	jQuery( '.ewd-upcp-taxonomy-collapsible-children' ).on( 'click', function() {

		jQuery( this ).toggleClass( 'ewd-upcp-taxonomy-collapsible-children-hidden' );

		jQuery( this ).parent().next().toggle( 'ewd-upcp-hidden' );
	} );

	// Tabbed product tabs
	jQuery( '.ewd-upcp-single-product-menu-tab' ).on( 'click', function() {

		jQuery( '.ewd-upcp-single-product-menu-tab' ).removeClass( 'ewd-upcp-single-product-menu-tab-selected' );

		jQuery( this ).addClass( 'ewd-upcp-single-product-menu-tab-selected' );

		jQuery( '.ewd-upcp-single-product-tab' ).addClass( 'ewd-upcp-hidden' );

		jQuery( '.ewd-upcp-single-product-tab[data-tab="' + jQuery( this ).data( 'tab' ) + '"]' ).removeClass( 'ewd-upcp-hidden' );
	});

	// Additional product image toggles
	jQuery( '.ewd-upcp-thumbnail-anchor' ).on( 'click', function( event ) {

		event.preventDefault();

		if ( jQuery( this ).hasClass( 'ewd-upcp-video-thumbnail' ) ) {

			jQuery( '.ewd-upcp-single-product-main-video' ).removeClass( 'ewd-upcp-hidden' );

			jQuery( '.ewd-upcp-single-product-main-image' ).addClass( 'ewd-upcp-hidden' );

			jQuery( '.ewd-upcp-single-product-main-video' ).html( jQuery( '.ewd-upcp-single-video[data-video_key="' + jQuery( this ).data( 'video_key' ) + '"]' ).html() );
		}
		else {

			jQuery( '.ewd-upcp-single-product-main-image' ).removeClass( 'ewd-upcp-hidden' );

			jQuery( '.ewd-upcp-single-product-main-video' ).addClass( 'ewd-upcp-hidden' );

			jQuery( '.ewd-upcp-single-product-main-image .ewd-upcp-product-image' ).attr( 'src', jQuery( this ).attr( 'href' ) );

			jQuery( '.ewd-upcp-single-product-main-image' ).data( 'slideIndex', jQuery( this ).data( 'slideIndex' ) );
		}
	});

	ewd_upcp_set_click_handlers();

	ewd_upcp_adjust_thumbnail_heights();

	ewd_upcp_setup_sliders();

	ewd_upcp_setup_custom_product_page();
});

function ewd_upcp_set_click_handlers() {

	jQuery( '.ewd-upcp-catalog-product-list' ).on( 'click', function( event ) {

		if ( ewd_upcp_php_data.list_click_action == 'product' ) { return; }

		event.preventDefault();

		jQuery( this ).find( '.ewd-upcp-catalog-product-list-content' ).toggleClass( 'ewd-upcp-hidden' );
	} );

	jQuery( '.ewd-upcp-lightbox-mode .ewd-upcp-catalog-product-div' ).off( 'click' ).on( 'click', function( event ) {

		if ( jQuery( event.target ).hasClass( 'ewd-upcp-product-title' ) ) { return; }

		var product = jQuery( '.ewd-upcp-catalog-product-list[data-product_id="' + jQuery( this ).data( 'product_id' ) + '"]' ).length ? jQuery( '.ewd-upcp-catalog-product-list[data-product_id="' + jQuery( this ).data( 'product_id' ) + '"]' ) :
						( jQuery( '.ewd-upcp-catalog-product-detail[data-product_id="' + jQuery( this ).data( 'product_id' ) + '"]' ).length ? jQuery( '.ewd-upcp-catalog-product-detail[data-product_id="' + jQuery( this ).data( 'product_id' ) + '"]' ) : jQuery( '.ewd-upcp-catalog-product-thumbnail[data-product_id="' + jQuery( this ).data( 'product_id' ) + '"]' ) )

		jQuery( '#ewd-upcp-lightbox-div, #ewd-upcp-lightbox-close-div, #ewd-upcp-lightbox-background-div' ).css( 'display', 'inline' );

		jQuery( '#ewd-upcp-lightbox-div-img' ).attr( 'src', jQuery( product ).find( '.ewd-upcp-product-image' ).attr( 'src' ) );
		jQuery( '#ewd-upcp-lightbox-title-div' ).html( jQuery( product ).find( '.ewd-upcp-product-title' ).html() );
		jQuery( '#ewd-upcp-lightbox-price-div' ).html( jQuery( product ).find( '.ewd-upcp-catalog-product-price' ).html() );
		jQuery( '#ewd-upcp-lightbox-description-div' ).html( jQuery( product ).find( '.ewd-upcp-catalog-product-description' ).html() );
		jQuery( '#ewd-upcp-lightbox-link-container-div a' ).attr( 'href', jQuery( product ).find( '.ewd-upcp-product-details-link' ).attr( 'href' ) );
	});

	jQuery( '#ewd-upcp-lightbox-background-div, #ewd-upcp-lightbox-close-div' ).on('click.closeLightboxMode', function() {
		
		jQuery( '#ewd-upcp-lightbox-div, #ewd-upcp-lightbox-background-div, #ewd-upcp-lightbox-close-div' ).css( 'display', 'none' );
	});

	jQuery( '.ewd-upcp-product-comparison-button' ).off( 'click' ).on( 'click', function() {

		if ( jQuery( this ).hasClass( 'ewd-upcp-comparison-clicked' ) ) {

			jQuery( '#ewd-upcp-product-comparison-form' ).find( 'input[value="' + jQuery( this ).data( 'product_id' ) + '"]' ).remove();

			jQuery( this ).removeClass( 'ewd-upcp-comparison-clicked' );
		}
		else if ( !jQuery( '#ewd-upcp-product-comparison-form' ).find( 'input[value="' + jQuery( this ).data( 'product_id' ) + '"]' ).length ) {

			jQuery( '#ewd-upcp-product-comparison-form' ).append( '<input type="hidden" name="comparison_products[]" value="' + jQuery( this ).data( 'product_id' ) + '" data-product_name="' + jQuery( this ).data( 'product_name' ) + '" />' );
		
			jQuery( this ).addClass( 'ewd-upcp-comparison-clicked' );
		}

		jQuery( '#ewd-upcp-product-comparison-instructions' ).remove();

		if ( jQuery( '#ewd-upcp-product-comparison-form' ).find( 'input' ).length >= 2 ) {

			var product_name_string = '';

			jQuery( '#ewd-upcp-product-comparison-form' ).find( 'input' ).each( function() {

				product_name_string += jQuery( this ).data( 'product_name' ) + ', ';
			});

			product_name_string = product_name_string.slice(0, -2);

			var replacement = ' and';
			product_name_string = product_name_string.replace(/,([^,]*)$/,replacement+'$1');

			var product_comparison_instructions_html = 
				'<div id="ewd-upcp-product-comparison-instructions">' + 

					ewd_upcp_php_data.compare_label + ' ' + 

					product_name_string + ' ' + 

					ewd_upcp_php_data.side_by_side_label + '!' +

					'<div class="ewd-upcp-clear"></div>' +

					'<input type="submit" value="' + ewd_upcp_php_data.compare_label + '" />' +

				'</div>';

			jQuery( '#ewd-upcp-product-comparison-form' ).append( product_comparison_instructions_html );
		}
	});

	jQuery( '.ewd-upcp-product-action-button' ).off( 'click' ).on( 'click', function() {

		jQuery( '.ewd-upcp-catalog-cart' ).removeClass( 'ewd-upcp-hidden' );

		jQuery( '.ewd-upcp-cart-item-count' ).html( +jQuery( '.ewd-upcp-cart-item-count' ).html() + 1 );

		var data = 'product_id=' + jQuery( this ).data( 'product_id' ) + '&action=ewd_upcp_add_to_cart';
    	jQuery.post( ajaxurl, data, function( response ) {} );
	});

	jQuery( '.ewd-upcp-clear-cart' ).off( 'click' ).on( 'click', function() {

		jQuery( '.ewd-upcp-catalog-cart' ).addClass( 'ewd-upcp-hidden' );

		jQuery( '.ewd-upcp-cart-item-count' ).html( '0' );

		var data = '&action=ewd_upcp_clear_cart';
    	jQuery.post( ajaxurl, data, function( response ) {} );
	});
}

function ewd_upcp_adjust_thumbnail_heights() {

	if ( ewd_upcp_php_data.disable_auto_adjust_thumbnail_heights ) { return; }

	jQuery( '.ewd-upcp-catalog-product-thumbnail' ).css( 'height', 'auto' );

	var max_height = Math.max.apply( null, jQuery( '.ewd-upcp-catalog-product-thumbnail' ).map( function () {

	    return jQuery( this ).height();
	} ).get() );

	jQuery( '.ewd-upcp-catalog-product-thumbnail' ).css( 'height', max_height );
}

function ewd_upcp_setup_sliders() {

	var min_price = jQuery( 'input[name="ewd-upcp-price-slider-min"]' ).length ? Math.floor( jQuery( 'input[name="ewd-upcp-price-slider-min"]' ).val() ) : 0;
	var max_price = jQuery( 'input[name="ewd-upcp-price-slider-max"]' ).length ? Math.ceil( jQuery( 'input[name="ewd-upcp-price-slider-max"]' ).val() ) : 10000000;

	jQuery( '#ewd-upcp-price-filter' ).slider( {

    	range: true,

    	min: min_price,
    	max: max_price,

    	values: [ min_price, max_price ],

        change: function( event, ui ) {

        	jQuery( 'input[name="ewd-upcp-price-slider-min"]' ).val( ui.values[ 0 ] );
        	jQuery( 'input[name="ewd-upcp-price-slider-max"]' ).val( ui.values[ 1 ] );

        	ewd_upcp_reset_page_and_update_catalog();
        }
    } );

    jQuery( 'input[name="ewd-upcp-price-slider-min"]' ).on( 'keyup', function() {

    	jQuery( '#ewd-upcp-price-filter' ).slider( 'values', 0, jQuery(this).val() );
    });

    jQuery( 'input[name="ewd-upcp-price-slider-max"]' ).on( 'keyup', function() {

    	jQuery( '#ewd-upcp-price-filter' ).slider( 'values', 1, jQuery(this).val() );
    });

    jQuery( '.ewd-upcp-catalog-sidebar-custom-field-slider' ).each( function() {
    	var custom_field_id = jQuery( this ).data( 'custom_field_id' );

    	var min_value = Math.floor( jQuery( 'input[name="ewd-upcp-custom-field-slider-min"][data-custom_field_id="' + custom_field_id + '"]' ).val() );
    	var max_value = Math.ceil( jQuery( 'input[name="ewd-upcp-custom-field-slider-max"][data-custom_field_id="' + custom_field_id + '"]' ).val() );

    	jQuery( '.ewd-upcp-catalog-sidebar-custom-field-slider[data-custom_field_id="' + custom_field_id + '"]' ).slider( {

    		range: true,

    		min: min_value,
    		max: max_value,

    		values: [ min_value, max_value ],

    	    change: function( event, ui ) {
    	       
    	       jQuery( 'input[name="ewd-upcp-custom-field-slider-min"][data-custom_field_id="' + custom_field_id + '"]' ).val( ui.values[ 0 ] );
    	       jQuery( 'input[name="ewd-upcp-custom-field-slider-max"][data-custom_field_id="' + custom_field_id + '"]' ).val( ui.values[ 1 ] );

    	       ewd_upcp_reset_page_and_update_catalog();
    	    }
    	});
    });

    jQuery( 'input[name="ewd-upcp-custom-field-slider-min"]' ).on( 'keyup', function() {

    	var custom_field_id = jQuery( this ).data( 'custom_field_id' );

    	jQuery( '.ewd-upcp-catalog-sidebar-custom-field-slider[data-custom_field_id="' + custom_field_id + '"]' ).slider( 'values', 0, jQuery( this ).val() );
    });

    jQuery( 'input[name="ewd-upcp-custom-field-slider-max"]' ).on( 'keyup', function() {

    	var custom_field_id = jQuery( this ).data( 'custom_field_id' );

    	jQuery( 'input[name="ewd-upcp-custom-field-slider-max"][data-custom_field_id="' + custom_field_id + '"]' ).slider( 'values', 1, jQuery( this ).val() );
    });
}

function ewd_upcp_setup_custom_product_page() {

	if ( ! jQuery( '.gridster ul' ).length ) { return; }

	// Custom product page 
	if (typeof pp_top_bottom_padding === 'undefined' || pp_top_bottom_padding === null) {pp_top_bottom_padding = 10;}
	if (typeof pp_left_right_padding === 'undefined' || pp_left_right_padding === null) {pp_left_right_padding = 10;}
	if (typeof pp_grid_width === 'undefined' || pp_grid_width === null) {pp_grid_width = 90;}
	if (typeof pp_grid_height === 'undefined' || pp_grid_height === null) {pp_grid_height = 35;}
		
	gridster = jQuery( '.gridster ul' ).gridster( {

        widget_margins: [pp_top_bottom_padding, pp_left_right_padding],
        widget_base_dimensions: [pp_grid_width, pp_grid_height],

		helper: 'clone',

   	}).data('gridster');

	if ( gridster ) { gridster.disable(); }
		
	gridster_mobile = jQuery( '.gridster-mobile ul' ).gridster( {

        widget_margins: [pp_top_bottom_padding, pp_left_right_padding],
        widget_base_dimensions: [pp_grid_width, pp_grid_height],
		
		helper: 'clone',

   	}).data('gridster');
	
	if ( gridster_mobile ) { gridster_mobile.disable(); }

	jQuery( '.ewd-upcp-custom-product-page' ).removeClass( 'ewd-upcp-hidden' );
}

function ewd_upcp_display_clear_all_reset_page_and_update_catalog() {

	jQuery( '.ewd-upcp-catalog-sidebar-clear-all' ).removeClass( 'ewd-upcp-hidden' );

	ewd_upcp_reset_page_and_update_catalog()
}

function ewd_upcp_reset_page_and_update_catalog() {

	jQuery( 'input[name="catalog-current-page"]' ).val( 1 );

	jQuery( '.ewd-upcp-pagination .current-page' ).html( '1' );

	jQuery( '.pagination-links a.first-page, .pagination-links a.prev-page' ).addClass( 'disabled' );

	jQuery( '.pagination-links a.next-page, .pagination-links a.last-page' ).removeClass( 'disabled' );
	
	ewd_upcp_update_catalog();	
}

var request_count = 0;
function ewd_upcp_update_catalog() {

	request_count = request_count + 1;

	if ( infinite_scroll_loading ) {

		jQuery( '.ewd-upcp-thumbnail-view, .ewd-upcp-list-view, .ewd-upcp-detail-view' ).append( '<h3 class="ewd-upcp-inifinite-scroll-updating">' + ewd_upcp_php_data.updating_results_label + '</h3>' );
	}
	else {

		jQuery( '.ewd-upcp-thumbnail-view, .ewd-upcp-list-view, .ewd-upcp-detail-view' ).html( '<h3>' + ewd_upcp_php_data.updating_results_label + '</h3>' );
	}

	var data = ewd_upcp_get_request_string_and_set_history();
	
	jQuery.post( ajaxurl, data, function( response ) {
		
		if ( response.data.request_count != request_count ) { return; } 

			if ( infinite_scroll_loading ) {
				
				jQuery( '.ewd-upcp-inifinite-scroll-updating' ).remove();

				jQuery( '.ewd-upcp-thumbnail-view' ).append( response.data.thumbnail_view ? response.data.thumbnail_view : ewd_upcp_php_data.no_results_found_label );
				jQuery( '.ewd-upcp-list-view' ).append( response.data.list_view ? response.data.list_view : ewd_upcp_php_data.no_results_found_label );
				jQuery( '.ewd-upcp-detail-view' ).append( response.data.detail_view ? response.data.detail_view : ewd_upcp_php_data.no_results_found_label );
			}
			else {
				
				jQuery( '.ewd-upcp-thumbnail-view' ).html( response.data.thumbnail_view ? response.data.thumbnail_view : ewd_upcp_php_data.no_results_found_label );
				jQuery( '.ewd-upcp-list-view' ).html( response.data.list_view ? response.data.list_view : ewd_upcp_php_data.no_results_found_label );
				jQuery( '.ewd-upcp-detail-view' ).html( response.data.detail_view ? response.data.detail_view : ewd_upcp_php_data.no_results_found_label );
			}

			ewd_upcp_adjust_sidebar_counts( response.data.filters );

			ewd_upcp_set_click_handlers();
			
			ewd_upcp_thumbnail_height

			ewd_upcp_adjust_thumbnail_heights();

			infinite_scroll_loading = false;
			
			/*adjustCatalogueHeight();
			addClickHandlers();
			addLightboxHandlers();
			addProductcomparisonClickHandlers();
			addInquiryAndCartHandlers();*/
	});
}

function ewd_upcp_get_request_string_and_set_history() {

	var categories = [];
	var subcategories = [];
	var tags = [];
	var custom_fields = [];

	var id = jQuery( 'input[name="catalog-id"]' ).val();
	var excluded_views = jQuery('input[name="catalog-excluded-views"]').val();
	var current_page = jQuery( 'input[name="catalog-current-page"]' ).val();
	var products_per_page = jQuery( 'input[name="catalog-product-per-page"]' ).val();
	var ajax_url = jQuery( 'input[name="catalog-base-url"]' ).val();

	var default_search_text = jQuery( 'input[name="catalog-default-search-text"]' ).val();

	var orderby_select_value = jQuery( 'select[name="ewd-upcp-sort-by"]' ).val();

	var orderby = orderby_select_value ? orderby_select_value.substring( 0, orderby_select_value.indexOf( '_' ) ) : '';
	var order = orderby_select_value ? orderby_select_value.substring( orderby_select_value.indexOf( '_' ) + 1 ) : '';
	
	var min_price = jQuery( '#ewd-upcp-price-range input[name="ewd-upcp-price-slider-min"]' ).val();
	var max_price = jQuery( '#ewd-upcp-price-range input[name="ewd-upcp-price-slider-max"]' ).val();

	min_price = min_price != undefined ? min_price : 0;
	max_price = max_price != undefined ? max_price : 1000000;

	jQuery( '.ewd-upcp-catalog-sidebar-category input:checked' ).each( function() { categories.push( jQuery( this ).val() ); } );
	jQuery( '.ewd-upcp-catalog-sidebar-categories select' ).each( function() { if ( jQuery( this ).val() != "all" ) { categories.push( jQuery( this ).val() ); } } );

	jQuery( '.ewd-upcp-catalog-sidebar-subcategory input:checked' ).each(function() { subcategories.push( jQuery( this ).val() ); } );
	jQuery( '.ewd-upcp-catalog-sidebar-subcategories select' ).each( function() { if ( jQuery( this ).val() != "all" ) { subcategories.push( jQuery( this ).val() ); } } );

	jQuery( '.ewd-upcp-catalog-sidebar-tag input:checked' ).each( function() { tags.push( jQuery( this ).val() ); } );
	jQuery( '.ewd-upcp-catalog-sidebar-tags select' ).each( function() { if ( jQuery( this ).val() != "all" ) { tags.push( jQuery( this ).val() ); } } );

	jQuery( '.ewd-upcp-catalog-sidebar-custom-field input:checked' ).each( function() { 

		custom_fields.push( jQuery( this ).prop( 'name' ) + '=' + jQuery( this ).val() ); 
	} );
	jQuery( '.ewd-upcp-catalog-sidebar-custom-field-div select' ).each( function() {

		if ( jQuery( this ).val() != "all" ) {

			custom_fields.push( jQuery( this ).prop( 'name' ) + '=' + jQuery( this ).val() ); 
		} 
	} );

	jQuery( '.ewd-upcp-catalog-sidebar-custom-field-slider' ).each( function() {

    	var custom_field_id = jQuery( this ).data( 'custom_field_id' );

    	var selected_min = jQuery( 'input[name="ewd-upcp-custom-field-slider-min"][data-custom_field_id="' + custom_field_id + '"]' ).val();
    	var selected_max = jQuery( 'input[name="ewd-upcp-custom-field-slider-max"][data-custom_field_id="' + custom_field_id + '"]' ).val();

    	if ( selected_min != jQuery( 'input[name="ewd-upcp-custom-field-slider-min"][data-custom_field_id="' + custom_field_id + '"]' ).data( 'custom_field_minimum' ) || selected_max != jQuery( 'input[name="ewd-upcp-custom-field-slider-max"][data-custom_field_id="' + custom_field_id + '"]' ).data( 'custom_field_maximum' ) ) {
    		
    		custom_fields.push( custom_field_id + "=" + selected_min );
    		custom_fields.push( custom_field_id + "=" + selected_max );
    	}
    });

	var product_name = jQuery('.prod-cat-sidebar').css('display') != "none" ? jQuery( 'input[name="ewd-upcp-search"]' ).val() : jQuery( 'input[name="ewd-upcp-search-mobile"]' ).val();

	if ( product_name == undefined || product_name == default_search_text ) { product_name = ''; }

	let url = new URL( window.location.href );

	if ( product_name ) {

		url.searchParams.set( 'prod_name', product_name );
	}
	else { 

		url.searchParams.delete('prod_name'); 
	}

	if ( ! ewd_upcp_php_data.price_filtering_disabled && min_price != jQuery( '#ewd-upcp-price-range input[name="ewd-upcp-price-slider-min"]' ).data( 'min_price' ) ) {

		url.searchParams.set( 'min_price', min_price );
	}
	else { 

		url.searchParams.delete('min_price'); 
	}

	if ( ! ewd_upcp_php_data.price_filtering_disabled && max_price != jQuery( '#ewd-upcp-price-range input[name="ewd-upcp-price-slider-max"]' ).data( 'max_price' ) ) {

		url.searchParams.set( 'max_price', max_price );
	}
	else { 

		url.searchParams.delete('max_price'); 
	}

	if ( categories.length ) {

		url.searchParams.set( 'categories', categories.join( ',' ) );
	}
	else { 

		url.searchParams.delete('categories'); 
	}

	if ( subcategories.length ) {

		url.searchParams.set( 'subcategories', subcategories.join( ',' ) );
	}
	else { 

		url.searchParams.delete('subcategories'); 
	}

	if ( tags.length ) {

		url.searchParams.set( 'tags', tags.join( ',' ) );
	}
	else { 

		url.searchParams.delete('tags'); 
	}

	if ( custom_fields.length ) {

		url.searchParams.set( 'custom_fields', custom_fields.join( ',' ) );
	}
	else { 

		url.searchParams.delete('custom_fields'); 
	}

	window.history.replaceState( null, null, url );

	var data = 'id=' + id + '&excluded_views=' + excluded_views + '&orderby=' + orderby + '&order=' + order + '&ajax_url=' + ajax_url + '&current_page=' + current_page + '&products_per_page=' + products_per_page + '&default_search_text=' + default_search_text + '&product_name=' + product_name + '&max_price=' + max_price + '&min_price=' + min_price + '&category=' + categories + '&subcategory=' + subcategories + '&tags=' + tags + '&custom_fields=' + encodeURIComponent( custom_fields ) + '&request_count=' + request_count + '&action=ewd_upcp_update_catalog';

	return data;
}

function ewd_upcp_adjust_sidebar_counts( filter_counts ) {

	// Pagination
	jQuery( '.ewd-upcp-pagination .product-count' ).html( filter_counts.products );

	jQuery( '.ewd-upcp-pagination .total-pages' ).html( filter_counts.max_pages );

	if ( filter_counts.max_pages <= 1 ) {

		jQuery( '.ewd-upcp-pagination' ).addClass( 'ewd-upcp-hidden' );
	}
	else {

		jQuery( '.ewd-upcp-pagination' ).removeClass( 'ewd-upcp-hidden' );
	}

	// Categories - checkbox or radio
	jQuery( '.ewd-upcp-catalog-sidebar-category' ).each( function() {

		if ( jQuery( this ).data( 'taxonomy_id' ) in filter_counts.categories ) {

			jQuery( this ).removeClass( 'ewd-upcp-hidden' );

			jQuery( this ).find( '.ewd-upcp-catalog-sidebar-taxonomy-count' ).html( '(' + filter_counts.categories[ jQuery( this ).data( 'taxonomy_id' ) ].catalog_count + ')' );
		}
		else {

			jQuery( this ).find( '.ewd-upcp-catalog-sidebar-taxonomy-count' ).html( '(0)' );

			if ( ewd_upcp_php_data.hide_empty_filtering_options ) {

				jQuery( this ).addClass( 'ewd-upcp-hidden' );
			}
		}
	});

	// Categories - dropdown
	jQuery( 'select[name="ewd-upcp-catalog-sidebar-categories-dropdown"] option' ).each( function() {

		if ( jQuery( this ).val() in filter_counts.categories ) {

			jQuery( this ).removeClass( 'ewd-upcp-hidden' );

			jQuery( this ).find( '.ewd-upcp-catalog-sidebar-taxonomy-count' ).html( '(' + filter_counts.categories[ jQuery( this ).val() ].catalog_count + ')' );
		}
		else {

			if ( jQuery( this ).val() == 'all' ) { return; }

			jQuery( this ).find( '.ewd-upcp-catalog-sidebar-taxonomy-count' ).html( '(0)' );

			if ( ewd_upcp_php_data.hide_empty_filtering_options ) {

				jQuery( this ).addClass( 'ewd-upcp-hidden' );
			}
		}
	});

	// Sub-Categories - checkbox or radio
	jQuery( '.ewd-upcp-catalog-sidebar-subcategory' ).each( function() {

		if ( jQuery( this ).data( 'taxonomy_id' ) in filter_counts.subcategories ) {

			jQuery( this ).removeClass( 'ewd-upcp-hidden' );

			jQuery( this ).find( '.ewd-upcp-catalog-sidebar-taxonomy-count' ).html( '(' + filter_counts.subcategories[ jQuery( this ).data( 'taxonomy_id' ) ].catalog_count + ')' );
		}
		else {

			jQuery( this ).find( '.ewd-upcp-catalog-sidebar-taxonomy-count' ).html( '(0)' );

			if ( ewd_upcp_php_data.hide_empty_filtering_options ) {

				jQuery( this ).addClass( 'ewd-upcp-hidden' );
			}
		}
	});

	// Sub-Categories - dropdown
	jQuery( 'select[name="ewd-upcp-catalog-sidebar-subcategories-dropdown"] option' ).each( function() {

		if ( jQuery( this ).val() in filter_counts.subcategories ) {

			jQuery( this ).removeClass( 'ewd-upcp-hidden' );

			jQuery( this ).find( '.ewd-upcp-catalog-sidebar-taxonomy-count' ).html( '(' + filter_counts.subcategories[ jQuery( this ).val() ].catalog_count + ')' );
		}
		else {

			if ( jQuery( this ).val() == 'all' ) { return; }

			jQuery( this ).find( '.ewd-upcp-catalog-sidebar-taxonomy-count' ).html( '(0)' );

			if ( ewd_upcp_php_data.hide_empty_filtering_options ) {

				jQuery( this ).addClass( 'ewd-upcp-hidden' );
			}
		}
	});

	// Tags - checkbox or radio
	jQuery( '.ewd-upcp-catalog-sidebar-tag' ).each( function() {

		if ( jQuery( this ).data( 'taxonomy_id' ) in filter_counts.tags ) {

			jQuery( this ).removeClass( 'ewd-upcp-hidden' );

			jQuery( this ).find( '.ewd-upcp-catalog-sidebar-taxonomy-count' ).html( '(' + filter_counts.tags[ jQuery( this ).data( 'taxonomy_id' ) ].catalog_count + ')' );
		}
		else {

			jQuery( this ).find( '.ewd-upcp-catalog-sidebar-taxonomy-count' ).html( '(0)' );

			if ( ewd_upcp_php_data.hide_empty_filtering_options ) {

				jQuery( this ).addClass( 'ewd-upcp-hidden' );
			}
		}
	});

	// Tags - dropdown
	jQuery( 'select[name="ewd-upcp-catalog-sidebar-tags-dropdown"] option' ).each( function() {

		if ( jQuery( this ).val() in filter_counts.tags ) {

			jQuery( this ).removeClass( 'ewd-upcp-hidden' );

			jQuery( this ).find( '.ewd-upcp-catalog-sidebar-taxonomy-count' ).html( '(' + filter_counts.tags[ jQuery( this ).val() ].catalog_count + ')' );
		}
		else {

			if ( jQuery( this ).val() == 'all' ) { return; }

			jQuery( this ).find( '.ewd-upcp-catalog-sidebar-taxonomy-count' ).html( '(0)' );

			if ( ewd_upcp_php_data.hide_empty_filtering_options ) {

				jQuery( this ).addClass( 'ewd-upcp-hidden' );
			}
		}
	});

	// Custom Fields - checkbox or radio
	jQuery( '.ewd-upcp-catalog-sidebar-custom-field' ).each( function() {

		if ( jQuery( this ).data( 'custom_field_id' ) in filter_counts.custom_fields && jQuery( this ).data( 'value' ) in filter_counts.custom_fields[ jQuery( this ).data( 'custom_field_id' ) ] ) {

			jQuery( this ).removeClass( 'ewd-upcp-hidden' );

			jQuery( this ).find( '.ewd-upcp-catalog-sidebar-custom-field-count' ).html( '(' + filter_counts.custom_fields[ jQuery( this ).data( 'custom_field_id' ) ][ jQuery( this ).data( 'value' ) ] + ')' );
		}
		else {

			jQuery( this ).find( '.ewd-upcp-catalog-sidebar-custom-field-count' ).html( '(0)' );

			if ( ewd_upcp_php_data.hide_empty_filtering_options ) {

				jQuery( this ).addClass( 'ewd-upcp-hidden' );
			}
		}
	});

	// Custom Fields - dropdown
	jQuery( 'select[name="ewd-upcp-catalog-custom-field-dropdown"] option' ).each( function() {

		if ( jQuery( this ).parent().data( 'custom_field_id' ) in filter_counts.custom_fields && jQuery( this ).val() in filter_counts.custom_fields[ jQuery( this ).parent().data( 'custom_field_id' ) ] ) {

			jQuery( this ).removeClass( 'ewd-upcp-hidden' );

			jQuery( this ).find( '.ewd-upcp-catalog-sidebar-custom-field-count' ).html( '(' + filter_counts.custom_fields[ jQuery( this ).parent().data( 'custom_field_id' ) ][ jQuery( this ).val() ] + ')' );
		}
		else {

			if ( jQuery( this ).val() == 'all' ) { return; }

			jQuery( this ).find( '.ewd-upcp-catalog-sidebar-custom-field-count' ).html( '(0)' );

			if ( ewd_upcp_php_data.hide_empty_filtering_options ) {

				jQuery( this ).addClass( 'ewd-upcp-hidden' );
			}
		}
	});
}