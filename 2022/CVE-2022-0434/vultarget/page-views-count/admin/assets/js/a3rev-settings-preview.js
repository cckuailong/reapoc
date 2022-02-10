/**
 * A3revThemes Settings Live Preview
 * 2011-10-07.
 *
 * @description The code below is designed to generate a live preview using the
 * setting specified in a "custom settings" field in the ResponsiFramework.
 *
 * @since 4.7.0
 */

(function ($) {

  a3revButtonPreview = {
  
  	/**
  	 * loadPreviewButtons()
  	 *
  	 * @description Setup a "preview" button next to each type field.
  	 * @since 4.7.0
  	 */
  
    loadPreviewButtons: function () {
     
     // Register event handlers.
     a3revButtonPreview.handleEvents();
      
    }, // End loadPreviewButtons()
    
    /**
     * handleEvents()
     *
     * @description Handle the events.
     * @since 4.7.0
     */
    
    handleEvents: function () {
    	$(document).on( 'click', 'a.a3rev-ui-border-preview-button', function () {
    		a3revButtonPreview.generateBorderPreview( $( this ) );
    		return false;
    	});
		
		$(document).on( 'click', 'a.a3rev-ui-box_shadow-preview-button', function () {
    		a3revButtonPreview.generateBoxShadowPreview( $( this ) );
    		return false;
    	});
    	
    	$(document).on( 'click', 'a.preview_remove', function () {
    		a3revButtonPreview.closePreview( $( this ) );
    		return false;
    	});
    }, 
    
    /**
     * closePreview()
     *
     * @description Close the preview.
     * @since 4.7.0
     */
     
     closePreview: function ( target ) {
		target.parents( '.section' ).find( '.a3rev-ui-settings-preview-button .refresh' ).removeClass( 'refresh' );
     	target.parents( '.settings-preview-container' ).remove();
     }, 
    
	
    /**
     * generateBorderPreview()
     *
     * @description Generate the border preview.
     * @since 4.7.0
     */
     
    generateBorderPreview: function ( target ) {
    	var previewText = '<div class="settings-apply-preview">Grumpy wizards make toxic brew for the evil Queen and Jack.</div>';
    	var previewHTML = '';
    	var previewStyles = '';
    	
    	// Get the control parent element.
    	var controls = target.parents( '.a3rev-ui-settings-control' );
    	    	
    	var borderSize = controls.find( '.a3rev-ui-border_styles-width' ).val();
    	var borderStyle = controls.find( '.a3rev-ui-border_styles-style' ).val();
    	var borderColor = controls.find( '.a3rev-ui-border_styles-color' ).val();
		var borderRoundedCorner = 0;
		if ( controls.find( '.a3rev-ui-border-corner' ).is(":checked") ) {
			borderRoundedCorner = 1;
		}
		var borderTopLeft = controls.find( '.a3rev-ui-border_top_left_corner' ).val();
		var borderTopRight = controls.find( '.a3rev-ui-border_top_right_corner' ).val();
		var borderBottomLeft = controls.find( '.a3rev-ui-border_bottom_left_corner' ).val();
		var borderBottomRight = controls.find( '.a3rev-ui-border_bottom_right_corner' ).val();
				
		// Remove "current" class from previously modified border field.
    	$( '.settings-preview' ).removeClass( 'current' );
		
    	// Construct styles.
    	previewStyles += 'border: ' + borderSize + ' ' + borderStyle + ' ' + borderColor + ';';
    	if ( borderRoundedCorner == 1 ) { 
			previewStyles += ' border-radius: ' + borderTopLeft + 'px ' + borderTopRight + 'px ' + borderBottomRight + 'px ' + borderBottomLeft + 'px ;';
			previewStyles += ' -webkit-border-radius: ' + borderTopLeft + 'px ' + borderTopRight + 'px ' + borderBottomRight + 'px ' + borderBottomLeft + 'px ;';
			previewStyles += ' -moz-border-radius: ' + borderTopLeft + 'px ' + borderTopRight + 'px ' + borderBottomRight + 'px ' + borderBottomLeft + 'px ;';
		}
    	
    	// Construct preview HTML.
    	var previewHTMLInner = $( '<div />' ).addClass( 'current' ).addClass( 'settings-preview' ).html( previewText );
    	
    	previewHTML = $( '<div />' ).addClass( 'settings-preview-container' ).html( previewHTMLInner ).append( '<a href="#" class="preview_remove a3-plugin-ui-delete-icon">&nbsp;</a>' );
    	
    	// If no preview display is present, add one.
    	if ( ! controls.next( '.settings-preview-container' ).length ) {
    		previewHTML.find( '.settings-apply-preview' ).attr( 'style', previewStyles );
    		controls.after( previewHTML );
    	} else {
    	// Otherwise, just update the styles of the existing preview.
    		controls.next( '.settings-preview-container' ).find( '.settings-apply-preview' ).attr( 'style', previewStyles );
    	}
    	
    	// Set the button to "refresh" mode.
    	controls.find( '.a3rev-ui-settings-preview-button span' ).addClass( 'refresh' );
    },
	
	/**
     * generateBoxShadowPreview()
     *
     * @description Generate the border preview.
     * @since 4.7.0
     */
     
    generateBoxShadowPreview: function ( target ) {
    	var previewText = '<div class="settings-apply-preview">Grumpy wizards make toxic brew for the evil Queen and Jack.</div>';
    	var previewHTML = '';
    	var previewStyles = '';
		var customStyles = '';
    	
    	// Get the control parent element.
    	var controls = target.parents( '.a3rev-ui-settings-control' );
    	    	
    	var hShadow = controls.find( '.a3rev-ui-box_shadow-h_shadow' ).val();
    	var vShadow = controls.find( '.a3rev-ui-box_shadow-v_shadow' ).val();
		var blurSize = controls.find( '.a3rev-ui-box_shadow-blur' ).val();
		var spreadSize = controls.find( '.a3rev-ui-box_shadow-spread' ).val();
    	var shadowColor = controls.find( '.a3rev-ui-box_shadow-color' ).val();
		var insetShadow = '';
		if ( controls.find( '.a3rev-ui-box_shadow-inset' ).is(":checked") ) {
			insetShadow = 'inset';
		}
				
		// Remove "current" class from previously modified border field.
    	$( '.settings-preview' ).removeClass( 'current' );
		
    	// Construct styles.
		customStyles += hShadow + ' ' + vShadow + ' ' + blurSize  + ' ' + spreadSize  + ' ' + shadowColor  + ' ' + insetShadow ;
    	previewStyles += ' box-shadow: ' + customStyles + ';';
		previewStyles += ' -moz-box-shadow: ' + customStyles + ';';
		previewStyles += ' -webkit-box-shadow: ' + customStyles + ';';
    	
    	// Construct preview HTML.
    	var previewHTMLInner = $( '<div />' ).addClass( 'current' ).addClass( 'settings-preview' ).html( previewText );
    	
    	previewHTML = $( '<div />' ).addClass( 'settings-preview-container' ).html( previewHTMLInner ).append( '<a href="#" class="preview_remove a3-plugin-ui-delete-icon">&nbsp;</a>' );
    	
    	// If no preview display is present, add one.
    	if ( ! controls.next( '.settings-preview-container' ).length ) {
    		previewHTML.find( '.settings-apply-preview' ).attr( 'style', previewStyles );
    		controls.after( previewHTML );
    	} else {
    	// Otherwise, just update the styles of the existing preview.
    		controls.next( '.settings-preview-container' ).find( '.settings-apply-preview' ).attr( 'style', previewStyles );
    	}
    	
    	// Set the button to "refresh" mode.
    	controls.find( '.a3rev-ui-settings-preview-button span' ).addClass( 'refresh' );
    }

   
  }; // End a3revButtonPreview Object // Don't remove this, or the sky will fall on your head.

/*-----------------------------------------------------------------------------------*/
/* Execute the above methods in the a3revButtonPreview object.
/*-----------------------------------------------------------------------------------*/
  
	$(document).ready(function () {

		a3revButtonPreview.loadPreviewButtons();
	
	});
  
})(jQuery);