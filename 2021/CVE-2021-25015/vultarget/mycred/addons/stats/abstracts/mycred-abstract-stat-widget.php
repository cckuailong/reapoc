<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * myCRED_Statistics_Widget class
 * @see http://codex.mycred.me/
 * @since 1.6
 * @version 1.0
 */
if ( ! class_exists( 'myCRED_Statistics_Widget' ) ) :
	abstract class myCRED_Statistics_Widget {

		public $id;
		public $ctypes;
		public $dates;

		public $args;
		public $core;
		public $colors;

		/**
		 * Construct
		 */
		function __construct( $widget_id = NULL, $args = array(), $default = NULL ) {

			if ( $widget_id === NULL ) return false;

			$this->id = str_replace( array( '_', '-', ' ' ), '', $widget_id );
			$this->ctypes = mycred_get_types();

			if ( ! is_array( $default ) )
				$default = array(
					'ctypes' => 'all',
					'span'   => 0,
					'number' => 5
				);

			$this->args = wp_parse_args( $args, $default );

			if ( $this->args['ctypes'] == 'all' )
				$this->core = mycred();
			else
				$this->core = mycred( $this->args['ctypes'] );

			$this->colors = mycred_get_type_color();
			$this->now = current_time( 'timestamp' );

		}

		/**
		 * Get Data
		 * @version 1.0
		 */
		function get_data() {
			return array();
		}

		/**
		 * Widget
		 * @version 1.0
		 */
		function widget() {
			wp_die( 'function myCRED_Statistics_Widget::widget() must be over-ridden in a sub-class.' );
		}

		/**
		 * Preferences
		 * @version 1.0
		 */
		function preferences() {
		
		}

		/**
		 * Sanitize Preferences
		 * @version 1.0
		 */
		function sanitise_preferences( $data ) {
			return $data;
		}

		

		/**
		 * Action Buttons
		 * @version 1.0.1
		 */
		function action_buttons() {

			$screen_id = MYCRED_SLUG;
			$buttons   = array();

			if ( $this->args['ctypes'] != 'all' ) {

				$this->args['ctypes'] = str_replace( 'view_', '', $this->args['ctypes'] );
				if ( $this->args['ctypes'] != MYCRED_DEFAULT_TYPE_KEY )
					$screen_id .= '_' . $this->args['ctypes'];

				$url = add_query_arg( array( 'page' => $screen_id ), admin_url( 'admin.php' ) );
				$buttons[] = '<a href="' . esc_url( $url ) . '" class="button button-secondary button-large">' . __( 'View Log', 'mycred' ) . '</a>';

				$url = add_query_arg( array( 'page' => $screen_id . '-hooks' ), admin_url( 'admin.php' ) );
				$buttons[] = '<a href="' . esc_url( $url ) . '" class="button button-secondary button-large">' . __( 'Hooks', 'mycred' ) . '</a>';

				$url = add_query_arg( array( 'page' => $screen_id . '-settings' ), admin_url( 'admin.php' ) );
				$buttons[] = '<a href="' . esc_url( $url ) . '" class="button button-secondary button-large">' . __( 'Settings', 'mycred' ) . '</a>';

			}

			$output = '';
			if ( ! empty( $buttons ) ) {
				$output = '<p class="circulation-buttons mycred-action-buttons">' . implode( ' ', $buttons ) . '</p>';
			}

			return apply_filters( 'mycred_stats_action_buttons', $output, $this );

		}

		/**
		 * Format Number
		 * Attempts to combine all decimal type setups when displaying
		 * an overall value. Otherwise the give value will be formatted
		 * according to the selected point type.
		 * @version 1.0
		 */
		function format_number( $value = 0 ) {

			$result = $value;
			if ( isset( $this->args['ctypes'] ) ) {

				// All point types
				$selected_type = sanitize_text_field( $this->args['ctypes'] );
				if ( $selected_type == 'all' ) {

					// Find the highest decimal value
					$decimal_values = array();
					foreach ( $this->ctypes as $type_id => $label ) {

						$mycred = mycred( $type_id );

						if ( ! isset( $mycred->format['decimals'] ) )
							$decimals = $mycred->core['format']['decimals'];
						else
							$decimals = $mycred->format['decimals'];

						$decimal_values[ $decimals ] = $type_id;

					}

					// Sort to get the highest value
					krsort( $decimal_values, SORT_NUMERIC );
					reset( $decimal_values );
					$highest = key( $decimal_values );

					// Format the value using the highest decimal value
					$mycred = mycred( $decimal_values[ $highest ] );
					$result = $mycred->format_number( $value );

				}

				// Specific point type
				else {

					// Default type - always available under $this->core
					if ( $selected_type == MYCRED_DEFAULT_TYPE_KEY )
						$result = $this->core->format_number( $value );

					// Custom type
					elseif ( array_key_exists( $selected_type, $this->ctypes ) ) {
						$mycred = mycred( $selected_type );
						$result = $mycred->format_number( $value );
					}

				}

			}

			return $result;

		}

	}
endif;
