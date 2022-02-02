jQuery(document).ready(function(jQuery) {

	var themes;

	setTimeout(function process_if_page_already_in_open_wptc() {
		wptc_theme_rollback();
	}, 2000);

	if(typeof wp != 'undefined'){
		themes = wp.themes = (wp.themes) || {};
	} else {
		themes = wp.themes = {};
	}

	themes.data = typeof _wpThemeSettings !== 'undefined' ? _wpThemeSettings : '';

	jQuery.fn.contentChange = function( callback ) {
		var elms = jQuery( this );
		elms.each(
			function( i ) {
				var elm = jQuery( this );
				elm.data( 'lastContents', elm.html() );
				window.watchContentChange = window.watchContentChange ? window.watchContentChange : [];
				window.watchContentChange.push( { 'element': elm, 'callback': callback } );
			}
		);
		return elms;
	};
	setInterval( function() {
		if ( window.watchContentChange ) {
			for ( i in window.watchContentChange ) {
				if ( window.watchContentChange[ i ].element.data( 'lastContents' ) != window.watchContentChange[ i ].element.html() ) {
					window.watchContentChange[ i ].callback.apply( window.watchContentChange[ i ].element );
					window.watchContentChange[ i ].element.data( 'lastContents', window.watchContentChange[ i ].element.html() );
				}

			}
		}
	}, 150 );

	// On clicking a theme template
	jQuery( '.theme-overlay' ).contentChange( function( e ) {

		// pass off to rollback function
		wptc_theme_rollback();

	} );

	jQuery('body').on('click', '.wptc-rollback', function(e){
		prevent_action_propagation_wptc(e);

		swal({
			title              : wptc_get_dialog_header('Processing...'),
			html               : wptc_get_dialog_body('Collecting information about previous installed versions...'),
			padding            : '0px 0px 10px 0',
			showConfirmButton  : false,
			showCancelButton   : false,
			onOpen: () => {
		    	swal.showLoading();
	   			wptc_get_rollback_version(this);
			},
		});
	});


	/**
	 * Check to see if Rollback button is in place
	 *
	 * @returns {boolean}
	 */
	function wptc_is_rollback_btn_there() {

		if ( jQuery( '.wptc-rollback' ).length > 0 ) {
			return true;
		}
		return false;

	}

	/**
	 * Is Theme WordPress.org?
	 *
	 * @description Rollback only supports WordPress.org themes
	 */
	function wptc_theme_rollback() {

		// get theme name that was clicked
		var theme = wptc_get_parameter_by_name( 'theme' );

		// check that rollback button hasn't been placed
		if ( wptc_is_rollback_btn_there() ) {
			// button is there, bail
			return false;
		}

		var theme_data = wptc_get_theme_data( theme );

		if (!theme_data) {
			return ;
		}

		var active_theme = jQuery( '.theme-overlay' ).hasClass( 'active' );

		var rollback_btn_html = '<a type="theme" slug="' + theme + '" current_version="' + theme_data.version + '" name="' + theme_data.name + '"  class="button wptc-rollback">WPTC Rollback</a>';

		if (jQuery('.active-theme').is(':visible')) {
			jQuery( '.active-theme' ).append( rollback_btn_html );
		} else{
			jQuery( '.inactive-theme' ).append( rollback_btn_html );
		}
	}

	/**
	 * Get Theme Data
	 *
	 * @description Loops through the wp.themes.data.themes object, finds a match, and returns the data
	 * @param theme
	 * @returns {*}
	 */
	function wptc_get_theme_data( theme ) {

		if(typeof wp == 'undefined'){

			return;
		}

		var theme_data = wp.themes.data.themes;

		if (!theme_data) {
			return ;
		}

		// Loop through complete theme data to find this current theme's data
		for ( var i = 0, len = theme_data.length; i < len; i ++ ) {
			if ( theme_data[ i ].id === theme ) {
				return theme_data[ i ]; // Return as soon as the object is found
			}
		}
		return null; // The object was not found
	}

	function wptc_get_parameter_by_name( name ) {
		name = name.replace( /[\[]/, '\\[' ).replace( /[\]]/, '\\]' );
		var regex = new RegExp( '[\\?&]' + name + '=([^&#]*)' ),
			results = regex.exec( location.search );
		return results === null ? '' : decodeURIComponent( results[ 1 ].replace( /\+/g, ' ' ) );
	}

});

function wptc_get_rollback_version(that){
	var name = jQuery(that).attr('name');
	var version = jQuery(that).attr('current_version');
	var slug = jQuery(that).attr('slug');
	var type = jQuery(that).attr('type');

	jQuery.post(ajaxurl, {
		security: wptc_ajax_object.ajax_nonce,
		action: 'get_previous_versions_wptc',
		dataType: "json",
		data: {type:type, name: name, version: version, slug:slug}
	}, function(response) {
		if (!response) {
			return swal({
					title              : wptc_get_dialog_header('Failed!'),
					html               : wptc_get_dialog_body('Could not fetch information.' , 'error'),
					padding            : '0px 0px 10px 0',
					buttonsStyling     : false,
					showCancelButton   : false,
					confirmButtonColor : '',
					confirmButtonClass : 'button-primary wtpc-button-primary',
					confirmButtonText  : 'Ok',
				});
		}
		try{
			response = jQuery.parseJSON(response);
		} catch(err){
			return swal({
					title              : wptc_get_dialog_header('Failed!'),
					html               : wptc_get_dialog_body('Could not fetch information.' , 'error'),
					padding            : '0px 0px 10px 0',
					buttonsStyling     : false,
					showCancelButton   : false,
					confirmButtonColor : '',
					confirmButtonClass : 'button-primary wtpc-button-primary',
					confirmButtonText  : 'Ok',
				});
		}

		if (response.status === 'error') {
			return swal({
					title              : wptc_get_dialog_header('Error!'),
					html               : wptc_get_dialog_body('There are no backup and update points found for ' + name + '. <br> Rollback is possible only when '+name+' plugin was updated with WPTC\'s <strong> Backup and Update feature </strong> or <strong> Automatic Updates </strong> feature.', 'error'),
					padding            : '0px 0px 10px 0',
					buttonsStyling     : false,
					showCancelButton   : false,
					confirmButtonColor : '',
					confirmButtonClass : 'button-primary wtpc-button-primary',
					confirmButtonText  : 'Ok',
				});
		}

		wptc_do_rollback(response.data, type, version);
	});
}

function wptc_do_rollback(response, type, version){

	swal({
		title              : wptc_get_dialog_header('Are you sure?'),
		html               : wptc_get_dialog_body('Clicking on Yes will restore <strong>previously installed version of ' + response.update_details.name + '</strong>. This will restore only the files of this ' + type + ' and no database will be restored. <a style="color:#0073aa" href="https://docs.wptimecapsule.com/article/49-roll-back" target="_blank">Read More</a><br><br> Are you sure want to continue ?', ''),
		padding            : '0px 0px 10px 0',
		buttonsStyling     : false,
		showCancelButton   : true,
		confirmButtonColor : '',
		cancelButtonColor  : '',
		confirmButtonClass : 'button-primary wtpc-button-primary',
		cancelButtonClass  : 'button-secondary wtpc-button-secondary',
		confirmButtonText  : 'Yes',
		cancelButtonText   : 'Cancel',
		}).then(function () {
			swal({
				title              : wptc_get_dialog_header('Processing!'),
				html               : wptc_get_dialog_body('Starting Restore...' , ''),
				padding            : '0px 0px 10px 0',
				buttonsStyling     : false,
				showConfirmButton  : false,
			});
			start_restore_wptc({files:{}, folders:[{file: response.update_details.path, backup_id: response.backup_id}]}, false, response.backup_id, true, false);
		}, function (dismiss) {

		}
	);
}
