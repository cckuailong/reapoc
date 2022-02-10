/**
 * Tooltip JavaScript.
 *
 * @package    Wplegalpages
 * @subpackage Wplegalpages/admin
 * @author     wpeka <https://club.wpeka.com>
 */

jQuery( document ).ready(
	function(){
		// jQuery( '[data-toggle="tooltip"]' ).tooltip();.

		jQuery( '#lp-is_adult' ).change(
			function(){
				if (this.checked) {
					jQuery( '#exit_url_section' ).show();
				} else {
					jQuery( '#exit_url_section' ).hide();
				}
			}
		);

		jQuery( '#lp_privacy' ).change(
			function(){
				if (this.checked) {
					jQuery( '#privacy_page_section' ).show();
				} else {
					jQuery( '#privacy_page_section' ).hide();
				}
			}
		);

		jQuery( '.wplegal-template-language' ).change(
			function() {
				var selectedLang = jQuery( this ). children( "option:selected" ). val();
				if (selectedLang == 'eng') {
					jQuery( '.wplegal-template-eng' ).css( {"display":"list-item"} );
					jQuery( '.wplegal-template-fr' ).css( {"display":"none"} );
					jQuery( '.wplegal-template-de' ).css( {"display":"none"} );
				}
				if (selectedLang == 'fr') {
					jQuery( '.wplegal-template-fr' ).css( {"display":"list-item"} );
					jQuery( '.wplegal-template-eng' ).css( {"display":"none"} );
					jQuery( '.wplegal-template-de' ).css( {"display":"none"} );
				}
				if (selectedLang == 'de') {
					jQuery( '.wplegal-template-de' ).css( {"display":"list-item"} );
					jQuery( '.wplegal-template-fr' ).css( {"display":"none"} );
					jQuery( '.wplegal-template-eng' ).css( {"display":"none"} );
				}
			}
		);

		jQuery( '#lp_accept_submit' ).click(
			function() {
				var parent = jQuery( this ).parent( '.lp_accept_terms' );
				var data   = parent.find( 'input[type="hidden"]' );
				jQuery( this ).addClass( 'button-disabled' );
				jQuery.ajax(
					{
						type: 'post',
						dataType: 'json',
						url: ajaxurl + '?action=lp_accept_terms',
						data: data,
						success: function(msg) {
							if (msg.success) {
								window.location = jQuery( '.lp-accept-terms' ).find( 'input[name="redirect_url"]' ).val();
							} else {
								jQuery( '.lp_accept_terms' ).find( 'input[type="button"]' ).removeClass( 'button-disabled' );
							}
						}
					}
				)
			}
		);

		jQuery( '#wplegal_pages_settings_warning_disable' ).click(
			function() {
				jQuery( '#wplegalpages_settings_warning' ).css('display','none');
				var request = {
					action: "wplegalpages_disable_settings_warning",
				};
				jQuery.post( ajaxurl, request ).done();
			}
		);

		jQuery( '#wplegal_accordian' ).click(
			function() {
				var display_state = jQuery('#wplegal_refund_list').css('display');
				jQuery("#wplegal_accordian").toggleClass("wplegal-accordian-active");

				if(display_state === 'none'){
					jQuery('#wplegal_refund_list').css('display','block');
				}else{
					jQuery('#wplegal_refund_list').css('display','none');
				}
			}
		);

		jQuery( '#lp_ajax_settings_form' ).submit(
            function(e) {
                e.preventDefault();
                var dataV = jQuery(this).serialize();
                var url = jQuery('#lp_admin_ajax_url').val(); 
			   jQuery("#wplegalpages-save-settings-alert").fadeIn(400);
                jQuery.ajax({
                    type: 'POST',
                    url: url,
                    data: dataV + '&action=lp_save_admin_settings', 
                }).done(function (data) {
                    data = JSON.parse(data);
					jQuery("#wplegalpages-save-settings-alert").fadeOut(2500);
                });
            }
        );
	}
);
