jQuery(document).ready(function() {

    if ( ! jQuery.isFunction( jQuery.fn.spectrum ) ) { return; }

    jQuery('.sap-spectrum').spectrum({
        showInput: true,
        showInitial: true,
        preferredFormat: "hex",
        allowEmpty: true
    });

    jQuery('.sap-spectrum').css('display', 'inline');

    jQuery('.sap-spectrum').on('change', function() {
        if (jQuery(this).val() != "") {
            jQuery(this).css('background', jQuery(this).val());
            var rgb = EWD_SAP_hexToRgb(jQuery(this).val());
            var Brightness = (rgb.r * 299 + rgb.g * 587 + rgb.b * 114) / 1000;
            if (Brightness < 100) {jQuery(this).css('color', '#ffffff');}
            else {jQuery(this).css('color', '#000000');}
        }
        else {
            jQuery(this).css('background', 'none');
        }
    });

    jQuery('.sap-spectrum').each(function() {
        if (jQuery(this).val() != "") {
            jQuery(this).css('background', jQuery(this).val());
            var rgb = EWD_SAP_hexToRgb(jQuery(this).val());
            var Brightness = (rgb.r * 299 + rgb.g * 587 + rgb.b * 114) / 1000;
            if (Brightness < 100) {jQuery(this).css('color', '#ffffff');}
            else {jQuery(this).css('color', '#000000');}
        }
    });

    jQuery( 'fieldset[data-conditional_on]' ).each( function() {
        
        var option = jQuery( this );
        var option_name = jQuery( 'input[name="option_page"]' ).val() + '[' + option.data( 'conditional_on' ) + ']';
        
        jQuery( '[name="' + option_name + '"], [name="' + option_name + '[]"]' ).on( 'change', function() {
            
            var option_value = jQuery( this ).attr( 'type' ) != 'checkbox' ? jQuery( this ).val() : 
                                ( ( option.data( 'conditional_on_value' ) == 1 || option.data( 'conditional_on_value' ) == '' ) ? jQuery( this ).is( ':checked' ) : 
                                    ( jQuery( this ).is( ':checked' ) ? option.data( 'conditional_on_value' ) : false ) );

            if ( option_value == option.data( 'conditional_on_value' ) ) {

                option.parent().parent().removeClass( 'sap-hidden' );
            }
             else {

                option.parent().parent().addClass( 'sap-hidden' );
            }
        });
    });
});

function EWD_SAP_hexToRgb(hex) {
    var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
    return result ? {
        r: parseInt(result[1], 16),
        g: parseInt(result[2], 16),
        b: parseInt(result[3], 16)
    } : null;
}

//OPTIONS PAGE YES/NO TOGGLE SWITCHES
jQuery(document).ready(function($){
	$('.sap-admin-option-toggle').on('change', function() {
		var Input_Name = $(this).data('inputname');
		if ($(this).is(':checked')) {
			$('input[name="' + Input_Name + '"][value="1"]').prop('checked', true).trigger('change');
			$('input[name="' + Input_Name + '"][value=""]').prop('checked', false);
		}
		else {
			$('input[name="' + Input_Name + '"][value="1"]').prop('checked', false).trigger('change');
			$('input[name="' + Input_Name + '"][value=""]').prop('checked', true);
		}
	});
});

/*LOCK BOXES*/
jQuery( document ).ready( function() {

    function resizeLockdownBoxes() {
        jQuery('.sap-premium-options-table-overlay').each(function(){
            
            var eachProTableOverlay = jQuery( this );
            var associatedTable = eachProTableOverlay.next();
            associatedTable.css('min-height', '260px');
            var tablePosition = associatedTable.position();

            eachProTableOverlay.css( 'width', associatedTable.outerWidth(true) + 'px' );
            eachProTableOverlay.css( 'height', associatedTable.outerHeight() + 'px' );
            eachProTableOverlay.css( 'left', tablePosition.left + 'px' );
            eachProTableOverlay.css( 'top', tablePosition.top + 'px' );
        });
    }

	setTimeout( resizeLockdownBoxes, 750 );
	jQuery( window ).on( 'resize', resizeLockdownBoxes );
});