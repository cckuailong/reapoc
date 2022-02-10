<?php
/**
 * My Calendar Simple Search Widget
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
 * My Calendar Simple Search class.
 *
 * @category  Widgets
 * @package   My Calendar
 * @author    Joe Dolson
 * @copyright 2009
 * @license   GPLv2 or later
 * @version   1.0
 */
class My_Calendar_Simple_Search extends WP_Widget {

	/**
	 * Contructor.
	 */
	function __construct() {
		parent::__construct(
			false,
			$name = __( 'My Calendar: Simple Event Search', 'my-calendar' ),
			array(
				'customize_selective_refresh' => true,
				'description'                 => __( 'Search your events.', 'my-calendar' ),
			)
		);
	}

	/**
	 * Build the My Calendar Event Search widget output.
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
		$widget_url   = ( isset( $instance['url'] ) ) ? $instance['url'] : false;
		echo $before_widget;
		echo ( '' !== $instance['title'] ) ? $widget_title : '';

		echo my_calendar_searchform( 'simple', $widget_url );
		echo $after_widget;
	}

	/**
	 * Edit the search widget.
	 *
	 * @param array $instance Current widget settings.
	 */
	function form( $instance ) {
		$widget_title = ( isset( $instance['title'] ) ) ? $instance['title'] : '';
		$widget_url   = ( isset( $instance['url'] ) ) ? $instance['url'] : '';
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'my-calendar' ); ?>:</label><br/>
			<input class="widefat" type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $widget_title ); ?>"/>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Search Results Page', 'my-calendar' ); ?>:</label><br/>
			<input class="widefat" type="text" id="<?php echo $this->get_field_id( 'url' ); ?>" name="<?php echo $this->get_field_name( 'url' ); ?>" value="<?php echo esc_url( $widget_url ); ?>"/>
		</p>
		<?php
	}

	/**
	 * Update the My Calendar Search Widget settings.
	 *
	 * @param object $new Widget settings new data.
	 * @param object $instance Widget settings instance.
	 *
	 * @return $instance Updated instance.
	 */
	function update( $new, $instance ) {
		$instance['title'] = mc_kses_post( $new['title'] );
		$instance['url']   = esc_url_raw( $new['url'] );

		return $instance;
	}
}
