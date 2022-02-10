/* Hiding empty settings sections */
jQuery( document ).ready( function() {

    jQuery( 'select[name="rtb-settings[location-select]"]' ).change(function() {

        manageHidingSections();
    });

    manageHidingSections();
});

function manageHidingSections() {

    jQuery( '.sap-settings-page .form-table tr.sap-hidden' ).each(function(){
        
        var associatedEmptyTable = jQuery( this ).parent().parent();

        associatedEmptyTable.hide();
        associatedEmptyTable.prev( 'h2' ).hide();
    });

    jQuery( '.sap-settings-page .form-table tr:not(.sap-hidden)' ).each(function(){
        
        var associatedNonEmptyTable = jQuery( this ).parent().parent();

        associatedNonEmptyTable.show();
        associatedNonEmptyTable.prev( 'h2' ).show();
    });
}


jQuery(document).ready(function() {
	jQuery('.rtb-spectrum').spectrum({
		showInput: true,
		showInitial: true,
		preferredFormat: "hex",
		allowEmpty: true
	});

	jQuery('.rtb-spectrum').css('display', 'inline');

	jQuery('.rtb-spectrum').on('change', function() {
		if (jQuery(this).val() != "") {
			jQuery(this).css('background', jQuery(this).val());
			var rgb = RTB_hexToRgb(jQuery(this).val());
			var Brightness = (rgb.r * 299 + rgb.g * 587 + rgb.b * 114) / 1000;
			if (Brightness < 100) {jQuery(this).css('color', '#ffffff');}
			else {jQuery(this).css('color', '#000000');}
		}
		else {
			jQuery(this).css('background', 'none');
		}
	});

	jQuery('.rtb-spectrum').each(function() {
		if (jQuery(this).val() != "") {
			jQuery(this).css('background', jQuery(this).val());
			var rgb = RTB_hexToRgb(jQuery(this).val());
			var Brightness = (rgb.r * 299 + rgb.g * 587 + rgb.b * 114) / 1000;
			if (Brightness < 100) {jQuery(this).css('color', '#ffffff');}
			else {jQuery(this).css('color', '#000000');}
		}
	});
});

function RTB_hexToRgb(hex) {
    var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
    return result ? {
        r: parseInt(result[1], 16),
        g: parseInt(result[2], 16),
        b: parseInt(result[3], 16)
    } : null;
}
