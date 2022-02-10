var filtering_running = false;
var wpforms_search_running = 'No';

jQuery(function(){ //DOM Ready
    ufaqSetClickHandlers();
    UFAQSetAutoCompleteClickHandlers();
    UFAQSetRatingHandlers();
    UFAQSetExpandCollapseHandlers();
    UFAQSetPaginationHandlers();

    UFAQWPFormsHandler();
});

function ewd_ufaq_run_effect( display, faq_element ) {

    var selected_effect = typeof ewd_ufaq_php_data != 'undefined' ? ewd_ufaq_php_data.reveal_effect : 'none';

    // most effect types need no options passed by default
    var options = {};

    // some effects have required parameters
    if ( selected_effect === 'size' ) {

      options = { to: { width: 200, height: 60 } };
    }

    // run the effect
    if ( display == 'show' ) { faq_element.find( '.ewd-ufaq-faq-body' ).show( selected_effect, options, 500, ewd_ufaq_toggle_hidden_class( faq_element ) ); }
	if ( display == 'hide' ) { faq_element.find( '.ewd-ufaq-faq-body' ).hide( selected_effect, options, 500, ewd_ufaq_toggle_hidden_class( faq_element ) ); }
};

// callback function to bring a hidden box back
function ewd_ufaq_toggle_hidden_class( faq_element ) {

	setTimeout( function() { faq_element.find( '.ewd-ufaq-faq-body' ).toggleClass( 'ewd-ufaq-hidden' ); }, 500 );
};

function ufaqSetClickHandlers() {

	jQuery( '.ewd-ufaq-faq-toggle' ).off( 'click' ).on( 'click', function( event ) {
		
		jQuery( this ).attr( 'aria-expanded', function ( i, attr ) {
			return attr == 'true' ? 'false' : 'true'
		});

		event.preventDefault();
		
		faq = jQuery( this ).parent();
		
		if ( faq.find( '.ewd-ufaq-faq-body' ).hasClass( 'ewd-ufaq-hidden' ) ) {

			EWD_UFAQ_Reveal_FAQ( faq );

			if ( typeof ewd_ufaq_php_data != 'undefined' && ewd_ufaq_php_data.faq_scroll ) { jQuery('html, body').animate({scrollTop: jQuery(this).offset().top -80}, 100); }
		}
		else {

			EWD_UFAQ_Hide_FAQ( faq );
		}
	});

	jQuery( '.ewd-ufaq-faq-category-title-toggle' ).off( 'click' ).on( 'click', function() {
		
		var category = jQuery( this ).parent();
		var category_inner = category.find( '.ewd-ufaq-faq-category-inner' );
		
		category_inner.toggleClass( 'ewd-ufaq-faq-category-body-hidden' );

		if ( typeof ewd_ufaq_php_data == 'undefined' || ! ewd_ufaq_php_data.category_accordion ) { return; }

		jQuery( '.ewd-ufaq-faq-category-inner' ).each( function( index, object ) {
			
			if ( object != category_inner.get( 0 ) ) { jQuery( this ).addClass( 'ewd-ufaq-faq-category-body-hidden' ); }
		});
	});

	jQuery( '.ewd-ufaq-back-to-top-link' ).off( 'click' ).on( 'click', function( event ) {
		event.preventDefault();

		jQuery( 'html, body' ).animate( { scrollTop: jQuery( '#ewd-ufaq-faq-list' ).offset().top -80 }, 100 );
	});

	jQuery( '.ewd-ufaq-faq-header-link' ).off( 'click' ).on( 'click', function( event ) {
		event.preventDefault();

		var faq_id = jQuery( this ).data( 'postid' );
		var faq = jQuery( '#ewd-ufaq-post-' + faq_id ).first();

		if ( faq.find( '.ewd-ufaq-faq-body' ).hasClass( 'ewd-ufaq-hidden' ) ) {
			
			EWD_UFAQ_Reveal_FAQ( faq );
		}

		jQuery( 'html, body' ).animate( { scrollTop: faq.offset().top - 20 }, 100 );
	});

	jQuery( '.ewd-ufaq-text-input ' ).on( 'search', function() {

		ewd_ufaq_ajax_reload();
	});
}

function UFAQSetAutoCompleteClickHandlers() {

	jQuery( '.ewd-ufaq-text-auto-complete' ).on( 'keyup', function() {

		jQuery( '.ewd-ufaq-text-auto-complete' ).autocomplete({

			source: ewd_ufaq_php_data.question_titles,
			minLength: 3,
			appendTo: '#ewd-ufaq-jquery-ajax-search',
			select: function( event, ui ) {
				jQuery( this ).val( ui.item.value );
        		ewd_ufaq_ajax_reload();
			}
		});

		jQuery( '.ewd-ufaq-text-auto-complete' ).autocomplete( 'enable' );
	}); 
}

function EWD_UFAQ_Reveal_FAQ( faq_element ) {

	var post_id = faq_element.data( 'post_id' );

	var data = 'post_id=' + post_id + '&action=ewd_ufaq_record_view';
    jQuery.post(ajaxurl, data, function(response) {});

    faq_element.find( '.ewd-ufaq-post-margin-symbol' ).html( faq_element.find( '.ewd-ufaq-post-margin-symbol' ).html().toUpperCase() );

	faq_element.find( '.ewd-ufaq-faq-excerpt' ).addClass( 'ewd-ufaq-hidden' );
	
	if ( typeof ewd_ufaq_php_data != 'undefined' && ewd_ufaq_php_data.reveal_effect != 'none' ) { ewd_ufaq_run_effect( 'show', faq_element ); }
	else { faq_element.find( '.ewd-ufaq-faq-body' ).removeClass( 'ewd-ufaq-hidden' ); }
			
	if ( typeof ewd_ufaq_php_data != 'undefined' && ewd_ufaq_php_data.faq_accordion ) { 

		jQuery( '.ewd-ufaq-faq-div' ).each( function() {

			if ( jQuery( this ).prop( 'id' ) != faq_element.prop( 'id' ) ) {

		  		EWD_UFAQ_Hide_FAQ( jQuery(this) );
			} 
			else {

				jQuery( this ).addClass( 'ewd-ufaq-post-active' );
			}
		});
	}
	else {

		faq_element.addClass( 'ewd-ufaq-post-active' );
	}
}

function EWD_UFAQ_Hide_FAQ( faq_element ) {

	var post_id = faq_element.data( 'post_id' );

	faq_element.find( '.ewd-ufaq-faq-excerpt' ).removeClass( 'ewd-ufaq-hidden' );

	if ( typeof ewd_ufaq_php_data != 'undefined' && ewd_ufaq_php_data.reveal_effect != 'none' && ! faq_element.find( '.ewd-ufaq-faq-body' ).hasClass( 'ewd-ufaq-hidden' ) ) { ewd_ufaq_run_effect( 'hide', faq_element ); }
	else { faq_element.find( '.ewd-ufaq-faq-body' ).addClass( 'ewd-ufaq-hidden' ); }

	faq_element.removeClass( 'ewd-ufaq-post-active' );
	faq_element.find( '.ewd-ufaq-post-margin-symbol' ).html( faq_element.find( '.ewd-ufaq-post-margin-symbol' ).html().toLowerCase() );
}

jQuery(document).ready(function() {

    jQuery( '#ewd-ufaq-ajax-search-submit' ).click( function() {

    	jQuery( '.ewd-ufaq-bottom' ).data( 'current_page', 1 );

		ewd_ufaq_ajax_reload();
    });

	jQuery( '#ewd-ufaq-ajax-form' ).submit( function( event ) {

		event.preventDefault();

		ewd_ufaq_ajax_reload();
	});

	jQuery( '#ewd-ufaq-jquery-ajax-search .ewd-ufaq-text-input' ).keyup( function() {
		
		ewd_ufaq_ajax_reload();
	});

	if ( jQuery( '#ewd-ufaq-ajax-text-input' ).length ) {

		if ( jQuery( '#ewd-ufaq-ajax-text-input' ).val() != '' ) { ewd_ufaq_ajax_reload(); }
	}

	if ( typeof ewd_ufaq_php_data != 'undefined' && ewd_ufaq_php_data.display_faq > 0 ) {
		
		var faq = jQuery( '.ewd-ufaq-faq-div[data-post_id="' + ewd_ufaq_php_data.display_faq + '"]' );

		faq.parent().removeClass( 'ewd-ufaq-hidden' );

		EWD_UFAQ_Reveal_FAQ( faq );

		jQuery('html, body').animate({scrollTop: faq.offset().top -80}, 100);
	}
});

var request_count = 0;
function ewd_ufaq_ajax_reload( pagination, append_results, search_string ) {

	filtering_running = true;

    var search_string = search_string ? search_string : jQuery( '.ewd-ufaq-text-input' ).val();
    var include_cat = jQuery( '#ewd-ufaq-include-category' ).val();
    var exclude_cat = jQuery( '#ewd-ufaq-exclude-category' ).val();
    var orderby = jQuery( '#ewd-ufaq-orderby' ).val();
    var order = jQuery( '#ewd-ufaq-order' ).val();
    var post_count = jQuery( '#ewd-ufaq-post-count' ).val();
    var current_url = jQuery( '#ewd-ufaq-current-url' ).val();
    var show_on_load = jQuery( '#ewd-ufaq-show-on-load' ).val();

    if ( search_string == undefined ) { search_string = ''; }

    if ( pagination == 'Yes' ) {

    	var faqs_only = 'Yes';
    	var faq_page = jQuery( '.ewd-ufaq-bottom' ).data( 'current_page' );
    }
    else {

    	var retrieving_results = typeof ewd_ufaq_php_data != 'undefined' ? ewd_ufaq_php_data.retrieving_results : 'Retrieving Results';

    	jQuery( '.ewd-ufaq-faqs' ).html( '<h3>' + ewd_ufaq_php_data.retrieving_results + '</h3>' );

    	var faqs_only = 'No';
    	var faq_page = 0;
    }

    request_count = request_count + 1;

    if (show_on_load == 'No' && Question.length == 0) {jQuery('#ewd-ufaq-ajax-results').html(''); return;} 

    var data = 'search_string=' + search_string + '&include_category=' + include_cat + '&exclude_category=' + exclude_cat + '&orderby=' + orderby + '&order=' + order + '&post_count=' + post_count + '&request_count=' + request_count + '&current_url=' + current_url + '&faqs_only=' + faqs_only + '&faq_page=' + faq_page + '&action=ewd_ufaq_search';
    jQuery.post( ajaxurl, data, function( response ) {
		
		if ( response.data.request_count == request_count ) {

			if ( append_results == 'Yes' ) { jQuery( '.ewd-ufaq-faqs' ).append( response.data.output ); }
			else { jQuery( '.ewd-ufaq-faqs' ).html( response.data.output ); }

			jQuery( '.ewd-ufaq-bottom' ).data( 'max_page', response.data.max_page );

			jQuery( '.ewd-ufaq-expand-all' ).removeClass( 'ewd-ufaq-hidden' );
			jQuery( '.ewd-ufaq-collapse-all' ).addClass( 'ewd-ufaq-hidden' );

       		ufaqSetClickHandlers();
       		UFAQSetRatingHandlers();
       		UFAQSetPaginationHandlers();
       		UFAQUpdatePaginationButtons();
       		UFAQSetExpandCollapseHandlers();

       		filtering_running = false;
       	}
    });
}

function UFAQSetRatingHandlers() {

	jQuery( '.ewd-ufaq-rating-button' ).off( 'click' ).on( 'click', function() {
		
		var faq_id = jQuery( this ).data( 'faq_id' );
		jQuery( '*[data-faq_id="' + faq_id + '"]' ).off( 'click' );

		var current_count = jQuery( this ).find( 'span' ).html();
		current_count++;
		jQuery( this ).find( 'span' ).html( current_count );

		if ( jQuery( this ).hasClass( 'ewd-ufaq-up-vote' ) ) { vote_type = 'up'; }
		else { vote_type = 'down'; }

		var data = 'faq_id=' + faq_id + '&vote_type=' + vote_type + '&action=ewd_ufaq_update_rating';
    	
    	jQuery.post( ajaxurl, data );
	});
}

function UFAQSetExpandCollapseHandlers() {

	jQuery('.ewd-ufaq-expand-all').off('click').on('click', function() {
		
		var accordion_setting = ewd_ufaq_php_data.faq_accordion;
		ewd_ufaq_php_data.faq_accordion = false; // turn FAQ accordion off while expanding all

		jQuery( '.ewd-ufaq-faq-toggle' ).each( function() {

			var faq = jQuery(this).parent();

			jQuery(this).attr('aria-expanded', 'true');

			EWD_UFAQ_Reveal_FAQ( faq );
		});

		ewd_ufaq_php_data.faq_accordion = accordion_setting; //reset FAQ accordion setting

		jQuery('.ewd-ufaq-faq-category-inner').removeClass('ewd-ufaq-faq-category-body-hidden');
		jQuery('.ewd-ufaq-collapse-all').removeClass('ewd-ufaq-hidden');
		jQuery('.ewd-ufaq-expand-all').addClass('ewd-ufaq-hidden');
	});

	jQuery('.ewd-ufaq-collapse-all').off('click').on('click', function() {

		jQuery('.ewd-ufaq-faq-toggle').each(function() {

			var faq = jQuery(this).parent();

			jQuery(this).attr('aria-expanded', 'false');

			EWD_UFAQ_Hide_FAQ( faq );
		});

		if ( jQuery( '.ewd-ufaq-faq-category-title-toggle' ).length ) { jQuery( '.ewd-ufaq-faq-category-inner' ).addClass( 'ewd-ufaq-faq-category-body-hidden' );}

		jQuery('.ewd-ufaq-expand-all').removeClass('ewd-ufaq-hidden');
		jQuery('.ewd-ufaq-collapse-all').addClass('ewd-ufaq-hidden');
	});
}

function UFAQSetPaginationHandlers() {

	jQuery('.ewd-ufaq-previous-faqs').off( 'click' ).on('click', function() {

		var current_page = jQuery( '.ewd-ufaq-bottom' ).data( 'current_page' );

		jQuery( '.ewd-ufaq-bottom' ).data( 'current_page', Math.max( current_page - 1, 0 ) );
		jQuery( '.ewd-ufaq-max-faqs-not-reached' ).remove();

		ewd_ufaq_ajax_reload( 'Yes', 'No' );
	});

	jQuery('.ewd-ufaq-next-faqs').off( 'click' ).on('click', function() {

		var current_page = jQuery( '.ewd-ufaq-bottom' ).data( 'current_page' );

		jQuery( '.ewd-ufaq-bottom' ).data( 'current_page', current_page + 1 );
		jQuery( '.ewd-ufaq-max-faqs-not-reached' ).remove();

		ewd_ufaq_ajax_reload( 'Yes', 'No' );
	});

	jQuery( '.ewd-ufaq-load-more' ).off( 'click' ).on( 'click', function() {

		var current_page = jQuery( '.ewd-ufaq-bottom' ).data( 'current_page' );

		jQuery( '.ewd-ufaq-bottom' ).data( 'current_page', current_page + 1 );
		jQuery( '.ewd-ufaq-max-faqs-not-reached' ).remove();

		ewd_ufaq_ajax_reload( 'Yes', 'Yes' );
	});

	if ( jQuery( '.ewd-ufaq-pagination-infinite_scroll' ).length ) {

		jQuery( window ).scroll( function() {

			var infinite_pos = jQuery( '.ewd-ufaq-pagination-infinite_scroll').position();

			if ( infinite_pos != undefined && jQuery( '.ewd-ufaq-bottom' ).data( 'current_page' ) != jQuery( '.ewd-ufaq-bottom' ).data( 'max_page' ) ) {

				if  ( ( jQuery( window ).height() + jQuery( window ).scrollTop() > infinite_pos.top ) && filtering_running == false ) {

					jQuery( '.ewd-ufaq-bottom' ).data( 'current_page', jQuery( '.ewd-ufaq-bottom' ).data( 'current_page' ) + 1 );

					ewd_ufaq_ajax_reload( 'Yes', 'Yes' );
				}
			}
		});
	}
}

function UFAQUpdatePaginationButtons() {

	jQuery('.ewd-ufaq-bottom').first().appendTo('.ewd-ufaq-faq-list');

	if ( jQuery( '.ewd-ufaq-bottom' ).data( 'current_page' ) < jQuery( '.ewd-ufaq-bottom' ).data( 'max_page' ) ) {

		jQuery( '.ewd-ufaq-load-more, .ewd-ufaq-next-faqs' ).removeClass( 'ewd-ufaq-hidden' );
	}
	else { jQuery( '.ewd-ufaq-load-more, .ewd-ufaq-next-faqs' ).addClass( 'ewd-ufaq-hidden' ); }

	if ( jQuery( '.ewd-ufaq-bottom' ).data( 'current_page' ) <= 1 ) {

		jQuery( '.ewd-ufaq-previous-faqs' ).addClass( 'ewd-ufaq-hidden' );
	}
	else { jQuery( '.ewd-ufaq-previous-faqs' ).removeClass( 'ewd-ufaq-hidden' ); }
}

function UFAQWPFormsHandler() {
	
	if ( typeof wpforms_integration === 'undefined' || wpforms_integration === null ) { return; }

	if ( wpforms_integration.ufaq_enabled == 'disabled' ) { return; }

	var target_field = wpforms_integration.ufaq_selected_field;

	jQuery( '#wpforms-' + wpforms_integration.form_id + '-field_' + wpforms_integration.ufaq_selected_field ).on( 'keyup', function() {

		var search_string = jQuery( this ).val();

		if ( search_string.length > 12 ) {

			jQuery( '.ewd-ufaq-wpforms-faq-results' ).removeClass( 'ewd-ufaq-hidden' );
			jQuery( '.ewd-ufaq-wpforms-label' ).removeClass( 'ewd-ufaq-hidden' );

			ewd_ufaq_ajax_reload( 'No', 'No', search_string );
		}
		else if ( ! filtering_running ) {

			jQuery( '.ewd-ufaq-faqs' ).html( ' ' );
			jQuery( '.ewd-ufaq-wpforms-faq-results' ).addClass( 'ewd-ufaq-hidden' );
			jQuery( '.ewd-ufaq-wpforms-label' ).addClass( 'ewd-ufaq-hidden' );
		}

	});
}

/*jQuery(document).ready(function() {
  jQuery('a[href*=#]:not([href=#])').click(function() {
  	var post_id = jQuery(this).attr("data-postid"); 
    var selectedIDString = 'ewd-ufaq-body-'+post_id;
    
    if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
      var target = jQuery(this.hash);
      target = target.length ? target : jQuery('[name=' + this.hash.slice(1) +']');
      if (target.length) {

    jQuery('html,body').on("scroll mousedown wheel DOMMouseScroll mousewheel keyup touchmove", function(){
       jQuery('html,body').stop();
    });
		
		if (jQuery('#'+selectedIDString).hasClass("ewd-ufaq-hidden")) {
			EWD_UFAQ_Reveal_FAQ(post_id, selectedIDString);
		}

        jQuery('html,body').animate({
          scrollTop: target.offset().top
        }, 1000);
        //return false;
      }
    }
  });
});*/
