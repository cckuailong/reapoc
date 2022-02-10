<?php
/**
 * My Calendar Filters Widget
 *
 * @category Widgets
 * @package  My Calendar
 * @author   Joe Dolson
 * @license  GPLv2 or later
 * @link     https://www.joedolson.com/my-calendar/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * My Calendar Event Filters class.
 *
 * @category  Widgets
 * @package   My Calendar
 * @author    Joe Dolson
 * @copyright 2009
 * @license   GPLv2 or later
 * @version   1.0
 */
class My_Calendar_Filters extends WP_Widget {

	/**
	 * Contructor.
	 */
	function __construct() {
		parent::__construct(
			false,
			$name = __( 'My Calendar: Event Filters', 'my-calendar' ),
			array(
				'customize_selective_refresh' => true,
				'description'                 => __( 'Filter displayed events.', 'my-calendar' ),
			)
		);
	}

	/**
	 * Build the My Calendar Event filters widget output.
	 *
	 * @param array $args Widget arguments.
	 * @param array $instance This instance settings.
	 */
	function widget( $args, $instance ) {
		$before_widget = $args['before_widget'];
		$after_widget  = $args['after_widget'];
		$before_title  = $args['before_title'];
		$after_title   = $args['after_title'];

		$widget_title = apply_filters( 'widget_title', $instance['title'], $instance, $args );
		$widget_title = ( '' !== $widget_title ) ? $before_title . $widget_title . $after_title : '';
		$widget_url   = ( isset( $instance['url'] ) ) ? $instance['url'] : mc_get_uri();
		$ltype        = ( isset( $instance['ltype'] ) ) ? $instance['ltype'] : false;
		$show         = ( isset( $instance['show'] ) ) ? $instance['show'] : array();
		$show         = implode( $show, ',' );

		echo $before_widget;
		echo ( '' !== $instance['title'] ) ? $widget_title : '';

		echo mc_filters( $show, $widget_url, $ltype );
		echo $after_widget;
	}

	/**
	 * Edit the filters widget.
	 *
	 * @param array $instance Current widget settings.
	 */
	function form( $instance ) {
		$widget_title = ( isset( $instance['title'] ) ) ? $instance['title'] : '';
		$widget_url   = ( isset( $instance['url'] ) ) ? $instance['url'] : mc_get_uri();
		$ltype        = ( isset( $instance['ltype'] ) ) ? $instance['ltype'] : false;
		$show         = ( isset( $instance['show'] ) ) ? $instance['show'] : array();

		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'my-calendar' ); ?>:</label><br/>
			<input class="widefat" type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $widget_title ); ?>"/>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Target Calendar Page', 'my-calendar' ); ?>:</label><br/>
			<input class="widefat" type="text" id="<?php echo $this->get_field_id( 'url' ); ?>" name="<?php echo $this->get_field_name( 'url' ); ?>" value="<?php echo esc_url( $widget_url ); ?>"/>
		</p>
		<ul>
			<?php $locations = in_array( 'locations', $show, true ) ? 'checked="checked"' : ''; ?>
			<li>
				<input type="checkbox" id="<?php echo $this->get_field_id( 'show' ); ?>_locations" name="<?php echo $this->get_field_name( 'show' ); ?>[]" value="locations" <?php echo $locations; ?> /> <label for="<?php echo $this->get_field_id( 'show' ); ?>_locations"><?php _e( 'Locations', 'my-calendar' ); ?></label>
			</li>
			<?php $categories = in_array( 'categories', $show, true ) ? 'checked="checked"' : ''; ?>
			<li>
				<input type="checkbox" id="<?php echo $this->get_field_id( 'show' ); ?>_categories" name="<?php echo $this->get_field_name( 'show' ); ?>[]" value="categories" <?php echo $categories; ?> /> <label for="<?php echo $this->get_field_id( 'show' ); ?>_categories"><?php _e( 'Categories', 'my-calendar' ); ?></label>
			</li>
			<?php $access = in_array( 'access', $show, true ) ? 'checked="checked"' : ''; ?>
			<li>
				<input type="checkbox" id="<?php echo $this->get_field_id( 'show' ); ?>_access" name="<?php echo $this->get_field_name( 'show' ); ?>[]" value="access" <?php echo $access; ?> /> <label for="<?php echo $this->get_field_id( 'show' ); ?>_access"><?php _e( 'Accessibility Features', 'my-calendar' ); ?></label>
			</li>
		</ul>
		<p>
			<label for="<?php echo $this->get_field_id( 'ltype' ); ?>"><?php _e( 'Filter locations by', 'my-calendar' ); ?></label>
			<select id="<?php echo $this->get_field_id( 'ltype' ); ?>" name="<?php echo $this->get_field_name( 'ltype' ); ?>">
				<option value="name" <?php selected( $ltype, 'name' ); ?>><?php _e( 'Location Name', 'my-calendar' ); ?></option>
				<option value="state" <?php selected( $ltype, 'state' ); ?>><?php _e( 'State/Province', 'my-calendar' ); ?></option>
				<option value="city" <?php selected( $ltype, 'city' ); ?>><?php _e( 'City', 'my-calendar' ); ?></option>
				<option value="region" <?php selected( $ltype, 'region' ); ?>><?php _e( 'Region', 'my-calendar' ); ?></option>
				<option value="zip" <?php selected( $ltype, 'zip' ); ?>><?php _e( 'Postal Code', 'my-calendar' ); ?></option>
				<option value="country" <?php selected( $ltype, 'country' ); ?>><?php _e( 'Country', 'my-calendar' ); ?></option>
			</select>
		</p>
		<?php
	}

	/**
	 * Update the My Calendar Event Filters Widget settings.
	 *
	 * @param object $new Widget settings new data.
	 * @param object $instance Widget settings instance.
	 *
	 * @return $instance Updated instance.
	 */
	function update( $new, $instance ) {
		$instance['title'] = mc_kses_post( $new['title'] );
		$instance['url']   = esc_url_raw( $new['url'] );
		$instance['ltype'] = sanitize_title( $new['ltype'] );
		$instance['show']  = array_map( 'sanitize_title', $new['show'] );

		return $instance;
	}
}
