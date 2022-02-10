<?php
/**
 * View plugin class
 *
 * @since             1.0.0
 * @package           TInvWishlist\Helper
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * View plugin class
 */
class TInvWL_View {

	/**
	 * Plugin name
	 *
	 * @var string
	 */
	static $_name;
	/**
	 * Plugin version
	 *
	 * @var string
	 */
	static $_version;
	/**
	 * Redirect url
	 *
	 * @var string
	 */
	static $_redirect;
	/**
	 * Debug mode
	 *
	 * @var boolean
	 */
	static $debug = false;

	/**
	 * Set Plugin info
	 *
	 * @param string $plugin_name Plugin name.
	 * @param string $version Plugin version.
	 */
	public static function _init( $plugin_name, $version ) {
		self::$_name    = $plugin_name;
		self::$_version = $version;
	}

	/**
	 * Init header and notification
	 *
	 * @return boolean
	 */
	private static function init() {
		add_action( 'tinvwl_view_header', array( __CLASS__, 'header' ) );
		if ( ! empty( self::$_redirect ) ) {
			return add_action( 'tinvwl_view_header', array( __CLASS__, '_redirect' ), 5 );
		}
		$message = self::get_session_arr( '_errors' );
		if ( ! empty( $message ) ) {
			add_action( 'tinvwl_view_header', array( __CLASS__, '_error' ), 20 );
		}
		$message = self::get_session_arr( '_tips' );
		if ( ! empty( $message ) ) {
			add_action( 'tinvwl_view_header', array( __CLASS__, '_tip' ), 30 );
		}
		$message = self::get_session_arr( '_attentions' );
		if ( ! empty( $message ) ) {
			add_action( 'tinvwl_view_header', array( __CLASS__, '_attention' ), 40 );
		}
	}

	/**
	 * Get session message variable
	 *
	 * @param string $name Name message variable.
	 * @param mixed $default Default value.
	 *
	 * @return mixed
	 */
	public static function get_session_arr( $name, $default = array() ) {
		$data = get_option( self::$_name . $name, $default );
		if ( empty( $data ) ) {
			return $default;
		}

		return $data;
	}

	/**
	 * Set session message variable
	 *
	 * @param string $name Name message variable.
	 * @param mixed $value Value.
	 */
	public static function set_session_arr( $name, $value = array() ) {
		update_option( self::$_name . $name, $value );
	}

	/**
	 * Set error message
	 *
	 * @param string $msg Message.
	 * @param integer $code Code.
	 */
	public static function set_error( $msg = '', $code = 100 ) {
		$_errors   = self::get_session_arr( '_errors' );
		$_errors[] = array( $code, $msg );
		self::set_session_arr( '_errors', $_errors );
	}

	/**
	 * Set tips message
	 *
	 * @param string $msg Message.
	 */
	public static function set_tips( $msg = '' ) {
		$_tips   = self::get_session_arr( '_tips' );
		$_tips[] = $msg;
		self::set_session_arr( '_tips', $_tips );
	}

	/**
	 * Set redirect.
	 *
	 * @param string $url Url redirect.
	 *
	 * @return boolean
	 */
	public static function set_redirect( $url = '' ) {
		if ( filter_var( $url, FILTER_VALIDATE_URL ) ) {
			self::$_redirect = $url;

			return true;
		}

		return false;
	}

	/**
	 * Set attention message
	 *
	 * @param string $msg Message.
	 */
	public static function set_attentions( $msg = '' ) {
		$_attentions   = self::get_session_arr( '_attentions' );
		$_attentions[] = $msg;
		self::set_session_arr( '_tips', $_attentions );
	}

	/**
	 * Redirect
	 *
	 * @return boolean
	 */
	public static function _redirect() {
		if ( empty( self::$_redirect ) ) {
			return false;
		}
		printf( '<script language = "javascript">document.location.href="%s";</script>', self::$_redirect ); // WPCS: xss ok.
		wp_die();
	}

	/**
	 * Attention
	 *
	 * @return string
	 */
	public static function _attention() {
		$msg = self::get_session_arr( '_attentions' );
		self::set_session_arr( '_attentions' );
		if ( 0 === count( $msg ) ) {
			return '';
		}
		$msg = array_pop( $msg );

		return self::_message( 'warning', $msg, __( 'Attention!', 'ti-woocommerce-wishlist' ) );
	}

	/**
	 * Error
	 *
	 * @return string
	 */
	public static function _error() {
		$msg = self::get_session_arr( '_errors' );
		self::set_session_arr( '_errors' );
		if ( 0 === count( $msg ) ) {
			return '';
		}
		$msg = array_pop( $msg );

		return self::_message( 'error', $msg[1], sprintf( __( 'Errors(%s)', 'ti-woocommerce-wishlist' ), $msg[0] ) );
	}

	/**
	 * Tip
	 *
	 * @return string
	 */
	public static function _tip() {
		$msg = self::get_session_arr( '_tips' );
		self::set_session_arr( '_tips' );
		if ( 0 === count( $msg ) ) {
			return '';
		}
		$msg = array_pop( $msg );

		return self::_message( 'info', $msg, __( 'Useful Tip', 'ti-woocommerce-wishlist' ) );
	}

	/**
	 * Message output
	 *
	 * @param string $_status Status for class.
	 * @param string $_message Message text.
	 * @param string $_header Header message.
	 *
	 * @return boolean
	 */
	private static function _message( $_status, $_message = '', $_header = '' ) {
		if ( empty( $_message ) ) {
			return false;
		}
		include self::file( 'message', '' );

		return true;
	}

	/**
	 * Include admin template
	 *
	 * @param string $name Name file.
	 * @param string $type Folder section.
	 *
	 * @return boolean
	 */
	private static function file( $name, $type = 'admin' ) {
		if ( empty( $name ) ) {
			return self::file( 'null', '' );
		}

		$path = array( 'views', $type, $name );
		$path = implode( DIRECTORY_SEPARATOR, $path );
		$path = sprintf( '%s%s.php', TINVWL_PATH, strtolower( $path ) );
		if ( file_exists( $path ) ) {
			return $path;
		}

		return self::file( 'null', '' );
	}

	/**
	 * Ajax templates
	 *
	 * @param string $_template_name If empty returned json array.
	 * @param array $_data Parameter for template.
	 * @param string $_type Folder section.
	 */
	public static function ajax( $_template_name, $_data = array(), $_type = '' ) {
		if ( empty( $_template_name ) ) {
			if ( is_object( $_data ) || is_array( $_data ) ) {
				wp_send_json( $_data );
			}
			$_data = (string) $_data;
			if ( empty( $_type ) ) {
				$_type = 'text/html';
			}
			header( sprintf( 'Content-Type: %s', $_type ) );
			echo $_data; // WPCS: xss ok.
		} else {
			if ( is_array( $_template_name ) ) {
				$_template_name = implode( '-', $_template_name );
			}
			$_type = 'text/html';
			header( sprintf( 'Content-Type: %s', $_type ) );
			self::view( $_template_name, $_data );
		}
		wp_die();
	}

	/**
	 * Create global template
	 *
	 * @param string $_template_name Name file.
	 * @param array $_data Parameter for template.
	 */
	public static function render( $_template_name, $_data = array() ) {
		$_data = apply_filters( 'tinvwl_view_data_general', $_data );
		self::init();
		$_header = empty( $_data['_header'] ) ? '' : $_data['_header'];
		$_footer = empty( $_data['_footer'] ) ? '' : $_data['_footer'];
		include self::file( 'general', '' );
	}

	/**
	 * Create template
	 *
	 * @param string $_template_name Name file.
	 * @param array $_data Parameter for template.
	 * @param type $_type Folder section.
	 */
	public static function view( $_template_name, $_data = array(), $_type = 'admin' ) {
		if ( is_array( $_template_name ) ) {
			$_template_name = implode( '-', $_template_name );
		}
		if ( is_string( $_template_name ) ) {
			$_data = apply_filters( 'tinvwl_view_data_' . $_template_name, $_data );
		}
		if ( array_key_exists( 'options', $_data ) ) {
			TInvWL_Form::setoptions( $_data['options'] );
			unset( $_data['options'] );
		}
		if ( array_key_exists( 'value', $_data ) ) {
			TInvWL_Form::setvalue( $_data['value'] );
			unset( $_data['value'] );
		}
		extract( $_data ); // @codingStandardsIgnoreLine WordPress.VIP.RestrictedFunctions.extract
		if ( self::$debug && is_string( $_template_name ) ) {
			printf( "\r\n<!-- START: %s -->\r\n", esc_attr( @$_template_name ) ); // @codingStandardsIgnoreLine Generic.PHP.NoSilencedErrors.Discouraged
		}
		if ( is_object( $_template_name ) ) {
			$_template_name->Run();
		} else {
			include self::file( $_template_name, $_type );
		}
		if ( self::$debug && is_string( $_template_name ) ) {
			printf( "\r\n<!-- END: %s -->\r\n", esc_attr( @$_template_name ) ); // @codingStandardsIgnoreLine Generic.PHP.NoSilencedErrors.Discouraged
		}
	}

	/**
	 * Generated header
	 *
	 * @param name $_name Title for page.
	 */
	public static function header( $_name = '' ) {
		$status_panel = self::status_panel();
		$status_panel = apply_filters( 'tinvwl_view_panelstatus', $status_panel );
		include self::file( 'header', '' );
	}

	/**
	 * Status icon
	 *
	 * @return array
	 */
	private static function status_panel() {
		return array(
			sprintf( '<a class="tinvwl-btn grey w-icon md-icon smaller-txt" href="%s"><i class="ftinvwl ftinvwl-graduation-cap"></i><span class="tinvwl-txt">%s</span></a>', 'https://templateinvaders.com/documentation/ti-woocommerce-wishlist-free/?utm_source=' . TINVWL_UTM_SOURCE . '&utm_campaign=' . TINVWL_UTM_CAMPAIGN . '&utm_medium=' . TINVWL_UTM_MEDIUM . '&utm_content=header_documentation&partner=' . TINVWL_UTM_SOURCE, __( 'read documentation', 'ti-woocommerce-wishlist' ) ),
		);
	}

	/**
	 * Formated admin url
	 *
	 * @param string $page Page title.
	 * @param string $cat Category title.
	 * @param array $arg Arguments array.
	 *
	 * @return string
	 */
	public static function admin_url( $page, $cat = '', $arg = array() ) {
		$protocol = is_ssl() ? 'https' : 'http';
		$glue     = '-';
		$params   = array(
			'page' => implode( $glue, array_filter( array( self::$_name, $page ) ) ),
			'cat'  => $cat,
		);
		if ( is_array( $arg ) ) {
			$params = array_merge( $params, $arg );
		}
		$params = array_filter( $params );
		$params = http_build_query( $params );
		if ( is_string( $arg ) ) {
			$params = $params . '&' . $arg;
		}

		return admin_url( sprintf( 'admin.php?%s', $params ), $protocol );
	}
}
