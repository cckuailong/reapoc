<?php
/**
 * Registrations for the Events Calendar Field Group Template
 * Creates the outer wrapping element of all visible form fields
 *
 * @version 2.5 Registrations for the Events Calendar by Roundup WP
 *
 */
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>
<div class="rtec-form-fields-wrapper">
	<?php foreach ( $fields_atts as $field_name => $field_attributes ) {
		$fields->the_field_html( $field_name, $field_attributes, $errors, $submission_data, $event_meta['registrations_left'] );
	} ?>
</div>
