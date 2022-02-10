<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Widget' ) ) {
	require_once ABSPATH . 'wp-admin/includes/widgets.php';
}

if ( !class_exists( 'rtbBookingFormWidget' ) ) {
/**
 * Booking form widget
 *
 * Extends WP_Widget to display a booking form in a widget.
 * @since 0.0.1
 */
class rtbBookingFormWidget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 * @since 0.0.1
	 */
	function __construct() {

		parent::__construct(
			'rtb_booking_form_widget',
			__('Booking Form', 'restaurant-reservations'),
			array( 'description' => __( 'Display a form to accept bookings.', 'restaurant-reservations' ), )
		);

	}

	/**
	 * Print the widget content
	 * @since 0.0.1
	 */
	public function widget( $args, $instance ) {

		global $rtb_controller;
		
		// Don't show the widget if the form has already been displayed. The
		// date and time pickers don't yet support multiple forms on a page.
		if ( $rtb_controller->form_rendered === true ) {
			return;
		}

		// Print the widget's HTML markup
		echo $args['before_widget'];
		if( isset( $instance['title'] ) ) {
			$title = apply_filters( 'widget_title', $instance['title'] );
			echo $args['before_title'] . $title . $args['after_title'];
		}
		echo rtb_print_booking_form();
		echo $args['after_widget'];

	}

	/**
	 * Print the form to configure this widget in the admin panel
	 * @since 1.0
	 */
	public function form( $instance ) {
		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"> <?php _e( 'Title' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text"<?php if ( isset( $instance['title'] ) ) : ?> value="<?php echo esc_attr( $instance['title'] ); ?>"<?php endif; ?>>
		</p>

		<?php
	}

	/**
	 * Sanitize and save the widget form values.
	 * @since 1.0
	 */
	public function update( $new_instance, $old_instance ) {

		$instance = array();
		if ( !empty( $new_instance['title'] ) ) {
			$instance['title'] = strip_tags( $new_instance['title'] );
		}

		return $instance;

	}

}
} // endif
