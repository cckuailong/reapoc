<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class wpbs_widget extends WP_Widget {

	/**
	 * Constructor
	 *
	 */
	public function __construct() {

		$widget_ops = array( 
			'classname'   => 'wpbs_calendar',
			'description' => __( 'Insert a WP Booking System Calendar', 'wp-booking-system' ),
		);

		parent::__construct( 'wpbs_widget', 'WP Booking System - Old Widget', $widget_ops );

	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 *
	 */
	public function widget( $args, $instance ) {
		
		// Remove the "wpbs" prefix to have a cleaner code
		$instance = ( ! empty( $instance ) && is_array( $instance ) ? $instance : array() );

		foreach( $instance as $key => $value ) {

			$instance[ str_replace( 'wpbs_', '', $key ) ] = $value;
			unset( $instance[$key] );

		}

		$calendar = wpbs_get_calendar( absint( $instance['select_calendar'] ) );

		if(is_null($calendar)){
			return false;
		}

		$calendar_args = array(
			'show_title'      => ( ! empty( $instance['show_title'] ) && $instance['show_title'] == 'yes' ? 1 : 0 ),
			'show_legend'     => ( ! empty( $instance['show_legend'] ) && $instance['show_legend'] == 'yes' ? 1 : 0 ),
			'language' 		  => ( ! empty( $instance['calendar_language'] ) ? ( $instance['calendar_language'] == 'auto' ? wpbs_get_locale() : $instance['calendar_language'] ) : 'en' ),
		);

		$form = wpbs_get_form( absint( $instance['select_form'] ) );

        $form_args = array(
            'language' 		  => ( ! empty( $instance['calendar_language'] ) ? ( $instance['calendar_language'] == 'auto' ? wpbs_get_locale() : $instance['calendar_language'] ) : 'en' ),
        );
		
		$output = '<div class="wpbs-main-wrapper wpbs-main-wrapper-calendar-' . $instance['select_calendar'] . ' wpbs-main-wrapper-form-' . $instance['select_form'] . '">';

		// Initialize the calendar outputter
		$calendar_outputter = new WPBS_Calendar_Outputter($calendar, $calendar_args);

		$output .= $calendar_outputter->get_display();

		// Initialize the form outputter
		$form_outputter = new WPBS_Form_Outputter($form, $form_args, array(), $calendar->get('id'));

		$output .= $form_outputter->get_display();

		$output .= '</div>';

		echo $output;

	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 *
	 */
	public function form( $instance ) {
		
		global $wpdb;
        
        $calendar_id       = ( ! empty( $instance['wpbs_select_calendar'] ) ? $instance['wpbs_select_calendar'] : 0 );
        $show_title        = ( ! empty( $instance['wpbs_show_title'] ) ? $instance['wpbs_show_title'] : 'yes' );
        $show_legend       = ( ! empty( $instance['wpbs_show_legend'] ) ? $instance['wpbs_show_legend'] : 'yes' );
        $calendar_language = ( ! empty( $instance['wpbs_calendar_language'] ) ? $instance['wpbs_calendar_language'] : 'en' );
		$form_id       		= ( ! empty( $instance['wpbs_select_form'] ) ? $instance['wpbs_select_form'] : 0 );
        $calendars = wpbs_get_calendars();
		$forms = wpbs_get_forms();

        ?>
        
        <!-- Calendar -->
		<p>
			<label for="<?php echo $this->get_field_id('wpbs_select_calendar'); ?>"><?php echo __( 'Calendar', 'wp-booking-system' ); ?></label>

			<select name="<?php echo $this->get_field_name('wpbs_select_calendar'); ?>" id="<?php echo $this->get_field_id('wpbs_select_calendar'); ?>" class="widefat">
				<?php foreach( $calendars as $calendar ):?>
					<option <?php echo ( $calendar->get('id') == $calendar_id ? 'selected="selected"' : '' );?> value="<?php echo $calendar->get('id'); ?>"><?php echo $calendar->get('name'); ?></option>
				<?php endforeach;?>
			</select>
		</p>

		<!-- Show Title -->
		<p>
			<label for="<?php echo $this->get_field_id('wpbs_show_title'); ?>"><?php echo __( 'Display title', 'wp-booking-system' );?></label>

			<select name="<?php echo $this->get_field_name('wpbs_show_title'); ?>" id="<?php echo $this->get_field_id('wpbs_show_title'); ?>" class="widefat">
				<option value="yes"><?php echo __( 'Yes', 'wp-booking-system' ); ?></option>
				<option value="no" <?php echo ( $show_title == 'no' ? 'selected="selected"' : '' ); ?>><?php echo __( 'No', 'wp-booking-system' );?></option>
			</select>
		</p>

		<!-- Show Legend -->
		<p>
			<label for="<?php echo $this->get_field_id('wpbs_show_legend'); ?>"><?php echo __( 'Display legend', 'wp-booking-system' );?></label>

			<select name="<?php echo $this->get_field_name('wpbs_show_legend'); ?>" id="<?php echo $this->get_field_id('wpbs_show_legend'); ?>" class="widefat">
				<option value="yes"><?php echo __( 'Yes', 'wp-booking-system' ); ?></option>
				<option value="no" <?php echo ( $show_legend == 'no' ? 'selected="selected"' : '' ); ?>><?php echo __( 'No', 'wp-booking-system' ); ?></option>
			</select>
		</p>


		<!-- Calendar Language -->
		<p>
			<label for="<?php echo $this->get_field_id('wpbs_calendar_language'); ?>"><?php echo __( 'Language', 'wp-booking-system' );?></label>

			<select name="<?php echo $this->get_field_name('wpbs_calendar_language'); ?>" id="<?php echo $this->get_field_id('wpbs_calendar_language'); ?>" class="widefat">
				<?php
					$settings 		  = get_option( 'wpbs_settings', array() );
					$languages 		  = wpbs_get_languages();
					$active_languages = ( ! empty( $settings['active_languages'] ) ? $settings['active_languages'] : array() );
				?>

				<option value="auto"><?php echo __( 'Auto (let WP choose)', 'wp-booking-system' );?></option>

				<?php foreach( $active_languages as $code ):?>
					<option value="<?php echo esc_attr( $code ); ?>" <?php echo ( $calendar_language == $code ? 'selected="selected"' : '' ); ?>><?php echo ( ! empty( $languages[$code] ) ? $languages[$code] : '' ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>

		<!-- Form -->
		<p>
			<label for="<?php echo $this->get_field_id('wpbs_select_form'); ?>"><?php echo __( 'Form', 'wp-booking-system' ); ?></label>

			<select name="<?php echo $this->get_field_name('wpbs_select_form'); ?>" id="<?php echo $this->get_field_id('wpbs_select_form'); ?>" class="widefat">
				<?php foreach( $forms as $form ):?>
					<option <?php echo ( $form->get('id') == $form_id ? 'selected="selected"' : '' );?> value="<?php echo $form->get('id'); ?>"><?php echo $form->get('name'); ?></option>
				<?php endforeach;?>
			</select>
		</p>
        <?php

    }


	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 *
	 * @return array
	 *
	 */
	public function update( $new_instance, $old_instance ) {
		
		return $new_instance;

	}

}

class WPBS_Widget_Calendar extends WP_Widget {

	/**
	 * Constructor
	 *
	 */
	public function __construct() {

		$widget_ops = array( 
			'classname'   => 'wpbs_calendar',
			'description' => __( 'Insert a WP Booking System Calendar', 'wp-booking-system' ),
		);

		parent::__construct( 'wpbs_calendar', 'WP Booking System', $widget_ops );

	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 *
	 */
	public function widget( $args, $instance ) {

		// Remove the "wpbs" prefix to have a cleaner code
		$instance = ( ! empty( $instance ) && is_array( $instance ) ? $instance : array() );

		foreach( $instance as $key => $value ) {

			$instance[ str_replace( 'wpbs_', '', $key ) ] = $value;
			unset( $instance[$key] );

		}

		$calendar = wpbs_get_calendar( absint( $instance['select_calendar'] ) );

		if(is_null($calendar)){
			return false;
		}

		$calendar_args = array(
			'show_title'      => ( ! empty( $instance['show_title'] ) && $instance['show_title'] == 'yes' ? 1 : 0 ),
			'show_legend'     => ( ! empty( $instance['show_legend'] ) && $instance['show_legend'] == 'yes' ? 1 : 0 ),
			'language' 		  => ( ! empty( $instance['calendar_language'] ) ? ( $instance['calendar_language'] == 'auto' ? wpbs_get_locale() : $instance['calendar_language'] ) : 'en' ),
		);

		$form = wpbs_get_form( absint( $instance['select_form'] ) );

        $form_args = array(
            'language' 		  => ( ! empty( $instance['calendar_language'] ) ? ( $instance['calendar_language'] == 'auto' ? wpbs_get_locale() : $instance['calendar_language'] ) : 'en' ),
        );
		
		$output = '<div class="wpbs-main-wrapper wpbs-main-wrapper-calendar-' . $instance['select_calendar'] . ' wpbs-main-wrapper-form-' . $instance['select_form'] . '">';

		// Initialize the calendar outputter
		$calendar_outputter = new WPBS_Calendar_Outputter($calendar, $calendar_args);

		$output .= $calendar_outputter->get_display();

		// Initialize the form outputter
		$form_outputter = new WPBS_Form_Outputter($form, $form_args, array(), $calendar->get('id'));

		$output .= $form_outputter->get_display();

		$output .= '</div>';

		echo $output;

	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 *
	 */
	public function form( $instance ) {
		
		global $wpdb;
        
        $calendar_id       = ( ! empty( $instance['wpbs_select_calendar'] ) ? $instance['wpbs_select_calendar'] : 0 );
        $show_title        = ( ! empty( $instance['wpbs_show_title'] ) ? $instance['wpbs_show_title'] : 'yes' );
        $show_legend       = ( ! empty( $instance['wpbs_show_legend'] ) ? $instance['wpbs_show_legend'] : 'yes' );
        $calendar_language = ( ! empty( $instance['wpbs_calendar_language'] ) ? $instance['wpbs_calendar_language'] : 'en' );
		$form_id       		= ( ! empty( $instance['wpbs_select_form'] ) ? $instance['wpbs_select_form'] : 0 );
        $calendars = wpbs_get_calendars();
		$forms = wpbs_get_forms();

        ?>
        
        <!-- Calendar -->
		<p>
			<label for="<?php echo $this->get_field_id('wpbs_select_calendar'); ?>"><?php echo __( 'Calendar', 'wp-booking-system' ); ?></label>

			<select name="<?php echo $this->get_field_name('wpbs_select_calendar'); ?>" id="<?php echo $this->get_field_id('wpbs_select_calendar'); ?>" class="widefat">
				<?php foreach( $calendars as $calendar ):?>
					<option <?php echo ( $calendar->get('id') == $calendar_id ? 'selected="selected"' : '' );?> value="<?php echo $calendar->get('id'); ?>"><?php echo $calendar->get('name'); ?></option>
				<?php endforeach;?>
			</select>
		</p>

		<!-- Show Title -->
		<p>
			<label for="<?php echo $this->get_field_id('wpbs_show_title'); ?>"><?php echo __( 'Display title', 'wp-booking-system' );?></label>

			<select name="<?php echo $this->get_field_name('wpbs_show_title'); ?>" id="<?php echo $this->get_field_id('wpbs_show_title'); ?>" class="widefat">
				<option value="yes"><?php echo __( 'Yes', 'wp-booking-system' ); ?></option>
				<option value="no" <?php echo ( $show_title == 'no' ? 'selected="selected"' : '' ); ?>><?php echo __( 'No', 'wp-booking-system' );?></option>
			</select>
		</p>

		<!-- Show Legend -->
		<p>
			<label for="<?php echo $this->get_field_id('wpbs_show_legend'); ?>"><?php echo __( 'Display legend', 'wp-booking-system' );?></label>

			<select name="<?php echo $this->get_field_name('wpbs_show_legend'); ?>" id="<?php echo $this->get_field_id('wpbs_show_legend'); ?>" class="widefat">
				<option value="yes"><?php echo __( 'Yes', 'wp-booking-system' ); ?></option>
				<option value="no" <?php echo ( $show_legend == 'no' ? 'selected="selected"' : '' ); ?>><?php echo __( 'No', 'wp-booking-system' ); ?></option>
			</select>
		</p>


		<!-- Calendar Language -->
		<p>
			<label for="<?php echo $this->get_field_id('wpbs_calendar_language'); ?>"><?php echo __( 'Language', 'wp-booking-system' );?></label>

			<select name="<?php echo $this->get_field_name('wpbs_calendar_language'); ?>" id="<?php echo $this->get_field_id('wpbs_calendar_language'); ?>" class="widefat">
				<?php
					$settings 		  = get_option( 'wpbs_settings', array() );
					$languages 		  = wpbs_get_languages();
					$active_languages = ( ! empty( $settings['active_languages'] ) ? $settings['active_languages'] : array() );
				?>

				<option value="auto"><?php echo __( 'Auto (let WP choose)', 'wp-booking-system' );?></option>

				<?php foreach( $active_languages as $code ):?>
					<option value="<?php echo esc_attr( $code ); ?>" <?php echo ( $calendar_language == $code ? 'selected="selected"' : '' ); ?>><?php echo ( ! empty( $languages[$code] ) ? $languages[$code] : '' ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>

		<!-- Form -->
		<p>
			<label for="<?php echo $this->get_field_id('wpbs_select_form'); ?>"><?php echo __( 'Form', 'wp-booking-system' ); ?></label>

			<select name="<?php echo $this->get_field_name('wpbs_select_form'); ?>" id="<?php echo $this->get_field_id('wpbs_select_form'); ?>" class="widefat">
				<?php foreach( $forms as $form ):?>
					<option <?php echo ( $form->get('id') == $form_id ? 'selected="selected"' : '' );?> value="<?php echo $form->get('id'); ?>"><?php echo $form->get('name'); ?></option>
				<?php endforeach;?>
			</select>
		</p>
        <?php

    }


	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 *
	 * @return array
	 *
	 */
	public function update( $new_instance, $old_instance ) {
		
		return $new_instance;

	}

}

add_action( 'widgets_init', function() {
	register_widget( 'WPBS_Widget_Calendar' );
	
	if(!get_option('wpbs_db_version')){
		register_widget( 'wpbs_widget' );
	}
	
});