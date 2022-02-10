"use strict";

var wpbcSummary = function (obj, $) {
  // Define private property
  var p_forms = obj.forms_structure = obj.forms_structure || [];

  obj.get_form_structure = function (resource_id) {
    return p_forms[resource_id];
  };

  obj.set_form_structure = function (resource_id, form_structure) {
    p_forms[resource_id] = form_structure;
  };

  return obj;
}(wpbcSummary || {}, jQuery);

function booking_form_submit_click_trigger(event, resource_id, booking_form_html, active_locale) {
  console.log(event, resource_id, booking_form_html, active_locale);
}

jQuery(".booking_form_div").on('booking_form_submit_click', booking_form_submit_click_trigger); // Trigger for dates selection in the booking form
// 	jQuery( ".booking_form_div" ).trigger( "date_selected", [ bk_type, date ] );
//  jQuery( ".booking_form_div" ).on('date_selected', function(event, bk_type, date) { ... } );

/*
?>
<script type="text/javascript">
	jQuery( document ).ready( function (){

		// Set Security - Nonce for Ajax  - Listing
		oper_contacts_listing.set_secure_param( 'nonce',   '<?php echo wp_create_nonce( 'oper_contacts_listing_ajx' . '_opernonce' ) ?>' );
		oper_contacts_listing.set_secure_param( 'user_id', '<?php echo get_current_user_id(); ?>' );
		oper_contacts_listing.set_secure_param( 'locale',  '<?php echo get_user_locale(); ?>' );

		// Set other parameters
		oper_contacts_listing.set_other_param( 'listing_container',    '.oper_contacts_listing_container' );
		oper_contacts_listing.set_other_param( 'pagination_container', '.oper_contacts_pagination' );

		// Send Ajax request and show listing after this.
		oper_contacts_send_search_request_with_params( <?php echo wp_json_encode( $escaped_request_params ); ?> );
	} );
</script>
<?php
 */
//# sourceMappingURL=wpbc_form_summary.js.map