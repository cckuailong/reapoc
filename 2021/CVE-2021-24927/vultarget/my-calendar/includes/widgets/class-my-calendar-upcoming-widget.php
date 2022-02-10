<?php
/**
 * My Calendar Upcoming Events Widget
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
 * My Calendar Upcoming Events class.
 *
 * @category  Widgets
 * @package   My Calendar
 * @author    Joe Dolson
 * @copyright 2009
 * @license   GPLv2 or later
 * @version   1.0
 */
class My_Calendar_Upcoming_Widget extends WP_Widget {

	/**
	 * Contructor.
	 */
	function __construct() {
		parent::__construct(
			false,
			$name = __( 'My Calendar: Upcoming Events', 'my-calendar' ),
			array(
				'customize_selective_refresh' => true,
				'description'                 => __( 'List recent and future events.', 'my-calendar' ),
			)
		);
	}

	/**
	 * Build the My Calendar Upcoming Events widget output.
	 *
	 * @param array $args Widget arguments.
	 * @param array $instance This instance settings.
	 */
	function widget( $args, $instance ) {
		$before_widget = $args['before_widget'];
		$after_widget  = $args['after_widget'];
		$before_title  = $args['before_title'];
		$after_title   = $args['after_title'];

		$title  = ( isset( $instance['my_calendar_upcoming_title'] ) ) ? $instance['my_calendar_upcoming_title'] : '';
		$before = ( isset( $instance['my_calendar_upcoming_before'] ) ) ? $instance['my_calendar_upcoming_before'] : '';
		$after  = ( isset( $instance['my_calendar_upcoming_after'] ) ) ? $instance['my_calendar_upcoming_after'] : '';
		$skip   = ( isset( $instance['my_calendar_upcoming_skip'] ) ) ? $instance['my_calendar_upcoming_skip'] : '';
		$show   = ( isset( $instance['my_calendar_upcoming_show_today'] ) ) ? $instance['my_calendar_upcoming_show_today'] : '';
		$type   = ( isset( $instance['my_calendar_upcoming_type'] ) ) ? $instance['my_calendar_upcoming_type'] : '';
		$order  = ( isset( $instance['my_calendar_upcoming_order'] ) ) ? $instance['my_calendar_upcoming_order'] : '';
		$cat    = ( isset( $instance['my_calendar_upcoming_category'] ) ) ? $instance['my_calendar_upcoming_category'] : '';

		$the_title      = apply_filters( 'widget_title', $title, $instance, $args );
		$the_template   = ( isset( $instance['my_calendar_upcoming_template'] ) ) ? $instance['my_calendar_upcoming_template'] : '';
		$the_substitute = ( isset( $instance['my_calendar_no_events_text'] ) ) ? $instance['my_calendar_no_events_text'] : '';
		$before         = ( '' !== $before ) ? esc_attr( $instance['my_calendar_upcoming_before'] ) : 3;
		$after          = ( '' !== $after ) ? esc_attr( $instance['my_calendar_upcoming_after'] ) : 3;
		$skip           = ( '' !== $skip ) ? esc_attr( $instance['my_calendar_upcoming_skip'] ) : 0;
		$show_today     = ( 'no' === $show ) ? 'no' : 'yes';
		$type           = esc_attr( $type );
		$order          = esc_attr( $order );
		$the_category   = ( '' === $cat ) ? 'default' : esc_attr( $instance['my_calendar_upcoming_category'] );
		$author         = ( ! isset( $instance['my_calendar_upcoming_author'] ) || '' === $instance['my_calendar_upcoming_author'] ) ? 'default' : esc_attr( $instance['my_calendar_upcoming_author'] );
		$host           = ( ! isset( $instance['mc_host'] ) || '' === $instance['mc_host'] ) ? 'default' : esc_attr( $instance['mc_host'] );
		$ltype          = ( ! isset( $instance['ltype'] ) || '' === $instance['ltype'] ) ? '' : esc_attr( $instance['ltype'] );
		$lvalue         = ( ! isset( $instance['lvalue'] ) || '' === $instance['lvalue'] ) ? '' : esc_attr( $instance['lvalue'] );
		$widget_link    = ( isset( $instance['my_calendar_upcoming_linked'] ) && 'yes' === $instance['my_calendar_upcoming_linked'] ) ? mc_get_uri( false, $instance ) : '';
		$widget_link    = ( ! empty( $instance['mc_link'] ) ) ? esc_url( $instance['mc_link'] ) : $widget_link;
		$widget_title   = empty( $the_title ) ? '' : $the_title;
		$widget_title   = ( '' === $widget_link ) ? $widget_title : "<a href='$widget_link'>$widget_title</a>";
		$widget_title   = ( '' !== $widget_title ) ? $before_title . $widget_title . $after_title : '';
		$month          = ( 0 === strpos( $type, 'month+' ) ) ? date_i18n( 'F', strtotime( $type ) ) : date_i18n( 'F' );
		$widget_title   = str_replace( '{month}', $month, $widget_title );
		$from           = ( isset( $instance['mc_from'] ) ) ? $instance['mc_from'] : false;
		$to             = ( isset( $instance['mc_to'] ) ) ? $instance['mc_to'] : false;
		$site           = ( isset( $instance['mc_site'] ) ) ? $instance['mc_site'] : false;

		$args = array(
			'before'     => $before,
			'after'      => $after,
			'type'       => $type,
			'category'   => $the_category,
			'template'   => $the_template,
			'fallback'   => $the_substitute,
			'order'      => $order,
			'skip'       => $skip,
			'show_today' => $show_today,
			'author'     => $author,
			'host'       => $host,
			'from'       => $from,
			'ltype'      => $ltype,
			'lvalue'     => $lvalue,
			'to'         => $to,
			'site'       => $site,
		);

		$the_events = my_calendar_upcoming_events( $args );
		if ( '' !== $the_events ) {
			echo $before_widget;
			echo $widget_title;
			echo $the_events;
			echo $after_widget;
		}
	}

	/**
	 * Edit the upcoming events widget.
	 *
	 * @param array $instance Current widget settings.
	 */
	function form( $instance ) {
		$defaults = mc_widget_defaults();
		$title    = ( isset( $instance['my_calendar_upcoming_title'] ) ) ? esc_attr( $instance['my_calendar_upcoming_title'] ) : '';
		$template = ( isset( $instance['my_calendar_upcoming_template'] ) ) ? esc_attr( $instance['my_calendar_upcoming_template'] ) : '';
		if ( ! $template ) {
			$template = $defaults['upcoming']['template'];
		}
		$text       = ( isset( $instance['my_calendar_no_events_text'] ) ) ? esc_attr( $instance['my_calendar_no_events_text'] ) : '';
		$category   = ( isset( $instance['my_calendar_upcoming_category'] ) ) ? esc_attr( $instance['my_calendar_upcoming_category'] ) : '';
		$author     = ( isset( $instance['my_calendar_upcoming_author'] ) ) ? esc_attr( $instance['my_calendar_upcoming_author'] ) : '';
		$host       = ( isset( $instance['mc_host'] ) ) ? esc_attr( $instance['mc_host'] ) : '';
		$ltype      = ( isset( $instance['ltype'] ) ) ? esc_attr( $instance['ltype'] ) : '';
		$lvalue     = ( isset( $instance['lvalue'] ) ) ? esc_attr( $instance['lvalue'] ) : '';
		$before     = ( isset( $instance['my_calendar_upcoming_before'] ) ) ? esc_attr( $instance['my_calendar_upcoming_before'] ) : 3;
		$after      = ( isset( $instance['my_calendar_upcoming_after'] ) ) ? esc_attr( $instance['my_calendar_upcoming_after'] ) : 3;
		$show_today = ( isset( $instance['my_calendar_upcoming_show_today'] ) ) ? esc_attr( $instance['my_calendar_upcoming_show_today'] ) : 'no';
		$type       = ( isset( $instance['my_calendar_upcoming_type'] ) ) ? esc_attr( $instance['my_calendar_upcoming_type'] ) : 'events';
		$order      = ( isset( $instance['my_calendar_upcoming_order'] ) ) ? esc_attr( $instance['my_calendar_upcoming_order'] ) : 'asc';
		$linked     = ( isset( $instance['my_calendar_upcoming_linked'] ) ) ? esc_attr( $instance['my_calendar_upcoming_linked'] ) : '';
		$from       = ( isset( $instance['mc_from'] ) ) ? esc_attr( $instance['mc_from'] ) : '';
		$to         = ( isset( $instance['mc_to'] ) ) ? esc_attr( $instance['mc_to'] ) : '';
		$site       = ( isset( $instance['mc_site'] ) ) ? esc_attr( $instance['mc_site'] ) : false;

		if ( 'yes' === $linked ) {
			$default_link = mc_get_uri( false, $instance );
		} else {
			$default_link = '';
		}
		$link = ( ! empty( $instance['mc_link'] ) ) ? esc_url( $instance['mc_link'] ) : $default_link;
		$skip = ( isset( $instance['my_calendar_upcoming_skip'] ) ) ? esc_attr( $instance['my_calendar_upcoming_skip'] ) : 0;
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'my_calendar_upcoming_title' ); ?>"><?php _e( 'Title', 'my-calendar' ); ?>:</label><br/>
			<input class="widefat" type="text" id="<?php echo $this->get_field_id( 'my_calendar_upcoming_title' ); ?>" name="<?php echo $this->get_field_name( 'my_calendar_upcoming_title' ); ?>" value="<?php echo esc_attr( $title ); ?>"/>
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
			<label for="<?php echo $this->get_field_id( 'my_calendar_upcoming_template' ); ?>"><?php _e( 'Template', 'my-calendar' ); ?></label><br/>
			<textarea class="widefat" rows="6" cols="20" id="<?php echo $this->get_field_id( 'my_calendar_upcoming_template' ); ?>" name="<?php echo $this->get_field_name( 'my_calendar_upcoming_template' ); ?>"><?php echo esc_attr( $template ); ?></textarea>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'mc_link' ); ?>"><?php _e( 'Widget title links to:', 'my-calendar' ); ?></label><br/>
			<input class="widefat" type="text" id="<?php echo $this->get_field_id( 'mc_link' ); ?>" name="<?php echo $this->get_field_name( 'mc_link' ); ?>" value="<?php echo $link; ?>"/>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'my_calendar_upcoming_type' ); ?>"><?php _e( 'Display upcoming events by:', 'my-calendar' ); ?></label>
			<select id="<?php echo $this->get_field_id( 'my_calendar_upcoming_type' ); ?>" name="<?php echo $this->get_field_name( 'my_calendar_upcoming_type' ); ?>">
				<option value="events" <?php echo ( 'events' === $type ) ? 'selected="selected"' : ''; ?>><?php _e( 'Events (e.g. 2 past, 3 future)', 'my-calendar' ); ?></option>
				<option value="days" <?php echo ( 'days' === $type ) ? 'selected="selected"' : ''; ?>><?php _e( 'Dates (e.g. 4 days past, 5 forward)', 'my-calendar' ); ?></option>
				<option value="month" <?php echo ( 'month' === $type ) ? 'selected="selected"' : ''; ?>><?php _e( 'Show current month', 'my-calendar' ); ?></option>
				<option value="month+1" <?php echo ( 'month+1' === $type ) ? 'selected="selected"' : ''; ?>><?php _e( 'Show next month', 'my-calendar' ); ?></option>
				<option value="month+2" <?php echo ( 'month+2' === $type ) ? 'selected="selected"' : ''; ?>><?php _e( 'Show 2nd month out', 'my-calendar' ); ?></option>
				<option value="month+3" <?php echo ( 'month+3' === $type ) ? 'selected="selected"' : ''; ?>><?php _e( 'Show 3rd month out', 'my-calendar' ); ?></option>
				<option value="month+4" <?php echo ( 'month+4' === $type ) ? 'selected="selected"' : ''; ?>><?php _e( 'Show 4th month out', 'my-calendar' ); ?></option>
				<option value="month+5" <?php echo ( 'month+5' === $type ) ? 'selected="selected"' : ''; ?>><?php _e( 'Show 5th month out', 'my-calendar' ); ?></option>
				<option value="month+6" <?php echo ( 'month+6' === $type ) ? 'selected="selected"' : ''; ?>><?php _e( 'Show 6th month out', 'my-calendar' ); ?></option>
				<option value="month+7" <?php echo ( 'month+7' === $type ) ? 'selected="selected"' : ''; ?>><?php _e( 'Show 7th month out', 'my-calendar' ); ?></option>
				<option value="month+8" <?php echo ( 'month+8' === $type ) ? 'selected="selected"' : ''; ?>><?php _e( 'Show 8th month out', 'my-calendar' ); ?></option>
				<option value="month+9" <?php echo ( 'month+9' === $type ) ? 'selected="selected"' : ''; ?>><?php _e( 'Show 9th month out', 'my-calendar' ); ?></option>
				<option value="month+10" <?php echo ( 'month+10' === $type ) ? 'selected="selected"' : ''; ?>><?php _e( 'Show 10th month out', 'my-calendar' ); ?></option>
				<option value="month+11" <?php echo ( 'month+11' === $type ) ? 'selected="selected"' : ''; ?>><?php _e( 'Show 11th month out', 'my-calendar' ); ?></option>
				<option value="month+12" <?php echo ( 'month+12' === $type ) ? 'selected="selected"' : ''; ?>><?php _e( 'Show 12th month out', 'my-calendar' ); ?></option>
				<option value="year" <?php echo ( 'year' === $type ) ? 'selected="selected"' : ''; ?>><?php _e( 'Show current year', 'my-calendar' ); ?></option>
				<option value="custom" <?php echo ( 'custom' === $type ) ? 'selected="selected"' : ''; ?>><?php _e( 'Custom Dates', 'my-calendar' ); ?></option>
			</select>
		</p>
		<?php
		if ( 'custom' === $type ) {
			?>
		<p>
			<label for="<?php echo $this->get_field_id( 'mc_from' ); ?>"><?php _e( 'Start date', 'my-calendar' ); ?>:</label>
			<input type="text" id="<?php echo $this->get_field_id( 'mc_from' ); ?>" name="<?php echo $this->get_field_name( 'mc_from' ); ?>" value="<?php echo $from; ?>"/>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'mc_to' ); ?>"><?php _e( 'End date', 'my-calendar' ); ?>:</label>
			<input type="text" id="<?php echo $this->get_field_id( 'mc_to' ); ?>" name="<?php echo $this->get_field_name( 'mc_to' ); ?>" value="<?php echo $to; ?>"/>
		</p>
			<?php
		}
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'my_calendar_upcoming_skip' ); ?>"><?php _e( 'Skip the first <em>n</em> events', 'my-calendar' ); ?></label>
			<input type="text" id="<?php echo $this->get_field_id( 'my_calendar_upcoming_skip' ); ?>" name="<?php echo $this->get_field_name( 'my_calendar_upcoming_skip' ); ?>" value="<?php echo $skip; ?>"/>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'my_calendar_upcoming_order' ); ?>"><?php _e( 'Events sort order:', 'my-calendar' ); ?></label>
			<select id="<?php echo $this->get_field_id( 'my_calendar_upcoming_order' ); ?>" name="<?php echo $this->get_field_name( 'my_calendar_upcoming_order' ); ?>">
				<option value="asc" <?php echo ( 'asc' === $order ) ? 'selected="selected"' : ''; ?>><?php _e( 'Ascending (near to far)', 'my-calendar' ); ?></option>
				<option value="desc" <?php echo ( 'desc' === $order ) ? 'selected="selected"' : ''; ?>><?php _e( 'Descending (far to near)', 'my-calendar' ); ?></option>
			</select>
		</p>
		<?php
		if ( ! ( 'month' === $type || 'month+1' === $type || 'year' === $type ) ) {
			?>
			<p>
				<input type="text" id="<?php echo $this->get_field_id( 'my_calendar_upcoming_after' ); ?>" name="<?php echo $this->get_field_name( 'my_calendar_upcoming_after' ); ?>" value="<?php echo $after; ?>" size="1" maxlength="3"/>
				<label for="<?php echo $this->get_field_id( 'my_calendar_upcoming_after' ); ?>">
				<?php
				// Translators: "days" or "events".
				printf( __( '%s into the future;', 'my-calendar' ), $type );
				?>
				</label><br/>
				<input type="text" id="<?php echo $this->get_field_id( 'my_calendar_upcoming_before' ); ?>" name="<?php echo $this->get_field_name( 'my_calendar_upcoming_before' ); ?>" value="<?php echo $before; ?>" size="1" maxlength="3"/> <label for="<?php echo $this->get_field_id( 'my_calendar_upcoming_before' ); ?>">
				<?php
				// Translators: "days" or "events".
				printf( __( '%s from the past', 'my-calendar' ), $type );
				?>
				</label>
			</p>
			<?php
		}
		?>
		<p>
			<input type="checkbox" id="<?php echo $this->get_field_id( 'my_calendar_upcoming_show_today' ); ?>" name="<?php echo $this->get_field_name( 'my_calendar_upcoming_show_today' ); ?>" value="yes"<?php echo ( 'yes' === $show_today ) ? ' checked="checked"' : ''; ?> />
			<label for="<?php echo $this->get_field_id( 'my_calendar_upcoming_show_today' ); ?>"><?php _e( "Include today's events", 'my-calendar' ); ?></label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'my_calendar_no_events_text' ); ?>"><?php _e( 'Show this text if there are no events meeting your criteria:', 'my-calendar' ); ?></label><br/>
			<input class="widefat" type="text" id="<?php echo $this->get_field_id( 'my_calendar_no_events_text' ); ?>" name="<?php echo $this->get_field_name( 'my_calendar_no_events_text' ); ?>" value="<?php echo $text; ?>"/>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'my_calendar_upcoming_category' ); ?>"><?php _e( 'Category or categories to display:', 'my-calendar' ); ?></label><br/>
			<input class="widefat" type="text" id="<?php echo $this->get_field_id( 'my_calendar_upcoming_category' ); ?>" name="<?php echo $this->get_field_name( 'my_calendar_upcoming_category' ); ?>" value="<?php echo $category; ?>"/>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'my_calendar_upcoming_author' ); ?>"><?php _e( 'Author or authors to show:', 'my-calendar' ); ?></label><br/>
			<input class="widefat" type="text" id="<?php echo $this->get_field_id( 'my_calendar_upcoming_author' ); ?>" name="<?php echo $this->get_field_name( 'my_calendar_upcoming_author' ); ?>" value="<?php echo $author; ?>"/>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'mc_host' ); ?>"><?php _e( 'Host or hosts to show:', 'my-calendar' ); ?></label><br/>
			<input class="widefat" type="text" id="<?php echo $this->get_field_id( 'mc_host' ); ?>" name="<?php echo $this->get_field_name( 'mc_host' ); ?>" value="<?php echo $host; ?>"/>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'ltype' ); ?>"><?php _e( 'Location (Type)', 'my-calendar' ); ?></label><br/>
			<select name="<?php echo $this->get_field_name( 'ltype' ); ?>" id="<?php echo $this->get_field_id( 'ltype' ); ?>" class="widefat">
				<option value=''><?php _e( 'All locations', 'my-calendar' ); ?></option>
				<option value='event_label' <?php selected( $ltype, 'event_label' ); ?>><?php _e( 'Location Name', 'my-calendar' ); ?></option>
				<option value='event_city' <?php selected( $ltype, 'event_city' ); ?>><?php _e( 'City', 'my-calendar' ); ?></option>
				<option value='event_state' <?php selected( $ltype, 'event_state' ); ?>><?php _e( 'State', 'my-calendar' ); ?></option>
				<option value='event_postcode' <?php selected( $ltype, 'event_postcode' ); ?>><?php _e( 'Postal Code', 'my-calendar' ); ?></option>
				<option value='event_country' <?php selected( $ltype, 'event_country' ); ?>><?php _e( 'Country', 'my-calendar' ); ?></option>
				<option value='event_region' <?php selected( $ltype, 'event_region' ); ?>><?php _e( 'Region', 'my-calendar' ); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'lvalue' ); ?>"><?php _e( 'Location (Value)', 'my-calendar' ); ?></label><br/>
			<input type="text" class="widefat" name="<?php echo $this->get_field_name( 'lvalue' ); ?>" id="<?php echo $this->get_field_id( 'lvalue' ); ?>" value="<?php echo esc_attr( $lvalue ); ?>" />
		</p>
		<?php
	}

	/**
	 * Update the My Calendar Upcoming Widget settings.
	 *
	 * @param object $new Widget settings new data.
	 * @param object $instance Widget settings instance.
	 *
	 * @return $instance Updated instance.
	 */
	function update( $new, $instance ) {
		$instance = array_map( 'mc_kses_post', array_merge( $instance, $new ) );
		if ( ! isset( $new['my_calendar_upcoming_show_today'] ) ) {
			$instance['my_calendar_upcoming_show_today'] = 'no';
		}

		return $instance;
	}
}
