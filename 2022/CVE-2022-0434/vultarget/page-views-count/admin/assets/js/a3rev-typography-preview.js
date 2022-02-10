/**
 * A3revThemes Typography Live Preview
 * 2011-10-07.
 *
 * @description The code below is designed to generate a live preview using the
 * setting specified in a "custom typography" field in the ResponsiFramework.
 *
 * @since 4.7.0
 */

(function ($) {

  a3revTypographyPreview = {
  
  	/**
  	 * loadPreviewButtons()
  	 *
  	 * @description Setup a "preview" button next to each typography field.
  	 * @since 4.7.0
  	 */
  
    loadPreviewButtons: function () {
     
     // Register event handlers.
     a3revTypographyPreview.handleEvents();
      
    }, // End loadPreviewButtons()
    
    /**
     * handleEvents()
     *
     * @description Handle the events.
     * @since 4.7.0
     */
    
    handleEvents: function () {
    	$(document).on( 'click', 'a.a3rev-ui-typography-preview-button', function () {
    		a3revTypographyPreview.generatePreview( $( this ) );
    		return false;
    	});
    	
    	$(document).on( 'click', 'a.preview_remove', function () {
    		a3revTypographyPreview.closePreview( $( this ) );
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
		target.parents( '.section' ).find( '.a3rev-ui-typography-preview-button .refresh' ).removeClass( 'refresh' );
     	target.parents( '.typography-preview-container' ).remove();
     }, 
    
    /**
     * generatePreview()
     *
     * @description Generate the typography preview.
     * @since 4.7.0
     */
     
    generatePreview: function ( target ) {
    	var previewText = 'Grumpy wizards make toxic brew for the evil Queen and Jack.';
    	var previewHTML = '';
    	var previewStyles = '';
    	
    	// Get the control parent element.
    	var controls = target.parents( '.a3rev-ui-typography-control' );
    	
    	var sizeSelector = '.a3rev-ui-typography-size';
    	
    	var fontSize = controls.find( sizeSelector ).val();
    	
    	var fontFace = controls.find( '.a3rev-ui-typography-face' ).val();
    	var fontStyle = controls.find( '.a3rev-ui-typography-style' ).val();
    	var fontColor = controls.find( '.a3rev-ui-typography-color' ).val();
   		var lineHeight = controls.find( '.a3rev-ui-typography-line_height' ).val();
		
		// Generate array of non-Google fonts.
		var nonGoogleFonts = new Array( 
										'Arial, sans-serif', 
										'Verdana, Geneva, sans-serif', 
										'Trebuchet MS, Tahoma, sans-serif', 
										'Georgia, serif', 
										'Times New Roman, serif', 
										'Tahoma, Geneva, Verdana, sans-serif', 
										'Palatino, Palatino Linotype, serif', 
										'Helvetica Neue, Helvetica, sans-serif', 
										'Calibri, Candara, Segoe, Optima, sans-serif', 
										'Myriad Pro, Myriad, sans-serif', 
										'Lucida Grande, Lucida Sans Unicode, Lucida Sans, sans-serif', 
										'Arial Black, sans-serif', 
										'Gill Sans, Gill Sans MT, Calibri, sans-serif', 
										'Geneva, Tahoma, Verdana, sans-serif', 
										'Impact, Charcoal, sans-serif', 
										'Courier, Courier New, monospace',
										'Century Gothic, sans-serif'
									);

		// Remove "current" class from previously modified typography field.
    	$( '.typography-preview' ).removeClass( 'current' );
    	
    	// Prepare selected fontFace for testing.
    	var fontFaceTest = fontFace.replace( /"/g, '&quot;' );

		// Load Google WebFonts, if we need to.    	
    	if ( jQuery.inArray( fontFaceTest, nonGoogleFonts ) == -1 ) { // -1 is returned if the item is not found in the array.

			// Prepare fontFace for use in the WebFont loader.
			var fontFaceString = fontFace;
			
			// Handle fonts that require specific weights when being included.
			switch ( fontFaceString ) {
				case 'Allan':
				case 'Cabin Sketch':
				case 'Corben':
				case 'UnifrakturCook':
					fontFaceString += ':700';
				break;
				
				case 'Buda':
				case 'Open Sans Condensed':
					fontFaceString += ':300';
				break;
				
				case 'Coda':
				case 'Sniglet':
					fontFaceString += ':800';
				break;
				
				case 'Raleway':
					fontFaceString += ':100';
				break;
			}
			
			
			fontFaceString += '::latin';
			fontFaceString = fontFaceString.replace( / /g, '+' );

			// Add the fontFace in quotes for use in the style declaration, if the selected font has a number in it.
			var specificFonts = new Array( 'Goudy Bookletter 1911' );
			
			if ( jQuery.inArray( fontFace, specificFonts ) > -1 ) {
				var fontFace = "'" + fontFace + "'";
			}

			WebFontConfig = {
			google: { families: [ fontFaceString ] }
			};
				
			if ( $( 'script.google-webfonts-script' ).length ) { $( 'script.google-webfonts-script' ).remove(); WebFont.load({ google: {families: [ fontFaceString ]} }); }
				
				(function() {
				var wf = document.createElement( 'script' );
				wf.src = ('https:' == document.location.protocol ? 'https' : 'http') +
				'://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js';
				wf.type = 'text/javascript';
				wf.async = 'true';
				var s = document.getElementsByTagName( 'script' )[0];
				s.parentNode.insertBefore( wf, s );
				
				$( wf ).addClass( 'google-webfonts-script' );
				
				})();
		
		}
		
    	// Construct styles.
    	previewStyles += 'font: ' + fontStyle + ' ' + fontSize + '/' + lineHeight + ' ' + fontFace + ';';
    	if ( fontColor ) { previewStyles += ' color: ' + fontColor + ';'; }
    	
    	// Construct preview HTML.
    	var previewHTMLInner = $( '<div />' ).addClass( 'current' ).addClass( 'typography-preview' ).text( previewText );
    	
    	previewHTML = $( '<div />' ).addClass( 'typography-preview-container' ).html( previewHTMLInner ).append( '<a href="#" class="preview_remove a3-plugin-ui-delete-icon">&nbsp;</a>' );
    	
    	// If no preview display is present, add one.
    	if ( ! controls.next( '.typography-preview-container' ).length ) {
    		previewHTML.find( '.typography-preview' ).attr( 'style', previewStyles );
    		controls.after( previewHTML );
    	} else {
    	// Otherwise, just update the styles of the existing preview.
    		controls.next( '.typography-preview-container' ).find( '.typography-preview' ).attr( 'style', previewStyles );
    	}
    	
    	// Set the button to "refresh" mode.
    	controls.find( '.a3rev-ui-typography-preview-button span' ).addClass( 'refresh' );
    }

   
  }; // End a3revTypographyPreview Object // Don't remove this, or the sky will fall on your head.

/*-----------------------------------------------------------------------------------*/
/* Execute the above methods in the a3revTypographyPreview object.
/*-----------------------------------------------------------------------------------*/
  
	$(document).ready(function () {

		a3revTypographyPreview.loadPreviewButtons();
	
	});
  
})(jQuery);