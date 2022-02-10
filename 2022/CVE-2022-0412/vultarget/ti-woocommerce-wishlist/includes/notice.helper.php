<?php
/**
 * Notice plugin class
 *
 * @since             1.0.0
 * @package           TInvWishlist
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Notice plugin class
 */
class TInvWL_Notice {

	private static $shownotices = array();
	private static $notices = array();
	static $_instance;
	protected $curent;

	function __construct() {
		self::$shownotices = get_option( 'ti_admin_shownotices', array() );
		self::$notices     = get_option( 'ti_admin_notices', array() );
		self::define_hooks();
	}

	/**
	 * Instance Class
	 *
	 * @return \TInvWL_Notice
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		self::$_instance->curent = null;

		return self::$_instance;
	}

	public static function define_hooks() {
		add_action( 'admin_init', array( __CLASS__, 'hide_notices' ) );
		add_action( 'shutdown', array( __CLASS__, 'save' ) );
		add_action( 'admin_notices', array( __CLASS__, 'output' ) );
		self::apply_triggers();
		self::apply_resets();
	}

	public static function save() {
		update_option( 'ti_admin_shownotices', self::$shownotices );
		update_option( 'ti_admin_notices', self::$notices );
	}

	public static function add( $name ) {
		self::$notices = array_unique( array_merge( self::$notices, array( $name ) ) );
		if ( ! array_key_exists( $name, self::$shownotices ) ) {
			self::$shownotices[ $name ] = array();
		}
	}

	public static function has( $name ) {
		return in_array( $name, self::$notices );
	}

	public static function filter() {
		$notice = self::$shownotices;
		foreach ( $notice as $name => $data ) {
			if ( ! is_array( $data ) ) {
				continue;
			}
			$data  = array_reverse( $data, true );
			$_data = array();
			foreach ( $data as $key => $value ) {
				$_data[ $key ] = $value;
				break;
			}
			$notice[ $name ] = array_filter( $_data );
		}
		$notice = array_filter( $notice );

		return $notice;
	}

	public static function output() {
		$notices = self::filter();

		foreach ( $notices as $name => $notice_data ) {
			$notice = get_option( 'ti_admin_notice_' . $name, array() );
			if ( empty( $notice ) ) {
				continue;
			}
			foreach ( $notice_data as $key => $status ) {
				if ( is_integer( $status ) ) {
					if ( array_key_exists( $status - 1, $notice ) ) {
						$message = $notice[ $status - 1 ];
					} else {
						$message = array_shift( $notice );
					}
				} else {
					$message = array_shift( $notice );
				}
				self::template( $name, $key, $message );
				break;
			}
		}
	}

	public static function template( $name, $key, $message ) {
		if ( empty( $message ) ) {
			return;
		}
		$output = '<div id="message" class="updated woocommerce-message"><a class="woocommerce-message-close notice-dismiss" href="' . esc_url( wp_nonce_url( add_query_arg( 'ti-hide-notice', $name, add_query_arg( 'ti-hide-notice-trigger', $key ) ), 'ti_hide', '_ti_notice_nonce' ) ) . '">' . __( 'Dismiss', 'ti-woocommerce-wishlist' ) . '</a>' . wp_kses_post( wpautop( $message ) ) . '</div>';

		echo apply_filters( 'tinvwl_notice_' . $name, $output, $key, $message ); // WPCS: XSS ok.
	}

	public static function remove( $name ) {
		unset( self::$notices[ $name ] );
		self::$shownotices[ $name ] = false;
	}

	public static function show( $name, $tag = null, $arg = true ) {
		if ( is_array( self::$shownotices[ $name ] ) ) {
			$notice = get_option( 'ti_admin_notice_' . $name, array() );
			if ( ! is_array( $notice ) ) {
				$notice = array( $notice );
				$notice = array_filter( $notice );
			}
			if ( empty( $notice ) ) {
				return;
			}
			$notice_key = $arg;
			if ( ! is_integer( $arg ) || ! array_key_exists( $arg, $notice ) ) {
				$notice_keys = array_keys( $notice );
				if ( 1 < count( $notice ) ) {
					$notice_key = $notice_keys[ rand( 0, count( $notice_keys ) ) ];
				} else {
					$notice_key = $notice_keys[0];
				}
				$notice_key = absint( $notice_key ) + 1;
			}
			self::$shownotices[ $name ][ $tag ] = $notice_key;
		}
	}

	public static function hide( $name, $tag = null ) {
		if ( is_array( self::$shownotices[ $name ] ) ) {
			if ( array_key_exists( $name, self::$shownotices ) ) {
				if ( empty( $tag ) ) {
					foreach ( array_keys( self::$shownotices[ $name ] ) as $tag ) {
						self::hide( $name, $tag );
					}
				} else {
					self::$shownotices[ $name ][ $tag ] = false;
				}
			}
		}
	}

	public static function reset( $name ) {
		self::$shownotices[ $name ] = array();
	}

	public static function get() {
		return self::$notices;
	}

	public static function remove_notice( $name ) {
		self::remove( $name );
		delete_option( 'ti_admin_notice_' . $name );
		delete_option( 'ti_admin_notice_trigger_' . $name );
		delete_option( 'ti_admin_notice_reset_' . $name );
	}

	function add_notice( $name, $notice ) {
		if ( ! is_array( $notice ) ) {
			$notice = array( $notice );
		}
		if ( self::has( $name ) ) {
			$this->curent = null;

			return $this;
		}
		self::add( $name );
		update_option( 'ti_admin_notice_' . $name, $notice );
		$this->curent = $name;

		return $this;
	}

	function modify_notice( $name, $notice, $index = 0 ) {
		if ( ! is_array( $notice ) ) {
			$notice = array( $notice );
		}
		self::add( $name );
		$_notice = get_option( 'ti_admin_notice_' . $name, array() );
		foreach ( $notice as $value ) {
			$_value = wp_kses_post( $value );
			if ( ! in_array( $_value, $_notice ) ) {
				$_notice[ $index ] = $_value;
			}
			$index ++;
		}
		update_option( 'ti_admin_notice_' . $name, $_notice );
		$this->curent = $name;

		return $this;
	}

	function set_notice( $name ) {
		$this->curent = $name;

		return $this;
	}

	function add_trigger( $tag, $function_to_add = null, $priority = 10, $accepted_args = 1, $name = null ) {
		if ( empty( $name ) ) {
			$name = $this->curent;
		}
		if ( empty( $name ) ) {
			return $this;
		}
		if ( empty( $function_to_add ) ) {
			$function_to_add = '__return_true';
		}
		$priority = absint( $priority );

		$data         = get_option( 'ti_admin_notice_trigger_' . $name, array() );
		$idx          = md5( serialize( array( $tag, $function_to_add, $priority ) ) );
		$data[ $idx ] = array( $tag, $function_to_add, $priority, $accepted_args );
		update_option( 'ti_admin_notice_trigger_' . $name, $data );
		$this->curent = $name;

		return $this;
	}

	function remove_trigger( $tag, $function_to_add = null, $priority = 10, $name = null ) {
		if ( empty( $name ) ) {
			$name = $this->curent;
		}
		if ( empty( $name ) ) {
			return $this;
		}
		$priority = absint( $priority );
		$data     = get_option( 'ti_admin_notice_trigger_' . $name, array() );
		$idx      = md5( serialize( array( $tag, $function_to_add, $priority ) ) );
		if ( array_key_exists( $idx, $data ) ) {
			unset( $data[ $idx ] );
			update_option( 'ti_admin_notice_trigger_' . $name, $data );
		}
		$this->curent = $name;

		return $this;
	}

	public static function apply_triggers() {
		foreach ( self::$notices as $notice ) {
			self::apply_trigger( $notice );
		}
	}

	public static function apply_trigger( $name ) {
		$data    = get_option( 'ti_admin_notice_trigger_' . $name, array() );
		$trigger = new TInvWL_Notice_Trigger( 'ti_admin_notice_trigger_' );
		if ( empty( $data ) ) {
			self::show( $name );
		} else {
			foreach ( $data as $idx => $_data ) {
				if ( ! array_key_exists( $idx, (array) @self::$shownotices[ $name ] ) ) {
					add_filter( $_data[0], array( $trigger, $name . '__' . $idx ), $_data[2], $_data[3] );

					return;
				}
			}
			if ( 0 == count( array_filter( self::$shownotices[ $name ] ) ) ) {
				self::remove( $name );
			}
		}
	}

	function add_reset( $tag, $function_to_add = null, $priority = 10, $accepted_args = 1, $name = null ) {
		if ( empty( $name ) ) {
			$name = $this->curent;
		}
		if ( empty( $name ) ) {
			return $this;
		}
		if ( empty( $function_to_add ) ) {
			$function_to_add = '__return_true';
		}
		$priority     = absint( $priority );
		$data         = get_option( 'ti_admin_notice_reset_' . $name, array() );
		$idx          = md5( serialize( array( $tag, $function_to_add, $priority ) ) );
		$data[ $idx ] = array( $tag, $function_to_add, $priority, $accepted_args );
		update_option( 'ti_admin_notice_reset_' . $name, $data );
		$this->curent = $name;

		return $this;
	}

	function remove_reset( $tag, $function_to_add = null, $priority = 10, $name = null ) {
		if ( empty( $name ) ) {
			$name = $this->curent;
		}
		if ( empty( $name ) ) {
			return $this;
		}
		if ( empty( $function_to_add ) ) {
			$function_to_add = '__return_true';
		}
		$priority = absint( $priority );
		$data     = get_option( 'ti_admin_notice_reset_' . $name, array() );
		$idx      = md5( serialize( array( $tag, $function_to_add, $priority ) ) );
		if ( array_key_exists( $idx, $data ) ) {
			unset( $data[ $idx ] );
			update_option( 'ti_admin_notice_reset_' . $name, $data );
		}
		$this->curent = $name;

		return $this;
	}

	public static function apply_resets() {
		foreach ( self::$notices as $notice ) {
			self::apply_reset( $notice );
		}
	}

	public static function apply_reset( $name ) {
		$data    = get_option( 'ti_admin_notice_reset_' . $name, array() );
		$trigger = new TInvWL_Notice_Trigger( 'ti_admin_notice_reset_' );
		if ( ! empty( $data ) ) {
			foreach ( $data as $idx => $_data ) {
				add_filter( $_data[0], array( $trigger, $name . '__' . $idx ), $_data[2], $_data[3] );
			}
		}
	}

	public static function hide_notices( $name = null ) {
		if ( ! empty( $name ) ) {
			self::hide( $name );
		} else {
			$data = filter_input_array( INPUT_GET, array(
				'_ti_notice_nonce'       => FILTER_DEFAULT,
				'ti-hide-notice-trigger' => FILTER_DEFAULT,
				'ti-hide-notice'         => FILTER_DEFAULT,
			) );
			$name = $data['ti-hide-notice'];
			if ( ! empty( $name ) ) {
				if ( isset( $data['_ti_notice_nonce'] ) && wp_verify_nonce( $data['_ti_notice_nonce'], 'ti_hide' ) ) {
					self::hide( $name, $data['ti-hide-notice-trigger'] );
					do_action( 'tinvwl_notice_hide_' . $name );
				} elseif ( isset( $data['_ti_notice_nonce'] ) && wp_verify_nonce( $data['_ti_notice_nonce'], 'ti_remove' ) ) {
					self::remove_notice( $name );
					do_action( 'tinvwl_notice_remove_' . $name );
				}
			}
		}
	}

}


if ( ! class_exists( 'TInvWL_Notice_Trigger' ) ) {
	class TInvWL_Notice_Trigger {

		private $prefix;

		function __construct( $prefix ) {
			$this->prefix = $prefix;
		}

		function __call( $name, $arguments ) {
			list( $name, $idx ) = explode( '__', $name );
			if ( empty( $idx ) ) {
				return;
			}
			$data = get_option( $this->prefix . $name, array() );
			if ( array_key_exists( $idx, $data ) ) {
				$result = call_user_func_array( $data[ $idx ][1], array_slice( $arguments, 0, (int) $data[ $idx ][3] ) );
				if ( ! empty( $result ) ) {
					TInvWL_Notice::show( $name, $idx, $result );
				}
			}

			return array_shift( $arguments );
		}
	}
}

if ( is_admin() ) {
	TInvWL_Notice::instance();
}
