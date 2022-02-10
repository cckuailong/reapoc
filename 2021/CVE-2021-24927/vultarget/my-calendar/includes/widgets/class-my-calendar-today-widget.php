<?php
/**
 * My Calendar Today's Events Widget
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
 * My Calendar Today's Events class.
 *
 * @category  Widgets
 * @package   My Calendar
 * @author    Joe Dolson
 * @copyright 2009
 * @license   GPLv2 or later
 * @version   1.0
 */
class My_Calendar_Today_Widget extends WP_Widget {

	/**
	 * Contructor.
	 */
	function __construct() {
		parent::__construct(
			false,
			$name = __( 'My Calendar: Today\'s Events', 'my-calendar' ),
			array(
				'customize_selective_refresh' => true,
				'description'                 => __( 'A list of events today.', 'my-calendar' ),
			)
		);
	}

	/**
	 * Build the My Calendar Today's Events widget output.
	 *
	 * @param array $args Widget arguments.
	 * @param array $instance This instance settings.
	 */
	function widget( $args, $instance ) {
		$before_widget = $args['before_widget'];
		$after_widget  = $args['after_widget'];
		$before_title  = $args['before_title'];
		$after_title   = $args['after_title'];
		$today_title   = isset( $instance['my_calendar_today_title'] ) ? $instance['my_calendar_today_title'] : '';
		$template      = isset( $instance['my_calendar_today_template'] ) ? $instance['my_calendar_today_template'] : '';
		$no_events     = isset( $instance['my_calendar_no_events_text'] ) ? $instance['my_calendar_no_events_text'] : '';
		$category      = isset( $instance['my_calendar_today_category'] ) ? $instance['my_calendar_today_category'] : '';

		$the_title      = apply_filters( 'widget_title', $today_title, $instance, $args );
		$the_template   = $template;
		$the_substitute = $no_events;
		$the_category   = ( '' === $category ) ? 'default' : esc_attr( $instance['my_calendar_today_category'] );
		$author         = ( ! isset( $instance['my_calendar_today_author'] ) || '' === $instance['my_calendar_today_author'] ) ? 'all' : esc_attr( $instance['my_calendar_today_author'] );
		$host           = ( ! isset( $instance['mc_host'] ) || '' === $instance['mc_host'] ) ? 'all' : esc_attr( $instance['mc_host'] );
		$default_link   = mc_get_uri( false, $args );
		$widget_link    = ( ! empty( $instance['my_calendar_today_linked'] ) && 'yes' === $instance['my_calendar_today_linked'] ) ? $default_link : '';
		$widget_link    = ( ! empty( $instance['mc_link'] ) ) ? esc_url( $instance['mc_link'] ) : $widget_link;
		$widget_title   = empty( $the_title ) ? '' : $the_title;
		$date           = ( ! empty( $instance['mc_date'] ) ) ? $instance['mc_date'] : false;
		$site           = ( isset( $instance['mc_site'] ) ) ? $instance['mc_site'] : false;

		if ( false !== strpos( $widget_title, '{date}' ) ) {
			$widget_title = str_replace( '{date}', date_i18n( mc_date_format() ), $widget_title );
		}
		$widget_title = ( '' === $widget_link ) ? $widget_title : "<a href='$widget_link'>$widget_title</a>";
		$widget_title = ( '' !== $widget_title ) ? $before_title . $widget_title . $after_title : '';

		$args = array(
			'category' => $the_category,
			'template' => $the_template,
			'fallback' => $the_substitute,
			'author'   => $author,
			'host'     => $host,
			'date'     => $date,
			'site'     => $site,
		);

		$the_events = my_calendar_todays_events( $args );
		if ( '' !== $the_events ) {
			echo $before_widget;
			echo $widget_title;
			echo $the_events;
			echo $after_widget;
		}
	}

	/**
	 * Edit the today's events widget.
	 *
	 * @param array $instance Current widget settings.
	 */
	function form( $instance ) {
		$defaults        = mc_widget_defaults();
		$widget_title    = ( isset( $instance['my_calendar_today_title'] ) ) ? esc_attr( $instance['my_calendar_today_title'] ) : '';
		$widget_template = ( isset( $instance['my_calendar_today_template'] ) ) ? esc_attr( $instance['my_calendar_today_template'] ) : '';
		if ( ! $widget_template ) {
			$widget_template = $defaults['today']['template'];
		}
		$widget_text     = ( isset( $instance['my_calendar_no_events_text'] ) ) ? esc_attr( $instance['my_calendar_no_events_text'] ) : '';
		$widget_category = ( isset( $instance['my_calendar_today_category'] ) ) ? esc_attr( $instance['my_calendar_today_category'] ) : '';
		$widget_linked   = ( isset( $instance['my_calendar_today_linked'] ) ) ? esc_attr( $instance['my_calendar_today_linked'] ) : '';
		$date            = ( isset( $instance['mc_date'] ) ) ? esc_attr( $instance['mc_date'] ) : '';
		if ( 'yes' === $widget_linked ) {
			$default_link = mc_get_uri( false, $instance );
		} else {
			$default_link = '';
		}
		$widget_link   = ( ! empty( $instance['mc_link'] ) ) ? esc_url( $instance['mc_link'] ) : $default_link;
		$widget_author = ( isset( $instance['my_calendar_today_author'] ) ) ? esc_attr( $instance['my_calendar_today_author'] ) : '';
		$widget_host   = ( isset( $instance['mc_host'] ) ) ? esc_attr( $instance['mc_host'] ) : '';
		$site          = ( isset( $instance['mc_site'] ) ) ? esc_attr( $instance['mc_site'] ) : '';

		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'my_calendar_today_title' ); ?>"><?php _e( 'Title', 'my-calendar' ); ?>:</label><br/>
			<input class="widefat" type="text" id="<?php echo $this->get_field_id( 'my_calendar_today_title' ); ?>" name="<?php echo $this->get_field_name( 'my_calendar_today_title' ); ?>" value="<?php echo $widget_title; ?>"/>
		</p>
		<?php
		if ( function_exists( 'is_multisite' ) && is_multisite() ) {
			?>
		<p>
			<label for="<?php echo $this->get_field_id( 'mc_site' ); ?>"><?php _e( 'Blog ID', 'my-calendar' ); ?>:</label><br/>
			<input class="widefat" type="text" id="<?php echo $this->get_field_id( 'mc_site' ); ?>" name="<?php echo $this->get_field_name( 'mc_site' ); ?>" value="<?php echo esc_attr( $site ); ?>"/>
		</p>
			<?php
		}
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'my_calendar_today_template' ); ?>"><?php _e( 'Template', 'my-calendar' ); ?></label><br/>
			<textarea class="widefat" rows="8" cols="20" id="<?php echo $this->get_field_id( 'my_calendar_today_template' ); ?>" name="<?php echo $this->get_field_name( 'my_calendar_today_template' ); ?>"><?php echo $widget_template; ?></textarea>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'mc_link' ); ?>"><?php _e( 'Widget title links to:', 'my-calendar' ); ?></label><br/>
			<input class="widefat" type="text" id="<?php echo $this->get_field_id( 'mc_link' ); ?>" name="<?php echo $this->get_field_name( 'mc_link' ); ?>" value="<?php echo $widget_link; ?>"/>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'my_calendar_no_events_text' ); ?>"><?php _e( 'Show this text if there are no events today:', 'my-calendar' ); ?></label><br/>
			<input class="widefat" type="text" id="<?php echo $this->get_field_id( 'my_calendar_no_events_text' ); ?>" name="<?php echo $this->get_field_name( 'my_calendar_no_events_text' ); ?>" value="<?php echo $widget_text; ?>"/>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'mc_date' ); ?>"><?php _e( 'Custom date', 'my-calendar' ); ?></label><br/>
			<input class="widefat" type="text" id="<?php echo $this->get_field_id( 'mc_date' ); ?>" name="<?php echo $this->get_field_name( 'mc_date' ); ?>" value="<?php echo $date; ?>"/>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'my_calendar_today_category' ); ?>"><?php _e( 'Category or categories to display:', 'my-calendar' ); ?></label><br/>
			<input class="widefat" type="text" id="<?php echo $this->get_field_id( 'my_calendar_today_category' ); ?>" name="<?php echo $this->get_field_name( 'my_calendar_today_category' ); ?>" value="<?php echo $widget_category; ?>"/>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'my_calendar_today_author' ); ?>"><?php _e( 'Author or authors to show:', 'my-calendar' ); ?></label><br/>
			<input class="widefat" type="text" id="<?php echo $this->get_field_id( 'my_calendar_today_author' ); ?>" name="<?php echo $this->get_field_name( 'my_calendar_today_author' ); ?>" value="<?php echo $widget_author; ?>"/>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'mc_host' ); ?>"><?php _e( 'Host or hosts to show:', 'my-calendar' ); ?></label><br/>
			<input class="widefat" type="text" id="<?php echo $this->get_field_id( 'mc_host' ); ?>" name="<?php echo $this->get_field_name( 'mc_host' ); ?>" value="<?php echo $widget_host; ?>"/>
		</p>
		<?php
	}

	/**
	 * Update the My Calendar Today's Events Widget settings.
	 *
	 * @param object $new Widget settings new data.
	 * @param object $instance Widget settings instance.
	 *
	 * @return $instance Updated instance.
	 */
	function update( $new, $instance ) {
		$instance = array_map( 'mc_kses_post', array_merge( $instance, $new ) );

		return $instance;
	}
}
