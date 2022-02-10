<?php
/**
 * Registrations for the Events Calendar RTEC Template
 * Creates the outer wrapping element of all HTML when the registration
 * form is live.
 *
 * @version 2.5 Registrations for the Events Calendar by Roundup WP
 *
 */
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>
<div class="rtec-outer-wrap<?php echo esc_attr( $outer_wrap_classes ); ?>"<?php echo $data_atts; ?>>
    <?php

    do_action( 'rtec_before_display_form', $before_display_args );

    echo $event_header_html;

    echo $attendee_list_html;

    echo $attendance_count_html;

    do_action( 'rtec_before_the_register_button' );

    ?>
	<div id="rtec" class="rtec<?php echo esc_attr( $classes_string ); ?>"<?php echo $data_string; ?>>
        <?php

        echo $register_button_html;

		do_action( 'rtec_before_the_form_html' );

		include RTEC_Form::get_template( 'form' );

		do_action( 'rtec_after_the_form_html' );

		echo $already_registered_tools_html;

		?>
	</div>
</div>
