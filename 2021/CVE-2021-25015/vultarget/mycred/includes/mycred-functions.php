<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * myCRED_Settings class
 * @see http://codex.mycred.me/classes/mycred_settings/
 * @since 0.1
 * @version 1.6
 */
if ( ! class_exists( 'myCRED_Settings' ) ) :
	class myCRED_Settings {

		/**
		 * The default point type key
		 */
		private $default_cred_id    = '';

		/**
		 * The current point type key
		 */
		public $cred_id             = '';

		/**
		 * Indicates if this is the main type or not
		 */
		private $is_main_type       = false;

		/**
		 * The Log database table
		 */
		public $log_table           = '';

		/**
		 * Indicates if we are using multisite
		 */
		public $is_multisite        = false;

		/**
		 * Indicates if the master template feature is in use
		 */
		public $use_master_template = false;

		/**
		 * Indicates if the central logging feature is in use
		 */
		public $use_central_logging = false;

		/**
		 * The point type settings option key
		 */
		private $option_id          = '';

		/**
		 * The point type settings array
		 */
		public $core                = array();

		/**
		 * myCRED Default attachment_id
		 * @since 2.2
 		 * @version 1.0
		 */
		public $attachment_id = false;

		/**
		 * myCRED Default Image Url
		 * @since 2.2
 		 * @version 1.0
		 */
		public $image_url = false;

		/**
		 * Construct
		 * @since 1.0
		 * @version 1.9
		 */
		public function __construct( $point_type = MYCRED_DEFAULT_TYPE_KEY ) {

			// The point type key
			$this->default_cred_id     = MYCRED_DEFAULT_TYPE_KEY;
			$this->cred_id             = ( ( ! is_string( $point_type ) || sanitize_key( $point_type ) == '' || $point_type === NULL ) ? $this->default_cred_id : $point_type );
			$this->is_main_type        = ( ( $this->cred_id == $this->default_cred_id ) ? true : false );

			// Multisite related
			$this->is_multisite        = is_multisite();
			$this->use_master_template = mycred_override_settings();
			$this->use_central_logging = mycred_centralize_log();

			// Log table
			$this->log_table           = $this->get_log_table();

			// Option ID
			$this->option_id           = 'mycred_pref_core';
			if ( ! $this->is_main_type )
				$this->option_id .= '_' . $this->cred_id;

			// The point type settings
			$this->core                = $this->get_point_type_settings();
			if ( $this->core !== false ) {
				foreach ( $this->core as $key => $value ) {
					$this->$key = $value;
				}
			}

			//Point Type Image
			$this->image_url = $this->get_type_image();

			do_action_ref_array( 'mycred_settings', array( &$this ) );

		}

		/**
		 * Default Settings
		 * @since 1.3
		 * @since 2.3 Added `by_roles` in exclude
		 * @version 1.2
		 */
		public function defaults() {

			return array(
				'cred_id'   => MYCRED_DEFAULT_TYPE_KEY,
				'format'      => array(
					'type'        => 'bigint',
					'decimals'    => 0,
					'separators'  => array(
						'decimal'     => '.',
						'thousand'    => ','
					)
				),
				'name'        => array(
					'singular'    => __( 'Point', 'mycred' ),
					'plural'      => __( 'Points', 'mycred' )
				),
				'before'      => '',
				'after'       => '',
				'caps'        => array(
					'plugin'      => 'manage_options',
					'creds'       => 'export'
				),
				'max'         => 0,
				'exclude'     => array(
					'plugin_editors' => 0,
					'cred_editors'   => 0,
					'list'           => '',
					'by_roles'		 => ''
				),
				'frequency'   => array(
					'rate'        => 'always',
					'date'        => ''
				),
				'delete_user' => 0
			);

		}

		/**
		 * Get Point Type Settings
		 * @since 1.8
		 * @version 1.0
		 */
		public function get_point_type_settings() {

			$defaults  = $this->defaults();
			$settings  = mycred_get_option( $this->option_id, $defaults );

			return apply_filters( 'mycred_get_point_type_settings', $settings, $defaults, $this );

		}

		/**
		 * Default Settings
		 * @since 1.8
		 * @version 1.2
		 */
		public function get_log_table() {

			global $wpdb;

			if ( $this->is_multisite && $this->use_central_logging )
				$wp_prefix = $wpdb->base_prefix;
			else
				$wp_prefix = $wpdb->prefix;

			$table_name = wp_cache_get('mycred_log_table_name');
			if( FALSE === $table_name ) {
				$table_name = $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $wp_prefix . 'myCRED_log' ) );
				if( $table_name !== NULL ) {
					wp_cache_set('mycred_log_table_name', $table_name);
				}
			}

			if( $table_name == NULL ) {
				$table_name = $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $wp_prefix . 'mycred_log' ) );
				if( $table_name == NULL ) {
					$table_name = $wp_prefix . 'myCRED_log';
				}
				wp_cache_set('mycred_log_table_name', $table_name);
			}

			if ( defined( 'MYCRED_LOG_TABLE' ) )
				$table_name = MYCRED_LOG_TABLE;

			return $table_name;

		}

		/**
		 * The Point Types Name - Singular form
		 * @since 0.1
		 * @version 1.2
		 */
		public function singular() {

			return $this->name['singular'];

		}

		/**
		 * The Point Types Name - Plural form
		 * @since 0.1
		 * @version 1.2
		 */
		public function plural() {

			return $this->name['plural'];

		}

		/**
		 * Zero
		 * Returns zero formated with or without decimals.
		 * @since 1.3
		 * @version 1.0
		 */
		public function zero() {

			return number_format( 0, $this->format['decimals'] );

		}

		/**
		 * Number
		 * Returns a given creds formated either as a float with the set number of decimals or as a integer.
		 * This function should be used when you need to make sure the variable is returned in correct format
		 * but without any custom layout you might have given your creds.
		 *
		 * @param $number (int|float) the initial number
		 * @returns the given number formated either as an integer or float
		 * @since 0.1
		 * @version 1.2
		 */
		public function number( $value = NULL ) {

			if ( $value === NULL ) return $value;

			$decimals = $this->format['decimals'];
			$value    = str_replace( '+', '', $value );

			if ( $decimals > 0 )
				return (float) number_format( (float) $value, $decimals, '.', '' );

			return (int) $value;

		}

		/**
		 * Format Number
		 * Returns a given creds formated with set decimal and thousands separator and either as a float with
		 * the set number of decimals or as a integer. This function should be used when you want to display creds
		 * formated according to your settings. Do not use this function when adding/removing points!
		 *
		 * @param $number (int|float) the initial number
		 * @returns the given number formated either as an integer or float
		 * @filter 'mycred_format_number'
		 * @since 0.1
		 * @version 1.1
		 */
		public function format_number( $value = NULL ) {

			if ( $value === NULL ) return $value;

			$value    = $this->number( $value );
			$decimals = $this->format['decimals'];
			$sep_dec  = $this->format['separators']['decimal'];
			$sep_tho  = $this->format['separators']['thousand'];

			// Format
			$creds    = number_format( $value, (int) $decimals, $sep_dec, $sep_tho );

			return apply_filters( 'mycred_format_number', $creds, $value, $this->core );

		}

		/**
		 * Format Creds
		 * Returns a given number formated with prefix and/or suffix along with any custom presentation set.
		 *
		 * @param $creds (int|float) number of creds
		 * @param $before (string) optional string to insert before the number
		 * @param $after (string) optional string to isnert after the number
		 * @param $force_in (boolean) option to force $before after prefix and $after before suffix
		 * @filter 'mycred_format_creds'
		 * @returns formated string
		 * @since 0.1
		 * @version 1.1
		 */
		public function format_creds( $value = 0, $before = '', $after = '', $force_in = false ) {

			// Prefix
			$prefix = ( ! empty( $this->before ) ) ? $this->before . ' ' : '';

			// Suffix
			$suffix = ( ! empty( $this->after ) ) ? ' ' . $this->after : '';

			// Layout
			$layout = $before . $prefix . $this->format_number( $value ) . $suffix . $after;
			if ( $force_in )
				$layout = $prefix . $before . $this->format_number( $value ) . $after . $suffix;

			return apply_filters( 'mycred_format_creds', $layout, $value, $this );

		}

		/**
		 * Round Value
		 * Will round a given value either up or down with the option to use precision.
		 *
		 * @param $amount (int|float) required amount to round
		 * @param $up_down (string|boolean) choice of rounding up or down. using false bypasses this function
		 * @param $precision (int) the optional number of decimal digits to round to. defaults to 0
		 * @returns rounded int or float
		 * @since 0.1
		 * @version 1.1
		 */
		public function round_value( $value = NULL, $up_down = false, $precision = 0 ) {

			if ( $value === NULL || ! $up_down ) return $amount;

			// Use round() for precision
			$original_value = $value;
			if ( $precision !== false ) {

				if ( $up_down == 'up' )
					$value = round( $value, (int) $precision, PHP_ROUND_HALF_UP );

				elseif ( $up_down == 'down' )
					$value = round( $value, (int) $precision, PHP_ROUND_HALF_DOWN );

			}

			// Use ceil() or floor() for everything else
			else {

				if ( $up_down == 'up' )
					$value = ceil( $value );

				elseif ( $up_down == 'down' )
					$value = floor( $value );

			}

			return apply_filters( 'mycred_round_value', $value, $original_value, $up_down, $precision );

		}

		/**
		 * Get Lowest Value
		 * Returns the lowest point value available based on the number of decimal places
		 * we use. So with 1 decimal = 0.1, 2 decimals 0.01 etc. Defaults to 1.
		 * @since 1.7
		 * @version 1.1
		 */
		public function get_lowest_value() {

			$lowest   = 1;
			$decimals = $this->format['decimals'] - 1;

			if ( $decimals > 0 ) {

				$lowest = '0.' . str_repeat( '0', $decimals ) . '1';
				$lowest = (float) $lowest;

			}

			return $lowest;

		}

		/**
		 * Apply Exchange Rate
		 * Applies a given exchange rate to the given amount.
		 * 
		 * @param $amount (int|float) the initial amount
		 * @param $rate (int|float) the exchange rate to devide by
		 * @param $round (bool) option to round values, defaults to yes.
		 * @since 0.1
		 * @version 1.3
		 */
		public function apply_exchange_rate( $amount = 0, $rate = 1 ) {

			$value = $amount;
			if ( $rate != 1 ) {

				// Make sure we are not passing decimals without a leading zero
				if ( substr( $rate, 0, 1 ) === '.' )
					$rate = (float) '0' . $rate;

				$value = $amount / $rate;

				$value = $this->number( $value );

			}

			return apply_filters( 'mycred_apply_exchange_rate', $value, $amount, $rate );

		}

		/**
		 * Parse Template Tags
		 * Parses template tags in a given string by checking for the 'ref_type' array key under $log_entry->data.
		 * @since 0.1
		 * @version 1.0
		 */
		public function parse_template_tags( $content = '', $log_entry ) {

			// Prep
			$reference = $log_entry->ref;
			$ref_id    = $log_entry->ref_id;
			$data      = $log_entry->data;

			// Unserialize if serialized
			$data      = maybe_unserialize( $data );

			// Run basic template tags first
			$content   = $this->template_tags_general( $content );

			// Start by allowing others to play
			$content   = apply_filters( 'mycred_parse_log_entry',              $content, $log_entry );
			$content   = apply_filters( "mycred_parse_log_entry_{$reference}", $content, $log_entry );

			// Get the reference type
			if ( isset( $data['ref_type'] ) || isset( $data['post_type'] ) ) {

				if ( isset( $data['ref_type'] ) )
					$type = $data['ref_type'];

				elseif ( isset( $data['post_type'] ) )
					$type = $data['post_type'];

				if ( $type == 'post' )
					$content = $this->template_tags_post( $content, $ref_id, $data );

				elseif ( $type == 'user' )
					$content = $this->template_tags_user( $content, $ref_id, $data );

				elseif ( $type == 'comment' )
					$content = $this->template_tags_comment( $content, $ref_id, $data );

				$content = apply_filters( "mycred_parse_tags_{$type}", $content, $log_entry );

			}

			if( $reference == 'signup_referral' ){

				$content = $this->template_tags_user( $content, $ref_id, $data );

			}

			return $content;

		}

		/**
		 * General Template Tags
		 * Replaces the general template tags in a given string.
		 * @since 0.1
		 * @version 1.2
		 */
		public function template_tags_general( $content = '' ) {

			$content = apply_filters( 'mycred_parse_tags_general', $content );

			// Singular
			$content = str_replace( array( '%singular%', '%Singular%' ), $this->singular(), $content );
			$content = str_replace( '%_singular%',       strtolower( $this->singular() ), $content );

			// Plural
			$content = str_replace(  array( '%plural%', '%Plural%' ), $this->plural(), $content );
			$content = str_replace( '%_plural%',         strtolower( $this->plural() ), $content );

			// Login URL
			$content = str_replace( '%login_url%',       wp_login_url(), $content );
			$content = str_replace( '%login_url_here%',  wp_login_url( mycred_get_permalink() ), $content );

			// Logout URL
			$content = str_replace( '%logout_url%',      wp_logout_url(), $content );
			$content = str_replace( '%logout_url_here%', wp_logout_url( mycred_get_permalink() ), $content );

			// Blog Related
			if ( preg_match( '%(num_members|blog_name|blog_url|blog_info|admin_email)%', $content, $matches ) ) {
				$content = str_replace( '%num_members%',     $this->count_members(), $content );
				$content = str_replace( '%blog_name%',       get_bloginfo( 'name' ), $content );
				$content = str_replace( '%blog_url%',        get_bloginfo( 'url' ), $content );
				$content = str_replace( '%blog_info%',       get_bloginfo( 'description' ), $content );
				$content = str_replace( '%admin_email%',     get_bloginfo( 'admin_email' ), $content );
			}

			return $content;

		}

		/**
		 * Amount Template Tags
		 * Replaces the amount template tags in a given string.
		 * @since 0.1
		 * @version 1.0.3
		 */
		public function template_tags_amount( $content = '', $amount = 0 ) {

			$content = $this->template_tags_general( $content );
			if ( ! $this->has_tags( 'amount', 'cred|cred_f', $content ) ) return $content;

			$content = apply_filters( 'mycred_parse_tags_amount', $content, $amount, $this );
			$content = str_replace( '%cred_f%', $this->format_creds( $amount ), $content );
			$content = str_replace( '%cred%',   $amount, $content );

			return $content;

		}

		/**
		 * Post Related Template Tags
		 * Replaces the post related template tags in a given string.
		 * @param $content (string) string containing the template tags
		 * @param $ref_id (int) required post id as reference id
		 * @param $data (object) Log entry data object
		 * @param $link_target (string) Optional link target to add to any links
		 * @return (string) parsed string
		 * @since 0.1
		 * @version 1.1
		 */
		public function template_tags_post( $content = '', $ref_id = NULL, $data = '', $link_target = '' ) {

			if ( $ref_id === NULL ) return $content;

			$content = $this->template_tags_general( $content );
			if ( ! $this->has_tags( 'post', 'post_title|post_url|link_with_title|post_type', $content ) ) return $content;

			if ( $link_target != '' )
				$link_target = ' target="' . esc_attr( $link_target ) . '"';

			// Get Post Object
			$post     = mycred_get_post( $ref_id );
			$post_url = '#item-has-been-deleted';

			// Post does not exist - see if we can re-construct
			if ( !isset( $post->ID ) ) {

				// Nope, no backup, bye
				if ( ! is_array( $data ) || ! array_key_exists( 'ID', $data ) ) return $content;

				// Re-construct
				$post = new StdClass();
				foreach ( $data as $key => $value ) {

					if ( $key == 'post_title' )
						$value .= ' (' . __( 'Deleted', 'mycred' ) . ')';

					$post->$key = $value;

				}

			}
			else {

				$post_url = mycred_get_permalink( $post );

			}

			// Let others play first
			$content = apply_filters( 'mycred_parse_tags_post', $content, $post, $data );

			// Replace template tags
			$content = str_replace( '%post_title%',      esc_attr( mycred_get_the_title( $post ) ), $content );
			$content = str_replace( '%post_url%',        esc_url_raw( $post_url ), $content );
			$content = str_replace( '%link_with_title%', '<a href="' . esc_url_raw( $post_url ) . '"' . $link_target . '>' . esc_attr( $post->post_title ) . '</a>', $content );

			// Post type template tag
			$post_type = get_post_type_object( $post->post_type );
			if ( $post_type !== NULL )
				$content = str_replace( '%post_type%', $post_type->labels->singular_name, $content );

			return $content;

		}

		/**
		 * User Related Template Tags
		 * Replaces the user related template tags in the given string.
		 * @param $content (string) string containing the template tags
		 * @param $ref_id (int) required user id as reference id
		 * @param $data (object) Log entry data object
		 * @param $link_target (string) Optional link target to add to any links
		 * @return (string) parsed string
		 * @since 0.1
		 * @version 1.4
		 */
		public function template_tags_user( $content = '', $ref_id = NULL, $data = '', $link_target = '' ) {

			if ( $ref_id === NULL ) return $content;

			$content = $this->template_tags_general( $content );
			if ( ! $this->has_tags( 'user', 'user_id|user_name|user_name_en|display_name|user_profile_url|user_profile_link|user_nicename|user_email|user_url|balance|balance_f', $content ) ) return $content;

			// Get User Object
			if ( $ref_id !== false )
				$user = get_userdata( $ref_id );

			// User object is passed on though $data
			elseif ( $ref_id === false && is_object( $data ) && isset( $data->ID ) )
				$user = $data;

			// User array is passed on though $data
			elseif ( $ref_id === false && is_array( $data ) || array_key_exists( 'ID', (array) $data ) ) {

				$user = new StdClass();
				foreach ( $data as $key => $value ) {

					if ( $key == 'login' )
						$user->user_login = $value;

					else
						$user->$key = $value;

				}

			}

			else return $content;

			// Let others play first
			$content     = apply_filters( 'mycred_parse_tags_user', $content, $user, $data );

			if ( ! isset( $user->ID ) ) return $content;

			// Replace template tags
			$content     = str_replace( '%user_id%',            $user->ID, $content );
			$content     = str_replace( '%user_name%',          esc_attr( $user->user_login ), $content );
			$content     = str_replace( '%user_name_en%',       urlencode( $user->user_login ), $content );

			$profile_url = mycred_get_users_profile_url( $user->ID );

			if ( $link_target != '' )
				$link_target = ' target="' . esc_attr( $link_target ) . '"';

			$content     = str_replace( '%display_name%',       esc_attr( $user->display_name ), $content );
			$content     = str_replace( '%user_profile_url%',   esc_url_raw( $profile_url ), $content );
			$content     = str_replace( '%user_profile_link%',  '<a href="' . esc_url_raw( $profile_url ) . '"' . $link_target . '>' . esc_attr( $user->display_name ) . '</a>', $content );

			$content     = str_replace( '%user_nicename%',      ( isset( $user->user_nicename ) ) ? esc_attr( $user->user_nicename ) : '', $content );
			$content     = str_replace( '%user_email%',         ( isset( $user->user_email ) ) ? esc_attr( $user->user_email ) : '', $content );
			$content     = str_replace( '%user_url%',           ( isset( $user->user_url ) ) ? esc_url_raw( $user->user_url ) : '', $content );

			// Balance Related
			$balance     = $this->get_users_balance( $user->ID );

			$content     = str_replace( '%balance%',            $balance, $content );
			$content     = str_replace( '%balance_f%',          $this->format_creds( $balance ), $content );

			return $content;

		}

		/**
		 * Comment Related Template Tags
		 * Replaces the comment related template tags in a given string.
		 * @param $content (string) string containing the template tags
		 * @param $ref_id (int) required comment id as reference id
		 * @param $data (object) Log entry data object
		 * @param $link_target (string) Optional link target to add to any links
		 * @return (string) parsed string
		 * @since 0.1
		 * @version 1.1
		 */
		public function template_tags_comment( $content = '', $ref_id = NULL, $data = '', $link_target = '' ) {

			if ( $ref_id === NULL ) return $content;

			$content = $this->template_tags_general( $content );
			if ( ! $this->has_tags( 'comment', 'comment_id|c_post_id|c_post_title|c_post_url|c_link_with_title', $content ) ) return $content;

			// Get Comment Object
			$comment            = get_comment( $ref_id );
			$comment_url        = '#item-has-been-deleted';
			$comment_post_title = __( 'Deleted Item', 'mycred' );

			// Comment does not exist - see if we can re-construct
			if ( $comment === NULL ) {

				// Nope, no backup, bye
				if ( ! is_array( $data ) || ! array_key_exists( 'comment_ID', $data ) ) return $content;

				// Re-construct
				$comment = new StdClass();
				foreach ( $data as $key => $value ) {
					$comment->$key = $value;
				}

			}
			else {

				$comment_post       = mycred_get_post( $comment->comment_post_ID );
				$comment_url        = mycred_get_permalink( $comment_post );
				$comment_post_title = mycred_get_permalink( $comment_post );

			}

			// Let others play first
			$content = apply_filters( 'mycred_parse_tags_comment', $content, $comment, $data );

			if ( $link_target != '' )
				$link_target = ' target="' . esc_attr( $link_target ) . '"';

			$content = str_replace( '%comment_id%',        $comment->comment_ID, $content );

			$content = str_replace( '%c_post_id%',         $comment->comment_post_ID, $content );
			$content = str_replace( '%c_post_title%',      esc_attr( $comment_post_title ), $content );

			$content = str_replace( '%c_post_url%',        esc_url_raw( $comment_url ), $content );
			$content = str_replace( '%c_link_with_title%', '<a href="' . esc_url_raw( $comment_url ) . '">' . esc_attr( $comment_post_title ) . '</a>', $content );

			return $content;

		}

		/**
		 * Has Tags
		 * Checks if a string has any of the defined template tags.
		 * @param $type (string) template tag type
		 * @param $tags (string) tags to search for, list with |
		 * @param $content (string) content to search
		 * @filter 'mycred_has_tags'
		 * @filter 'mycred_has_tags_{$type}'
		 * @returns (boolean) true or false
		 * @since 1.2.2
		 * @version 1.1
		 */
		public function has_tags( $type = '', $tags = '', $content = '' ) {

			$tags = apply_filters( 'mycred_has_tags', $tags, $content );
			$tags = apply_filters( 'mycred_has_tags_' . $type, $tags, $content );

			if ( $tags == '' || ! preg_match( '%(' . trim( $tags ) . ')%', $content, $matches ) ) return false;

			return true;

		}

		/**
		 * Available Template Tags
		 * Based on an array of template tag types, a list of codex links
		 * are generted for each tag type.
		 * @since 1.4
		 * @version 1.0.1
		 */
		public function available_template_tags( $available = array(), $custom = '' ) {

			// Prep
			$links = $template_tags = array();

			// General
			if ( in_array( 'general', $available ) )
				$template_tags[] = array(
					'title' => __( 'General', 'mycred' ),
					'url'   => 'http://codex.mycred.me/category/template-tags/temp-general/'
				);

			// User
			if ( in_array( 'user', $available ) )
				$template_tags[] = array(
					'title' => __( 'User Related', 'mycred' ),
					'url'   => 'http://codex.mycred.me/category/template-tags/temp-user/'
				);

			// Post
			if ( in_array( 'post', $available ) )
				$template_tags[] = array(
					'title' => __( 'Post Related', 'mycred' ),
					'url'   => 'http://codex.mycred.me/category/template-tags/temp-post/'
				);

			// Comment
			if ( in_array( 'comment', $available ) )
				$template_tags[] = array(
					'title' => __( 'Comment Related', 'mycred' ),
					'url'   => 'http://codex.mycred.me/category/template-tags/temp-comment/'
				);

			// Widget
			if ( in_array( 'widget', $available ) )
				$template_tags[] = array(
					'title' => __( 'Widget Related', 'mycred' ),
					'url'   => 'http://codex.mycred.me/category/template-tags/temp-widget/'
				);

			// Amount
			if ( in_array( 'amount', $available ) )
				$template_tags[] = array(
					'title' => __( 'Amount Related', 'mycred' ),
					'url'   => 'http://codex.mycred.me/category/template-tags/temp-amount/'
				);

			// Video
			if ( in_array( 'video', $available ) )
				$template_tags[] = array(
					'title' => __( 'Video Related', 'mycred' ),
					'url'   => 'http://codex.mycred.me/category/template-tags/temp-video/'
				);

			if ( ! empty( $template_tags ) ) {
				foreach ( $template_tags as $tag ) {
					$links[] = '<a href="' . $tag['url'] . '" target="_blank">' . $tag['title'] . '</a>';
				}
			}

			if ( ! empty( $custom ) )
				$custom = ' ' . __( 'and', 'mycred' ) . ': ' . $custom;

			return __( 'Available Template Tags:', 'mycred' ) . ' ' . implode( ', ', $links ) . $custom . '.';

		}

		/**
		 * Allowed Tags
		 * Strips HTML tags from a given string.
		 *
		 * @param $data (string) to strip tags off
		 * @param $allow (string) allows you to overwrite the default filter with a custom set of tags to strip
		 * @filter 'mycred_allowed_tags'
		 * @returns (string) string stripped of tags
		 * @since 0.1
		 * @version 1.1
		 */
		public function allowed_tags( $data = '', $allow = '' ) {

			if ( $allow === false )
				return strip_tags( $data );

			elseif ( ! empty( $allow ) )
				return strip_tags( $data, $allow );

			return strip_tags( $data, apply_filters( 'mycred_allowed_tags', '<a><br><em><strong><span>' ) );

		}

		/**
		 * Allowed HTML Tags
		 * Used for settings that support HTML. These settings are
		 * sanitized using wp_kses() where these tags are used.
		 * @since 1.6
		 * @version 1.0.1
		 */
		public function allowed_html_tags() {

			return apply_filters( 'mycred_allowed_html_tags', array(
				'a'    => array( 'href' => array(), 'title' => array(), 'target' => array() ),
				'abbr' => array( 'title' => array() ), 'acronym' => array( 'title' => array() ),
				'code' => array(), 'pre' => array(), 'em' => array(), 'strong' => array(),
				'div'  => array( 'class' => array(), 'id' => array() ), 'span' => array( 'class' => array() ),
				'p'    => array(), 'ul' => array(), 'ol' => array(), 'li' => array(),
				'h1'   => array(), 'h2' => array(), 'h3' => array(), 'h4' => array(), 'h5' => array(), 'h6' => array(),
				'img'  => array( 'src' => array(), 'class' => array(), 'alt' => array() ),
				'br'   => array( 'class' => array() )
			), $this );

		}

		/**
		 * Get Point Admin Capability
		 * Returns the set WordPress capability that defines who is considered a "Point Administrator".
		 * @returns capability (string)
		 * @since 1.8
		 * @version 1.0
		 */
		public function get_point_admin_capability() {

			// Need to have something or we are in deep trouble
			if ( ! isset( $this->caps['plugin'] ) || empty( $this->caps['plugin'] ) )
				$this->caps['plugin'] = 'edit_theme_options';

			// Try to prevent "lockouts" on Multisites where the delete_user cap is not available admins.
			// Try instead using "export" which should also be available for admins.
			if ( $this->is_multisite && $this->caps['plugin'] == 'delete_user' )
				$this->caps['plugin'] = 'edit_theme_options';

			// backwards cap.
			$capability = apply_filters( 'mycred_edit_plugin_cap', $this->caps['plugin'] );

			return apply_filters( 'get_point_admin_capability', $capability, $this );

		}
		// Backwards comp
		public function edit_creds_cap() {

			_deprecated_function( __FUNCTION__, 'get_point_admin_capability', '1.8' );

			return $this->get_point_admin_capability();

		}

		/**
		 * Is Point Administrator
		 * Check if a given user or the current user is a Point Administrator.
		 * @param $user_id (int) user id
		 * @returns true or false
		 * @since 1.8
		 * @version 1.0
		 */
		public function user_is_point_admin( $user_id = NULL ) {

			$result = false;

			if ( ! did_action( 'init' ) ) {
				_doing_it_wrong( __FUNCTION__, 'Capability should not be checked before wp init', '1.8' );
				return $result;
			}

			// Grab current user id
			if ( $user_id === NULL )
				$user_id = get_current_user_id();

			// Check if user can
			if ( user_can( $user_id, $this->get_point_admin_capability() ) )
				$result = true;

			return $result;

		}
		// Backwards comp
		public function can_edit_creds( $user_id = NULL ) {

			_deprecated_function( __FUNCTION__, 'user_is_point_admin', '1.8' );

			return $this->user_is_point_admin( $user_id );

		}

		/**
		 * Get Point Editor Capability
		 * Returns the set WordPress capability that defines who is considered a "Point Editor".
		 * @returns capability (string)
		 * @since 1.8
		 * @version 1.0
		 */
		public function get_point_editor_capability() {

			if ( ! isset( $this->caps['creds'] ) || empty( $this->caps['creds'] ) )
				$this->caps['creds'] = 'manage_options';

			$capability = apply_filters( 'mycred_edit_creds_cap', $this->caps['creds'] );

			return apply_filters( 'get_point_editor_capability', $capability, $this );

		}
		// Backwards comp
		public function edit_plugin_cap() {

			_deprecated_function( __FUNCTION__, 'get_point_editor_capability', '1.8' );

			return $this->get_point_admin_capability();

		}

		/**
		 * Is Point Editor
		 * Check if a given user or the current user is a Point Editor.
		 * @param $user_id (int) user id
		 * @returns true or false
		 * @since 1.8
		 * @version 1.0
		 */
		public function user_is_point_editor( $user_id = NULL ) {

			$result = false;

			if ( ! did_action( 'init' ) ) {
				_doing_it_wrong( __FUNCTION__, 'Capability should not be checked before wp init', '1.8' );
				return $result;
			}

			// Grab current user id
			if ( $user_id === NULL )
				$user_id = get_current_user_id();

			// Check if user can
			if ( user_can( $user_id, $this->get_point_editor_capability() ) )
				$result = true;

			return $result;

		}
		// Backwards comp
		public function can_edit_plugin( $user_id = '' ) {

			_deprecated_function( __FUNCTION__, 'user_is_point_editor', '1.8' );

			return $this->user_is_point_editor( $user_id );

		}

		/**
		 * Check if user id is in exclude list
		 * @return true or false
		 * @since 0.1
		 * @since 2.3 Added to check is user is excluded by role
		 * @version 1.2
		 */
		public function in_exclude_list( $user_id = '' ) {

			$result = false;

			// Grab current user id
			if ( empty( $user_id ) )
				$user_id = get_current_user_id();

			if ( ! isset( $this->exclude['list'] ) )
				$this->exclude['list'] = '';

			$list = wp_parse_id_list( $this->exclude['list'] );
			if ( in_array( $user_id, $list ) )
				$result = true;

			//Check if Excluded by Role
			if( !$result && !empty( $this->exclude['by_roles'] ) )
			{
				$roles = explode( ',', $this->exclude['by_roles'] );

				$user = get_user_by(  'id', $user_id );

				$user_roles = $user->roles;

				foreach( $roles as $role )
				{
					if( in_array( $role, $user_roles ) )
					{
						$result = true;
						break;
					}
				}

			}

			return apply_filters( 'mycred_is_excluded_list', $result, $user_id );

		}

		/**
		 * Exclude Point Administrators?
		 * @return true or false
		 * @since 1.8
		 * @version 1.0
		 */
		public function exclude_point_admins() {

			return (bool) $this->exclude['plugin_editors'];

		}
		// Backwards comp
		public function exclude_plugin_editors() {

			_deprecated_function( __FUNCTION__, 'exclude_point_admins', '1.8' );

			return $this->exclude_point_admins();

		}

		/**
		 * Exclude Point Editors?
		 * @return true or false
		 * @since 1.8
		 * @version 1.0
		 */
		public function exclude_point_editors() {

			return (bool) $this->exclude['cred_editors'];

		}
		// Backwards comp
		public function exclude_creds_editors() {

			_deprecated_function( __FUNCTION__, 'exclude_point_editors', '1.8' );

			return $this->exclude_point_editors();

		}

		/**
		 * Exclude User
		 * Checks is a given user or the current user is excluded from using this point type.
		 * @param $user_id (int), the users numeric ID
		 * @returns boolean true on user should be excluded else false
		 * @since 0.1
		 * @version 1.1
		 */
		public function exclude_user( $user_id = NULL ) {

			if ( $user_id === NULL )
				$user_id = get_current_user_id();

			// Quick override
			if ( apply_filters( 'mycred_exclude_user', false, $user_id, $this ) === true ) return true;

			// In case we auto exclude point administrators
			if ( $this->exclude_point_admins() && $this->user_is_point_admin( $user_id ) ) return true;

			// In case we auto exclude point editors
			if ( $this->exclude_point_editors() && $this->user_is_point_editor( $user_id ) ) return true;

			// In case our ID is in our exclude list of ids
			if ( $this->in_exclude_list( $user_id ) ) return true;

			return false;

		}

		/**
		 * Count Members
		 * @since 1.1
		 * @version 1.1
		 */
		public function count_members() {

			global $wpdb;

			$count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT( DISTINCT user_id ) FROM {$wpdb->usermeta} WHERE meta_key = %s;", mycred_get_meta_key( $this->cred_id ) ) );
			if ( $count === NULL ) $count = 0;

			return $count;

		}

		/**
		 * Get Point Type Key
		 * Returns the default cred id.
		 * @since 1.8
		 * @version 1.0
		 */
		public function get_point_type_key() {

			return $this->cred_id;

		}
		// Backwards comp
		public function get_cred_id() {

			_deprecated_function( __FUNCTION__, 'get_point_type_key', '1.8' );

			return $this->get_point_type_key();

		}

		/**
		 * Get Max
		 * @since 1.3
		 * @version 1.0
		 */
		public function max() {

			if ( ! isset( $this->max ) )
				$this->max = 0;

			return $this->max;

		}

		/**
		 * Enforce Max
		 * @since 1.8
		 * @version 1.0
		 */
		public function enforce_max( $user_id = NULL, $amount = NULL ) {

			$maxium          = $this->max();
			if ( $amount == 0 || $maxium == 0 ) return $amount;

			$original_amount = $amount;

			// Enforce max adjustments
			if ( ( $maxium > $this->zero() && $amount > $maxium ) || ( $maxium < $this->zero() && $amount < $maxium ) ) {

				$amount = $this->number( $maxium );

				do_action( 'mycred_max_enforced', $user_id, $original_amount, $maxium );

			}

			return $amount;

		}

		/**
		 * Get users balance
		 * Returns a given users current balance raw.
		 * @param $user_id (int), required user id
		 * @param $type (string), optional cred type to check for
		 * @returns zero if user id is not set or if no creds were found, else returns amount
		 * @since 0.1
		 * @version 1.5
		 */
		public function get_users_balance( $user_id = NULL, $point_type = NULL ) {

			if ( $user_id === NULL ) return $this->zero();

			global $mycred_types, $mycred_current_account;

			// Point type
			if ( $point_type === NULL || ! array_key_exists( $point_type, $mycred_types ) )
				$point_type = $this->get_point_type_key();

			if ( mycred_is_current_account( $user_id ) && ! empty( $mycred_current_account->balance[ $point_type ] ) )
				$balance = $mycred_current_account->balance[ $point_type ]->get( 'current' );

			else
				$balance = mycred_get_user_meta( $user_id, $point_type, '', true );

			if ( $balance == '' ) $balance = $this->zero();

			return $this->number( apply_filters( 'mycred_get_users_cred', $balance, $this, $user_id, $point_type ) );

		}
		// Backwards comp
		public function get_users_cred( $user_id = NULL, $type = NULL ) {

			_deprecated_function( __FUNCTION__, 'get_users_balance', '1.8' );

			return $this->get_users_balance( $user_id, $type );

		}

		/**
		 * Get users total balance
		 * Returns a given users total balance raw.
		 * @param $user_id (int), required user id
		 * @param $type (string), optional cred type to check for
		 * @returns zero if user id is not set or if no creds were found, else returns amount
		 * @since 1.8
		 * @version 1.0
		 */
		public function get_users_total_balance( $user_id = NULL, $point_type = NULL ) {

			$total_balance = $this->zero();

			$user_id       = absint( $user_id );
			if ( $user_id === 0 ) return $total_balance;

			// Feature needs to be enabled
			if ( MYCRED_ENABLE_TOTAL_BALANCE ) {

				global $mycred_types, $mycred_current_account;

				// Point type
				if ( $point_type === NULL || ! array_key_exists( $point_type, $mycred_types ) )
					$point_type = $this->get_point_type_key();

				if ( mycred_is_current_account( $user_id ) && $mycred_current_account->balance[ $point_type ] !== false )
					$total_balance = $mycred_current_account->balance[ $point_type ]->get( 'accumulated' );

				else
					$total_balance = mycred_get_user_meta( $user_id, $point_type, '_total', true );

				if ( $total_balance == '' ) {

					$total_balance = mycred_query_users_total( $user_id, $point_type );

					mycred_update_user_meta( $user_id, $point_type, '_total', $total_balance );

				}

			}

			return $this->number( apply_filters( 'mycred_get_users_total_cred', $total_balance, $this, $user_id, $point_type ) );

		}

		/**
		 * Update users balance
		 * Used to adjust a users balance for a given point type. Returns the new balance.
		 * @param $user_id (int), required user id
		 * @param $amount (int|float), amount to add/deduct from users balance. This value must be pre-formated.
		 * @param $type (string), optional point type key to adjust instead of the current one.
		 * @returns the new balance.
		 * @since 0.1
		 * @version 1.5
		 */
		public function update_users_balance( $user_id = NULL, $amount = NULL, $point_type = NULL ) {

			// Minimum Requirements: User id and amount can not be null
			if ( $user_id === NULL || $amount === NULL || $amount == $this->zero() ) return $amount;

			global $mycred_types, $mycred_current_account;

			// Point type
			if ( $point_type === NULL || ! array_key_exists( $point_type, $mycred_types ) )
				$point_type = $this->get_point_type_key();

			// Prep amount
			$amount          = $this->number( $amount );
			$amount          = $this->enforce_max( $user_id, $amount );

			// Adjust balance
			$current_balance = $this->get_users_balance( $user_id, $point_type );
			$new_balance     = $this->number( $current_balance + $amount );

			// Save new balance
			mycred_update_user_meta( $user_id, $point_type, '', $new_balance );

			// Update the current account object
			if ( mycred_is_current_account( $user_id ) && $mycred_current_account->balance[ $point_type ] !== false )
				$mycred_current_account->balance[ $point_type ]->set( 'current', $new_balance );

			// Let others play
			do_action( 'mycred_update_user_balance', $user_id, $current_balance, $amount, $point_type );

			// Return the new balance
			return $new_balance;

		}

		/**
		 * Update users total balance
		 * Updates a given users total balance with the option to add an adjustment directly.
		 * @param $user_id (int), required user id
		 * @param $amount (int|float), required amount to add to the total
		 * @param $type (string), optional cred type to check for
		 * @returns zero if user id is not set or if no creds were found, else returns amount
		 * @since 1.8
		 * @version 1.0
		 */
		public function update_users_total_balance( $user_id = NULL, $amount = 0, $point_type = NULL ) {

			if ( ! MYCRED_ENABLE_TOTAL_BALANCE || ! MYCRED_ENABLE_LOGGING || $amount == 0 ) return $amount;

			global $mycred_current_account;

			if ( mycred_is_current_account( $user_id ) && $mycred_current_account->balance[ $point_type ] !== false )
				$total_balance = $mycred_current_account->balance[ $point_type ]->get( 'accumulated' );

			else {

				$total_balance = mycred_get_user_meta( $user_id, $point_type, '_total', true );
				$total_balance = $this->number( $total_balance );

			}

			$total_balance += $amount;

			mycred_update_user_meta( $user_id, $point_type, '_total', $total_balance );

			if ( mycred_is_current_account( $user_id ) && $mycred_current_account->balance[ $point_type ] !== false )
				$mycred_current_account->balance[ $point_type ]->set( 'accumulated', $total_balance );

			do_action( 'mycred_update_user_total_balance', $total_balance, $user_id, $point_type, $this );

			return $total_balance;

		}

		/**
		 * Set users balance
		 * Changes a users balance to the amount given.
		 * @param $user_id (int), required user id
		 * @param $new_balance (int|float), amount to add/deduct from users balance. This value must be pre-formated.
		 * @returns (bool) true on success or false on fail.
		 * @since 2.3 `$results` and do_action `mycred_finish_without_log_entry` added, to update users' rank when updating from profile page manually.
		 * @since 1.7.3
		 * @version 1.2
		 */
		public function set_users_balance( $user_id = NULL, $new_balance = NULL ) {

			// Minimum Requirements: User id and amount can not be null
			if ( $user_id === NULL || $new_balance === NULL ) return false;
			
			global $mycred_current_account;

			$point_type  = $this->get_point_type_key();
			$new_balance = $this->number( $new_balance );

			// Update balance
			mycred_update_user_meta( $user_id, $point_type, '', $new_balance );

			if ( mycred_is_current_account( $user_id ) && $mycred_current_account->balance[ $point_type ] !== false )
				$mycred_current_account->balance[ $point_type ]->set( 'current', $new_balance );

			// Clear caches
			mycred_delete_option( 'mycred-cache-total-' . $point_type );

			$result = array(
				'current'	=>	$new_balance,
				'user_id' 	=>	$user_id,
				'reference' =>	'manual',
				'type' 		=>	$point_type
			);

			do_action( 'mycred_finish_without_log_entry', $result );

			// Let others play
			do_action( 'mycred_set_user_balance', $user_id, $new_balance, $this );

			return true;

		}

		/**
		 * Set users total balance
		 * Changes a users total balance to the amount given.
		 * @param $user_id (int), required user id
		 * @param $new_balance (int|float), amount to add/deduct from users balance. This value must be pre-formated.
		 * @returns (bool) true on success or false on fail.
		 * @since 1.8
		 * @version 1.0
		 */
		public function set_users_total_balance( $user_id = NULL, $new_balance = NULL ) {

			// Minimum Requirements: User id and amount can not be null
			if ( $user_id === NULL || $new_balance === NULL || ! MYCRED_ENABLE_TOTAL_BALANCE || ! MYCRED_ENABLE_LOGGING ) return false;

			global $mycred_current_account;

			$total_balance = $this->number( $new_balance );

			// Update balance
			mycred_update_user_meta( $user_id, $this->get_point_type_key(), '_total', $total_balance );

			if ( mycred_is_current_account( $user_id ) && $mycred_current_account->balance[ $point_type ] !== false )
				$mycred_current_account->balance[ $point_type ]->set( 'accumulated', $total_balance );

			// Let others play
			do_action( 'mycred_set_user_total_balance', $user_id, $total_balance, $this );

			return true;

		}

		/**
		 * Add Creds
		 * Adds creds to a given user. A refernece ID, user id and number of creds must be given.
		 * Important! This function will not check if the user should be excluded from gaining points, this must
		 * be done before calling this function!
		 * @param $ref (string), required reference id
		 * @param $user_id (int), required id of the user who will get these points
		 * @param $cred (int|float), required number of creds to give or deduct from the given user.
		 * @param $ref_id (int), optional array of reference IDs allowing the use of content specific keywords in the log entry
		 * @param $data (object|array|string|int), optional extra data to save in the log. Note that arrays gets serialized!
		 * @param $type (string), optional point name, defaults to MYCRED_DEFAULT_TYPE_KEY
		 * @returns boolean true on success or false on fail
		 * @since 0.1
		 * @version 1.7
		 */
		public function add_creds( $ref = '', $user_id = '', $amount = '', $entry = '', $ref_id = '', $data = '', $type = NULL ) {

			// Minimum Requirements: Reference not empty, User ID not empty and Amount is not empty
			if ( empty( $ref ) || empty( $user_id ) || empty( $amount ) ) return false;

			// Check exclusion
			if ( $this->exclude_user( $user_id ) ) return false;

			// Prep amount
			$amount             = $this->number( $amount );
			$amount             = $this->enforce_max( $user_id, $amount );
			if ( $amount == $this->zero() || $amount == 0 ) return false;

			global $mycred_types;

			// Point type
			if ( $type === NULL || ! array_key_exists( $type, $mycred_types ) )
				$type = $this->get_point_type_key();

			// Execution Override
			// Allows us to stop an execution. 
			// excepts a boolean reply
			$execute            = apply_filters( 'mycred_add', true, compact( 'ref', 'user_id', 'amount', 'entry', 'ref_id', 'data', 'type' ), $this );

			// Acceptable answers:
			// true (boolean)
			if ( $execute === true ) {

				// Allow the adjustment of the values before we run them
				$run_this   = apply_filters( 'mycred_run_this', compact( 'ref', 'user_id', 'amount', 'entry', 'ref_id', 'data', 'type' ), $this );

				// Add to log
				$this->add_to_log(
					$run_this['ref'],
					$run_this['user_id'],
					$run_this['amount'],
					$run_this['entry'],
					$run_this['ref_id'],
					$run_this['data'],
					$run_this['type']
				);

				// Update balance
				$this->update_users_balance( (int) $run_this['user_id'], $run_this['amount'], $run_this['type'] );

				// Update total balance (if enabled)
				if ( MYCRED_ENABLE_TOTAL_BALANCE && MYCRED_ENABLE_LOGGING && ( $run_this['amount'] > 0 || ( $run_this['amount'] < 0 && $run_this['ref'] == 'manual' ) ) ) {

					$is_update_total_balance = apply_filters( 'mycred_update_total_balance', true, $run_this );

					if ( $is_update_total_balance ) {
						$this->update_users_total_balance( (int) $run_this['user_id'], $run_this['amount'], $run_this['type'] );
					}

				}

			}

			// false (boolean)
			else { $run_this = false; }

			// For all features that need to run once we have done or not done something
			return apply_filters( 'mycred_add_finished', $execute, $run_this, $this );

		}

		/**
		 * Add Log Entry
		 * Adds a new entry into the log. A reference id, user id and number of credits must be set.
		 * @param $ref (string), required reference id
		 * @param $user_id (int), required id of the user who will get these points
		 * @param $cred (int|float), required number of creds to give or deduct from the given user.
		 * @param $ref_id (array), optional array of reference IDs allowing the use of content specific keywords in the log entry
		 * @param $data (object|array|string|int), optional extra data to save in the log. Note that arrays gets serialized!
		 * @returns false if requirements are not set or db insert id if successful.
		 * @since 0.1
		 * @version 1.5
		 */
		public function add_to_log( $ref = '', $user_id = '', $amount = '', $entry = '', $ref_id = '', $data = '', $type = NULL ) {

			// Minimum Requirements: Reference not empty, User ID not empty and Amount is not empty
			if ( empty( $ref ) || empty( $user_id ) || empty( $amount ) || empty( $entry ) ) return false;

			// Prep amount
			$amount    = $this->number( $amount );
			$amount    = $this->enforce_max( $user_id, $amount );
			if ( $amount === $this->zero() || $amount == 0 ) return false;

			$insert_id = 0;

			mycred_update_users_history( $user_id, $type, $ref, $ref_id, $amount );

			// Option to disable logging
			if ( MYCRED_ENABLE_LOGGING ) {

				global $wpdb, $mycred_types;

				// Strip HTML from log entry
				$entry = $this->allowed_tags( $entry );

				// Point type
				if ( $type === NULL || ! array_key_exists( $type, $mycred_types ) )
					$type = $this->get_point_type_key();

				$time   = apply_filters( 'mycred_log_time', current_time( 'timestamp' ), $ref, $user_id, $amount, $entry, $ref_id, $data, $type );
				$insert = array(
					'ref'     => $ref,
					'ref_id'  => $ref_id,
					'user_id' => (int) $user_id,
					'creds'   => $amount,
					'ctype'   => $type,
					'time'    => $time,
					'entry'   => $entry,
					'data'    => ( is_array( $data ) || is_object( $data ) ) ? serialize( $data ) : $data
				);

				// Insert into DB
				$wpdb->insert(
					$this->log_table,
					$insert,
					array( '%s', '%d', '%d', '%s', '%s', '%d', '%s', ( is_numeric( $data ) ) ? '%d' : '%s' )
				);

				$insert_id = $wpdb->insert_id;

				wp_cache_delete( 'mycred_references' . $type, MYCRED_SLUG );

				delete_transient( 'mycred_log_entries' );

			}

			return apply_filters( 'mycred_new_log_entry_id', $insert_id, $insert, $this );

		}

		/**
		 * Update Log Entry
		 * Updates an existing log entry.
		 * @param $entry_id (id), required log entry id
		 * @param $data (array), required column data to update
		 * @param $prep (array), required column prep
		 * @returns false if requirements are not met or true
		 * @since 1.6.7
		 * @version 1.1
		 */
		public function update_log_entry( $entry_id = NULL, $data = array(), $prep = array() ) {

			if ( $entry_id === NULL || empty( $data ) || empty( $prep ) ) return false;

			// If logging is disabled, pretend we did the job
			if ( ! MYCRED_ENABLE_LOGGING ) return true;

			global $wpdb;

			$wpdb->update(
				$this->log_table,
				$data,
				array( 'id' => $entry_id ),
				$prep,
				array( '%d' )
			);

			do_action( 'mycred_log_entry_updated', $entry_id, $data );

			return true;

		}

		/**
		 * Has Entry
		 * Checks to see if a given action with reference ID and user ID exists in the log database.
		 * @param $reference (string) required reference ID
		 * @param $ref_id (int) optional reference id
		 * @param $user_id (int) optional user id
		 * @param $data (array|string) option data to search
		 * @since 0.1
		 * @version 1.4
		 */
		function has_entry( $reference = NULL, $ref_id = NULL, $user_id = NULL, $data = NULL, $type = NULL ) {

			$has_entry = false;
			if ( ! MYCRED_ENABLE_LOGGING ) return $has_entry;

			global $mycred_current_account;

			if ( $user_id !== NULL && mycred_is_current_account( $user_id ) && ! empty( $mycred_current_account->point_type ) && in_array( $type, $mycred_current_account->point_type ) ) {

				if ( isset( $mycred_current_account->balance[ $type ]->history ) && ! empty( $mycred_current_account->balance[ $type ]->history->data ) ) {

					$data = $mycred_current_account->balance[ $type ]->history->data;
					if ( array_key_exists( $reference, $data ) && ! empty( $data[ $reference ]->reference_ids ) && in_array( $ref_id, $data[ $reference ]->reference_ids ) ) {

						$has_entry = true;

					}

				}

			}

			if ( ! $has_entry ) {

				global $wpdb;

				$wheres   = array();

				if ( $reference !== NULL )
					$wheres[] = $wpdb->prepare( "ref = %s", $reference );

				if ( $ref_id !== NULL )
					$wheres[] = $wpdb->prepare( "ref_id = %d", $ref_id );

				if ( $user_id !== NULL )
					$wheres[] = $wpdb->prepare( "user_id = %d", $user_id );

				if ( $data !== NULL )
					$wheres[] = $wpdb->prepare( "data = %s", maybe_serialize( $data ) );

				if ( $type === NULL ) $type = $this->get_point_type_key();
				$wheres[] = $wpdb->prepare( "ctype = %s", $type );

				$where    = implode( ' AND ', $wheres );

				if ( ! empty( $wheres ) ) {

					$check = $wpdb->get_var( "SELECT id FROM {$this->log_table} WHERE {$where};" );
					if ( $check !== NULL )
						$has_entry = true;

				}

			}

			return apply_filters( 'mycred_has_entry', $has_entry, $reference, $ref_id, $user_id, $data, $type );

		}

		/**
		 * Gets point type image
		 * @since 2.2
 		 * @version 1.0
		 */
		public function get_type_image()
		{
			$attachment_url = false;

			if( $this->attachment_id )
				$attachment_url = wp_get_attachment_url( $this->attachment_id );
			else
				$attachment_url = wp_get_attachment_url( mycred_get_default_point_image_id() );

			if( $attachment_url )
				return $attachment_url;

			return false;
		}

	}
endif;

/**
 * myCRED Label
 * Returns the myCRED Label
 * @since 1.3.3
 * @version 1.1
 */
if ( ! function_exists( 'mycred_label' ) ) :
	function mycred_label( $trim = false ) {

		global $mycred_label;

		if ( $mycred_label === NULL )
			$mycred_label = apply_filters( 'mycred_label', MYCRED_DEFAULT_LABEL );

		$name = $mycred_label;
		if ( $trim )
			$name = strip_tags( $mycred_label );

		return $name;

	}
endif;

/**
 * Get myCRED
 * Returns myCRED's general settings and core functions.
 * Replaces mycred_get_settings()
 * @since 1.4
 * @version 1.1
 */
if ( ! function_exists( 'mycred' ) ) :
	function mycred( $point_type = MYCRED_DEFAULT_TYPE_KEY ) {

		global $mycred, $current_mycred;

		// Custom point type
		if ( $point_type != MYCRED_DEFAULT_TYPE_KEY ) {

			if ( isset( $current_mycred->cred_id ) && $current_mycred->cred_id === $point_type )
				return $current_mycred;

			$current_mycred = new myCRED_Settings( $point_type );

			return $current_mycred;

		}

		// Main point type
		if ( ! isset( $mycred->cred_id ) )
			$mycred = new myCRED_Settings();

		return $mycred;

	}
endif;

/**
 * Get Network Settings
 * Returns myCRED's network settings or false if multisite is not enabled.
 * @since 0.1
 * @version 1.2
 */
if ( ! function_exists( 'mycred_get_settings_network' ) ) :
	function mycred_get_settings_network() {

		global $mycred_network;

		$defaults            = array(
			'master'            => 0,
			'central'           => 0,
			'block'             => ''
		);

		if ( is_array( $mycred_network ) && ! empty( $mycred_network ) && array_key_exists( 'master', $mycred_network ) )
			return $mycred_network;

		$settings            = ( ( is_multisite() ) ? get_blog_option( get_network()->site_id, 'mycred_network', $defaults ) : $defaults );
		$settings            = shortcode_atts( $defaults, $settings );

		$settings['master']  = (bool) $settings['master'];
		$settings['central'] = (bool) $settings['central'];

		return $settings;

	}
endif;

/**
 * Is Main Site
 * In Multisite installs, this function will check if the current site or
 * a given site is the main site in the network.
 * @since 1.8
 * @version 1.0
 */
if ( ! function_exists( 'mycred_is_main_site' ) ) :
	function mycred_is_main_site( $site_id = NULL ) {

		if ( ! is_multisite() ) return true;

		if ( $site_id === NULL )
			$site_id = get_current_blog_id();

		if ( get_network()->site_id != $site_id )
			return false;

		return true;

	}
endif;

/**
 * Check if site is blocked
 * @since 1.5.4
 * @version 1.1
 */
if ( ! function_exists( 'mycred_is_site_blocked' ) ) :
	function mycred_is_site_blocked( $blog_id = NULL ) {

		// Only applicable for multisites
		if ( ! is_multisite() ) return false;

		// Blog ID
		if ( $blog_id === NULL )
			$blog_id = get_current_blog_id();

		// Main sites can not be blocked
		if ( $blog_id == get_network()->site_id ) return false;

		// Get Network settings
		$network    = mycred_get_settings_network();
		$block_list = wp_parse_id_list( $network['block'] );
		$blocked    = false;

		// Check if we are in the block list
		if ( ! empty( $block_list ) && in_array( $blog_id, $block_list ) )
			$blocked = true;

		return apply_filters( 'mycred_is_site_blocked', $blocked, $blog_id );

	}
endif;

/**
 * Override Settings
 * Checks if the Master Template feature is enabled on a Multisite install.
 * @since 0.1
 * @version 1.1
 */
if ( ! function_exists( 'mycred_override_settings' ) ) :
	function mycred_override_settings() {

		// Not a multisite
		if ( ! is_multisite() ) return false;

		$network_setup = mycred_get_settings_network();

		return apply_filters( 'mycred_mu_override_settings', (bool) $network_setup['master'], $network_setup );

	}
endif;

/**
 * Centralize Log
 * Checks if the Central Logging feature is enabled on a Multisite install.
 * @since 1.3
 * @version 1.1
 */
if ( ! function_exists( 'mycred_centralize_log' ) ) :
	function mycred_centralize_log() {

		// Not a multisite
		if ( ! is_multisite() ) return true;

		$network_setup = mycred_get_settings_network();

		return apply_filters( 'mycred_mu_centralize_log', (bool) $network_setup['central'], $network_setup );

	}
endif;

/**
 * Get Module
 * @since 1.7.3
 * @version 1.0.1
 */
if ( ! function_exists( 'mycred_get_module' ) ) :
	function mycred_get_module( $module = '', $type = 'solo' ) {

		global $mycred_modules;

		if ( $type == 'solo' ) {

			if ( ! array_key_exists( $module, $mycred_modules['solo'] ) )
				return false;

			return $mycred_modules['solo'][ $module ];

		}

		if ( ! array_key_exists( $type, $mycred_modules['type'] ) )
			return false;

		return $mycred_modules['type'][ $type ][ $module ];

	}
endif;

/**
 * Get Addon Settings
 * @since 1.7.7
 * @version 1.1
 */
if ( ! function_exists( 'mycred_get_addon_settings' ) ) :
	function mycred_get_addon_settings( $addon = '', $point_type = MYCRED_DEFAULT_TYPE_KEY ) {

		$settings  = false;
		$main_type = mycred();

		$mycred    = $main_type;
		if ( $point_type != MYCRED_DEFAULT_TYPE_KEY )
			$mycred = mycred( $point_type );

		if ( $addon != '' ) {

			if ( isset( $mycred->$addon ) )
				$settings = $mycred->$addon;

			if ( $settings === false && isset( $main_type->$addon ) && $point_type == MYCRED_DEFAULT_TYPE_KEY )
				$settings = $main_type->$addon;

			if ( empty( $settings ) )
				$settings = mycred_get_addon_defaults( $addon );

		}

		return apply_filters( 'mycred_get_addon_settings', $settings, $addon, $point_type );

	}
endif;

/**
 * Get Remote API Settings
 * @since 1.3
 * @version 1.0
 */
if ( ! function_exists( 'mycred_get_remote' ) ) :
	function mycred_get_remote() {

		$defaults = apply_filters( 'mycred_remote_defaults', array(
			'enabled' => 0,
			'key'     => '',
			'uri'     => 'api-dev',
			'debug'   => 0
		) );

		return mycred_apply_defaults( $defaults, mycred_get_option( 'mycred_pref_remote', array() ) );

	}
endif;

/**
 * Is myCRED Ready
 * @since 1.3
 * @version 1.1
 */
if ( ! function_exists( 'is_mycred_ready' ) ) :
	function is_mycred_ready() {

		if ( mycred_is_installed() !== false ) return true;

		return false;

	}
endif;

/**
 * Is myCRED Installed
 * Returns either false (setup has not been run) or the timestamp when it was completed.
 * @since 1.7
 * @version 1.0.1
 */
if ( ! function_exists( 'mycred_is_installed' ) ) :
	function mycred_is_installed() {

		return mycred_get_option( 'mycred_setup_completed', false );

	}
endif;

/**
 * Maybe Install myCRED Table
 * Check to see if maybe the myCRED table needs to be installed.
 * @since 1.7.6
 * @version 1.0.1
 */
if ( ! function_exists( 'maybe_install_mycred_table' ) ) :
	function maybe_install_mycred_table() {

		// No need to check this if we have disabled logging. Prevent this from being used using AJAX
		if ( ! MYCRED_ENABLE_LOGGING || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) || apply_filters( 'mycred_maybe_install_db', true ) === false ) return;

		global $wpdb, $mycred_log_table;

		// Check if the table exists
		if ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $mycred_log_table ) ) != $mycred_log_table ) {

			mycred_install_log( NULL, true );

			do_action( 'mycred_reinstalled_table' );

		}

	}
endif;

/**
 * Install Log
 * Installs the log for a site.
 * @since 1.3
 * @version 1.4.1
 */
if ( ! function_exists( 'mycred_install_log' ) ) :
	function mycred_install_log( $decimals = NULL, $force = false ) {

		if ( ! MYCRED_ENABLE_LOGGING ) return true;
		$mycred = mycred();

		if ( ! $force ) {

			$db_version = mycred_get_option( 'mycred_version_db', false );

			// DB Already installed
			if ( $db_version == myCRED_DB_VERSION ) return true;

		}

		global $wpdb;

		$cred_format = 'bigint(22)';
		$point_type  = $mycred->cred_id;

		// If decimals is not provided
		if ( $decimals === NULL )
			$decimals = $mycred->format['decimals'];

		// Point format in the log
		if ( $decimals > 0 ) {

			if ( $decimals > 4 )
				$cred_format = "decimal(32,$decimals)";

			else
				$cred_format = "decimal(22,$decimals)";

		}

		$wpdb->hide_errors();

		$collate = '';
		if ( $wpdb->has_cap( 'collation' ) ) {

			if ( ! empty( $wpdb->charset ) )
				$collate .= "DEFAULT CHARACTER SET {$wpdb->charset}";

			if ( ! empty( $wpdb->collate ) )
				$collate .= " COLLATE {$wpdb->collate}";

		}

		// Log structure
		$sql = "
			id            INT(11) NOT NULL AUTO_INCREMENT, 
			ref           VARCHAR(256) NOT NULL, 
			ref_id        INT(11) DEFAULT NULL, 
			user_id       INT(11) DEFAULT NULL, 
			creds         {$cred_format} DEFAULT NULL, 
			ctype         VARCHAR(64) DEFAULT '{$point_type}', 
			time          BIGINT(20) DEFAULT NULL, 
			entry         LONGTEXT DEFAULT NULL, 
			data          LONGTEXT DEFAULT NULL, 
			PRIMARY KEY   (id), 
			UNIQUE KEY id (id)"; 

		// Insert table
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( "CREATE TABLE IF NOT EXISTS {$mycred->log_table} ( " . $sql . " ) $collate;" );

		mycred_update_option( 'mycred_version_db', myCRED_DB_VERSION );

		return true;

	}
endif;

/**
 * Get Post Types
 * Returns an array of post types that myCRED uses.
 * @since 1.7
 * @version 1.1
 */
if ( ! function_exists( 'get_mycred_post_types' ) ) :
	function get_mycred_post_types() {

		$post_type_keys   = array( 'mycred_badge', 'buycred_payment' );

		// Badges
		$post_type_keys[] = ( defined( 'MYCRED_BADGE_KEY' ) ) ? MYCRED_BADGE_KEY : 'mycred_badge';

		// Coupons
		$post_type_keys[] = ( defined( 'MYCRED_COUPON_KEY' ) ) ? MYCRED_COUPON_KEY : 'mycred_coupon';

		// Ranks
		$post_type_keys[] = ( defined( 'MYCRED_RANK_KEY' ) ) ? MYCRED_RANK_KEY : 'mycred_rank';

		return apply_filters( 'mycred_post_types', $post_type_keys );

	}
endif;

/**
 * Get User ID
 * Attempts to return a user ID based on the request passed to this function-
 * Supports:
 * - NULL / empty string - returns the current users ID.
 * - "current" string - returns the current users ID.
 * - "bbprofile" string - returns the BuddyPress profile ID. Requires use on BP profiles.
 * - "author" string - returns the post authors user ID. Requires use inside the loop.
 * - "replyauthor" string - returns the bbPress reply authors ID. Requires use in bbPress forums topics.
 *
 * @since 1.7
 * @version 1.1
 */
if ( ! function_exists( 'mycred_get_user_id' ) ) :
	function mycred_get_user_id( $requested = '' ) {

		if ( is_string( $requested ) && strlen( $requested ) == 0 ) return $requested;

		$user_id = 0;
		if ( ! is_numeric( $requested ) ) {

			// Current user
			if ( $requested === 'current' || strlen( $requested ) == 0 )
				$user_id = get_current_user_id();

			// Comma separated list of IDs
			elseif ( count( explode( ',', $requested ) ) > 1 ) {

				$user_id = wp_parse_id_list( $requested );

			}

			// BuddyPress Profile ID
			elseif ( $requested === 'bbprofile' ) {

				if ( function_exists( 'bp_displayed_user_id' ) )
					$requested = bp_displayed_user_id();

			}

			// Post Author
			elseif ( $requested === 'author' ) {

				global $post;

				$author = get_the_author_meta( 'ID' );

				if ( empty( $author ) && isset( $post->post_author ) )
					$author = $post->post_author;

				if ( absint( $author ) )
					$user_id = $author;

			}

			// bbPress reply author
			elseif ( $requested === 'replyauthor' ) {

				if ( function_exists( 'bbp_get_reply_author_id' ) )
					$user_id = bbp_get_reply_author_id( bbp_get_reply_id() );

			}

			// Email address
			elseif ( is_email( $requested ) ) {

				$user = get_user_by( 'email', $requested );
				if ( isset( $user->ID ) )
					$user_id = $user->ID;

			}

			else {

				$user = get_user_by( 'login', $requested );
				if ( isset( $user->ID ) )
					$user_id = $user->ID;

				else {

					$user = get_user_by( 'slug', $requested );
					if ( isset( $user->ID ) )
						$user_id = $user->ID;

				}

			}

		}
		else {

			$user_id = absint( $requested );

		}

		return apply_filters( 'mycred_get_user_id', $user_id, $requested );

	}
endif;

/**
 * Get Users Profile URL
 * Returns a given users profile URL.
 * @since 1.7.4
 * @version 1.0.2
 */
if ( ! function_exists( 'mycred_get_users_profile_url' ) ) :
	function mycred_get_users_profile_url( $user_id = NULL ) {

		$profile_url = '';
		if ( $user_id === NULL || absint( $user_id ) === 0 ) return $profile_url;

		$user        = get_userdata( $user_id );
		$profile_url = get_author_posts_url( $user_id );

		// BuddyPress option
		if ( function_exists( 'bp_core_get_user_domain' ) )
			$profile_url = bp_core_get_user_domain( $user_id );

		return apply_filters( 'mycred_users_profile_url', $profile_url, $user );

	}
endif;

/**
 * Get Users Account
 * Returns either the current users or the given users account object.
 * @since 1.7
 * @version 1.1
 */
if ( ! function_exists( 'mycred_get_account' ) ) :
	function mycred_get_account( $user_id = NULL ) {

		global $mycred_current_account, $mycred_account;

		if ( $user_id === NULL ) {

			if ( ! did_action( 'init' ) ) {

				_doing_it_wrong( __FUNCTION__, 'This function should not be used before init.', '1.8' );

				return false;

			}

			if ( ! is_user_logged_in() ) return false;

			$user_id = get_current_user_id();

		}

		$user_id = absint( $user_id );
		if ( $user_id === 0 ) return false;

		if ( mycred_is_current_account( $user_id ) )
			return $mycred_current_account;

		if ( mycred_is_account( $user_id ) ) {

			return $mycred_account;

		}

		$mycred_account = new myCRED_Account( $user_id );

		do_action( 'mycred_get_account' );

		return $mycred_account;

	}
endif;

/**
 * Get Account
 * Check if the account global is available based on a given user id.
 * @since 1.8
 * @version 1.0
 */
if ( ! function_exists( 'mycred_is_account' ) ) :
	function mycred_is_account( $user_id = NULL ) {

		global $mycred_account;

		if ( isset( $mycred_account )
			&& ( $mycred_account instanceof myCRED_Account )
			&& ( $user_id === $mycred_account->user_id ) 
			&& ( $user_id !== NULL )
			&& ( $user_id !== 0 )
		) {

			return true;

		}

		return false;

	}
endif;

/**
 * Get Current Account
 * Returns the current account object (if one exists) else false.
 * @since 1.8
 * @version 1.0
 */
if ( ! function_exists( 'mycred_get_current_account' ) ) :
	function mycred_get_current_account() {

		global $mycred_current_account;

		if ( isset( $mycred_current_account ) && ( $mycred_current_account instanceof myCRED_Account ) )
			return $mycred_current_account;

		return false;

	}
endif;

/**
 * Set Current Account
 * Sets the current account object.
 * @since 1.8
 * @version 1.0
 */
if ( ! function_exists( 'mycred_set_current_account' ) ) :
	function mycred_set_current_account( $user_id = NULL ) {

		global $mycred_current_account;

		if ( isset( $mycred_current_account )
			&& ( $mycred_current_account instanceof myCRED_Account )
			&& ( $user_id === $mycred_current_account->user_id ) 
			&& ( $user_id !== NULL )
		) {

			return $mycred_current_account;

		}

		$mycred_current_account = new myCRED_Account( ( ( $user_id === NULL ) ? get_current_user_id() : $user_id ) );

		do_action( 'mycred_set_current_account' );

		return $mycred_current_account;

	}
endif;

/**
 * Get Current Account
 * Check if the current account global is available based on a given user id.
 * @since 1.8
 * @version 1.0
 */
if ( ! function_exists( 'mycred_is_current_account' ) ) :
	function mycred_is_current_account( $user_id = NULL ) {

		global $mycred_current_account;

		if ( isset( $mycred_current_account )
			&& ( $mycred_current_account instanceof myCRED_Account )
			&& ( $user_id === $mycred_current_account->user_id ) 
			&& ( $user_id !== NULL )
			&& ( $user_id !== 0 )
		) {

			return true;

		}

		return false;

	}
endif;

/**
 * Get Cred Types
 * Returns an associative array of registered point types.
 * @param $name_first (bool) option to replace "myCRED" with the point type name set.
 * @since 1.4
 * @version 1.1
 */
if ( ! function_exists( 'mycred_get_types' ) ) :
	function mycred_get_types( $name_first = false ) {

		global $mycred_types;

		if ( is_array( $mycred_types ) && ! empty( $mycred_types ) )
			$types = $mycred_types;

		else {

			$types = array();

			$available_types = mycred_get_option( 'mycred_types', array( MYCRED_DEFAULT_TYPE_KEY => mycred_label() ) );
			if ( count( $available_types ) > 1 ) {

				foreach ( $available_types as $type => $label ) {

					if ( $type == MYCRED_DEFAULT_TYPE_KEY )
						$label   = mycred_get_point_type_name( MYCRED_DEFAULT_TYPE_KEY, false );

					$types[ $type ] = $label;

				}

			}
			else {

				if ( $name_first )
					$available_types[ MYCRED_DEFAULT_TYPE_KEY ] = mycred_get_point_type_name( MYCRED_DEFAULT_TYPE_KEY, false );

				$types = $available_types;

			}

		}

		return apply_filters( 'mycred_types', $types );

	}
endif;

/**
 * Get Point Type
 * @since 1.8
 * @version 1.0
 */
if ( ! function_exists( 'mycred_get_point_type' ) ) :
	function mycred_get_point_type( $point_type = MYCRED_DEFAULT_TYPE_KEY ) {

		$point_type = sanitize_key( $point_type );

		global $current_mycred_type;

		if ( isset( $current_mycred_type )
			&& ( $current_mycred_type instanceof myCRED_Point_Type )
			&& ( $current_mycred_type->cred_id === $point_type )
		) {

			return $current_mycred_type;

		}

		$current_mycred_type = new myCRED_Point_Type( $point_type );

		do_action( 'mycred_get_point_type', $point_type );

		return $current_mycred_type;

	}
endif;

/**
 * Get Usable Point Types
 * Returns an array of point type keys that a given user is allowed to use.
 * @since 1.7
 * @version 1.0.1
 */
if ( ! function_exists( 'mycred_get_usable_types' ) ) :
	function mycred_get_usable_types( $user_id = NULL ) {

		$original_id = $user_id;
		if ( $user_id === NULL )
			$user_id = get_current_user_id();

		$usable      = array();
		if ( is_user_logged_in() || $original_id !== NULL ) {

			global $mycred, $mycred_current_account;

			if ( ! isset( $mycred_current_account->balance ) || empty( $mycred_current_account->balance ) ) {

				$types = mycred_get_types();

				if ( count( $types ) == 1 && ! $mycred->exclude_user( $user_id ) )
					$usable[] = MYCRED_DEFAULT_TYPE_KEY;

				else {

					foreach ( $types as $type_id => $type ) {

						if ( $type_id == MYCRED_DEFAULT_TYPE_KEY && ! $mycred->exclude_user( $user_id ) )
							$usable[] = MYCRED_DEFAULT_TYPE_KEY;

						else {

							$custom_type = mycred( $type_id );
							if ( ! $custom_type->exclude_user( $user_id ) )
								$usable[] = $type_id;

						}

					}

				}

			}
			elseif ( ! empty( $mycred_current_account->balance ) ) {

				foreach ( $mycred_current_account->balance as $balance ) {

					if ( $balance !== false )
						$usable[] = $balance->point_type->cred_id;

				}

			}

		}

		return $usable;

	}
endif;

/**
 * Point Type Exists
 * @since 1.6.8
 * @version 1.0.1
 */
if ( ! function_exists( 'mycred_point_type_exists' ) ) :
	function mycred_point_type_exists( $type = NULL ) {

		$result = false;
		$types  = mycred_get_types();
		$type   = sanitize_key( $type );

		// Remove _total from total balances to get the underlaying id
		$type   = str_replace( '_total', '', $type );

		// Need to remove blog id suffix on multisites
		// This function should not be used to check for point type ids with
		// blog ID suffixes but in case it is used incorrectly, we need to fix this.
		if ( is_multisite() )
			$type = str_replace( '_' . get_current_blog_id(), '', $type );

		if ( strlen( $type ) > 0 && array_key_exists( $type, $types ) )
			$result = true;

		return $result;

	}
endif;

/**
 * Get Point Type Name
 * Returns the name given to a particular point type.
 * @param $signular (boolean) option to return the plural version, returns singular by default
 * @since 0.1
 * @version 1.1
 */
if ( ! function_exists( 'mycred_get_point_type_name' ) ) :
	function mycred_get_point_type_name( $point_type = MYCRED_DEFAULT_TYPE_KEY, $singular = true ) {

		$mycred = mycred( $point_type );

		if ( $singular )
			return $mycred->singular();

		return $mycred->plural();

	}
endif;

/**
 * Select Point Type from Select Dropdown
 * @since 1.4
 * @version 1.0
 */
if ( ! function_exists( 'mycred_types_select_from_dropdown' ) ) :
	function mycred_types_select_from_dropdown( $name = '', $id = '', $selected = '', $return = false, $extra = '' ) {

		$types  = mycred_get_types();
		$output = '';

		if ( count( $types ) == 1 )
			$output .= '<input type="hidden"' . $extra . ' name="' . $name . '" id="' . $id . '" value="mycred_default" />';

		else {

			$output .= '<select' . $extra . ' name="' . $name . '" id="' . $id . '">';

			foreach ( $types as $type => $label ) {

				if ( $type == MYCRED_DEFAULT_TYPE_KEY ) {
					$_mycred = mycred( $type );
					$label   = $_mycred->plural();
				}

				$output .= '<option value="' . $type . '"';
				if ( $selected == $type ) $output .= ' selected="selected"';
				$output .= '>' . $label . '</option>';

			}

			$output .= '</select>';

		}

		if ( $return )
			return $output;

		echo $output;

	}
endif;

/**
 * Select Point Type from Checkboxes
 * @since 1.4
 * @version 1.0.1
 */
if ( ! function_exists( 'mycred_types_select_from_checkboxes' ) ) :
	function mycred_types_select_from_checkboxes( $name = '', $id = '', $selected_values = array(), $return = false ) {

		$types = mycred_get_types();

		$output = '';
		if ( count( $types ) > 0 ) {
			foreach ( $types as $type => $label ) {
				$selected = '';
				if ( in_array( $type, (array) $selected_values ) )
					$selected = ' checked="checked"';

				$id .= '-' . $type;

				$output .= '<div class="mycred-notify-pt-wrapper"><label for="' . $id . '"><input type="checkbox" name="' . $name . '" id="' . $id . '" value="' . $type . '"' . $selected . ' /> ' . $label . '</label></div>';
			}
		}

		if ( $return )
			return $output;

		echo $output;

	}
endif;

/**
 * Get DB Column
 * Helper function to return the correct database tabel based on
 * our multisite setup.
 * @since 1.8
 * @version 1.0
 */
if ( ! function_exists( 'mycred_get_db_column' ) ) :
	function mycred_get_db_column( $column = '' ) {

		global $wpdb;

		$table = '';
		if ( ! in_array( $column, array( 'posts', 'postmeta', 'comments', 'commentmeta', 'terms', 'term_meta', 'term_relationships', 'links', 'options' ) ) ) return $table;

		if ( isset( $wpdb->$column ) )
			$table = $wpdb->$column;

		// This is what are are here for. On multisites, if we enable the Master template
		// feature, we need to get the column for the networks main site instead of our own
		if ( mycred_override_settings() && ! mycred_is_main_site() )
			$table = $wpdb->get_blog_prefix( get_network()->site_id ) . $column;

		return apply_filters( 'mycred_get_db_column', $table );

	}
endif;

/**
 * Add Option
 * @since 1.7.6
 * @version 1.0
 */
if ( ! function_exists( 'mycred_add_option' ) ) :
	function mycred_add_option( $option_id, $value = '' ) {

		if ( is_multisite() ) {

			// Master template enabled
			if ( mycred_override_settings() )
				return add_blog_option( 1, $option_id, $value );

			// Master template disabled
			return add_blog_option( $GLOBALS['blog_id'], $option_id, $value );

		}
		return add_option( $option_id, $value );

	}
endif;

/**
 * Get Option
 * @since 1.4
 * @version 1.0.2
 */
if ( ! function_exists( 'mycred_get_option' ) ) :
	function mycred_get_option( $option_id, $default = array() ) {

		if ( is_multisite() ) {

			// Master template enabled
			if ( mycred_override_settings() )
				return get_blog_option( get_network()->site_id, $option_id, $default );

			// Master template disabled
			return get_blog_option( get_current_blog_id(), $option_id, $default );

		}

		$get_option_id = apply_filters( 'mycred_get_option_id', $option_id );

		return get_option( $get_option_id, $default );

	}
endif;

/**
 * Update Option
 * @since 1.4
 * @version 1.0.2
 */
if ( ! function_exists( 'mycred_update_option' ) ) :
	function mycred_update_option( $option_id, $value = '' ) {

		if ( is_multisite() ) {

			// Master template enabled
			if ( mycred_override_settings() )
				return update_blog_option( get_network()->site_id, $option_id, $value );

			// Master template disabled
			return update_blog_option( get_current_blog_id(), $option_id, $value );

		}

		return update_option( $option_id, $value );

	}
endif;

/**
 * Delete Option
 * @since 1.5.2
 * @version 1.0.1
 */
if ( ! function_exists( 'mycred_delete_option' ) ) :
	function mycred_delete_option( $option_id ) {

		if ( is_multisite() ) {

			// Master template enabled
			if ( mycred_override_settings() )
				return delete_blog_option( get_network()->site_id, $option_id );

			// Master template disabled
			return delete_blog_option( get_current_blog_id(), $option_id );

		}

		return delete_option( $option_id );

	}
endif;

/**
 * Get Meta Key
 * @since 1.6.8
 * @version 1.0
 */
if ( ! function_exists( 'mycred_get_meta_key' ) ) :
	function mycred_get_meta_key( $key = '', $end = '' ) {

		if ( is_multisite() ) {

			$blog_id = get_current_blog_id();

			if ( $blog_id > 1 && ! mycred_centralize_log() && $key != 'mycred_rank' )
				$key .= '_' . $blog_id;

			elseif ( $blog_id > 1 && ! mycred_override_settings() && $key == 'mycred_rank' )
				$key .= '_' . $blog_id;

		}

		if ( strlen( $end ) > 0 )
			$key .= $end;

		return $key;

	}
endif;

/**
 * Add User Meta
 * @since 1.5
 * @version 1.1
 */
if ( ! function_exists( 'mycred_add_user_meta' ) ) :
	function mycred_add_user_meta( $user_id, $key = '', $end = '', $value = '', $unique = false ) {

		$key = mycred_get_meta_key( $key, $end );

		return add_user_meta( $user_id, $key, $value, $unique );

	}
endif;

/**
 * Get User Meta
 * @since 1.5
 * @version 1.1
 */
if ( ! function_exists( 'mycred_get_user_meta' ) ) :
	function mycred_get_user_meta( $user_id, $key = '', $end = '', $unique = false ) {

		$key = mycred_get_meta_key( $key, $end );

		return get_user_meta( $user_id, $key, $unique );

	}
endif;

/**
 * Update User Meta
 * @since 1.5
 * @version 1.1
 */
if ( ! function_exists( 'mycred_update_user_meta' ) ) :
	function mycred_update_user_meta( $user_id, $key = '', $end = '', $value = '', $previous = '' ) {

		$key = mycred_get_meta_key( $key, $end );

		return update_user_meta( $user_id, $key, $value, $previous );

	}
endif;

/**
 * Delete User Meta
 * @since 1.5
 * @version 1.1.1
 */
if ( ! function_exists( 'mycred_delete_user_meta' ) ) :
	function mycred_delete_user_meta( $user_id, $key = '', $end = '', $value = '' ) {

		$key = mycred_get_meta_key( $key, $end );

		if ( $value === NULL )
			return delete_user_meta( $user_id, $key );

		return delete_user_meta( $user_id, $key, $value );

	}
endif;

/**
 * Add Post Meta
 * @since 1.8
 * @version 1.0
 */
if ( ! function_exists( 'mycred_add_post_meta' ) ) :
	function mycred_add_post_meta( $post_id, $key = '', $value = '', $unique = false ) {

		$override = ( mycred_override_settings() && ! mycred_is_main_site() );

		if ( $override )
			switch_to_blog( get_network()->site_id );

		$result = add_post_meta( $post_id, $key, $value, $unique );

		if ( $override )
			restore_current_blog();

		return $result;

	}
endif;

/**
 * Get Post Meta
 * @since 1.8
 * @version 1.0
 */
if ( ! function_exists( 'mycred_get_post_meta' ) ) :
	function mycred_get_post_meta( $post_id, $key = '', $unique = false ) {

		$override = ( mycred_override_settings() && ! mycred_is_main_site() );

		if ( $override )
			switch_to_blog( get_network()->site_id );

		$value = get_post_meta( $post_id, $key, $unique );

		if ( $override )
			restore_current_blog();

		return $value;

	}
endif;

/**
 * Update Post Meta
 * @since 1.8
 * @version 1.0
 */
if ( ! function_exists( 'mycred_update_post_meta' ) ) :
	function mycred_update_post_meta( $post_id, $key = '', $value = '', $previous = '' ) {

		$override = ( mycred_override_settings() && ! mycred_is_main_site() );

		if ( $override )
			switch_to_blog( get_network()->site_id );

		$result = update_post_meta( $post_id, $key, $value, $previous );

		if ( $override )
			restore_current_blog();

		return $result;

	}
endif;

/**
 * Delete Post Meta
 * @since 1.8
 * @version 1.0
 */
if ( ! function_exists( 'mycred_delete_post_meta' ) ) :
	function mycred_delete_post_meta( $post_id, $key = '', $value = '' ) {

		$override = ( mycred_override_settings() && ! mycred_is_main_site() );

		if ( $override )
			switch_to_blog( get_network()->site_id );

		$result = delete_post_meta( $post_id, $key, $value );

		if ( $override )
			restore_current_blog();

		return $result;

	}
endif;

/**
 * Get Post
 * @since 1.8
 * @version 1.0
 */
if ( ! function_exists( 'mycred_get_post' ) ) :
	function mycred_get_post( $post_id = NULL ) {

		$override = ( mycred_override_settings() && ! mycred_is_main_site() );

		if ( $override )
			switch_to_blog( get_network()->site_id );

		$post = get_post( $post_id );

		if ( $override )
			restore_current_blog();

		return $post;

	}
endif;

/**
 * Get Post
 * @since 1.8
 * @version 1.0
 */
if ( ! function_exists( 'mycred_get_permalink' ) ) :
	function mycred_get_permalink( $post_id = NULL ) {

		$override = ( mycred_override_settings() && ! mycred_is_main_site() );

		if ( $override )
			switch_to_blog( get_network()->site_id );

		$permalink = get_permalink( $post_id );

		if ( $override )
			restore_current_blog();

		return $permalink;

	}
endif;

/**
 * Get Post Type
 * @since 1.8
 * @version 1.0
 */
if ( ! function_exists( 'mycred_get_post_type' ) ) :
	function mycred_get_post_type( $post_id = NULL ) {

		$override = ( mycred_override_settings() && ! mycred_is_main_site() );

		if ( $override )
			switch_to_blog( get_network()->site_id );

		$post_type = get_post_type( $post_id );

		if ( $override )
			restore_current_blog();

		return $post_type;

	}
endif;

/**
 * Get Post Title
 * @since 1.8
 * @version 1.0
 */
if ( ! function_exists( 'mycred_get_the_title' ) ) :
	function mycred_get_the_title( $post_id = NULL ) {

		$override = ( mycred_override_settings() && ! mycred_is_main_site() );

		if ( $override )
			switch_to_blog( get_network()->site_id );

		$post_type = get_the_title( $post_id );

		if ( $override )
			restore_current_blog();

		return $post_type;

	}
endif;

/**
 * Get Page by Title
 * @since 1.8
 * @version 1.0
 */
if ( ! function_exists( 'mycred_get_page_by_title' ) ) :
	function mycred_get_page_by_title( $post_id, $type, $post_type ) {

		$override = ( mycred_override_settings() && ! mycred_is_main_site() );

		if ( $override )
			switch_to_blog( get_network()->site_id );

		$results = get_page_by_title( $post_id, $type, $post_type );

		if ( $override )
			restore_current_blog();

		return $results;

	}
endif;

/**
 * Trash Post
 * @since 1.8
 * @version 1.0
 */
if ( ! function_exists( 'mycred_trash_post' ) ) :
	function mycred_trash_post( $post_id = NULL ) {

		$override = ( mycred_override_settings() && ! mycred_is_main_site() );

		if ( $override )
			switch_to_blog( get_network()->site_id );

		$results = wp_trash_post( $post_id );

		if ( $override )
			restore_current_blog();

		return $results;

	}
endif;

/**
 * Get Attachment URL
 * @since 1.8.11
 * @version 1.0
 */
if ( ! function_exists( 'mycred_get_attachment_url' ) ) :
	function mycred_get_attachment_url( $post_id = NULL ) {

		$override = ( mycred_override_settings() && ! mycred_is_main_site() );

		if ( $override )
			switch_to_blog( get_network()->site_id );

		$results = wp_get_attachment_url( $post_id );

		if ( $override )
			restore_current_blog();

		return $results;

	}
endif;

/**
 * Delete Post
 * @since 1.8
 * @version 1.0
 */
if ( ! function_exists( 'mycred_delete_post' ) ) :
	function mycred_delete_post( $post_id = NULL, $force = false ) {

		$override = ( mycred_override_settings() && ! mycred_is_main_site() );

		if ( $override )
			switch_to_blog( get_network()->site_id );

		$results = wp_delete_post( $post_id, $force );

		if ( $override )
			restore_current_blog();

		return $results;

	}
endif;

/**
 * Is Admin
 * Conditional tag that checks if a given user or the current user
 * can either edit the plugin or creds.
 * @param $user_id (int), optional user id to check, defaults to current user
 * @returns true or false
 * @since 0.1
 * @version 1.2
 */
if ( ! function_exists( 'mycred_is_admin' ) ) :
	function mycred_is_admin( $user_id = NULL, $point_type = MYCRED_DEFAULT_TYPE_KEY ) {

		$mycred = mycred( $point_type );

		if ( $mycred->user_is_point_admin( $user_id ) || $mycred->user_is_point_editor( $user_id ) )
			return true;

		return false;

	}
endif;

/**
 * Exclude User
 * Checks if a given user is excluded from using myCRED.
 * @see http://codex.mycred.me/functions/mycred_exclude_user/
 * @param $user_id (int), optional user to check, defaults to current user
 * @since 0.1
 * @version 1.2
 */
if ( ! function_exists( 'mycred_exclude_user' ) ) :
	function mycred_exclude_user( $user_id = NULL, $point_type = MYCRED_DEFAULT_TYPE_KEY ) {

		$mycred = mycred( $point_type );

		return $mycred->exclude_user( $user_id );

	}
endif;

/**
 * Get Users Point Balance
 * Retreaves a given users point balance.
 * @returns false if user is excluded or if invalid values are provided, else returns the raw balance.
 * @since 1.7.4
 * @version 1.1
 */ 
if ( ! function_exists( 'mycred_get_users_balance' ) ) :
	function mycred_get_users_balance( $user_id = NULL, $point_type = MYCRED_DEFAULT_TYPE_KEY ) {

		$mycred = mycred( $point_type );

		if ( $mycred->exclude_user( $user_id ) ) return false;

		return $mycred->get_users_balance( $user_id, $point_type );

	}
endif;
// Depreciated
if ( ! function_exists( 'mycred_get_users_cred' ) ) :
	function mycred_get_users_cred( $user_id = NULL, $point_type = MYCRED_DEFAULT_TYPE_KEY ) {

		return mycred_get_users_balance( $user_id, $point_type );

	}
endif;

/**
 * Get Users Total Balance
 * @since 1.7.6
 * @version 1.0
 */ 
if ( ! function_exists( 'mycred_get_users_total_balance' ) ) :
	function mycred_get_users_total_balance( $user_id = NULL, $point_type = MYCRED_DEFAULT_TYPE_KEY ) {

		$mycred = mycred( $point_type );

		if ( $mycred->exclude_user( $user_id ) ) return false;

		return $mycred->get_users_total_balance( $user_id, $point_type );

	}
endif;

/**
 * Get Users Creds Formated
 * Returns the given users current cred balance formated. If no user id is given
 * this function will return false!
 * @param $user_id (int), required user id
 * @return users balance (string) or false if no user id is given
 * @since 0.1
 * @version 1.3
 */
if ( ! function_exists( 'mycred_display_users_balance' ) ) :
	function mycred_display_users_balance( $user_id = NULL, $point_type = MYCRED_DEFAULT_TYPE_KEY ) {

		$mycred  = mycred( $point_type );

		if ( $mycred->exclude_user( $user_id ) ) return '';

		$balance = $mycred->get_users_balance( $user_id, $point_type );

		return $mycred->format_creds( $balance );

	}
endif;
// Depreciated
if ( ! function_exists( 'mycred_get_users_fcred' ) ) :
	function mycred_get_users_fcred( $user_id = NULL, $point_type = MYCRED_DEFAULT_TYPE_KEY ) {

		return mycred_display_users_balance( $user_id, $point_type );

	}
endif;

/**
 * Display Users Total Balance
 * @since 1.7.6
 * @version 1.1
 */ 
if ( ! function_exists( 'mycred_display_users_total_balance' ) ) :
	function mycred_display_users_total_balance( $user_id = NULL, $point_type = MYCRED_DEFAULT_TYPE_KEY ) {

		$mycred  = mycred( $point_type );

		if ( $mycred->exclude_user( $user_id ) ) return '';

		$balance = $mycred->get_users_total_balance( $user_id, $point_type );

		return $mycred->format_creds( $balance );

	}
endif;

/**
 * Format Number
 * @since 1.3.3
 * @version 1.2
 */
if ( ! function_exists( 'mycred_format_number' ) ) :
	function mycred_format_number( $value = NULL, $point_type = MYCRED_DEFAULT_TYPE_KEY ) {

		if ( $value === NULL || ! is_numeric( $value ) ) return $value;

		$mycred = mycred( $point_type );

		return $mycred->format_number( $value );

	}
endif;

/**
 * Format Points
 * @since 1.8
 * @version 1.0
 */
if ( ! function_exists( 'mycred_format_points' ) ) :
	function mycred_format_points( $value = NULL, $point_type = MYCRED_DEFAULT_TYPE_KEY ) {

		if ( $value === NULL || ! is_numeric( $value ) ) return $value;

		$mycred = mycred( $point_type );

		return $mycred->format_creds( $value );

	}
endif;
// Depreciated
if ( ! function_exists( 'mycred_format_creds' ) ) :
	function mycred_format_creds( $value = NULL, $point_type = MYCRED_DEFAULT_TYPE_KEY ) {

		return mycred_format_points( $value, $point_type );

	}
endif;

/**
 * Add Points
 * Adds creds to a given user. A refernece ID, user id and amount must be given.
 * Important! This function will not check if the user should be excluded from gaining points, this must
 * be done before calling this function!
 * @see http://codex.mycred.me/functions/mycred_add/
 * @param $ref (string), required reference id
 * @param $user_id (int), required id of the user who will get these points
 * @param $amount (int|float), required number of creds to give or deduct from the given user.
 * @param $ref_id (array), optional array of reference IDs allowing the use of content specific keywords in the log entry
 * @param $data (object|array|string|int), optional extra data to save in the log. Note that arrays gets serialized!
 * @returns boolean true on success or false on fail
 * @since 0.1
 * @version 1.3
 */
if ( ! function_exists( 'mycred_add' ) ) :
	function mycred_add( $ref = '', $user_id = '', $amount = '', $entry = '', $ref_id = '', $data = '', $point_type = MYCRED_DEFAULT_TYPE_KEY ) {

		// $ref, $user_id and $cred is required
		if ( $ref == '' || $user_id == '' || $amount == '' ) return false;

		$mycred = mycred( $point_type );

		return $mycred->add_creds( $ref, $user_id, $amount, $entry, $ref_id, $data, $point_type );

	}
endif;

/**
 * Subtract Creds
 * Subtracts creds from a given user. Works just as mycred_add() but the creds are converted into a negative value.
 * @see http://codex.mycred.me/functions/mycred_subtract/
 * @uses mycred_add()
 * @since 0.1
 * @version 1.1.1
 */
if ( ! function_exists( 'mycred_subtract' ) ) :
	function mycred_subtract( $ref = '', $user_id = '', $amount = '', $entry = '', $ref_id = '', $data = '', $point_type = MYCRED_DEFAULT_TYPE_KEY ) {

		if ( $ref == '' || $user_id == '' || $amount == '' ) return false;
		if ( $amount > 0 ) $amount = 0 - $amount;

		return mycred_add( $ref, $user_id, $amount, $entry, $ref_id, $data, $point_type );

	}
endif;

/**
 * Plugin Activation
 * @since 1.3
 * @version 1.1.1
 */
if ( ! function_exists( 'mycred_plugin_activation' ) ) :
	function mycred_plugin_activation() {

		// Load Installer
		require_once myCRED_INCLUDES_DIR . 'mycred-install.php';
		$installer = mycred_installer();

		// Compatibility check
		$installer::compat();

		// First time activation
		if ( get_option( 'mycred_version', false ) === false )
			$installer::activate();

		// Re-activation
		else
			$installer::reactivate();

	}
endif;

/**
 * Runs when the plugin is deactivated
 * @since 1.3
 * @version 1.0
 */
if ( ! function_exists( 'mycred_plugin_deactivation' ) ) :
	function mycred_plugin_deactivation() {

		// Clear Cron
		wp_clear_scheduled_hook( 'mycred_reset_key' );
		wp_clear_scheduled_hook( 'mycred_banking_recurring_payout' );
		wp_clear_scheduled_hook( 'mycred_banking_interest_compound' );
		wp_clear_scheduled_hook( 'mycred_banking_interest_payout' );
		
		update_option( 'mycred_deactivated_on', time() );

		/**
		 * Runs when the plugin is deleted
		 * @since 1.3
		 * @version 1.2
		 */
		register_uninstall_hook( myCRED_THIS, 'mycred_plugin_uninstall' );

		do_action( 'mycred_deactivation' );

	}
endif;

if ( ! function_exists( 'mycred_plugin_uninstall' ) ) :
	function mycred_plugin_uninstall() {
			
		// Load Installer
		require_once myCRED_INCLUDES_DIR . 'mycred-install.php';
		$installer = mycred_installer();

		do_action( 'mycred_before_deletion', $installer );

		// Run uninstaller
		$installer::uninstall();

		do_action( 'mycred_after_deletion', $installer );

	}
endif;

/**
 * Apply Defaults
 * Based on the shortcode_atts() function with support for
 * multidimentional arrays.
 * @since 1.1.2
 * @version 1.0
 */
if ( ! function_exists( 'mycred_apply_defaults' ) ) :
	function mycred_apply_defaults( &$pref, $set ) {

		$set    = (array) $set;
		$return = array();

		foreach ( $pref as $key => $value ) {

			if ( array_key_exists( $key, $set ) ) {

				if ( is_array( $value ) && ! empty( $value ) )
					$return[ $key ] = mycred_apply_defaults( $value, $set[ $key ] );

				else
					$return[ $key ] = $set[ $key ];

			}

			else $return[ $key ] = $value;

		}

		return $return;

	}
endif;

/**
 * Strip Tags
 * Strippes HTML tags from a given string.
 * @param $string (string) string to stip
 * @param $overwrite (string), optional HTML tags to allow
 * @since 0.1
 * @version 1.0
 */
if ( ! function_exists( 'mycred_strip_tags' ) ) :
	function mycred_strip_tags( $string = '', $overwride = '' ) {

		$mycred = mycred();

		return $mycred->allowed_tags( $string, $overwrite );

	}
endif;

/**
 * Flush Widget Cache
 * @since 0.1
 * @version 1.0
 */
if ( ! function_exists( 'mycred_flush_widget_cache' ) ) :
	function mycred_flush_widget_cache( $id = NULL ) {

		if ( $id === NULL ) return;
		wp_cache_delete( $id, 'widget' );

	}
endif;

/**
 * Get Exchange Rates
 * Returns the exchange rates for point types
 * @since 1.5
 * @version 1.0
 */
if ( ! function_exists( 'mycred_get_exchange_rates' ) ) :
	function mycred_get_exchange_rates( $point_type = '' ) {

		$types   = mycred_get_types();
		$default = array();

		foreach ( $types as $type => $label ) {
			if ( $type == $point_type ) continue;
			$default[ $type ] = 0;
		}

		$settings = mycred_get_option( 'mycred_pref_exchange_' . $point_type, $default );
		$settings = mycred_apply_defaults( $default, $settings );

		return $settings;

	}
endif;

/**
 * Is Float?
 * @since 1.5
 * @version 1.0
 */
if ( ! function_exists( 'isfloat' ) ) :
	function isfloat( $f ) {

		return ( $f == (string)(float) $f );

	}
endif;

/**
 * Translate Limit Code
 * @since 1.6
 * @version 1.0.1
 */
if ( ! function_exists( 'mycred_translate_limit_code' ) ) :
	function mycred_translate_limit_code( $code = '', $id, $mycred ) {

		if ( $code == '' ) return '-';

		if ( $code == '0/x' || $code == 0 )
			return __( 'No limit', 'mycred' );

		$result = '-';
		$check  = explode( '/', $code );
		if ( count( $check ) == 2 ) {

			$per    = __( 'in total', 'mycred' );
			if ( $check[1] == 'd' )
				$per = __( 'per day', 'mycred' );

			elseif ( $check[1] == 'w' )
				$per = __( 'per week', 'mycred' );

			elseif ( $check[1] == 'm' )
				$per = __( 'per month', 'mycred' );

			$result = sprintf( _n( 'Maximum once', 'Maximum %d times', $check[0], 'mycred' ), $check[0] ) . ' ' . $per;

		}

		elseif ( is_numeric( $code ) ) {

			$result = sprintf( _n( 'Maximum once', 'Maximum %d times', $code, 'mycred' ), $code );

		}

		return apply_filters( 'mycred_translate_limit_code', $result, $code, $id, $mycred );

	}
endif;

/**
 * Ordinal Suffix
 * @since 1.7
 * @version 1.1
 */
if ( ! function_exists( 'mycred_ordinal_suffix' ) ) :
	function mycred_ordinal_suffix( $num = 0, $depreciated = true ) {

		if ( ! is_numeric( $num ) ) return $num;

		$value  = $num;
		$num    = $num % 100; // protect against large numbers

		$result = sprintf( _x( '%d th', 'e.g. 5 th', 'mycred' ), $value );
		if ( $num < 11 || $num > 13 ) {
			switch ( $num % 10 ) {

				case 1 : $result = sprintf( _x( '%d st', 'e.g. 1 st', 'mycred' ), $value );
				case 2 : $result = sprintf( _x( '%d nd', 'e.g. 2 nd', 'mycred' ), $value );
				case 3 : $result = sprintf( _x( '%d rd', 'e.g. 3 rd', 'mycred' ), $value );

			}
		}

		return apply_filters( 'mycred_ordinal_suffix', $result, $value );

	}
endif;

/**
 * Date to Timestamp
 * Converts a well formatted date string into GMT unixtimestamp.
 * @since 1.7
 * @version 1.0
 */
if ( ! function_exists( 'mycred_date_to_gmt_timestamp' ) ) :
	function mycred_date_to_gmt_timestamp( $string = '' ) {

		return strtotime( get_gmt_from_date( $string ) );

	}
endif;

/**
 * Timestamp to Date
 * Converts a GMT unixtimestamp to local timestamp
 * @since 1.7
 * @version 1.0
 */
if ( ! function_exists( 'mycred_gmt_timestamp_to_local' ) ) :
	function mycred_gmt_timestamp_to_local( $string = '' ) {

		return strtotime( get_date_from_gmt( date( 'Y-m-d H:i:s', $string ), 'Y-m-d H:i:s' ) );

	}
endif;

/**
 * Force Singular Session
 * Used to prevent multiple simultaneous AJAX calls from any one user.
 * The $timelimit sets the minimum amount of seconds that must have passed between
 * two AJAX requests.
 * @since 1.7
 * @version 1.1
 */
if ( ! function_exists( 'mycred_force_singular_session' ) ) :
	function mycred_force_singular_session( $user_id = NULL, $key = NULL, $timelimit = MYCRED_MIN_TIME_LIMIT ) {

		$force      = false;
		$time       = time();
		$user_id    = absint( $user_id );
		$key        = sanitize_text_field( $key );
		$timelimit  = absint( $timelimit );

		if ( $key == '' ) return true;

		// 1 - Cookies
		$last_call  = $time - $timelimit;
		$cookie_key = md5( $user_id . $key );
		if ( isset( $_COOKIE[ $cookie_key ] ) )
			$last_call = absint( $_COOKIE[ $cookie_key ] );

		if ( ( $time - $last_call ) < $timelimit )
			$force = true;

		setcookie( $cookie_key, $time, ( time() + DAY_IN_SECONDS ), COOKIEPATH, COOKIE_DOMAIN );

		return apply_filters( 'mycred_force_singular_session', $force, $user_id, $key, $timelimit );

	}
endif;

/**
 * Locate Template
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mycred_locate_template' ) ) :
	function mycred_locate_template( $template_name, $template_path = 'mycred', $default_path = '' ) {

		if ( empty( $template_path ) || empty( $default_path ) ) return false;

		if ( substr( $template_path, -1 ) != '/' )
			$template_path = trailingslashit( $template_path );

		// Look within passed path within the theme - this is priority.
		$template = locate_template( array( $template_path . $template_name, $template_name ) );

		// Get default template/
		if ( ! $template || empty( $template ) ) $template = $default_path . $template_name;

		// Return what we found.
		return apply_filters( 'mycred_locate_template', $template, $template_name, $template_path );

	}
endif;
if ( ! function_exists( 'mycred_leaderboard_exclude_role' ) ) :
	function mycred_leaderboard_exclude_role($exclude) {
		$roles = explode (",", $exclude); 
		$exclude = get_users( array( 'role__in'=>$roles,'fields' =>  'ID' ) );
		$exclude = implode(',', $exclude);

		// Return what we found.
		return apply_filters( 'mycred_leaderboard_exclude_role', $exclude );
	}
endif;

/**
 * Level Requirements
 * @since 2.1
 * @version 1.0
 */
if ( !function_exists( 'mycred_badge_level_req_check' ) ):
    function mycred_badge_level_req_check( $badge_id, $level_index = 0 ) {

        $content = '';
        
        global $wpdb;
        $user_id = get_current_user_id();
        $badge_requirements = mycred_show_badge_requirements( $badge_id );
        $require_levels = mycred_get_badge_levels( $badge_id );
        $total_levels = count( $require_levels );
        $level = 0;
        $base_requirements = $require_levels[$level]['requires'];
        $results = array();
        $levels_list = array();
        $starting_index = 0;

        if ( $level_index == 0 )
            $starting_index = 0;
        if ( $level_index == 1 )
            $starting_index = 1;
        if ( $level_index > 1 )
            $starting_index = $level_index - 1;

            // Based on the base requirements, we first get the users log entry results

        //Gathering Base requirement's log of current user
        foreach ( $base_requirements as $requirement ) {
            if ( $requirement['type'] == '' )
                $requirement['type'] = MYCRED_DEFAULT_TYPE_KEY;

            $mycred = mycred( $requirement['type'] );
            if ( $mycred->exclude_user( $user_id ) ) continue;

            $having = 'COUNT(*)';
            if ( $requirement['by'] != 'count' )
                $having = 'SUM(creds)';

            $query = $wpdb->get_var( $wpdb->prepare( "SELECT {$having} FROM {$mycred->log_table} WHERE ctype = %s AND ref = %s AND user_id = %d;", $requirement['type'], $requirement['reference'], $user_id ) );
            if ( $query === NULL ) $query = 0;

            $results[ $requirement['reference'] ] = $query;
        }

        //Checking requirements has been achieved
        for ( $i = $starting_index; $i <= $level_index; $i++ )
        {
            foreach ($require_levels[$i]['requires'] as $requirement) {
                $ref = $requirement['reference'];
                $amount = $requirement['amount'];

                if (!empty($results[$ref]) && $results[$ref] >= $amount)
                    $levels_list[$i][] = "achieved";
                else
                    $levels_list[$i][] = "notAchieved";
            }
        }

        //Rendering Level requirements
        $content .= '<ul>';
        for( $i = $starting_index; $i <= $level_index; $i++ )
        {
            $counter = 0;

            foreach ($badge_requirements[$i]["requirements"] as $id => $requirement)
            {
                if( $levels_list[$i][$counter] == 'achieved' )
                    $content .= "<li class='mycred-level-requirement mycred-strike-off'>{$requirement}</li>";
                else
                    $content .= "<li class='mycred-level-requirement'>{$requirement}</li>";

                $counter++;
            }
        }
        $content .= '</ul>';

        return $content;

    }
endif;

 /**
 * Get Addon default Settings
 * @since 2.1.1
 * @version 1.0
 */
if ( ! function_exists( 'mycred_get_addon_defaults' ) ) :
	function mycred_get_addon_defaults( $addon = '' ) {

		$settings = array();

		switch ( $addon ) {
			case 'badges':
				$settings = array(
                    'show_level_description'   => 0,
                    'show_congo_text'          => 0,
                    'show_steps_to_achieve'    => 0,
                    'show_levels'              => 0,
                    'show_level_points'        => 0,
                    'show_earners'             => 0,
                    'open_badge'               => 0,
                    'open_badge_evidence_page' => 0,
                    'buddypress'               => '',
                    'bbpress'                  => '',
                    'show_all_bp'              => 0,
                    'show_all_bb'              => 0
                );
				break;
			case 'coupons':
				$settings = array(
					'log'         => 'Coupon redemption',
					'invalid'     => 'This is not a valid coupon',
					'expired'     => 'This coupon has expired',
					'user_limit'  => 'You have already used this coupon',
					'min'         => 'A minimum of %amount% is required to use this coupon',
					'max'         => 'A maximum of %amount% is required to use this coupon',
					'excluded'    => 'You can not use coupons.',
					'success'     => '%amount% successfully deposited into your account'
				);
				break;
			case 'emailnotices':
				$settings = array(
					'from'        => array(
						'name'        => get_bloginfo( 'name' ),
						'email'       => get_bloginfo( 'admin_email' ),
						'reply_to'    => get_bloginfo( 'admin_email' )
					),
					'filter'      => array(
						'subject'     => 0,
						'content'     => 0
					),
					'use_html'    => true,
					'content'     => '',
					'styling'     => '',
					'send'        => '',
					'override'    => 0
				);
				break;
			case 'notifications':
				$settings = array(
					'life'      => 7,
					'template'  => '<p>%entry%</p><h1>%cred_f%</h1>',
					'use_css'   => 1,
					'duration'  => 3
				);
				break;
			case 'rank':
				$settings = array(
					'manual'      => 0,
					'public'      => 0,
					'base'        => 'current',
					'slug'        => MYCRED_RANK_KEY,
					'bb_location' => 'top',
					'bb_template' => 'Rank: %rank_title%',
					'bp_location' => '',
					'bb_template' => 'Rank: %rank_title%',
					'order'       => 'ASC',
					'support'     => array(
						'content'         => 0,
						'excerpt'         => 0,
						'comments'        => 0,
						'page-attributes' => 0,
						'custom-fields'   => 0
					)
				);
				break;
			case 'sell_content':
				$settings = array(
					'post_types'  => 'post,page',
						'filters'     => array(),
					'type'        => array( MYCRED_DEFAULT_TYPE_KEY ),
					'reload'      => 0,
					'working'     => 'Processing ...',
					'templates'   => array(
						'members'     => '<div class="text-center"><h3>Premium Content</h3><p>Buy access to this content.</p><p>%buy_button%</p></div>',
						'visitors'    => '<div class="text-center"><h3>Premium Content</h3><p>Login to buy access to this content.</p></div>',
						'cantafford'  => '<div class="text-center"><h3>Premium Content</h3><p>Buy access to this content.</p><p><strong>Insufficient Funds</strong></p></div>'
					)
				);
				break;
			case 'stats':
				$settings = array(
					'color_positive' => '',
					'color_negative' => '',
					'animate'        => 1,
					'bezier'         => 1,
					'caching'        => 'off'
				);
				break;
			case 'transfers':
				$settings = array(
					'types'      => array( MYCRED_DEFAULT_TYPE_KEY ),
					'logs'       => array(
						'sending'   => 'Transfer of %plural% to %display_name%',
						'receiving' => 'Transfer of %plural% from %display_name%'
					),
					'errors'     => array(
						'low'       => 'You do not have enough %plural% to send.',
						'over'      => 'You have exceeded your %limit% transfer limit.'
					),
					'templates'  => array(
						'login'     => '',
						'balance'   => 'Your current balance is %balance%',
						'limit'     => 'Your current %limit% transfer limit is %left%',
						'button'    => 'Transfer'
					),
					'autofill'   => 'user_login',
					'reload'     => 1,
					'message'    => 0,
					'limit'      => array(
						'amount'    => 1000,
						'limit'     => 'none'
					)
				);
				break;
		}

		return apply_filters( 'mycred_get_addon_defaults', $settings, $addon );

	}
endif;

/**
 * Add submenu inside myCred main menu
 * @since 2.2
 * @version 1.0
 */
if ( ! function_exists( 'mycred_add_main_submenu' ) ) :
	function mycred_add_main_submenu( $page_title, $menu_title, $capability, $menu_slug, $function = '', $position = null ) {

		$main_menu_slug = apply_filters( 'mycred_add_main_submenu_slug', MYCRED_MAIN_SLUG, compact( 
			'page_title', 
			'menu_title',  
			'capability',
			'menu_slug',
			'function',
			'position'
		));

		return add_submenu_page( $main_menu_slug, $page_title, $menu_title, $capability, $menu_slug, $function, $position );

	}
endif;


/**
 * Get Badge Rank Social Icons
 * @since 2.2
 * @version 1.0
 */
if ( !function_exists( 'mycred_br_get_social_icons' ) ):
	function mycred_br_get_social_icons( $facebook_url = '', $twitter_url = '', $linkedin_url = '', $pinterest_url = '' )
        {
            $mycred = mycred();

            $br_enable_fb = isset( $mycred->core["br_social_share"]["enable_fb"] ) && $mycred->core["br_social_share"]["enable_fb"] == '1' ? true : false; 
            $br_enable_twitter = isset( $mycred->core["br_social_share"]["enable_twitter"] ) && $mycred->core["br_social_share"]["enable_twitter"] == '1' ? true : false; 
            $br_enable_li = isset( $mycred->core["br_social_share"]["enable_li"] ) && $mycred->core["br_social_share"]["enable_li"] == '1' ? true : false; 
            $br_enable_pt = isset( $mycred->core["br_social_share"]["enable_pt"] ) && $mycred->core["br_social_share"]["enable_pt"] == '1' ? true : false;

            $content = '';

            $content .= '<div class="mycred-badge-social-icons">';

            $br_socail_icon = isset( $mycred->core["br_social_share"]["button_style"] ) ? $mycred->core["br_social_share"]["button_style"] : ''; 

            if( $br_socail_icon == 'button_style' )
            {
                if( $br_enable_fb )
                    $content .= '
					<a href="'.$facebook_url.'" target="_blank"><button class="mycred-social-icons mycred-social-icon-facebook">facebook</button></a>';
                if( $br_enable_twitter )
                    $content .= '
					<a href="'.$twitter_url.'" target="_blank"><button class="mycred-social-icons mycred-social-icon-twitter">twitter</button></a>';
                if( $br_enable_li )
                    $content .= '
					<a href="'.$linkedin_url.'" target="_blank"><button class="mycred-social-icons mycred-social-icon-linkedin">linkedin</button></a>';
                if( $br_enable_pt )
                    $content .= '
					<a href="'.$pinterest_url.'" target="_blank"><button class="mycred-social-icons mycred-social-icon-pinterest">pinterest</button></a>';
            }

            if( $br_socail_icon == 'icon_style' )
            {
                if( $br_enable_fb )
                    $content .= '
                    <a href="'.$facebook_url.'" target="_blank" class="mycred-social-icons mycred-social-icon-facebook"></a>';
                if( $br_enable_twitter )
                    $content .= '
                    <a href="'.$twitter_url.'" target="_blank" class="mycred-social-icons mycred-social-icon-twitter"></a>';
                if( $br_enable_li )
                    $content .= '
                    <a href="'.$linkedin_url.'" target="_blank" class="mycred-social-icons mycred-social-icon-linkedin"></a>';
                if( $br_enable_pt )
                    $content .= '
                    <a href="'.$pinterest_url.'" target="_blank" class="mycred-social-icons mycred-social-icon-pinterest"></a>';
            }

            if( $br_socail_icon == 'text_style' )
            {
                if( $br_enable_fb )
                    $content .= '
                    <a href="'.$facebook_url.'" target="_blank"><button class="facebook social-text">facebook</button></a>';
                if( $br_enable_twitter )
                    $content .= '
                    <a href="'.$twitter_url.'" target="_blank"><button class="twitter social-text">twitter</button></a>';
                if( $br_enable_li )
                    $content .= '
                    <a href="'.$linkedin_url.'" target="_blank"><button class="linkedin social-text">linkedin</button></a>';
                if( $br_enable_pt )
                    $content .= '
                    <a href="'.$pinterest_url.'" target="_blank"><button class="pinterest social-text">pinterest</button></a>';
            }

            if( $br_socail_icon == 'icon_style_hover' )
            {
                if( $br_enable_fb )
                    $content .= '
                    <a href="'.$facebook_url.'" target="_blank" class="i-text-admin mycred-social-icons mycred-social-icon-facebook"></a>';
                if( $br_enable_twitter )
                    $content .= '
                    <a href="'.$twitter_url.'" target="_blank" class="i-text-admin mycred-social-icons mycred-social-icon-twitter"></a>';
                if( $br_enable_li )
                    $content .= '
                    <a href="'.$linkedin_url.'" target="_blank" class="i-text-admin mycred-social-icons mycred-social-icon-linkedin"></a>';
                if( $br_enable_pt )
                    $content .= '
                    <a href="'.$pinterest_url.'" target="_blank" class="i-text-admin mycred-social-icons mycred-social-icon-pinterest"></a>';
            }

            $content .= '</div>';
        

            return $content;
        }
endif;

/**
 * Upload default point image
 * @since 2.2
 * @version 1.1
 */
if( !function_exists( 'mycred_upload_default_point_image' ) ):
function mycred_upload_default_point_image()
{
	$default_point_image = mycred_get_option( 'mycred_default_point_image' );

	$image_url = wp_get_attachment_url( $default_point_image );

	if( empty( $default_point_image ) || !$image_url )
	{
		$image_url = plugin_dir_path( __DIR__ ) . 'assets/images/default-point-type.png';

		$upload_dir = wp_upload_dir();

		$image_data = file_get_contents( $image_url );

		$filename = basename( $image_url );

		if ( wp_mkdir_p( $upload_dir['path'] ) ) 
			$file = $upload_dir['path'] . '/' . $filename;
		else 
			$file = $upload_dir['basedir'] . '/' . $filename;
	

		file_put_contents( $file, $image_data );

		$wp_filetype = wp_check_filetype( $filename, null );

		$attachment = array(
			'post_mime_type' => $wp_filetype['type'],
			'post_title' => 'mycred_default_image',
			'post_content' => '',
			'post_status' => 'inherit'
		);

		$attach_id = wp_insert_attachment( $attachment, $file );

		$attach_data = wp_generate_attachment_metadata( $attach_id, $file );

		wp_update_attachment_metadata( $attach_id, $attach_data );

		mycred_update_option( 'mycred_default_point_image', $attach_id );
	}
}
endif;

/**
 * Gets default point image
 * @since 2.2
 * @version 1.1
 */
if( !function_exists( 'mycred_get_default_point_image_id' ) ):
function mycred_get_default_point_image_id()
{
	$image_id = mycred_get_option( 'mycred_default_point_image' );

	if( empty( $image_id ) )
		return false;
	
	return $image_id;
}
endif;

/**
 * Creates select2
 * @since 2.3
 * @version 1.0
 */
if( !function_exists( 'mycred_create_select2' ) ):
function mycred_create_select2( $options = '', $attributes = array(), $selected = array() )
{
	$content = '';
	$is_selected = false;
	$content .= "<select ";

	if( !empty( $attributes ) )
		foreach( $attributes as $attr => $value )
			$content .= "{$attr}='{$value}'";

	$content .= "style='width: 168px;'>";

	if( !empty( $options ) )
	{
		foreach( $options as $key => $value )
		{
			foreach( $selected as $s_key )
			{
				if( $s_key == $key )
				{
					$content .= "<option selected='selected' value='{$key}'>{$value}</option>";	
					$is_selected = true;
				}
			}
			if( $is_selected )
			{
				$is_selected = false;
				continue;	
			}
			$content .= "<option value='{$key}'>{$value}</option>";
		}
	}										

	$content .= "</select>";

	return $content;
}
endif;

/**
 * Get Ranks Point type 
 * @var int $rank_id
 * @since 2.3
 * @version 1.0
 * @return bool|string
 */
if ( !function_exists( 'mycred_get_rank_pt' ) ):
function mycred_get_rank_pt( $rank_id )
{
	$pt = get_post_meta( $rank_id, 'ctype', true );

	if( $pt )
		return $pt;
	else
		return false;
}
endif;

/**
 * Get Email Notice Instances
 * Returns an array of supported instances where an email can be sent by this add-on.
 * @since 1.8
 * @since 2.3 Moved from Email Norification
 * @version 1.0
 */
if ( ! function_exists( 'mycred_get_email_instances' ) ) :
	function mycred_get_email_instances( $none = true ) {

		$instances = array();

		if ( $none ) $instances[''] = __( 'Select', 'mycred' );

		$instances['any']      = __( 'users balance changes', 'mycred' );
		$instances['positive'] = __( 'users balance increases', 'mycred' );
		$instances['negative'] = __( 'users balance decreases', 'mycred' );
		$instances['zero']     = __( 'users balance reaches zero', 'mycred' );
		$instances['minus']    = __( 'users balance goes negative', 'mycred' );

		if ( class_exists( 'myCRED_Badge_Module' ) ) {
			$instances['badge_new'] = __( 'user gains a badge', 'mycred' );
			$instances['badge_level'] = __( 'user gains a new badge level', 'mycred' );
		}

		if ( class_exists( 'myCRED_Ranks_Module' ) ) {
			$instances['rank_up']   = __( 'user is promoted to a higher rank', 'mycred' );
			$instances['rank_down'] = __( 'user is demoted to a lower rank', 'mycred' );
		}

		if ( class_exists( 'myCRED_Transfer_Module' ) ) {
			$instances['transfer_out'] = __( 'user sends a transfer', 'mycred' );
			$instances['transfer_in']  = __( 'user receives a transfer', 'mycred' );
		}

		if ( class_exists( 'myCRED_cashCRED_Module' ) ) {
			$instances['cashcred_approved'] = __( 'cashcred withdraw approval', 'mycred' );
			$instances['cashcred_pending']  = __( 'cashcred withdraw pending', 'mycred' );
			$instances['cashcred_cancel']  = __( 'cashcred cancel', 'mycred' );
		}
		
		
		$instances['custom']  = __( 'a custom event occurs', 'mycred' );

		return apply_filters( 'mycred_email_instances', $instances );

	}
endif;